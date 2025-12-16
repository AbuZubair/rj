<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Staff extends Model
{
    protected $table = "staff";

    public $primaryKey = "id";

    const CREATED_AT = 'created_date';

    const UPDATED_AT = 'updated_date';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nip',
        'avatar',
        'jk',
        'fullname',
        'tempat_lahir',
        'tanggal_lahir',
        'pendidikan_terakhir',
        'instansi_terakhir',
        'jurusan',
        'jabatan',
        'jenis_ptk',
        'unit_mengajar',
        'agama',
        'alamat',
        'kelurahan',
        'kecamatan',
        'email',
        'phone',
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
        'join_date',
        'relieve_date',
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
        $query =  DB::table('staff')
            ->leftJoin('parameter as a', 'a.value', '=', 'staff.jabatan') 
            ->leftJoin('parameter as b', 'b.value', '=', 'staff.jenis_ptk')
            ->leftJoin('parameter as c', 'c.value', '=', 'staff.unit_mengajar')
            ->select('staff.*', 'a.label as jabatan_text', 'b.label as jenis_ptk_text', 'c.label as unit_mengajar_text');
        if($request->get('searchStatus')!='')$query->where('staff.is_active',$request->get('searchStatus'));
        $data = $query->orderByDesc('created_date')->get();
        return $data;
    }
}
