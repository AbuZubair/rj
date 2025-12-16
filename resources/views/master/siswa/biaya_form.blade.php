<form method="post" action="{{ route('kesiswaan.biaya') }}" data-redirect="{{ route('kesiswaan') }}" autocomplete="off" class="form-horizontal form-admin" id="form-trans">
  @csrf
  <input type="hidden" name="nis_biaya" id="nis_biaya">
  <div class="card ">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Uang Masuk*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('uang_masuk') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('uang_masuk') ? ' is-invalid' : '' }}" data-type="currency" name="uang_masuk" id="uang_masuk" type="text" placeholder="{{ __('Uang Masuk') }}" value="{{ old('uang_masuk', isset($data) ? $data['uang_masuk'] : '') }}" required />
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Daftar Ulang*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('daftar_ulang') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('daftar_ulang') ? ' is-invalid' : '' }}" data-type="currency" name="daftar_ulang" id="daftar_ulang" type="text" placeholder="{{ __('Daftar Ulang') }}" value="{{ old('daftar_ulang', isset($data) ? $data['daftar_ulang'] : '') }}" required />
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('SPP*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('spp') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('spp') ? ' is-invalid' : '' }}" data-type="currency" name="spp" id="spp" type="text" placeholder="{{ __('SPP') }}" value="{{ old('spp', isset($data) ? $data['spp'] : '') }}" required />
            </div>
          </div>
        </div> 
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Uang Masuk Terbayar') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('um_masuk') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('um_masuk') ? ' is-invalid' : '' }}" data-type="currency" name="um_masuk" id="um_masuk" type="text" placeholder="{{ __('Uang Masuk Terbayar') }}" value="{{ old('um_masuk', isset($data) ? $data['um_masuk'] : '') }}" required />
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Daftar Ulang Terbayar') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('du_masuk') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('du_masuk') ? ' is-invalid' : '' }}" data-type="currency" name="du_masuk" id="du_masuk" type="text" placeholder="{{ __('Daftar Ulang Terbayar') }}" value="{{ old('du_masuk', isset($data) ? $data['du_masuk'] : '') }}" required />
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