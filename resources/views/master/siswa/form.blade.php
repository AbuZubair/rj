<form method="post" action="{{ route('kesiswaan.crud') }}" data-redirect="{{ route('kesiswaan') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body">
      <div class="form-row">
        <div class="form-group col-md-12 mb-2">
          <div class="avatar-wrapper">
            <img class="profile-pic avatar-img" src="" />
            <div class="upload-button">
              <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            </div>
            <input class="file-upload" type="file" name="avatar" id="avatar"  accept="image/*"/>
          </div>
        </div>  
      </div>
      
      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Jenjang*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('jenjang') ? ' has-danger' : '' }} position-relative jenjang-container">
              <select class="form-control" name="jenjang" id="jenjang" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="col-sm-12 col-form-label">{{ __('Tingkat Kelas*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('tingkat_kelas') ? ' has-danger' : '' }}">
              <select class="form-control" name="tingkat_kelas" id="tingkat_kelas" required>
                <option value="" disabled selected>Select your option</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-4">
          <label class="col-sm-6 col-form-label">{{ __('NIS*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('nis') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('nis') ? ' is-invalid' : '' }} prevent-edit" name="nis" id="nis" type="text" placeholder="{{ __('NIS') }}" readonly>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Nama Lengkap*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('fullname') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('fullname') ? ' is-invalid' : '' }}" name="fullname" id="fullname" type="text" placeholder="{{ __('Nama Lengkap') }}" value="{{ old('fullname', isset($data) ? $data['fullname'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div> 
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('NIK*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('nik') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('nik') ? ' is-invalid' : '' }}" name="nik" id="nik" type="text" placeholder="{{ __('NIK') }}" value="{{ old('nik', isset($data) ? $data['nik'] : '') }}" required>
            </div>
          </div>
        </div>      
      </div>

      <div class="form-row">
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Jenis Kelamin*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('jenis_kelamin') ? ' has-danger' : '' }} position-relative jenis_kelamin-container">
              <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                <option value="L" {{ old('jenis_kelamin', isset($data) ? $data['jenis_kelamin'] : '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin', isset($data) ? $data['jenis_kelamin'] : '') == 'P' ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Tempat Lahir*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('tempat_lahir') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('tempat_lahir') ? ' is-invalid' : '' }}" name="tempat_lahir" id="tempat_lahir" type="text" placeholder="{{ __('Tempat Lahir') }}" value="{{ old('tempat_lahir', isset($data) ? $data['tempat_lahir'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Tanggal Lahir*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('tanggal_lahir') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('tanggal_lahir') ? ' is-invalid' : '' }}" name="tanggal_lahir" id="tanggal_lahir" type="date" placeholder="{{ __('Tanggal Lahir') }}" value="{{ old('tanggal_lahir', isset($data) ? $data['tanggal_lahir'] : '') }}" required="true" aria-required="true"/>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row col-md-12">
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
        <div class="col-sm-6 d-flex align-items-end">
          <a href="#" class="show-optional" onclick="viewOptional(event)">Optional fields >></a>
          <a href="#" class="hide-optional" onclick="hideOptional(event)" style="display:none"><< Sembunyikan Optional fields</a>
        </div>
      </div>

      <!-- Optional section -->
      <section class="optional-section d-none">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('NISN') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nisn') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nisn') ? ' is-invalid' : '' }}" name="nisn" id="nisn" type="text" placeholder="{{ __('NISN') }}" value="{{ old('nisn', isset($data) ? $data['nisn'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Urutan Anak Ke') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('urutan_anak_ke') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('urutan_anak_ke') ? ' is-invalid' : '' }}" name="urutan_anak_ke" id="urutan_anak_ke" type="number" placeholder="{{ __('Urutan Anak Ke') }}" value="{{ old('urutan_anak_ke', isset($data) ? $data['urutan_anak_ke'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Tinggal Bersama') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('tinggal_bersama') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('tinggal_bersama') ? ' is-invalid' : '' }}" name="tinggal_bersama" id="tinggal_bersama" type="text" placeholder="{{ __('Tinggal Bersama') }}" value="{{ old('tinggal_bersama', isset($data) ? $data['tinggal_bersama'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Alamat Tinggal') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('alamat_tinggal') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('alamat_tinggal') ? ' is-invalid' : '' }}" name="alamat_tinggal" id="alamat_tinggal" type="text" placeholder="{{ __('Alamat Tinggal') }}" value="{{ old('alamat_tinggal', isset($data) ? $data['alamat_tinggal'] : '') }}">
              </div>
            </div>
          </div>          
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Kelurahan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('kelurahan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('kelurahan') ? ' is-invalid' : '' }}" name="kelurahan" id="kelurahan" type="text" placeholder="{{ __('Kelurahan') }}" value="{{ old('kelurahan', isset($data) ? $data['kelurahan'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Kecamatan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('kecamatan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('kecamatan') ? ' is-invalid' : '' }}" name="kecamatan" id="kecamatan" type="text" placeholder="{{ __('Kecamatan') }}" value="{{ old('kecamatan', isset($data) ? $data['kecamatan'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Provinsi') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('provinsi') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('provinsi') ? ' is-invalid' : '' }}" name="provinsi" id="provinsi" type="text" placeholder="{{ __('Provinsi') }}" value="{{ old('provinsi', isset($data) ? $data['provinsi'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Nama Ayah') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nama_ayah') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nama_ayah') ? ' is-invalid' : '' }}" name="nama_ayah" id="nama_ayah" type="text" placeholder="{{ __('Nama Ayah') }}" value="{{ old('nama_ayah', isset($data) ? $data['nama_ayah'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Pekerjaan Ayah') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('pekerjaan_ayah') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('pekerjaan_ayah') ? ' is-invalid' : '' }}" name="pekerjaan_ayah" id="pekerjaan_ayah" type="text" placeholder="{{ __('Pekerjaan Ayah') }}" value="{{ old('pekerjaan_ayah', isset($data) ? $data['pekerjaan_ayah'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Tempat Lahir Ayah') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('tempat_lahir_ayah') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('tempat_lahir_ayah') ? ' is-invalid' : '' }}" name="tempat_lahir_ayah" id="tempat_lahir_ayah" type="text" placeholder="{{ __('Tempat Lahir Ayah') }}" value="{{ old('tempat_lahir_ayah', isset($data) ? $data['tempat_lahir_ayah'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Tanggal Lahir Ayah') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('tanggal_lahir_ayah') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('tanggal_lahir_ayah') ? ' is-invalid' : '' }}" name="tanggal_lahir_ayah" id="tanggal_lahir_ayah" type="date" placeholder="{{ __('Tanggal Lahir Ayah') }}" value="{{ old('tanggal_lahir_ayah', isset($data) ? $data['tanggal_lahir_ayah'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Nama Ibu') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nama_ibu') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nama_ibu') ? ' is-invalid' : '' }}" name="nama_ibu" id="nama_ibu" type="text" placeholder="{{ __('Nama Ibu') }}" value="{{ old('nama_ibu', isset($data) ? $data['nama_ibu'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Pekerjaan Ibu') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('pekerjaan_ibu') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('pekerjaan_ibu') ? ' is-invalid' : '' }}" name="pekerjaan_ibu" id="pekerjaan_ibu" type="text" placeholder="{{ __('Pekerjaan Ibu') }}" value="{{ old('pekerjaan_ibu', isset($data) ? $data['pekerjaan_ibu'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
           <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Tempat Lahir Ibu') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('tempat_lahir_ibu') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('tempat_lahir_ibu') ? ' is-invalid' : '' }}" name="tempat_lahir_ibu" id="tempat_lahir_ibu" type="text" placeholder="{{ __('Tempat Lahir Ibu') }}" value="{{ old('tempat_lahir_ibu', isset($data) ? $data['tempat_lahir_ibu'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Tanggal Lahir Ibu') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('tanggal_lahir_ibu') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('tanggal_lahir_ibu') ? ' is-invalid' : '' }}" name="tanggal_lahir_ibu" id="tanggal_lahir_ibu" type="date" placeholder="{{ __('Tanggal Lahir Ibu') }}" value="{{ old('tanggal_lahir_ibu', isset($data) ? $data['tanggal_lahir_ibu'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Penghasilan Orangtua') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('penghasilan_orangtua') ? ' has-danger' : '' }} position-relative penghasilan-orangtua-container">
                <select class="form-control" name="penghasilan_orangtua" id="penghasilan_orangtua">
                  <option value="" disabled selected>Pilih Penghasilan</option>
                  <option value="dibawah 1juta" {{ old('penghasilan_orangtua', isset($data) ? $data['penghasilan_orangtua'] : '') == 'dibawah 1juta' ? 'selected' : '' }}>Dibawah 1 Juta</option>
                  <option value="diatas 1juta" {{ old('penghasilan_orangtua', isset($data) ? $data['penghasilan_orangtua'] : '') == 'diatas 1juta' ? 'selected' : '' }}>Diatas 1 Juta</option>
                  <option value="diatas 3juta" {{ old('penghasilan_orangtua', isset($data) ? $data['penghasilan_orangtua'] : '') == 'diatas 3juta' ? 'selected' : '' }}>Diatas 3 Juta</option>
                  <option value="diatas 5juta" {{ old('penghasilan_orangtua', isset($data) ? $data['penghasilan_orangtua'] : '') == 'diatas 5juta' ? 'selected' : '' }}>Diatas 5 Juta</option>
                  <option value="diatas 10juta" {{ old('penghasilan_orangtua', isset($data) ? $data['penghasilan_orangtua'] : '') == 'diatas 10juta' ? 'selected' : '' }}>Diatas 10 Juta</option>
                  <option value="diatas 20juta" {{ old('penghasilan_orangtua', isset($data) ? $data['penghasilan_orangtua'] : '') == 'diatas 20juta' ? 'selected' : '' }}>Diatas 20 Juta</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Phone') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" id="phone" type="text" placeholder="{{ __('Phone') }}" value="{{ old('phone', isset($data) ? $data['phone'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Asal Sekolah') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('asal_sekolah') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('asal_sekolah') ? ' is-invalid' : '' }}" name="asal_sekolah" id="asal_sekolah" type="text" placeholder="{{ __('Asal Sekolah') }}" value="{{ old('asal_sekolah', isset($data) ? $data['asal_sekolah'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Alamat Sekolah Asal') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('alamat_sekolah_asal') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('alamat_sekolah_asal') ? ' is-invalid' : '' }}" name="alamat_sekolah_asal" id="alamat_sekolah_asal" type="text" placeholder="{{ __('Alamat Sekolah Asal') }}" value="{{ old('alamat_sekolah_asal', isset($data) ? $data['alamat_sekolah_asal'] : '') }}">
              </div>
            </div>
          </div>          
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Tinggi Badan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('tinggi_badan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('tinggi_badan') ? ' is-invalid' : '' }}" name="tinggi_badan" id="tinggi_badan" type="text" placeholder="{{ __('Tinggi Badan') }}" value="{{ old('tinggi_badan', isset($data) ? $data['tinggi_badan'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Berat Badan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('berat_badan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('berat_badan') ? ' is-invalid' : '' }}" name="berat_badan" id="berat_badan" type="text" placeholder="{{ __('Berat Badan') }}" value="{{ old('berat_badan', isset($data) ? $data['berat_badan'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Riwayat Sakit') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('riwayat_sakit') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('riwayat_sakit') ? ' is-invalid' : '' }}" name="riwayat_sakit" id="riwayat_sakit" type="text" placeholder="{{ __('Riwayat Sakit') }}" value="{{ old('riwayat_sakit', isset($data) ? $data['riwayat_sakit'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Bidang Olahraga') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('bidang_olahraga') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('bidang_olahraga') ? ' is-invalid' : '' }}" name="bidang_olahraga" id="bidang_olahraga" type="text" placeholder="{{ __('Bidang Olahraga') }}" value="{{ old('bidang_olahraga', isset($data) ? $data['bidang_olahraga'] : '') }}">
              </div>
            </div>
          </div>          
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Bidang Lainnya') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('bidang_lainnya') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('bidang_lainnya') ? ' is-invalid' : '' }}" name="bidang_lainnya" id="bidang_lainnya" type="text" placeholder="{{ __('Bidang Lainnya') }}" value="{{ old('bidang_lainnya', isset($data) ? $data['bidang_lainnya'] : '') }}">
              </div>
            </div>
          </div>
           <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Program Unggulan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('program_unggulan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('program_unggulan') ? ' is-invalid' : '' }}" name="program_unggulan" id="program_unggulan" type="text" placeholder="{{ __('Program Unggulan') }}" value="{{ old('program_unggulan', isset($data) ? $data['program_unggulan'] : '') }}">
              </div>
            </div>
          </div>
        </div>
      </section>                                             
    </div>
    <div class="card-footer ml-auto mr-auto">
      <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
      <button type="submit" class="btn button-link">{{ __('Save') }}</button>
    </div>
  </div>
</form>
