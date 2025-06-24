<form method="post" action="{{ route('po.crud',isset($data)?1:0 ) }}" data-redirect="{{ route('po') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Purchase No.') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('transaction_no') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('transaction_no') ? ' is-invalid' : '' }}" name="transaction_no" id="transaction_no" type="text" placeholder="{{ __('Kode') }}" value="{{ old('transaction_no', isset($data) ? $data['transaction_no'] : '') }}" readonly/>
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
                    <td class="col-md-3">
                      <input type="text" class="inputItem" name="inputitem" id="inputitem" placeholder="Input Item">
                    </td>
                    <td class="col-md-1">
                      <input type="text" data-type="currency" class="inputQty" name="inputqty" id="inputqty" placeholder="Input Qty">
                    </td>
                    <td class="col-md-2">
                      <select name="inputsatuan" id="inputsatuan" class="inputSatuan" placeholder="Select Satuan">
                        <option value="" disable>Select Satuan</option>
                      </select>
                    </td>
                    <td class="col-md-2">
                      <input type="text" data-type="currency" class="inputHarga" name="inputharga" id="inputharga" placeholder="Input Harga">
                    </td>
                    <td class="d-none sub-total"></td>
                    <td class="col-md-1 d-flex align-items-center"><span class="d-flex align-items-center add-btn" onClick="addItem()" style="cursor:pointer"><i class="material-icons mr-2">add_circle</i> Insert</span></td>
                    <td class="col-md-2 align-items-center">
                      <div class="form-check">
                          <label class="form-check-label">
                              <input class="form-check-input master_update" type="checkbox" name="is_master_update" value="">
                              Update Master?
                              <span class="form-check-sign" style="top:0 !important">
                                  <span class="check"></span>
                              </span>
                          </label>
                      </div>
                    </td>
                  </tr>
                  <tr class="d-flex">
                    <th class="col-md-1">No.</th>
                    <th class="col-md-3">Item</th>
                    <th class="col-md-1">Qty</th>
                    <th class="col-md-2">Satuan Beli</th>
                    <th class="col-md-2">Harga Satuan</th>
                    <th class="col-md-2">Total</th>
                    <th class="col-md-1"></th>
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
                    <td class="col-md-2">
                      <input type="text" class="satuan" name="satuan-sample" id="satuan">
                    </td>
                    <td class="col-md-2">
                      <input type="text" data-type="currency" class="harga" name="harga-sample" id="harga" readonly>
                    </td>
                    <td class="col-md-1 sub-total"></td>
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

    </div>
    <div class="card-footer ml-auto mr-auto">
      <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
      <button type="submit" class="btn button-link">{{ __('Save') }}</button>
    </div>
  </div>
</form>
