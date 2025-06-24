@extends('layouts.admin', ['activePage' => 'trans', 'titlePage' => __('Transaction')])

@push('css')
<style>
  select[readonly].select2-hidden-accessible + .select2-container {
      pointer-events: none;
      touch-action: none;
  }

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
      background: #eee;
      box-shadow: none;
  }

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
      display: none;
  }
</style>
@endpush

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-2 col-form-label">{{ __('Date') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <input type="date" class="form-control" id="searchDate" name="searchDate">
                  </div>
                </div>
              </div> 
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-2 col-form-label">{{ __('COA') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchCoa" id="searchCoa">
                      <option value="" disabled selected>Select your option</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-2 col-form-label">{{ __('Type') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchDk" id="searchDk">
                      <option value="" disabled selected>Select your option</option>
                      <option value="debit">Debit</option>
                      <option value="kredit">Kredit</option>
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
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('trans/delete')}}" edit-url="{{url('trans/edit')}}" data-modal="transModal" data-checkbox="transs">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input class="ml-md-0 ml-4" type="checkbox" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>Date</th>    
                    <th>ID</th> 
                    <th>Description</th>              
                    <th>COA</th>
                    <th>Debit/Kredit</th>
                    <th>Amount</th>
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
<div class="modal fade ps-child" id="transModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="transModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('trans.form')
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
   var table;
   var murabahah;
   var isEdit = null;
   $(document).ready(function(){  

    $('#trans_type').select2({
      dropdownParent: $("#transModal")
    })

    $('#dk').select2({
      dropdownParent: $("#transModal")
    })

    $('#trans_type').on('change', function() {        
      var data = $('#trans_type').val();
      if (data) {
        $('.main-section').show();
        if(data == 'murabahah'){
          $('.single_coa').show()
          $('.double_coa').hide()
          $('.list-murabahah').show()
          $('.list-anggota').hide()
          $('select[name=dk]').val('kredit').attr({'readonly': 'readonly'}).trigger('change')
          $('select[name=coa_code]').val('A.1.2').attr({'readonly': 'readonly'}).trigger('change')
        }else if(data == 'konsinasi'){
          $('.single_coa').show()
          $('.double_coa').hide()
          $('.list-murabahah').hide()
          $('.list-anggota').hide()
          $('select[name=dk]').val('kredit').attr({'readonly': 'readonly'}).trigger('change')
          $('select[name=coa_code]').val('D.2.3').attr({'readonly': 'readonly'}).trigger('change')
        }else{
          if(!isEdit){
            $('#coa_code_kredit').attr("required", true);
            $('#coa_code_debit').attr("required", true);
            $('#coa_code').attr("required", false);
            $('#dk').attr("required", false);
            $('.double_coa').show()
            $('.single_coa').hide()
          }else{
            $('#coa_code_kredit').attr("required", false);
            $('#coa_code_debit').attr("required", false);
            $('#coa_code').attr("required", true);
            $('#dk').attr("required", true);
            $('.double_coa').hide()
            $('.single_coa').show()
          }
          $('.list-anggota').show()
          $('.list-murabahah').hide()
        }
      } else {
        $('.main-section').hide();
      }
    });

    $('#no_murabahah').on('change', function() {      
      var data = $('#no_murabahah').val();
      if(data){
        let angsuran = murabahah.find(e => e.no_murabahah==data)
        $('input[name=amount]').val(angsuran.angsuran.toString().replace(".00","").replaceAll('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, "."))
      }
    });

    $('#searchDk').select2()

    var config = {
      processing: true,        
      serverSide: true,
      ajax: {
          url: "{{ route('trans.list') }}",
          data: function ( d ) {         
              d.coa_code = $('select[name=searchCoa]').val();
              d.searchDate = $('input[name=searchDate]').val();   
              d.dk = $('select[name=searchDk]').val(); 
          }, 
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      },
      columns: [
          {data:null,render:function(data,type,full,meta){
              return '<input class="ml-md-0 ml-4" type="checkbox" name="transs" value="'+data.id+'" onClick="singleToggle()"/>';                    
            },
            
          },
          {data:null,render:function(data,type,full,meta){
              return data.trans_date+'-'+data.trans_month+'-'+data.trans_year
            }
          },
          {data: 'trans_no',  name: 'trans_no'},
          {data: 'tans_desc', name: 'tans_desc'},
          {data: 'coa_name', name: 'coa_name'},
          {data: 'dk', name: 'dk'},
          {data: 'amount', render:function(data,type,full,meta){
              if(data)return formatCurrency(data)
                return '-'
            }
          },   
          {data:null,render:function(data,type,full,meta){
              let html = `<button class="btn btn-sm btn-info" onClick="editAct('${data.id}','view')">View</button>`;
              html += `<button class="btn btn-sm btn-success" onClick="editAct('${data.id}')">Edit</button>`;
              return html; 
            }
          }            
      ],
      columnDefs: [
        {"targets": 0, "orderable": false, "className": 'text-center'}
      ],
      dom: '<"toolbar">frtip'
    }   
    
    table = $('.yajra-datatable').DataTable(config)
    let timer = null;

    $(".dataTables_filter input")
      .unbind() // Unbind previous default bindings
      .bind("input", function(e) { // Bind our desired behavior
          var value = this.value;

          if(timer != null){
            clearTimeout(timer);
          }
          
          timer = setTimeout(function() {
            table.search(value).draw();
          }, 500);
      });

    var btn = '<button class="btn btn-primary" onClick="addAct()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'    
    $("div.toolbar").attr('class','mt-4 mb-4').html(btn);
        
    getDropdown()
    getAnggotaDropdown()
    getMurabahahDropdown()
    
  });
  
  function addAct() {
    isEdit = false;
    add();
  }

  function editAct(id, mode) {
    isEdit = true;
    $('.double_coa').hide();
    $('.single_coa').show();
    $('#coa_code_kredit').attr("required", false);
    $('#coa_code_debit').attr("required", false);
    edit(id, mode)
  }

  function getDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("coa/dropdown-list")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            listCoa = jsonResponse.data
            for (let index = 0; index < listCoa.length; index++) {
              $('#searchCoa').append($('<option>', { 
                value: listCoa[index].coa_code,
                text : listCoa[index].coa_name 
              }));
              $('#coa_code').append($('<option>', { 
                value: listCoa[index].coa_code,
                text : listCoa[index].coa_name 
              }));
               $('#coa_code_kredit').append($('<option>', { 
                value: listCoa[index].coa_code,
                text : listCoa[index].coa_name 
              }));
               $('#coa_code_debit').append($('<option>', { 
                value: listCoa[index].coa_code,
                text : listCoa[index].coa_name 
              }));
            }
            $('#searchCoa').select2()
            $('#coa_code').select2({
              dropdownParent: $("#transModal")
            })
            $('#coa_code_kredit').select2({
              dropdownParent: $("#transModal")
            })
            $('#coa_code_debit').select2({
              dropdownParent: $("#transModal")
            })
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
            for (let index = 0; index < data.length; index++) {
              $('#no_anggota').append($('<option>', { 
                value: data[index].no_anggota,
                text : data[index].fullname 
              }));
            }
                   
            $('#no_anggota').select2({
                dropdownParent: $("#transModal")
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

  function getMurabahahDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("akad-kredit/dropdown-list")}}',
        type: 'GET',
        data: {status: "ongoing"},
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            murabahah = data;
            for (let index = 0; index < data.length; index++) {
              $('#no_murabahah').append($('<option>', { 
                value: data[index].no_murabahah,
                text : `${data[index].no_murabahah} - ${data[index].fullname}` 
              }));
            }
                   
            $('#no_murabahah').select2({
                dropdownParent: $("#transModal")
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
    $('#searchProject').val("").trigger('change')
    table.ajax.reload()
  }

  function formatting(n) {
    // format number 1000000 to 1,234,567
    return n.replace(".00","").replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
  }


</script>
@endpush