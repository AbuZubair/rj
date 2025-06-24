<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use App\Coa;
use App\Iuran;
use App\Anggota;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $sharedService;
    private $model;

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
        return view('report.index');
    }

    public function getData(Request $request)
    {       
        if($request->input('type') == 'iuran'){
            $query = Iuran::getTotalIuran();
                        
            if($request->input('searchTypeIuran')!='')$query->where('iuran.type',$request->input('searchTypeIuran'));
            $year = ($request->input('searchUntilYear')!='')?$request->input('searchUntilYear'):date('Y');
            $query->where('iuran.year','<=',$year);
            if($request->input('searchAnggota')!='')$query->where('iuran.no_anggota',$request->input('searchAnggota'));
            if(in_array(Auth::user()->getRole(), [1]))$query->where('iuran.no_anggota',Auth::user()->getNoAnggota());
            $query->where('status',1);
            
            $data = $query
            ->groupBy('iuran.no_anggota','iuran.type','anggota.fullname','c.max_month', 'b.year', 'return_val.return_total')
            ->orderBy('iuran.year', 'desc')
            ->orderBy('month', 'desc')
            ->select(DB::raw('c.max_month as month, b.year as max_year, "'.$year.'" as cur_year, param.label as type_iuran, anggota.fullname, iuran.no_anggota, SUM(amount) as total, ref.label as ref, return_val.return_total '))
            ->get()->toArray();                              
            return Datatables::of($data)               
                ->make(true);
        }else if($request->input('type') == 'potongan'){
            $query = DB::table('anggota')
                            ->leftJoin(
                                DB::raw("(
                                    SELECT SUM(amount) AS iuran, no_anggota FROM iuran WHERE (TYPE = '0' OR TYPE='1') AND 
                                    STATUS = 1 AND MONTH ='".$request->input('searchMonth')."' AND YEAR='".$request->input('searchYear')."' GROUP BY no_anggota
                                ) `a` "), 
                                'a.no_anggota', '=', 'anggota.no_anggota'
                            )
                            ->leftJoin(
                                DB::raw("(
                                    SELECT SUM(amount) AS piutang, murabahah.no_anggota 
                                    FROM transaction 
                                    INNER JOIN murabahah ON transaction.no_murabahah = murabahah.no_murabahah
                                    WHERE trans_type='murabahah' AND 
                                    coa_code='A.1.2' AND dk='kredit' AND trans_month ='".$request->input('searchMonth')."' AND trans_year='".$request->input('searchYear')."' GROUP BY no_anggota
                                ) `b` "), 
                                'b.no_anggota', '=', 'anggota.no_anggota'
                            )
                            ->leftJoin(
                                DB::raw("(
                                    SELECT SUM(amount) AS tabungan, no_anggota FROM iuran WHERE TYPE = '4' AND 
                                    STATUS = 1 AND MONTH ='".$request->input('searchMonth')."' AND YEAR='".$request->input('searchYear')."' GROUP BY no_anggota
                                ) `c` "), 
                                'c.no_anggota', '=', 'anggota.no_anggota'
                            )
                            ->leftJoin(
                                DB::raw("(
                                    SELECT SUM(amount) AS sembako, no_anggota
                                    FROM transaction 
                                    WHERE trans_type='sembako' AND 
                                    coa_code='A.1.2' AND dk='kredit' AND trans_month ='".$request->input('searchMonth')."' AND trans_year='".$request->input('searchYear')."' GROUP BY no_anggota
                                ) `d` "), 
                                'd.no_anggota', '=', 'anggota.no_anggota'
                            )
                            ->leftJoin(
                                DB::raw("(select * from `parameter` where `parameter`.`param` = 'grade') `param`"), 
                                'param.value', '=', 'anggota.grade'
                            );
            $query->where('anggota.is_active',"Y");
            if($request->input('searchAnggota')!='')$query->where('anggota.no_anggota',$request->input('searchAnggota'));
            if(in_array(Auth::user()->getRole(), [1]))$query->where('anggota.no_anggota',Auth::user()->getNoAnggota());
            if($request->input('searchGrade')!=''){
                if($request->input('searchGrade') == 'staff')$query->where('anggota.grade',$request->input('searchGrade'));
                    else $query->where('anggota.grade','!=','staff');
            };
            $data = $query
            ->select(DB::raw("anggota.no_anggota, param.label as grade, anggota.fullname, a.iuran, 
                    (case when b.piutang IS NOT NULL then b.piutang ELSE 0 END) AS piutang, 
                    (case when c.tabungan IS NOT NULL then c.tabungan ELSE 0 END) AS tabungan,  
                    (case when d.sembako IS NOT NULL then d.sembako ELSE 0 END) AS sembako,  
                    (a.iuran + (case when b.piutang IS NOT NULL then b.piutang ELSE 0 END) + (case when c.tabungan IS NOT NULL then c.tabungan ELSE 0 END) + (case when d.sembako IS NOT NULL then d.sembako ELSE 0 END)) AS total"))
            ->get()->toArray();                              
            return Datatables::of($data)               
                ->make(true);
        }else if($request->input('type') == 'transaction'){
            $query = DB::table('transaction')   
                ->select(DB::raw('transaction.*, CONCAT("TRN",LPAD(transaction.id, 8, "0")) as trans_no, CASE WHEN transaction.dk ="kredit" THEN transaction.amount ELSE NULL END AS kredit, CASE WHEN transaction.dk ="debit" THEN transaction.amount ELSE NULL END AS debit, coa_code, DATE_FORMAT(CONCAT(trans_year,"-",trans_month,"-",trans_date), "%d %M %Y") as groupDate'));
                // $query->where('trans_type','!=','murabahah');
                if($request->input('searchStartDate')!='' && $request->input('searchEndDate')!=''){
                    $query->whereBetween(DB::raw("STR_TO_DATE(CONCAT(trans_month,' ',trans_date,' ',trans_year), '%m %d %Y')"),[$request->input('searchStartDate'),$request->input('searchEndDate')]);
                }
                if($request->input('searchYear')!='')$query->where('trans_year',$request->input('searchYear'));
                if($request->input('searchMonth')!='')$query->where('trans_month',$request->input('searchMonth'));
                if($request->input('searchDate')!='')$query->whereRaw("STR_TO_DATE(CONCAT(trans_month,' ',trans_date,' ',trans_year), '%m %d %Y') ='". $request->input('searchDate')."' ");
                if($request->input('searchCoa')!='')$query->where('transaction.coa_code',$request->input('searchCoa'));
                              
                $data = $query
                    ->orderBy('trans_year', 'desc')
                    ->orderBy('trans_month', 'desc')
                    ->orderBy('trans_date', 'desc')
                    ->get()->toArray();                              
                return Datatables::of($data)               
                    ->make(true);
        }else if($request->input('type') == 'piutang'){
            $query = DB::table('murabahah')
                ->leftJoin('anggota', 'anggota.no_anggota', '=', 'murabahah.no_anggota')
                ->leftJoin(
                    DB::raw("(select * from `parameter` where `parameter`.`param` = 'grade') `ref`"), 
                    'ref.value', '=', 'anggota.grade'
                )
                ->select(DB::raw('murabahah.*,anggota.fullname, ref.label as grade,(murabahah.nilai_total - murabahah.nilai_pembayaran) AS sisa_pembayaran, (murabahah.margin - murabahah.deduction) AS remaining_deduction'));
            if($request->input('searchAnggota')!='')$query->where('murabahah.no_anggota',$request->input('searchAnggota'));
            if($request->input('searchGrade')!=''){
                if($request->input('searchGrade') == 'staff')$query->where('anggota.grade',$request->input('searchGrade'));
                    else $query->where('anggota.grade','!=','staff');
            };
            if(in_array(Auth::user()->getRole(), [1]))$query->where('murabahah.no_anggota',Auth::user()->getNoAnggota());
            $data = $query
                ->get()->toArray();                              
            return Datatables::of($data)               
                ->make(true);
        }else if($request->input('type') == 'shu'){
            $query = Anggota::whereRaw('YEAR(relieve_date) = (select MAX(shu_year) from anggota)')->orWhere('is_active', 'Y');
            if($request->input('searchAnggota')!='')$query->where('no_anggota',$request->input('searchAnggota'));
            if(in_array(Auth::user()->getRole(), [1]))$query->where('no_anggota',Auth::user()->getNoAnggota());
            $data = $query
                ->select(DB::raw('anggota.*,(anggota.shu_iuran + anggota.shu_murabahah) as total'))
                ->get()->toArray();                              
            return Datatables::of($data)               
                ->make(true);
        }else if($request->input('type') == 'sales'){
            $query = DB::table('sales')   
                ->select(DB::raw('sales.*,CASE WHEN sales.payment_type = "cash" OR (sales.payment_type = "piutang" AND sales.status = 1) THEN sales.charge_amount ELSE 0 END as total_cash,CASE WHEN sales.status = 0 THEN sales.charge_amount ELSE 0 END as total_piutang,DATE_FORMAT(sales_date, "%d-%m-%Y") as sales_date,DATE_FORMAT(sales_date, "%d %M %Y") as groupDate'));
                if($request->input('searchYear')!='')$query->whereRaw("YEAR(sales_date) ='".$request->input('searchYear')."' ");
                if($request->input('searchMonth')!='')$query->whereRaw("MONTH(sales_date) ='".$request->input('searchMonth')."' ");
                if($request->input('searchDate')!='')$query->where("sales_date", $request->input('searchDate'));
                              
                $data = $query
                    ->orderBy('sales_date', 'desc')
                    ->get()->toArray();                              
                return Datatables::of($data)               
                    ->make(true);
        }else if($request->input('type') == 'angsuran'){
            $query = DB::table('transaction')
                    ->leftJoin('murabahah', 'transaction.no_murabahah', '=', 'murabahah.no_murabahah')
                    ->leftJoin('anggota', 'anggota.no_anggota', '=', 'murabahah.no_anggota')
                    ->select(DB::raw('transaction.*,murabahah.*,anggota.fullname'))
                    ->where('trans_type','murabahah')
                    ->where('coa_code','A.1.2')
                    ->where('dk','kredit');
            if($request->input('searchAnggota')!='')$query->where('murabahah.no_anggota',$request->input('searchAnggota'));
            if(in_array(Auth::user()->getRole(), [1]))$query->where('murabahah.no_anggota',Auth::user()->getNoAnggota());
            if($request->input('searchYear')!='')$query->where('trans_year',$request->input('searchYear'));
            if($request->input('searchMonth')!='')$query->where('trans_month',$request->input('searchMonth'));
            if($request->input('searchKredit')!='')$query->where('transaction.no_murabahah',$request->input('searchKredit'));
            $data = $query
                ->orderBy('trans_year', 'desc')
                ->orderBy('trans_month', 'desc')
                ->get()->toArray();                              
            return Datatables::of($data)               
                ->make(true);
        }else if($request->input('type') == 'tb'){
            $order = Coa::generateOrder();
            $query = DB::table('coa')
                    ->leftJoin(
                        DB::raw("(SELECT begining_balance, ending_balance, debit, kredit, coa_code FROM neraca 
                        WHERE YEAR = (SELECT MAX(YEAR) FROM neraca) AND 
                        MONTH = (SELECT MAX(MONTH) FROM neraca WHERE YEAR = (SELECT MAX(YEAR) FROM neraca))) `a`"), 
                        'a.coa_code', '=', 'coa.coa_code'
                    )
                    ->where('is_sum','N')
                    ->where('coa_level','>','2')
                    ->select(DB::raw('coa.coa_code, coa.coa_name, coa.coa_level, coa.is_sum, coa.begining_balance, a.ending_balance, a.debit, a.kredit'));
            $data = $query->orderByRaw($order)->get()->toArray();                              
            return Datatables::of($data)               
                ->make(true);
        }else if($request->input('type') == 'neraca'){
            $order = Coa::generateOrder();
            $query = DB::table('coa')
                    ->leftJoin(
                        DB::raw("(SELECT debit, kredit, coa_code FROM neraca 
                        WHERE YEAR = (SELECT MAX(YEAR) FROM neraca) AND 
                        MONTH = (SELECT MAX(MONTH) FROM neraca WHERE YEAR = (SELECT MAX(YEAR) FROM neraca))) `a`"), 
                        'a.coa_code', '=', 'coa.coa_code'
                    )
                    ->whereRaw("SUBSTRING(coa.coa_code, 1, 1) IN ('A','B','C')")
                    ->select(DB::raw('coa.*,a.debit,a.kredit'));
            $data = $query->orderByRaw($order)->get()->toArray();                              
            return Datatables::of($data)               
                ->make(true);
        }else if($request->input('type') == 'laba_rugi'){
            $order = Coa::generateOrder();
            $query = DB::table('coa')
                    ->leftJoin(
                        DB::raw("(SELECT debit, kredit, coa_code FROM neraca 
                        WHERE YEAR = (SELECT MAX(YEAR) FROM neraca) AND 
                        MONTH = (SELECT MAX(MONTH) FROM neraca WHERE YEAR = (SELECT MAX(YEAR) FROM neraca))) `a`"), 
                        'a.coa_code', '=', 'coa.coa_code'
                    )
                    ->whereRaw("SUBSTRING(coa.coa_code, 1, 1) NOT IN ('A','B','C')")
                    ->select(DB::raw('coa.*,a.debit,a.kredit'));
            $data = $query->orderByRaw($order)->get()->toArray();                              
            return Datatables::of($data)               
                ->make(true);
        }
        
    }

    public function getDropdown()
    {
        
        $data = $this->sharedService->getDropdown();
        echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    function distinctProject( $arr ) {
        $models = array_map( function( $arr ) {
            return $arr->project;
        }, $arr );
    
        $unique_models = array_unique( $models );
    
        return array_values( array_intersect_key( $arr, $unique_models ) );
    }
   

}
