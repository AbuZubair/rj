<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Yajra\Datatables\Datatables;
use App\Iuran;
use App\Anggota;
use App\Sales;
use App\Murabahah;
use App\ItemTransaction;
use App\Http\Requests\RegOnlineRequest;
use App\Http\Requests\NewPasienRequest;
use PDF;
use stdClass;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $sharedService;
    private $model;

    public function __construct(Shared $sharedService, Model $model)
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
        return view('dashboard');
    }
    
    public function getCardData(Request $request){
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $this->getIuran($request->input('data'))));
    }

    public function getCreditSummary(Request $request){
        $query = Murabahah::_getQuery();
        $query->whereRaw('murabahah.status != "2"');
        if(in_array(Auth::user()->getRole(), [1]))$query->where('anggota.no_anggota',Auth::user()->getNoAnggota());
        $data = $query
            ->groupBy('murabahah.type')
            ->select(DB::raw('SUM(nilai_total) as total, SUM((murabahah.nilai_total - murabahah.nilai_pembayaran)) AS total_sisa,murabahah.type'))
            ->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getIuran($type){
        $query = Iuran::getTotalIuran();
        $query->where('iuran.type',$type)->where('iuran.year','<=',date('Y'))->where('status',1);
        if(in_array(Auth::user()->getRole(), [1]))$query->where('iuran.no_anggota',Auth::user()->getNoAnggota());
        $data = $query
            ->groupBy('iuran.no_anggota','iuran.type','anggota.fullname','c.max_month', 'b.year', 'return_val.return_total')
            ->orderBy('iuran.year', 'desc')
            ->orderBy('month', 'desc')
            ->select(DB::raw('SUM(amount)-COALESCE(return_val.return_total, 0) as total,c.max_month,b.year'))
            ->get()->toArray();
        $return = new stdClass();
        $total = 0;
        for ($i=0; $i < count($data); $i++) { 
            $total += $data[$i]->total;
        }
        $return->total = $total;
        $return->month = ($data)?$data[0]->max_month:'-';
        $return->year = ($data)?$data[0]->year:'-';
        return $return;
    }

    public function getSalesToko()
    {
        $data = Sales::select(DB::raw('SUM(charge_amount) as count, MONTHNAME(sales_date) AS group_date, MAX(updated_date) as last_update'))
        ->whereRaw("YEAR(sales_date) = '".date('Y')."'")
        ->groupByRaw('MONTHNAME(sales_date)')
        ->orderByRaw('MONTH(sales_date) ASC')
        ->get();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getPurchase()
    {
        $data = ItemTransaction::select(DB::raw("SUM(charge_amount) as count, trans_month, MAX(updated_date) as last_update"))
        ->where("trans_year",date('Y'))
        ->where('transaction_code','002')
        ->groupByRaw("trans_month")
        ->orderByRaw('trans_month ASC')
        ->get();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getPertumbuhanAnggota()
    {
        $data = Anggota::select(DB::raw('COUNT(id) as count,YEAR(join_date) AS group_date'))->groupByRaw('YEAR(join_date)')->get();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

}
