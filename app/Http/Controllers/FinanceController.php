<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Hash;
use App\Potongan;
use App\Neraca;
use App\Coa;
use App\Sales;
use App\Transaction;
use App\Iuran;
use App\ItemProduk;
use App\Shu;
use App\Murabahah;
use App\Anggota;
use App\Pengurus;

class FinanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $sharedService;
    private $model;
    private $sumData;

    public function __construct(Shared $sharedService,Model $model)
    {
        $this->sharedService = $sharedService;
        $this->model = $model;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        return view('finance.index');
    }

    public function getLastPotongan()
    {
        $data = Potongan::orderByDesc('year')->orderByDesc('month')->first();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getLastClosing()
    {
        $data = Neraca::select('month', 'year','created_date')->groupByRaw('month,year,created_date')->orderByDesc('year')->orderByDesc('month')->first();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function submitPotongan(Request $request)
    {
        $month = str_pad($this->sharedService->getMonthIndex($request->post('month')) + 1, 2, '0', STR_PAD_LEFT);
        $year = $request->post('year');
        // if((int)$month > date('m') && $year >= date('Y') ){
        //     echo json_encode(array('status' => false, 'message' => 'Potongan hanya bisa dilakukan di bulan berjalan atau bulan sebelumnya'));
        //     die();
        // }
        // if(Potongan::where('month',$month)->where('year',$year)->where('status',1)->doesntExist()){
            DB::beginTransaction();
            try {
                $check = Potongan::where('month',$month)->where('year',$year)->where('status',1);
                if($check->doesntExist()){
                    $data = new Potongan;
                    $data->month = $month;
                    $data->year = $year;
                    $data->status = 1;
                    $data->created_by = Auth::user()->getUsername();
                }else{
                    $data = $check->first();
                }
                $data->updated_date = date('Y-m-d H:i:s');
                if($data->save()){
                    $msg = Auth::user()->getUsername(). ' submit potongan bulanan succesfully : '.json_encode($data);              
                    // Log::info($msg);
                    $this->sharedService->logs($msg);

                    $generate = Potongan::generatePotongan($request);
                    if($generate){
                        echo json_encode(array('status' => true, 'message' => 'Process Succesfully'));
                        DB::commit();
                    }
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());  
                $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully submit potongan bulanan: '.$e->getMessage());       
                echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
                DB::rollBack();
            }
        // }else{
        //     echo json_encode(array('status' => false, 'message' => 'Potongan untuk bulan tersebut sudah dilakukan'));
        // }
    }

    public function submitClosing(Request $request)
    {
        $month = (int)(str_pad($this->sharedService->getMonthIndex($request->post('month')) + 1, 2, '0', STR_PAD_LEFT));
        $year = $request->post('year');
        $last_month =  Neraca::whereRaw('year = (SELECT MAX(year) from neraca)')->max('month');
        $last_year = Neraca::max('year');
        if($month > date('m') && $year >= date('Y') ){
            echo json_encode(array('status' => false, 'message' => 'Closing hanya bisa dilakukan di bulan berjalan atau bulan sebelumnya'));
            die();
        }
        if(($month < $last_month && $year <= $last_year) || $year < $last_year){
            echo json_encode(array('status' => false, 'message' => 'Closingan untuk bulan tersebut sudah dilakukan'));
            die();
        }
        // if(($month >= $last_month && $last_year == $year) || !$last_month || ($month == "1" && $last_month == "12" && $year == $last_year + 1)){
            DB::beginTransaction();
            if($last_month == $month && $year == $last_year){
                $neracas = Neraca::where('month',$month)->where('year',$year);

                // Revert back coa ending balance
                $neracas_array = $neracas->get()->toArray();
                for ($i=0; $i < count($neracas_array); $i++) {
                    $revert_coa = Coa::where('coa_code',$neracas_array[$i]['coa_code'])->first();
                    $revert_coa->ending_balance = $neracas_array[$i]['begining_balance'];
                    try {
                        $revert_coa->save();
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
                
                $neracas->delete();
            }
            try {
                $res = $this->monthlyClosing($month,$year);
                if($res){
                    $msg = Auth::user()->getUsername(). ' submit closing bulanan succesfully : '.json_encode($res);              
                    Log::info($msg);
                    $this->sharedService->logs($msg);
                    echo json_encode(array('status' => true, 'message' => 'Process Succesfully'));
                    DB::commit();
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());  
                $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully submit closing bulanan: '.$e->getMessage());       
                echo json_encode(array('status' => false, 'message' => 'Proccess Unsuccessfully'));
                DB::rollBack();
            }
        // }else{
        //     echo json_encode(array('status' => false, 'message' => 'Closingan untuk bulan tersebut sudah dilakukan'));
        // }
    }

    private function monthlyClosing($month,$year){
        try {
            $order = Coa::generateOrder();
            $coas = Coa::select('coa_code','begining_balance','ending_balance','rumus_ending_balance')->where('is_sum','N')->orderByRaw($order)->get()->toArray();
            for ($i=0; $i < count($coas); $i++) { 
                if($coas[$i]['coa_code'] == config('app.coa_penjualan')){
                    $penjualan = Sales::where('sales_code','700')->where('payment_type','!=','potongan_anggota')->where('status',1)
                                ->whereRaw('YEAR(sales_date) = '.$year.' ')->whereRaw('MONTH(sales_date) <= "'.$month.'" ')->sum('charge_amount');

                    $penjualan_piutang = Sales::where('sales_code','700')->where('payment_type','!=','potongan_anggota')->where('status',0)
                                ->whereRaw('YEAR(sales_date) = '.$year.' ')->whereRaw('MONTH(sales_date) <= "'.$month.'" ')->sum('charge_amount');
                    
                    $coas[$i]['kredit'] = $penjualan+$penjualan_piutang;
                    $coas[$i]['debit'] = 0;

                    $bank_idx = array_search("A.1.1.2",array_column($coas,'coa_code'));
                    $coas[$bank_idx]['debit'] += $penjualan;

                    $piutang_idx = array_search("A.1.2",array_column($coas,'coa_code'));
                    $coas[$piutang_idx]['debit'] += $penjualan_piutang;

                }else if($coas[$i]['coa_code'] == config('app.coa_hpp')){
                    $total_hpp = DB::table('item')
                                ->leftJoin('sales_detail', 'item.item_code', '=', 'sales_detail.item_code')
                                ->leftJoin('sales', 'sales.sales_no', '=', 'sales_detail.sales_no')
                                ->whereRaw('YEAR(sales.sales_date) = "'.$year.'" ')
                                ->groupByRaw('item.item_code,item.hpp')->select(DB::raw('item.item_code,item.hpp'))->get()->toArray();
                    $coas[$i]['debit'] = array_sum(array_column($total_hpp,'hpp'));
                    $coas[$i]['kredit'] = 0;
                }else{
                    $debit = Transaction::where('coa_code',$coas[$i]['coa_code'])->where('dk','debit')->where('trans_year', $year)->where('trans_month','<=',$month)->sum('amount');
                    $kredit = Transaction::where('coa_code',$coas[$i]['coa_code'])->where('dk','kredit')->where('trans_year', $year)->where('trans_month','<=',$month)->sum('amount');
                    $coas[$i]['debit'] = $debit;
                    $coas[$i]['kredit'] = $kredit;
                }
                $coas[$i]['month'] = $month;
                $coas[$i]['year'] = $year;
                $coas[$i]['created_by'] = Auth::user()->getUsername();

                /* calculate ending balance */
                 if($coas[$i]['rumus_ending_balance'] == 1){
                    $coas[$i]['ending_balance'] = $coas[$i]['ending_balance']+$coas[$i]['debit']-$coas[$i]['kredit'];
                }else if($coas[$i]['rumus_ending_balance'] == 2){
                    $coas[$i]['ending_balance'] = $coas[$i]['ending_balance']-$coas[$i]['debit']+$coas[$i]['kredit'];
                }
                unset($coas[$i]['rumus_ending_balance']);
                /*********************** */
            }
            $res = Neraca::insert($coas);
            if($res){
                return $coas;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function submitHpp(Request $request)
    {
        $month = str_pad($this->sharedService->getMonthIndex($request->post('month')) + 1, 2, '0', STR_PAD_LEFT);
        $year = $request->post('year');
        if((int)$month > date('m') && $year >= date('Y') ){
            echo json_encode(array('status' => false, 'message' => 'Hitung HPP hanya bisa dilakukan di bulan berjalan atau bulan sebelumnya'));
            die();
        }
        DB::beginTransaction();
        try {
            $data = DB::table('item')
            ->leftJoin('sales_detail', 'item.item_code', '=', 'sales_detail.item_code')
            ->leftJoin('sales', 'sales.sales_no', '=', 'sales_detail.sales_no')
            ->whereRaw('YEAR(sales.sales_date) = "'.$year.'" ')
            ->groupBy('item.item_code')
            ->select(DB::raw('item.item_code'))->get();

            for ($i=0; $i < count($data); $i++) { 
                $stock_card = DB::table('stock_card')
                    ->leftJoin('item_transaction_detail', function($join)
                        {
                            $join->on('stock_card.item_code', '=', 'item_transaction_detail.item_code');
                            $join->on('stock_card.transaction_no', '=', 'item_transaction_detail.transaction_no');
                        }
                    )
                    ->whereRaw('YEAR(transaction_date) = "'.$year.'" ')
                    ->where('stock_card.item_code',$data[$i]->item_code)
                    ->where('transaction_code','002')
                    ->orderByRaw('transaction_date,created_date')
                    ->select(DB::raw('stock_card.*,item_transaction_detail.harga'))
                    ->get()->toArray();

                $penjualan = DB::table('stock_card')
                    ->whereRaw('YEAR(transaction_date) = "'.$year.'" ')
                    ->where('stock_card.item_code',$data[$i]->item_code)
                    ->where('transaction_code','700')
                    ->sum('stock_out');
                $persediaan_awal_query = DB::table('stock_card')
                    ->whereRaw('YEAR(transaction_date) = "'.((int)$year-1).'" ')
                    ->orderByRaw('transaction_date DESC,created_date DESC')->first();
                $persediaan_awal = (isset($persediaan_awal_query))?$persediaan_awal_query->stock_balance:0;
                $pembelian = array_sum(array_column($stock_card,'stock_in'));
                $bsj = $persediaan_awal + $pembelian;
                $pbd = $bsj - $penjualan;

                //hitung persediaan barang akhir
                if($stock_card[count($stock_card)-1]->stock_in < $pbd){
                    $count = $pbd;
                    $j = count($stock_card) - 1;
                    $pbd_akhir = 0;
                    while ($j >= 0 || $count>0) {
                        if($count > $stock_card[$j]->stock_in){
                            $pbd_akhir += $stock_card[$j]->stock_in * $stock_card[$j]->harga;
                        }else{
                            $pbd_akhir += $count * $stock_card[$j]->harga;
                        }
                        
                        $count -= $stock_card[$j]->stock_in;
                        $j--;
                    }
                }else{
                    $pbd_akhir = $pbd * $stock_card[count($stock_card)-1]->harga;
                }

                //hitung barang siap jual
                $harga_akhir_query = DB::table('stock_card')
                        ->leftJoin('item_transaction_detail', function($join)
                            {
                                $join->on('stock_card.item_code', '=', 'item_transaction_detail.item_code');
                                $join->on('stock_card.transaction_no', '=', 'item_transaction_detail.transaction_no');
                            }
                        )
                        ->whereRaw('YEAR(transaction_date) = "'.((int)$year-1).'" ')
                        ->where('transaction_code','002')
                        ->orderByRaw('transaction_date DESC,created_date DESC')->first();
                $harga_akhir = isset($harga_akhir_query)?$harga_akhir_query->harga : $stock_card[0]->harga;
                $bsj_akhir = $harga_akhir * $persediaan_awal;
                for ($a=0; $a < count($stock_card); $a++) { 
                    $bsj_akhir += $stock_card[$a]->stock_in * $stock_card[$a]->harga;
                }

                $hpp = $bsj_akhir - $pbd_akhir;
                ItemProduk::where('item_code', $data[$i]->item_code)
                    ->update(['hpp' => $hpp]);
            }

            $msg = Auth::user()->getUsername(). ' submit generate hpp bulanan succesfully : ';              
            Log::info($msg);
            $this->sharedService->logs($msg);
            echo json_encode(array('status' => true, 'message' => 'Process Succesfully'));
            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully generate hpp');       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
            DB::rollBack();
        }
    }

    private function generateSumData()
    {
        $this->sumData = Coa::select('coa_code','begining_balance')->where('is_sum','Y')->orderBy('coa_code')->get()->toArray();
        for ($i=0; $i < count($this->sumData); $i++) { 
            $this->sumData[$i]['ending_balance'] = 0;
        }
    }

    private function generateEndingBalance($year, $coa)
    {
        try {
            $data = DB::table('neraca')
                ->where('year',$year)
                ->whereRaw('month = (SELECT MAX(month) from neraca where year = '.$year.')')
                ->where('neraca.coa_code',$coa['coa_code'])
                ->leftJoin('coa', 'coa.coa_code', '=', 'neraca.coa_code')
                ->select('neraca.*', 'coa.coa_parent')->get()->first();

            if(substr($data->coa_parent, 0, 3) == "A.1"){
                $this->sumData[array_search("A.1.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
            }
            if(substr($coa['coa_code'],0,1) == 'A'){
                $this->sumData[array_search("A.4.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
            }
            if(substr($coa['coa_code'],0,1) == 'B' || substr($coa['coa_code'],0,1) == 'C'){
                if(substr($coa['coa_code'],0,1) == 'C')$this->sumData[array_search("C.7.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
                $this->sumData[array_search("C.7.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
            }
            if($data->coa_parent == 'D.1'){
                if($coa['coa_code'] == 'D.1.1'){
                    $this->sumData[array_search("D.1.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
                }else{
                    $this->sumData[array_search("D.1.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] -= $data->ending_balance;
                }
            }
            if($data->coa_parent == 'D.2'){
                if($coa['coa_code'] == 'D.2.1'){
                    $this->sumData[array_search("D.2.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
                }else if($coa['coa_code'] == 'D.2.2'){
                    $this->sumData[array_search("D.2.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] -= $data->ending_balance;
                }else if($coa['coa_code'] == 'D.2.3'){
                    $this->sumData[array_search("D.2.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
                    $this->sumData[array_search("D.2.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] = 
                        $this->sumData[array_search("D.2.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] + $this->sumData[array_search("D.1.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'];
                }
            }
            if($data->coa_parent == 'E.1' || $coa['coa_code'] == 'E.1'){
                $this->sumData[array_search("E.1.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
            }
            if($data->coa_parent == 'E.2' || $coa['coa_code'] == 'E.2'){
                if($this->sumData[array_search("E.1.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] == 0){
                    $this->sumData[array_search("E.1.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] = 
                        $this->sumData[array_search("D.2.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] - $this->sumData[array_search("E.1.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'];
                }
                $this->sumData[array_search("E.2.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
            }
            if($data->coa_parent == 'E.3' || $data->coa_parent == 'E.3.1' || $coa['coa_code'] == 'E.3'){
                if($this->sumData[array_search("E.2.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] == 0){
                    $this->sumData[array_search("E.2.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] = 
                        $this->sumData[array_search("E.1.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] - $this->sumData[array_search("E.2.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'];
                }
                $this->sumData[array_search("E.3.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
            }
            if($data->coa_parent == 'E.4' || $coa['coa_code'] == 'E.4'){
                if($this->sumData[array_search("E.3.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] == 0){
                    $this->sumData[array_search("E.3.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] = 
                        $this->sumData[array_search("E.2.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] - $this->sumData[array_search("E.3.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'];
                }
                $this->sumData[array_search("E.4.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] += $data->ending_balance;
            }
            if($coa['coa_code'] == 'E.5'){
                if($this->sumData[array_search("E.4.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] == 0){
                    $this->sumData[array_search("E.4.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] = 
                        $this->sumData[array_search("E.3.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] - $this->sumData[array_search("E.4.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'];
                }
                // if($this->sumData[array_search("E.4.9999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] == 0){
                //     $this->sumData[array_search("E.4.9999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] = 
                //         $this->sumData[array_search("E.4.9999-J",array_column($this->sumData, 'coa_code'))]['begining_balance'] - $this->sumData[array_search("E.4.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'];
                // }
                $this->sumData[array_search("E.5.99-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] = 
                    $this->sumData[array_search("E.4.999-J",array_column($this->sumData, 'coa_code'))]['ending_balance'] - $data->ending_balance;
            }

            Coa::where('coa_code',$coa['coa_code'])->update(['begining_balance' => DB::raw('coa.ending_balance'), 'ending_balance' => $data->ending_balance]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function submitYearClosing(Request $request)
    {
        $year = $request->post('year');
        if(Neraca::where('month',"12")->where('year',$year)->doesntExist()){
            // $res = $this->monthlyClosing($last_month,$year);
            echo json_encode(array('status' => false, 'message' => 'Silahkan selesaikan potongan bulanan terlebih dahulu'));
            die();
        }
        
        DB::beginTransaction();
        try {
            $this->generateSumData();
            $order = Coa::generateOrder();
            $coas = Coa::where('is_sum','N')->orderByRaw($order)->get()->toArray();
            for ($i=0; $i < count($coas); $i++) {
               $this->generateEndingBalance($year, $coas[$i]);
            }
            /* update ending balance */
                for ($i=0; $i < count($this->sumData); $i++) { 
                    $data = Coa::where('coa_code',$this->sumData[$i]['coa_code'])->first();
                    $data->ending_balance = $this->sumData[$i]['ending_balance'];
                    try {
                        $data->save();
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
            /************************ */

            /*Hitung SHU*/
            if(Shu::where('year',$year)->count() > 0){
                Shu::where('year',$year)->delete();
            }
            if(Pengurus::where('year',$year)->count() > 0){
                Pengurus::where('year',$year)->delete();
            }
            $query = Shu::mainQuery();
            $data = $query
                    ->leftJoin(
                        DB::raw("(select ending_balance from coa where coa_code='E.5.99-J') `bal`"), 
                        DB::raw("1"), '=', DB::raw("1")
                    )
                    ->where('parameter.param','point_shu')
                    ->where('parameter.is_active','Y')
                    ->select(DB::raw('parameter.value, "'.$year.'", (bal.ending_balance * (param.value / 100)) as jumlah ,param.value as persentase, "'.Auth::user()->getUsername().'" '));
            DB::table('shu')->insertUsing(['point_shu','year','jumlah','persen','created_by'], $data);
            $shu = $data->get()->toArray();
            /*********** */

            /* Bagi SHU Iuran */  
            $queryIuran = Iuran::getTotalIuran()
                    ->where('iuran.type',0)
                    ->where('status',1)
                    ->where('iuran.year','<=',$year)
                    ->orWhere(function($q) use($year) {
                        $q->where('anggota.is_active', 'Y')
                            ->whereRaw('YEAR(relieve_date) = "'.$year.'" ');
                    })
                    ->groupBy('iuran.no_anggota')
                    ->select(DB::raw('iuran.no_anggota, SUM(amount) as total'));
            $iuran = $queryIuran->get()->toArray();
            $total = array_sum(array_column($iuran,'total'));
            $persentase_idx = array_search("anggota_iuran",array_column($shu,'value'));
            $persentase_iuran = $shu[$persentase_idx]->jumlah;
            $shu_blm_dibagi = (Coa::where('coa_parent','C.7')->orderByRaw('CAST(SUBSTRING(coa.coa_code, 5) AS UNSIGNED) DESC')->first())->ending_balance ?? 0;
            $pembagi = $persentase_iuran+$shu_blm_dibagi;
            $pengali_iuran = $total > 0 ? $pembagi / $total : 0;
            for ($i=0; $i < count($iuran); $i++) { 
                $shu_iuran = $iuran[$i]->total * $pengali_iuran;
                $anggota = Anggota::where('no_anggota',$iuran[$i]->no_anggota)->first();
                if(isset($anggota)){
                    $anggota->shu_iuran = $shu_iuran;
                    $anggota->shu_year = $year;
                    try {
                        $anggota->save();
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
            }
            /************/

            /* Bagi SHU Kredit */ 

            $queryMurabahah = Murabahah::whereRaw('YEAR(date) = "'.$year.'" ')->where('type',0);
            $totalMurabahah = $queryMurabahah->sum(DB::raw('nilai_total-nilai_awal'));
            if($totalMurabahah > 0){
                $murabahah = $queryMurabahah->orderBy('no_anggota')->get();
                $persentase_idx = array_search("anggota_jasa",array_column($shu,'value'));
                $persentase_krdt = $shu[$persentase_idx]->jumlah;
                $pengali_krdt = $persentase_krdt / $totalMurabahah;
                $last_murabahah = new \stdClass();
                for ($i=0; $i < count($murabahah); $i++) {
                    $nilai = $murabahah[$i]->nilai_total - $murabahah[$i]->nilai_awal;
                    $shu_krdt = $nilai*$pengali_krdt;
                    $anggota = Anggota::where('no_anggota',$murabahah[$i]->no_anggota)->first();
                    if($murabahah[$i] != $last_murabahah){
                        $anggota->shu_murabahah = $shu_krdt;
                        $last_murabahah = $murabahah[$i];
                    }else{
                        $anggota->shu_murabahah = $anggota->shu_murabahah + $shu_krdt;
                    }
                    try {
                        $anggota->save();
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
            }            
            /*****************/

            /* Bagi SHU Pengurus */ 

            $persentase_idx = array_search("pengurus",array_column($shu,'value'));
            $persentase_pengurus = $shu[$persentase_idx]->jumlah;
            $query = Pengurus::mainQuery();
            $dataPengurus = $query
                    ->where('parameter.param','kepengurusan')
                    ->where('parameter.is_active','Y')
                    ->orderBy('id')
                    ->select(DB::raw('anggota.no_anggota,parameter.value,param.value as persentase,YEAR(NOW())'))
                    ->get()->toArray();

            $dataToInsert = [];

            for ($i=0; $i < count($dataPengurus); $i++) { 
                $element = $dataPengurus[$i];
                if($element->no_anggota === null || trim($element->no_anggota) === ''){
                    DB::rollBack();
                    echo json_encode(array('status' => false, 'message' => 'Silahkan update kepengurusan di Master menu'));
                    die();
                }
                array_push($dataToInsert,[
                    'no_anggota' => $element->no_anggota,
                    'pengurus' => $element->value,
                    'persen' => $element->persentase,
                    'jumlah' => ($element->persentase / 100) * $persentase_pengurus,
                    'year' => $year,
                    'created_by' => Auth::user()->getUsername()
                ]);
            }
            
            Pengurus::insert($dataToInsert);
           
            /*****************/

            $msg = Auth::user()->getUsername(). ' submit closing tahunan succesfully : '.json_encode($shu);              
            Log::info($msg);
            $this->sharedService->logs($msg);
            echo json_encode(array('status' => true, 'message' => 'Process Succesfully'));
            DB::commit();

        } catch (Exception $e) {
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully submit closing tahunan: '.$e->getMessage());       
            echo json_encode(array('status' => false, 'message' => 'Proccess Unsuccessfully'));
            DB::rollBack();
        }
    }

    public function getLastYearClosing()
    {
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => Shu::max('year')));
    }

}
