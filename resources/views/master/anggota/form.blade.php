<form method="post" action="{{ route('anggota.crud',isset($data)?1:0 ) }}" data-redirect="{{ route('anggota') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('No Anggota*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('no_anggota') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('no_anggota') ? ' is-invalid' : '' }} prevent-edit" name="no_anggota" id="no_anggota" type="text" placeholder="{{ __('No Anggota') }}" value="{{ old('no_anggota', isset($data) ? $data['no_anggota'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Nama Lengkap*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('fullname') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('fullname') ? ' is-invalid' : '' }}" name="fullname" id="fullname" type="text" placeholder="{{ __('Nama Lengkap') }}" value="{{ old('fullname', isset($data) ? $data['fullname'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>       
      </div>

      <div class="form-row">        
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Email*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="email" type="email" placeholder="{{ __('Email') }}" value="{{ old('email', isset($data) ? $data['email'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>     
        <div class="form-group col-md-6">
          <label class="col-sm-10 col-form-label">{{ __('Tanggal Join*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('join_date') ? ' has-danger' : '' }}">            
              <input type="date" class="form-control{{ $errors->has('join_date') ? ' is-invalid' : '' }}" id="join_date" name="join_date"  placeholder="Tanggal Bergabung" onkeydown="return false" required>
            </div>          
          </div>
        </div>      
      </div>

      <div class="form-row">
        <div class="form-group col-md-4">
          <label class="col-sm-6 col-form-label">{{ __('Grade*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('grade') ? ' has-danger' : '' }}">
              <select class="form-control" name="grade" id="grade" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-6 col-form-label">{{ __('Divisi*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('divisi') ? ' has-danger' : '' }}">
              <select class="form-control" name="divisi" id="divisi" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-10 col-form-label">{{ __('Department*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('department') ? ' has-danger' : '' }}">
              <select class="form-control" name="department" id="department" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Limit Kredit*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('limit_kredit') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('limit_kredit') ? ' is-invalid' : '' }}" data-type="currency" name="limit_kredit" id="limit_kredit" type="text" placeholder="{{ __('Limit Kredit') }}" value="{{ old('limit_kredit', isset($data) ? $data['limit_kredit'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6">
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
