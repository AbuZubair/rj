<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Sales;
use App\Anggota;
use App\Http\Requests\SalesRequest;
use Maatwebsite\Excel\Excel;
use App\Exports\SalesDetailExport;
use charlieuki\ReceiptPrinter\ReceiptPrinter as ReceiptPrinter;

use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $sharedService;
    private $model;
    private $excel;

    public function __construct(Shared $sharedService,Model $model, Excel $excel)
    {
        $this->sharedService = $sharedService;
        $this->model = $model;
        $this->excel = $excel;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        return view('sales.index');
    }

    public function crud(SalesRequest $request, $req)
    {
        DB::beginTransaction();
        try{
            $data = new Sales;
            $data->sales_no = $request->input('sales_no');
            $data->sales_code = '700';
            $data->sales_date = date('Y-m-d');
            $data->charge_amount = (int)str_replace(".","",$request->input('charge_amount'));
            $data->note = $request->input('note');
            $payment_type = $request->input('payment_type');
            $data->payment_type = $payment_type;
            if($payment_type=='potongan_anggota')$data->no_anggota = $request->input('no_anggota');
            $data->status = ($payment_type == 'piutang')?0:1;
            $data->created_by = Auth::user()->getUsername();
            if($data->save()){  
                $detail = Sales::saveDetail($request->input(),$data->sales_code);
                if($detail){
                    $msg = Auth::user()->getUsername().' save sales succesfully : '.json_encode($data);              
                    Log::info($msg);
                    $this->sharedService->logs($msg);

                    /****Sembako Anggota****/
                    if($payment_type=='potongan_anggota'){
                        $this->addTransaction($data);
                    }
                    /********************* */
                    $data->items = $detail;
                    $this->receiptPrint($data);
                    echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
                    DB::commit();
                }
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully save sales: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
            DB::rollBack();
        }
    }

    public function getDetail(Request $request)
    {
        $data =  DB::table('sales_detail')
            ->leftJoin('item', 'item.item_code', '=', 'sales_detail.item_code')
            ->select(DB::raw('sales_detail.*,item.item_name'))
            ->where('sales_no',$request->get('sales_no'))
            ->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function exportDetail(Request $request)
    {
        return $this->excel->download(new SalesDetailExport($request->query('searchYear'),$request->query('searchMonth'),$request->query('searchDate')), 'sales_detail'.date('dmYHis').'.xlsx');
    }

    public function addTransaction($data)
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $anggota = Anggota::where('no_anggota', $data->no_anggota)->first();
        $arr = [           
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'amount' => $data->charge_amount, 'no_anggota' => $data->no_anggota, 'trans_type' => 'sembako', 'dk' => 'debit', 'coa_code' => 'A.1.2', 'tans_desc' => 'Piutang Usaha (Sembako) a/n '.$anggota->fullname, 'created_by' => Auth::user()->getUsername()],
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'amount' => $data->charge_amount, 'no_anggota' => $data->no_anggota, 'trans_type' => 'sembako', 'dk' => 'kredit', 'coa_code' => 'D.2.1', 'tans_desc' => 'Penjualan a/n '.$anggota->fullname, 'created_by' => Auth::user()->getUsername()]
        ];        
        DB::table('transaction')->insert($arr);
    }

    public function receiptPrint($data)
    {
        // Set params
        $mid = '-';
        $store_name = config('app.name');
        $store_address = config('app.address');
        $store_phone = '';
        $store_email = '';
        $store_website = '';
        $tax_percentage = 0;
        $transaction_id = $data->sales_no;
        $currency = 'Rp';
       
        // Set items
        $items = $data->items;

        // Init printer
        $printer = new ReceiptPrinter;
        $printer->init(
            config('receiptprinter.connector_type'),
            config('receiptprinter.connector_descriptor')
        );

        // Set store info
        $printer->setStore($mid, $store_name, $store_address, $store_phone, $store_email, $store_website);

        // Set currency
        $printer->setCurrency($currency);

        // Add items
        foreach ($items as $item) {
            $printer->addItem(
                $item['name'],
                $item['qty'],
                $item['price']
            );
        }
        // Set tax
        $printer->setTax($tax_percentage);

        // Calculate total
        $printer->calculateSubTotal();
        $printer->calculateGrandTotal();

        // Set transaction ID
        $printer->setTransactionID($transaction_id);

        // Set logo
        // Uncomment the line below if $image_path is defined
        //$printer->setLogo($image_path);

        // Set QR code
        $printer->setQRcode([
            'tid' => $transaction_id,
        ]);

        // Print receipt
        $printer->printReceipt();
    }

    public function getHutang()
    {
        $data = Sales::where("status",0)->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function bayarHutang(Request $request)
    {
        $data = Sales::find($request->post('id'));
        $data->status = 1;
        if($data->save()){  
            $msg = Auth::user()->getUsername().' bayar hutang sales succesfully : '.json_encode($data);              
            Log::info($msg);
            $this->sharedService->logs($msg);
            echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
        }
    }
}
