<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Anggota;
use App\Iuran;
use App\Murabahah;
use App\Library\Services\Shared;

class Potongan extends Model
{
    protected $table = "potongan";

    public $primaryKey = "id";

    const CREATED_AT = 'created_date';

    const UPDATED_AT = 'updated_date';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [    
        'id',
        'month',
        'year',
        'status',
        'created_date',
        'created_by',
        'updated_date',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public static function generatePotongan($request){
        try {

            $shared = new Shared;
            $month =  $shared->getMonthIndex($request->post('month'));
            $bank = 0;
            $iuran = DB::table('parameter')->where('parameter.param','iuran')->select('value')->first();
            $class = new Potongan();

            /* Potongan iuran */
            $select_iuran_wajib = Anggota::where('is_active','Y')
            ->whereRaw("no_anggota NOT IN (SELECT no_anggota FROM iuran WHERE month = '".str_pad($month+1, 2, '0', STR_PAD_LEFT)."' AND year = '".$request->post('year')."' AND type=0 )")
            ->select(DB::raw('anggota.no_anggota, "'.str_pad($month+1, 2, '0', STR_PAD_LEFT).'", "'.$request->post('year').'", "0", "'.$iuran->value.'" as amt, "1", "'.date('Y-m-d').'", "'.Auth::user()->getUsername().'" '));
            $sum_iuran = array_sum(array_column($select_iuran_wajib->get()->toArray(),'amt'));
            $bank += $sum_iuran;
            if($sum_iuran > 0)$class->addTransaction($sum_iuran,[str_pad($month+1, 2, '0', STR_PAD_LEFT), $request->post('year')],'C.2', 'Simpanan Wajib '.count($select_iuran_wajib->get()->toArray()).' anggota');

            DB::table('iuran')->insertUsing(['no_anggota','month','year','type','amount','status','paid_date','created_by'], $select_iuran_wajib);
            
            // $bank += array_sum(array_column(Iuran::where('month',str_pad($month+1, 2, '0', STR_PAD_LEFT))->where('year',$request->post('year'))->where('status',0)->get()->toArray(),'amount'));
            // Iuran::where('month',str_pad($month+1, 2, '0', STR_PAD_LEFT))->where('year',$request->post('year'))->where('status',0)->update(['status' => 1]);

            $msg = Auth::user()->getUsername(). 'generate potongan iuran succesfully : '.json_encode($select_iuran_wajib->get()->toArray());              
            // Log::info($msg);
            // $shared->logs($msg);

            /**************** */

            /* Potongan tabungan */

            //tabungan
            $tabungan = Anggota::where('is_active','Y')
            ->where('tabungan','!=','0')
            ->whereRaw('(YEAR(tabungan_date) <= '.$request->post('year').' AND  MONTH(tabungan_date) <= '.($month+1).')')
            ->whereRaw("no_anggota NOT IN (SELECT no_anggota FROM iuran WHERE month = '".str_pad($month+1, 2, '0', STR_PAD_LEFT)."' AND year = '".$request->post('year')."' AND type=3 )");
            $select_tabungan = $tabungan->select(DB::raw('anggota.no_anggota, "'.str_pad($month+1, 2, '0', STR_PAD_LEFT).'", "'.$request->post('year').'", "3", anggota.tabungan, "1", "'.date('Y-m-d').'", "'.Auth::user()->getUsername().'" '));

            if( $select_tabungan->count() != 0 ){
                DB::table('iuran')->insertUsing(['no_anggota','month','year','type','amount','status','paid_date','created_by'], $select_tabungan);
                $tabungan->where('is_tabungan_monthly','N')->update(['tabungan' => '0']);

                $msg = Auth::user()->getUsername(). 'generate potongan tabungan succesfully : '.json_encode($select_tabungan->get()->toArray());              
                // Log::info($msg);
                // $shared->logs($msg);
            }

            //thr
            $thr = Anggota::where('is_active','Y')
            ->where('thr','!=','0')
            ->whereRaw("STR_TO_DATE(CONCAT(".$request->post('year').",'-',".($month+1).",'-',DAY(CURDATE())), '%Y-%m-%d') >= thr_date")
            ->whereRaw("no_anggota NOT IN (SELECT no_anggota FROM iuran WHERE month = '".str_pad($month+1, 2, '0', STR_PAD_LEFT)."' AND year = '".$request->post('year')."' AND type=4 )");
            $select_thr = $thr->select(DB::raw('anggota.no_anggota, "'.str_pad($month+1, 2, '0', STR_PAD_LEFT).'", "'.$request->post('year').'", "4", anggota.thr, "1", "'.date('Y-m-d').'", "'.Auth::user()->getUsername().'" '));

            if( $select_thr->count() != 0 ){
                $bank += array_sum(array_column($select_thr->get()->toArray(),'thr'));
                $class->addTransaction(array_sum(array_column($select_thr->get()->toArray(),'thr')),[str_pad($month+1, 2, '0', STR_PAD_LEFT), $request->post('year')],'B.1.2.1', 'Uang Tabungan Idul Fitri '.$request->post('year'));
                
                DB::table('iuran')->insertUsing(['no_anggota','month','year','type','amount','status','paid_date','created_by'], $select_thr);
                $thr->where('is_thr_monthly','N')->update(['thr' => '0']);

                $msg = Auth::user()->getUsername(). 'generate potongan thr succesfully : '.json_encode($select_thr->get()->toArray());              
                // Log::info($msg);
                // $shared->logs($msg);
            }

            /**************** */

            /* Potongan piutang */
            $day = (date('n') > $month+1 && date('Y') >= $request->post('year'))?date('t',strtotime($request->post('year').'-'.(sprintf("%02d", $month+1)).'-01')):date('d');
            $select_murabahah = Murabahah::
                    leftJoin('anggota', 'anggota.no_anggota', '=', 'murabahah.no_anggota')
                    ->whereRaw("STR_TO_DATE(CONCAT(".($month+1).",' ',".$day.",' ',".$request->post('year')."), '%m %d %Y') >= murabahah.date")
                    ->where('status','<',2)
                    ->whereRaw("no_murabahah NOT IN (SELECT no_murabahah FROM transaction WHERE trans_month = '".str_pad($month+1, 2, '0', STR_PAD_LEFT)."' AND trans_year = '".$request->post('year')."' AND trans_type='murabahah' AND dk='kredit' AND coa_code='A.1.2' )")
                    ->select(DB::raw('"'.str_pad($month+1, 2, '0', STR_PAD_LEFT).'", "'.$request->post('year').'", "'.date('d').'", murabahah.no_murabahah, (CASE WHEN (murabahah.margin - murabahah.deduction = 1) THEN (murabahah.nilai_total - murabahah.nilai_pembayaran) WHEN (murabahah.deduction = 0 AND murabahah.nilai_transport != 0) THEN (murabahah.angsuran + murabahah.nilai_transport)  ELSE murabahah.angsuran END), "murabahah", "kredit", "A.1.2", CONCAT("Piutang Usaha a/n-", anggota.fullname, " (Auto Generate): ", no_murabahah), "'.Auth::user()->getUsername().'" '));
            $data_murabahah = $select_murabahah->get();
            DB::table('transaction')->insertUsing(['trans_month','trans_year','trans_date','no_murabahah','amount','trans_type','dk','coa_code','tans_desc','created_by'], $select_murabahah);
           
            for ($i=0; $i < count($data_murabahah); $i++) { 
                $data_toupdate = Murabahah::where('no_murabahah',$data_murabahah[$i]->no_murabahah)->first();
                $angsuran = ($data_toupdate->nilai_total-$data_toupdate->nilai_pembayaran < $data_toupdate->angsuran)?$data_toupdate->nilai_total-$data_toupdate->nilai_pembayaran:(($data_toupdate->deduction == 0 AND $data_toupdate->nilai_transport != 0)?$data_toupdate->angsuran+$data_toupdate->nilai_transport:$data_toupdate->angsuran);
                $pembayaran = $data_toupdate->nilai_pembayaran + $angsuran;
                $data_toupdate->nilai_pembayaran = $pembayaran;
                $bank += $angsuran;
                $data_toupdate->status = ($pembayaran == $data_toupdate->nilai_total)?2:1;
                $data_toupdate->deduction = $data_toupdate->deduction + 1;
                $data_toupdate->save();
            }

            $msg = Auth::user()->getUsername(). 'generate potongan piutang succesfully : '.json_encode($select_murabahah->get()->toArray());              
            // Log::info($msg);
            $shared = new Shared;
            // $shared->logs($msg);

            /**************** */

            /* Potongan sembako */

            $select_sembako = DB::table('transaction')         
                    ->leftJoin('anggota', 'anggota.no_anggota', '=', 'transaction.no_anggota')           
                    ->where('trans_month',$month+1)
                    ->where('trans_year',$request->post('year'))
                    ->where('trans_type','sembako')
                    ->where('coa_code', 'A.1.2')
                    ->where('dk', 'debit')
                    ->whereRaw("transaction.no_anggota NOT IN (SELECT no_anggota FROM transaction WHERE trans_month = '".str_pad($month+1, 2, '0', STR_PAD_LEFT)."' AND trans_year = '".$request->post('year')."' AND trans_type='sembako' AND dk='kredit' AND coa_code='A.1.2' ) ")
                    ->select(DB::raw('"'.str_pad($month+1, 2, '0', STR_PAD_LEFT).'", "'.$request->post('year').'", "'.date('d').'", transaction.no_anggota, amount, trans_type, "kredit", "A.1.2", CONCAT("Piutang Usaha a/n-", anggota.fullname, " (Auto Generate)"), "'.Auth::user()->getUsername().'" '));

            DB::table('transaction')->insertUsing(['trans_month','trans_year','trans_date','no_anggota','amount','trans_type','dk','coa_code','tans_desc','created_by'], $select_sembako);

            /**************** */

            /* Add bank akun */

            $year = $request->post('year');
            $month = str_pad($month+1, 2, '0', STR_PAD_LEFT);
            $day = date('d');
            $arr = [
                ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'amount' => $bank, 'trans_type' => 'other', 'dk' => 'debit', 'coa_code' => 'A.1.1.1', 'tans_desc' => 'Bank - Potongan-'.$month.'-'.$year, 'created_by' => Auth::user()->getUsername()]
            ];
            if($bank>0)DB::table('transaction')->insert($arr);

            /*************** */

            return true;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function addTransaction($data, $date, $coa, $desc)
    {
        $year = $date[1];
        $month = $date[0];
        $day = date('d');
        $arr = [
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'amount' => $data, 'trans_type' => 'other', 'dk' => 'kredit', 'coa_code' => $coa, 'tans_desc' => $desc, 'created_by' => Auth::user()->getUsername()],
        ];
        DB::table('transaction')->insert($arr);
    }
}
