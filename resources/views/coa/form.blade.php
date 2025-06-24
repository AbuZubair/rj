<form method="post" action="{{ route('coa.crud',isset($data)?1:0 ) }}" data-redirect="{{ route('coa') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Kode*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('coa_code') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('coa_code') ? ' is-invalid' : '' }}" name="coa_code" id="coa_code" type="text" placeholder="{{ __('Nama Lengkap') }}" value="{{ old('coa_code', isset($data) ? $data['coa_code'] : '') }}" required="true" aria-required="true" @if(isset($data)) disabled @endif/>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('COA*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('coa_name') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('coa_name') ? ' is-invalid' : '' }}" name="coa_name" id="coa_name" type="text" placeholder="{{ __('Nama Lengkap') }}" value="{{ old('coa_name', isset($data) ? $data['coa_name'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>        
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Level*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('coa_level') ? ' has-danger' : '' }}">                      
              <select class="form-control {{ $errors->has('coa_level') ? ' is-invalid' : '' }}" name="coa_level" id="coa_level" required />
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Parent*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('coa_parent') ? ' has-danger' : '' }}">
              <select class="form-control {{ $errors->has('coa_parent') ? ' is-invalid' : '' }}" name="coa_parent" id="coa_parent">
                <option value="" disabled selected>Select your option</option>
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
