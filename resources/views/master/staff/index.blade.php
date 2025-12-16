@extends('layouts.admin', ['activePage' => 'staff', 'titlePage' => __('Staff')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Status') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchStatus" id="searchStatus">
                      <option value="Y">Aktif</option>
                      <option value="N">Non Aktif</option>
                    </select>
                  </div>
                </div>
              </div>   
            </div> 
          </form>
          <div class="card-footer" style="justify-content: end !important;">
            <button class="btn btn-success" onClick="search()">Search</button>
            <button class="btn btn-warning" onClick="reset()">Clear Search</button>
          </div>           
        </div>  
        <table id="dynamic-table" class="table yajra-datatable w-100" delete-url="{{url('master/staff/delete')}}" edit-url="{{url('master/staff/edit')}}" data-modal="staffModal" data-checkbox="staffs">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input type="checkbox" class="ml-md-0 ml-4" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>NIP</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Tempat Tanggal Lahir</th>
                    <th>Pendidikan Terakhir</th>
                    <th>Nama Sekolah</th>
                    <th>Jurusan</th>
                    <th>Mulai Bekerja</th>
                    <th>Jabatan</th>
                    <th>Jenis PTK</th>   
                    <th>Unit Mengajar</th>
                    <th>Agama</th>
                    <th>Alamat Jalan</th>
                    <th>Desa/Kelurahan</th>
                    <th>Kecamatan</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>SK Pengangkatan</th>
                    <th>TMT Pengangkatan</th>
                    <th>Lembaga Pengangkatan</th>
                    <th>Nama Ibu Kandung</th>
                    <th>Status Perkawinan</th>
                    <th>Pekerjaan Suami/Istri</th>
                    <th>Keahlian</th>
                    <th>NPWP</th>
                    <th>Nama Wajib Pajak</th>
                    <th>Kewarganegaraan</th>
                    <th>Bank</th>
                    <th>Nomor Rekening Bank</th>
                    <th>Rekening Atas Nama</th>
                    <th>NIK</th>
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

<div class="modal fade" id="staffModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="staffModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body modal-dialog-scrollable">
         @include('master.staff.form')
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
                      <input type="file" name="file" accept=".xlsx" id="item-files" class="form-control">
                  </div>
                </div>
                <div class="card-footer ml-auto mr-auto">
                  <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
                  <button type="button" class="btn button-link" onClick="uploadStaff()">{{ __('Save') }}</button>
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
             <div class="modal-container">
                <div class="card">
                  <div class="card-header text-center">
                    <h5 class="mb-0">Berikut list data bermasalah:</h5>
                    <p>Sisa data berhasil disimpan (jika ada)</p>
                  </div>
                  <div class="card-body">
                    <table class="table table-import-failed table-danger">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Nip</th>
                          <th>Nama</th>
                          <th>Error</th>
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
   $(document).ready(function(){

    getDropdown();

    $('#searchStatus').select2();

    $('#jk').select2({
      dropdownParent: $(".jk-container")
    });
    $('#agama').select2({
      dropdownParent: $(".agama-container")
    });
    $('#status_perkawinan').select2({
      dropdownParent: $(".status_perkawinan-container")
    });

    $('input[name=is_active]').change(function() {    
      let that = this; 
      $(that).val($(that).attr('data-value'))
    });
       
    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        ajax: {
            url: "{{ route('staff.list') }}",
            data: function ( d ) {        
                d.searchStatus = $('select[name=searchStatus]').val();
            }, 
            type: 'GET',
        },
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="staffs" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'nip', name: 'nip'},
            {data: 'fullname', name: 'fullname'},
            {data:null,name:'jk',render:function(data,type,full,meta){
                return `<span>${(data.jk=='L')?'Laki-laki':'Perempuan'}</span>`;                    
              }
            }, 
            {data:null,render:function(data,type,full,meta){
                return `<span>${data.tempat_lahir}, ${formatYYYYMMDDtoDDMMYYYY(data.tanggal_lahir)}</span>`;                    
              }
            },
            {data: 'pendidikan_terakhir', name: 'pendidikan_terakhir'},
            {data: 'instansi_terakhir', name: 'instansi_terakhir'},
            {data: 'jurusan', name: 'jurusan'},
            {data:null,name:'join_date',render:function(data,type,full,meta){
                return `<span>${formatYYYYMMDDtoDDMMYYYY(data.join_date)}</span>`;                    
              }
            },
            {data: 'jabatan_text', name: 'jabatan_text'},
            {data: 'jenis_ptk_text', name: 'jenis_ptk_text'},
            {data: 'unit_mengajar_text', name: 'unit_mengajar_text'},
            {data: 'agama', name: 'agama'},
            {data: 'alamat', name: 'alamat'},
            {data: 'kelurahan', name: 'kelurahan'},
            {data: 'kecamatan', name: 'kecamatan'},
            {data: 'phone', name: 'phone'},
            {data: 'email', name: 'email'},
            {data: 'sk_pengangkatan', name: 'sk_pengangkatan'},
            {data:null,name:'tmt_pengangkatan',render:function(data,type,full,meta){
                return `<span>${formatYYYYMMDDtoDDMMYYYY(data.tmt_pengangkatan)}</span>`;                    
              }
            },
            {data: 'lembaga_pengangkatan', name: 'lembaga_pengangkatan'},
            {data: 'nama_ibu_kandung', name: 'nama_ibu_kandung'},
            {data: 'status_perkawinan', name: 'status_perkawinan'},
            {data: 'pekerjaan_pasangan', name: 'pekerjaan_pasangan'},
            {data: 'keahlian', name: 'keahlian'},
            {data: 'npwp', name: 'npwp'},
            {data: 'nama_wajib_pajak', name: 'nama_wajib_pajak'},
            {data: 'kewarganegaraan', name: 'kewarganegaraan'},
            {data: 'bank', name: 'bank'},
            {data: 'no_rek', name: 'no_rek'},
            {data: 'an_rek', name: 'an_rek'},
            {data: 'nik', name: 'nik'},
            {data:null,name:'is_active',render:function(data,type,full,meta){
                return `<span class="badge badge-${(data.is_active=='Y')?'info':'danger'}">${(data.is_active=='Y')?'Aktif':'Inactive'}</span>`;                    
              }
            },            
            {data:null,render:function(data,type,full,meta){
                return '<button class="btn btn-sm btn-success" onClick="edit('+data.id+')"><i class="material-icons">edit</i></button>';                    
              }
            },
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'},
          {
            visible: false,
            targets: [4,5,6,7,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31]
          }
        ],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        order: [],
        dom: '<"toolbar">frtip'
    });

    var btn = '<button class="btn btn-primary" onClick="add()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'
    btn += '<a href="export-staff" target="_blank" class="btn btn-info"><i class="material-icons">download</i> Export Data</a>'
    btn += '<button class="btn btn-success" onClick="openUploadModal()"><i class="material-icons">file_upload</i> Upload Data</button>'
    $("div.toolbar").html(btn);
    $("div.dt-buttons").addClass('float-right')

    table.on( 'error.dt', function ( e, settings, techNote, message ) {
      showNotification(message, 'danger');
    } )
        
  });  


  function getDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/staff/dropdown-params")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $('#jabatan').find('option').remove()
            $('#jenis_ptk').find('option').remove()
           
            $('#jabatan').append('<option value="" disabled selected>Select your option</option>')
            $('#jenis_ptk').append('<option value="" disabled selected>Select your option</option>')
            for (let index = 0; index < data.jabatan.length; index++) {
              $('#jabatan').append($('<option>', { 
                    value: data.jabatan[index].value,
                    text : data.jabatan[index].label 
              }));
            }

            for (let index = 0; index < data.jenis_ptk.length; index++) {
              $('#jenis_ptk').append($('<option>', { 
                    value: data.jenis_ptk[index].value,
                    text : data.jenis_ptk[index].label 
              }));
            }

            for (let index = 0; index < data.unit_mengajar.length; index++) {
              $('#unit_mengajar').append($('<option>', { 
                    value: data.unit_mengajar[index].value,
                    text : data.unit_mengajar[index].label 
              }));
            }
                   
            $('#jabatan').select2({
                dropdownParent: $(".jabatan-container")
            });
            $('#jenis_ptk').select2({
                dropdownParent: $(".jenis_ptk-container")
            });
            $('#unit_mengajar').select2({
                dropdownParent: $(".unit_mengajar-container")
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
    $('select[name=searchStatus]').val("").trigger('change')
    table.ajax.reload()
  }

  function viewOptional(e) {
    e.preventDefault();
    $(".optional-section").removeClass("d-none");
    $(".show-optional").hide();
    $(".hide-optional").show();
  }

  function hideOptional(e) {
    e.preventDefault();
    $(".optional-section").addClass("d-none");
    $(".show-optional").show();
    $(".hide-optional").hide();
  }

  function uploadStaff(){
    uploadItem("{{url('master/staff-import')}}", function(response){
      let msg = response.message;
      if (response.status) {
          showNotification(msg, "success");
          $("#importModal").modal("hide");
          table.ajax.reload();
          $("#item-files").val("").trigger("change");
      } else {
          showNotification(msg, "danger");
          if (response.failure && response.failure.length > 0) {
            $(".table-import-failed > tbody").empty();
            $.each(response.failure, function (key, val) {
              let no = parseInt(key) + 1;
              let tds = ' <td>' + no + '</td><td>' + val.values.nip + '</td><td>' + val.values.fullname + '</td><td>' + val.errors.join(", ") + '</td>';
              $('.table-import-failed > tbody:last').append('<tr>' + tds + '</tr>');
            });
            $("#importModal").modal("hide");
            table.ajax.reload();
            $("#item-files").val("").trigger("change");
            $("#importFailModal").modal({
                focus: true,
            });
          }
      }
    });
  }
  
</script>
@endpush