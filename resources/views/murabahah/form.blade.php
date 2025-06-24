<form method="post" action="{{ route('murabahah.crud',isset($data)?1:0 ) }}" data-redirect="{{ route('murabahah') }}" autocomplete="off" class="form-horizontal form-admin">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['id']:''}}">
  <div class="card" style="margin-top: 0 !important;">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-10 col-form-label">{{ __('Transaksi No.') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('no_murabahah') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('no_murabahah') ? ' is-invalid' : '' }}" name="no_murabahah" id="no_murabahah" type="text" placeholder="{{ __('Kode') }}" value="{{ old('no_murabahah', isset($data) ? $data['no_murabahah'] : '') }}" readonly >
            </div>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="col-sm-10 col-form-label">{{ __('Tanggal Transaksi*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('date_trans') ? ' has-danger' : '' }}">            
              <input type="date" class="form-control{{ $errors->has('date_trans') ? ' is-invalid' : '' }}" id="date_trans" name="date_trans"  placeholder="Tanggal Transaksi" onkeydown="return false" required>
            </div>          
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Tanggal Mulai Angsuran*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('date') ? ' has-danger' : '' }}">            
              <input type="date" class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" id="date" name="date"  placeholder="Tanggal Mulai Iuran" onkeydown="return false" required>
            </div>          
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="col-sm-12 col-form-label">{{ __('Type*') }}</label>
          <div class="col-sm-12">
            <div class="form-group">
              <select class="form-control prevent-edit" name="type" id="type" required>
                <option value="" disabled selected>Select your option</option>
                <option value="0">Barang</option>
                <option value="1">Jasa</option>
              </select>
            </div>
          </div>
        </div>
      </div>
            
      <div class="form-content" style="display:none">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Anggota*') }}</label>
            <div class="col-sm-12">
              <div class="form-group">
                <select class="form-control prevent-edit" name="no_anggota" id="no_anggota" required>
                  <option value="" disabled selected>Select your option</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Harga Item*') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nilai_awal') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nilai_awal') ? ' is-invalid' : '' }}" data-type="currency" name="nilai_awal" id="nilai_awal" type="text" placeholder="{{ __('Harga Item') }}" value="{{ old('nilai_awal', isset($data) ? $data['nilai_awal'] : '') }}" required />
              </div>
            </div>
          </div>        
        </div>

        <div class="form-row">
          <div class="form-group col-md-12">
            <label class="col-sm-6 col-form-label">{{ __('Uraian') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('desc') ? ' has-danger' : '' }}">
                <textarea class="form-control{{ $errors->has('desc') ? ' is-invalid' : '' }}" name="desc" id="desc" placeholder="{{ __('Description') }}" value="{{ old('desc', isset($data) ? $data['transDesc'] : '') }}"></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="col-sm-9 col-form-label">{{ __('Tenor Cicilan*') }}</label>
            <div class="col-sm-12">
              <div class="form-group">
                <select class="form-control" name="margin" id="margin" required>
                  <option value="" disabled selected>Select your option</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6 content-barang">
            <label class="col-sm-9 col-form-label">{{ __('Biaya Transportasi') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nilai_transport') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nilai_transport') ? ' is-invalid' : '' }} optional-input" data-type="currency" name="nilai_transport" id="nilai_transport" type="text" placeholder="{{ __('Biaya Transportasi') }}" value="{{ old('nilai_transport', isset($data) ? $data['nilai_transport'] : '') }}" readonly />
              </div>
            </div>
          </div>  
          <div class="form-group col-md-6 content-jasa">
            <label class="col-sm-12 col-form-label">{{ __('Harga total (termasuk biaya jasa)*') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nilai_total_jasa') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nilai_total_jasa') ? ' is-invalid' : '' }}" data-type="currency" name="nilai_total_jasa" id="nilai_total_jasa" type="text" placeholder="{{ __('Harga total') }}" value="{{ old('nilai_total_jasa', isset($data) ? $data['nilai_total_jasa'] : '') }}" readonly />
              </div>
            </div>
          </div>      
        </div>

        <div class="form-row col-md-12 content-barang">
          <div class="col-sm-12">
            <a href="#" class="show-angsuran" onclick="viewAngsuran(event)">Lihat Detail Angsuran >></a>
            <a href="#" class="hide-angsuran" onclick="hideAngsuran(event)" style="display:none"><< Sembunyikan Detail Angsuran</a>
          </div>
        </div>

        <div class="form-row autogenerate-sec" style="display:none">
          <div class="form-group col-md-4">
            <label class="col-sm-9 col-form-label">{{ __('Margin') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('margin_view') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('margin_view') ? ' is-invalid' : '' }}" name="margin_view" id="margin_view" type="text" placeholder="{{ __('Margin') }}" value="{{ old('margin_view', isset($data) ? $data['margin_view'] : '') }}" readonly />
              </div>
            </div>
          </div> 
          <div class="form-group col-md-4">
            <label class="col-sm-9 col-form-label">{{ __('Harga Jual') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('nilai_total') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('nilai_total') ? ' is-invalid' : '' }}" data-type="currency" name="nilai_total" id="nilai_total" type="text" placeholder="{{ __('Nilai Total') }}" value="{{ old('nilai_total', isset($data) ? $data['nilai_total'] : '') }}" readonly />
              </div>
            </div>
          </div> 
          <div class="form-group col-md-4">
            <label class="col-sm-9 col-form-label">{{ __('Angsuran') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('angsuran') ? ' has-danger' : '' }}">
                <input class="form-control{{ $errors->has('angsuran') ? ' is-invalid' : '' }}" data-type="currency" name="angsuran" id="angsuran" type="text" placeholder="{{ __('Angsuran') }}" value="{{ old('angsuran', isset($data) ? $data['angsuran'] : '') }}" readonly />
              </div>
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
