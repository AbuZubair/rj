<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemProduk extends Model
{
    protected $table = "item";

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
        'item_code',
        'item_name',
        'group_item',
        'satuan_beli',
        'satuan_jual',
        'harga_beli',
        'hpp',
        'harga_jual',
        'konversi',
        'sales_available',
        'is_active',
        'is_deleted',
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

    public static function getData()
    {
        $query =  DB::table('item')
            ->leftJoin(
                DB::raw("(select * from `parameter` where `parameter`.`param` = 'item_grup') `a`"), 
                'a.value', '=', 'item.group_item'
            )
            ->leftJoin(
                DB::raw("(select * from `parameter` where `parameter`.`param` = 'item_satuan') `b`"), 
                'b.value', '=', 'item.satuan_beli'
            )
            ->leftJoin(
                DB::raw("(select * from `parameter` where `parameter`.`param` = 'item_satuan') `c`"), 
                'c.value', '=', 'item.satuan_jual'
            )
            ->select('item.*', 'a.label as grup', 'b.label as satuan_beli_label', 'c.label as satuan_jual_label');
        $data = $query->orderByDesc('created_date')->get();
        return $data;
    }

    public static function getFullList($request)
    {
        $query =  DB::table('item')
            ->leftJoin(
                DB::raw("(select * from `parameter` where `parameter`.`param` = 'item_grup') `a`"), 
                'a.value', '=', 'item.group_item'
            )
            ->leftJoin('stock_item', 'stock_item.item_code', '=', 'item.item_code') 
            ->select('item.*', 'a.label as grup', 'stock_item.balance as stock_balance, stock_item.satuan as stock_satuan');
        if($request->get('query') != null && $request->get('query') != '')$query->where('item.item_name', 'like', $request->get('query').'%')->orWhere('item.item_code', 'like', $request->get('query').'%');
        $data = $query->where('item.is_active','Y')->orderByDesc('created_date')->get();
        return $data;
    }
}
