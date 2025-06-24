@extends('layouts.admin', ['activePage' => 'stock_information', 'titlePage' => __('Stock Information')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Item</th>
                    <th>Balance</th>
                    <th>Satuan Beli</th>                    
                    <th>Satuan Jual</th> 
                    <th>Konversi</th>  
                    <th>Harga Beli</th>
                    <th>Harga Beli per Satuan Jual</th>                   
                    <th>Harga Jual</th>
                    <th>HPP (Per tahun)</th>
                    <th>Update Terakhir</th>
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
  let table;
   $(document).ready(function(){

    table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        retrieve: true,
        ajax: "{{ route('stock-information.list') }}",
        columns: [
            {data: 'item_code', name: 'item_code'},
            {data: 'item_name', name: 'item.item_name'},
            {data: 'balance', name: 'balance'}, 
            {data: 'satuan_beli', name: 'item.satuan_beli'},
            {data: 'satuan', name: 'satuan'},  
            {data: 'konversi', name: 'item.konversi'},  
            {data: 'harga_beli', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: null, render:function(data,type,full,meta){
                if(data){
                  let harga = Math.round(data.harga_beli / data.konversi)
                  return formatCurrency(harga)
                }else{
                  return '0'
                }
              }
            }, 
            {data: 'harga_jual', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'hpp', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            },
            {data: 'updated_date', name: 'updated_date'}, 
        ],
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              total_index: [6,7,8,9],
              wo_grand: true
            },
            title: 'Stock-Information_'+getDate()
          }
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'},
          {"targets": 6, "searchable": false},
          {"targets": 7, "searchable": false},
          {"targets": 8, "searchable": false},
          {"targets": 9, "searchable": false}
        ],
        order: [],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        dom: "B<'clear'>flrtip"
    });
        
  });  

  
</script>
@endpush