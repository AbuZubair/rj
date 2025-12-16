<form method="post" action="{{ route('staff.crud') }}" data-redirect="{{ route('staff') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body">

      <div class="form-row">
        <div class="form-group col-md-4">
          <label class="col-sm-6 col-form-label">{{ __('NIP*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('nip') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('nip') ? ' is-invalid' : '' }} prevent-edit" name="nip" id="nip" type="text" placeholder="{{ __('NIP') }}" value="{{ old('nip', isset($data) ? $data['nip'] : '') }}" required="true" aria-required="true"/>
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
            <div class="form-group{{ $errors->has('jk') ? ' has-danger' : '' }} position-relative jk-container">
              <select class="form-control" name="jk" id="jk">
                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                <option value="L" {{ old('jk', isset($data) ? $data['jk'] : '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jk', isset($data) ? $data['jk'] : '') == 'P' ? 'selected' : '' }}>Perempuan</option>
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

      <div class="form-row">
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Bank*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('bank') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('bank') ? ' is-invalid' : '' }}" name="bank" id="bank" type="text" placeholder="{{ __('Bank') }}" value="{{ old('bank', isset($data) ? $data['bank'] : '') }}" required>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('No Rekening*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('no_rek') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('no_rek') ? ' is-invalid' : '' }}" name="no_rek" id="no_rek" type="text" placeholder="{{ __('No Rekening') }}" value="{{ old('no_rek', isset($data) ? $data['no_rek'] : '') }}" required>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label class="col-sm-12 col-form-label">{{ __('Atas Nama Rekening*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('an_rek') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('an_rek') ? ' is-invalid' : '' }}" name="an_rek" id="an_rek" type="text" placeholder="{{ __('Atas Nama Rekening') }}" value="{{ old('an_rek', isset($data) ? $data['an_rek'] : '') }}" required>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Tanggal Join*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('join_date') ? ' has-danger' : '' }}">
              <input type="date" class="form-control{{ $errors->has('join_date') ? ' is-invalid' : '' }}" id="join_date" name="join_date" value="{{ old('join_date', isset($data) ? $data['join_date'] : '') }}" required>
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

      <div class="form-row col-md-12">
        <div class="col-sm-6">
          <a href="#" class="show-optional" onclick="viewOptional(event)">Optional fields >></a>
          <a href="#" class="hide-optional" onclick="hideOptional(event)" style="display:none"><< Sembunyikan Optional fields</a>
        </div>
      </div>

      <!-- Optional section -->
      <section class="optional-section d-none">
        <div class="form-row">
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Agama') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('agama') ? ' has-danger' : '' }} position-relative agama-container">
                <select class="form-control" name="agama" id="agama">
                  <option value="" disabled selected>Pilih Agama</option>
                  <option value="Islam" {{ old('agama', isset($data) ? $data['agama'] : '') == 'Islam' ? 'selected' : '' }}>Islam</option>
                  <option value="Kristen" {{ old('agama', isset($data) ? $data['agama'] : '') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                  <option value="Katolik" {{ old('agama', isset($data) ? $data['agama'] : '') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                  <option value="Hindu" {{ old('agama', isset($data) ? $data['agama'] : '') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                  <option value="Buddha" {{ old('agama', isset($data) ? $data['agama'] : '') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                  <option value="Konghucu" {{ old('agama', isset($data) ? $data['agama'] : '') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                  <option value="Lainnya" {{ old('agama', isset($data) ? $data['agama'] : '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Email') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="email" type="email" placeholder="{{ __('Email') }}" value="{{ old('email', isset($data) ? $data['email'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Phone') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" id="phone" type="text" placeholder="{{ __('Phone') }}" value="{{ old('phone', isset($data) ? $data['phone'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Pendidikan Terakhir') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('pendidikan_terakhir') ? ' has-danger' : '' }}">
                <input class="form-control" name="pendidikan_terakhir" id="pendidikan_terakhir" type="text" placeholder="{{ __('Pendidikan Terakhir') }}" value="{{ old('pendidikan_terakhir', isset($data) ? $data['pendidikan_terakhir'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Instansi Terakhir') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('instansi_terakhir') ? ' has-danger' : '' }}">
                <input class="form-control" name="instansi_terakhir" id="instansi_terakhir" type="text" placeholder="{{ __('Instansi Terakhir') }}" value="{{ old('instansi_terakhir', isset($data) ? $data['instansi_terakhir'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Jurusan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('jurusan') ? ' has-danger' : '' }}">
                <input class="form-control" name="jurusan" id="jurusan" type="text" placeholder="{{ __('Jurusan') }}" value="{{ old('jurusan', isset($data) ? $data['jurusan'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label class="col-sm-6 col-form-label">{{ __('Jabatan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('jabatan') ? ' has-danger' : '' }} position-relative jabatan-container">
                <select class="form-control" name="jabatan" id="jabatan">
                  <option value="" disabled selected>Select your option</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-6 col-form-label">{{ __('Jenis PTK') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('jenis_ptk') ? ' has-danger' : '' }} position-relative jenis_ptk-container">
                <select class="form-control" name="jenis_ptk" id="jenis_ptk">
                  <option value="" disabled selected>Select your option</option>
                </select>
              </div> 
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Unit Mengajar') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('unit_mengajar') ? ' has-danger' : '' }} position-relative unit_mengajar-container">
                <select class="form-control" name="unit_mengajar" id="unit_mengajar">
                  <option value="" disabled selected>Select your option</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('SK Pengangkatan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('sk_pengangkatan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('sk_pengangkatan') ? ' is-invalid' : '' }}" name="sk_pengangkatan" id="sk_pengangkatan" type="text" placeholder="{{ __('SK Pengangkatan') }}" value="{{ old('sk_pengangkatan', isset($data) ? $data['sk_pengangkatan'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('TMT Pengangkatan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('tmt_pengangkatan') ? ' has-danger' : '' }}">
                <input type="date" class="form-control{{ $errors->has('tmt_pengangkatan') ? ' is-invalid' : '' }}" id="tmt_pengangkatan" name="tmt_pengangkatan" value="{{ old('tmt_pengangkatan', isset($data) ? $data['tmt_pengangkatan'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-4">
            <label class="col-sm-12 col-form-label">{{ __('Lembaga Pengangkatan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('lembaga_pengangkatan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('lembaga_pengangkatan') ? ' is-invalid' : '' }}" name="lembaga_pengangkatan" id="lembaga_pengangkatan" type="text" placeholder="{{ __('Lembaga Pengangkatan') }}" value="{{ old('lembaga_pengangkatan', isset($data) ? $data['lembaga_pengangkatan'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Nama Ibu Kandung') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nama_ibu_kandung') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nama_ibu_kandung') ? ' is-invalid' : '' }}" name="nama_ibu_kandung" id="nama_ibu_kandung" type="text" placeholder="{{ __('Nama Ibu Kandung') }}" value="{{ old('nama_ibu_kandung', isset($data) ? $data['nama_ibu_kandung'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Status Perkawinan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('status_perkawinan') ? ' has-danger' : '' }} position-relative status_perkawinan-container">
                <select class="form-control" name="status_perkawinan" id="status_perkawinan">
                  <option value="" disabled selected>Pilih Status Perkawinan</option>
                  <option value="K" {{ old('status_perkawinan', isset($data) ? $data['status_perkawinan'] : '') == 'K' ? 'selected' : '' }}>Kawin</option>
                  <option value="BK" {{ old('status_perkawinan', isset($data) ? $data['status_perkawinan'] : '') == 'BK' ? 'selected' : '' }}>Belum Kawin</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-12 col-form-label">{{ __('Pekerjaan Pasangan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('pekerjaan_pasangan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('pekerjaan_pasangan') ? ' is-invalid' : '' }}" name="pekerjaan_pasangan" id="pekerjaan_pasangan" type="text" placeholder="{{ __('Pekerjaan Pasangan') }}" value="{{ old('pekerjaan_pasangan', isset($data) ? $data['pekerjaan_pasangan'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Keahlian') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('keahlian') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('keahlian') ? ' is-invalid' : '' }}" name="keahlian" id="keahlian" type="text" placeholder="{{ __('Keahlian') }}" value="{{ old('keahlian', isset($data) ? $data['keahlian'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('NPWP') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('npwp') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('npwp') ? ' is-invalid' : '' }}" name="npwp" id="npwp" type="text" placeholder="{{ __('NPWP') }}" value="{{ old('npwp', isset($data) ? $data['npwp'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Nama Wajib Pajak') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nama_wajib_pajak') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nama_wajib_pajak') ? ' is-invalid' : '' }}" name="nama_wajib_pajak" id="nama_wajib_pajak" type="text" placeholder="{{ __('Nama Wajib Pajak') }}" value="{{ old('nama_wajib_pajak', isset($data) ? $data['nama_wajib_pajak'] : '') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Kewarganegaraan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('kewarganegaraan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('kewarganegaraan') ? ' is-invalid' : '' }}" name="kewarganegaraan" id="kewarganegaraan" type="text" placeholder="{{ __('Kewarganegaraan') }}" value="{{ old('kewarganegaraan', isset($data) ? $data['kewarganegaraan'] : '') }}">
              </div>
            </div>
          </div>        
        </div>

        <div class="form-row">
          <div class="form-group col-md-12">
            <label class="col-sm-6 col-form-label">{{ __('Alamat') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('alamat') ? ' has-danger' : '' }}">
                <textarea class="form-control{{ $errors->has('alamat') ? ' is-invalid' : '' }}" name="alamat" id="alamat" placeholder="{{ __('Alamat') }}">{{ old('alamat', isset($data) ? $data['alamat'] : '') }}</textarea>
              </div>
            </div>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Kelurahan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('kelurahan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('kelurahan') ? ' is-invalid' : '' }}" name="kelurahan" id="kelurahan" type="text" placeholder="{{ __('Kelurahan') }}" value="{{ old('kelurahan', isset($data) ? $data['kelurahan'] : '') }}">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Kecamatan') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('kecamatan') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('kecamatan') ? ' is-invalid' : '' }}" name="kecamatan" id="kecamatan" type="text" placeholder="{{ __('Kecamatan') }}" value="{{ old('kecamatan', isset($data) ? $data['kecamatan'] : '') }}">
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
