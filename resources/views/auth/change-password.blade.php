@extends('layouts.admin', ['activePage' => '', 'titlePage' => __('Change Password')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <div id="password-section">
          <div class="row align-items-center">
            <label class="col-sm-2 col-form-label">{{ __('Password*') }}</label>
            <div class="col-sm-7">
              <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password" type="password" placeholder="{{ __('Password') }}" value="{{ old('password') }}" />
              </div>
            </div>
          </div>

          <div class="row align-items-center">
            <label class="col-sm-2 col-form-label">{{ __('Re-Type Password*') }}</label>
            <div class="col-sm-7">
              <div class="form-group{{ $errors->has('password_confirmation ') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('password_confirmation ') ? ' is-invalid' : '' }}" name="password_confirmation" id="password_confirmation" type="password" placeholder="{{ __('Re-type') }}" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
 
</script>
@endpush