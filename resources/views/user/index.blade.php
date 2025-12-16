@extends('layouts.admin', ['activePage' => 'user', 'titlePage' => __('User')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('user/delete')}}" edit-url="{{url('user/edit')}}" data-modal="userModal" data-checkbox="users">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input class="ml-md-0 ml-4" type="checkbox" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>User</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Email</th>                    
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
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="userModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('user.form')
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
   $(document).ready(function(){

    //getDropdown()

    $('#role').select2({
        dropdownParent: $("#userModal")
    });

    $('#role').on('change', function() {        
      var data = $('#role').val();
      if (data === '1') {
        $('.staff-list').show();
      } else {
        $('.staff-list').hide();
        $('#no_staff').val('').trigger('change');
      }
    });
       
    const table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        ajax: "{{ route('user.list') }}",
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="users" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'username', name: 'username'},
            {data: null, render:function(data,type,full,meta){
                return `${data.first_name} ${data.last_name != null ? data.last_name : ''}`;      
              }
            },
            {data:null,render:function(data,type,full,meta){
                const role = getRole(data.role);
                let html = '';
                for (let index = 0; index < role.length; index++) {
                  html += `<span class="badge badge-${getRandomTemplateColor(role[index])} mr-2">${role[index]}</span>`
                }
                return html;      
              }
            },
            {data: 'email', name: 'email'},
            {data:null,render:function(data,type,full,meta){
                return '<button class="btn btn-sm btn-success" onClick="edit('+data.id+')"><i class="material-icons">edit</i></button>';                    
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
    $("div.toolbar").html(btn);

    table.on( 'error.dt', function ( e, settings, techNote, message ) {
      showNotification(message, 'danger');
    } )
        
  });  

  function getDropdown(status = ''){
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
                dropdownParent: $("#userModal")
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
  
</script>
@endpush