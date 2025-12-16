@extends('layouts.admin', ['activePage' => 'finance', 'titlePage' => __('Finance')])

@push('css')
<style>
  .info-sec{
    font-size: 10px !important;
  }
</style>
@endpush

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">

        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <form class="searchForm">
                <div class="card-header">
                  <h4>Closing Bulanan</h4>
                </div>
                <div class="card-body row">
                  <div class="col-sm-12 align-items-center">
                    <label class="col-sm-12 col-form-label">{{ __('Bulan') }}</label>
                    <div class="col-sm-12">
                      <div class="form-group">                      
                        <select class="form-control" name="monthClosing" id="monthClosing">
                          <option value="" disabled selected>Select your option</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12 align-items-center">
                    <label class="col-sm-12 col-form-label">{{ __('Tahun') }}</label>
                    <div class="col-sm-12">
                      <div class="form-group">                      
                        <select class="form-control" name="yearClosing" id="yearClosing">
                          <option value="" disabled selected>Select your option</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12 align-items-center thin-text">
                    <span class="col-sm-12 d-flex info-sec">
                      <p>Closing terakhir: &nbsp; </p>
                      <span class="last-closing"></span>
                      <p class="closing-terakhir ml-2">| Pada tgl: &nbsp; </p>
                      <span class="tgl-closing-terakhir"></span>
                    </span>
                  </div>
                </div> 
              </form>
              <div class="card-footer" style="justify-content: end !important;">
                <button class="btn btn-primary" onClick="submit(0,'{{url('finance/submit-closing')}}')" >Submit</button>
              </div>           
            </div> 
          </div>

          <div class="col-md-6">
            <div class="card">
              <form class="searchForm">
                <div class="card-header">
                  <h4>Tutup Buku</h4>
                </div>
                <div class="card-body row">
                  <div class="col-sm-12 align-items-center">
                    <label class="col-sm-12 col-form-label">{{ __('Tahun') }}</label>
                    <div class="col-sm-12">
                      <div class="form-group">                      
                        <select class="form-control" name="yearsClosing" id="yearsClosing">
                          <option value="" disabled selected>Select your option</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12 align-items-center thin-text">
                    <span class="col-sm-12 d-flex">
                      <p>Closing terakhir: &nbsp; </p>
                      <span class="year-last-closing"></span>
                    </span>
                  </div>   
                </div> 
              </form>
              <div class="card-footer" style="justify-content: start !important;">
                <div class="col-sm-12">
                  <button class="btn btn-primary" onClick="yearClosing()" >Submit</button>
                </div>
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
  let table;
  const month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
  const mth = new Date().getMonth();
  const year = new Date().getFullYear();
  $(document).ready(function(){
       
    getYearList();
    getLastClosing();
    getLastYearClosing();

    for(var i=0;i<month.length;i++){
      $('#monthClosing').append($('<option>', { 
        value: month[i],
        text : month[i] 
      }));
      $('#monthClosing').select2();
      $('#monthClosing').val(month[mth]).trigger("change");
    }
  });  

  function getYearList() {
    //get list year from 2016 until current year plus one
    var yearList = [];
    for(var i=year-3;i<=year;i++){
      $('#yearClosing').append($('<option>', { 
        value: i,
        text : i 
      }));
      $('#yearClosing').select2();
      $('#yearClosing').val(year).trigger("change");

      $('#yearsClosing').append($('<option>', { 
        value: i,
        text : i 
      }));
      $('#yearsClosing').select2();
      $('#yearsClosing').val(year).trigger("change");
    }
 
  }

  function getLastClosing() {
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("finance/get-last-closing")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            if(data){
              const formattedMth = parseInt(data.month);
              const formattedYear = parseInt(data.year);
              const requiredMonth = formattedMth === 12 ? 1 : formattedMth + 1;
              const requiredYear = formattedMth === 12 ? formattedYear + 1 : formattedYear;
              $('#monthClosing').val(month[requiredMonth - 1]).trigger("change");
              $('#yearClosing').val(requiredYear).trigger("change");
            }
            $('.last-closing').text((data)?`${month[parseInt(data.month)-1]}-${data.year}`:'-')
            if(!data){
              $('.closing-terakhir').hide()
            }else{
              $('.closing-terakhir').show()
              $('.tgl-closing-terakhir').text((new Date(data.created_date)).toLocaleString("id-ID"))
            }
            resolve()
          }else{
            reject()
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
          reject()
        },
      })
    }) 
  }

  function getLastYearClosing() {
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("finance/get-last-year-closing")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $('.year-last-closing').text((data)?`${data}`:'-')
            if(data==0){
              $('.closing-terakhir').hide()
            }else{
              $('.closing-terakhir').show()
              $('.tgl-closing-terakhir').text((new Date(data.created_date)).toLocaleString("id-ID"))

              const requiredYear = parseInt(data) + 1;
              $('#yearsClosing').select2();
              $('#yearsClosing').val(requiredYear > year ? year : requiredYear).trigger("change");
            }
            resolve()
          }else{
            reject()
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
          reject()
        },
      })
    }) 
  }

  function submit(type,url) {
    let month = (type === 0)?'#monthClosing':'';
    let year = (type === 0)?'#yearClosing':'';
    if(!$(month).val() || !$(year).val()){
      showNotification('Silahkan pilih Bulan dan Tahun', 'danger');
      return false;
    }
    let title = null;
    if(type === 0){
      title = "Closing Bulanan"
    }
    $.confirm({
      title: title,
      content: `Bulan/Tahun: ${$(month).val()}/${$(year).val()} `,
      buttons: {
          confirm: function () {
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            }); 
            $.ajax({
              url : url,
              type: 'POST',
              data : {month: $(month).val(), year: $(year).val()},
              beforeSend: function() {
                showNotification('Loading..','warning',1000)
              },
              success: function(data) {
                $.notifyClose();
                var jsonResponse = JSON.parse(data);
                if(jsonResponse.status){
                  showNotification(jsonResponse.message,'success');
                  if(type === 0){                   
                    getLastClosing()
                  }
                }else{
                  showNotification(jsonResponse.message, 'danger');
                }
              },
              error: function(xhr) { // if error occured
                $.notifyClose();
                var msg = xhr.responseJSON.message
                showNotification(msg,'danger')
              },
            })
          },
          cancel: function () {
            return;
          },
      }
    }); 
  }

  function yearClosing() {
    $.confirm({
      title: "Tutup Buku Tahunan",
      content: `Apakah anda yakin?`,
      buttons: {
          confirm: function () {
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            }); 
            $.ajax({
              url : "{{url('finance/submit-year-closing')}}",
              type: 'POST',
              data : {year: $('#yearsClosing').val()},
              beforeSend: function() {
                showNotification('Loading..','warning',10000)
              },
              success: function(data) {
                $.notifyClose();
                var jsonResponse = JSON.parse(data);
                if(jsonResponse.status){
                  showNotification(jsonResponse.message,'success');
                  getLastYearClosing();
                }else{
                  showNotification(jsonResponse.message, 'danger');
                }
              },
              error: function(xhr) { // if error occured
                $.notifyClose();
                var msg = xhr.responseJSON.message
                showNotification(msg,'danger')
              },
            })
          },
          cancel: function () {
            return;
          },
      }
    });
  }

</script>
@endpush