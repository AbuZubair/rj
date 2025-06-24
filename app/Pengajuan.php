<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Pengajuan extends Model
{
    protected $table = "pengajuan";

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
        'type',
        'no_anggota',
        'amount',
        'margin',
        'angsuran',
        'nilai_total',
        'nilai_awal',
        'status',
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

    public function getQuery()
    {
        return DB::table('pengajuan')
            ->leftJoin('anggota', 'anggota.no_anggota', '=', 'pengajuan.no_anggota')
            ->select('pengajuan.*', 'anggota.fullname')
            ->orderBy('id','desc');
    }

    public static function _getQuery()
    {
        $class = new Pengajuan();
        $query = $class->getQuery();
        return $query;
    }

    public static function getData($request)
    {
        $class = new Pengajuan();
        $query = $class->getQuery();
        if(in_array(Auth::user()->getRole(), [1]))$query->where('pengajuan.no_anggota',Auth::user()->getNoAnggota());
        $data = $query->get();
        return $data;
    }
}
