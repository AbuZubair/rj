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
        'nis',
        'jenjang',
        'tingkat_kelas',
        'th_ajaran',
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
     * Set relationship with siswa
     */
    public function siswa()
    {
        return $this->belongsTo('App\Siswa', 'nis', 'nis');
    }

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
            ->leftJoin('siswa', 'siswa.nis', '=', 'iuran.nis')
            ->select(DB::raw('iuran.*, param.label as type_iuran, siswa.fullname, CASE WHEN iuran.status = 1 THEN "Sudah Dibayarkan" ELSE "Belum Dibayar" END AS status_iuran'));
    }

    public static function getData($request)
    {
        $class = new Iuran();
        $query = $class->getQuery();
        if($request->get('jenjang')!='')$query->where('iuran.jenjang',$request->get('jenjang'));
        if($request->get('tingkat_kelas')!='')$query->where('iuran.tingkat_kelas',$request->get('tingkat_kelas'));
        if($request->get('th_ajaran')!='')$query->where('iuran.th_ajaran',$request->get('th_ajaran'));
        if($request->get('date')!='')$query->where("paid_date", $request->get('date'));
        if($request->get('type')!='')$query->where('iuran.type',$request->get('type'));
        $data = $query->orderByDesc('paid_date')->get();
        return $data;
    }

    public static function getReportList($request)
    {
        // Get all active siswa and join with each biaya siswa table that has th_ajaran from request  
        // Add new column in select called "status_spp", this column will be 1 if month and year "spp_terakhir" in Siswa is less than this month if today's date is more than 10, otherwise 0
        $query = DB::table('siswa')
            ->leftJoin(
                DB::raw("(select nis, th_ajaran, status_um, status_du, um_masuk, du_masuk from `biaya_siswa` where `biaya_siswa`.`th_ajaran` = '".$request->input('tahun_ajaran')."') `bs`"),
                'bs.nis', '=', 'siswa.nis'
            )
            ->leftJoin('parameter as a', 'a.value', '=', 'siswa.jenjang') 
            ->select('siswa.fullname', 'siswa.jenjang', 'a.label as jenjang_text', 'siswa.tingkat_kelas', 'siswa.nis', 'siswa.spp_terakhir', 'bs.um_masuk', 'bs.du_masuk', 'bs.th_ajaran', 'bs.status_du', 'bs.status_um', DB::raw('CASE WHEN MONTH(siswa.spp_terakhir) >= MONTH(CURRENT_DATE) AND YEAR(siswa.spp_terakhir) = YEAR(CURRENT_DATE) THEN 1 ELSE 0 END AS status_spp'))
            ->where('siswa.is_active', 'Y');

        if($request->input('jenjang') != '') {
            $query->where('siswa.jenjang', $request->input('jenjang'));
        }

        if($request->input('tingkat_kelas') != '') {
            $query->where('siswa.tingkat_kelas', $request->input('tingkat_kelas'));
        }

        return $query->get()->toArray();
    }
}
