@extends('layouts.admin', ['activePage' => 'biaya', 'titlePage' => __('Biaya')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row"> 
      <div class="col-lg-12 table-responsive">
        <div class="ml-auto mr-auto mt-5 pt-4 loader-container">
          <div class="loader"></div>
        </div>
        
        <table class="table settings-table" style="display:none">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Konfigurasi</th>
                    <th>Value</th>
                    <th></th>         
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
  const body = $('.settings-table tbody')
  var datas;
  $(document).ready(function(){

    getData().then((data) => {
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        const tr = $("<tr></tr>");
        const input = $(`<input class='form-control'  data-type='currency' name='${element.param}_${element.param1}' value='${formatCurrency(element.value)}' />`)
        input.on({
            keyup: function() {
              formatingCurrency($(this));
            },
        })
        tr.append($(`<td>${i+1}</td>`))
        .append($(`<td>${element.param.replace(/^_*(.)|_+(.)/g, (s, c, d) => c ? c.toUpperCase() : ' ' + d.toUpperCase())} ${element.param1.toUpperCase()} ${element.param2!=null?"(Kelas "+element.param2.toUpperCase()+")":""}</td>`))
        .append($(`<td></td>`).append(input))
        .append($(`<td><button class='btn btn-primary' onClick='save("${element.param}","${element.param1}")'>Simpan</button></td>`))
        body.append(tr)        
      }
      $('.loader-container').hide();
      $('.table').show();
    });

  }); 

  function getData(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("settings/biaya/list")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            datas = jsonResponse.data
            resolve(jsonResponse.data)
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

  function save(key, key2) {
    let input = $('input[name='+key+'_'+key2+']');
    let data = {}; 
    // for (let index = 0; index < input.length; index++) {
      // const element = input[index];
      let value = $(input).val()
      let item = {};
      if(value == '' || value == null){
        showNotification('Silahkan isi semua field','danger')
        return;
      }
      const find = datas.find(dt => dt.param === key && dt.param1 === key2);
      if(find && find.value !== value.replace(/\./g, "")){
        data[`${key}-${key2}`] = value;
      }else{
        return;
      }
    // }
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    $.ajax({
      url : "{{url('settings/biaya/save')}}",
      type: 'POST',
      data : data,
      beforeSend: function() {
        showNotification('Loading..','warning',1000)
      },
      success: function(data) {
        $.notifyClose();
        var jsonResponse = JSON.parse(data);
        var msg = jsonResponse.message
        if(jsonResponse.status){
          showNotification(msg,'success');
          getData();
        }else{ 
          showNotification(msg,'danger')
        }
        
      },
      error: function(xhr) { // if error occured
        $.notifyClose();
        var msg = xhr.responseJSON.message
        showNotification(msg,'danger')
      },
    })
  }
  
</script>
@endpush