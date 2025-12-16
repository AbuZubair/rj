<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Library\Services\Shared;

class Siswa extends Model
{
    protected $table = "siswa";

    public $primaryKey = "id";

    const CREATED_AT = 'created_date';

    const UPDATED_AT = 'updated_date';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nis',
        'avatar',
        'email',
        'status_pendaftaran',
        'jenjang',
        'tingkat_kelas',
        'fullname',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'jenis_kelamin',
        'urutan_anak_ke',
        'nisn',
        'alamat_tinggal',
        'kelurahan',
        'kecamatan',
        'provinsi',
        'tinggal_bersama',
        'nama_ayah',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'tempat_lahir_ibu',
        'tanggal_lahir_ibu',
        'pekerjaan_ibu',
        'penghasilan_orangtua',
        'phone',
        'asal_sekolah',
        'alamat_sekolah_asal',
        'tinggi_badan',
        'berat_badan',
        'riwayat_sakit',
        'bidang_olahraga',
        'bidang_lainnya',
        'program_unggulan',
        'join_date',
        'relieve_date',
        'tabungan',
        'is_tabungan_monthly',
        'tabungan_date',
        'spp_terakhir',
        'is_graduated',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_date', 'updated_date'
    ];

    public static function getData($request)
    {
        $query =  DB::table('siswa')
            ->leftJoin('parameter as a', 'a.value', '=', 'siswa.jenjang') 
            ->select('siswa.*', 'a.label as jenjang_text');        
        if($request->get('searchJenjang')!='')$query->where('siswa.jenjang',$request->get('searchJenjang'));
        if($request->get('searchTingkat')!='')$query->where('siswa.tingkat_kelas',$request->get('searchTingkat'));
        if($request->get('searchStatus')!='')$query->where('siswa.is_active',$request->get('searchStatus'));
        $data = $query->orderByDesc('created_date')->get();
        return $data;
    }

    public static function getLatestNis($jenjang, $tingkat_kelas){
        // Jenjang code mapping
        $jenjangCodes = [
            'paud' => '01',
            'sd' => '02',
            'smp' => '03',
            'sma' => '04'
        ];

        // You might need to pass $jenjang, $sharedService, $tingkat_kelas to this function in real usage.
        // For now, assume they are available in the scope or passed as parameters.

        // Example usage:
        // public static function getLatestNis($jenjang, $tingkat_kelas, $sharedService)

        // Get jenjang code
        $jenjangCode = isset($jenjangCodes[$jenjang]) ? $jenjangCodes[$jenjang] : '00';

        // Get tahun ajaran and extract tahun masuk
        $th_ajaran = Shared::getTahunAjaranStatic(); // e.g. "2025/2026"
        preg_match('/(\d{4})\/(\d{4})/', $th_ajaran, $matches);
        $tahunMasuk = isset($matches[1], $matches[2]) ? substr($matches[1], 2, 2) . substr($matches[2], 2, 2) : '0000';

        // Get angkatan ke
        $lastAngkatan = Shared::getLastAngkatan($jenjang);
        $angkatanKe = str_pad($lastAngkatan + 1, 2, '0', STR_PAD_LEFT);

        // Find latest order number for this jenjang, tahun masuk, angkatan, tingkat
        $nisPrefix = $jenjangCode . $tahunMasuk . $angkatanKe;

        $latestNis = DB::table('siswa')
            ->where('jenjang', $jenjang)
            ->where('tingkat_kelas', $tingkat_kelas)
            ->where('nis', 'like', $nisPrefix . '%')
            ->orderByDesc('nis')
            ->value('nis');

        if ($latestNis) {
            $orderNumber = intval(substr($latestNis, -3)) + 1;
        } else {
            $orderNumber = 1;
        }

        $orderNumberStr = str_pad($orderNumber, 3, '0', STR_PAD_LEFT);

        return $nisPrefix . $orderNumberStr;
    }
}
