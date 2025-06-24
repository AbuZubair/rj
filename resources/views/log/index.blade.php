@extends('layouts.admin', ['activePage' => 'log', 'titlePage' => __('Log')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12" style="overflow-x:auto !important">
        <table id="dynamic-table" class="table yajra-datatable" style="width: 100%;">
            <thead>
                <tr>
                    <th width="10%">ID</th>
                    <th width="70%">Message</th>
                    <th width="20%">Date</th>                    
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
    
    var table = $('.yajra-datatable').DataTable({
        processing: true,        
        serverSide: true,
        ajax: "{{ route('log.list') }}",
        columns: [
            {data: 'rowID', name: 'rowID'},
            {data: 'message', name: 'message'},
            {data: 'created_date', name: 'created_date'}          
        ],
        order: [[2,'desc']],
        columnDefs: [
          {"targets": 1, "className": 'break-word'}
        ],
    });
        
  });  
</script>
@endpush