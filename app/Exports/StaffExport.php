<?php

namespace App\Exports;

use App\Staff;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StaffExport implements
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
        return  Staff::select(
            'nip',
            'fullname',
            'jk',
            'tempat_lahir',
            'tanggal_lahir',
            'pendidikan_terakhir',
            'instansi_terakhir',
            'jurusan',
            'join_date',
            'jabatan',
            'jenis_ptk',
            'unit_mengajar',
            'agama',
            'alamat',
            'kelurahan',
            'kecamatan',
            'phone',
            'email',
            'sk_pengangkatan',
            'tmt_pengangkatan',
            'lembaga_pengangkatan',
            'nama_ibu_kandung',
            'status_perkawinan',
            'pekerjaan_pasangan',
            'keahlian',
            'npwp',
            'nama_wajib_pajak',
            'kewarganegaraan',
            'bank',
            'no_rek',
            'an_rek',
            'nik',
            'is_active'
        );
    }

    public function map($item): array
    {
        return [
            $item->nip,
            $item->fullname,
            $item->jk,
            $item->tempat_lahir,
            $item->tanggal_lahir,
            $item->pendidikan_terakhir,
            $item->instansi_terakhir,
            $item->jurusan,
            $item->join_date,
            $item->jabatan,
            $item->jenis_ptk,
            $item->unit_mengajar,
            $item->agama,
            $item->alamat,
            $item->kelurahan,
            $item->kecamatan,
            $item->phone,
            $item->email,
            $item->sk_pengangkatan,
            $item->tmt_pengangkatan,
            $item->lembaga_pengangkatan,
            $item->nama_ibu_kandung,
            $item->status_perkawinan,
            $item->pekerjaan_pasangan,
            $item->keahlian,
            $item->npwp,
            $item->nama_wajib_pajak,
            $item->kewarganegaraan,
            $item->bank,
            $item->no_rek,
            $item->an_rek,
            $item->nik,
            $item->is_active
        ];
    }

    public function headings(): array
    {
        return [
            "nip", "fullname", "jk", "tempat_lahir", "tanggal_lahir", "pendidikan_terakhir", "instansi_terakhir", "jurusan", "join_date", "jabatan", "jenis_ptk", "unit_mengajar", "agama", "alamat", "kelurahan", "kecamatan", "phone", "email", "sk_pengangkatan", "tmt_pengangkatan", "lembaga_pengangkatan", "nama_ibu_kandung", "status_perkawinan", "pekerjaan_pasangan", "keahlian", "npwp", "nama_wajib_pajak", "kewarganegaraan", "bank", "no_rekening_bank", "atas_nama_rekening", "nik", "is_active"
        ];
    }
}