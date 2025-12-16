@extends('layouts.admin', ['activePage' => 'report-iuran', 'titlePage' => __('Report Iuran')])


@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">        
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-4 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Type') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchType" id="searchType">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-4 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Jenjang') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchJenjang" id="searchJenjang">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-4 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Kelas') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchTingkat" id="searchTingkat">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-4 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Tahun Ajaran') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchTahunAjaran" id="searchTahunAjaran">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>    
            </div> 
          </form>
          <div class="card-footer" style="justify-content: end !important;">
            <button class="btn btn-success" onClick="search()" >Search</button>
            <button class="btn btn-warning" onClick="reset()" >Clear Search</button>
          </div>           
        </div>  

        <table id="dynamic-table" class="table yajra-datatable">
            <thead>
                <tr>
                  <th>NIS</th>
                  <th>Nama</th>
                  <th>Jenjang</th>
                  <th>Kelas</th>
                  <th>Status UM</th>
                  <th>Uang Masuk</th>
                  <th>Status DU</th>
                  <th>Daftar Ulang</th>                 
                  <th>SPP</th>
                  <th>SPP Terakhir</th>
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
  let th_ajaran;
  $(document).ready(function(){    
    $('#searchTingkat').select2();
    getDropdown().then(() => 
      getTahunAjaran()
    );
  });

  function getDropdown(){
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
              $('#searchType').append($('<option>', { 
                value: data.type[index].value,
                text : data.type[index].label 
              }));
            }
            for (let index = 0; index < data.jenjang.length; index++) {
              $('#searchJenjang').append($('<option>', { 
                value: data.jenjang[index].value,
                text : data.jenjang[index].label 
              }));               
            } 
            for (let index = 0; index < data.th_ajaran.length; index++) {
              $('#searchTahunAjaran').append($('<option>', { 
                value: data.th_ajaran[index].value,
                text : data.th_ajaran[index].label 
              }));
            }                    
            $('#searchType').select2();
            $('#searchJenjang').select2();
            $('#searchTahunAjaran').select2();
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

  function getTahunAjaran(){
    $.ajax({
      url : '{{url("iuran/get-current-tahun-ajaran")}}',
      type: 'GET',
      data: '',
      success: function(data) {
        var jsonResponse = JSON.parse(data);
        if(jsonResponse.status){
          var data = jsonResponse.data;
          th_ajaran = data;
          $('#searchTahunAjaran').val(data).trigger('change');
          dtIuran();
        }else{
          showNotification(jsonResponse.message, 'danger');
        }
      },
      error: function(xhr) { // if error occured
        var msg = xhr.responseJSON.message
        showNotification(msg,'danger')
      },
    })
  }

  function dtIuran() {
    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        ajax: {
            url: "{{ route('report.list', 'iuran') }}",
            data: function ( d ) {                         
                d.date = $('select[name=searchDate]').val();
                d.type = $('select[name=searchType]').val();
                d.jenjang = $('select[name=searchJenjang]').val();
                d.tingkat = $('select[name=searchTingkat]').val();
                d.tahun_ajaran = $('select[name=searchTahunAjaran]').val();
            }, 
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            {data: 'nis', name: 'nis'},
            {data: 'fullname', name: 'fullname'},
            {data: 'jenjang_text', name: 'jenjang_text'},
            {data: 'tingkat_kelas', name: 'tingkat_kelas'},
            {data: 'status_um', render:function(data,type,full,meta){
                // Return green dot if data equal to 1,otherwise red circle dot
                return `<div class="circle circle-${data == 1 ? 'green' : 'red'}"></div>`
              }
            },
            {data: 'um_masuk', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '-'
              }
            },
            {data: 'status_du', render:function(data,type,full,meta){
                // Return green dot if data equal to 1,otherwise red circle dot
                return `<div class="circle circle-${data == 1 ? 'green' : 'red'}"></div>`
              }
            },
            {data: 'du_masuk', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '-'
              }
            },
            {data: 'status_spp', render:function(data,type,full,meta){
                // Return green dot if data equal to 1,otherwise red circle dot
                return `<div class="circle circle-${data == 1 ? 'green' : 'red'}"></div>`
              }
            },{data: 'spp_terakhir', render:function(data,type,full,meta){
                if(data){
                  let myDate = new Date(data); // Creates a Date object for the current date and time

                  let year = myDate.getFullYear();
                  let mth = myDate.getMonth();
                  const month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                  return `${month[mth]} ${year}`
                }
                return '-'
              }
            }
        ],
        columnDefs: [
          {
            targets: [0,2,3,4,5,6], // your case first column
            className: "text-center",
          },
          {
            targets: 1,
            className: "text-left",
          }
        ],
        order: [],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [0,1,2,3,5,7,9]
            },
            title: 'Iuran_'+getDate()
          }
        ],
        dom: 'B<"toolbar">lfrtip'
    });

    $("div.toolbar").addClass('d-flex flex-column flex-md-row justify-content-start mb-4 mt-4').html('<div style="height: 20px"></div>');
    $("div.dt-buttons").addClass('float-right')
  }

  function search(){
    table.ajax.reload()
  }

  function reset(){
    $('.searchForm').trigger("reset")
    $('select[name=searchDate]').val("").trigger('change')
    $('select[name=searchJenjang]').val("").trigger('change')
    $('select[name=searchType]').val("").trigger('change')
    $('select[name=searchTingkat]').val("").trigger('change')
    $('select[name=searchTahunAjaran]').val(th_ajaran).trigger('change')
    table.ajax.reload()
  }
</script>
@endpush