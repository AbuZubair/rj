@extends('layouts.admin', ['activePage' => 'kesiswaan', 'titlePage' => __('Kesiswaan')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Jenjang') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchJenjang" id="searchJenjang">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Tingkat') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchTingkat" id="searchTingkat">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>               
            </div>
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
        <table id="dynamic-table" class="table yajra-datatable w-100" delete-url="{{url('master/kesiswaan/delete')}}" edit-url="{{url('master/kesiswaan/edit')}}" data-modal="siswaModal" data-checkbox="siswas">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input type="checkbox" class="ml-md-0 ml-4" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Jenjang</th>
                    <th>Tingkat / Kelas</th>
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

<div class="modal fade" id="siswaModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="siswaModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body modal-dialog-scrollable">
         @include('master.siswa.form')
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="biayaModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="biayaModalTitle">Biaya</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body modal-dialog-scrollable">
         @include('master.siswa.biaya_form')
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
                  <button type="button" class="btn button-link" onClick="uploadsiswa()">{{ __('Save') }}</button>
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
                          <th>NIS</th>
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

    // Generate tahun masuk options
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 10; // Adjust the range as needed
    for (let year = startYear; year <= currentYear; year++) {
      $('#tahun_masuk').append($('<option>', { 
        value: year,
        text : year 
      }));
    }
    $('#tahun_masuk').select2();


    $('#searchStatus').select2();
    $('#searchTingkat').select2();

    $('#jenis_kelamin').select2({
      dropdownParent: $(".jenis_kelamin-container")
    });

    $('#penghasilan_orangtua').select2({
      dropdownParent: $(".penghasilan-orangtua-container")
    });

    $('input[name=is_active]').change(function() {
      let that = this;
      $(that).val($(that).attr('data-value'))
    });

    $('#searchJenjang').on('change', function() {        
      var data = $('#searchJenjang').val();
      if (data.toLowerCase() === 'sd') {
        $('#searchTingkat').html(`
          <option value="" disabled selected>Select your option</option>
          <option value="1">Kelas 1</option>
          <option value="2">Kelas 2</option>
          <option value="3">Kelas 3</option>
          <option value="4">Kelas 4</option>
          <option value="5">Kelas 5</option>
          <option value="6">Kelas 6</option>
        `);
      } else if (data.toLowerCase().includes('smp')) {
        $('#searchTingkat').html(`
          <option value="" disabled selected>Select your option</option>
          <option value="7">Kelas 7</option>
          <option value="8">Kelas 8</option>
          <option value="9">Kelas 9</option>
        `);
      } else if (data.toLowerCase().includes('sma')) {
        $('#searchTingkat').html(`
          <option value="" disabled selected>Select your option</option>
          <option value="10">Kelas 10</option>
          <option value="11">Kelas 11</option>
          <option value="12">Kelas 12</option>
        `);
      } else {
        $('#searchTingkat').html(`
          <option value="" disabled selected>Select your option</option>
          <option value="tk-a">TK A</option>
          <option value="tk-b">TK B</option>
        `);
      }
       $('#searchTingkat').select2();
    });

    $('#jenjang').on('change', function() {        
      var data = $('#jenjang').val();
      if(data){
        if (data.toLowerCase() === 'sd') {
          $('#tingkat_kelas').html(`
            <option value="" disabled selected>Select your option</option>
            <option value="1">Kelas 1</option>
            <option value="2">Kelas 2</option>
            <option value="3">Kelas 3</option>
            <option value="4">Kelas 4</option>
            <option value="5">Kelas 5</option>
            <option value="6">Kelas 6</option>
          `);
        } else if (data.toLowerCase().includes('smp')) {
          $('#tingkat_kelas').html(`
            <option value="" disabled selected>Select your option</option>
            <option value="7">Kelas 7</option>
            <option value="8">Kelas 8</option>
            <option value="9">Kelas 9</option>
          `);
        } else if (data.toLowerCase().includes('sma')) {
          $('#tingkat_kelas').html(`
            <option value="" disabled selected>Select your option</option>
            <option value="10">Kelas 10</option>
            <option value="11">Kelas 11</option>
            <option value="12">Kelas 12</option>
          `);
        } else {
          $('#tingkat_kelas').html(`
            <option value="" disabled selected>Select your option</option>
            <option value="tk-a">TK A</option>
            <option value="tk-b">TK B</option>
          `);
        }
      }
      $('#tingkat_kelas').select2();
    });
       
    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        ajax: {
            url: "{{ route('kesiswaan.list') }}",
            data: function ( d ) {        
              d.searchStatus = $('select[name=searchStatus]').val();
              d.searchTingkat = $('select[name=searchTingkat]').val();
              d.searchJenjang = $('select[name=searchJenjang]').val();
            }, 
            type: 'GET',
        },
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="siswas" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'nis', name: 'nis'},
            {data: 'fullname', name: 'fullname'},
            {data:null,name:'jenis_kelamin',render:function(data,type,full,meta){
                return `<span>${(data.jenis_kelamin=='L')?'Laki-laki':'Perempuan'}</span>`;                    
              }
            }, 
            {data: 'jenjang_text', name: 'jenjang_text'},
            {data: 'tingkat_kelas', name: 'tingkat_kelas'},
            {data:null,name:'is_active',render:function(data,type,full,meta){
                return `<span class="badge badge-${(data.is_active=='Y')?'info':'danger'}">${(data.is_active=='Y')?'Aktif':'Inactive'}</span>`;                    
              }
            },            
            {data:null,render:function(data,type,full,meta){
                let html = `<button class="btn btn-sm btn-success" onClick="edit(${data.id})"><i class="material-icons">edit</i></button>`;
                if(data.is_active === 'Y'){
                  html += `<button class="btn btn-sm btn-info" onClick="openBiaya('${data.nis}')"><i class="material-icons mr-1">money</i>Biaya</button>`;
                }                
                return html;                
              }
            },
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'}
        ],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        order: [],
        dom: '<"toolbar">frtip'
    });

    var btn = '<button class="btn btn-primary" onClick="addSiswa()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'
    btn += '<a href="export-kesiswaan" target="_blank" class="btn btn-info"><i class="material-icons">download</i>Export Data</a>'
    btn += '<button class="btn btn-success" onClick="openUploadModal()"><i class="material-icons">file_upload</i> Upload Data</button>'
    $("div.toolbar").html(btn);
    $("div.dt-buttons").addClass('float-right')

    table.on( 'error.dt', function ( e, settings, techNote, message ) {
      showNotification(message, 'danger');
    } )
        
  });

  function addSiswa(){
    add();
    const el = $('input[name=nis]')
    el.val(`Auto Genereated`);
    el.prop('readonly', true);
  }


  function getDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/kesiswaan/dropdown-params")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $('#searchJenjang').find('option').remove()
            $('#jenjang').find('option').remove()
           
            $('#jenjang').append('<option value="" disabled selected>Select your option</option>')
            $('#searchJenjang').append('<option value="" disabled selected>Select your option</option>')
            for (let index = 0; index < data.jenjang.length; index++) {
              $('#jenjang').append($('<option>', { 
                value: data.jenjang[index].value,
                text : data.jenjang[index].label 
              }));
              $('#searchJenjang').append($('<option>', { 
                value: data.jenjang[index].value,
                text : data.jenjang[index].label 
              }));
            }

            $('#jenjang').select2({
              dropdownParent: $(".jenjang-container")
            });

            $('#searchJenjang').select2();

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

  function uploadsiswa(){
    uploadItem("{{url('master/kesiswaan-import')}}", function(response){
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
              let tds = ' <td>' + no + '</td><td>' + val.values.nis + '</td><td>' + val.values.fullname + '</td><td>' + val.errors.join(", ") + '</td>';
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

  function openBiaya(nis) {
    $.ajax({
      url: '{{ url("master/kesiswaan/biaya") }}/' + encodeURIComponent(nis),
      type: 'GET',
      data: null,
      success: function(data) {
        // Handle success
        var jsonResponse = JSON.parse(data);
        if (jsonResponse.status) {
          if (jsonResponse.data) {
            const { uang_masuk, daftar_ulang, spp, um_masuk, du_masuk } = jsonResponse.data;
            $('#uang_masuk').val(formatting(uang_masuk) ?? '');
            $('#daftar_ulang').val(formatting(daftar_ulang) ?? '');
            $('#spp').val(formatting(spp) ?? '');
            $('#um_masuk').val(formatting(um_masuk) ?? '');
            $('#du_masuk').val(formatting(du_masuk) ?? '');
            $('#nis_biaya').val(nis);
            $('#biayaModal').modal('show');
          }
        }
      },
      error: function(xhr) {
        // Handle error
        var msg = xhr.responseJSON.message;
        showNotification(msg, "danger");
      }
    });
  }
  
</script>
@endpush