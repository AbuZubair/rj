<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Siswa;
use App\BiayaSiswa;
use App\Imports\SiswaImport;
use App\Http\Requests\Master\SiswaRequest;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class SiswaController extends Controller
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
        return view('master.siswa.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data =  Siswa::getData($request);
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
        $data = Siswa::find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(SiswaRequest $request)
    {
        try{
            $isEdit = $request->input('id') != '' ? true : false;
            $data = new Siswa;
            if($isEdit){
                $data = Siswa::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();
                if($request->input('is_active') == 'N')$data->relieve_date = date('Y-m-d');
                if($request->input('is_active') == 'Y' && $data->is_active == 'N'){
                    $data->join_date = date('Y-m-d');
                }
            }else{
                $data->nis = Siswa::getLatestNis($request->input('jenjang'), $request->input('tingkat_kelas'));
                $data->created_by = Auth::user()->getUsername();
                $data->join_date = $request->input('join_date');
            }
            if($request->hasFile('avatar')){
                $avatarName = time().'.'.$request->file('avatar')->extension();
                $request->file('avatar')->move(public_path('avatars'), $avatarName);
                $data->avatar = $avatarName;
            }
            $data->email = $request->input('email');
            $data->status_pendaftaran = $request->input('status_pendaftaran');
            $data->jenjang = $request->input('jenjang');
            $data->tingkat_kelas = $request->input('tingkat_kelas');
            $data->fullname = $request->input('fullname');
            $data->tempat_lahir = $request->input('tempat_lahir');
            $data->tanggal_lahir = $request->input('tanggal_lahir');
            $data->nik = $request->input('nik');
            $data->jenis_kelamin = $request->input('jenis_kelamin');
            $data->urutan_anak_ke = $request->input('urutan_anak_ke');
            $data->nisn = $request->input('nisn');
            $data->alamat_tinggal = $request->input('alamat_tinggal');
            $data->kelurahan = $request->input('kelurahan');
            $data->kecamatan = $request->input('kecamatan');
            $data->provinsi = $request->input('provinsi');
            $data->tinggal_bersama = $request->input('tinggal_bersama');
            $data->nama_ayah = $request->input('nama_ayah');
            $data->tempat_lahir_ayah = $request->input('tempat_lahir_ayah');
            $data->tanggal_lahir_ayah = $request->input('tanggal_lahir_ayah');
            $data->pekerjaan_ayah = $request->input('pekerjaan_ayah');
            $data->nama_ibu = $request->input('nama_ibu');
            $data->tempat_lahir_ibu = $request->input('tempat_lahir_ibu');
            $data->tanggal_lahir_ibu = $request->input('tanggal_lahir_ibu');
            $data->pekerjaan_ibu = $request->input('pekerjaan_ibu');
            $data->penghasilan_orangtua = $request->input('penghasilan_orangtua');
            $data->phone = $request->input('phone');
            $data->asal_sekolah = $request->input('asal_sekolah');
            $data->alamat_sekolah_asal = $request->input('alamat_sekolah_asal');
            $data->tinggi_badan = $request->input('tinggi_badan');
            $data->berat_badan = $request->input('berat_badan');
            $data->riwayat_sakit = $request->input('riwayat_sakit');
            $data->bidang_olahraga = $request->input('bidang_olahraga');
            $data->bidang_lainnya = $request->input('bidang_lainnya');
            $data->program_unggulan = $request->input('program_unggulan');
            $data->is_active = $request->input('is_active');
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' siswa succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                if(BiayaSiswa::where('nis', $data->nis)->where('jenjang', $data->jenjang)->count() == 0){
                    $this->saveBiaya($data);
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

    private function saveBiaya($siswa)
    {
        try{
            $included = ['uang_masuk','spp'];
            $query = DB::table('parameter')
                ->whereIn('parameter.param',$included)
                ->where('parameter.param1', $siswa->jenjang);
            if($siswa->tingkat_kelas == '6' || $siswa->tingkat_kelas == '9' || $siswa->tingkat_kelas == '12'){
                $query->where('parameter.param2', $siswa->tingkat_kelas);
            }else{
                $query->whereNull('parameter.param2');
            }
            $param = $query->orderByDesc('param')->get()->toArray();
            if(count($param) > 0){
                $data = new BiayaSiswa;
                $data->nis = $siswa->nis;
                $data->created_by = Auth::user()->getUsername();
                $data->jenjang = $siswa->jenjang;
                // Get th_ajaran from parameter
                $th_ajaran = $this->sharedService->getTahunAjaran();
                $data->th_ajaran = $th_ajaran;
                foreach ($param as $key => $value) {
                    $data->{$value->param} = (int)$value->value;
                }
                if($data->save()){  
                    $msg = Auth::user()->getUsername().' save biaya succesfully : '.json_encode($data);              
                    Log::info($msg);
                    $this->sharedService->logs($msg);
                    return true;
                }
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully save biaya: '.$e->getMessage());       
            return false;
        }
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Siswa::whereIn('id', $id);
            $msg = Auth::user()->getUsername(). ' delete Siswa succesfully : '.json_encode($data->get()->toArray());
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete Siswa: '.$e->getMessage());
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function getDropdown(Request $request)
    {
        $query = Siswa::where('is_active','Y');
        if($request->get('q') != ''){
            $query->where('fullname', 'like', '%'.$request->get('q').'%');
        }
        $data = $query->orderByDesc('created_date')->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getParams(Request $request)
    {
        $data =  new \stdClass();
        $data->jenjang = $this->sharedService->getParamDropdown('jenjang');
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function store(Request $request)
    {
        $file = $request->file('file');

        $import = new SiswaImport;
        $import->import($file);

        $msg = 'Prosess berhasil dilakukan.';
        $status = true;

        if ($import->failures()->isNotEmpty()) {
            $msg = 'Terdapat '.count($import->failures()).' data gagal disimpan';
            $status = false;
        }

        echo json_encode(array('status' => $status, 'message' =>  $msg, 'failure' => $import->failures()));

    }

    public function getBiaya($nis)
    {
        $data = BiayaSiswa::where('nis', $nis)->where('th_ajaran', $this->sharedService->getTahunAjaran())->first();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function updateBiaya(Request $request){
        $nis = $request->input('nis_biaya');
        $data = BiayaSiswa::where('nis', $nis)->where('th_ajaran', $this->sharedService->getTahunAjaran())->first();
        if($data){
            $uang_masuk = str_replace(".","",$request->input('uang_masuk'));
            $um =  str_replace(",",".",$uang_masuk);
            $data->uang_masuk = $uang_masuk;
            $daftar_ulang = str_replace(".","",$request->input('daftar_ulang'));
            $du = str_replace(",",".",$daftar_ulang);
            $data->daftar_ulang = $daftar_ulang;
            $spp = str_replace(".","",$request->input('spp'));
            $spp_formatted = str_replace(",",".",$spp);
            $data->spp = $spp;
            $data->updated_by = Auth::user()->getUsername();
            $um_masuk = str_replace(".","",$request->input('um_masuk'));
            $paid_um =  str_replace(",",".",$um_masuk);
            $du_masuk = str_replace(".","",$request->input('du_masuk'));
            $paid_du =  str_replace(",",".",$du_masuk);
            $data->um_masuk = $paid_um;
            $data->du_masuk = $paid_du;
            if($paid_um === $data->uang_masuk){
                $data->status_um = 1;
            }
            if($paid_du === $data->daftar_ulang){
                $data->status_du = 1;
            }
            if($data->save()){
                echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
            }
        }
        else{
            echo json_encode(array('status' => 404, 'message' => 'Data tidak ditemukan'));
        }
    }

}
