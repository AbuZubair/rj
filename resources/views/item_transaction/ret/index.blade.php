@extends('layouts.admin', ['activePage' => 'purchase_return', 'titlePage' => __('Purchase Order Return')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Purchase No.') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <input type="text" name="searchTransNo" class="form-control" id="searchTransNo">
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Date') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                  <input type="date" class="form-control" id="searchDate" name="searchDate"  placeholder="Date" onkeydown="return false">
                  </div>
                </div>
              </div>
              <!-- <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Status') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control" name="searchStatus" id="searchStatus">
                      <option value="" disabled selected>Select your option</option>
                      <option value="0">Menunggu</option>
                      <option value="1">Diterima Sebagian</option>
                      <option value="3">Selesai</option>
                    </select>
                  </div>
                </div>
              </div>     -->
            </div> 
          </form>
          <div class="card-footer" style="justify-content: end !important;">
            <button class="btn btn-success" onClick="search()" >Search</button>
            <button class="btn btn-warning" onClick="reset()" >Clear Search</button>
          </div>           
        </div>
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('item-transaction/purchase-order-return/delete')}}" edit-url="{{url('item-transaction/purchase-order-return/edit')}}" data-modal="retModal" data-checkbox="rets">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input class="ml-md-0 ml-4" type="checkbox" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>Purchase Order Return No.</th>
                    <th>Date</th>
                    <th>Note</th>
                    <th>Total Amount</th>                    
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
<div class="modal fade modal-transaction modal-full" id="retModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="padding-right:0 !important">
  <div class="modal-dialog">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="retModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('item_transaction.ret.form')
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
    getPORList();
    typeahead();

    // $('#searchStatus').select2()

    $('.modal-transaction').on('hidden.bs.modal', function (e) {
      resetItem()
      $('.reference_string').hide()
      $('.reference').select2({
        dropdownParent: $("#retModal")
      });
      $('.por-btn').attr('disabled',false)
    })

    $("input[name=charge_amount]").change(function(){
      if($(this).val()!='' && $(this).val()!=0)$('.form-admin').find('button[type=submit]').attr('disabled',false)
      else $('.form-admin').find('button[type=submit]').attr('disabled',true)
    });

    $(".reference").change(function(){
      if($(this).val() != null){
        $('.loader').show()
        resetList()
        renderRow($(this).val(),null,'por').then(() => {
          $('.loader').hide()
          $('.list-item').show()
          const countTotal = () => {
            let arrSubtotal = $('.item-table tbody').children().find('.sub-total').toArray()
            arrSubtotal = arrSubtotal.filter(item => $(item).text()!='').map(item => parseInt($(item).text().replaceAll('.', '')))
            total = arrSubtotal.reduce((partialSum, a) => partialSum + a, 0);
            renderTfoot()
          }
          $('.item-table tbody').children().each(function () {
            if($(this).attr('data-row') && $(this).attr('data-row') > -1){
              let idx = $(this).attr('data-row')
              let parent = $(this)
              parent.children().each(function () {
                let el = $(this).children().last();
                if(el.is('input')){
                  if(el.attr('name').includes('harga')){
                    // el.prop('readonly',false)
                    el.on({
                        keyup: function() {
                          let subtotal = ($(this).val() && !isNaN($(this).val()))?parseInt($(this).val().replaceAll('.', '')) * parent.find('input[id=qty'+idx+']').val() : 0;
                          parent.find('.sub-total').text(subtotal.toString().replace(".00","").replace(/\B(?=(\d{3})+(?!\d))/g, "."))
                          countTotal()
                          formatCurrency($(this));
                        },
                    });
                  }
                  if(el.attr('name').includes('konversi')){
                    // el.prop('readonly',false)
                  }
                }
              })
            }
          })
        })
      }
    });
       
    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        ajax: {
            url: "{{ route('ret.list') }}",
            data: function ( d ) {        
                d.transaction_no = $('input[name=searchTransNo]').val();
                d.date = $('input[name=searchDate]').val();
                d.status = $('#searchStatus').val();
            }, 
            type: 'GET',
        },
        columns: [
            {data:null,render:function(data,type,full,meta){
              let disabled = (data.status>0)?'disabled':''
                return '<input class="ml-md-0 ml-4" type="checkbox" name="pors" '+disabled+' value="'+data.id+'" onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'transaction_no', name: 'transaction_no'},
            {data:null,render:function(data,type,full,meta){
                return data.trans_date+'-'+data.trans_month+'-'+data.trans_year
              }
            },
            {data: 'note', name: 'note'}, 
            {data: 'charge_amount', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '-'
              }
            },
            {data: 'status', render:function(data,type,full,meta){
                switch (data) {
                  case 3:
                    return "Selesai"
                    break;
                  default:
                    break;
                }
              }
            },
            {data:null,render:function(data,type,full,meta){
              let disabled = (data.status>0)?'disabled':''
              let html = `<button class="btn btn-sm btn-info" onClick="editItemTransaction('${data.transaction_no}','view')">View</button>`;
              // html += `<button class="btn btn-sm btn-success" ${disabled} onClick="editItemTransaction('${data.transaction_no}')">Edit</button>`;
                return html; 
              }
            },
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'}
        ],
        order: [],
        dom: '<"toolbar">lrtip'
    });

    var btn = '<button class="btn btn-primary" onClick="add();generatePONo()"><i class="material-icons">add</i> Add</button>'
    // btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'    
    $("div.toolbar").attr('class','mt-4 mb-4').html(btn);
        
  });  

  function getPORList(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("item-transaction/purchase-order-receive/dropdown")}}',
        type: 'GET',
        data: {status:[3,4]},
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            for (let index = 0; index < data.length; index++) {
              $('.reference').append($('<option>', { 
                value: data[index].transaction_no,
                text : `${data[index].transaction_no} ${data[index].note?'('+data[index].note+')':''}`
              }));
            }

            $('.reference').select2({
              dropdownParent: $("#retModal")
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

  function getDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("item-transaction/purchase-order-return/dropdown-params")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            for (let index = 0; index < data.item_satuan.length; index++) {
              $('.inputSatuan').append($('<option>', { 
                value: data.item_satuan[index].value,
                text : data.item_satuan[index].label 
              }));
            }

            $('.inputSatuan').select2({
              dropdownParent: $("#retModal")
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

  function generatePONo() {
    const d = new Date();
    $('input[name=transaction_no]').val(`RET${d.getFullYear()+("0" + (d.getMonth() + 1)).slice(-2)}${String(Date.now()).slice(-4)}`)
  }

  function typeahead(){
    const datas = new Bloodhound({
      datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      remote: {
        wildcard: '%QUERY',
        url: '{{url("master/produk/typehead")}}?query=%QUERY',
        transform: response => $.map(response.data, data => ({
          value: data.item_code,
          label: `${data.item_name} (code:${data.item_code})`,
          data: data
        }))
      }
    });

    // Instantiate the Typeahead UI
    $('.inputItem').typeahead({
      minLength: 1,
      highlight: true,
      hint: false      
    }, {
      display: 'label',
      source: datas,
      limit: 10
    });
  
    $('.inputItem').bind('typeahead:select typeahead:autocompleted', function(ev, suggestion) {
      // $('input[name=inputitem]').val(suggestion.value)
      $('#inputsatuan').val(suggestion.data.satuan_beli).trigger('change')
      $('input[name=inputharga]').val(suggestion.data.harga_beli.toString().replace(".00","").replace(/\B(?=(\d{3})+(?!\d))/g, "."))
      $('input[name=inputkonversi]').val(suggestion.data.konversi)
    });
   
  }

  function resetItem(){
    resetList();
    $('.list-item').hide()
    $('.reference').val('').trigger('change')
  }

  function direct(){
    resetItem()
    $('.list-item').show()
    $('.form-admin').find('button[type=submit]').attr('disabled',false)
  }

  function search(){
    table.ajax.reload()
  }

  function reset(){
    $('select[name=searchStatus]').val("").trigger('change')
    $('input[name=searchTransNo]').val('')
    $('input[name=searchDate]').val('')
    table.ajax.reload()
  }
  
</script>
@endpush