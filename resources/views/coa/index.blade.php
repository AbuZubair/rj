@extends('layouts.admin', ['activePage' => 'coa', 'titlePage' => __('Chart of Accounts(COA)')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('coa/delete')}}" edit-url="{{url('coa/edit')}}" data-modal="coaModal" data-checkbox="coas">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input class="ml-md-0 ml-4" type="checkbox" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>Kode</th>
                    <th>COA</th>                         
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
<div class="modal fade" id="coaModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="coaModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('coa.form')
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
  let listCoa = [];
   $(document).ready(function(){

    getLevelDropdown().then(() => {
      getDropdownList();
    });

    $('#coa_level').on('change', function() {        
      var level = $('#coa_level').val();
      if (level == 1) {
        setTimeout(() => {
          $('#coa_parent').attr('disabled', true);
        });
      }else{
        $('#coa_parent').attr('disabled', false);
        const list = listCoa.filter(item => item.coa_level == level - 1);
        setParentList(list)
      }
    });
       
    const table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        ajax: "{{ route('coa.list') }}",
        columns: [
            {data:null,render:function(data,type,full,meta){
                return '<input class="ml-md-0 ml-4" type="checkbox" name="coas" value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'coa_code', name: 'coa_code'},
            {data:null,render:function(data,type,full,meta){
                return `<div class="${data.coa_level==1?'font-weight-bold':''}" >${data.coa_name}<div>`;                    
              },
              name: 'coa_name'
            },
            {data:null,render:function(data,type,full,meta){
                return '<button class="btn btn-sm btn-success" onClick="edit('+data.id+')"><i class="material-icons">edit</i></button>';                    
              }
            },
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'}
        ],
        order: [],
        dom: '<"toolbar">lfrtip'
    });

    var btn = '<button class="btn btn-primary" onClick="add()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'   
    $("div.toolbar").attr('class','mb-4').html(btn);
        
  });  

  function getLevelDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("coa/dropdown-level")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $('#coa_level').find('option').remove()
                       
            $('#coa_level').append('<option value="" disabled selected>Select your option</option>')
            for (let index = 0; index < data.length; index++) {
              $('#coa_level').append($('<option>', { 
                  value: data[index],
                  text : data[index] 
              }));
            }
                   
            $('#coa_level').select2({
                dropdownParent: $("#coaModal")
            });
            $('#coa_parent').select2({
                dropdownParent: $("#coaModal")
            });
            resolve();
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

  function getDropdownList(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("coa/dropdown-list")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            listCoa = jsonResponse.data
            setParentList(listCoa)
            resolve();
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

  function setParentList(data) {
    $('#coa_parent').find('option').remove()
                       
    $('#coa_parent').append('<option value="" disabled selected>Select your option</option>')
    for (let index = 0; index < data.length; index++) {
      $('#coa_parent').append($('<option>', { 
            value: data[index].coa_code,
            text : `${data[index].coa_code} - ${data[index].coa_name}` 
      }));
    }
    $('#coa_parent').select2({
      dropdownParent: $("#coaModal")
    });
  }
  
</script>
@endpush