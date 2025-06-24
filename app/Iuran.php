<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Iuran extends Model
{
    protected $table = "iuran";

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
        'month',
        'year',
        'type',
        'amount',
        'status',
        'paid_date',
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
        return DB::table('iuran')
            ->leftJoin(
                DB::raw("(select * from `parameter` where `parameter`.`param` = 'type_iuran') `param`"), 
                'param.value', '=', 'iuran.type'
            )
            ->leftJoin('anggota', 'anggota.no_anggota', '=', 'iuran.no_anggota')
            ->select(DB::raw('iuran.*, param.label as type_iuran, anggota.fullname, CASE WHEN iuran.status = 1 THEN "Sudah Dibayarkan" ELSE "Belum Dibayar" END AS status_iuran'));
    }

    public static function getData($request)
    {
        $class = new Iuran();
        $query = $class->getQuery();
        if($request->get('month')!='')$query->where('iuran.month',$request->get('month'));
        if($request->get('year')!='')$query->where('iuran.year',$request->get('year'));
        if($request->get('type')!='')$query->where('iuran.type',$request->get('type'));
        if(in_array(Auth::user()->getRole(), [1]))$query->where('iuran.no_anggota',Auth::user()->getNoAnggota());
        $data = $query->orderByDesc('year')->orderByDesc('month')->get();
        return $data;
    }

    public static function getList($request)
    {
        $class = new Iuran();
        $query = $class->getQuery();
        if($request->get('type'))$query->whereIn('type', explode(',',$request->get('type')));
        $data = $query->orderByDesc('created_date')->get();
        return $data;
    }

    public static function getTotalIuran(){
        $query = DB::table('iuran')
                ->leftJoin(
                    DB::raw("(select * from `parameter` where `parameter`.`param` = 'type_iuran') `param`"), 
                    'param.value', '=', 'iuran.type'
                )
                ->leftJoin(
                    DB::raw("(select * from `parameter` where `parameter`.`param` = 'type_iuran') `ref`"), 
                    'ref.value', '=', 'iuran.reference'
                )
                ->leftJoin('anggota', 'anggota.no_anggota', '=', 'iuran.no_anggota')
                ->leftJoin(
                    DB::raw("(select MAX(year) as year, no_anggota, type from `iuran` GROUP BY no_anggota, type) `b`"), function($join)
                    {
                        $join->on('b.no_anggota', '=', 'iuran.no_anggota');
                        $join->on('b.type', '=', 'iuran.type');
                    }
                )
                ->leftJoin(
                    DB::raw("(select MAX(year) as year, MAX(MONTH) AS max_month, no_anggota, type from `iuran` GROUP BY no_anggota, type, year) `c`"), function($join)
                    {
                        $join->on('c.no_anggota', '=', 'iuran.no_anggota');
                        $join->on('c.type', '=', 'iuran.type');
                        $join->on('c.year', '=', 'b.year');
                    }
                )
                ->leftJoin(
                    DB::raw("(select no_anggota, reference, SUM(amount) as return_total from `iuran` where type = 2 AND year <= iuran.year GROUP BY no_anggota, reference) `return_val`"), function($join)
                    {
                        $join->on('return_val.no_anggota', '=', 'iuran.no_anggota');
                        $join->on('return_val.reference', '=', 'iuran.type');
                    }
                );
        return $query;
    }
}
