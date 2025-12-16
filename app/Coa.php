<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $table = "coa";

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
        'coa_code' ,
        'coa_name' ,
        'coa_level',
        'coa_parent' ,
        'begining_balance',
        'ending_balance',
        'year',
        'rumus_ending_balance',
        'created_date',
        'created_by' ,
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

    public static function generateOrder(){
        $max = Coa::max('coa_level');
        $order = "SUBSTRING(coa.coa_code, 1, 1),";
        for ($i=2; $i <= $max; $i++) {
            $pos = (($i - 1) * 2) + 1;
            $order .= " CAST(SUBSTRING(coa.coa_code, ".$pos.") AS UNSIGNED),";
        }
        $order .= " coa.coa_level";
        return $order;
    }
}
