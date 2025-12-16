<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Staff;
use App\User;
use App\Imports\StaffImport;
use App\Http\Requests\Master\StaffRequest;

use Illuminate\Http\Request;

class StaffController extends Controller
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
        return view('master.staff.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data =  Staff::getData($request);
            return Datatables::of($data)               
                ->make(true);
        }
    }

    public function add()
    {
        $type = 'Add';
        return view('master.siswa.form',compact('type'));
    }

    public function edit(Request $req)
    {
        $id = $req->get('id');
        $data = Staff::find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(StaffRequest $request)
    {
        $is_update = false;
        try{
            $data = new Staff;
            if($request->input('id') != ''){
                $data = Staff::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();                
                if($request->input('is_active') == 'N')$data->relieve_date = date('Y-m-d');
                if($request->input('is_active') == 'Y' && $data->is_active == 'N'){
                    $data->join_date = date('Y-m-d');
                }
                $is_update = true;
            }else{
                $data->nip = $request->input('nip');
                $data->created_by = Auth::user()->getUsername();
                $data->join_date = $request->input('join_date'); 
            }

            $data->jk = $request->input('jk');
            $data->fullname = $request->input('fullname');
            $data->tempat_lahir = $request->input('tempat_lahir');
            $data->tanggal_lahir = $request->input('tanggal_lahir');
            $data->pendidikan_terakhir = $request->input('pendidikan_terakhir');
            $data->instansi_terakhir = $request->input('instansi_terakhir');
            $data->jurusan = $request->input('jurusan');
            $data->jabatan = $request->input('jabatan');
            $data->jenis_ptk = $request->input('jenis_ptk');
            $data->unit_mengajar = $request->input('unit_mengajar');
            $data->agama = $request->input('agama');
            $data->alamat = $request->input('alamat');
            $data->kelurahan = $request->input('kelurahan');
            $data->kecamatan = $request->input('kecamatan');
            $data->email = $request->input('email');
            $data->phone = $request->input('phone');
            $data->sk_pengangkatan = $request->input('sk_pengangkatan');
            $data->tmt_pengangkatan = $request->input('tmt_pengangkatan');
            $data->lembaga_pengangkatan = $request->input('lembaga_pengangkatan');
            $data->nama_ibu_kandung = $request->input('nama_ibu_kandung');
            $data->status_perkawinan = $request->input('status_perkawinan');
            $data->pekerjaan_pasangan = $request->input('pekerjaan_pasangan');
            $data->keahlian = $request->input('keahlian');
            $data->npwp = $request->input('npwp');
            $data->nama_wajib_pajak = $request->input('nama_wajib_pajak');
            $data->kewarganegaraan = $request->input('kewarganegaraan');
            $data->no_rek = $request->input('no_rek');
            $data->an_rek = $request->input('an_rek');
            $data->nik = $request->input('nik');
            $data->is_active = $request->input('is_active');
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' staff succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                $userQuery = User::where('nip', $data->nip);
                if($is_update){
                    $user = $userQuery->first();
                    if($user != null){
                        $user->email = $data->email;
                        $user->save();
                    }
                }
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' anggota: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
        }
            
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Staff::whereIn('id', $id);
            $msg = Auth::user()->getUsername(). ' delete staff succesfully : '.json_encode($data->get()->toArray());
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete staff: '.$e->getMessage());
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function getDropdown(Request $request)
    {
        $data = Staff::where('is_active','Y')->orderByDesc('created_date')->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getParams(Request $request)
    {
        $data =  new \stdClass();
        $data->jabatan = $this->sharedService->getParamDropdown('jabatan');
        $data->jenis_ptk = $this->sharedService->getParamDropdown('jenis_ptk');
        $data->unit_mengajar = $this->sharedService->getParamDropdown('unit_mengajar');
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function store(Request $request)
    {
        $file = $request->file('file');

        $import = new StaffImport;
        $import->import($file);

        $msg = 'Prosess berhasil dilakukan.';
        $status = true;

        if ($import->failures()->isNotEmpty()) {
            $msg = 'Terdapat '.count($import->failures()).' data gagal disimpan';
            $status = false;
        }

        echo json_encode(array('status' => $status, 'message' =>  $msg, 'failure' => $import->failures()));

    }

}
