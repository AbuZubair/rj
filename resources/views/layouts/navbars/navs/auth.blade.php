<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top " style="padding-left: 0 !important;padding-right: 0 !important">
  <div class="container-fluid">
    <div class="navbar-wrapper">
      <a class="navbar-brand" href="#"><h3><b>{{ $titlePage }}</b></h3></a>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
      <span class="sr-only">Toggle navigation</span>
      <span class="navbar-toggler-icon icon-bar"></span>
      <span class="navbar-toggler-icon icon-bar"></span>
      <span class="navbar-toggler-icon icon-bar"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end">
      <form class="navbar-form">
        <!-- <div class="input-group no-border">
        <input type="text" value="" class="form-control" placeholder="Search...">
        <button type="submit" class="btn btn-white btn-round btn-just-icon">
          <i class="material-icons">search</i>
          <div class="ripple-container"></div>
        </button>
        </div> -->
      </form>
      <ul class="navbar-nav">       
        <li class="nav-item dropdown">
          <a class="nav-link d-lg-flex" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="material-icons">person</i>
            <p class="d-none d-lg-block" style="text-transform: capitalize;">
              Hi, {{ Auth::user()->getFullname() }}
            </p>
            <p class="d-lg-none d-md-block">
              {{ __('Account') }}
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="javascript:;" onClick="changePassword()" >{{ __('Change Password') }}</a>            
            <a class="dropdown-item" href="{{ route('logout') }}" >{{ __('Log out') }}</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="modal fade" id="chgPwdModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="password-section">
          <div class="row align-items-center">
            <label class="col-sm-4 col-form-label">{{ __('Current Password*') }}</label>
            <div class="col-sm-7">
              <div class="form-group{{ $errors->has('current_password') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('current_password') ? ' is-invalid' : '' }}" name="current_password" id="current_password" type="password" placeholder="{{ __('Current Password') }}" required="true" aria-required="true"/>
              </div>
            </div>
          </div>

          <div class="row align-items-center">
            <label class="col-sm-4 col-form-label">{{ __('New Password*') }}</label>
            <div class="col-sm-7">
              <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="chgPassword" id="chgPassword" type="password" placeholder="{{ __('Password') }}" required="true" aria-required="true"/>
              </div>
            </div>
          </div>

          <div class="row align-items-center">
            <label class="col-sm-4 col-form-label">{{ __('Confirmation*') }}</label>
            <div class="col-sm-7">
              <div class="form-group{{ $errors->has('password_confirmation ') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('password_confirmation ') ? ' is-invalid' : '' }}" name="chg_password_confirmation" id="chg_password_confirmation" type="password" placeholder="{{ __('Confirmation') }}" required="true" aria-required="true"/>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer" style="justify-content: center !important;">
        <button onClick="saveChangePwd()" class="btn button-link">{{ __('Save') }}</button>
      </div>
    </div>
  </div>
</div>
