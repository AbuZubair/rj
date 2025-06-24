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
        'no_murabahah',
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
            ->leftJoin('murabahah', 'transaction.no_murabahah', '=', 'murabahah.no_murabahah')
            ->leftJoin('anggota', 'murabahah.no_anggota', '=', 'anggota.no_anggota') 
            ->leftJoin('coa', 'coa.coa_code', '=', 'transaction.coa_code')                     
            ->select(DB::raw('transaction.*,CONCAT("TRN",LPAD(transaction.id, 8, "0")) as trans_no,anggota.fullname as anggota,coa.coa_name'));
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
        if($request->input('dk')!='')$query = $query->where('transaction.dk',$request->input('dk'));
        if(in_array(Auth::user()->getRole(), [4]))$query->where('transaction.trans_type','konsinasi');
        // $query = $query->where('tans_desc',"!=","Auto Generate Potongan");
        $data = $query->orderByDesc('transaction.created_date')->get();
        return $data;
    }

    public static function getPaginatedData($request)
    {
        $search = $request->input('search.value');
        $columns = $request->get('columns');
    
        $pageSize = ($request->length) ? $request->length : 10;

        /**
         * Base query
         */
        $class = new Transaction();
        $query = $class->getQuery();

        $itemCounter = $query->get();
        $count_total = $query->count();

        // Filter by search and filter
        $count_filter = 0;
        $is_filtered = false;
        if($request->input('coa_code')!=''){
            $query = $query->where('transaction.coa_code',$request->input('coa_code'));
            $is_filtered = true;
        }
        if($request->input('searchDate')!=''){
            $query = $query->where('transaction.trans_year',date_format(date_create($request->input('searchDate')),"Y"))
                        ->where('transaction.trans_month',date_format(date_create($request->input('searchDate')),"m"))
                        ->where('transaction.trans_date',date_format(date_create($request->input('searchDate')),"d"));
            $is_filtered = true;
        }
        if($request->input('dk')!=''){
            $query = $query->where('transaction.dk',$request->input('dk'));
            $is_filtered = true;
        }        
        if($search != ''){
            $query = $query->where( 'transaction.id' , 'LIKE' , '%'.$search.'%')
                        ->orWhere( 'transaction.tans_desc' , 'LIKE' , '%'.$search.'%')
                        ->orWhere( 'coa.coa_name' , 'LIKE' , '%'.$search.'%');
            $is_filtered = true;
        }
        if($is_filtered){
            $count_filter = $query->count();
        }
        if(in_array(Auth::user()->getRole(), [4]))$query->where('transaction.trans_type','konsinasi');

        // Paginate
        $start = ($request->start) ? $request->start : 0;
        $query->skip($start)->take($pageSize);

        if($count_filter == 0){
            $count_filter = $count_total;
        }

        $data = $query->orderByDesc('transaction.created_date')->get();
        return array('data' => $data,'count_filter' => $count_filter,'count_total' => $count_total);
    }
}
