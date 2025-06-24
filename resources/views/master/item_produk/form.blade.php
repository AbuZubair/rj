<form method="post" action="{{ route('produk.crud',isset($data)?1:0 ) }}" data-redirect="{{ route('produk') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Kode') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('item_code') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('item_code') ? ' is-invalid' : '' }}" name="item_code" id="item_code" type="text" placeholder="{{ __('Kode') }}" value="{{ old('item_code', isset($data) ? $data['item_code'] : '') }}"/>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Nama Produk*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('item_name') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('item_name') ? ' is-invalid' : '' }}" name="item_name" id="item_name" type="text" placeholder="{{ __('Nama Produk') }}" value="{{ old('item_name', isset($data) ? $data['item_name'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-9 col-form-label">{{ __('Harga Beli*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('harga_beli') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('harga_beli') ? ' is-invalid' : '' }}" data-type="currency" name="harga_beli" id="harga_beli" type="text" placeholder="{{ __('Nominal') }}" value="{{ old('harga_beli', isset($data) ? $data['harga_beli'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="col-sm-9 col-form-label">{{ __('Harga Jual*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('harga_jual') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('harga_jual') ? ' is-invalid' : '' }}" data-type="currency" name="harga_jual" id="harga_jual" type="text" placeholder="{{ __('Nominal') }}" value="{{ old('harga_jual', isset($data) ? $data['harga_jual'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>           
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Satuan Beli*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('satuan_beli') ? ' has-danger' : '' }}">
              <select class="form-control" name="satuan_beli" id="satuan_beli" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Satuan Jual*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('satuan_jual') ? ' has-danger' : '' }}">
              <select class="form-control" name="satuan_jual" id="satuan_jual" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>        
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-9 col-form-label">{{ __('Konversi*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('konversi') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('konversi') ? ' is-invalid' : '' }}"  name="konversi" id="konversi" type="number" placeholder="{{ __('Konversi Satuan') }}" value="1" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
        <!-- <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Grup*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('group_item') ? ' has-danger' : '' }}">
              <select class="form-control" name="group_item" id="group_item" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div> -->
        <div class="form-group col-md-6">
          <label class="col-sm-9 col-form-label">{{ __('HPP*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('hpp') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('hpp') ? ' is-invalid' : '' }}" data-type="currency" name="hpp" id="hpp" type="text" placeholder="{{ __('Nominal') }}" value="{{ old('hpp', isset($data) ? $data['hpp'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
      </div>


      <div class="form-row">
        <div class="form-group col-md-6 row mt-4">
          <label class="col-sm-9 col-form-label">{{ __('Aktif?*') }}</label>
          <div class="col-sm-6">
            <div class="form-check form-check-radio form-check-inline">
              <label class="form-check-label">
                <input class="form-check-input" type="radio" name="is_active" id="status1" value="Y" data-value="Y" {{ (!isset($data))? "checked" : "" }} > Y
                <span class="circle">
                    <span class="check"></span>
                </span>
              </label>
            </div>
            <div class="form-check form-check-radio form-check-inline">
              <label class="form-check-label">
                <input class="form-check-input" type="radio" name="is_active" id="status2" value="N" data-value="N" > N
                <span class="circle">
                    <span class="check"></span>
                </span>
              </label>
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
