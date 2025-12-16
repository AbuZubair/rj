@extends('layouts.admin', ['activePage' => 'dashboard', 'titlePage' => 'Dashboard'])

@section('content')

  <div class="content">
    <div class="container-fluid">
      <button class="btn btn-primary" onClick="naikKelas()" >Ajaran Baru</button>
      @if(!in_array(Auth::user()->getRole(), [3,4]))
        <!-- <div class="row">
          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
              <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                  <h3 class="m-3">P</h3>
                </div>
                <p class="card-category">Total Iuran Pokok</p>
                <div class="loader float-right"></div>
                <h4 class="card-title iuran-pokok"></h4>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">date_range</i> <span class="last-iuranpokok"> Sampai tahun berjalan (<span id="cur-year"></span>)</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
              <div class="card-header card-header-success card-header-icon">
                <div class="card-icon">
                  <h3 class="m-3">W</h3>
                </div>
                <p class="card-category">Total Iuran Wajib</p>
                <div class="loader float-right"></div>
                <h4 class="card-title iuran-wajib"></h4>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">date_range</i> <span class="last-iuranwajib"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
              <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                  <h3 class="m-3">T</h3>
                </div>
                <p class="card-category">Tabungan Hari Raya</p>
                <div class="loader float-right"></div>
                <h4 class="card-title thr"></h4>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">date_range</i> <span class="last-thr"></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card card-stats">
              <div class="card-header card-header-danger card-header-icon">
                <div class="card-icon float-none position-absolute">
                  <h3 class="m-3">KB</h3>
                </div>

                <section style="padding-left: 5.5rem;">
                  <p class="card-category">Kredit Barang</p>
                  <div class="loader float-right"></div>
                  <h4 class="card-title kredit-barang"></h4>          
                  <h4 class="card-title sisa-kredit-barang"></h4>
                </section>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">date_range</i> <span class="last-kb"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card card-stats">
              <div class="card-header card-header-danger card-header-icon">
                <div class="card-icon float-none position-absolute">
                  <h3 class="m-3">KJ</h3>
                </div>
                <section style="padding-left: 5.5rem;">
                  <p class="card-category">Kredit Jasa</p>
                  <div class="loader float-right"></div>
                  <h4 class="card-title kredit-jasa"></h4>          
                  <h4 class="card-title sisa-kredit-jasa"></h4>
                </section>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">date_range</i> <span class="last-kj"></span>
                </div>
              </div>
            </div>
          </div>
        </div> -->
      @endif   

      <!-- <div class="row">
        <div class="{{ !in_array(Auth::user()->getRole(), [3,4])?'col-md-6':'col-md-12' }}">
          <div class="card card-chart">
            <div class="card-header card-header-success">
              <div class="ct-chart" id="dailySalesChart"></div>
            </div>
            <div class="card-body">
              <h4 class="card-title">Sales Toko</h4>
              <p class="card-category">
                <span class="text-success"><i class="fa fa-long-arrow-up" id="growth-sales"></i> <span class="percentage-sales"></span> </span> <span class="desc-sales"></span>
              </p>
            </div>
            <div class="card-footer">
              <div class="stats">
                <i class="material-icons">access_time</i> last transaction <span class="last-update mx-1"></span> ago
              </div>
            </div>
          </div>
        </div>
        <div class="{{ !in_array(Auth::user()->getRole(), [3,4])?'col-md-6':'col-md-12' }}">
          <div class="card card-chart">
            <div class="card-header card-header-info">
              <div class="ct-chart" id="dailyPurchaseChart"></div>
            </div>
            <div class="card-body">
              <h4 class="card-title">Purchase</h4>
              <p class="card-category">Pembelian sampai bulan ini</p>
            </div>
            <div class="card-footer">
              <div class="stats">
              <i class="material-icons">access_time</i> last transaction <span class="last-update-purchase mx-1"></span> ago
              </div>
            </div>
          </div>
        </div>
      </div> -->

      @if(!in_array(Auth::user()->getRole(), [3,4]))
      <!-- <div class="row">
        <div class="col-md-12">
          <div class="card card-chart">
            <div class="card-header card-header-danger">
              <div class="ct-chart" id="completedTasksChart"></div>
            </div>
            <div class="card-body">
              <h4 class="card-title">Pertumbuhan Anggota</h4>
              <p class="card-category">Data per tahun</p>
            </div>
            <div class="card-footer">
              <div class="stats">
                <i class="material-icons">access_time</i> latest data
              </div>
            </div>
          </div>
        </div>
      </div> -->
      @endif 

    </div>
  </div>

