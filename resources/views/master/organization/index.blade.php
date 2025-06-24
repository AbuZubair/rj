@extends('layouts.admin', ['activePage' => 'organization', 'titlePage' => __('Organization')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Level') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchLevel" id="searchLevel">
                      <option value="">All</option>
                      <option value="divisi">Divisi</option>
                      <option value="department">Department</option>
                      <option value="grade">Grade</option>
                    </select>
                  </div>
                </div>
              </div>   
            </div> 
          </form>         
        </div>  
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('master/organisasi/delete')}}" edit-url="{{url('master/organisasi/edit')}}" data-modal="organisasiModal" data-checkbox="organisasis">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input type="checkbox" class="ml-md-0 ml-4" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>Nama</th>
                    <th>Level</th>
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
<div class="modal fade" id="organisasiModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="organisasiModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('master.organization.form')
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
   let tbl;
   $(document).ready(function(){

    $('#searchLevel').select2();

    $('#searchLevel').on('change', function() {
      search();
    });

    $('#param').select2({
        dropdownParent: $("#organisasiModal")
    });

    tbl = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        ajax: {
            url: "{{ route('organisasi.list') }}",
            data: function ( d ) {        
              d.param = $('select[name=searchLevel]').val();
            }, 
            type: 'GET',
        },
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="organisasis" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'label', name: 'label'},
            {data: 'param', name: 'param'},           
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
              columns: [ 1, 2 ]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Organisasi_'+getDate()
          }
        ],
        order: [],
        dom: 'B<"toolbar">lfrtip'
    });

    var btn = '<button class="btn btn-primary" onClick="add()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'    
    $("div.toolbar").html(btn);
    $("div.dt-buttons").addClass('float-right')

    tbl.on( 'error.dt', function ( e, settings, techNote, message ) {
      showNotification(message, 'danger');
    } )
        
  });  


  function search(){
    tbl.ajax.reload()
  }
  
</script>
@endpush