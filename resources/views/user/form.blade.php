<form method="post" action="{{ route('user.crud') }}" data-redirect="{{ route('user') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('First Name*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('first_name') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" id="first_name" type="text" placeholder="{{ __('First Name') }}" value="{{ old('first_name', isset($data) ? $data['first_name'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Last Name') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('last_name') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" id="last_name" type="text" placeholder="{{ __('Last Name') }}" value="{{ old('last_name', isset($data) ? $data['last_name'] : '') }}" />
            </div>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Role*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('role') ? ' has-danger' : '' }}">                      
              <select class="form-control {{ $errors->has('role') ? ' is-invalid' : '' }}" name="role" id="role" placeholder="{{ __('Role') }}" value="{{ old('role', isset($data) ? $data['role'] : '') }}" required>
                <option value="" disabled selected>Select your option</option>
                <option value="0">Admin</option>
                <option value="1">Staff</option>
                <option value="2">Finance</option>
                <option value="3">Inventory</option>
              </select>
            </div>
          </div>
        </div>

        <!-- <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Phone Number*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('phone_number') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('phone_number') ? ' is-invalid' : '' }}" name="phone_number" id="phone_number" type="text" placeholder="{{ __('Phone Number') }}" value="{{ old('phone_number', isset($data) ? $data['phone_number'] : '') }}" required />
            </div>
          </div>
        </div>  -->
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Email') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="email" type="email" placeholder="{{ __('Email') }}" value="{{ old('email', isset($data) ? $data['email'] : '') }}" />
            </div>
          </div>
        </div>       
      </div>

      <div class="form-group staff-list" style="display: none;">
        <label class="col-sm-6 col-form-label">{{ __('NIP*') }}</label>
        <div class="col-sm-12">
          <div class="row align-items-center">
            <div class="col-sm-9">
              <select class="form-control" name="nip" id="nip">
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
            <div class="col-sm-3">
              <a class="btn btn-primary" href="{{ url('master/staff') }}">Add Staff</a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="form-row" id="password-section">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Password*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password" type="password" placeholder="{{ __('Password') }}" value="{{ old('password') }}" />
            </div>
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-12 col-form-label">{{ __('Re-Type Password*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('password_confirmation ') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('password_confirmation ') ? ' is-invalid' : '' }}" name="password_confirmation" id="password_confirmation" type="password" placeholder="{{ __('Re-type') }}" />
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