@endsection

@push('js')
  <script>
    var table;
    var role = "{{Auth::user()->getRole()}}"
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      let role = "{{Auth::user()->getRole()}}"
      md.initDashboardPageCharts();
      $('#cur-year').text((new Date()).getFullYear())

      if(!['3','4'].includes(role)){
        // getCardData(1).then((data) => {
        //   let pokok = (data)?formatCurrency(data.total):0
        //   $('.iuran-pokok').text('Rp '+ pokok )
        //   $('.iuran-pokok').prev().hide()
        // });
        // getCardData(0).then((data) => {
        //   let wajib = (data)?formatCurrency(data.total):0
        //   $('.iuran-wajib').text('Rp '+ wajib )
        //   $('.last-iuranwajib').text((data && data!=0)?'Per '+formattingDate(data.month, data.year):'-')
        //   $('.iuran-wajib').prev().hide()
        // });
        // getCardData(4).then((data) => {
        //   let thr = (data)?formatCurrency(data.total):0
        //   $('.thr').text('Rp '+ thr )
        //   $('.last-thr').text((data && data!=0)?'Per '+formattingDate(data.month, data.year):'-')
        //   $('.thr').prev().hide()
        // });

        // $(".separate-col").css({
        //     "visibility": "hidden"
        // });
        // getCreditSummary().then((data) => {
          
        //   const total_brg = data.find(v => v.type === 0);
        //   let kredit_brg = (total_brg != null)?formatCurrency(total_brg.total):0
        //   let sisa_kredit_barang = (total_brg != null)?formatCurrency(total_brg.total_sisa):0

        //   $('.kredit-barang').text('Total: Rp '+ kredit_brg )
        //   $('.last-kb').text((kredit_brg && kredit_brg!=0)?'Per '+ new Date().getFullYear():'-')
        //   $('.kredit-barang').prev().hide()

        //   $('.sisa-kredit-barang').text('O/S: Rp '+ sisa_kredit_barang )

        //   const total_jasa = data.find(v => v.type === 1);
        //   let kredit_jasa = (total_jasa != null)?formatCurrency(total_jasa.total):0
        //   let sisa_kredit_jasa = (total_jasa != null)?formatCurrency(total_jasa.total_sisa):0

        //   $('.kredit-jasa').text('Total: Rp '+ kredit_jasa )
        //   $('.last-kj').text((kredit_jasa && kredit_jasa!=0)?'Per '+ new Date().getFullYear():'-')
        //   $('.kredit-jasa').prev().hide()

        //   $('.sisa-kredit-jasa').text('O/S: Rp '+ sisa_kredit_jasa )

        //   $(".separate-col").css({
        //     "visibility": "visible"
        //   });
        // })
      }
    });

    function getCardData(type) {
      return new Promise((resolve,reject) => {
        $.ajax({
          url : '{{url("dashboard/get-card-data")}}',
          data: {data:type},
          type: 'GET',
          success: function(data) {
            var jsonResponse = JSON.parse(data);
            if(jsonResponse.status){
              var data = jsonResponse.data
              resolve(data)
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

    function getCreditSummary(type) {
      return new Promise((resolve,reject) => {
        $.ajax({
          url : '{{url("dashboard/get-credit-summary")}}',
          type: 'GET',
          success: function(data) {
            var jsonResponse = JSON.parse(data);
            if(jsonResponse.status){
              var data = jsonResponse.data
              resolve(data)
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

    function naikKelas() {
      $.confirm({
        title: "Ajaran Baru",
        content: `Anda yakin akan memulai ajaran baru?`,
        buttons: {
            confirm: function () {
              $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
              }); 
              $.ajax({
                url : "{{url('dashboard/ajaran-baru')}}",
                type: 'POST',
                beforeSend: function() {
                  showNotification('Loading..','warning',null,true);
                },
                success: function(data) {
                  $.notifyClose();
                  var jsonResponse = JSON.parse(data);
                  if(jsonResponse.status){
                    showNotification(jsonResponse.message,'success');
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