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
use App\Iuran;
use App\IuranDetail;
use App\BiayaSiswa;
use App\Siswa;
use App\Http\Requests\IuranRequest;

class IuranController extends Controller
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
        return view('iuran.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Iuran::getData($request);
            return Datatables::of($data)               
                ->make(true);
        }
    }

    public function add()
    {
        $type = 'Add';
        return view('user.form',compact('type'));
    }

    public function edit(Request $req)
    {
        $id = $req->get('id');
        // Join with siswa to get nis
        $data = Iuran::with('siswa')->find($id);
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(IuranRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = new Iuran;
            $amt = str_replace(".","",$request->input('amount'));
            $amount =  str_replace(",",".",$amt);
            if($request->input('id') != ''){
                $data = Iuran::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();
            }else{
                $data->created_by = Auth::user()->getUsername();
            }
            $data->nis = $request->input('nis');
            $data->paid_date = $request->input('paid_date');
            $data->amount = $amount;
            $data->status = 1;
            $data->jenjang = $request->input('jenjang');
            $data->tingkat_kelas = $request->input('tingkat_kelas');
            $data->th_ajaran = $this->sharedService->getTahunAjaran();
            $data->type = $request->input('type');
            $data->is_beasiswa = $request->input('is_beasiswa');
            if($data->save()){  
                $this->processBiayaAndDetial($data);
                // Log the action
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' iuran succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' Iuran: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
            DB::rollBack();
        }
            
    }

    private function processBiayaAndDetial($data){
        // Get reference biaya from biaya_siswa
        $query_biaya = BiayaSiswa::where('nis', $data->nis)->where('jenjang', $data->jenjang)->orderByDesc('th_ajaran');
        if($data->type === 'spp'){
            $th_ajaran = $this->sharedService->getTahunAjaran();
            $biaya = $query_biaya->where('th_ajaran', $th_ajaran)->first();
            if(IuranDetail::where('iuran_id', $data->id)->exists()){
                IuranDetail::where('iuran_id', $data->id)->delete();
            }
            // Get count of detail month by divide amount and biaya_siswa spp
            $count = ceil($data->amount / $biaya->spp);
            $last_spp = null;
            // Get last month and year from iuran detail by nis
            $lastDetail = IuranDetail::where('nis', $data->nis)->orderBy('year', 'desc')->orderBy('month', 'desc')->first();
            $month = 0;
            $year = 0;
            if ($lastDetail) {
                // Set month and year to the next month
                $month = ($lastDetail->month % 12) + 1; // Increment month, reset to 1 if it exceeds 12
                $year = $lastDetail->year + floor($lastDetail->month / 12); // Increment year if month exceeds 12
            }else{
                // Set year to tahun_masuk and month to July
                $siswa = Siswa::where('nis', $data->nis)->where('is_active', 'Y')->first();
                $year = $siswa->tahun_masuk;
                $month = 7; // July
            }
            for($i = 0; $i < $count; $i++){
                $last_spp = $this->addDetail($data, $biaya->spp, $month, $year);
                $month = ($month % 12) + 1; // Increment month, reset to 1 if it exceeds 12
                if($month == 1){
                    $year += 1; // Increment year if month exceeds 12
                }    
            }
            if($last_spp){
                // Update spp_terakhir in siswa
                $siswa = Siswa::where('nis', $data->nis)->where('is_active', 'Y')->first();
                $siswa->spp_terakhir = $last_spp;
                if($siswa->save()){
                    $msg = Auth::user()->getUsername().' update siswa spp_terakhir succesfully : '.json_encode($siswa);              
                    Log::info($msg);
                    $this->sharedService->logs($msg);
                    DB::commit();
                }else{
                    Log::error('Gagal update siswa spp_terakhir');
                    $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully update Iuran: Gagal update siswa spp_terakhir');
                    DB::rollBack();
                    // Throw error
                    throw new Exception('Gagal update siswa spp_terakhir');
                }
            }
        }else{
            switch ($data->type) {
                case 'uang_masuk':
                    $biaya = $query_biaya->where('th_ajaran', $data->th_ajaran)->first();
                    $current_um_masuk = ($biaya->um_masuk != null) ? $biaya->um_masuk : 0;
                    if($biaya->uang_masuk < $data->amount + $current_um_masuk){
                        Log::error('Uang masuk tidak boleh lebih besar dari biaya uang masuk yang sudah ditentukan');
                        $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully update Iuran: Uang masuk tidak boleh lebih besar dari biaya uang masuk yang sudah ditentukan');
                        DB::rollBack();
                        // Throw error
                        throw new Exception('Uang masuk tidak boleh lebih besar dari biaya uang masuk yang sudah ditentukan');
                    }else{
                        $biaya->um_masuk = $data->amount + $current_um_masuk;
                        if($biaya->um_masuk === $biaya->uang_masuk){
                            $biaya->status_um = 1; // Lunas
                        }
                    }
                break;
                default:
                    $biaya = $query_biaya->where('th_ajaran', $data->th_ajaran)->first();
                    $current_du_masuk = ($biaya->du_masuk != null) ? $biaya->du_masuk : 0;
                    if($biaya->daftar_ulang < $data->amount + $current_du_masuk){
                        Log::error('Daftar ulang tidak boleh lebih besar dari biaya daftar ulang yang sudah ditentukan');
                        $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully update Iuran: Daftar ulang tidak boleh lebih besar dari biaya daftar ulang yang sudah ditentukan');
                        DB::rollBack();
                        // Throw error
                        throw new Exception('Daftar ulang tidak boleh lebih besar dari biaya daftar ulang yang sudah ditentukan');
                    }else{
                        $biaya->du_masuk = $data->amount + $current_du_masuk;
                        if($biaya->du_masuk === $biaya->daftar_ulang){
                            $biaya->status_du = 1; // Lunas
                        }
                    }
                    break;
            }
            if($biaya->save()){
                $msg = Auth::user()->getUsername().' update biaya siswa succesfully : '.json_encode($biaya);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                DB::commit();
            }else{
                Log::error('Gagal update biaya siswa');
                $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully update Iuran: Gagal update biaya siswa');
                DB::rollBack();
                // Throw error
                throw new Exception('Gagal update biaya siswa');
            }
        }
    }

    public function addDetail($data, $amount, $month, $year)
    {
        $detail = new IuranDetail;
        $detail->nis = $data->nis;
        $detail->iuran_id = $data->id;
        $detail->created_by = Auth::user()->getUsername();
        $detail->month = $month;
        $detail->year = $year;
        $detail->amount = $amount;
        if($detail->save()){
            $msg = Auth::user()->getUsername().' add iuran detail succesfully : '.json_encode($detail);              
            Log::info($msg);
            $this->sharedService->logs($msg);
            // Return new date by given month and year
            return date('Y-m-d', strtotime("{$detail->year}-{$detail->month}-01"));
        }
        return null;
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Iuran::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete Iuran succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete Iuran: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function getParams(Request $request)
    {
        $data =  new \stdClass();
        $data->type = $this->sharedService->getParamDropdown('type_iuran');
        $data->jenjang = $this->sharedService->getParamDropdown('jenjang');
        $data->th_ajaran = $this->sharedService->getParamDropdown('th_ajaran');
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getDetail($request)
    {
        $data = IuranDetail::where('iuran_id', $request)->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

     public function getTahunAjaran(){
        $data = $this->sharedService->getTahunAjaran();
        echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function printBukti($nis)
    {
        // Load iuran with siswa relation
        $data = Iuran::with('siswa')->where('nis', $nis)->firstOrFail();

        // Receipt number: [TH_AJARAN digits][id padded to 5]
        $thDigits = preg_replace('/\D/', '', $data->th_ajaran);
        // Format th_digits like 20212022 -> 2122
        if (strlen($thDigits) >= 8) {
            $thDigits = substr($thDigits, 2, 2) . substr($thDigits, 6, 2);
        }
        $receiptNo = $thDigits . str_pad($data->id, 5, '0', STR_PAD_LEFT);

        // Fields
        $nis = $data->nis;
        $nama = $data->siswa->fullname ?? ($data->siswa->name ?? ''); // fallback if attribute differs
        $kelas = trim($data->tingkat_kelas . ' ' . $data->jenjang);
        $paidDate = \Carbon\Carbon::parse($data->paid_date)->format('d-M-Y');
        $nominal = number_format($data->amount, 0, ',', '.');

        // Address and logo
        $address = config('app.address');
        $logoPath = public_path('images/RJ.png');
        $logoSrc = file_exists($logoPath) ? $logoPath : '';

        // Terbilang (simple Indonesian number to words)
        $terbilangFunc = function ($n) use (&$terbilangFunc) {
            $n = (int)$n;
            $words = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
            if ($n < 12) return $words[$n];
            if ($n < 20) return $terbilangFunc($n - 10) . ' belas';
            if ($n < 100) return $terbilangFunc(intval($n / 10)) . ' puluh' . ($n % 10 ? ' ' . $terbilangFunc($n % 10) : '');
            if ($n < 200) return 'seratus' . ($n - 100 ? ' ' . $terbilangFunc($n - 100) : '');
            if ($n < 1000) return $terbilangFunc(intval($n / 100)) . ' ratus' . ($n % 100 ? ' ' . $terbilangFunc($n % 100) : '');
            if ($n < 2000) return 'seribu' . ($n - 1000 ? ' ' . $terbilangFunc($n - 1000) : '');
            if ($n < 1000000) return $terbilangFunc(intval($n / 1000)) . ' ribu' . ($n % 1000 ? ' ' . $terbilangFunc($n % 1000) : '');
            if ($n < 1000000000) return $terbilangFunc(intval($n / 1000000)) . ' juta' . ($n % 1000000 ? ' ' . $terbilangFunc($n % 1000000) : '');
            return (string)$n;
        };
        $terbilang = trim(ucfirst($terbilangFunc($data->amount))) . ' Rupiah';

        // Petugas / pencetak
        $petugas = config('app.bendahara', 'Admin');

        // Move receipt number below header (right aligned) and replace flex layout with table for compatibility
        $html = '
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#000; margin:0; padding:0; }
                .wrap { width:100%; padding:12px 10px; box-sizing:border-box; }
                .hdr { width:100%; border-bottom:1px solid #000; padding-bottom:8px; display:table; }
                .hdr .left { display:table-cell; vertical-align:middle; }
                .hdr .center { display:table-cell; text-align:center; vertical-align:middle; }
                .logo { width:50px; }
                .school-name { font-weight:bold; font-size:16px; }
                .school-sub { font-size:11px; color:#222; margin-top:2px; }
                .receipt-title { text-align:center; font-weight:bold; margin:12px 0 8px 0; font-size:14px; }
                table.info { width:100%; border-collapse:collapse; margin-top:6px; }
                table.info td.label { width:130px; font-weight:600; vertical-align:top; padding:2px 6px; }
                table.info td.colon { width:8px; vertical-align:top; padding:2px 0; text-align:center; }
                table.info td.value { vertical-align:top; padding:2px 6px; }
                .amount { font-weight:600; }
                .purpose { margin-top:6px; font-size:12px; }
                .keterangan { margin-top:12px; border:1px solid #000; padding:8px; width:100%; font-size:11px; box-sizing:border-box; }
                .right-sign { text-align:center; width:200px; }
                .keterangan ul { margin:4px 0 0 16px; padding:0; }
                .small { font-size:11px; color:#333; }
            </style>
        </head>
        <body>
            <div class="wrap">
                <div class="hdr">
                    <div class="left">
                        <img src="' . htmlspecialchars($logoSrc) . '" class="logo" />
                    </div>
                    <div class="center">
                        <div class="school-name">SEKOLAH ISLAM RIYADHUL JANNAH</div>
                        <div class="school-sub">' . htmlspecialchars($address) . '</div>
                    </div>
                </div>

                <!-- Receipt number moved below header, aligned right -->
                <div style="width:100%; text-align:right; margin-top:8px; font-size:12px;">
                    No. Kuitansi<br/>
                    <strong>' . htmlspecialchars($receiptNo) . '</strong>
                </div>                

                <div class="receipt-title">TANDA BUKTI PEMBAYARAN</div>

                <p style="margin-top:10px;">Telah terima dari:</p>

                <table class="info">
                    <tr>
                        <td class="label">N I S</td>
                        <td class="colon">:</td>
                        <td class="value">' . htmlspecialchars($nis) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Nama</td>
                        <td class="colon">:</td>
                        <td class="value">' . htmlspecialchars($nama) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Kelas</td>
                        <td class="colon">:</td>
                        <td class="value">' . htmlspecialchars($kelas) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal</td>
                        <td class="colon">:</td>
                        <td class="value">' . htmlspecialchars($paidDate) . '</td>
                    </tr>
                </table>

                <p>Uang sejumlah <span class="amount">Rp ' . htmlspecialchars($nominal) . ' (' . htmlspecialchars($terbilang) . ')</span> untuk Pembayaran: ' . htmlspecialchars($data->type . ($data->type === "spp" ? ' Bulanan' : '')) . ' siswa ' . htmlspecialchars($nama) . ' (' . htmlspecialchars($nis) . ')</p>

                <div class="keterangan" style="width: auto; display: inline-block;">
                    <strong>Keterangan:</strong>
                    <ul class="small">
                        <li>Tgl cetak: ' . date('d/m/Y H:i:s') . '</li>
                        <li>Petugas: ' . htmlspecialchars($petugas) . '</li>
                    </ul>
                </div>
                <div class="right-sign" style="text-align: center; display: inline-block; float: right;">
                    Yang menerima<br/><br/><br/>
                    ( ' . htmlspecialchars($petugas) . ' )
                </div>
            </div>
        </body>
        </html>
        ';
        
        // Generate PDF and stream
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a5', 'portrait');
        return $pdf->download('bukti_' . $receiptNo . '.pdf');
    }
}
