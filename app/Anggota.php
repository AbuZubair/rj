<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Anggota extends Model
{
    protected $table = "anggota";

    public $primaryKey = "id";

    const CREATED_AT = 'created_date';

    const UPDATED_AT = 'updated_date';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [    
        'id',
        'no_anggota',
        'fullname',
        'grade',
        'department',
        'divisi',
        'join_date',
        'relieve_date',
        'limit_kredit',
        'is_active',
        'tabungan',
        'thr',
        'is_tabungan_monthly',
        'is_thr_monthly',
        'thr_date',
        'shu_iuran',
        'shu_murabahah',
        'created_date',
        'created_by',
        'updated_date',
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
        $query =  DB::table('anggota')
            ->leftJoin('parameter as a', 'a.value', '=', 'anggota.department') 
            ->leftJoin('parameter as b', 'b.value', '=', 'anggota.divisi')
            ->leftJoin('parameter as c', 'c.value', '=', 'anggota.grade')
            ->select('anggota.*', 'a.label as department_text', 'b.label as divisi_text', 'c.label as grade_text');
        if($request->get('searchStatus')!='')$query->where('anggota.is_active',$request->get('searchStatus'));
        $data = $query->orderByDesc('created_date')->get();
        return $data;
    }

    public static function getLatestNoAnggota()
    {
        $query = DB::table('anggota')
            ->select(DB::raw('MAX(CAST(SUBSTRING(no_anggota,2) as INT)) as max'));
                    
        return $query->get()->first();
    }
}
