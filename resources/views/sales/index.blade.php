@extends('layouts.admin', ['activePage' => 'sales', 'titlePage' => __('Sales')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">

      <form method="post" action="{{ route('sales.crud',isset($data)?1:0 ) }}" data-redirect="afterSubmit" autocomplete="off" class="form-horizontal form-admin">
        @csrf

        <input type="hidden" name="payment_type">
        <input type="hidden" name="no_anggota">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Sales No.') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('sales_no') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('sales_no') ? ' is-invalid' : '' }}" name="sales_no" id="sales_no" type="text" placeholder="{{ __('Kode') }}" value="{{ old('sales_no', isset($data) ? $data['sales_no'] : '') }}" readonly/>
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Note') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('note') ? ' has-danger' : '' }}">
                <textarea class="form-control{{ $errors->has('note') ? ' is-invalid' : '' }}" name="note" id="note" placeholder="{{ __('') }}" value="{{ old('note', isset($data) ? $data['transDesc'] : '') }}"></textarea>
              </div>
            </div>
          </div>
        </div>   
        
        <div class="row mt-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-primary text-white">List Item</div>
              <div class="card-body p-4 table-responsive">
                <table class="table item-table">
                  <thead>
                    <tr class="d-flex justify-content-between align-items-center border w-100">
                      <td class="d-none col-md-1"><span>#</span></td>
                      <td class="col-md-3" id="item-wrapper">
                        <input type="text" class="inputItem" name="inputitem" id="inputitem" placeholder="Input Item">
                      </td>
                      <td class="col-md-1">
                        <input type="text" data-type="currency" class="inputQty" name="inputqty" id="inputqty" placeholder="Qty">
                      </td>
                      <td class="col-md-1">
                        <input type="text" data-type="currency" class="inputSatuan" name="inputsatuan" id="inputsatuan" placeholder="Satuan" readonly>
                      </td>
                      <td class="col-md-2">
                        <input type="text" data-type="currency" class="inputHarga" name="inputharga" id="inputharga" placeholder="Input Harga" readonly>
                      </td>
                      <td class="col-md-1">
                        <input type="number" id="inputdisc" name="inputdisc" placeholder="Disc(%)">
                      </td>
                      <td class="col-md-2">
                        <input type="text" data-type="currency" id="inputdiscamt" name="inputdiscamt" placeholder="Disc amt">
                      </td>
                      <td class="d-none sub-total"></td>
                      <td class="col-md-2 d-flex align-items-center"><span class="d-flex align-items-center add-btn" onClick="addItem()" style="cursor:pointer"><i class="material-icons mr-2">add_circle</i> Insert</span></td>
                    </tr>
                    <tr class="d-flex">
                      <th class="col-md-1 text-small">No.</th>
                      <th class="col-md-3 text-small">Item</th>
                      <th class="col-md-1 text-small">Qty</th>
                      <th class="col-md-1 text-small">Satuan</th>
                      <th class="col-md-2 text-small">Harga</th>
                      <th class="col-md-1 text-small">Disc</th>
                      <th class="col-md-2 text-small">Total</th>
                      <th class="col-md-1 text-small"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="d-none" data-row="-1">
                      <td class="col-md-1"><span>#</span></td>
                      <td class="col-md-3">
                        <input type="text" class="item" name="item-sample" id="item">
                      </td>
                      <td class="col-md-1">
                        <input type="text" data-type="currency" class="qty" name="qty-sample" id="qty">
                      </td>
                      <td class="col-md-1">
                        <input type="text" class="satuan" name="satuan-sample" id="satuan">
                      </td>
                      <td class="col-md-2">
                        <input type="text" data-type="currency" class="harga" name="harga-sample" id="harga" readonly>
                      </td>
                      <td class="col-md-1">
                        <input type="text" data-type="currency"  class="disc" name="disc-sample" id="disc" readonly>
                      </td>
                      <td class="col-md-1 d-none">
                        <input type="text" class="discamt" name="discamt-sample" id="discamt" readonly>
                      </td>
                      <td class="col-md-2 sub-total"></td>
                      <td class="col-md-1 d-flex align-items-center"><span class="d-flex align-items-center" onClick="removeItem()" style="cursor:pointer"><i class="material-icons mr-2">remove_circle</i></span></td>
                    </tr>
                  </tbody>
                  <tfoot style="display:none">
                    <tr class="d-flex">
                      <td class="col-md-9 text-right"><b>Total: </b></td>
                      <td>
                        <input type="hidden" class="charge_amount" name="charge_amount" id="charge_amount" readonly>
                        <span class="total-items">0</span>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex w-100 justify-content-center">
          <button type="button" onClick="submitForm()" class="btn button-link">{{ __('Bayar') }}</button>
          <button type="button" onClick="updateHutang()" class="btn btn-warning">{{ __('Update Piutang') }}</button>
        </div>
      </form>

      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">      
    <div class="modal-content">
      <div class="modal-body">
        <div class="card mt-0">
          <div class="card-body">
            <div>
              <div class="form-group">
                <label class="col-form-label">{{ __('Tipe Pembayaran') }}</label>
                <div class="form-group{{ $errors->has('payment_type') ? ' has-danger' : '' }}">                      
                  <select class="form-control {{ $errors->has('payment_type') ? ' is-invalid' : '' }}" name="payment_type_input" id="payment_type" required />
                    <option value="" disabled selected>Select your option</option>
                    <option value="cash">Cash</option>
                    <option value="potongan_anggota">Potong Anggota</option>
                    <option value="piutang">Piutang</option>
                  </select>
                </div>
              </div>
              <div class="form-group cash-sec">
                <label class="col-form-label">{{ __('Cash') }}</label>
                <div class="form-group{{ $errors->has('cash') ? ' has-danger' : '' }}">                      
                  <input class="form-control" data-type="currency" name="cash" id="cash" type="text" placeholder="{{ __('Nominal') }}" value="{{ old('cash', isset($data) ? $data['cash'] : '') }}" required="true" aria-required="true"/>
                </div>
              </div>
              <div class="form-group potongan-sec" style="display:none">
                <label class="col-form-label">{{ __('No. Anggota') }}</label>
                <div class="form-group{{ $errors->has('no_anggota') ? ' has-danger' : '' }}">                      
                  <select class="form-control" name="no_anggota_input" id="no_anggota">
                    <option value="" disabled selected>Select your option</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer justify-content-center">
            <button type="button" onClick="bayar()" class="btn button-link">{{ __('Save') }}</button>
            <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="changeModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="iuranModalTitle">Terima Kasih</h5>
      </div>
      <div class="modal-body">
        <div class="card mt-0">
          <div class="card-body">
            <h3 class="change-wording"></h3>
          </div>
          <div class="card-footer justify-content-center">
            <button type="button" onClick="closeChange()" data-dismiss="modal" class="btn button-link">{{ __('Ok') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="hutangModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">      
    <div class="modal-content">
      <div class="modal-body">
        <div class="card mt-0">
          <div class="card-body">
            <div>
              <div class="form-group">
                <label class="col-form-label">{{ __('List Hutang') }}</label>
                <div class="form-group{{ $errors->has('hutang') ? ' has-danger' : '' }}">                      
                  <select class="form-control" name="hutang" id="hutang">
                    <option value="" disabled selected>Select your option</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer justify-content-center">
            <button type="button" onClick="bayarHutang()" class="btn button-link">{{ __('Save') }}</button>
            <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
  let selectedItem='';
  $(document).ready(function(){

    $(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });

    typeahead();
    generateTransNo();

    $('#item-wrapper').on('keyup', function(e) {
        if(e.which == 13) {
          if($('input[name=inputitem]').val() == selectedItem)addItem()
          $(".tt-suggestion:first-child", this).trigger('click');
        }
    });
        
  });  

  function generateTransNo() {
    const d = new Date();
    $('input[name=sales_no]').val(`SALES${d.getFullYear()+("0" + (d.getMonth() + 1)).slice(-2)}${String(Date.now()).slice(-4)}`)
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

    $('.inputItem').focus()
  
    $('.inputItem').bind('typeahead:select typeahead:autocompleted', function(ev, suggestion) {
      selectedItem=suggestion.label
      $('#inputsatuan').val(suggestion.data.satuan_jual)
      $('input[name=inputqty]').val(1)
      $('input[name=inputharga]').val(suggestion.data.harga_jual.toString().replace(".00","").replace(/\B(?=(\d{3})+(?!\d))/g, "."))
    });
   
  }

  function submitForm(){
    if(!$('input[name=charge_amount]').val() || $('input[name=charge_amount]').val() == 0){
      showNotification('Silahkan pilih item','danger');
      return;
    }
    $('#payment_type').select2({
        dropdownParent: $("#paymentModal")
    });
    getAnggota()
    $('#paymentModal').modal({
        focus: true,
        backdrop: 'static'    
    })

    $('#payment_type').on('change', function() {        
      var data = $('#payment_type').val();
      if (data === 'cash') {
        $('.cash-sec').show();
        $('.potongan-sec').hide();
        $('#no_anggota').val('').trigger('change');
      } else if (data === 'potongan_anggota') {
        $('.cash-sec').hide();
        $('.potongan-sec').show();
      } else {
        $('.cash-sec').hide();
        $('.potongan-sec').hide();
      }
    });

    $('#payment_type').val('cash').trigger('change')
  }

  function bayar(){
    if($('#payment_type').val() == 'cash' && !$('input[name=cash]').val()){
      showNotification('Silahkan masukan cash','danger');
      return false;
    }
    if(($('#payment_type').val() == 'cash') && (parseInt($('input[name=cash]').val().replaceAll('.', '')) < $('input[name=charge_amount]').val())){
      showNotification('Cash tidak cukup','danger');
      return false;
    }
    const form = $('.form-admin')
    $('input[name=payment_type]').val($('#payment_type').val())
    if($('#payment_type').val() == 'potongan_anggota'){
      $('input[name=no_anggota]').val($('#no_anggota').val())
    }
    form.submit()
  }

  function getAnggota(){
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
                    text : `${data[index].no_anggota} ${data[index].fullname}`
              }));
            }
                   
            $('#no_anggota').select2({
                dropdownParent: $("#paymentModal")
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

  function afterSubmit(){
    $('#paymentModal').modal('hide')
    let wording;
    if($('#payment_type').val() == 'cash'){
      let change = parseInt($('input[name=cash]').val().replaceAll('.', '')) - $('input[name=charge_amount]').val();
      wording = `Kembalian anda ${change.toString().replace(".00","").replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`
    }else{
      wording = `Pembayaran Berhasil` 
    }
    
    $('.change-wording').text(wording)
    $('#changeModal').modal({
        focus: true,
        backdrop: 'static'    
    })
  }

  function closeChange(){
    location.reload();
    return false;
  }

  function getHutang(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("sales/get-hutang")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data

            for (let index = 0; index < data.length; index++) {
              $('#hutang').append($('<option>', { 
                  value: data[index].id,
                  text : `${data[index].sales_no} / ${data[index].sales_date}`
              }));
            }
                   
            $('#hutang').select2({
                dropdownParent: $("#hutangModal")
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

  function updateHutang() {
    getHutang().then(() => {
      $('#hutangModal').modal({
        focus: true,
        backdrop: 'static'    
      })
    })
  }

  function bayarHutang() {
    if(!$('#hutang').val()){
      showNotification("Silahkan pilih hutang yang akan dibayarkan",'danger');
      return;
    }
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    let params = {
      id: $('#hutang').val()
    }
    $.ajax({
      url : '{{url("sales/bayar-hutang")}}',
      type: 'POST',
      data : params,
      beforeSend: function() {
        showNotification('Loading..','warning',1000)
      },
      success: function(data) {
        $.notifyClose();
        var jsonResponse = JSON.parse(data);
        if(jsonResponse.status){
          showNotification(jsonResponse.message,'success');
          $('#hutangModal').modal('hide')
          $('#hutang').val('').trigger('change')
        }else{
          showNotification(jsonResponse.message, 'danger');
        }
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