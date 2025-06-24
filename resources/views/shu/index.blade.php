@extends('layouts.admin', ['activePage' => 'pembagian', 'titlePage' => __('Pembagian SHU')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tahun</th>
                    <th>Uraian</th>                    
                    <th>Persentase(%)</th>
                    <th>Total</th>
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
   $(document).ready(function(){
    var groupCol = 3;
    const table = $('.yajra-datatable').DataTable({
        responsive: true,
        processing: true,        
        serverSide: true,
        paging: false,
        ajax: "{{ route('shu.pembagian.list') }}",
        columns: [
            {data:null,name:''},
            {data: 'year', name: 'year'},
            {data: 'label', name: 'label'},
            {data: 'persentase', name: 'persentase'},
            {data: 'total', render:function(data,type,full,meta){
                if(data)return formatCurrency(data)
                  return '0'
              }
            }
        ],
        columnDefs: [
          {"targets": 0, "orderable": false, "className": 'text-center'}
        ],
        order: [],
        dom: "B<'clear'><'H'r>t<'F'>",
        buttons: [
          {
            extend: 'excel',
            exportOptions: {
              columns: [ 1,2,3,4 ],
              total_index: [3]
            },
            title: 'PembagianSHU_'+getDate()
          }
        ],
        drawCallback: function (settings) {
            var that = this;
            if (settings.bSorted || settings.bFiltered) {
                this.$('td:first-child', {
                    "filter": "applied"
                }).each(function (i) {
                    that.fnUpdate(i + 1, this.parentNode, 0, false, false);
                });
            }

            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var rowsData = api.rows({
                page: 'current'
            }).data();

            var last = null;
            var subTotal = new Array();
            var grandTotal = new Array();
            var groupID = -1;
            var length = api.column(groupCol, {page: 'current' }).data().length

            api.column(groupCol, {
                page: 'current'
            }).data().each(function (group, i) {
                $.each($(rows).eq(i), function (colIndex, colValue) {
                  $(this).find('td').eq(0).html(i+1)
                })     
                var last_ = (last)?last.toLowerCase().trim():last
                var group_ = (group)?group.toLowerCase().trim():group
                if (last_ !== group_) {      
                    groupID++;
                    last = group;                       
                }
                if(i==length-1){
                  var html = "<tr class='grandTotal'><td colspan='4'>Grand Total : </td></tr>"
                  $(rows).eq(i).after(html);                    
                }

                //Sub-total of each column within the same grouping
                var val = api.row(api.row($(rows).eq(i)).index()).data(); //Current order index              
                $.each(val, function (colIndex, colValue) {
                    if (typeof subTotal[groupID] == 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] == 'undefined') {
                        subTotal[groupID][colIndex] = 0;
                    }
                    if (typeof grandTotal[colIndex] == 'undefined') {
                        grandTotal[colIndex] = 0;
                    }

                    value = colValue ? parseFloat(colValue) : 0;
                    subTotal[groupID][colIndex] += value;
                    grandTotal[colIndex] += value;
                });
            });      

            $('#dynamic-table tbody').find('.grandTotal').each(function (i, v) { 
                var TotalInfo = "";                          
                TotalInfo += "<td class='groupTDTot'>" + formatCurrency(grandTotal.total.toFixed(2)) + "</td>"; 
                $(this).append(TotalInfo);
            });
                                  
        }
    });
        
  });  
  
</script>
@endpush