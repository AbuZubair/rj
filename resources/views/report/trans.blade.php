@extends('layouts.admin', ['activePage' => 'report-trans', 'titlePage' => __('Report Transaction')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">        
        <div class="card">
          <div class="card-body">
            <form class="searchForm">
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
              </div>
              <div class="row">
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
          <div class="card-footer" style="justify-content: end !important;">
            <button class="btn btn-success" onClick="search()" >Search</button>
            <button class="btn btn-warning" onClick="reset()" >Clear Search</button>
          </div>           
        </div>  

        <table id="transaction-table" class="table yajra-datatable tableList" style="width:100%;">
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
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
  var role = "{{Auth::user()->getRole()}}";
  let table;
  var currentYear = new Date().getFullYear()
  var currentMonth = ("0" + ((new Date()).getMonth() + 1)).slice(-2)
   const month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
  $(document).ready(function(){
    $('#searchMonth').select2();
    $('#searchMonth').val(currentMonth).trigger('change')
    generateArrayOfYears();
    getCoa();
    dtTrans();
  });

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

  function getCoa(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("coa/dropdown-list")}}',
        type: 'GET',
        data: {
          minlevel: 3
        },
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

  function dtTrans() {
    const groupCol = 4;
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
            url: "{{ route('report.list', 'trans') }}",
            data: function ( d ) {         
                d.searchYear = $('select[name=searchYear]').val();
                d.searchMonth = $('select[name=searchMonth]').val();
                d.searchDate = $('input[name=searchDate]').val();
                d.searchStartDate = $('input[name=searchStartDate]').val();  
                d.searchEndDate = $('input[name=searchEndDate]').val();
                d.searchCoa = $('select[name=searchCoa]').val();
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
            }   
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

    $("div.toolbar").addClass('d-flex flex-column flex-md-row justify-content-start mb-4 mt-4').html('<div style="height: 20px"></div>');
    $("div.dt-buttons").addClass('float-right')
  }

  function search(){
    table.ajax.reload()
  }

  function reset(){
    $('.searchForm').trigger("reset")
    $('#searchMonth').val('').trigger('change')
    $('#searchYear').val('').trigger('change')
    $('select[name=searchYear]').val(currentYear).trigger('change')
    $('select[name=searchMonth]').val(currentMonth).trigger('change')
    table.ajax.reload()
  }
</script>
@endpush