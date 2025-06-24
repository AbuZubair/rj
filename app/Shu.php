<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Shu extends Model
{
    protected $table = "shu";

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
        'year',
        'jumlah',
        'persen',
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
                DB::raw("(select value,param1 from `parameter` where `parameter`.`param` = 'persen_shu') `param`"), 
                'param.param1', '=', 'parameter.value'
            );
    }
}
