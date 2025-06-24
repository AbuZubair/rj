<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Murabahah extends Model
{
    protected $table = "murabahah";

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
        'no_murabahah',
        'type',
        'date',
        'date_trans',
        'no_anggota',
        'amount',
        'margin',
        'angsuran',
        'nilai_total',
        'nilai_awal',
        'nilai_pembayaran',
        'nilai_transport',
        'deduction',
        'status',
        'desc',
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
        return DB::table('murabahah')
            ->leftJoin('anggota', 'anggota.no_anggota', '=', 'murabahah.no_anggota')
            ->select('murabahah.*', 'anggota.no_anggota', 'anggota.fullname')
            ->orderBy('murabahah.id','desc');
    }

    public static function _getQuery()
    {
        $class = new Murabahah();
        $query = $class->getQuery();
        return $query;
    }

    public static function getData($request)
    {
        $class = new Murabahah();
        $query = $class->getQuery();
        $data = $query->get();
        return $data;
    }

    public static function checkAndUpdate()
    {
        $check1 = DB::table('murabahah')
            ->leftJoin(
                DB::raw("(SELECT no_murabahah, SUM(amount) as total from `transaction` where trans_type = 'murabahah' AND coa_code = 'A.1.2' AND dk = 'kredit' GROUP BY no_murabahah) `a`"), 
                'a.no_murabahah', '=', 'murabahah.no_murabahah'
            )
            ->select('murabahah.*', 'a.total')
            ->where('murabahah.type',0)
            ->where(function ($query) {
                $query->whereRaw('a.total < murabahah.nilai_pembayaran')
                      ->orWhere('murabahah.deduction', '!=', DB::raw('murabahah.margin - ROUND((murabahah.nilai_total -  a.total) / murabahah.angsuran, 0)'));
            })
            ->update([ 'murabahah.nilai_pembayaran' => DB::raw('a.total'), 'murabahah.status' => DB::raw('CASE WHEN (a.total = murabahah.nilai_total) THEN 2 ELSE 1 END'), 'murabahah.deduction' => DB::raw('murabahah.margin - ROUND((murabahah.nilai_total -  a.total) / murabahah.angsuran, 0)') ]);

        $check_selesai =  DB::table('murabahah')->whereRaw('murabahah.nilai_total = murabahah.nilai_pembayaran')
            ->update([ 'murabahah.status' => 2 ]);
        return true;
    }
}
