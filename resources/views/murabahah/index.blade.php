@extends('layouts.admin', ['activePage' => 'murabahah', 'titlePage' => __('Akad Kredit')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable" delete-url="{{url('akad-kredit/delete')}}" edit-url="{{url('akad-kredit/edit')}}" data-modal="murabahahModal" data-checkbox="murabahahs">
            <thead>
                <tr>
                    <th class="text-center r-sort"><input class="ml-md-0 ml-4" type="checkbox" id="selectAll" value="selectAll" onClick="toggle(this)"/></th>
                    <th>No. Anggota</th>
                    <th>Anggota</th>
                    <th>No. Akad</th>
                    <th>Tanggal Akad</th>
                    <th>Tanggal Mulai Potong Angsuran</th>
                    <th>Tipe Kredit</th>
                    <th>Margin</th>
                    <th>Harga Total</th>
                    <th>Angsuran</th> 
                    <th>Total Pembayaran</th> 
                    <th>Sisa Piutang</th> 
                    <th>Status</th>     
                    <th>Desc</th>                 
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
<div class="modal fade modal-transaction" id="murabahahModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">      
    <div class="modal-content">
      <div class="modal-header pl-4 bg-info text-white">
        <h5 class="modal-title" id="murabahahModalTitle">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('murabahah.form')
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
   $(document).ready(function(){
       
    $('#type').select2({
      dropdownParent: $("#murabahahModal")
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

    const table = $('.yajra-datatable').DataTable({
        // responsive: true,
        processing: true,        
        serverSide: true,
        ajax: "{{ route('murabahah.list') }}",
        columns: [
            {data:null,render:function(data,type,full,meta){
                let disabled = (data.status>0)?'disabled':''
                return '<input class="ml-md-0 ml-4" type="checkbox" name="murabahahs" value="'+data.id+'" '+disabled+' onClick="singleToggle()"/>';                    
              },
              
            },
            {data: 'no_anggota', name: 'no_anggota'},
            {data: 'fullname', name: 'fullname'},
            {data: 'no_murabahah', name: 'no_murabahah'},
            {data: 'date_trans', name: 'date_trans'},
            {data: 'date', render:function(data,type,full,meta){
                const d = new Date(date);
                return data;
              }
            },
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
            {data: 'angsuran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'nilai_pembayaran', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: null, render:function(data,type,full,meta){              
                if(data){
                  const sisa = data.nilai_total - data.nilai_pembayaran;
                  return formatCurrency(sisa)
                }
                  return '0'
              }
            },
            {data:null,name: 'status', render:function(data,type,full,meta){
                return (data.status==0)?"New":(data.status==1)?"Berjalan":"Selesai"                    
              }
            },
            {data: 'desc', name: 'desc'},
            {data:null,render:function(data,type,full,meta){
                const disabled = (data.status!=0)?'disabled':'';
                return '<button class="btn btn-sm btn-success" '+disabled+' onClick="edit('+data.id+')">Edit</button>';                    
              }
            },
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'},
          {
            visible: false,
            targets: [4,11,13]
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
              columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
              total_index: [7,8,9,10]
            },
            title: 'KOPERASI KARYAWAN PRASADHA MAKMUR SEJAHTERA Kredit_'+getDate()
          }
        ],
        dom: 'B<"toolbar">lfrtip'
    });

    var btn = '<button class="btn btn-primary" onClick="add();generateTransNo()"><i class="material-icons">add</i> Add</button>'
    btn += '<button class="btn btn-danger" onClick="deleteRow()"><i class="material-icons">delete</i> Delete</button>'
    btn += '<button class="btn btn-info" onClick="checkUpdate()">Check dan Update</button>'     
    $("div.toolbar").attr('class','mb-4').html(btn);
    $("div.dt-buttons").addClass('float-right')
        
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
            for (let index = 0; index < data.length; index++) {
              $('#no_anggota').append($('<option>', { 
                value: data[index].no_anggota,
                text : `${data[index].no_anggota} ${data[index].fullname}` 
              }));
            }
                   
            $('#no_anggota').select2({
                dropdownParent: $("#murabahahModal")
            });

            for (let index = 0; index < 12; index++) {
              $('#margin').append($('<option>', { 
                value: index+1,
                text : index+1 
              }));
            }

            $('#margin').select2({
                dropdownParent: $("#murabahahModal")
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
    $('input[name=angsuran]').val((Math.round(harga_total/tenor)).toFixed(2).toString().replaceAll('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, "."))
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

  function checkUpdate() {
    return new Promise((resolve,reject) => {
      $.ajax({
        url : '{{url("akad-kredit/check-update")}}',
        type: 'GET',
        data: '',
        beforeSend: function() {
          showNotification('Loading..','warning',1000)
        },
        success: function(data) {
          var jsonResponse = JSON.parse(data);
          if(jsonResponse.status){
            $.notifyClose();
            showNotification(jsonResponse.message,'success');
          }else{
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
  
</script>
@endpush