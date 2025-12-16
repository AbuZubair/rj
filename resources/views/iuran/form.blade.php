<form method="post" action="{{ route('iuran.crud')}}" data-redirect="{{ route('iuran') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-12 col-form-label">{{ __('Tanggal Bayar*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('paid_date') ? ' has-danger' : '' }}">            
              <input type="date" class="form-control{{ $errors->has('paid_date') ? ' is-invalid' : '' }}" id="paid_date" name="paid_date"  placeholder="Date" onkeydown="return false" required>
            </div>          
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Type*') }}</label>
          <div class="col-sm-12">
            <div class="form-group {{ $errors->has('type') ? ' has-danger' : '' }} type-wrapper">                      
              <select class="form-control" name="type" id="type" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>        
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Siswa*') }}</label>
          <div class="col-sm-12">
            <div class="form-group {{ $errors->has('nis') ? ' has-danger' : '' }} nis-wrapper">
              <select class="form-control ajax-remote" data-name="siswa_fullname" name="nis" id="nis" required>
              </select>
            </div>
          </div>
        </div>
        <input type="hidden" name="jenjang" id="jenjang" value="{{isset($data)?$data['jenjang']:''}}">
        <input type="hidden" name="tingkat_kelas" id="tingkat_kelas" value="{{isset($data)?$data['tingkat_kelas']:''}}">

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Nominal*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('amount') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" data-type="currency" name="amount" id="amount" type="text" placeholder="{{ __('Nominal') }}" value="{{ old('amount', isset($data) ? $data['amount'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6 th_ajaran_form" style="display: none;">
          <label class="col-sm-6 col-form-label">{{ __('Tahun Ajaran*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('th_ajaran') ? ' has-danger' : '' }} th-ajaran-wrapper">
               <select class="form-control" name="th_ajaran" id="th_ajaran">
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="col-sm-9 col-form-label">{{ __('Beasiswa?') }}</label>
          <div class="col-sm-6">
            <div class="form-check form-check-radio form-check-inline">
              <label class="form-check-label">
                <input class="form-check-input" type="radio" name="is_beasiswa" id="status1" value="Y" data-value="Y" > Y
                <span class="circle">
                    <span class="check"></span>
                </span>
              </label>
            </div>
            <div class="form-check form-check-radio form-check-inline">
              <label class="form-check-label">
                <input class="form-check-input" type="radio" name="is_beasiswa" id="status2" value="N" data-value="N" {{ (!isset($data))? "checked" : "" }} > N
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
