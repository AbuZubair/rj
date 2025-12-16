<form method="post" action="{{ route('organisasi') }}" data-redirect="{{ route('organisasi') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Nama*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('label') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('label') ? ' is-invalid' : '' }}" name="label" id="label" type="text" placeholder="{{ __('Nama') }}" value="{{ old('label', isset($data) ? $data['label'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Parameter*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('param') ? ' has-danger' : '' }}">                      
              <select class="form-control {{ $errors->has('param') ? ' is-invalid' : '' }}" name="param" id="param" placeholder="{{ __('Level') }}" value="{{ old('param', isset($data) ? $data['param'] : '') }}" required>
                <option value="" disabled selected>Select your option</option>
                <option value="jabatan">Jabatan</option>
                <option value="jenis_ptk">Jenis PTK</option>
                <option value="unit_mengajar">Unit Mengajar</option>
                <option value="jenjang">Jenjang</option>          
              </select>
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
