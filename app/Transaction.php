<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Library\Services\Shared;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    protected $table = "transaction";

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
        'trans_year',
        'trans_month',
        'trans_date',
        'no_anggota',
        'amount',
        'trans_type',
        'dk',
        'coa_code',
        'tans_desc',
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
        return DB::table('transaction')
            ->leftJoin('coa', 'coa.coa_code', '=', 'transaction.coa_code')                     
            ->select(DB::raw('transaction.*,CONCAT("TRN",LPAD(transaction.id, 8, "0")) as trans_no,coa.coa_name'));
    }

    public static function getData($request)
    {
        $class = new Transaction();
        $query = $class->getQuery();
        if($request->input('coa_code')!='')$query = $query->where('transaction.coa_code',$request->input('coa_code'));
        if($request->input('searchDate')!=''){
            $query = $query->where('transaction.trans_year',date_format(date_create($request->input('searchDate')),"Y"))
            ->where('transaction.trans_month',date_format(date_create($request->input('searchDate')),"m"))
            ->where('transaction.trans_date',date_format(date_create($request->input('searchDate')),"d"));
        }
        $data = $query->orderByDesc('transaction.created_date')->get();
        return $data;
    }

    public static function getReportList($request){
        $query = DB::table('transaction')   
            ->select(DB::raw('transaction.*, CONCAT("TRN",LPAD(transaction.id, 8, "0")) as trans_no, CASE WHEN transaction.dk ="kredit" THEN transaction.amount ELSE NULL END AS kredit, CASE WHEN transaction.dk ="debit" THEN transaction.amount ELSE NULL END AS debit, coa_code, DATE_FORMAT(CONCAT(trans_year,"-",trans_month,"-",trans_date), "%d %M %Y") as groupDate'));
            if($request->input('searchStartDate')!='' && $request->input('searchEndDate')!=''){
                $query->whereBetween(DB::raw("STR_TO_DATE(CONCAT(trans_month,' ',trans_date,' ',trans_year), '%m %d %Y')"),[$request->input('searchStartDate'),$request->input('searchEndDate')]);
            }
            if($request->input('searchYear')!='')$query->where('trans_year',$request->input('searchYear'));
            if($request->input('searchMonth')!='')$query->where('trans_month',$request->input('searchMonth'));
            if($request->input('searchDate')!='')$query->whereRaw("STR_TO_DATE(CONCAT(trans_month,' ',trans_date,' ',trans_year), '%m %d %Y') ='". $request->input('searchDate')."' ");
            if($request->input('searchCoa')!='')$query->where('transaction.coa_code',$request->input('searchCoa'));
                              
        return $query
            ->orderBy('trans_year', 'desc')
            ->orderBy('trans_month', 'desc')
            ->orderBy('trans_date', 'desc')
            ->get()->toArray();
    }
}
