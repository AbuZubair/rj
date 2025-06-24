@extends('layouts.admin', ['activePage' => 'iuran', 'titlePage' => __('Iuran Anggota')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Bulan') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchMonth" id="searchMonth">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Tahun') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchYear" id="searchYear">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Type') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchType" id="searchType">
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
                    <th>No Anggota</th>
                    <th>Nama</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Tipe Iuran</th>
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
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">      
    <div class="modal-content">
      <div class="modal-body">
        <div class="card">
          <div class="card-body">
            <p>{{ __('Set Status') }}</p>
            <div>
              <button type="button" class="btn btn-info" onClick="updateStatus(1)">{{ __('Bayar') }}</button>
              <button type="button" class="btn btn-danger" onClick="updateStatus(0)">{{ __('Belum Bayar') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="thrModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">      
    <div class="modal-content">
      <div class="modal-body">
        <div class="card">
          <div class="card-header">
            {{ __('Set Tabungan Hari Raya') }}
          </div>
          <div class="card-body">
            <!-- <p>{{ __('Set Tabungan Hari Raya') }}</p> -->
            <div>
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label class="col-sm-6 col-form-label">{{ __('Nominal*') }}</label>
                  <div class="col-sm-12">
                    <div class="form-group{{ $errors->has('thr') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('thr') ? ' is-invalid' : '' }}" data-type="currency" name="thr" id="thr" type="text" placeholder="{{ __('Nominal') }}" value="{{ old('thr', isset($data) ? $data['thr'] : '') }}" />
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-12">
                  <label class="col-sm-12 col-form-label">{{ __('Rutin Bulanan?*') }}</label>
                  <div class="col-sm-8">
                    <div class="form-check form-check-radio form-check-inline">
                      <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="is_thr_monthly" id="is_thr_monthly1" value="N" data-value="N" checked> {{ __('N') }}
                        <span class="circle">
                            <span class="check"></span>
                        </span>
                      </label>
                    </div>
                    <div class="form-check form-check-radio form-check-inline">
                      <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="is_thr_monthly" id="is_thr_monthly2" value="Y" data-value="Y" > {{ __('Y') }}
                        <span class="circle">
                            <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer ml-auto mr-auto">
            <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
            <button type="button" class="btn button-link" onClick="setThr()">{{ __('Save') }}</button>
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
       
    getYearList();
    getDropdown();
    getAnggotaDropdown();
    // getIuranList('0,1');
    if(role==1)checkThr()

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

    $('#type').on('change', function() {        
      var data = $('#type').val();
      if (data=='2') {
        if($('#no_anggota').val() == null){
          showNotification("Silahkan pilih anggota terlebih dahulu", 'danger');
        }
        $('.iuran-list').show();
      } else {
        $('.iuran-list').hide();
        $('#reference').val('').trigger('change');
      }
    });

    $('#reference').on('change', function() {
      var data = $('#reference').val();
      if(data!=null && !$("#id").val()){
        getAmount(data)
      }      
    });

    $('input[name=is_thr_monthly]').change(function() {    
      let that = this; 
      $(that).val($(that).attr('data-value'))
    });

    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        ajax: {
            url: "{{ route('iuran.list') }}",
            data: function ( d ) {        
                d.month = $('select[name=searchMonth]').val();
                d.year = $('select[name=searchYear]').val();
                d.type = $('select[name=searchType]').val();
            }, 
            type: 'GET',
        },
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="iurans" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'no_anggota', name: 'no_anggota'},
            {data: 'fullname', name: 'fullname'},
            {data: 'month', render:function(data,type,full,meta){
                return month[parseInt(data)-1]
              }
            },
            {data: 'year', name: 'year'},
            {data: 'type_iuran', name: 'type_iuran'},
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
                return (role!=1)?'<button class="btn btn-sm btn-success" onClick="edit('+data.id+')">Edit</button>':'-';    
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
              columns: [ 1, 2, 3, 4, 5, 6],
              total_index: [5]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Iuran_'+getDate()
          }
        ],
        dom: 'B<"toolbar"><"info-thr">lfrtip'
    });

    var btn;
    if(role!=1){
      btn = '<div><button class="btn btn-primary" onClick="add()"><i class="material-icons">add</i> Add</button>'
      btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'   
      // btn += '<button class="btn btn-warning" onClick="update()"><i class="material-icons">add_task</i> Update Status</button></div>'
      // btn += '<button class="btn btn-primary" onClick="monthlyPay()"><i class="material-icons">playlist_add_check</i> Potong Bulan <strong>'+month[(new Date).getMonth()]+'</strong></button></div>'        
      $("div.toolbar").addClass('d-flex flex-column flex-md-row justify-content-start mb-4 mt-4').html(btn);
      $("div.dt-buttons").addClass('float-right')
    }else{
      btn = '<div><button class="btn btn-primary" onClick="addThr()">Tabungan Hari Raya</button>'
      $("div.toolbar").addClass('d-flex flex-column flex-md-row justify-content-between mt-4').html(btn);
      $("div.info-thr").addClass('mb-4 thin-text');
      $("div.dt-buttons").addClass('d-none');
    }
    
  });  

  function getYearList() {
    //get list year from 2016 until current year plus one
    var year = new Date().getFullYear();
    var yearList = [];
    for(var i=2016;i<=year;i++){
      $('#searchYear').append($('<option>', { 
        value: i,
        text : i 
      }));
      $('#year').append($('<option>', { 
        value: i,
        text : i 
      }));
      $('#searchYear').select2();
      $('#year').select2({
          dropdownParent: $("#iuranModal")
      });
    }
 
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
              if(['0','1','3','4'].includes(data.type[index].value)){
                $('#reference').append($('<option>', { 
                    value: data.type[index].value,
                    text : data.type[index].label
                }));
              }
            }                   
            $('#searchType').select2();
            $('#type').select2({
                dropdownParent: $("#iuranModal")
            });
            $('#reference').select2({
                dropdownParent: $("#iuranModal")
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
            $('#no_anggota').find('option').remove()
           
            $('#no_anggota').append('<option value="" disabled selected>Select your option</option>')
            for (let index = 0; index < data.length; index++) {
              $('#no_anggota').append($('<option>', { 
                    value: data[index].no_anggota,
                    text : `${data[index].no_anggota} ${data[index].fullname}` 
              }));
            }
                   
            $('#no_anggota').select2({
                dropdownParent: $("#iuranModal")
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

  function getIuranList(type = ''){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("iuran/dropdown-list")}}',
        type: 'GET',
        data: {type},
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            for (let index = 0; index < data.length; index++) {
              $('#reference').append($('<option>', { 
                    value: data[index].id,
                    text : `${data[index].no_anggota} - ${data[index].fullname} ( Iuran ${data[index].type_iuran}, ${(data[index].month)?data[index].month + ' / ':''} ${data[index].year} )`
              }));
            }
                   
            $('#reference').select2({
                dropdownParent: $("#iuranModal")
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
    $('select[name=searchMonth]').val("").trigger('change')
    $('select[name=searchYear]').val("").trigger('change')
    $('select[name=searchType]').val("").trigger('change')
    table.ajax.reload()
  }

  function update() {
    let arr = []
    let rowcollection =  table.$("input[type=checkbox]", {"page": "all"});
    rowcollection.each(function(index,elem){      
      if($(elem).prop("checked")){
        let checkbox_value = $(elem).val();
        arr.push(checkbox_value)
      }        
    });
    if(arr.length == 0){
      showNotification('Silahkan pilih salah satu', 'danger');
    }else{
      $('#updateModal').modal({
        focus: true,    
      })
    }
  }

  function monthlyPay() {
    doUpdate(1)
  }

  function updateStatus(status) {
    let arr = []
    let rowcollection =  table.$("input[type=checkbox]", {"page": "all"});
    rowcollection.each(function(index,elem){      
      if($(elem).prop("checked")){
        let checkbox_value = $(elem).val();
        arr.push(checkbox_value)
      }        
    });
    if(arr.length == 0){
      showNotification('Silahkan pilih salah satu', 'danger');
    }else{
      doUpdate(status,arr)
    }
  }

  function doUpdate(status,data='') {
    $.confirm({
      title: 'Confirmation!',
      content: 'Anda yakin??',
      buttons: {
          confirm: function () {
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            }); 
            $.ajax({
              url : '{{url("iuran/update-status")}}',
              type: 'POST',
              data : {data: data, status: status},
              beforeSend: function() {
                showNotification('Loading..','warning',1000)
              },
              success: function(data) {
                var jsonResponse = JSON.parse(data);
                if(jsonResponse.status === 200){
                  $.notifyClose();
                  showNotification(jsonResponse.message,'success');
                  table.rows().invalidate('data').draw(false);
                  $('#updateModal').modal('hide')
                }else{
                  showNotification(jsonResponse.message, 'danger');
                }
              },
              error: function(xhr) { // if error occured
                var msg = xhr.responseJSON.message
                showNotification(msg,'danger')
              },
            })
          },
          cancel: function () {
            return;
          },
      }
    });
  }

  function addThr() {
    $('#thrModal').modal({
        focus: true,    
    })
  }

  function checkThr() {
    $.ajax({
      url : '{{url("iuran/get-thr")}}',
      type: 'GET',
      success: function(data) {
        var jsonResponse = JSON.parse(data);
        if(jsonResponse.status){
          if(jsonResponse.data.thr){
            $('#thr').val(formatting(jsonResponse.data.thr))
            if(jsonResponse.data.is_thr_monthly == "Y"){
              $('#is_thr_monthly2').prop('checked',true);
            }
            $("div.info-thr").html(`Potongan berikutnya untuk tabungan hari raya: Rp ${formatCurrency(jsonResponse.data.thr)}`);
          }
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

  function setThr() {
    if(!$('input[name=thr]').val()){
      showNotification("Silahkan isi nominal", 'danger');
      return false;
    }

    let data = {
      thr: $('input[name=thr]').val(),
      is_thr_monthly: $('input[name=is_thr_monthly]:checked').val()
    }

    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    $.ajax({
      url : '{{url("iuran/set-thr")}}',
      type: 'POST',
      data : {data: data},
      beforeSend: function() {
        showNotification('Loading..','warning',1000)
      },
      success: function(data) {
        var jsonResponse = JSON.parse(data);
        if(jsonResponse.status){
          $.notifyClose();
          showNotification(jsonResponse.message,'success');
          $('#thrModal').modal('hide');
          $("div.info-thr").html(`Potongan berikutnya untuk tabungan hari raya: Rp ${$('input[name=thr]').val()}`);
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

  function getAmount(data) {
    $.ajax({
        url : '{{url("iuran/amount-iuran")}}',
        type: 'GET',
        data: {type: data, no_anggota: $('#no_anggota').val()},
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $('#amount').val(formatting(data))

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