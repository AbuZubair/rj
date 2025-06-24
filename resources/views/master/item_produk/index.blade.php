@extends('layouts.admin', ['activePage' => 'produk', 'titlePage' => __('Produk')])

@push('css')
<style>
  .modal-dialog-scrollable{
    max-height: 90vh;
    overflow: scroll;
  }
</style>
@endpush

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('master/produk/delete')}}" edit-url="{{url('master/produk/edit')}}" data-modal="produkModal" data-checkbox="produks">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input class="ml-md-0 ml-4" type="checkbox" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <!-- <th>Grup</th>                     -->
                    <th>Satuan Jual</th>
                    <th>Satuan Beli</th>
                    <th>Harga Jual</th>
                    <th>Harga Beli</th>   
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
<div class="modal fade" id="produkModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="produkModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('master.item_produk.form')
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="importModalLabel">Import File</h5>
          </div>
          <div class="modal-body">
              <div class="card">
                <div class="card-body">
                  <div class="input-group mb-3">
                      <input type="file" name="file" accept=".xlsx" id="produk-files" class="form-control">
                  </div>
                </div>
                <div class="card-footer ml-auto mr-auto">
                  <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
                  <button type="button" class="btn button-link" onClick="uploadProduk()">{{ __('Save') }}</button>
                </div>
              </div>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="importFailModal" tabindex="-1" role="dialog" aria-labelledby="importFailModalLabel" aria-hidden="true">
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
                  <h5 class="mb-0">Berikut list data duplikasi:</h5>
                  <p>Sisa data berhasil disimpan (jika ada)</p>
                </div>
                <div class="card-body">
                  <table class="table table-import-failed table-danger">
                    <thead>
                      <tr>
                        <th>No.</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
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
  var table;
   $(document).ready(function(){

    getDropdown();

    $('input[name=is_active]').change(function() {    
      let that = this; 
      $(that).val($(that).attr('data-value'))
    });
       
    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        ajax: "{{ route('produk.list') }}",
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="produks" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'item_code', name: 'item_code'},
            {data: 'item_name', name: 'item_name'},
            {data: 'satuan_jual_label', name: 'satuan_jual'},
            {data: 'satuan_beli_label', name: 'satuan_beli'},
            // {data: 'grup', name: 'grup'}, 
            {data: 'harga_jual', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '-'
              }
            },
            {data: 'harga_beli', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '-'
              }
            },
            {data: 'is_active',render:function(data,type,full,meta){
                return `<span class="badge badge-${(data=='Y')?'info':'danger'}">${(data=='Y')?'Aktif':'Inactive'}</span>`;                    
              }
            },
            {data:null,render:function(data,type,full,meta){
                return '<button class="btn btn-sm btn-success" onClick="edit('+data.id+')">Edit</button>';                    
              }
            },
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'}
        ],
        order: [],
        dom: '<"toolbar">frtip'
    });

    var btn = '<button class="btn btn-primary" onClick="add()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>' 
    btn += '<a href="export-product" target="_blank" class="btn btn-info">Export Data</a>'
    btn += '<button class="btn btn-success" onClick="openUploadModal()"><i class="material-icons">file_upload</i> Upload Bulk Data</button>'
    $("div.toolbar").html(btn);
        
  });  

  function getDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/produk/dropdown-params")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            for (let index = 0; index < data.item_grup.length; index++) {
              $('#group_item').append($('<option>', { 
                    value: data.item_grup[index].value,
                    text : data.item_grup[index].label 
              }));
            }

            for (let index = 0; index < data.item_satuan.length; index++) {
              $('#satuan_beli').append($('<option>', { 
                    value: data.item_satuan[index].value,
                    text : data.item_satuan[index].label 
              }));
              $('#satuan_jual').append($('<option>', { 
                    value: data.item_satuan[index].value,
                    text : data.item_satuan[index].label 
              }));
            }

            $('#group_item').select2({
                dropdownParent: $("#produkModal")
            });
            $('#satuan_beli').select2({
                dropdownParent: $("#produkModal")
            });
            $('#satuan_jual').select2({
                dropdownParent: $("#produkModal")
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

  function openUploadModal() {
    $('#importModal').modal({
        focus: true,    
    })
  }

  function uploadProduk() {
    var fileExtension = ['xlsx'];
    var file_data = $('#produk-files').prop('files')[0];
    if(!file_data){
      showNotification("Silahkan pilih file",'danger');
      return;
    }
    if ($.inArray($('#produk-files').val().split('.').pop().toLowerCase(), fileExtension) == -1) {
      showNotification("Format file salah",'danger');
      return;
    }
    var form_data = new FormData();                  
    form_data.append('file', file_data);
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    $.ajax({
      url : "{{url('master/product-import')}}",
      type: 'POST',
      data: form_data,
      contentType:false,
      cache:false,
      processData:false,
      beforeSend: function() {
        showNotification('Loading..','warning',5000)
      },
      success: function(data) {
        $.notifyClose();
        var jsonResponse = JSON.parse(data);
        var msg = jsonResponse.message
        if(jsonResponse.status){
          showNotification(msg,'success')
          $('#importModal').modal('hide');
          table.ajax.reload()
          $('#produk-files').val('').trigger('change')
        }else{ 
          showNotification(msg,'danger')
          if(jsonResponse.failure && jsonResponse.failure.length > 0){
            $('.table-import-failed > tbody').empty()
            $.each(jsonResponse.failure, function(key, val) {
              let no = parseInt(key) + 1;
              let tds = ' <td>' + no + '</td><td>' + val.values.item_code + '</td><td>' + val.values.item_name + '</td>';
              $('.table-import-failed > tbody:last').append('<tr>' + tds + '</tr>');
            });
            $('#importModal').modal('hide');
            table.ajax.reload()
            $('#produk-files').val('').trigger('change');
            $('#importFailModal').modal({
              focus: true,    
            })
          }
        }
        getDropdown()
      },
      error: function(xhr) { // if error occured
        $.notifyClose();
        var msg = xhr.responseJSON.message
        showNotification(msg,'danger')
      },
    })
  }

  
</script>
@endpush