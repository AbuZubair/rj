<?php

use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProdukMultiSheetExport;
use App\Exports\StaffMultiSheetExport;
use App\Exports\SiswaMultiSheetExport;

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
    Route::post('dashboard/ajaran-baru', ['as' => 'dashboard.ajaran-baru', 'uses' => 'HomeController@ajaranBaru'])->middleware('ajax-call');
    
    Route::get('user', ['as' => 'user', 'uses' => 'UserController@index']);
    Route::get('user/create', ['as' => 'user.create', 'uses' => 'UserController@add']);
    Route::get('user/edit', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
    Route::post('user', ['as' => 'user.crud', 'uses' => 'UserController@crud'])->middleware('ajax-call');
    Route::post('user/delete', ['as' => 'user.delete', 'uses' => 'UserController@delete'])->middleware('ajax-call');
    Route::get('user/list', ['as' => 'user.list', 'uses' => 'UserController@getData'])->middleware('ajax-call');
    Route::get('user/email', ['as' => 'user.email', 'uses' => 'UserController@Email'])->middleware('ajax-call');


    Route::group(['namespace' => 'Settings'], function(){ 
        Route::get('settings/biaya', ['as' => 'settings.biaya', 'uses' => 'BiayaController@index']);
        Route::get('settings/biaya/list', ['as' => 'settings.biaya.list', 'uses' => 'BiayaController@getData'])->middleware('ajax-call');
        Route::post('settings/biaya/save', ['as' => 'settings.biaya.save', 'uses' => 'BiayaController@save'])->middleware('ajax-call');

        Route::get('settings/organisasi', ['as' => 'organisasi', 'uses' => 'OrganizationController@index']);
        Route::get('settings/organisasi/create', ['as' => 'organisasi.create', 'uses' => 'OrganizationController@add']);
        Route::get('settings/organisasi/edit', ['as' => 'organisasi.edit', 'uses' => 'OrganizationController@edit']);
        Route::post('settings/organisasi', ['as' => 'organisasi.crud', 'uses' => 'OrganizationController@crud'])->middleware('ajax-call');
        Route::post('settings/organisasi/delete', ['as' => 'organisasi.delete', 'uses' => 'OrganizationController@delete'])->middleware('ajax-call');
        Route::get('settings/organisasi/list', ['as' => 'organisasi.list', 'uses' => 'OrganizationController@getData'])->middleware('ajax-call');
    });

    Route::group(['namespace' => 'Master'], function(){   
        Route::get('master/staff', ['as' => 'staff', 'uses' => 'StaffController@index']);
        Route::get('master/staff/create', ['as' => 'staff.create', 'uses' => 'StaffController@add']);
        Route::get('master/staff/edit', ['as' => 'staff.edit', 'uses' => 'StaffController@edit']);
        Route::post('master/staff', ['as' => 'staff.crud', 'uses' => 'StaffController@crud'])->middleware('ajax-call');
        Route::post('master/staff/delete', ['as' => 'staff.delete', 'uses' => 'StaffController@delete'])->middleware('ajax-call');
        Route::get('master/staff/list', ['as' => 'staff.list', 'uses' => 'StaffController@getData'])->middleware('ajax-call');
        Route::get('master/staff/dropdown', ['as' => 'staff.dropdown', 'uses' => 'StaffController@getDropdown'])->middleware('ajax-call');
        Route::get('master/staff/dropdown-params', ['as' => 'staff.dropdown-param', 'uses' => 'StaffController@getParams'])->middleware('ajax-call');
        Route::post('master/staff-import', ['as' => 'staff.import', 'uses' => 'StaffController@store'])->middleware('ajax-call');
        Route::get('master/export-staff', function () {
            return Excel::download(new StaffMultiSheetExport, 'staff_'.date('YmdHis').'.xlsx');
        });

        Route::get('master/kesiswaan', ['as' => 'kesiswaan', 'uses' => 'SiswaController@index']);
        Route::get('master/kesiswaan/create', ['as' => 'kesiswaan.create', 'uses' => 'SiswaController@add']);
        Route::get('master/kesiswaan/edit', ['as' => 'kesiswaan.edit', 'uses' => 'SiswaController@edit']);
        Route::post('master/kesiswaan', ['as' => 'kesiswaan.crud', 'uses' => 'SiswaController@crud'])->middleware('ajax-call');
        Route::post('master/kesiswaan/delete', ['as' => 'kesiswaan.delete', 'uses' => 'SiswaController@delete'])->middleware('ajax-call');
        Route::get('master/kesiswaan/list', ['as' => 'kesiswaan.list', 'uses' => 'SiswaController@getData'])->middleware('ajax-call');
        Route::get('master/kesiswaan/dropdown', ['as' => 'kesiswaan.dropdown', 'uses' => 'SiswaController@getDropdown'])->middleware('ajax-call');
        Route::get('master/kesiswaan/dropdown-params', ['as' => 'kesiswaan.dropdown-param', 'uses' => 'SiswaController@getParams'])->middleware('ajax-call');
        Route::get('master/kesiswaan/biaya/{nis}', ['as' => 'kesiswaan.biaya-list', 'uses' => 'SiswaController@getBiaya'])->middleware('ajax-call');
        Route::post('master/kesiswaan/biaya', ['as' => 'kesiswaan.biaya', 'uses' => 'SiswaController@updateBiaya'])->middleware('ajax-call');
        Route::post('master/kesiswaan-import', ['as' => 'kesiswaan.import', 'uses' => 'SiswaController@store'])->middleware('ajax-call');
        Route::get('master/export-kesiswaan', function () {
            return Excel::download(new SiswaMultiSheetExport, 'kesiswaan_'.date('YmdHis').'.xlsx');
        });
    });

    Route::get('coa', ['as' => 'coa', 'uses' => 'CoaController@index']);
    Route::get('coa/create', ['as' => 'coa.create', 'uses' => 'CoaController@add']);
    Route::get('coa/edit', ['as' => 'coa.edit', 'uses' => 'CoaController@edit']);
    Route::post('coa', ['as' => 'coa.crud', 'uses' => 'CoaController@crud'])->middleware('ajax-call');
    Route::post('coa/delete', ['as' => 'coa.delete', 'uses' => 'CoaController@delete'])->middleware('ajax-call');
    Route::get('coa/list', ['as' => 'coa.list', 'uses' => 'CoaController@getData'])->middleware('ajax-call');
    Route::get('coa/dropdown-level', ['as' => 'coa.dropdown-level', 'uses' => 'CoaController@getLevelDropdown'])->middleware('ajax-call');
    Route::get('coa/dropdown-list', ['as' => 'coa.dropdown-list', 'uses' => 'CoaController@getDropdownList'])->middleware('ajax-call');

    Route::get('iuran', ['as' => 'iuran', 'uses' => 'IuranController@index']);
    Route::get('iuran/create', ['as' => 'iuran.create', 'uses' => 'IuranController@add']);
    Route::get('iuran/edit', ['as' => 'iuran.edit', 'uses' => 'IuranController@edit']);
    Route::post('iuran', ['as' => 'iuran.crud', 'uses' => 'IuranController@crud'])->middleware('ajax-call');
    Route::post('iuran/delete', ['as' => 'iuran.delete', 'uses' => 'IuranController@delete'])->middleware('ajax-call');
    Route::get('iuran/list', ['as' => 'iuran.list', 'uses' => 'IuranController@getData'])->middleware('ajax-call');
    Route::get('iuran/dropdown-params', ['as' => 'iuran.dropdown-param', 'uses' => 'IuranController@getParams'])->middleware('ajax-call');
    Route::get('iuran/detail/{id}', ['as' => 'iuran.detail', 'uses' => 'IuranController@getDetail'])->middleware('ajax-call');
    Route::get('iuran/get-current-tahun-ajaran', ['as' => 'iuran.get-current-tahun-ajaran', 'uses' => 'IuranController@getTahunAjaran'])->middleware('ajax-call');
    Route::get('iuran/print-bukti/{nis}', ['as' => 'iuran.print-bukti', 'uses' => 'IuranController@printBukti']);

    Route::get('trans', ['as' => 'trans', 'uses' => 'TransController@index']);
    Route::get('trans/create', ['as' => 'trans.create', 'uses' => 'TransController@add']);
    Route::get('trans/edit', ['as' => 'trans.edit', 'uses' => 'TransController@edit']);
    Route::post('trans', ['as' => 'trans.crud', 'uses' => 'TransController@crud'])->middleware('ajax-call');
    Route::post('trans/delete', ['as' => 'trans.delete', 'uses' => 'TransController@delete'])->middleware('ajax-call');
    Route::post('trans/list', ['as' => 'trans.list', 'uses' => 'TransController@getData'])->middleware('ajax-call');
    Route::get('trans/dropdown', ['as' => 'trans.dropdown', 'uses' => 'TransController@getDropdown'])->middleware('ajax-call');

    Route::get('finance', ['as' => 'finance', 'uses' => 'FinanceController@index']);
    Route::get('finance/get-last-closing', ['as' => 'finance.get-last-closing', 'uses' => 'FinanceController@getLastClosing'])->middleware('ajax-call');
     Route::get('finance/get-last-year-closing', ['as' => 'finance.get-last-year-closing', 'uses' => 'FinanceController@getLastYearClosing'])->middleware('ajax-call');
    Route::post('finance/submit-closing', ['as' => 'finance.submit-closing', 'uses' => 'FinanceController@submitClosing'])->middleware('ajax-call');
    Route::post('finance/submit-year-closing', ['as' => 'finance.submit-year-closing', 'uses' => 'FinanceController@submitYearClosing'])->middleware('ajax-call');
   
    // Route::get('shu/persentase', ['as' => 'shu.persentase', 'uses' => 'ShuController@persentase']);
    // Route::get('shu/pembagian', ['as' => 'shu.pembagian', 'uses' => 'ShuController@index']);
    // Route::get('shu/pengurus', ['as' => 'shu.pengurus', 'uses' => 'ShuController@pengurus']);
    // Route::get('shu/persentase/get-data', ['as' => 'shu.persentase.get-data', 'uses' => 'ShuController@persentaseGetData'])->middleware('ajax-call');
    // Route::post('shu/persentase/save-persentase', ['as' => 'shu.persentase.save-persentase', 'uses' => 'ShuController@persentaseSaveData'])->middleware('ajax-call');
    // Route::get('shu/pembagian/list', ['as' => 'shu.pembagian.list', 'uses' => 'ShuController@getData'])->middleware('ajax-call');
    // Route::post('shu/pengurus/list', ['as' => 'shu.pengurus.list', 'uses' => 'ShuController@getDataPengurus'])->middleware('ajax-call');

    Route::get('report/{type}', ['as' => 'report.view', 'uses' => 'ReportController@view']);
    Route::post('report/list/{type}', ['as' => 'report.list', 'uses' => 'ReportController@getData'])->middleware('ajax-call');

    // Route::get('log', ['as' => 'log', 'uses' => 'LogsController@index']);
    // Route::get('log/list', ['as' => 'log.list', 'uses' => 'LogsController@getData'])->middleware('ajax-call');
});