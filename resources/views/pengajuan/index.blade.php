@extends('layouts.admin', ['activePage' => 'pengajuan', 'titlePage' => __('Pengajuan')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('pengajuan/delete')}}" edit-url="{{url('pengajuan/edit')}}" data-modal="pengajuanModal" data-checkbox="pengajuans">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input class="ml-md-0 ml-4" type="checkbox" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>No. Anggota</th>
                    <th>Anggota</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tipe Kredit</th>
                    <th>Margin</th>
                    <th>Harga Total</th>
                    <th>Harga Pokok</th>
                    <th>Angsuran</th> 
                    <th>Detail</th>
                    <th>Status</th>                 
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade modal-transaction" id="pengajuanModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="pengajuanModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"
      >
        @if(Auth::user()->getRole() == 1)
          @include('pengajuan.form')
        @endif
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="pengajuanDetailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="pengajuanDetailModalTitle">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @if(Auth::user()->getRole() == 0)
          @include('murabahah.form')
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
  const role = "{{Auth::user()->getRole()}}"
  var table;
   $(document).ready(function(){
       
    $('#type').select2({
      dropdownParent: (role==1)?$("#pengajuanModal"):$("#pengajuanDetailModal")
    });

    getDropdownList();

    $('select[name=type]').on('change', function() {
      $(".form-content").show()
      if(this.value == 0){
        $(".content-barang").show()
        $(".content-barang input:not(.optional-input), .content-barang select").prop('required',true)
        $(".content-jasa").hide();
        $(".content-jasa input:not(.optional-input), .content-jasa select").prop('required',false)
        $(".content-jasa input, .content-jasa select").val('').trigger("change");
      }else{
        $(".content-barang").hide()
        $(".content-barang input, .content-barang select").prop('required',false)
        $(".content-barang input, .content-barang select").val('').trigger("change");
        $(".content-jasa").show();
        $(".content-jasa input:not(.optional-input), .content-jasa select").prop('required',true)
      }
    })

    $('.modal-transaction').on('hidden.bs.modal', function (e) {
      hideAngsuran(e)
    })

    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        ajax: "{{ route('pengajuan.list') }}",
        columns: [
            {data:null,render:function(data,type,full,meta){
                let disabled = (data.status>0)?'disabled':''
                return '<input class="ml-md-0 ml-4" type="checkbox" name="pengajuans" value="'+data.id+'" '+disabled+' onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'no_anggota', name: 'no_anggota'},
            {data: 'fullname', name: 'fullname'},
            {data: 'date', name: 'date'},
            {data: 'type', render:function(data,type,full,meta){
                return data == 0? 'Barang':'Jasa';
              }
            },
            {data: null, name: 'margin' , render:function(data,type,full,meta){
                return data.type==0?`${data.margin}%`:'-';
              }
            },
            {data: 'nilai_total', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'nilai_awal', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'angsuran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'desc', name: 'desc'},
            {data:null,name: 'status', render:function(data,type,full,meta){
                return (data.status==0)?"New":(data.status==1)?"Approved":"Rejected"                    
              }
            },
            {data:null,render:function(data,type,full,meta){
                let html = '<div class="d-flex">'
                if(role==1){
                  const disabled = (data.status!=0)?'disabled':'';
                  html += '<button class="btn btn-sm btn-success" '+disabled+' onClick="edit('+data.id+')"><i class="material-icons">edit</i></button>';
                }else{
                  html += '<button class="btn btn-sm btn-info" onClick="openDetail('+data.id+')"><i class="material-icons">visibility</i></button>';                 
                }
                html += `<a href="/pengajuan/download?id=${data.id}" class="btn btn-sm btn-primary" target="_blank"><i class="material-icons">print</i></a>`
                html += '</div>'
                return html;                    
              }
            },
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'},
          {
            visible: false,
            targets: [7]
          },
        ],
        order: [],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
              total_index: [5,6,7]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Pengajuan_'+getDate()
          }
        ],
        dom: 'B<"toolbar">lfrtip'
    });

    let btn = '';
    if(role == 1){
      btn += '<button class="btn btn-primary" onClick="add();generateTransNo()"><i class="material-icons">add</i> Add</button>'  
    }
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'    
    $("div.toolbar").attr('class','mb-4').html(btn);
    if(role==0){
      $("div.dt-buttons").addClass('float-right')
    }else{
      $("div.dt-buttons").addClass('d-none')
    }
   
        
  }); 
  
  function generateTransNo() {
    const d = new Date();
    const el = $('input[name=no_murabahah]')
    el.val(`KRD${d.getFullYear()+("0" + (d.getMonth() + 1)).slice(-2)}${String(Date.now()).slice(-4)}`);
    el.prop('readonly', true);

    $('#type').val(0).trigger('change');
  }

  function getDropdownList(){
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("master/anggota/dropdown")}}',
        type: 'GET',
        data: '',
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            if(role==1){
              let anggota = "{{Auth::user()->getNoAnggota()}}";
              data = data.filter(item => item.no_anggota == anggota)
            }
            for (let index = 0; index < data.length; index++) {
              $('#no_anggota').append($('<option>', { 
                value: data[index].no_anggota,
                text : `${data[index].no_anggota} ${data[index].fullname}` 
              }));
            }
                   
            $('#no_anggota').select2({
              dropdownParent: (role==1)?$("#pengajuanModal"):$("#pengajuanDetailModal")
            });
            

            for (let index = 0; index < 12; index++) {
              $('#margin').append($('<option>', { 
                value: index+1,
                text : index+1 
              }));
            }

            $('#margin').select2({
                dropdownParent: (role==1)?$("#pengajuanModal"):$("#pengajuanDetailModal")
            });
 
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

  function viewAngsuran(e) {
    e.preventDefault()
    if($('input[name=nilai_awal]').val() == ''){
      showNotification('Silahkan input Harga Item','danger')
      return;
    }

    if($('select[name=margin]').val() == null){
      showNotification('Silahkan pilih Tenor Cicilan','danger')
      return;
    }

    const harga = parseInt($('input[name=nilai_awal]').val().replaceAll('.', ''));
    const tenor = $('select[name=margin]').val()
    const harga_total = harga + (harga*tenor/100)

    $('input[name=margin_view]').val(`${tenor}%`)
    $('input[name=nilai_total]').val(harga_total.toString().replace(".00","").replace(/\B(?=(\d{3})+(?!\d))/g, "."))
    $('input[name=angsuran]').val((harga_total/tenor).toFixed(2).toString().replaceAll('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, "."))
    $('.autogenerate-sec').show()
    $('.show-angsuran').hide()
    $('.hide-angsuran').show()
  }

  function hideAngsuran(e) {
    e.preventDefault()
    $('.autogenerate-sec').hide()
    $('.show-angsuran').show()
    $('.hide-angsuran').hide()
  }

  function approval(code, id) {
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    let params = {
      code,
      id,
    }
    if(role==0){
      params.desc = $('#desc').val();
      params.nilai_total = $('#nilai_total_jasa').val();
    }
    if(code == 1){
      const that = $('.form-admin')    
      var post_url = that.attr("action");
      var request_method = that.attr("method");
      var form_data = that.serialize();
      var redirect = that.attr("data-redirect")
      var wording = (that.attr("data-wording")!=undefined)?that.attr("data-wording"):'';
      var data_wording;
      if(wording!=''){
        data_wording = {
          id: $('input[name=id]').val(),
          wording: CKEDITOR.instances['konten'].getData()
        }

        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        }); 
      }
            
      $.ajax({
        url : post_url,
        type: request_method,
        data : (wording!='')?data_wording:form_data,
        beforeSend: function() {
          showNotification('Loading..','warning')
        },
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status === 200){
            updateStatus(params,code);               
          }else{
            $.notifyClose();
            if(typeof jsonResponse.message === 'object' && jsonResponse.message.constructor === Object){
              var html = '';
              var msg = jsonResponse.message[Object.keys(jsonResponse.message)[0]];
              msg.forEach(element => {
                html += element + '<br>'
              });
              showNotification(html, 'danger',0);
              resetList()
            }else{
              showNotification(jsonResponse.message, 'danger',0);
            }          
          }
        },
        error: function(xhr) { // if error occured
          $.notifyClose();
          if(xhr.responseJSON.errors){            
            var html = '';
            var err = xhr.responseJSON.errors
            for (var key in err) {
                if (err.hasOwnProperty(key)) {
                    html += err[key] + '<br>'
                }
            }       
            showNotification(html, 'danger');
          }else{
            var msg = xhr.responseJSON.message
            showNotification(msg,'danger')
          }        
        },
      })
    }else{
      updateStatus(params,code);
    }
  }

  function updateStatus(params,code){
    $.ajax({
        url : '{{url("pengajuan/approval")}}',
        type: 'POST',
        data : params,
        beforeSend: function() {
          if(code != 1){
            showNotification('Loading..','warning',1000)
          }          
        },
        success: function(data) {
          $.notifyClose();
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            showNotification(jsonResponse.message,'success');
            $('#pengajuanDetailModal').modal('hide')
            table.ajax.reload();
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
  }

  function openDetail(id) {
    const no_murabahah = generateTransNo();
    $.ajax({
        url : '{{url("pengajuan/edit")}}',
        type: 'GET',
        data: {id},
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            var data = jsonResponse.data
            $("#no_murabahah").val(no_murabahah)
            $("#no_anggota").val(data.no_anggota).trigger("change")
            $("#type").val(data.type).trigger("change")
            $("#nilai_awal").val(formatting(data.nilai_awal))
            $("#margin").val(data.margin).trigger("change")
            $("#desc").val(data.desc)
            $('#pengajuanDetailModal').modal()
            $("#pengajuanDetailModal form").attr("data-redirect","{{ route('pengajuan') }}")
            $("#pengajuanDetailModal #nilai_transport").attr("readonly", false)
            $("#pengajuanDetailModal #nilai_total_jasa").attr("readonly", false)
            $("#pengajuanDetailModal .card-body").append($("<input type='hidden' value='"+data.id+"' name='pengajuan_id'/>"))
            $("#pengajuanDetailModal .card-footer").children("button:not(:first)").remove()
            const reject = $("<button type='button'></button>").addClass("btn btn-danger mr-2").text("Reject")
            const approve = $("<button type='button'></button>").addClass("btn btn-info").text("Approve")
            if(data.status != 0){
              reject.prop('disabled', true);
              approve.prop('disabled', true);
            }
            reject.on('click', function() {
              approval(2,data.id)
            })
            approve.on('click', function() {
              approval(1,data.id)
            })
            $("#pengajuanDetailModal .card-footer").append(reject).append(approve)
          }else{
            showNotification(jsonResponse.message, 'danger');
          }
        },
        error: function(xhr) { // if error occured
          var msg = xhr.responseJSON.message
          showNotification(msg,'danger')
        },
      })
  }

  function generateTransNo() {
    const d = new Date();
    return `KRD${d.getFullYear()+("0" + (d.getMonth() + 1)).slice(-2)}${String(Date.now()).slice(-4)}`;
  }
</script>
@endpush