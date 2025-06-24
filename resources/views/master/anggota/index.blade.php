@extends('layouts.admin', ['activePage' => 'anggota', 'titlePage' => __('Anggota')])

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
            <button class="btn btn-success" onClick="search()" >Search</button>
            <button class="btn btn-warning" onClick="reset()" >Clear Search</button>
          </div>           
        </div>  
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('master/anggota/delete')}}" edit-url="{{url('master/anggota/edit')}}" data-modal="anggotaModal" data-checkbox="anggotas">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input type="checkbox" class="ml-md-0 ml-4" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>No. Anggota</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>                     
                    <th>Department</th>
                    <th>Divisi</th>   
                    <th>Grade</th>
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
<div class="modal fade" id="anggotaModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="anggotaModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('master.anggota.form')
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

    $('input[name=is_active]').change(function() {    
      let that = this; 
      $(that).val($(that).attr('data-value'))
    });
       
    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        ajax: {
            url: "{{ route('anggota.list') }}",
            data: function ( d ) {        
                d.searchStatus = $('select[name=searchStatus]').val();
            }, 
            type: 'GET',
        },
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="anggotas" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'no_anggota', name: 'no_anggota'},
            {data: 'fullname', name: 'fullname'},
            {data: 'email', name: 'email'},
            {data: 'department_text', name: 'department_text'},
            {data: 'divisi_text', name: 'divisi_text'},
            {data: 'grade_text', name: 'grade_text'},
            {data:null,name:'is_active',render:function(data,type,full,meta){
                return `<span class="badge badge-${(data.is_active=='Y')?'info':'danger'}">${(data.is_active=='Y')?'Aktif':'Inactive'}</span>`;                    
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
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [ 1, 2, 3, 4, 5, 6, 7]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Anggota_'+getDate()
          }
        ],
        order: [],
        dom: 'B<"toolbar">lfrtip'
    });

    var btn = '<button class="btn btn-primary" onClick="add();setLimit()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'    
    $("div.toolbar").html(btn);
    $("div.dt-buttons").addClass('float-right')

    table.on( 'error.dt', function ( e, settings, techNote, message ) {
      showNotification(message, 'danger');
    } )
        
  });  

  function setLimit(){
    let val = formatCurrency('10000000')
    $('#limit_kredit').val(val)
  }

  function getDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/anggota/dropdown-params")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $('#divisi').find('option').remove()
            $('#department').find('option').remove()
           
            $('#divisi').append('<option value="" disabled selected>Select your option</option>')
            $('#department').append('<option value="" disabled selected>Select your option</option>')
            for (let index = 0; index < data.divisi.length; index++) {
              $('#divisi').append($('<option>', { 
                    value: data.divisi[index].value,
                    text : data.divisi[index].label 
              }));
            }

            for (let index = 0; index < data.department.length; index++) {
              $('#department').append($('<option>', { 
                    value: data.department[index].value,
                    text : data.department[index].label 
              }));
            }

            for (let index = 0; index < data.grade.length; index++) {
              $('#grade').append($('<option>', { 
                    value: data.grade[index].value,
                    text : data.grade[index].label 
              }));
            }
                   
            $('#divisi').select2({
                dropdownParent: $("#anggotaModal")
            });
            $('#department').select2({
                dropdownParent: $("#anggotaModal")
            });
            $('#grade').select2({
                dropdownParent: $("#anggotaModal")
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
  
</script>
@endpush