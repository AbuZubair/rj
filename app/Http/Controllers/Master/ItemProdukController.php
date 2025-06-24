<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\ItemProduk;
use App\Http\Requests\Master\ProdukRequest;
use App\Imports\ProdukImport;
use App\Exports\ProdukMultiSheetExport;

use Illuminate\Http\Request;

class ItemProdukController extends Controller
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
        return view('master.item_produk.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data =  ItemProduk::getData();
            return Datatables::of($data)               
                ->make(true);
        }
    }

    public function add()
    {
        $type = 'Add';
        return view('master.item_produk.form',compact('type'));
    }

    public function edit(Request $req)
    {
        $id = $req->get('id');
        $data = ItemProduk::find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(ProdukRequest $request, $req)
    {

        try{
            $data = new ItemProduk;
            if($request->input('id') != ''){
                $data = ItemProduk::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();
            }else{
                $data->created_by = Auth::user()->getUsername();
            }
            $data->item_code = ($request->input('item_code')!='')?$request->input('item_code'):time();
            $data->item_name = $request->input('item_name');
            // $data->group_item = $request->input('group_item');
            $data->hpp = (int)str_replace(".","",$request->input('hpp'));
            $data->harga_beli = (int)str_replace(".","",$request->input('harga_beli'));
            $data->harga_jual = (int)str_replace(".","",$request->input('harga_jual'));
            $data->satuan_beli = $request->input('satuan_beli');
            $data->satuan_jual = $request->input('satuan_jual');
            $data->konversi = $request->input('konversi');
            $data->is_active = $request->input('is_active');           
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' produk succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' produk: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
        }
            
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = ItemProduk::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete item produk succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete item produk: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function getByQuery(Request $request)
    {
        $data = ItemProduk::getFullList($request);
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data->toArray()));
    }

    public function getDropdown(Request $request)
    {
        $data = ItemProduk::where('is_active','Y')->orderByDesc('created_date')->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getParams(Request $request)
    {
        $data =  new \stdClass();
        $data->item_grup = $this->sharedService->getParamDropdown('item_grup');
        $data->item_satuan = $this->sharedService->getParamDropdown('item_satuan');
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function store(Request $request)
    {
        $file = $request->file('file');

        $import = new ProdukImport;
        $import->import($file);

        $msg = 'Prosess berhasil dilakukan.';
        $status = true;

        if ($import->failures()->isNotEmpty()) {
            $msg = 'Terdapat '.count($import->failures()).' data gagal disimpan karena duplikasi.';
            $status = false;
        }

        echo json_encode(array('status' => $status, 'message' =>  $msg, 'failure' => $import->failures()));

    }

}
