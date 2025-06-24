<form method="post" action="{{ route('iuran.crud',isset($data)?1:0 ) }}" data-redirect="{{ route('iuran') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Anggota*') }}</label>
          <div class="col-sm-12">
            <div class="form-group">
              <select class="form-control" name="no_anggota" id="no_anggota" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Type*') }}</label>
          <div class="col-sm-12">
            <div class="form-group">                      
              <select class="form-control" name="type" id="type" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>        
      </div>

      <div class="form-group iuran-list" style="display: none;">
        <label class="col-sm-6 col-form-label">{{ __('Referensi Iuran*') }}</label>
        <div class="col-sm-12">
          <div class="row align-items-center">
            <div class="col-sm-12">
              <select class="form-control" name="reference" id="reference">
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Bulan*') }}</label>
          <div class="col-sm-12">
            <div class="form-group">                      
              <select class="form-control" name="month" id="month" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Tahun*') }}</label>
          <div class="col-sm-12">
            <div class="form-group">                      
              <select class="form-control" name="year" id="year" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>        
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Nominal*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('amount') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" data-type="currency" name="amount" id="amount" type="text" placeholder="{{ __('Nominal') }}" value="{{ old('amount', isset($data) ? $data['amount'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>

        <!-- <div class="form-group col-md-6">
          <label class="col-sm-12 col-form-label">{{ __('Sudah dibayarkan?*') }}</label>
          <div class="col-sm-8">
            <div class="form-check form-check-radio form-check-inline">
              <label class="form-check-label">
                <input class="form-check-input" type="radio" name="status" id="status1" value="0" data-value="0" {{ (!isset($data))? "checked" : "" }} > {{ __('Belum') }}
                <span class="circle">
                    <span class="check"></span>
                </span>
              </label>
            </div>
            <div class="form-check form-check-radio form-check-inline">
              <label class="form-check-label">
                <input class="form-check-input" type="radio" name="status" id="status2" value="1" data-value="1" > {{ __('Sudah') }}
                <span class="circle">
                    <span class="check"></span>
                </span>
              </label>
            </div>
          </div>
        </div> -->
      </div>
                             

    </div>
    <div class="card-footer ml-auto mr-auto">
      <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
      <button type="submit" class="btn button-link">{{ __('Save') }}</button>
    </div>
  </div>
</form>
