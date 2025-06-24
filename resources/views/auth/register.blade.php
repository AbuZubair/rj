<form class="form form-register" method="POST" enctype="multipart/form-data" action="{{ route('user.register') }}" style="margin:4em 4em">
  @csrf
  <input type="hidden" name="category" value="{{$category}}">
  <div class="row">
    <div class="form-group col-lg-6 col-sm-12">
      <label for="username"></label>
      <input type="text" name="name" id="name" class="form-control" value="{{ old('name')  }}" placeholder="Nama Lengkap*" onkeypress="return /[a-z ]/i.test(event.key)">
      @if ($error!='' && $errors->first()=='name')
        <div id="password-error" class="error text-danger pt-2 pb-2" for="password" style="display: block;">
          <strong>@lang('validation.name')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'min,name') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.min_name')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'sara,name') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.sara')</strong>
        </div>
      @endif
    </div>

    <div class="form-group col-lg-6 col-sm-12">
      <label for="phone"></label>
      <input type="number" name="phone" id="phone" class="form-control" value="{{ old('phone')  }}" placeholder="Nomor Telepon*">
      @if ($error!='' && $errors->first()=='phone')
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.phone')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'max,phone') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.maksimum')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'numeric,phone') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.numeric')</strong>
        </div>
      @endif
    </div>

    <div class="form-group col-lg-6 col-sm-12">
      <label for="email"></label>
      <input type="number" name="nik" id="nik" class="form-control" value="{{ old('nik')  }}" placeholder="NIK* (contoh:317293xx)">
      @if ($error!='' && $errors->first()=='nik')
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.nik')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'max,nik') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.maksimum')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'numeric,nik') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.numeric')</strong>
        </div>
      @endif
    </div>

    <div class="form-group col-lg-6 col-sm-12">
      <label for="email"></label>
      <input type="email" name="email" id="email" class="form-control" value="{{ old('email')  }}" placeholder="Email*">
      @if ($error!='' && $errors->first()=='email')
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.email')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'email,format') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.format_email')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'unique') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.unique')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'sara,email') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.sara')</strong>
        </div>
      @endif
    </div>


    <div class="form-group col-lg-12 col-sm-12">
      <label for="asal_sekolah"></label>
      <input type="text" name="asalsekolah" id="asalsekolah" class="form-control typeahead" value="{{ old('asalsekolah')  }}" placeholder="Nama Sekolah*" autocomplete="off">
      <!-- <input class="typeahead" type="text" placeholder="States of USA"> -->
      @if ($error!='' && $errors->first()=='asalsekolah')
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.asalsekolah')</strong>
        </div>
      @endif
      @if ($error!='' && strpos($errors->first(), 'sara,asalsekolah') !== false)
        <div class="error text-danger pt-2 pb-2" style="display: block;">
          <strong>@lang('validation.sara')</strong>
        </div>
      @endif
    </div>

    @if ($category!='quiz')
      <div class="form-group col-lg-12 col-sm-12">
        <label for="ig"></label>
        <input type="text" name="ig" id="ig" class="form-control" value="{{ old('ig') ?? '' }}" placeholder="Akun Instagram* (contoh: @akunku)">
        @if ($errors!='' && $errors->first()=='ig')
          <div class="error text-danger pt-2 pb-2" style="display: block;">
            <strong>@lang('validation.ig')</strong>
          </div>
        @endif
      </div>
    @endif
  </div>
  <div class="row">
    <div class="col-lg-10 col-sm-12 ml-auto mr-auto">
      {!! htmlFormSnippet() !!}
    </div>
    <div class="col-lg-10 col-sm-12 ml-auto mr-auto">
      @if ($error!='' && strpos($errors->first(), 'recaptcha') !== false )
        <div class="error text-danger pt-2 pb-2">
          <strong>@lang('validation.recaptcha')</strong>
        </div>
      @endif
    </div>
  </div>
  <div class="col-lg-10 col-sm-12 ml-auto mr-auto">
    <div class="form-check">
      <input type="checkbox" name="tnc" id="tnc" class="form-check-input" id="exampleCheck1" onclick="checktnc();">
      <label class="form-check-label" for="exampleCheck1" style="color:#333 !important">
        Saya menyetujui  <button type="button" class="btn btn-link button-s" data-toggle="modal" data-target="#exampleModalLong">Syarat dan Ketentuan</button> Kompetisi Samsung Indonesia Cerdas 
      </label>
    </div>
    <button type="submit" id="register-btn" disabled class="btn btn-sm btn-block button-link">REGISTRASI</button>
  </div>
   
</form>



