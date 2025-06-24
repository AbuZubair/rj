@extends('layouts.admin', ['activePage' => 'pengurus', 'titlePage' => __('Kepengurusan')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 mb-2">
        <button class="btn btn-primary" onClick="save()" >Simpan</button>
      </div>  
      <div class="col-lg-12 table-responsive">
        <div class="ml-auto mr-auto mt-5 pt-4 loader-container">
          <div class="loader"></div>
        </div>
        
        <table class="table pengurus-table" style="display:none">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Pengurus</th>
                    <th>Anggota</th>         
                    <th>Persentase (%)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <td>
                  <button class="btn btn-info" onClick="add()"><i class="material-icons">add</i></button>
                </td>  
              </tr>
            </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
  const body = $('.pengurus-table tbody')
  var select_anggota;
  $(document).ready(function(){

    getAnggota().then(() => {
      getData()
    })
    
  }); 
  
  function add(){
    const tr = $("<tr></tr>");
    const index =  body.find('tr').length
    var clone = select_anggota.clone();
    clone.attr('name', `input_pengurus[${index}]`).attr('id', `pengurus[${index}]`);
    var anggota = $(`<td></td>`).append(clone)
    tr.append($(`<td>${index+1}</td>`))
    .append($(`<td><input class='' name='input_label[${index}]' /></td>`))
    .append(anggota)
    .append($(`<td><input class='' name='input_persentase[${index}]' /></td>`))
    .append($(`<td><i class="material-icons" style="cursor:pointer" onClick="deleteRow(this)">remove_circle</i></td>`))
    clone = null;
    body.append(tr)   
    $('select').select2();
  }

  function deleteRow(el) {
    let id = $(el).parent().parent().index()
    const rows = body.find('tr')
    for (let index = id+1; index < rows.length; index++) {
      const element = rows[index];
      const no = parseInt($(element).find("td:first").text()) - 1
      $(element).find("td:first").text(no);
      $(element).find("td:eq(1) > input").attr('name', `input_label[${no-1}]`);
      $(element).find("td:eq(2) > select").attr('name', `input_pengurus[${no-1}]`);
      $(element).find("td:eq(3) > input").attr('name', `input_persentase[${no-1}]`);
    }
    rows[id].remove();
  }

  function getData(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/pengurus/list")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            for (let i = 0; i < data.length; i++) {
              const element = data[i];
              const tr = $("<tr></tr>");
              var clone = select_anggota.clone();
              clone.attr('name', element.value).attr('id', element.value);
              clone.val(element.no_anggota).trigger('change');
              var anggota = $(`<td></td>`).append(clone)
              tr.append($(`<td>${i+1}</td>`))
              .append($(`<td><input class='' name='${element.value}_label' value='${element.label}' /></td>`))
              .append(anggota)
              .append($(`<td><input class='' name='${element.value}_persentase' value='${element.persentase}' /></td>`))
              .append($(`<td><i class="material-icons" style="cursor:pointer" onClick="deleteRow(this)">remove_circle</i></td>`))
              clone = null;
              body.append(tr)        
            }
            $('select').select2();
            $('.loader-container').hide();
            $('.table').show();
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

  function getAnggota(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/anggota/dropdown")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            let list_anggota = jsonResponse.data;
            select_anggota =  $("<select></select>").addClass('form-control');
            select_anggota.append('<option value="" disabled selected>Select your option</option>')
            if(list_anggota){
              for (let index = 0; index < list_anggota.length; index++) {
                select_anggota.append($('<option>', { 
                  value: list_anggota[index].no_anggota,
                  text : `${list_anggota[index].no_anggota} ${list_anggota[index].fullname}`
                }));
              }
            }
            resolve()
          }else{
            reject()
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          reject()
        },
      })
    })   
  }

  function save() {
    let input = body.find("select,input");
    let data = {}; 
    for (let index = 0; index < input.length; index++) {
      const element = input[index];
      let value = $(element).val()
      let name = $(element).attr('name');
      let item = {};
      if(value == '' || value == null){
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
      url : "{{url('master/pengurus/save')}}",
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