<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pengurus extends Model
{
    protected $table = "shu_pengurus";

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
        'pengurus',
        'persen',
        'jumlah',
        'year',
        'created_date',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_date', 'updated_date'
    ];

    public static function mainQuery()
    {
        return DB::table('parameter')
            ->leftJoin(
                DB::raw("(select value,param1 from `parameter` where `parameter`.`param` = 'persen_pengurus') `param`"), 
                'param.param1', '=', 'parameter.value'
            )
            ->leftJoin(
                DB::raw("(select fullname,no_anggota,pengurus from `anggota` where is_pengurus = 'Y') `anggota`"), 
                'parameter.value', '=', 'anggota.pengurus'
            );
    }
}
