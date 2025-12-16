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
use App\Transaction;
use Carbon\Carbon;

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

    public function view($request)
    {
        return view('report.'.$request);
    }

    public function getData(Request $request, $req)
    {       
        $data = null;
        if($req === 'iuran'){
            $data = Iuran::getReportList($request);
            return Datatables::of($data)               
                ->make(true);
        }else if($req === 'trans'){
            $data = Transaction::getReportList($request);
            return Datatables::of($data)               
                ->make(true);
        }else if($req === 'aruskas'){
            // Get month and year from request, default to current if not provided
            $month = $request->input('month');
            $year = $request->input('year');
            if (empty($month) || empty($year)) {
                $now = Carbon::now();
                $month = $now->month;
                $year = $now->year;
            }

            // Get all COA level 3+ with their parent and grandparent info
            $coas = DB::table('coa as c')
                ->leftJoin('neraca as n', 'n.coa_code', '=', 'c.coa_code')
                ->where('c.coa_level', '>=', 3)
                ->select(
                    'c.coa_code',
                    'c.coa_name',
                    'c.coa_level',
                    'c.coa_parent',
                    DB::raw('(SELECT coa_parent FROM coa WHERE coa_code = c.coa_parent) as parent_code'),
                    DB::raw('(SELECT coa_name FROM coa WHERE coa_code = c.coa_parent) as parent_name'),
                    DB::raw('(SELECT coa_parent FROM coa WHERE coa_code = (SELECT coa_parent FROM coa WHERE coa_code = c.coa_parent)) as grandparent_code'),
                    DB::raw('(SELECT coa_name FROM coa WHERE coa_code = (SELECT coa_parent FROM coa WHERE coa_code = c.coa_parent)) as grandparent_name')
                )
                ->get();

            // Get balances for each coa_code for the given month/year
            $balances = DB::table('neraca')
                ->where('month', (int)$month)
                ->where('year', $year)
                ->select('coa_code', 'begining_balance', 'ending_balance')
                ->get()
                ->keyBy('coa_code');

            // Group by grandparent (level 1) and parent (level 2)
            $result = [];
            $grouped = [];
            foreach ($coas as $coa) {
                $parent_code = $coa->coa_parent;
                $grandparent = Coa::where('coa_code', $parent_code)->first();
                $grandparent_code = $grandparent->coa_code;

                if (!isset($grouped[$grandparent_code])) {
                    $grouped[$grandparent_code] = [
                        'code' => $grandparent_code,
                        'uraian' => $grandparent->coa_name,
                        'rincian' => []
                    ];
                }

                $balance = $balances->get($coa->coa_code);
                $grouped[$grandparent_code]['rincian'][] = [
                    'rincian' => $coa->coa_name,
                    'coa_code' => $coa->coa_code,
                    'begining_balance' => $balance ? $balance->begining_balance : 0,
                    'ending_balance' => $balance ? $balance->ending_balance : 0
                ];
            }

            $data = array_values($grouped);
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
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
