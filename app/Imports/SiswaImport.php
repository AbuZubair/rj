<?php

namespace App\Imports;

use App\Siswa;
use App\BiayaSiswa;
use App\Library\Services\Shared;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

class SiswaImport implements
    ToModel,
    WithHeadingRow,
    SkipsOnError,
    WithValidation,
    WithMultipleSheets,
    SkipsOnFailure,
    SkipsEmptyRows
{
    use Importable, SkipsErrors, SkipsFailures;

    public function model(array $row)
    {
        $jenjang = strtolower(str_replace(' ', '_', $row['jenjang']));

        if($jenjang != "" && DB::table('parameter')->where('param','jenjang')->where('value',$jenjang)->doesntExist()){
            DB::table('parameter')->insert([
                'param' => 'jenjang',
                'value' => $jenjang,
                'label' => $jenjang
            ]);
        }

        // If $row['nis'] is empty, generate it from Siswa::getLatestNis
        if (empty($row['nis']) || is_null($row['nis'])) {
            $row['nis'] = Siswa::getLatestNis($jenjang, $row['tingkat_kelas']);
        }

        if(BiayaSiswa::where('nis', $row['nis'])->where('jenjang', $jenjang)->count() == 0){
            $included = ['uang_masuk','spp'];
            $query = DB::table('parameter')
                ->whereIn('parameter.param',$included)
                ->where('parameter.param1', $jenjang);
            if($row['tingkat_kelas'] == '6' || $row['tingkat_kelas'] == '9' || $row['tingkat_kelas'] == '12'){
                $query->where('parameter.param2', $row['tingkat_kelas']);
            }else{
                $query->whereNull('parameter.param2');
            }
            $param = $query->orderByDesc('param')->get()->toArray();
            if(count($param) > 0){
                $data = new BiayaSiswa;
                $data->nis = $row['nis'];
                $data->jenjang = $jenjang;
                $data->created_by = auth()->user()->getUsername();
                // Get th_ajaran from parameter
                $th_ajaran = Shared::getTahunAjaranStatic();
                $data->th_ajaran = $th_ajaran;
                foreach ($param as $key => $value) {
                    $data->{$value->param} = (int)$value->value;
                }
                $data->save();
            }
        }

        // Check if tanggal_lahir memakai format dibawah ini: 
        // - 1 Juli 2020
        // - 20-11-2017
        // - 2017-11-20
        // If so, convert to DateTime object
        $tanggal_lahir = $row['tanggal_lahir'];
        if(is_numeric($tanggal_lahir)) {
            $tanggal_lahir = (new Shared())->formatExcelDate($tanggal_lahir);
        }

        $tanggal_lahir_ayah = $row['tanggal_lahir_ayah'];
        if (is_numeric($tanggal_lahir_ayah)) {
            $tanggal_lahir_ayah = (new Shared())->formatExcelDate($tanggal_lahir_ayah);
        }

        $tanggal_lahir_ibu = $row['tanggal_lahir_ibu'];
        if (is_numeric($tanggal_lahir_ibu)) {
            $tanggal_lahir_ibu = (new Shared())->formatExcelDate($tanggal_lahir_ibu);
        }

        if (isset($row['nik'])) {
            $row['nik'] = (string) $row['nik'];
        }

        return new Siswa([
            'nis' => $row['nis'],
            'tahun_masuk' => $row['tahun_masuk'],
            'email' => $row['email'],
            'status_pendaftaran' => $row['status_pendaftaran'],
            'jenjang' => $jenjang,
            'tingkat_kelas' => $row['tingkat_kelas'],
            'fullname' => $row['fullname'],
            'tempat_lahir' => $row['tempat_lahir'],
            'tanggal_lahir' => $tanggal_lahir,
            'nik' => $row['nik'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'urutan_anak_ke' => $row['urutan_anak_ke'],
            'nisn' => $row['nisn'],
            'alamat_tinggal' => $row['alamat_tinggal'],
            'kelurahan' => $row['kelurahan'],
            'kecamatan' => $row['kecamatan'],
            'provinsi' => $row['provinsi'],
            'tinggal_bersama' => $row['tinggal_bersama'],
            'nama_ayah' => $row['nama_ayah'],
            'tempat_lahir_ayah' => $row['tempat_lahir_ayah'],
            'tanggal_lahir_ayah' => $tanggal_lahir_ayah,
            'pekerjaan_ayah' => $row['pekerjaan_ayah'],
            'nama_ibu' => $row['nama_ibu'],
            'tempat_lahir_ibu' => $row['tempat_lahir_ibu'],
            'tanggal_lahir_ibu' => $tanggal_lahir_ibu,
            'pekerjaan_ibu' => $row['pekerjaan_ibu'],
            'penghasilan_orangtua' => $row['penghasilan_orangtua'],
            'phone' => $row['phone'],
            'asal_sekolah' => $row['asal_sekolah'],
            'alamat_sekolah_asal' => $row['alamat_sekolah_asal'],
            'tinggi_badan' => $row['tinggi_badan'],
            'berat_badan' => $row['berat_badan'],
            'riwayat_sakit' => $row['riwayat_sakit'],
            'bidang_olahraga' => $row['bidang_olahraga'],
            'bidang_lainnya' => $row['bidang_lainnya'],
            'program_unggulan' => $row['program_unggulan'],
            'spp_terakhir' => $row['spp_terakhir'] ?? null,
            'is_active' => $row['is_active'] ?? 'Y'
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nis' => ['nullable','unique:siswa,nis'],
            '*.tahun_masuk' => ['required'],
            '*.nisn' => ['nullable','unique:siswa,nisn']
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nis.required' => 'NIS is required.',
            '*.nis.unique' => 'NIS tidak boleh sama.',
            '*.nisn.unique' => 'NISN tidak boleh sama.'
        ];
    }

    public static function afterImport(AfterImport $event)
    {
    }

    public function sheets(): array
    {
        return [
            // Link the first worksheet (index 0) to this same import class
            0 => $this,
        ];
    }

}