@extends('layouts.app',['web' => false])

@push('css')
<style>
  body{
    /* background: #00548b !important; */
  }
</style>
@endpush

@section('content')
<div class="row m-0" style="min-height: 100vh;">
  <div class="col-md-5 d-none d-lg-flex p-0 align-items-center bg-primary">
    <div class="landing-wording ml-5">
      <h1>Koperasi Karyawan Prasadha Makmur Sejahtera</h1>
    </div>
  </div>
  <div class="col-md-7 col-sm-12 p-0 d-flex align-items-center">
    <div class="w-100">
      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-2 text-center" role="alert">
          {{ session('success') }}
        </div>
      @endif

      <form class="form form-login w-75 m-auto" method="POST" action="{{ route('auth.on-sign-in') }}" style="{{ $errors->has('email') ? 'display:none':'' }}">
        @csrf
          <div class="card shadow ml-auto mr-auto mt-4">
            <div class="card-body">
              <div class="bmd-form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">perm_identity</i>
                    </span>
                  </div>
                  <input type="text" name="username" class="form-control" placeholder="{{ __('Username / Email...') }}" value="{{ old('username') }}" required>
                </div>
                @if ($errors->has('username'))
                  <div id="username-error" class="error text-danger pl-3" for="username" style="display: block;">
                    <strong>{{$errors->first()}}</strong>
                  </div>
                @endif
                @if ($errors->has('invalid'))
                  <div id="username-error" class="error text-danger pl-3" for="username" style="display: block;">
                    <strong>{{$errors->first()}}</strong>
                  </div>
                @endif
              </div>
              <div class="bmd-form-group{{ $errors->has('password') ? ' has-danger' : '' }} mt-3">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">lock_outline</i>
                    </span>
                  </div>
                  <input type="password" name="password" class="form-control" placeholder="{{ __('Password...') }}" required>
                </div>
                @if ($errors->has('password'))
                  <div id="password-error" class="error text-danger pl-3" for="password" style="display: block;">
                    <strong>{{ $errors->first() }}</strong>
                  </div>
                @endif
              </div>
            </div>
            <div class="card-footer justify-content-center">
              <button type="submit" class="btn btn-sm btn-block button-link">Masuk</button>
            </div>
          </div>
      </form>

      <form class="form form-forgot-password w-75 m-auto" method="POST" action="{{ route('auth.forgot-password') }}" style="{{ $errors->has('email') ? '':'display:none' }}">
        @csrf
        <div class="card shadow ml-auto mr-auto mt-4">
          <div class="card-body">
            <div class="bmd-form-group">
              <div class="input-group">
                <input type="text" name="email" class="form-control" placeholder="{{ __('Email...') }}" value="{{ old('email') }}" required>
              </div>
              @if ($errors->has('email'))
                <div id="email-error" class="error text-danger pl-3" for="email" style="display: block;">
                  <strong>{{$errors->first()}}</strong>
                </div>
              @endif
            </div>
          </div>
          <div class="card-footer justify-content-center">
            <button type="submit" class="btn btn-sm btn-block button-link">Submit</button>
          </div>
        </div>
      </form>
  
      <div class="text-center">
        <a href="#" onClick="forgotPassword()" id="forgot-btn" style="{{ $errors->has('email') ? 'display:none':'' }}">
            <small>{{ __('Forgot password?') }}</small>
        </a>
        <a href="#" onClick="back()" id="back-btn" style="{{ $errors->has('email') ? '':'display:none' }}">
            <small>
              {{ __('Kembali') }}
            </small>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
  var success = "{{session('success')}}"
  $(document).ready(function(){
      if(success){
        setTimeout(() => {
          $('.alert').hide()
        }, 7000);
      }
  });

  function checktnc() {
    if($('#tnc').is(":checked")){
      $('#register-btn').prop('disabled', false);
    }else{
      $('#register-btn').prop('disabled', true);
    }
  }

  function forgotPassword() {
    $('.form-login').hide();
    $('.form-forgot-password').show();
    $('#forgot-btn').hide();
    $('#back-btn').show();
  }

  function back(){
    $('.form-login').show();
    $('.form-forgot-password').hide();
    $('#back-btn').hide();
    $('#forgot-btn').show();
  }
</script>
@endpush

