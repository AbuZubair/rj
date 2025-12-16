@extends('layouts.admin', ['activePage' => 'report-aruskas', 'titlePage' => __('Report Arus Kas')])

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
            </form>
          </div>
          <div class="card-footer" style="justify-content: end !important;">
            <button class="btn btn-success" onClick="search()" >Search</button>
            <button class="btn btn-warning" onClick="reset()" >Clear Search</button>
          </div>           
        </div>  

        <table id="aruskas-summary-table" class="table table-bordered tableList" style="width:100%;">
          <thead>
            <tr style="background:#d9ead3;">
              <th>NO</th>
              <th>URAIAN</th>
              <th>RINCIAN</th>
              <th>COA CODE</th>
              <th>BEGINNING BALANCE</th>
              <th>ENDING BALANCE</th>
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
  var currentYear = new Date().getFullYear();
  var currentMonth = ("0" + (new Date().getMonth() + 1)).slice(-2);
  let selectedYear = currentYear;
  let selectedMonth = currentMonth;
  $(document).ready(function() {
    let monthInt = parseInt(selectedMonth, 10);
    let yearInt = parseInt(selectedYear, 10);

    let lastMonth = monthInt - 1;
    let lastMonthYear = yearInt;

    if (lastMonth === 0) {
      lastMonth = 12;
      lastMonthYear -= 1;
    }

    // If last month is December, align selected year
    if (lastMonth === 12) {
      selectedYear = lastMonthYear;
    }

    $('#searchMonth').select2();
    $('#searchMonth').val( ("0" + (lastMonth)).slice(-2)).trigger('change')
    generateArrayOfYears();
    getData().then(data => {
      dtTable(data);
    })
  });

  function getData(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : "{{ route('report.list', 'aruskas') }}",
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {                         
          year: $('select[name=searchYear]').val(),
          month: $('select[name=searchMonth]').val()
        },
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            resolve(data)
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

  function dtTable(data){
    let tbody = '';
    let rowNo = 1;

    data.forEach((group, i) => {
      const rincianCount = group.rincian.length;
      let totalBeginingBalanceRincian = 0;
      let totalEndingBalanceRincian = 0;
      group.rincian.forEach((item, j) => {
        tbody += '<tr>';
        if (j === 0) {
          tbody += `<td rowspan="${rincianCount}">${rowNo++}</td>`;
          tbody += `<td rowspan="${rincianCount}">${group.uraian}</td>`;
        }
        tbody += `<td>${item.rincian}</td>`;
        tbody += `<td>${item.coa_code}</td>`;
        tbody += `<td>${item.begining_balance.toLocaleString()}</td>`;
        tbody += `<td>${item.ending_balance.toLocaleString()}</td>`;
        tbody += '</tr>';
        totalBeginingBalanceRincian += item.begining_balance;
        totalEndingBalanceRincian += item.ending_balance;
      });
      tbody += `<tr class='groupTR'>
        <td colspan='4' class='groupTitle'>${group.uraian} Total : </td>
        <td class='groupTD'>${formatCurrency(totalBeginingBalanceRincian.toFixed(2))}</td>
        <td class='groupTD'>${formatCurrency(totalEndingBalanceRincian.toFixed(2))}</td>
      </tr>`
    });

    $('#aruskas-summary-table tbody').html(tbody);

    $('#aruskas-summary-table').DataTable({
      paging: false,
      searching: false,
      ordering: false,
      info: false,
      autoWidth: false,
      dom: 't',
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
    }

    $('#searchYear').select2();
    $('#searchYear').val(selectedYear).trigger('change');
  }
</script>
@endpush