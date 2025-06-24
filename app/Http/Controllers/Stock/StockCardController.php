<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Stock;

use Illuminate\Http\Request;

class StockCardController extends Controller
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
        return view('stock.stock_card.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data =  Stock::getStockCard();
            return Datatables::of($data)               
                ->make(true);
        }
    }

}
