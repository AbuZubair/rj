<?php

use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProdukImport;
use App\Exports\ProdukMultiSheetExport;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', ['as' => 'dashboard', 'uses' => 'HomeController@index'])->middleware('auth');
Auth::routes();

Route::group(['namespace' => 'Auth'], function(){    
   //Login Admin
	Route::get('login', ['middleware' => 'web', 'uses' => 'LoginController@index'])->name('login');
    Route::post('login', ['middleware' => 'web', 'uses' => 'LoginController@onSignedIn'])->name('auth.on-sign-in');

    //Sign Out User
    Route::get('logout', ['middleware' => 'web', 'uses' => 'SignOutController@index'])->name('logout');
 
    //Change Password
    Route::get('change-password', ['middleware' => 'web', 'uses' => 'ChangePasswordController@index'])->name('auth.change-password');
    Route::post('change-password', ['middleware' => 'web', 'uses' => 'ChangePasswordController@save'])->name('auth.change-password.save');

    //Forgot Password
    Route::post('forgot-password', ['middleware' => 'web', 'uses' => 'LoginController@forgotPasssword'])->name('auth.forgot-password');
});

Route::group(['middleware' => ['auth','web']], function () {
    Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'HomeController@index']);
    Route::get('dashboard/get-card-data', ['as' => 'dashboard.get-card-data', 'uses' => 'HomeController@getCardData'])->middleware('ajax-call');
    Route::get('dashboard/get-credit-summary', ['as' => 'dashboard.get-credit-summary', 'uses' => 'HomeController@getCreditSummary'])->middleware('ajax-call');
    Route::get('dashboard/get-pertumbuhan-anggota', ['as' => 'dashboard.get-pertumbuhan-anggota', 'uses' => 'HomeController@getPertumbuhanAnggota'])->middleware('ajax-call'); 
    Route::get('dashboard/get-sales-toko', ['as' => 'dashboard.get-sales-toko', 'uses' => 'HomeController@getSalesToko'])->middleware('ajax-call'); 
    Route::get('dashboard/get-purchase', ['as' => 'dashboard.get-purchase', 'uses' => 'HomeController@getPurchase'])->middleware('ajax-call'); 

    Route::get('user', ['as' => 'user', 'uses' => 'UserController@index']);
    Route::get('user/create', ['as' => 'user.create', 'uses' => 'UserController@add']);
    Route::get('user/edit', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
    Route::post('user/crud/{request}', ['as' => 'user.crud', 'uses' => 'UserController@crud'])->middleware('ajax-call');
    Route::post('user/delete', ['as' => 'user.delete', 'uses' => 'UserController@delete'])->middleware('ajax-call');
    Route::get('user/list', ['as' => 'user.list', 'uses' => 'UserController@getData'])->middleware('ajax-call');
    Route::get('user/email', ['as' => 'user.email', 'uses' => 'UserController@Email'])->middleware('ajax-call');

    Route::get('settings', ['as' => 'settings', 'uses' => 'SettingsController@index']);
        Route::get('settings/list', ['as' => 'settings.list', 'uses' => 'SettingsController@getData'])->middleware('ajax-call');
        Route::post('settings/save', ['as' => 'settings.save', 'uses' => 'SettingsController@save'])->middleware('ajax-call');

    Route::group(['namespace' => 'Master'], function(){   
        Route::get('master/anggota', ['as' => 'anggota', 'uses' => 'AnggotaController@index']);
        Route::get('master/anggota/create', ['as' => 'anggota.create', 'uses' => 'AnggotaController@add']);
        Route::get('master/anggota/edit', ['as' => 'anggota.edit', 'uses' => 'AnggotaController@edit']);
        Route::post('master/anggota/crud/{request}', ['as' => 'anggota.crud', 'uses' => 'AnggotaController@crud'])->middleware('ajax-call');
        Route::post('master/anggota/delete', ['as' => 'anggota.delete', 'uses' => 'AnggotaController@delete'])->middleware('ajax-call');
        Route::get('master/anggota/list', ['as' => 'anggota.list', 'uses' => 'AnggotaController@getData'])->middleware('ajax-call');
        Route::get('master/anggota/dropdown', ['as' => 'anggota.dropdown', 'uses' => 'AnggotaController@getDropdown'])->middleware('ajax-call');
        Route::get('master/anggota/dropdown-params', ['as' => 'anggota.dropdown-param', 'uses' => 'AnggotaController@getParams'])->middleware('ajax-call');

        Route::get('master/produk', ['as' => 'produk', 'uses' => 'ItemProdukController@index']);
        Route::get('master/produk/create', ['as' => 'produk.create', 'uses' => 'ItemProdukController@add']);
        Route::get('master/produk/edit', ['as' => 'produk.edit', 'uses' => 'ItemProdukController@edit']);
        Route::post('master/produk/crud/{request}', ['as' => 'produk.crud', 'uses' => 'ItemProdukController@crud'])->middleware('ajax-call');
        Route::post('master/produk/delete', ['as' => 'produk.delete', 'uses' => 'ItemProdukController@delete'])->middleware('ajax-call');
        Route::get('master/produk/list', ['as' => 'produk.list', 'uses' => 'ItemProdukController@getData'])->middleware('ajax-call');
        Route::get('master/produk/dropdown', ['as' => 'produk.dropdown', 'uses' => 'ItemProdukController@getDropdown'])->middleware('ajax-call');
        Route::get('master/produk/typehead', ['as' => 'produk.typehead', 'uses' => 'ItemProdukController@getByQuery'])->middleware('ajax-call');
        Route::get('master/produk/dropdown-params', ['as' => 'produk.dropdown-param', 'uses' => 'ItemProdukController@getParams'])->middleware('ajax-call');

        Route::post('master/product-import', ['as' => 'produk.import', 'uses' => 'ItemProdukController@store'])->middleware('ajax-call');
        Route::get('master/export-product', function () {
            return Excel::download(new ProdukMultiSheetExport, 'produk_'.date('YmdHis').'.xlsx');
        });

        Route::get('master/pengurus', ['as' => 'pengurus', 'uses' => 'PengurusController@index']);
        Route::get('master/pengurus/list', ['as' => 'pengurus.list', 'uses' => 'PengurusController@getData'])->middleware('ajax-call');
        Route::post('master/pengurus/save', ['as' => 'pengurus.save', 'uses' => 'PengurusController@save'])->middleware('ajax-call');

        Route::get('master/organisasi', ['as' => 'organisasi', 'uses' => 'OrganizationController@index']);
        Route::get('master/organisasi/create', ['as' => 'organisasi.create', 'uses' => 'OrganizationController@add']);
        Route::get('master/organisasi/edit', ['as' => 'organisasi.edit', 'uses' => 'OrganizationController@edit']);
        Route::post('master/organisasi', ['as' => 'organisasi.crud', 'uses' => 'OrganizationController@crud'])->middleware('ajax-call');
        Route::post('master/organisasi/delete', ['as' => 'organisasi.delete', 'uses' => 'OrganizationController@delete'])->middleware('ajax-call');
        Route::get('master/organisasi/list', ['as' => 'organisasi.list', 'uses' => 'OrganizationController@getData'])->middleware('ajax-call');
    });

    Route::group(['namespace' => 'ItemTransaction'], function(){   
        Route::get('item-transaction/purchase-order', ['as' => 'po', 'uses' => 'POController@index']);
        Route::get('item-transaction/purchase-order/create', ['as' => 'po.create', 'uses' => 'POController@add']);
        Route::get('item-transaction/purchase-order/edit', ['as' => 'po.edit', 'uses' => 'POController@edit']);
        Route::post('item-transaction/purchase-order/crud/{request}', ['as' => 'po.crud', 'uses' => 'POController@crud'])->middleware('ajax-call');
        Route::post('item-transaction/purchase-order/delete', ['as' => 'po.delete', 'uses' => 'POController@delete'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order/list', ['as' => 'po.list', 'uses' => 'POController@getData'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order/dropdown', ['as' => 'po.dropdown', 'uses' => 'POController@getDropdown'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order/dropdown-params', ['as' => 'po.dropdown-param', 'uses' => 'POController@getParams'])->middleware('ajax-call');

        Route::get('item-transaction/purchase-order-receive', ['as' => 'por', 'uses' => 'PORController@index']);
        Route::get('item-transaction/purchase-order-receive/create', ['as' => 'por.create', 'uses' => 'PORController@add']);
        Route::get('item-transaction/purchase-order-receive/edit', ['as' => 'por.edit', 'uses' => 'PORController@edit']);
        Route::post('item-transaction/purchase-order-receive/crud/{request}', ['as' => 'por.crud', 'uses' => 'PORController@crud'])->middleware('ajax-call');
        Route::post('item-transaction/purchase-order-receive/delete', ['as' => 'por.delete', 'uses' => 'PORController@delete'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order-receive/list', ['as' => 'por.list', 'uses' => 'PORController@getData'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order-receive/dropdown', ['as' => 'por.dropdown', 'uses' => 'PORController@getDropdown'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order-receive/dropdown-params', ['as' => 'por.dropdown-param', 'uses' => 'PORController@getParams'])->middleware('ajax-call');

        Route::get('item-transaction/purchase-order-return', ['as' => 'ret', 'uses' => 'RETController@index']);
        Route::get('item-transaction/purchase-order-return/create', ['as' => 'ret.create', 'uses' => 'RETController@add']);
        Route::get('item-transaction/purchase-order-return/edit', ['as' => 'ret.edit', 'uses' => 'RETController@edit']);
        Route::post('item-transaction/purchase-order-return/crud/{request}', ['as' => 'ret.crud', 'uses' => 'RETController@crud'])->middleware('ajax-call');
        Route::post('item-transaction/purchase-order-return/delete', ['as' => 'ret.delete', 'uses' => 'RETController@delete'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order-return/list', ['as' => 'ret.list', 'uses' => 'RETController@getData'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order-return/dropdown', ['as' => 'ret.dropdown', 'uses' => 'RETController@getDropdown'])->middleware('ajax-call');
        Route::get('item-transaction/purchase-order-return/dropdown-params', ['as' => 'ret.dropdown-param', 'uses' => 'RETController@getParams'])->middleware('ajax-call');
    });

    Route::group(['namespace' => 'Stock'], function(){   
        Route::get('stock/stock-information', ['as' => 'stock-information', 'uses' => 'StockInformationController@index']);
        Route::get('stock/stock-information/list', ['as' => 'stock-information.list', 'uses' => 'StockInformationController@getData'])->middleware('ajax-call');
       
        Route::get('stock/stock-adjustment', ['as' => 'stock-adjustment', 'uses' => 'StockAdjusmentController@index']);
        Route::post('stock/stock-adjustment/crud/{request}', ['as' => 'stock-adjustment.crud', 'uses' => 'StockAdjusmentController@crud'])->middleware('ajax-call');
        Route::get('stock/stock-adjustment/get-stock', ['as' => 'stock-adjustment.get-stock', 'uses' => 'StockAdjusmentController@getStok'])->middleware('ajax-call');
        
        Route::get('stock/stock-card', ['as' => 'stock-card', 'uses' => 'StockCardController@index']);
        Route::get('stock/stock-card/list', ['as' => 'stock-card.list', 'uses' => 'StockCardController@getData'])->middleware('ajax-call');
    });

    Route::get('coa', ['as' => 'coa', 'uses' => 'CoaController@index']);
    Route::get('coa/create', ['as' => 'coa.create', 'uses' => 'CoaController@add']);
    Route::get('coa/edit', ['as' => 'coa.edit', 'uses' => 'CoaController@edit']);
    Route::post('coa/crud/{request}', ['as' => 'coa.crud', 'uses' => 'CoaController@crud'])->middleware('ajax-call');
    Route::post('coa/delete', ['as' => 'coa.delete', 'uses' => 'CoaController@delete'])->middleware('ajax-call');
    Route::get('coa/list', ['as' => 'coa.list', 'uses' => 'CoaController@getData'])->middleware('ajax-call');
    Route::get('coa/dropdown-level', ['as' => 'coa.dropdown-level', 'uses' => 'CoaController@getLevelDropdown'])->middleware('ajax-call');
    Route::get('coa/dropdown-list', ['as' => 'coa.dropdown-list', 'uses' => 'CoaController@getDropdownList'])->middleware('ajax-call');

    Route::get('iuran', ['as' => 'iuran', 'uses' => 'IuranController@index']);
    Route::get('iuran/create', ['as' => 'iuran.create', 'uses' => 'IuranController@add']);
    Route::get('iuran/edit', ['as' => 'iuran.edit', 'uses' => 'IuranController@edit']);
    Route::post('iuran/crud/{request}', ['as' => 'iuran.crud', 'uses' => 'IuranController@crud'])->middleware('ajax-call');
    Route::post('iuran/delete', ['as' => 'iuran.delete', 'uses' => 'IuranController@delete'])->middleware('ajax-call');
    Route::get('iuran/list', ['as' => 'iuran.list', 'uses' => 'IuranController@getData'])->middleware('ajax-call');
    Route::get('iuran/dropdown-params', ['as' => 'iuran.dropdown-param', 'uses' => 'IuranController@getParams'])->middleware('ajax-call');
    Route::get('iuran/dropdown-list', ['as' => 'iuran.dropdown-list', 'uses' => 'IuranController@getDropdownList'])->middleware('ajax-call');
    Route::post('iuran/update-status', ['as' => 'iuran.update-status', 'uses' => 'IuranController@UpdateStatus'])->middleware('ajax-call');
    Route::post('iuran/set-thr', ['as' => 'iuran.set-thr', 'uses' => 'IuranController@SetThr'])->middleware('ajax-call');
    Route::get('iuran/get-thr', ['as' => 'iuran.get-thr', 'uses' => 'IuranController@getThr'])->middleware('ajax-call');
    Route::get('iuran/amount-iuran', ['as' => 'iuran.amount-iuran', 'uses' => 'IuranController@getAmount'])->middleware('ajax-call');

    Route::get('sales', ['as' => 'sales', 'uses' => 'SalesController@index']);
    Route::get('sales/create', ['as' => 'sales.create', 'uses' => 'SalesController@add']);
    Route::get('sales/edit', ['as' => 'sales.edit', 'uses' => 'SalesController@edit']);
    Route::post('sales/crud/{request}', ['as' => 'sales.crud', 'uses' => 'SalesController@crud'])->middleware('ajax-call');
    Route::get('sales/get-detail', ['as' => 'sales.get-detail', 'uses' => 'SalesController@getDetail'])->middleware('ajax-call');
    Route::get('sales/export-sales-detail', ['as' => 'sales.export-sales-detail', 'uses' => 'SalesController@exportDetail']);
    Route::get('sales/get-hutang', ['as' => 'sales.get-hutang', 'uses' => 'SalesController@getHutang']);
    Route::post('sales/bayar-hutang', ['as' => 'sales.bayar-hutang', 'uses' => 'SalesController@bayarHutang'])->middleware('ajax-call');

    Route::get('akad-kredit', ['as' => 'murabahah', 'uses' => 'MurabahahController@index']);
    Route::get('akad-kredit/create', ['as' => 'murabahah.create', 'uses' => 'MurabahahController@add']);
    Route::get('akad-kredit/edit', ['as' => 'murabahah.edit', 'uses' => 'MurabahahController@edit']);
    Route::post('akad-kredit/crud/{request}', ['as' => 'murabahah.crud', 'uses' => 'MurabahahController@crud'])->middleware('ajax-call');
    Route::post('akad-kredit/delete', ['as' => 'murabahah.delete', 'uses' => 'MurabahahController@delete'])->middleware('ajax-call');
    Route::get('akad-kredit/list', ['as' => 'murabahah.list', 'uses' => 'MurabahahController@getData'])->middleware('ajax-call');
    Route::get('akad-kredit/dropdown-level', ['as' => 'murabahah.dropdown-level', 'uses' => 'MurabahahController@getLevelDropdown'])->middleware('ajax-call');
    Route::get('akad-kredit/dropdown-list', ['as' => 'murabahah.dropdown-list', 'uses' => 'MurabahahController@getDropdownList'])->middleware('ajax-call');
    Route::post('akad-kredit/data-import', ['as' => 'murabahah.import', 'uses' => 'MurabahahController@store'])->middleware('ajax-call');
    Route::get('akad-kredit/check-update', ['as' => 'murabahah.check-update', 'uses' => 'MurabahahController@checkUpdate'])->middleware('ajax-call');

    Route::get('pengajuan', ['as' => 'pengajuan', 'uses' => 'PengajuanController@index']);
    Route::get('pengajuan/create', ['as' => 'pengajuan.create', 'uses' => 'PengajuanController@add']);
    Route::get('pengajuan/edit', ['as' => 'pengajuan.edit', 'uses' => 'PengajuanController@edit']);
    Route::post('pengajuan/crud/{request}', ['as' => 'pengajuan.crud', 'uses' => 'PengajuanController@crud'])->middleware('ajax-call');
    Route::post('pengajuan/delete', ['as' => 'pengajuan.delete', 'uses' => 'PengajuanController@delete'])->middleware('ajax-call');
    Route::get('pengajuan/list', ['as' => 'pengajuan.list', 'uses' => 'PengajuanController@getData'])->middleware('ajax-call');
    Route::get('pengajuan/dropdown-level', ['as' => 'pengajuan.dropdown-level', 'uses' => 'PengajuanController@getLevelDropdown'])->middleware('ajax-call');
    Route::get('pengajuan/dropdown-list', ['as' => 'pengajuan.dropdown-list', 'uses' => 'PengajuanController@getDropdownList'])->middleware('ajax-call');
    Route::post('pengajuan/approval', ['as' => 'pengajuan.approval', 'uses' => 'PengajuanController@approval'])->middleware('ajax-call');
    Route::get('pengajuan/download', ['as' => 'pengajuan.download', 'uses' => 'PengajuanController@download']);

    Route::get('trans', ['as' => 'trans', 'uses' => 'TransController@index']);
    Route::get('trans/create', ['as' => 'trans.create', 'uses' => 'TransController@add']);
    Route::get('trans/edit', ['as' => 'trans.edit', 'uses' => 'TransController@edit']);
    Route::post('trans/crud/{request}', ['as' => 'trans.crud', 'uses' => 'TransController@crud'])->middleware('ajax-call');
    Route::post('trans/delete', ['as' => 'trans.delete', 'uses' => 'TransController@delete'])->middleware('ajax-call');
    Route::post('trans/list', ['as' => 'trans.list', 'uses' => 'TransController@getData'])->middleware('ajax-call');
    Route::get('trans/transfer', ['as' => 'trans.transfer', 'uses' => 'TransController@transfer'])->middleware('ajax-call');
    Route::get('trans/dropdown', ['as' => 'trans.dropdown', 'uses' => 'TransController@getDropdown'])->middleware('ajax-call');
    Route::get('trans/getRek', ['as' => 'trans.getRek', 'uses' => 'TransController@getRek'])->middleware('ajax-call');
    Route::get('trans/getRekByName', ['as' => 'trans.getRekByName', 'uses' => 'TransController@getRekByName'])->middleware('ajax-call');

    Route::get('finance', ['as' => 'finance', 'uses' => 'FinanceController@index']);
    Route::get('finance/get-last-potongan', ['as' => 'finance.get-last-potongan', 'uses' => 'FinanceController@getLastPotongan'])->middleware('ajax-call');
    Route::get('finance/get-last-closing', ['as' => 'finance.get-last-closing', 'uses' => 'FinanceController@getLastClosing'])->middleware('ajax-call');
    Route::get('finance/get-last-year-closing', ['as' => 'finance.get-last-year-closing', 'uses' => 'FinanceController@getLastYearClosing'])->middleware('ajax-call');
    Route::post('finance/submit-potongan', ['as' => 'finance.submit-potongan', 'uses' => 'FinanceController@submitPotongan'])->middleware('ajax-call');
    Route::post('finance/submit-closing', ['as' => 'finance.submit-closing', 'uses' => 'FinanceController@submitClosing'])->middleware('ajax-call');
    Route::post('finance/submit-hpp', ['as' => 'finance.submit-hpp', 'uses' => 'FinanceController@submitHpp'])->middleware('ajax-call');
    Route::post('finance/submit-year-closing', ['as' => 'finance.submit-year-closing', 'uses' => 'FinanceController@submitYearClosing'])->middleware('ajax-call');

    Route::get('shu/persentase', ['as' => 'shu.persentase', 'uses' => 'ShuController@persentase']);
    Route::get('shu/pembagian', ['as' => 'shu.pembagian', 'uses' => 'ShuController@index']);
    Route::get('shu/pengurus', ['as' => 'shu.pengurus', 'uses' => 'ShuController@pengurus']);
    Route::get('shu/persentase/get-data', ['as' => 'shu.persentase.get-data', 'uses' => 'ShuController@persentaseGetData'])->middleware('ajax-call');
    Route::post('shu/persentase/save-persentase', ['as' => 'shu.persentase.save-persentase', 'uses' => 'ShuController@persentaseSaveData'])->middleware('ajax-call');
    Route::get('shu/pembagian/list', ['as' => 'shu.pembagian.list', 'uses' => 'ShuController@getData'])->middleware('ajax-call');
    Route::post('shu/pengurus/list', ['as' => 'shu.pengurus.list', 'uses' => 'ShuController@getDataPengurus'])->middleware('ajax-call');

    Route::get('report', ['as' => 'report', 'uses' => 'ReportController@index']);
    Route::post('report/list', ['as' => 'report.list', 'uses' => 'ReportController@getData'])->middleware('ajax-call');
    Route::get('report/list', ['as' => 'report.list', 'uses' => 'ReportController@getData'])->middleware('ajax-call');
    Route::get('report/dropdown', ['as' => 'report.dropdown', 'uses' => 'ReportController@getDropdown'])->middleware('ajax-call');

    Route::get('log', ['as' => 'log', 'uses' => 'LogsController@index']);
    Route::get('log/list', ['as' => 'log.list', 'uses' => 'LogsController@getData'])->middleware('ajax-call');
});