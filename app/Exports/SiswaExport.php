<?php

namespace App\Exports;

use App\Siswa;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SiswaExport implements
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    FromQuery
{
    use Exportable;

    private $year;


    public function __construct(int $year)
    {
        $this->year = $year;

    }

    public function query()
    {
        return  Siswa::select(
            'nis',
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
            'tahun_masuk',
            'is_active'
        );
    }

    public function map($item): array
    {
        return [
            $item->nis,
            $item->tahun_masuk,
            $item->email,
            $item->status_pendaftaran,
            $item->jenjang,
            $item->tingkat_kelas,
            $item->fullname,
            $item->tempat_lahir,
            $item->tanggal_lahir,
            $item->nik,
            $item->jenis_kelamin,
            $item->urutan_anak_ke,
            $item->nisn,
            $item->alamat_tinggal,
            $item->kelurahan,
            $item->kecamatan,
            $item->provinsi,
            $item->tinggal_bersama,
            $item->nama_ayah,
            $item->tempat_lahir_ayah,
            $item->tanggal_lahir_ayah,
            $item->pekerjaan_ayah,
            $item->nama_ibu,
            $item->tempat_lahir_ibu,
            $item->tanggal_lahir_ibu,
            $item->pekerjaan_ibu,
            $item->penghasilan_orangtua,
            $item->phone,
            $item->asal_sekolah,
            $item->alamat_sekolah_asal,
            $item->tinggi_badan,
            $item->berat_badan,
            $item->riwayat_sakit,
            $item->bidang_olahraga,
            $item->bidang_lainnya,
            $item->program_unggulan,            
            $item->is_active
        ];
    }

    public function headings(): array
    {
        return [
            'nis',
            'tahun_masuk',
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
            "is_active",
        ];
    }
}