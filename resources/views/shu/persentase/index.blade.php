@extends('layouts.admin', ['activePage' => 'persentase', 'titlePage' => __('Persentase')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 mb-2">
        <button class="btn btn-primary" onClick="save()" >Simpan</button>
      </div>  
      <div class="col-lg-12 table-responsive">
        <table class="table persentase-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Uraian</th>                    
                    <th>Persentase (%)</th>
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
  const body = $('.persentase-table tbody')
  $(document).ready(function(){

    getData()

  });  

  function getData(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("shu/persentase/get-data")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            for (let i = 0; i < data.length; i++) {
              const element = data[i];
              const tr = $("<tr></tr>");
              tr.append($(`<td>${i+1}</td>`)).append($(`<td>${element.label}</td>`)).append($(`<td><input class='' name='${element.value}' value='${element.persentase}' /></td>`))
              body.append(tr)        
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

  function save() {
    let input = body.find("input");
    let data = {}; 
    for (let index = 0; index < input.length; index++) {
      const element = input[index];
      let value = $(element).val()
      let name = $(element).attr('name');
      let item = {};
      if(value == ''){
        showNotification('Silahkan isi semua field','danger')
        return;
      }
      data[name] = value;
    }
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    $.ajax({
      url : "{{url('shu/persentase/save-persentase')}}",
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
          // body.find('tr').remove();
          // getData();
          showNotification(msg,'success')
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