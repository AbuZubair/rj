<?php

namespace App\Imports;

use App\Staff;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class StaffImport implements
    ToModel,
    WithHeadingRow,
    SkipsOnError,
    WithValidation,
    SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    public function model(array $row)
    {
        $jabatan = strtolower(str_replace(' ', '_', $row['jabatan']));
        $jenis_ptk = strtolower(str_replace(' ', '_', $row['jenis_ptk']));
        $unit_mengajar = strtolower(str_replace(' ', '_', $row['unit_mengajar']));
        if($jabatan != "" && DB::table('parameter')->where('param','jabatan')->where('value',$jabatan)->doesntExist()){
            DB::table('parameter')->insert([
                'param' => 'jabatan',
                'value' => $jabatan,
                'label' => $jabatan
            ]);
        }
        if($jenis_ptk != "" && DB::table('parameter')->where('param','jenis_ptk')->where('value',$jenis_ptk)->doesntExist()){
            DB::table('parameter')->insert([
                'param' => 'jenis_ptk',
                'value' => $jenis_ptk,
                'label' => $jenis_ptk
            ]);
        }
        if($unit_mengajar != "" && DB::table('parameter')->where('param','unit_mengajar')->where('value',$unit_mengajar)->doesntExist()){
            DB::table('parameter')->insert([
                'param' => 'unit_mengajar',
                'value' => $unit_mengajar,
                'label' => $unit_mengajar
            ]);
        }
        print_r($row);
        return new Staff([
            'nip' => $row['nip'],
            'fullname' => $row['fullname'],
            'jk' => $row['jk'],
            'tempat_lahir' => $row['tempat_lahir'],
            'tanggal_lahir' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir']),
            'pendidikan_terakhir' => $row['pendidikan_terakhir'],
            'instansi_terakhir' => $row['instansi_terakhir'],
            'jurusan' => $row['jurusan'],
            'join_date' => $row['join_date'],
            'jabatan' => $jabatan,
            'jenis_ptk' => $jenis_ptk,
            'unit_mengajar' => $unit_mengajar,
            'agama' => $row['agama'],
            'alamat' => $row['alamat'],
            'kelurahan' => $row['kelurahan'],
            'kecamatan' => $row['kecamatan'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'sk_pengangkatan' => $row['sk_pengangkatan'],
            'tmt_pengangkatan' => $row['tmt_pengangkatan'],
            'lembaga_pengangkatan' => $row['lembaga_pengangkatan'],
            'nama_ibu_kandung' => $row['nama_ibu_kandung'],
            'status_perkawinan' => $row['status_perkawinan'],
            'pekerjaan_pasangan' => $row['pekerjaan_pasangan'],
            'keahlian' => $row['keahlian'],
            'npwp' => $row['npwp'],
            'nama_wajib_pajak' => $row['nama_wajib_pajak'],
            'kewarganegaraan' => $row['kewarganegaraan'],
            'bank' => $row['bank'],
            'no_rek' => $row['no_rekening_bank'],
            'an_rek' => $row['atas_nama_rekening'],
            'nik' => $row['nik'],
            'is_active' => $row['is_active']
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nip' => ['required','unique:staff,nip']
        ];
    }

    public static function afterImport(AfterImport $event)
    {
    }

}