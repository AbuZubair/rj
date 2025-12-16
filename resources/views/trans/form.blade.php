<form method="post" action="{{ route('trans.crud') }}" data-redirect="{{ route('trans') }}" autocomplete="off" class="form-horizontal form-admin" id="form-trans">
  @csrf
  <input type="hidden" name="id" id="id" value="{{isset($data)?$data['rowID']:''}}">
  <div class="card ">
    <div class="card-body ">

      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Date*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('transDate') ? ' has-danger' : '' }}">            
              <input type="date" class="form-control{{ $errors->has('transDate') ? ' is-invalid' : '' }}" id="transDate" name="transDate"  placeholder="Date" onkeydown="return false" required>
            </div>          
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="col-sm-6 col-form-label">{{ __('Amount*') }}</label>
          <div class="col-sm-12">
            <div class="form-group{{ $errors->has('amount') ? ' has-danger' : '' }}">
              <input class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" data-type="currency" name="amount" id="amount" type="text" placeholder="{{ __('Amount') }}" value="{{ old('amount', isset($data) ? $data['amount'] : '') }}" required />
            </div>
          </div>
        </div>    
      </div>

      <div class="main-section">
        <div class="form-row">
          <div class="form-group col-md-12">
            <label class="col-sm-6 col-form-label">{{ __('Uraian*') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('tans_desc') ? ' has-danger' : '' }}">
                <textarea class="form-control{{ $errors->has('tans_desc') ? ' is-invalid' : '' }}" name="tans_desc" id="tans_desc" placeholder="{{ __('Description') }}" value="{{ old('tans_desc', isset($data) ? $data['transDesc'] : '') }}" required></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="form-row single_coa">
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('Debit / Kredit*') }}</label>
            <div class="col-sm-12">
              <div class="form-group dk-wrapper">                      
                <select class="form-control" name="dk" id="dk" value="{{ old('dk', isset($data) ? $data['dk'] : '') }}">
                  <option value="" disabled selected>Select your option</option>
                  <option value="debit">Debit</option>
                  <option value="kredit">Kredit</option>
                </select>
              </div>
            </div>
          </div>          
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('COA*') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('coa_code') ? ' has-danger' : '' }} coa-wrapper">
                <select class="form-control {{ $errors->has('coa_code') ? ' is-invalid' : '' }}" name="coa_code" id="coa_code">
                  <option value="" disabled selected>Select your option</option>
                </select>
              </div>
            </div>
          </div> 
        </div>

        <div class="form-row double_coa">          
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('COA Kredit') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('coa_code_kredit') ? ' has-danger' : '' }} coa-kredit-wrapper">
                <select class="form-control {{ $errors->has('coa_code_kredit') ? ' is-invalid' : '' }}" name="coa_code_kredit" id="coa_code_kredit">
                  <option value="" disabled selected>Select your option</option>
                </select>
              </div>
            </div>
          </div>           
          <div class="form-group col-md-6">
            <label class="col-sm-6 col-form-label">{{ __('COA Debit') }}</label>
            <div class="col-sm-12">
              <div class="form-group{{ $errors->has('coa_code_debit') ? ' has-danger' : '' }} coa-debit-wrapper">
                <select class="form-control {{ $errors->has('coa_code_debit') ? ' is-invalid' : '' }}" name="coa_code_debit" id="coa_code_debit">
                  <option value="" disabled selected>Select your option</option>
                </select>
              </div>
            </div>
          </div>
          <p class="p-0 pl-3 text-danger">*Pilih salah satu untuk single input(<strong>COA Kredit</strong> atau <strong>COA Debit</strong>)</p>
        </div>
      </div>                   

    </div>
    <div class="card-footer ml-auto mr-auto">      
      <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Close</button>
      <button type="submit" class="btn button-link">{{ __('Save') }}</button>
    </div>
  </div>
</form>  