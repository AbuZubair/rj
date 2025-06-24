@extends('layouts.admin', ['activePage' => 'report', 'titlePage' => __('Report')])

@push('css')
<style>
  .tableList tbody tr.groupTR td.groupTitle {
      background-color: #9c27b0 !important;
      padding: 5px 10px !important;
      color: #fff;
  }
  .tableList tbody tr.groupTR td.groupTD {
      background-color: #9c27b0;
      font-size: 12px;
      white-space: normal;
      color: #fff;
  }
  .detaillink{
    color: #9c27b0 !important;
    cursor: pointer !important;
  }
</style>
@endpush

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">        
        <div class="card">          
          <div class="card-body">
            <div class="form-group">
              <label for="tgl_daftar">Report Type</label>
              <select class="form-control" name="searchType" id="searchType">    
                <option value="" disabled selected>Select your option</option>
                @if(!in_array(Auth::user()->getRole(),['3','4']))
                  <option value="iuran">Iuran / Tabungan</option>
                  <option value="piutang">Piutang Kredit</option>
                  <option value="potongan">List Potongan</option>
                  <option value="shu">SHU Anggota</option>
                  <option value="angsuran">Angsuran Per Anggota</option>
                  @if(!in_array(Auth::user()->getRole(),['1']))
                    <option value="transaction">Transaction</option>
                    <option value="tb">TB</option>
                    <option value="neraca">Neraca</option>
                    <option value="laba_rugi">Laba Rugi</option>
                  @endif    
                @endif
                @if(!in_array(Auth::user()->getRole(),['1']))
                  <option value="sales">Sales</option>
                @endif 
              </select>
            </div>
            <!-- <div class="form-group jurnal-sec" style="display: none;">
              <label for="">Tipe Jurnal</label>
              <select class="form-control" name="jurnal_type" id="jurnal_type">    
                <option value="tb">TB</option>
                <option value="neraca">Neraca</option>
                <option value="laba_rugi">Laba Rugi</option>
              </select>
            </div> -->
            <form class="searchForm" style="display: none;">                  
              <div class="row date-sec">
                <div class="col-md-6">              
                  <div class="form-group">
                    <label for="tgl_daftar">Year</label>
                    <select class="form-control" name="searchYear" id="searchYear">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>                                 
                </div>   
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="tgl_daftar">Month</label>
                    <select class="form-control" name="searchMonth" id="searchMonth">
                      <option value="" disabled selected>Select your option</option>
                      <option value="01">Januari</option>
                      <option value="02">Februari</option>
                      <option value="03">Maret</option>
                      <option value="04">April</option>
                      <option value="05">Mei</option>
                      <option value="06">Juni</option>
                      <option value="07">Juli</option>
                      <option value="08">Agustus</option>
                      <option value="09">September</option>
                      <option value="10">Oktober</option>
                      <option value="11">November</option>
                      <option value="12">Desember</option>
                    </select>
                  </div>  
                </div>            
              </div>
              <div class="row">
                <div class="col-md-6 iuran-sec">              
                  <div class="form-group">
                    <label for="tgl_daftar">Type</label>
                    <select class="form-control" name="searchTypeIuran" id="searchTypeIuran">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>                                 
                </div>
                <div class="col-md-6 iuran-sec">              
                  <div class="form-group">
                    <label for="tgl_daftar">Sampai Tahun</label>
                    <select class="form-control" name="searchUntilYear" id="searchUntilYear">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>                                 
                </div> 
                @if(Auth::user()->getRole()!='1')
                <div class="col-md-6 anggota-sec">              
                  <div class="form-group">
                    <label for="tgl_daftar">Anggota</label>
                    <select class="form-control" name="searchAnggota" id="searchAnggota">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>                                 
                </div>  
                @endif
                <div class="col-md-6 kredit-sec">              
                  <div class="form-group">
                    <label for="tgl_daftar">Kredit</label>
                    <select class="form-control" name="searchKredit" id="searchKredit">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>                                 
                </div>
                <div class="col-md-6 grade-sec">              
                  <div class="form-group">
                    <label for="tgl_daftar">Grade</label>
                    <select class="form-control" name="searchGrade" id="searchGrade">
                      <option value="" disabled selected>Select your option</option>
                      <option value="staff">Staff</option>
                      <option value="spv">SPV keatas</option>
                    </select>
                  </div>                                 
                </div>
                <div class="col-md-6 date-full-sec mt-4">
                  <div class="form-group">
                    <label for="tgl_daftar">Date</label>
                    <input type="date" class="form-control" id="searchDate" name="searchDate">
                  </div>
                </div>
                <div class="col-md-6 date-between-sec mt-4">
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label for="tgl_daftar">Start Date</label>
                        <input type="date" class="form-control" id="searchStartDate" name="searchStartDate">
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label for="tgl_daftar">End Date</label>
                        <input type="date" class="form-control" id="searchEndDate" name="searchEndDate">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col coa-sec">              
                  <div class="form-group">
                    <label for="tgl_daftar">COA</label>
                    <select class="form-control" name="searchCoa" id="searchCoa">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>                                 
                </div>
              </div>
            </form>                        
          </div>                   
          <div class="card-footer" style="justify-content: normal !important;display:none">
            <button class="btn btn-success" onClick="search()" >Search</button>
            <button class="btn btn-warning" onClick="reset()" >Clear Search</button>
          </div>   
        </div>    

        <table id="iuran-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>No</th>
                  <th>No. Anggota</th>                    
                  <th>Anggota</th>       
                  <th>Iuran Terakhir</th>  
                  <th>Per Tahun</th>            
                  <th>Tipe</th>
                  <th>Ref</th>
                  <th>Total</th>
                </tr>     
            </thead>
            <tbody>
            </tbody>
        </table>

        <table id="piutang-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>No</th>
                  <th>No. Anggota</th>                    
                  <th>Anggota</th>       
                  <th>Posisi</th>  
                  <th>Total Payment</th>            
                  <th>Sisa Piutang</th>
                  <th>Angsuran</th>
                  <th>Biaya Transport</th>
                  <th>Type</th>
                  <th>Remaining Deduction</th>
                </tr>     
            </thead>
            <tbody>
            </tbody>
        </table>

        <table id="potongan-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>No</th>                    
                  <th>No. Anggota</th>                    
                  <th>Anggota</th>
                  <th>Grade</th>
                  <th>Iuran</th>
                  <th>Piutang</th>
                  <th>Tabungan</th>
                  <th>Sembako</th>
                  <th>Total</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <table id="transaction-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>                    
                  <th class="r-sort">Date</th>
                  <th>ID</th>
                  <th>Description</th> 
                  <th>Kode COA</th>
                  <th>Month</th>                                   
                  <th>Debit</th>
                  <th>Kredit</th>        
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <table id="shu-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>No</th>
                  <th>No. Anggota</th>                    
                  <th>Anggota</th>       
                  <th>Tahun SHU</th>  
                  <th>SHU Iuran</th>            
                  <th>SHU Kredit</th>
                  <th>SHU Total</th>
                </tr>     
            </thead>
            <tbody>
            </tbody>
        </table>

        <table id="sales-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>Date</th>  
                  <th>Sales No</th>
                  <th>Note</th> 
                  <th>Grouped</th>
                  <th>Type</th>
                  <th>Total Cash</th>
                  <th>Total Piutang</th>
                  <th>Total</th>
                </tr>     
            </thead>
            <tbody>
            </tbody>
        </table>

        <table id="angsuran-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>No</th>
                  <th>No. Anggota</th>                    
                  <th>Anggota</th>      
                  <th>No. Akad</th> 
                  <th>Bulan Potongan</th>  
                  <th>Tahun</th>
                  <th>Nilai Awal</th>
                  <th>Nilai Total</th>
                  <th>Margin</th>
                  <th>Total Pembayaran</th>
                  <th>Status</th>
                  <th>Angsuran</th>            
                </tr>     
            </thead>
            <tbody>
            </tbody>
        </table>

        <div id="tb-sec">
          <div class="mb-2"><h4 class="per-month"></h4></div>
        </div>

        <table id="tb-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>Code</th>                    
                  <th>Coa</th>       
                  <th>Begining Balance</th>  
                  <th>Debit</th>            
                  <th>Kredit</th>
                  <th>Ending Balance</th>
                </tr>     
            </thead>
            <tbody>
            </tbody>
        </table>

        <div id="neraca-sec">
          <div class="mb-2"><h4 class="per-year"></h4></div>
        </div>
        
        <table id="neraca-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>Code</th>                    
                  <th>Coa</th>       
                  <th>Ending Balance</th>
                </tr>     
            </thead>
            <tbody>
            </tbody>
        </table>

        <table id="laba_rugi-table" class="table yajra-datatable tableList" style="width:100%;display:none">
            <thead>
                <tr>
                  <th>Code</th>                    
                  <th>Coa</th>       
                  <th>Ending Balance</th>
                </tr>     
            </thead>
            <tbody>
            </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="salesDetailModal" tabindex="-1" role="dialog" aria-labelledby="salesDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header pl-2 pb-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body modal-dialog-scrollable pt-0">
              <div class="card">
                <div class="card-header text-center">
                  <h5 class="mb-0">Sales Detail <span id="sales_no"></span>:</h5>
                </div>
                <div class="card-body">
                  <table class="table table-sales-detail table-info">
                    <thead>
                      <tr>
                        <th>Kode</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Satuan</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                        <th>Diskon</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
          </div>
      </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
  var table1;
  var table2; 
  var table3; 
  var table4;
  var table5;
  var table6;
  var table7;
  var table8;
  var table9;
  var table10;
  var listProject;
  const month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
  var role = "{{Auth::user()->getRole()}}"
  var currentYear = new Date().getFullYear()
  var currentMonth = ("0" + ((new Date()).getMonth() + 1)).slice(-2)
  $(document).ready(function(){    
    $('#searchType').select2()
    $('#searchMonth').select2()
    $('#searchGrade').select2()
    $('#jurnal_type').select2()
    $('select[name=searchType]').on('change', function() {
      if(this.value == 'iuran'){
        if(table1 == null){
          dtIuran();
        }
        $('.searchForm').show()
        $('.anggota-sec').show()
        $('.grade-sec').hide()
        $('.iuran-sec').show()
        $('.date-sec').hide()
        $('.date-full-sec').hide()
        $('.date-between-sec').hide()
        $('.card-footer').show()
        $('#neraca-sec').hide()
        $('.jurnal-sec').hide()
        $('.coa-sec').hide()
        $('.kredit-sec').hide()
        // search()
        showHideTable()
      }else if(this.value == 'piutang'){
        if(!['3','4'].includes(role)){
          if(table6 == null){
            dtPiutang();
          }
        }
        $('.searchForm').show()
        $('.anggota-sec').show()
        $('.grade-sec').show()
        $('.iuran-sec').hide()
        $('.date-sec').hide()
        $('.date-full-sec').hide()
        $('.date-between-sec').hide()
        $('.card-footer').show()
        $('#neraca-sec').hide()
        $('.jurnal-sec').hide()
        $('.coa-sec').hide()
        $('.kredit-sec').hide()
        showHideTable()
      }else if(this.value == 'potongan'){
        if(!['3','4'].includes(role)){
          if(table2 == null){
            dtPotongan();
          }
        }
        $('#searchMonth').val(currentMonth).trigger('change')
        $('.searchForm').show()
        $('.date-sec').show()
        $('.card-footer').show() 
        $('.anggota-sec').show()
        $('.iuran-sec').hide()
        $('.date-full-sec').hide()
        $('.date-between-sec').hide()
        $('#neraca-sec').hide()
        $('.grade-sec').show()
        $('.jurnal-sec').hide()
        $('.coa-sec').hide()
        $('.kredit-sec').hide()
        search()
      }else if(this.value == 'transaction'){
        if(!['3','4'].includes(role)){
          if(role!=1){
            if(table3 == null){
              dtTransaction();
            }
          }          
        }
        $('#searchMonth').val(currentMonth).trigger('change')
        $('.searchForm').show()
        $('.date-sec').show()
        $('.date-full-sec').show()
        $('.date-between-sec').show()
        $('.card-footer').show()
        $('.iuran-sec').hide()
        $('.anggota-sec').hide()
        $('#neraca-sec').hide()
        $('.grade-sec').hide()
        $('.jurnal-sec').hide()
        $('.coa-sec').show()
        $('.kredit-sec').hide()
        search()
      }else if(this.value == 'shu'){
        if(!['3','4'].includes(role)){
          if(table5 == null){
              dtShu();
          }          
        }
        $('.searchForm').show()
        $('.date-sec').hide()
        $('.date-full-sec').hide()
        $('.date-between-sec').hide()
        $('.card-footer').show()
        $('.iuran-sec').hide()
        $('.anggota-sec').show()
        $('#neraca-sec').hide()
        $('.grade-sec').hide()
        $('.jurnal-sec').hide()
        $('.coa-sec').hide()
        $('.kredit-sec').hide()
        showHideTable()
      }else if(this.value == 'sales'){
        if(table7 == null){
          dtSales();
        }
        $('.searchForm').show()
        $('.date-sec').show()
        $('.date-full-sec').show()
        $('.date-between-sec').hide()
        $('.card-footer').show()
        $('.iuran-sec').hide()
        $('.anggota-sec').hide()
        $('#neraca-sec').hide()
        $('.grade-sec').hide()
        $('.jurnal-sec').hide()
        $('.coa-sec').hide()
        $('.kredit-sec').hide()
        showHideTable()
      }else if(this.value == 'angsuran'){
        if(!['3','4'].includes(role)){
          if(table8 == null){
            dtAngsuran();
          }          
        }
        // $('#searchMonth').val(currentMonth).trigger('change')
        $('#searchYear').val('').trigger('change')
        $('.searchForm').show()
        $('.date-sec').show()
        $('.date-full-sec').hide()
        $('.date-between-sec').hide()
        $('.card-footer').show()
        $('.iuran-sec').hide()
        $('.anggota-sec').show()
        $('#neraca-sec').hide()
        $('.grade-sec').hide()
        $('.jurnal-sec').hide()
        $('.coa-sec').hide()
        if(role == 1){
          getKredit();
        }else{
          $('.kredit-sec').hide()
        }
        showHideTable()
      }else{
        if(!['3','4'].includes(role)){
          if(role!=1){
            if(this.value == 'tb'){
              if(table4 == null){
                dtTb();
                getLastClosing()
              }
            }else{
              getLastYearClosing()
            }
            if(this.value == 'laba_rugi'){
              if(table10 == null){
                dtLabaRugi();
              }
            }
            if(this.value == 'neraca'){
              if(table9 == null){
                dtNeraca();
              }
            }
          }          
        }
        $('.searchForm').hide()
        $('.date-sec').hide()
        $('.date-full-sec').hide()
        $('.date-between-sec').hide()
        $('.card-footer').hide()
        $('.iuran-sec').hide()
        $('.anggota-sec').hide()
        $('.grade-sec').hide()
        // $('.jurnal-sec').show()
        $('.coa-sec').hide()
        $('.kredit-sec').hide()
        showHideTable()
      }
    });   
    
    $('select[name=jurnal_type]').on('change', function() {
      if($('select[name=searchType]').val() == 'neraca'){
        if(this.value == 'tb'){
          $('#tb-table').show()
          $('#tb-table_wrapper').show()
          $('#neraca-table').hide()
          $('#neraca-table_wrapper').hide()
          $('#laba_rugi-table').hide()
          $('#laba_rugi-table_wrapper').hide()
        }else if(this.value == 'neraca'){
          $('#neraca-table').show()
          $('#neraca-table_wrapper').show()
          $('#tb-table').hide()
          $('#tb-table_wrapper').hide()
          $('#laba_rugi-table').hide()
          $('#laba_rugi-table_wrapper').hide()
        }else{
          $('#laba_rugi-table').show()
          $('#laba_rugi-table_wrapper').show()
          $('#neraca-table').hide()
          $('#neraca-table_wrapper').hide()
          $('#tb-table').hide()
          $('#tb-table_wrapper').hide()         
        }
      }
    });

    $('select[name=searchAnggota]').on('change', function() {
      if($('select[name=searchType]').val() == 'angsuran'){
        if(this.value != ''){
          getKredit();
        }
      }
    })
  
    generateArrayOfYears()
    getAnggotaDropdown(); 
    getCoa();
    getTypeIuran().then(() =>  {
      // dtIuran()
      $('#iuran-table_wrapper').hide()
    });
    if(!['3','4'].includes(role)){
      // dtPiutang();
      // dtPotongan();
      // dtShu();
      // dtAngsuran();
      if(role!=1){
        // dtTransaction();
        // dtTb();
        // dtNeraca();
        // dtLabaRugi();
      }
    }
    // dtSales();
    
    $('#piutang-table_wrapper').hide()
    $('button[aria-controls=potongan-table]').hide()
    $('button[aria-controls=transaction-table]').hide()  
    $('#tb-table_wrapper').hide()
    $('#neraca-table_wrapper').hide()
    $('#laba_rugi-table_wrapper').hide()
    $('#shu-table_wrapper').hide()    
    $('#sales-table_wrapper').hide()    
    $('#angsuran-table_wrapper').hide()    
  });  

  function dtIuran(){
    var groupCol = 4;
    table1 = $('#iuran-table').DataTable({
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        paging: false,
        autoWidth: false,
        retrieve: true,
        orderFixed: [
          [groupCol, 'asc']
        ],
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0,
            className: 'text-center'
          }, 
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {                         
                d.searchUntilYear = $('select[name=searchUntilYear]').val();
                d.searchTypeIuran = $('select[name=searchTypeIuran]').val();
                d.searchAnggota = $('select[name=searchAnggota]').val();
                d.type= "iuran"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data:null,name:''},
            {data: 'no_anggota', name: 'no_anggota'},  
            {data: 'fullname', name: 'fullname'},                  
            {data: null, render:function(data,type,full,meta){
                return formattingDate(data.month, data.max_year)
              }
            },
            {data: 'cur_year', name: 'cur_year'},
            {data: 'type_iuran', name: 'type_iuran'},
            {data: 'ref', render:function(data,type,full,meta){
                return (data)?data:'-'
              }
            },
            {data: null, render:function(data,type,full,meta){
                if(data){
                  const return_total = (data.return_total)?data.return_total:0;
                  return formatCurrency(data.total-return_total)
                }else{
                  return '0'
                }                 
              }
            }
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [ 1, 2, 3, 4, 5, 6, 7],
              total_index: [6]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Iuran_'+getDate()
          }
        ],
        drawCallback: function (settings) {
            var that = this;
            if (settings.bSorted || settings.bFiltered) {
                this.$('td:first-child', {
                    "filter": "applied"
                }).each(function (i) {
                    that.fnUpdate(i + 1, this.parentNode, 0, false, false);
                });
            }

            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var rowsData = api.rows({
                page: 'current'
            }).data();

            var last = null;
            var subTotal = new Array();
            var grandTotal = new Array();
            var groupID = -1;
            var length = api.column(groupCol, {page: 'current' }).data().length

            api.column(groupCol, {
                page: 'current'
            }).data().each(function (group, i) {
                $.each($(rows).eq(i), function (colIndex, colValue) {
                  $(this).find('td').eq(0).html(i+1)
                })     
                var last_ = (last)?last.toLowerCase().trim():last
                var group_ = (group)?group.toLowerCase().trim():group
                if (last_ !== group_) {      
                    groupID++;
                    // if(last!==null)$(rows).eq(i).before("<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + "</td><td class='text-right' style='background-color:#9c27b0;color: #fff;'>Total</td></tr>");                    
                    last = group;                       
                }
                if(i==length-1){
                  // var html = "<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + " Total : </td></tr>"
                  var html = "<tr class='grandTotal'><td colspan='7'>Grand Total : </td></tr>"
                  $(rows).eq(i).after(html);                    
                }

                //Sub-total of each column within the same grouping
                var val = api.row(api.row($(rows).eq(i)).index()).data(); //Current order index              
                $.each(val, function (colIndex, colValue) {
                    if (typeof subTotal[groupID] == 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] == 'undefined') {
                        subTotal[groupID][colIndex] = 0;
                    }
                    if (typeof grandTotal[colIndex] == 'undefined') {
                        grandTotal[colIndex] = 0;
                    }

                    value = colValue ? parseFloat(colValue) : 0;
                    subTotal[groupID][colIndex] += value;
                    grandTotal[colIndex] += value;
                });

                // if(i==length-1)$(rows).eq(i).after("<tr class='groupTR'><td colspan='5' class='groupTitle'>Grand Total</td><td class='text-right' style='background-color:#9c27b0;color: #fff;'></td></tr>");                    
            });      

            $('#iuran-table tbody').find('.grandTotal').each(function (i, v) { 
                var TotalInfo = "";                          
                const return_total = (grandTotal.return_total)?grandTotal.return_total:0
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency((grandTotal.total - return_total).toFixed(2)) + "</td>"; 
                $(this).append(TotalInfo);
            });
                                  
        }
    });
     
  }

  function dtPiutang(){
    var groupCol = 4;
    table6 = $('#piutang-table').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        dom: "B<'clear'>lrtip",
        retrieve: true,
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0,
            className: 'text-center'
          }, 
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {                         
                d.searchAnggota = $('select[name=searchAnggota]').val();
                d.searchGrade = $('select[name=searchGrade]').val();
                d.type= "piutang"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data:null,name:''},
            {data: 'no_anggota', name: 'no_anggota'},  
            {data: 'fullname', name: 'fullname'},                  
            {data: 'grade', name: 'grade'},
            {data: 'nilai_pembayaran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'sisa_pembayaran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'angsuran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'nilai_transport', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'type', name: 'margin' , render:function(data,type,full,meta){
                return data == 0? 'Barang':'Jasa';
              }
            },
            {data: 'remaining_deduction', name: 'remaining_deduction'},
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9],
              total_index: [3,4,5,6]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA PiutangKredit_'+getDate()
          }
        ],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        drawCallback: function (settings) {
            var that = this;
            if (settings.bSorted || settings.bFiltered) {
                this.$('td:first-child', {
                    "filter": "applied"
                }).each(function (i) {
                    that.fnUpdate(i + 1, this.parentNode, 0, false, false);
                });
            }

            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var rowsData = api.rows({
                page: 'current'
            }).data();

            var last = null;
            var subTotal = new Array();
            var grandTotal = new Array();
            var groupID = -1;
            var length = api.column(groupCol, {page: 'current' }).data().length

            api.column(groupCol, {
                page: 'current'
            }).data().each(function (group, i) {
                $.each($(rows).eq(i), function (colIndex, colValue) {
                  $(this).find('td').eq(0).html(i+1)
                })     
            });                                  
        }
    });
     
  }

  function dtPotongan(){
    var groupCol = 2;
    table2 = $('#potongan-table').DataTable({     
        serverSide: true,
        searching: false,
        ordering: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        paging: false,
        autoWidth: false,
        retrieve: true,
        orderFixed: [
          [groupCol, 'asc']
        ],
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0,
            className: 'text-center'
          }, 
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {                         
                d.searchYear = $('select[name=searchYear]').val();
                d.searchMonth = $('select[name=searchMonth]').val();
                d.searchAnggota = $('select[name=searchAnggota]').val();
                d.searchGrade = $('select[name=searchGrade]').val();
                d.type= "potongan"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data:null,name:''},            
            {data: 'no_anggota', name: 'no_anggota'},
            {data: 'fullname', name: 'fullname'},
            {data: 'grade', name: 'grade'},                             
            {data: 'iuran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'piutang', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'tabungan', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'sembako', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'total', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            }
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [ 1, 2, 3, 4, 5, 6, 7, 8],
              total_index: [3,4,5,6,7]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Potongan_'+getDate()
          }
        ],
        drawCallback: function (settings) {
            var that = this;
            if (settings.bSorted || settings.bFiltered) {
                this.$('td:first-child', {
                    "filter": "applied"
                }).each(function (i) {
                    that.fnUpdate(i + 1, this.parentNode, 0, false, false);
                });
            }

            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var rowsData = api.rows({
                page: 'current'
            }).data();

            var last = null;
            var subTotal = new Array();
            var grandTotal = new Array();
            var groupID = -1;
            var length = api.column(groupCol, {page: 'current' }).data().length

            api.column(groupCol, {
                page: 'current'
            }).data().each(function (group, i) {
                $.each($(rows).eq(i), function (colIndex, colValue) {
                  $(this).find('td').eq(0).html(i+1)
                })     
                var last_ = (last)?last.toLowerCase().trim():last
                var group_ = (group)?group.toLowerCase().trim():group
                if (last_ !== group_) {      
                    groupID++;
                    // if(last!==null)$(rows).eq(i).before("<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + "</td><td class='text-right' style='background-color:#9c27b0;color: #fff;'>Total</td></tr>");                    
                    last = group;                       
                }
                if(i==length-1){
                  // var html = "<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + " Total : </td></tr>"
                  var html = "<tr class='grandTotal'><td colspan='4'>Grand Total : </td></tr>"
                  $(rows).eq(i).after(html);                    
                }

                //Sub-total of each column within the same grouping
                var val = api.row(api.row($(rows).eq(i)).index()).data(); //Current order index              
                $.each(val, function (colIndex, colValue) {
                    if (typeof subTotal[groupID] == 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] == 'undefined') {
                        subTotal[groupID][colIndex] = 0;
                    }
                    if (typeof grandTotal[colIndex] == 'undefined') {
                        grandTotal[colIndex] = 0;
                    }

                    value = colValue ? parseFloat(colValue) : 0;
                    subTotal[groupID][colIndex] += value;
                    grandTotal[colIndex] += value;
                });

                // if(i==length-1)$(rows).eq(i).after("<tr class='groupTR'><td colspan='5' class='groupTitle'>Grand Total</td><td class='text-right' style='background-color:#9c27b0;color: #fff;'></td></tr>");                    
            });      

            $('#potongan-table tbody').find('.grandTotal').each(function (i, v) { 
                var TotalInfo = "";                          
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.iuran.toFixed(2)) + "</td>";
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.piutang.toFixed(2)) + "</td>"; 
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.tabungan.toFixed(2)) + "</td>"; 
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.sembako.toFixed(2)) + "</td>";   
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.total.toFixed(2)) + "</td>";  
                $(this).append(TotalInfo);
            });
                                  
        }
    });
  }

  function dtTransaction(){
    var groupCol = 4;
    table3 = $('#transaction-table').DataTable({
        // processing: true,        
        serverSide: true,
        searching: false,
        ordering: false,
        paging: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        autoWidth: false,
        retrieve: true,
        orderFixed: [
          [groupCol, 'asc']
        ],
        columnDefs: [    
          {
            searchable: false,
            orderable: false,
            targets: 0
          },    
          {
            visible: false,
            targets: [3]
          },
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {         
                d.searchYear = $('select[name=searchYear]').val();
                d.searchMonth = $('select[name=searchMonth]').val();
                d.searchDate = $('input[name=searchDate]').val();
                d.searchStartDate = $('input[name=searchStartDate]').val();  
                d.searchEndDate = $('input[name=searchEndDate]').val();
                d.searchCoa = $('select[name=searchCoa]').val();
                d.type= "transaction"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data:null,render:function(data,type,full,meta){
                return data.trans_date+'-'+data.trans_month+'-'+data.trans_year
              }
            },
            {data: 'trans_no',  name: 'trans_no'},  
            {data: 'tans_desc', name: 'tans_desc'},           
            {data: 'coa_code', name: 'coa_code'}, 
            {data: 'groupDate', name: 'groupDate'},           
            {data: 'debit', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                return '0'              
              }
            },
            {data: 'kredit', render:function(data,type,full,meta){                
                if(data)return formatCurrency(data)
                return '0'
              }
            },    
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {             
              grouped_array_index: [4],
              total_index: [5,6],
              isGrand: true,
              total_grand_index: [5,6] 
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Transaction_'+getDate()
          }
        ],
        drawCallback: function (settings) {
            var that = this;
            if (settings.bSorted || settings.bFiltered) {
                this.$('td:first-child', {
                    "filter": "applied"
                }).each(function (i) {
                    that.fnUpdate(i + 1, this.parentNode, 0, false, false);
                });
            }

            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var rowsData = api.rows({
                page: 'current'
            }).data();

            var last = null;
            var subTotal = new Array();
            var grandTotal = new Array();            
            var groupID = -1;
            var length = api.column(groupCol, {page: 'current' }).data().length
            var budget = new Array();
            var updatedBudget = new Array();
            var change = null

            api.column(groupCol, {
                page: 'current'
            }).data().each(function (group, i) {                                                            
                if (last !== group) {      
                    groupID++;        
                    if(last!==null)$(rows).eq(i).before("<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + " Total : </td></tr>");                                                                                  
                    last = group; 
                }
                if(i==length-1){
                  var html = "<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + " Total : </td></tr>"
                  html += "<tr class='grandTotal'><td colspan='4'>Grand Total : </td></tr>"
                  $(rows).eq(i).after(html);                    
                }

                //Sub-total of each column within the same grouping
                var val = api.row(api.row($(rows).eq(i)).index()).data(); //Current order index                
                $.each(val, function (colIndex, colValue) {                  
                    if (typeof subTotal[groupID] == 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] == 'undefined') {
                        subTotal[groupID][colIndex] = 0;
                    }
                    if (typeof grandTotal[colIndex] == 'undefined') {
                        grandTotal[colIndex] = 0;
                    }

                    value = colValue ? parseFloat(colValue) : 0;
                    subTotal[groupID][colIndex] += value;
                    grandTotal[colIndex] += value;
                });
                        
            });                         
       
            $('#transaction-table tbody').find('.groupTR').each(function (i, v) {              
                var rowCount = $(this).nextUntil('.groupTR').length;
                var subTotalInfo = "";                          
                var saldo = subTotal[i].kredit - subTotal[i].debit;
                subTotalInfo += "<td class='groupTD'>" + formatCurrency(subTotal[i].debit.toFixed(2))+ "</td>"; 
                subTotalInfo += "<td class='groupTD'>" + formatCurrency(subTotal[i].kredit.toFixed(2)) + "</td>";
                $(this).append(subTotalInfo);
            });

            $('#transaction-table tbody').find('.grandTotal').each(function (i, v) { 
                var saldo = grandTotal.kredit - grandTotal.debit;
                var TotalInfo = "";                          
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.debit.toFixed(2)) + "</td>"; 
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.kredit.toFixed(2)) + "</td>";
                $(this).append(TotalInfo);
            });
            
        }
    });
  }

  function dtTb(){
    var groupCol = 4;
    table4 = $('#tb-table').DataTable({
        serverSide: true,
        searching: false,
        ordering: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        paging: false,
        autoWidth: false,
        retrieve: true,
        orderFixed: [
          [groupCol, 'asc']
        ],
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0,
            className: 'text-center'
          }, 
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {
                d.type= "tb"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data: 'coa_code', render:function(data,type,full,meta){
                if(data.includes('-J'))return ''
                  else return data
              }, name: 'coa_code'},  
            {data:null,render:function(data,type,full,meta){
                return `<div class="${data.coa_level==1?'font-weight-bold':''}">${data.coa_name}<div>`;                    
              }
            },                  
            {data: 'begining_balance', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'debit', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'kredit', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'ending_balance', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            }
        ],
        buttons: [
          {
            extend: 'excel',
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA TB_'+getDate(),
            exportOptions: {
              total_index: [2,3,4,5]
            },
          }
        ],
        rowCallback: function( row, data ) {
          if ( data.is_sum == "Y" ) {
            $('td:eq(2)', row).addClass("border-sum");
            $('td:eq(5)', row).addClass("border-sum");
          }
        }
    });
     
  }

  function dtNeraca(){
    table9 = $('#neraca-table').DataTable({
        serverSide: true,
        searching: false,
        ordering: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        paging: false,
        autoWidth: false,
        retrieve: true,
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0,
            className: 'text-center'
          }, 
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {
                d.type= "neraca"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data: 'coa_code', render:function(data,type,full,meta){
                if(data.includes('-J'))return ''
                  else return data
              }, name: 'coa_code'},  
            {data:null,render:function(data,type,full,meta){
                return `<div class="${data.coa_level==1?'font-weight-bold':''}">${data.coa_name}<div>`;                    
              }
            },                  
            {data: null, render:function(data,type,full,meta){
                let value = 0;
                let className = "";
                if(data.coa_level > 2 || data.is_sum == 'Y' || (data.coa_level === 2 && data.coa_code.includes("C."))){
                  if(data.ending_balance){
                    value = formatCurrency(data.ending_balance)
                  }else{
                    value= '0';
                  } 
                }else{
                  className = 'd-none'
                }
                return `<span class="${className}">${value}</span>`
              }
            }
        ],
        buttons: [
          {
            extend: 'excel',
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Neraca_'+getDate(),
            exportOptions: {
              total_index: [2]
            },
          }
        ]
    });
     
  }

  function dtLabaRugi(){
    table10 = $('#laba_rugi-table').DataTable({
        serverSide: true,
        searching: false,
        ordering: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        paging: false,
        autoWidth: false,
        retrieve: true,
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0,
            className: 'text-center'
          }, 
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {
                d.type= "laba_rugi"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data: 'coa_code', render:function(data,type,full,meta){
                if(data.includes('-J'))return ''
                  else return data
              }, name: 'coa_code'},  
            {data:null,render:function(data,type,full,meta){
                return `<div class="${data.coa_level==1?'font-weight-bold':''}">${data.coa_name}<div>`;                    
              }
            },                  
            {data: null, render:function(data,type,full,meta){
                if(data.coa_level > 2 || data.is_sum == 'Y'){
                  if(data.ending_balance)return formatCurrency(data.ending_balance)
                  return '0';
                }else{
                  return '';
                }
              }
            }
        ],
        buttons: [
          {
            extend: 'excel',
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Laba-Rugi_'+getDate(),
            exportOptions: {
              total_index: [2]
            },
          }
        ]
    });
     
  }

  function dtShu(){
    var groupCol = 4;
    table5 = $('#shu-table').DataTable({
      serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        paging: false,
        autoWidth: false,
        retrieve: true,
        orderFixed: [
          [groupCol, 'asc']
        ],
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0,
            className: 'text-center'
          }, 
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {                         
                d.searchAnggota = $('select[name=searchAnggota]').val();
                d.type= "shu"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data:null,name:''},
            {data: 'no_anggota', name: 'no_anggota'},  
            {data: 'fullname', name: 'fullname'},                  
            {data: 'shu_year', name: 'shu_year'},
            {data: 'shu_iuran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'shu_murabahah', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'total', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            }
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [ 1, 2, 3, 4, 5, 6],
              total_index: [3,4,5]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA SHUAnggota_'+getDate()
          }
        ],
        drawCallback: function (settings) {
            var that = this;
            if (settings.bSorted || settings.bFiltered) {
                this.$('td:first-child', {
                    "filter": "applied"
                }).each(function (i) {
                    that.fnUpdate(i + 1, this.parentNode, 0, false, false);
                });
            }

            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var rowsData = api.rows({
                page: 'current'
            }).data();

            var last = null;
            var subTotal = new Array();
            var grandTotal = new Array();
            var groupID = -1;
            var length = api.column(groupCol, {page: 'current' }).data().length

            api.column(groupCol, {
                page: 'current'
            }).data().each(function (group, i) {
                $.each($(rows).eq(i), function (colIndex, colValue) {
                  $(this).find('td').eq(0).html(i+1)
                })     
                var last_ = (last)?last.toLowerCase().trim():last
                var group_ = (group)?group.toLowerCase().trim():group
                if (last_ !== group_) {      
                    groupID++;
                    // if(last!==null)$(rows).eq(i).before("<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + "</td><td class='text-right' style='background-color:#9c27b0;color: #fff;'>Total</td></tr>");                    
                    last = group;                       
                }
                if(i==length-1){
                  // var html = "<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + " Total : </td></tr>"
                  var html = "<tr class='grandTotal'><td colspan='4'>Grand Total : </td></tr>"
                  $(rows).eq(i).after(html);                    
                }

                //Sub-total of each column within the same grouping
                var val = api.row(api.row($(rows).eq(i)).index()).data(); //Current order index              
                $.each(val, function (colIndex, colValue) {
                    if (typeof subTotal[groupID] == 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] == 'undefined') {
                        subTotal[groupID][colIndex] = 0;
                    }
                    if (typeof grandTotal[colIndex] == 'undefined') {
                        grandTotal[colIndex] = 0;
                    }

                    value = colValue ? parseFloat(colValue) : 0;
                    subTotal[groupID][colIndex] += value;
                    grandTotal[colIndex] += value;
                });

                // if(i==length-1)$(rows).eq(i).after("<tr class='groupTR'><td colspan='5' class='groupTitle'>Grand Total</td><td class='text-right' style='background-color:#9c27b0;color: #fff;'></td></tr>");                    
            });      

            $('#shu-table tbody').find('.grandTotal').each(function (i, v) { 
                var TotalInfo = "";                          
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.shu_iuran.toFixed(2)) + "</td>"; 
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.shu_murabahah.toFixed(2)) + "</td>"; 
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.total.toFixed(2)) + "</td>"; 
                $(this).append(TotalInfo);
            });
                                  
        }
    });
     
  }

  function dtSales() {
    var groupCol = 3;
    table7 = $('#sales-table').DataTable({
        // processing: true,        
        serverSide: true,
        searching: false,
        ordering: false,
        paging: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        autoWidth: false,
        retrieve: true,
        orderFixed: [
          [groupCol, 'asc']
        ],
        columnDefs: [    
          {
            searchable: false,
            orderable: false,
            targets: 0
          },    
          {
            visible: false,
            targets: [3]
          },
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {         
                d.searchYear = $('select[name=searchYear]').val();
                d.searchMonth = $('select[name=searchMonth]').val();
                d.searchDate = $('input[name=searchDate]').val();
                d.type= "sales"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data: 'sales_date', name: 'sales_date'}, 
            {data: 'sales_no', render:function(data,type,full,meta){ 
                return `<a class="detaillink" onClick="salesDetail('${data}')">${data}</a>`         
              }
            },  
            {data: 'note', name: 'note'},           
            {data: 'groupDate', name: 'groupDate'},           
            {data: 'payment_type', render:function(data,type,full,meta){ 
                const str = data.replace(/_/g, ' ')        
                return str.charAt(0).toUpperCase() + str.slice(1)            
              }
            },
            {data: 'total_cash', render:function(data,type,full,meta){                
                if(data)return formatCurrency(data)
                return '0'
              }
            },
            {data: 'total_piutang', render:function(data,type,full,meta){                
                if(data)return formatCurrency(data)
                return '0'
              }
            },
            {data: 'charge_amount', render:function(data,type,full,meta){                
                if(data)return formatCurrency(data)
                return '0'
              }
            },    
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {             
              grouped_array_index: [3],
              total_index: [5,6,7],
              isGrand: true,
              total_grand_index: [5,6,7] 
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Sales_'+getDate()
          }
        ],
        drawCallback: function (settings) {
            var that = this;
            if (settings.bSorted || settings.bFiltered) {
                this.$('td:first-child', {
                    "filter": "applied"
                }).each(function (i) {
                    that.fnUpdate(i + 1, this.parentNode, 0, false, false);
                });
            }

            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var rowsData = api.rows({
                page: 'current'
            }).data();

            var last = null;
            var subTotal = new Array();
            var grandTotal = new Array();            
            var groupID = -1;
            var length = api.column(groupCol, {page: 'current' }).data().length
            var budget = new Array();
            var updatedBudget = new Array();
            var change = null

            api.column(groupCol, {
                page: 'current'
            }).data().each(function (group, i) {                                                            
                if (last !== group) {      
                    groupID++;        
                    if(last!==null)$(rows).eq(i).before("<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + " Total : </td></tr>");                                                                                  
                    last = group; 
                }
                if(i==length-1){
                  var html = "<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + " Total : </td></tr>"
                  html += "<tr class='grandTotal'><td colspan='4'>Grand Total : </td></tr>"
                  $(rows).eq(i).after(html);                    
                }

                //Sub-total of each column within the same grouping
                var val = api.row(api.row($(rows).eq(i)).index()).data(); //Current order index                
                $.each(val, function (colIndex, colValue) {                  
                    if (typeof subTotal[groupID] == 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] == 'undefined') {
                        subTotal[groupID][colIndex] = 0;
                    }
                    if (typeof grandTotal[colIndex] == 'undefined') {
                        grandTotal[colIndex] = 0;
                    }

                    value = colValue ? parseFloat(colValue) : 0;
                    subTotal[groupID][colIndex] += value;
                    grandTotal[colIndex] += value;
                });
                        
            });                         
       
            $('#sales-table tbody').find('.groupTR').each(function (i, v) { 
                var rowCount = $(this).nextUntil('.groupTR').length;
                var subTotalInfo = "";                          
                subTotalInfo += "<td class='groupTD'>" + formatCurrency(subTotal[i].total_cash.toFixed(2))+ "</td>";
                subTotalInfo += "<td class='groupTD'>" + formatCurrency(subTotal[i].total_piutang.toFixed(2))+ "</td>";
                subTotalInfo += "<td class='groupTD'>" + formatCurrency(subTotal[i].charge_amount.toFixed(2))+ "</td>"; 
                $(this).append(subTotalInfo);
            });

            $('#sales-table tbody').find('.grandTotal').each(function (i, v) { 
                var TotalInfo = "";                        
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.total_cash.toFixed(2)) + "</td>";
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.total_piutang.toFixed(2)) + "</td>";
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.charge_amount.toFixed(2)) + "</td>"; 
                $(this).append(TotalInfo);
            });
            
        }
    });
    $("#sales-table_wrapper .dt-buttons").append('<button class="dt-button" onclick="exportDetail()">Export Detail Data</button>');
  }

  function dtAngsuran(){
    var groupCol = 4;
    table8 = $('#angsuran-table').DataTable({
        serverSide: true,
        searching: false,
        ordering: false,
        paging: false,
        dom: "B<'clear'><'H'r>t<'F'>",
        retrieve: true,
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0,
            className: 'text-center'
          }, 
        ],
        ajax: {
            url: "{{ route('report.list') }}",
            data: function ( d ) {                         
                d.searchAnggota = $('select[name=searchAnggota]').val();
                d.searchYear = $('select[name=searchYear]').val();
                d.searchMonth = $('select[name=searchMonth]').val();
                d.searchKredit = $('select[name=searchKredit]').val();
                d.type= "angsuran"
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data:null,name:''},
            {data: 'no_anggota', name: 'no_anggota'},               
            {data: 'fullname', name: 'fullname'},    
            {data: 'no_murabahah', name: 'no_murabahah'},                 
            {data: 'trans_month', render:function(data,type,full,meta){
                if(data)return month[data-1]
                  return '-'
              }
            },
            {data: 'trans_year', name: 'trans_year'}, 
            {data: 'nilai_awal', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'nilai_total', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: null, render:function(data,type,full,meta){
                // if(data){
                  if(data.type==0) return `${data.margin}%`
                  else return '-';
                // }
              }
            },
            {data: 'nilai_pembayaran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'status', render:function(data,type,full,meta){
                if(data){
                  if(data==1)return "Berjalan"
                    else if(data==2) return 'Selesai';
                }else{
                  return ""
                }
                
              }
            },
            {data: 'amount', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            }        
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ,10, 11],
              total_index: [10]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Angsuran_'+getDate()
          }
        ],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        drawCallback: function (settings) {
            var that = this;
            if (settings.bSorted || settings.bFiltered) {
                this.$('td:first-child', {
                    "filter": "applied"
                }).each(function (i) {
                    that.fnUpdate(i + 1, this.parentNode, 0, false, false);
                });
            }

            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var rowsData = api.rows({
                page: 'current'
            }).data();

            var last = null;
            var subTotal = new Array();
            var grandTotal = new Array();
            var groupID = -1;
            var length = api.column(groupCol, {page: 'current' }).data().length

            api.column(groupCol, {
                page: 'current'
            }).data().each(function (group, i) {
                $.each($(rows).eq(i), function (colIndex, colValue) {
                  $(this).find('td').eq(0).html(i+1)
                })

                var last_ = (last)?last.toLowerCase().trim():last
                var group_ = (group)?group.toLowerCase().trim():group
                if (last_ !== group_) {      
                    groupID++;
                    // if(last!==null)$(rows).eq(i).before("<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + "</td><td class='text-right' style='background-color:#9c27b0;color: #fff;'>Total</td></tr>");                    
                    last = group;                       
                }
                if(i==length-1){
                  // var html = "<tr class='groupTR'><td colspan='4' class='groupTitle'>" + last + " Total : </td></tr>"
                  var html = "<tr class='grandTotal'><td colspan='11'>Grand Total : </td></tr>"
                  $(rows).eq(i).after(html);                    
                }

                //Sub-total of each column within the same grouping
                var val = api.row(api.row($(rows).eq(i)).index()).data(); //Current order index              
                $.each(val, function (colIndex, colValue) {
                    if (typeof subTotal[groupID] == 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] == 'undefined') {
                        subTotal[groupID][colIndex] = 0;
                    }
                    if (typeof grandTotal[colIndex] == 'undefined') {
                        grandTotal[colIndex] = 0;
                    }

                    value = colValue ? parseFloat(colValue) : 0;
                    subTotal[groupID][colIndex] += value;
                    grandTotal[colIndex] += value;
                });
            });
            $('#angsuran-table tbody').find('.grandTotal').each(function (i, v) { 
                var TotalInfo = "";                          
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.amount.toFixed(2)) + "</td>";  
                $(this).append(TotalInfo);
            });                          
        }
    });
     
  }

  function generateArrayOfYears() {
    var max = currentYear
    var min = max - 5

    for (var i = max; i >= min; i--) {
      $('#searchYear').append($('<option>', { 
          value: i,
          text : i 
      }));
      $('#searchUntilYear').append($('<option>', { 
          value: i,
          text : i 
      }));
    }

    $('#searchUntilYear').select2();
    $('#searchUntilYear').val(currentYear).trigger('change');
    $('#searchYear').select2();
    $('#searchYear').val(currentYear).trigger('change');
  }

  function search(){
    if($('select[name=searchType').val()=='iuran'){      
      table1.ajax.reload()    
    }else if($('select[name=searchType').val()=='potongan'){
      table2.ajax.reload()
    }else if($('select[name=searchType').val()=='transaction'){              
      table3.ajax.reload()
    }else if($('select[name=searchType').val()=='neraca'){
      if($('#jurnal_type').val() == 'tb'){
        table4.ajax.reload()
      }else if($('#jurnal_type').val() == 'neraca'){
        table9.ajax.reload()
      }else if($('#jurnal_type').val() == 'laba_rugi'){
        table10.ajax.reload()
      }
    }else if($('select[name=searchType').val()=='sales'){
      table7.ajax.reload()
    }else if($('select[name=searchType').val()=='shu'){
      table5.ajax.reload()
    }else if($('select[name=searchType').val()=='angsuran'){
      if( $('select[name=searchAnggota]').val() != null || role==1){
        table8.ajax.reload()
        $('#angsuran-table_wrapper').show()
      }
    }else{
      table6.ajax.reload()
    }
    showHideTable()
  }

  function reset(){
    $('.searchForm').trigger("reset")
    $('#searchMonth').val('').trigger('change')
    $('#searchYear').val('').trigger('change')
    $('#searchAnggota').val('').trigger('change')
    $('#searchKredit').val('').trigger('change')
    if($('select[name=searchType').val()=='iuran'){  
      $('#searchTypeIuran').val("").trigger('change')
      $('select[name=searchAnggota]').val("").trigger('change')
      $('select[name=searchUntilYear]').val("").trigger('change')
      table1.ajax.reload()
    }else if($('select[name=searchType').val()=='potongan'){
      $('select[name=searchAnggota]').val("").trigger('change')
      $('select[name=searchYear]').val(currentYear).trigger('change')
      $('select[name=searchMonth]').val(currentMonth).trigger('change')
    }
  }

  function formatDate(date){
    var dts = new Date(date);
    var dd = String(dts.getDate()).padStart(2, '0');
    var mm = String(dts.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = dts.getFullYear();

    dts = dd + '-' + mm + '-' + yyyy;
    return dts;
  }

  function showHideTable(){
    if($('select[name=searchType').val()=='iuran'){      
      $('#iuran-table').show()
      $('#iuran-table_wrapper').show()
      $('#potongan-table').hide()
      $('button[aria-controls=potongan-table]').hide()
      $('#transaction-table').hide()
      $('button[aria-controls=transaction-table]').hide()
      hideJurnal()
      $('#piutang-table').hide()
      $('#piutang-table_wrapper').hide()
      $('#shu-table').hide()
      $('#shu-table_wrapper').hide()
      $('#sales-table').hide()
      $('#sales-table_wrapper').hide()
      $('#angsuran-table').hide()
      $('#angsuran-table_wrapper').hide()
    }else if($('select[name=searchType').val()=='potongan'){
      $('#iuran-table').hide()
      $('#iuran-table_wrapper').hide()
      $('#transaction-table').hide()
      $('button[aria-controls=transaction-table]').hide()
      hideJurnal()
      $('#potongan-table').show()
      $('button[aria-controls=potongan-table]').show()
      $('#piutang-table').hide()
      $('#piutang-table_wrapper').hide()
      $('#shu-table').hide()
      $('#shu-table_wrapper').hide()
      $('#sales-table').hide()
      $('#sales-table_wrapper').hide()
      $('#angsuran-table').hide()
      $('#angsuran-table_wrapper').hide()
    }else if($('select[name=searchType').val()=='piutang'){
      $('#iuran-table').hide()
      $('#iuran-table_wrapper').hide()
      $('#transaction-table').hide()
      $('button[aria-controls=transaction-table]').hide()
      hideJurnal()
      $('#piutang-table_wrapper').show()
      $('#piutang-table').show()
      $('button[aria-controls=piutang-table]').show()
      $('#shu-table').hide()
      $('#shu-table_wrapper').hide()
      $('#sales-table').hide()
      $('#sales-table_wrapper').hide()
      $('#angsuran-table').hide()
      $('#angsuran-table_wrapper').hide()
      $('#potongan-table').hide()
      $('button[aria-controls=potongan-table]').hide()
    }else if($('select[name=searchType').val()=='transaction'){
      $('#iuran-table').hide()
      $('#iuran-table_wrapper').hide()
      $('#potongan-table').hide()
      $('button[aria-controls=potongan-table]').hide()
      hideJurnal()
      $('#transaction-table').show()
      $('button[aria-controls=transaction-table]').show()
      $('#piutang-table').hide()
      $('#piutang-table_wrapper').hide()
      $('#shu-table').hide()
      $('#shu-table_wrapper').hide()
      $('#sales-table').hide()
      $('#sales-table_wrapper').hide()
      $('#angsuran-table').hide()
      $('#angsuran-table_wrapper').hide()
    }else if($('select[name=searchType').val()=='shu'){
      $('#iuran-table').hide()
      $('#iuran-table_wrapper').hide()
      $('#potongan-table').hide()
      $('button[aria-controls=potongan-table]').hide()
      hideJurnal()
      $('#transaction-table').hide()
      $('button[aria-controls=transaction-table]').hide()
      $('#piutang-table').hide()
      $('#piutang-table_wrapper').hide()
      $('#shu-table').show()
      $('#shu-table_wrapper').show()
      $('#sales-table').hide()
      $('#sales-table_wrapper').hide()
      $('#angsuran-table').hide()
      $('#angsuran-table_wrapper').hide()
    }else if($('select[name=searchType').val()=='sales'){
      $('#iuran-table').hide()
      $('#iuran-table_wrapper').hide()
      $('#potongan-table').hide()
      $('button[aria-controls=potongan-table]').hide()
      hideJurnal()
      $('#transaction-table').hide()
      $('button[aria-controls=transaction-table]').hide()
      $('#piutang-table').hide()
      $('#piutang-table_wrapper').hide()
      $('#shu-table').hide()
      $('#shu-table_wrapper').hide()
      $('#sales-table').show()
      $('#sales-table_wrapper').show()
      $('#angsuran-table').hide()
      $('#angsuran-table_wrapper').hide()
    }else if($('select[name=searchType').val()=='angsuran'){
      $('#iuran-table').hide()
      $('#iuran-table_wrapper').hide()
      $('#potongan-table').hide()
      $('button[aria-controls=potongan-table]').hide()
      hideJurnal()
      $('#transaction-table').hide()
      $('button[aria-controls=transaction-table]').hide()
      $('#piutang-table').hide()
      $('#piutang-table_wrapper').hide()
      $('#shu-table').hide()
      $('#shu-table_wrapper').hide()
      $('#sales-table').hide()
      $('#sales-table_wrapper').hide()
      $('#angsuran-table').show()
      // $('#angsuran-table_wrapper').show()
    }else if($('select[name=searchType').val()=='tb' || $('select[name=searchType').val()=='neraca' || $('select[name=searchType').val()=='laba_rugi'){
      $('#iuran-table').hide()
      $('#iuran-table_wrapper').hide()
      $('#potongan-table').hide()
      $('button[aria-controls=potongan-table]').hide()
      $('#transaction-table').hide()
      $('button[aria-controls=transaction-table]').hide()
      $('#piutang-table').hide()
      $('#piutang-table_wrapper').hide()
      $('#shu-table').hide()
      $('#shu-table_wrapper').hide()
      $('#sales-table').hide()
      $('#sales-table_wrapper').hide()
      $('#angsuran-table').hide()
      $('#angsuran-table_wrapper').hide()
      if($('select[name=searchType').val() == 'tb'){
        $('#tb-table').show()
        $('#tb-table_wrapper').show()
        $('#neraca-table').hide()
        $('#neraca-table_wrapper').hide()
        $('#laba_rugi-table').hide()
        $('#laba_rugi-table_wrapper').hide()
      }else if($('select[name=searchType').val() == 'neraca'){
        $('#neraca-table').show()
        $('#neraca-table_wrapper').show()
        $('#tb-table').hide()
        $('#tb-table_wrapper').hide()
        $('#laba_rugi-table').hide()
        $('#laba_rugi-table_wrapper').hide()
      }else{
        $('#neraca-table').hide()
        $('#neraca-table_wrapper').hide()
        $('#tb-table').hide()
        $('#tb-table_wrapper').hide()
        $('#laba_rugi-table').show()
        $('#laba_rugi-table_wrapper').show()
      }
    }
  }

  function hideJurnal(){
    $('#tb-table').hide()
    $('#tb-table_wrapper').hide()
    $('#neraca-table').hide()
    $('#neraca-table_wrapper').hide()
    $('#laba_rugi-table').hide()
    $('#laba_rugi-table_wrapper').hide()
  }

  function getKredit(){
    if ($('#searchKredit').hasClass("select2-hidden-accessible")){
      $('#searchKredit').select2("destroy")
      $('#searchKredit').find('option').remove()
    }
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("akad-kredit/dropdown-list")}}',
        type: 'GET',
        data: {status: "ongoing", no_anggota: (role!=1)?$('#searchAnggota').val():"{{Auth::user()->getNoAnggota()}}"},
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            murabahah = data;
            for (let index = 0; index < data.length; index++) {
              $('#searchKredit').append($('<option>', { 
                value: data[index].no_murabahah,
                text : `${data[index].no_murabahah} - ${data[index].fullname}` 
              }));
            }
                   
            $('#searchKredit').select2();
            $('.kredit-sec').show()
 
            resolve()
          }else{
            reject()
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
          reject()
        },
      })
    })
  }

  function getAnggotaDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/anggota/dropdown")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            for (let index = 0; index < data.length; index++) {
              $('#searchAnggota').append($('<option>', { 
                value: data[index].no_anggota,
                text : `${data[index].no_anggota} ${data[index].fullname}` 
              }));
            }
                   
            $('#searchAnggota').select2();
            resolve()
          }else{
            reject()
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
          reject()
        },
      })
    })  
  }

  function getTypeIuran(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("iuran/dropdown-params")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            for (let index = 0; index < data.type.length; index++) {
              if(data.type[index].value != 2){
                $('#searchTypeIuran').append($('<option>', { 
                    value: data.type[index].value,
                    text : data.type[index].label 
                }));
              }
            }
            $('#searchTypeIuran').append($('<option>', { 
                  value: '',
                  text : 'All' 
            }));
            $('#searchTypeIuran').select2();
            $('#searchTypeIuran').val(0).trigger('change');
            resolve()
          }else{
            reject()
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
          reject()
        },
      })
    })  
  }

  function getLastClosing() {
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("finance/get-last-closing")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $('.per-month').text((data)?`Per ${month[parseInt(data.month)-1]}-${data.year}`:'-')
            if(!data){
              $('#tb-sec').hide()
            }else{
              $('#tb-sec').show()
              $('#neraca-sec').hide()
            }
            resolve()
          }else{
            reject()
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
          reject()
        },
      })
    }) 
  }

   function getLastYearClosing() {
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("finance/get-last-year-closing")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $('.per-year').text((data)?data:'-')
            if(!data){
              $('#neraca-sec').hide()
            }else{
              $('#neraca-sec').show()
              $('#tb-sec').hide()
            }
            resolve()
          }else{
            reject()
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
          reject()
        },
      })
    }) 
  }

  function salesDetail(no) {
    $.ajax({
      url : "{{url('sales/get-detail')}}",
      type: 'GET',
      data: {sales_no:no},
      beforeSend: function() {
        showNotification('Loading..','warning',5000)
      },     
      success: function(data) {
        $.notifyClose();
        var jsonResponse = JSON.parse(data);
        var msg = jsonResponse.message
        if(jsonResponse.status){
          $('#sales_no').text('').text(no)
          $('.table-sales-detail > tbody').empty()
          $.each(jsonResponse.data, function(key, val) {
            let tds = ' <td>' + val.item_code + '</td><td>' + val.item_name + '</td><td>' + val.quantity + '</td><td>' 
            + val.satuan + '</td><td>' + formatCurrency(val.harga) + '</td><td>' + formatCurrency(val.total_amount) + '</td><td>' + formatCurrency(val.discount_amount) + '</td>' ;
            $('.table-sales-detail > tbody:last').append('<tr>' + tds + '</tr>');
          });
          $('#salesDetailModal').modal({
            focus: true,    
          })
        }
      },
      error: function(xhr) { // if error occured
        var msg = xhr.responseJSON.message
        showNotification(msg,'danger')
      },
    })
  }

  function exportDetail(){
    const searchYear = ($('select[name=searchYear]').val())?$('select[name=searchYear]').val():undefined;
    const searchMonth = ($('select[name=searchMonth]').val())?$('select[name=searchMonth]').val():undefined;
    const searchDate = ($('input[name=searchDate]').val())?$('input[name=searchDate]').val():undefined;

    let url = new URL('{{url("sales/export-sales-detail")}}');
    if(searchYear)url.searchParams.set('searchYear', searchYear);
    if(searchMonth)url.searchParams.set('searchMonth', searchMonth);
    if(searchDate)url.searchParams.set('searchDate', searchDate);

    window.open(url,"_blank")
  }

  function getCoa(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("coa/dropdown-list")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            listCoa = jsonResponse.data
            for (let index = 0; index < listCoa.length; index++) {
              $('#searchCoa').append($('<option>', { 
                value: listCoa[index].coa_code,
                text : `${listCoa[index].coa_code} - ${listCoa[index].coa_name}` 
              }));
            }
            $('#searchCoa').select2()
            resolve();
          }else{
            reject()
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
          reject()
        },
      })
    }) 
  }
</script>
@endpush