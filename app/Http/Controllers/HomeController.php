<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Yajra\Datatables\Datatables;
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
    
    public function ajaranBaru()
    {
        try {
            DB::beginTransaction();

            // 1) Hitung dan update tahun ajaran baru
            $currentTh = DB::table('parameter')->where('param', 'th_ajaran')->value('value');
            $nextTh = $currentTh;
            if ($currentTh && strpos($currentTh, '/') !== false) {
                [$y1, $y2] = explode('/', $currentTh);
                $nextTh = ((int)$y1 + 1) . '/' . ((int)$y2 + 1);
            } elseif (is_numeric($currentTh)) {
                $nextTh = (string)(((int)$currentTh) + 1);
            }
            DB::table('parameter')->where('param', 'th_ajaran')->update(['value' => $nextTh]);

            // Helper mapping untuk promosi yang mengubah jenjang
            $specialPromotions = [
                'paud|tk-b' => ['jenjang' => 'sd',  'tingkat_kelas' => '1'],
                'sd|6'      => ['jenjang' => 'smp', 'tingkat_kelas' => '1'],
                'smp|9'     => ['jenjang' => 'sma', 'tingkat_kelas' => '1'],
            ];

            // 2) Proses kenaikan / non-aktif siswa
            $students = \App\Siswa::where('is_active', 'Y')->get();
            
            foreach ($students as $siswa) {
                $jenjang = strtolower($siswa->jenjang);
                $kelas = strtolower((string)$siswa->tingkat_kelas);

                // SMA kelas 12 -> non-aktif
                if ($jenjang === 'sma' && $kelas === '12') {
                    $siswa->is_active = 'N';
                    $siswa->save();
                    continue;
                }

                // Kasus khusus: buat data baru untuk jenjang berikutnya dan non-aktifkan yg lama
                $key = $jenjang . '|' . $kelas;
                if (isset($specialPromotions[$key])) {
                    $target = $specialPromotions[$key];
                    $new = $siswa->replicate();
                    unset($new->id); // agar auto-increment bekerja
                    $new->jenjang = $target['jenjang'];
                    $new->tingkat_kelas = $target['tingkat_kelas'];
                    $new->is_active = 'Y';
                    $new->save();

                    $siswa->is_active = 'N';
                    $siswa->save();
                    continue;
                }

                // Default: naikkan tingkat_kelas satu tingkat
                if (ctype_digit($kelas)) {
                    $siswa->tingkat_kelas = (string)(((int)$kelas) + 1);
                    $siswa->save();
                } elseif (stripos($kelas, 'tk-a') !== false) {
                    // contoh tk-a -> tk-b
                    $siswa->tingkat_kelas = str_ireplace('tk-a', 'tk-b', $kelas);
                    $siswa->save();
                } else {
                    // jika format tidak dikenali, biarkan apa adanya (menghindari modifikasi salah)
                }
            }

            // 3) Buat data biaya untuk semua siswa dengan th_ajaran baru
            $activeStudents = \App\Siswa::where('is_active', 'Y')->get();
            foreach ($activeStudents as $siswa) {
                $jenjang = strtolower($siswa->jenjang);
                $kelas = strtolower((string)$siswa->tingkat_kelas);

                // helper untuk ambil nilai parameter; untuk kelas 6/9/12 gunakan param2
                $getParamValue = function ($paramName) use ($jenjang, $kelas) {
                    $q = DB::table('parameter')->where('param', $paramName)->where('param1', $jenjang);
                    if (in_array($kelas, ['6', '9', '12'])) {
                        $q->where('param2', $kelas);
                    }
                    return $q->value('value') ?? $q->value('param_value') ?? 0;
                };

                $uang_masuk = $getParamValue('uang_masuk');
                $spp = $getParamValue('spp');
                $daftar_ulang = $getParamValue('daftar_ulang');
                $status_um = 0;

                // Untuk siswa selain kelas 1, 7, dan 10: ambil um_masuk dan status_um dari th_ajaran sebelumnya
                if (!in_array($kelas, ['1', '7', '10'])) {
                    $previousData = \App\BiayaSiswa::where('nis', $siswa->nis)
                        ->where('th_ajaran', $currentTh)
                        ->first();
                    
                    if ($previousData) {
                        $um_masuk = $previousData->um_masuk;
                        $status_um = $previousData->status_um ?? $status_um;
                        $spp = $previousData->spp; // gunakan spp sebelumnya
                        $uang_masuk = $previousData->uang_masuk; // gunakan uang_masuk sebelumnya
                    }
                }

                // Simpan ke model BiayaSiswa (pastikan model fillable sesuai)
                \App\BiayaSiswa::create([
                    'nis'           => $siswa->nis,
                    'jenjang'       => $siswa->jenjang,
                    'th_ajaran'     => $nextTh,
                    'um_masuk'      => $um_masuk ?? 0,
                    'status_um'     => $status_um,
                    'uang_masuk'    => $uang_masuk,
                    'spp'           => $spp,
                    'daftar_ulang'  => $daftar_ulang,
                ]);
            }

            // 4) Naikkan angkatan terakhir untuk tiap jenjang
            $angkatanRows = DB::table('parameter')->where('param', 'angkatan_terakhir')->get();
            foreach ($angkatanRows as $row) {
                $current = (int)($row->value ?? $row->param_value ?? 0);
                DB::table('parameter')->where('id', $row->id)->update(['value' => $current + 1, 'label' => (string)($current + 1)]);
            }

            DB::commit();
            echo json_encode(array('status' => true, 'message' => 'Proses kenaikan th_ajaran, promosi siswa, dan pembuatan biaya berhasil.'));
        } catch (\Exception $e) {
            DB::rollBack();
            echo json_encode(array('status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()));
        }
    }

}
