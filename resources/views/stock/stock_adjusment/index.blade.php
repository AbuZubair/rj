@extends('layouts.admin', ['activePage' => 'stock_adjustment', 'titlePage' => __('Stock Adjustment')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div class="card">
          <form class="searchForm">
            <div class="card-body row">
              <div class="col-sm-12 col-md-6 align-items-center">
                <label class="col-sm-12 col-form-label">{{ __('Item / Produk') }}</label>
                <div class="col-sm-12">
                  <div class="form-group">                      
                    <select class="form-control inputItem" name="inputItem" id="inputItem">
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

        <div class="loader mt-4" style="display:none"></div>

        <form method="post" action="{{ route('stock-adjustment.crud',isset($data)?1:0 ) }}" data-redirect="{{ route('stock-adjustment') }}" autocomplete="off" class="form-horizontal form-admin" style="display:none">
          @csrf
          <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
          <div class="card" style="margin-top: 0 !important;">
            <div class="card-body ">

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-sm-6 col-form-label">{{ __('Item Code') }}</label>
                  <div class="col-sm-12">
                    <div class="form-group{{ $errors->has('item_code') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('item_code') ? ' is-invalid' : '' }}" name="item_code" id="item_code" type="text" placeholder="{{ __('Kode') }}" value="{{ old('item_code', isset($data) ? $data['item_code'] : '') }}" readonly/>
                    </div>
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label class="col-sm-6 col-form-label">{{ __('Item Name') }}</label>
                  <div class="col-sm-12">
                    <div class="form-group{{ $errors->has('item_name') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('item_name') ? ' is-invalid' : '' }}" name="item_name" id="item_name" type="text" placeholder="{{ __('Kode') }}" value="{{ old('item_name', isset($data) ? $data['item_name'] : '') }}" readonly/>
                    </div>
                  </div>
                </div>
              </div>   

              <div class="form-row">
                <div class="form-group col-md-4">
                  <label class="col-sm-6 col-form-label">{{ __('Balance') }}</label>
                  <div class="col-sm-12">
                    <div class="form-group{{ $errors->has('balance') ? ' has-danger' : '' }}">            
                      <input type="text" class="form-control{{ $errors->has('balance') ? ' is-invalid' : '' }}" id="balance" name="balance"  placeholder="Balance" readonly>
                    </div>          
                  </div>
                </div>
                <div class="form-group col-md-4">
                  <label class="col-sm-6 col-form-label">{{ __('Adjust') }}</label>
                  <div class="col-sm-12">
                    <div class="form-group{{ $errors->has('adjust') ? ' has-danger' : '' }}">            
                      <input type="text" class="form-control{{ $errors->has('adjust') ? ' is-invalid' : '' }}" id="adjust" name="adjust"  placeholder="Adjust">
                    </div>          
                  </div>
                </div>
                <div class="form-group col-md-4">
                  <label class="col-sm-6 col-form-label">{{ __('Satuan') }}</label>
                  <div class="col-sm-12">
                    <div class="form-group{{ $errors->has('satuan') ? ' has-danger' : '' }}">            
                      <input type="text" class="form-control{{ $errors->has('satuan') ? ' is-invalid' : '' }}" id="satuan" name="satuan"  placeholder="Satuan" readonly>
                    </div>          
                  </div>
                </div>
              </div>

            </div>
            <div class="card-footer ml-auto mr-auto">
              <button type="button" onClick="submitForm()" class="btn button-link">{{ __('Save') }}</button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">

  var list_item;
  $(document).ready(function(){
    getDropdown()
  });  

  function getDropdown(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/produk/dropdown")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            list_item = jsonResponse.data
            for (let index = 0; index < list_item.length; index++) {
              $('.inputItem').append($('<option>', { 
                value: list_item[index].item_code,
                text : list_item[index].item_name 
              }));
            }

            $('.inputItem').select2();
 
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

  function getStock(val){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("stock/stock-adjustment/get-stock")}}',
        type: 'GET',
        data: {item_code: val},
        success: function(data) {
          const jsonResponse = JSON.parse(data);
          resolve(jsonResponse)
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
    if(!$('.inputItem').val()){
      showNotification("Silahkan pilih item", 'danger');
      return 
    }
    $('.form-admin').hide();
    $('.loader').show()
    getStock($('.inputItem').val()).then((res) => {
      if(res.status){
        if(res.data){
          for (const property in res.data) {
            if(!['updated_by','updated_date'].includes(property)){
              $(`input[name=${property}]`).val(res.data[property])
            }
          }
        }else{
          let item = list_item.find(item => item.item_code == $('.inputItem').val());
          $(`input[name=item_code]`).val(item.item_code)
          $(`input[name=item_name]`).val(item.item_name)
          $(`input[name=balance]`).val(0)
          $(`input[name=satuan]`).val(item.satuan_jual)
        }
        
        $('.loader').hide()
        $('.form-admin').show()
        $('input[name=adjust]').focus()
      }else{
        showNotification(res.message, 'danger');
      }
    })
  }

  function reset(){
    $('.inputItem').val('').trigger('change');
    $('.form-admin').trigger("reset");
    $('.form-admin').hide();
  }

  function submitForm(){
    $.confirm({
      title: 'Stock Adjustment sangat tidak direkomendasikan!',
      content: 'Anda yakin??',
      buttons: {
          confirm: function () {
            $('.form-admin').submit()
          },
          cancel: function () {
            return;
          },
      }
    }); 
  }
  
</script>
@endpush