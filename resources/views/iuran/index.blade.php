@extends('layouts.admin', ['activePage' => 'iuran', 'titlePage' => __('Iuran Siswa')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Tanggal Bayar') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <input type="date" class="form-control" id="searchDate" name="searchDate">
                  </div>
                </div>
              </div> 
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
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('iuran/delete')}}" edit-url="{{url('iuran/edit')}}" data-modal="iuranModal" data-checkbox="iurans">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input class="ml-md-0 ml-4" type="checkbox" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Jenjang</th>
                    <th>Kelas</th>
                    <th>Tanggal Bayar</th>
                    <th>Tipe Iuran</th>
                    <!-- <th>Tahun Ajaran</th> -->
                    <th>Nominal</th>
                    <th>Status</th>                   
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="iuranModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="iuranModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('iuran.form')
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header pl-2 pb-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body modal-dialog-scrollable pt-0">
             <div class="modal-container">
                <div class="card">
                  <div class="card-header text-center">
                    <h5 class="mb-0">Iuran detail:</h5>
                  </div>
                  <div class="card-body">
                    <table class="table view-iuran-detail">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Bulan</th>
                          <th>Tahun</th>
                          <th>Amount</th>
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
</div>
@endsection

@push('js')
<script type="text/javascript">
  let table;
  let role = "{{Auth::user()->getRole()}}"
  const month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
   $(document).ready(function(){
       
    $('#nis').select2({
      ajax: {
        url: '{{url("master/kesiswaan/dropdown")}}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term, // search term
          };
        },
        processResults: function (data, params) {
          return {
            results: data.data
          };
        },
        cache: true
      },
      placeholder:"Select your option",
      minimumInputLength: 2,
      templateResult: formatSiswaSelection,
      templateSelection: formatSiswaValueSelection,
      dropdownParent: $(".nis-wrapper")
    }).on('select2:select', function (e) {           
      var data = e.params.data;
      onSiswaSelected(data);
    });

    $('#nis').on('change', function (e) {
      if($(".select2-selection__placeholder").length > 0){
        $(".select2-selection__placeholder").each(function () {
            $(this).text('Select your option');
        });
      }

      var data = $('#nis').val();
      if(data == null || data == ''){
        $('#jenjang').val('');
        $('#tingkat_kelas').val('');
        return;
      }else{
        $('#jenjang').val(data.jenjang);
        $('#tingkat_kelas').val(data.tingkat_kelas);
      }
    });

   
    $('#searchTingkat').select2();
    getDropdown();

    for(var i=0;i<month.length;i++){
      $('#searchMonth').append($('<option>', { 
        value: ('0' + (i+1)).slice(-2),
        text : month[i] 
      }));
      $('#month').append($('<option>', { 
        value: ('0' + (i+1)).slice(-2),
        text : month[i]  
      }));
      $('#searchMonth').select2();
      $('#month').select2({
          dropdownParent: $("#iuranModal")
      });
    }

    $('#searchJenjang').on('change', function() {
      var data = $('#searchJenjang').val();
      if(data != null && data != ''){
        let tingkatOptions = {
          'sd': [1,2,3,4,5,6],
          'smp': [7,8,9],
          'sma': [10,11,12],
          'paud': ['tk_a', 'tk_b'],
        };
        $('#searchTingkat').empty().append('<option value="" disabled selected>Select your option</option>');
        if (tingkatOptions[data]) {
          tingkatOptions[data].forEach(function(val) {
            // Convert to string for consistency
            val = val.toString();
            let label = `Kelas ${val}`;
            if(val.startsWith('tk_')) {
              label = val.toUpperCase().replace('_', ' ');
            }
            $('#searchTingkat').append($('<option>', { value: val, text: label }));
          });
        }
        $('#searchTingkat').trigger('change');
      }      
    });

    $('#type').on('change', function() {
      var data = $('#type').val();
      if(data == null || data == '' || data == 'spp'){
        $('.th_ajaran_form').hide();
        $('#th_ajaran').attr('required', false);
      }else{
        // Show the th_ajaran_form only for non-spp types
        $('.th_ajaran_form').show();
        // Added required attribute for th_ajaran
        $('#th_ajaran').attr('required', true);
      }      
    });

    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        ajax: {
            url: "{{ route('iuran.list') }}",
            data: function ( d ) {        
                d.date = $('select[name=searchDate]').val();
                d.type = $('select[name=searchType]').val();
                d.jenjang = $('select[name=searchJenjang]').val();
                d.tingkat = $('select[name=searchTingkat]').val();
                d.tahun_ajaran = $('select[name=searchTahunAjaran]').val();
            }, 
            type: 'GET',
        },
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="iurans" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'nis', name: 'nis'},
            {data: 'fullname', name: 'fullname'},
            {data: 'jenjang', name: 'jenjang'},
            {data: 'tingkat_kelas', name: 'tingkat_kelas'},
            {data: 'paid_date', name: 'paid_date'},
            {data: 'type_iuran', name: 'type_iuran'},
            // {data: 'th_ajaran', name: 'th_ajaran'},
            {data: 'amount', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '-'
              }
            },
            {data: 'status_iuran', render:function(data,type,full,meta){
              return `<span class="badge badge-${data.includes('Sudah')?'info':'danger'}">${data}</span>`
              }
            },
            {data:null,render:function(data,type,full,meta){
                let html = '<div class="d-flex">'
                html += '<button class="btn btn-sm btn-success" onClick="edit('+data.id+')"><i class="material-icons">edit</i></button>';
                if(data.type == 'spp'){
                  html += '<button class="btn btn-sm btn-info" onClick="view('+data.id+')"><i class="material-icons">visibility</i></button>';
                }
                html += '<button class="btn btn-sm btn-primary" onClick="printReceipt('+data.nis+')"><i class="material-icons">print</i></button>';
                html += '</div>'
                return html;
              }
            },
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'}
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
              columns: [ 0, 1, 2, 3, 4, 5, 6, 7]
            },
            title: 'Iuran_'+getDate()
          }
        ],
        dom: 'B<"toolbar"><"info-thr">lfrtip'
    });

    var btn;
    btn = '<div><button class="btn btn-primary" onClick="add()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button></div>'   
    // btn += '<button class="btn btn-warning" onClick="update()"><i class="material-icons">add_task</i> Update Status</button></div>'
    // btn += '<button class="btn btn-primary" onClick="monthlyPay()"><i class="material-icons">playlist_add_check</i> Potong Bulan <strong>'+month[(new Date).getMonth()]+'</strong></button></div>'        
    $("div.toolbar").addClass('d-flex flex-column flex-md-row justify-content-start mb-4 mt-4').html(btn);
    $("div.dt-buttons").addClass('float-right')
    
  });  

  function onSiswaSelected(siswa) {
    $('#jenjang').val(siswa.jenjang);
    $('#tingkat_kelas').val(siswa.tingkat_kelas);
  }

  function printReceipt(nis){
    window.open('{{url("iuran/print-bukti")}}/'+nis, '_blank');
  }

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
              $('#type').append($('<option>', { 
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
              $('#th_ajaran').append($('<option>', { 
                value: data.th_ajaran[index].value,
                text : data.th_ajaran[index].label 
              }));
            }                    
            $('#searchType').select2();
            $('#searchJenjang').select2();
            $('#searchTahunAjaran').select2();
            $('#type').select2({
                dropdownParent: $(".type-wrapper")
            });
            $('#th_ajaran').select2({
              dropdownParent: $(".th-ajaran-wrapper")
            });
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

  function search(){
    table.ajax.reload()
  }

  function reset(){
    $('.searchForm').trigger("reset")
    $('select[name=searchDate]').val("").trigger('change')
    $('select[name=searchJenjang]').val("").trigger('change')
    $('select[name=searchType]').val("").trigger('change')
    $('select[name=searchTingkat]').val("").trigger('change')
    $('select[name=searchTahunAjaran]').val("").trigger('change')
    table.ajax.reload()
  }

  function view(id){
    $.ajax({
        url : '{{url("iuran/detail")}}/'+id,
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $(".view-iuran-detail > tbody").empty();
            $.each(data, function (key, val) {
              let no = parseInt(key) + 1;
              let tds = ' <td>' + no + '</td><td>' + month[parseInt(val.month)-1] + '</td><td>' + val.year + '</td><td>' + formatCurrency(val.amount) + '</td>';
              $('.view-iuran-detail > tbody:last').append('<tr>' + tds + '</tr>');
            });
            $('#viewModal').modal('show');
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
  
</script>
@endpush