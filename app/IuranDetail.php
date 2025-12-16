<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IuranDetail extends Model
{
    protected $table = "iuran_detail";

    public $primaryKey = "id";

    public $timestamps = false;

    const CREATED_AT = 'created_date';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [    
        'id',
        'iuran_id',
        'month',
        'year',
        'amount',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_date'
    ];

}
