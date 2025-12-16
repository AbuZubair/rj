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
use App\Siswa;
use App\BiayaSiswa;

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

    public function getLastClosing()
    {
        $data = Neraca::select('month', 'year','created_date')->groupByRaw('month,year,created_date')->orderByDesc('year')->orderByDesc('month')->first();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
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
        DB::beginTransaction();
        if($last_month == $month && $year == $last_year){
            $neracas = Neraca::where('month',$month)->where('year',$year);
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
    }

    private function monthlyClosing($month,$year){
        try {
            $order = Coa::generateOrder();
            $coas = Coa::select('coa_code','begining_balance','ending_balance')->orderByRaw($order)->get()->toArray();
            for ($i=0; $i < count($coas); $i++) { 
                $debit = Transaction::where('coa_code',$coas[$i]['coa_code'])->where('dk','debit')->where('trans_year', $year)->where('trans_month','=',$month)->sum('amount');
                $kredit = Transaction::where('coa_code',$coas[$i]['coa_code'])->where('dk','kredit')->where('trans_year', $year)->where('trans_month','=',$month)->sum('amount');
                
                if($coas[$i]['coa_code'] == config('app.coa_piutang_um')){
                    $piutang_um = \App\BiayaSiswa::where('status_um', 0)
                        ->selectRaw('SUM(uang_masuk - um_masuk) as balance')
                        ->value('balance');                    
                    $debit = $piutang_um;
                }

                if($coas[$i]['coa_code'] == config('app.coa_piutang_du')){
                    $piutang_du = \App\BiayaSiswa::where('status_du', 0)
                        ->selectRaw('SUM(daftar_ulang - du_masuk) as balance')
                        ->value('balance');                    
                    $debit = $piutang_du;
                }

                if($coas[$i]['coa_code'] == config('app.coa_piutang_spp')){
                    // Hitung total piutang SPP sampai bulan closing
                    // Jika spp_terakhir null, asumsikan siswa baru, mulai wajib bayar bulan 7 (Juli) tahun berjalan
                    $piutang_spp = Siswa::join('biaya_siswa', function($join) {
                            $join->on('siswa.nis', '=', 'biaya_siswa.nis');
                        })
                        ->select('siswa.nis', 'siswa.spp_terakhir', DB::raw('(
                            SELECT spp FROM biaya_siswa bs2
                            WHERE bs2.nis = siswa.nis
                            ORDER BY bs2.th_ajaran DESC
                            LIMIT 1
                            ) as spp'))
                        ->where('siswa.is_active', 'Y')
                        ->get()
                        ->sum(function($row) use ($month, $year) {
                            $spp = (int)$row->spp;
                            if (!$spp) return 0;

                            if (!$row->spp_terakhir) {
                                // Siswa baru, mulai wajib bayar bulan 7 (Juli) tahun berjalan
                                $start_month = 7;
                                $start_year = $year;
                            } else {
                                [$last_year, $last_month] = explode('-', $row->spp_terakhir);
                                $start_month = (int)$last_month;
                                $start_year = (int)$last_year;
                            }

                            // Hitung selisih bulan
                            $diff = (($year - $start_year) * 12) + ($month - $start_month);                            
                            if ($diff <= 0) return 0;

                            return $diff * $spp;
                        });
                    $debit = $piutang_spp;
                }

                $code = $coas[$i]['coa_code'];
                if (preg_match('/^A\.1\.(\d+)$/', $code, $m)) {
                    $num = (int)$m[1];
                    if ($num >= 1 && $num <= 12) {
                        $jenjangMap = ['paud', 'tk', 'sd', 'sma']; // posisi 1..4 => PAUD..SMA
                        $pos = ($num - 1) % 4;
                        $jenjang = $jenjangMap[$pos];
                        $debit = Iuran::where('jenjang', $jenjang)
                            ->where('is_beasiswa', 'N')
                            ->sum('amount');
                    }
                }

                $coas[$i]['debit'] = $debit;
                $coas[$i]['kredit'] = $kredit;
                $coas[$i]['month'] = $month;
                $coas[$i]['year'] = $year;
                $coas[$i]['created_by'] = Auth::user()->getUsername();

                /* calculate ending balance */
                $balance = null;
                /**
                 * Get last neraca data
                 */
                $last_month = $month == 1 ? 12 : $month - 1;
                $last_year = $month == 1 ? $year - 1 : $year;
                $last_neraca = Neraca::where('coa_code',$coas[$i]['coa_code'])
                                ->where('year',$last_year)
                                ->where('month',$last_month)
                                ->first();
                if(isset($last_neraca)){                     
                    $balance = $last_neraca->ending_balance; 
                    $coas[$i]['begining_balance'] = $balance;
                }else{
                    $balance = $coas[$i]['ending_balance'];
                }

                $coas[$i]['ending_balance'] = $balance+$coas[$i]['debit']-$coas[$i]['kredit'];
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

    private function generateEndingBalance($year, $coa)
    {
        try {
            $data = DB::table('neraca')
                ->where('year',$year)
                ->whereRaw('month = (SELECT MAX(month) from neraca where year = '.$year.')')
                ->where('neraca.coa_code',$coa['coa_code'])
                ->leftJoin('coa', 'coa.coa_code', '=', 'neraca.coa_code')
                ->select('neraca.*', 'coa.coa_parent')->get()->first();

            Coa::where('coa_code',$coa['coa_code'])->update(['begining_balance' => DB::raw('coa.ending_balance'), 'ending_balance' => $data->ending_balance, 'year' => $year]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function submitYearClosing(Request $request)
    {
        $year = $request->post('year');
        if(Neraca::where('month',"12")->where('year',$year)->doesntExist()){
            echo json_encode(array('status' => false, 'message' => 'Silahkan selesaikan potongan bulanan terlebih dahulu'));
            die();
        }
        
        DB::beginTransaction();
        try {
            $order = Coa::generateOrder();
            $coas = Coa::orderByRaw($order)->get()->toArray();
            for ($i=0; $i < count($coas); $i++) {
               $this->generateEndingBalance($year, $coas[$i]);
            }

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
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => Coa::max('year')));
    }

}
