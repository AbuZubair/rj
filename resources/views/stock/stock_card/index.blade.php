@extends('layouts.admin', ['activePage' => 'stock_card', 'titlePage' => __('Stock Card')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable">
            <thead>
                <tr>
                    <th>Kode Item</th>
                    <th>Item</th>
                    <th>Stok Masuk</th>                    
                    <th>Stok Keluar</th>                 
                    <th>Stok Sebelumnya</th>
                    <th>Stok Balance</th>
                    <th>Satuan</th>
                    <th>Transaksi</th>
                    <th>Created Date</th>
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
        ajax: "{{ route('stock-card.list') }}",
        columns: [
            {data: 'item_code', name: 'item_code'},
            {data: 'item_name', name: 'item.item_name'},
            {data: 'stock_in', name: 'stock_in', className: 'text-center'}, 
            {data: 'stock_out', name: 'stock_out', className: 'text-center'}, 
            {data: 'stock_before', name: 'stock_before', className: 'text-center'},
            {data: 'stock_balance', name: 'stock_balance', className: 'text-center'}, 
            {data: 'satuan', name: 'satuan'}, 
            {data: 'transaction_no', name: 'transaction_no'}, 
            {data: 'created_date', name: 'created_date'}, 
        ],
        buttons: [
          {
            extend: 'excel',
            title: 'Stock-Card_'+getDate()
          }
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'}
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