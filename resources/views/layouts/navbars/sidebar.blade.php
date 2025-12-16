<div class="sidebar pb-4" data-color="green" data-background-color="white" data-image="">
  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
  <div class="logo mx-4">
    <a href="" class="simple-text logo-normal">
      <img src="{{url('/images/RJ.jpeg')}}" alt="" width="50%">
    </a>
  </div>
  <div class="sidebar-wrapper" style="height: auto !important">
    <ul class="nav">

      <li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
        <a class="nav-link m-0" href="{{ route('dashboard') }}">
          <i class="material-icons">dashboard</i>
            <p>{{ __('Dashboard') }}</p>
        </a>
      </li>

      @if(in_array(Auth::user()->getRole(), ['admin']))
      <li class="nav-item{{ $activePage == 'user' ? ' active' : '' }}">
        <a class="nav-link m-0" href="{{ url('user') }}">
          <i class="material-icons">account_circle</i>
            <p>User</p>
        </a>
      </li>
      @endif

      @if(in_array(Auth::user()->getRole(), ['admin', 'finance']))
      <li class="nav-item {{ in_array($activePage, ['biaya', 'organisasi']) ? ' active' : '' }}">
        <a class="nav-link m-0 parent-list d-flex align-items-center {{ in_array($activePage, ['biaya', 'organisasi']) ? ' opened' : '' }}" href="#">
          <i class="material-icons">settings</i>
          <p>Settings</p>
        </a>
        <ul class="nav flex-column mt-0 pl-4" data-dropdown="settings" style="{{ in_array($activePage, ['biaya', 'organisasi']) ? '' : 'display:none' }}">
          @if(in_array(Auth::user()->getRole(), ['admin', 'finance']))
          <li class="nav-item{{ $activePage == 'biaya' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('settings/biaya') }}">
              <i class="material-icons">money</i>
              <p>Biaya</p>
            </a>
          </li>
          @endif
          @if(in_array(Auth::user()->getRole(), ['admin']))
          <li class="nav-item{{ $activePage == 'organisasi' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('settings/organisasi') }}">
              <i class="material-icons">business</i>
              <p>Organisasi</p>
            </a>
          </li>
          @endif
        </ul>
      </li>
      @endif

      @if(in_array(Auth::user()->getRole(), ['admin']))
      <li class="nav-item {{ in_array($activePage, ['staff', 'kesiswaan']) ? ' active' : '' }}">
        <a class="nav-link m-0 parent-list d-flex align-items-center {{ in_array($activePage, ['staff', 'kesiswaan']) ? ' opened' : '' }}" href="#">
          <i class="material-icons">format_list_bulleted</i>
          <p>Master</p>
        </a>
        <ul class="nav flex-column mt-0 pl-4" data-dropdown="inventory" style="{{ in_array($activePage, ['staff', 'kesiswaan']) ? '' : 'display:none' }}">
          @if(in_array(Auth::user()->getRole(), ['admin']))
          <li class="nav-item{{ $activePage == 'staff' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('master/staff') }}">
              <i class="material-icons">assignment_ind</i>
              <p>Staff</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'kesiswaan' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('master/kesiswaan') }}">
              <i class="material-icons">face</i>
              <p>Kesiswaan</p>
            </a>
          </li>
          @endif
        </ul>
      </li>
      @endif

      <!-- @if(in_array(Auth::user()->getRole(), [0,3,4]))
      <li class="nav-item{{ in_array($activePage,['purchase_order','purchase_order_receive','purchase_return']) ? ' active' : '' }}">
        <a class="nav-link m-0 parent-list d-flex align-items-center {{ in_array($activePage,['purchase_order','purchase_order_receive','purchase_return']) ? ' opened' : '' }}" href="#">
          <i class="material-icons">receipt_long</i>
          <p>Item Transaction</p>
        </a>
        <ul class="nav flex-column mt-0 pl-4" data-dropdown="item-transaction" style="{{ in_array($activePage,['purchase_order','purchase_order_receive','purchase_return']) ? '' : 'display:none' }}">
          <li class="nav-item{{ $activePage == 'purchase_order' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('item-transaction/purchase-order') }}">
              <i class="material-icons">shopping_cart</i>
              <p>Purchase Order</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'purchase_order_receive' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('item-transaction/purchase-order-receive') }}">
              <i class="material-icons">store</i>
              <p>Purchase Order Receive</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'purchase_return' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('item-transaction/purchase-order-return') }}">
              <i class="material-icons">local_shipping</i>
              <p>Purchase Order Return</p>
            </a>
          </li>
        </ul>
      </li>
      @endif

      @if(in_array(Auth::user()->getRole(), [0,3,4]))
      <li class="nav-item{{ in_array($activePage,['stock_information','stock_adjustment','stock_card']) ? ' active' : '' }}">
        <a class="nav-link m-0 parent-list d-flex align-items-center {{ in_array($activePage,['stock_information','stock_adjustment','stock_card']) ? ' opened' : '' }}" href="#">
          <i class="material-icons">inventory</i>
          <p>Stock</p>
        </a>
        <ul class="nav flex-column mt-0 pl-4" data-dropdown="stock" style="{{ in_array($activePage,['stock_information','stock_adjustment','stock_card']) ? '' : 'display:none' }}">
          <li class="nav-item{{ $activePage == 'stock_information' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('stock/stock-information') }}">
              <i class="material-icons">summarize</i>
              <p>Stock Information</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'stock_adjustment' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('stock/stock-adjustment') }}">
              <i class="material-icons">edit</i>
              <p>Stock Adjusment</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'stock_card' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('stock/stock-card') }}">
              <i class="material-icons">ballot</i>
              <p>Stock Card</p>
            </a>
          </li>
        </ul>
      </li>
      @endif

      @if(in_array(Auth::user()->getRole(), [0,4]))
      <li class="nav-item{{ $activePage == 'sales' ? ' active' : '' }}">
        <a class="nav-link m-0" href="{{ url('sales') }}">
          <i class="material-icons">point_of_sales</i>
            <p>Sales</p>
        </a>
      </li>
      @endif -->

      @if(in_array(Auth::user()->getRole(), ['admin','staff']))
      <li class="nav-item{{ $activePage == 'iuran' ? ' active' : '' }}">
        <a class="nav-link m-0" href="{{ url('iuran') }}">
          <i class="material-icons">receipt</i>
            <p>Iuran</p>
        </a>
      </li>
      @endif

      @if(in_array(Auth::user()->getRole(), ['admin', 'finance']))
      <li class="nav-item{{ $activePage == 'coa' ? ' active' : '' }}">
        <a class="nav-link m-0" href="{{ url('coa') }}">
          <i class="material-icons">library_books</i>
            <p>Chart of Accounts (COA)</p>
        </a>
      </li>
      @endif

      @if(in_array(Auth::user()->getRole(), ['admin', 'finance']))
      <li class="nav-item{{ $activePage == 'trans' ? ' active' : '' }}">
        <a class="nav-link m-0" href="{{ url('trans') }}">
          <i class="material-icons">receipt_long</i>
            <p>Transaction</p>
        </a>
      </li>
      @endif

      @if(in_array(Auth::user()->getRole(), ['admin', 'finance']))
      <li class="nav-item{{ $activePage == 'finance' ? ' active' : '' }}">
        <a class="nav-link m-0" href="{{ url('finance') }}">
          <i class="material-icons">price_check</i>
            <p>Finance</p>
        </a>
      </li>
      @endif

      <!-- @if(in_array(Auth::user()->getRole(), [0,2]))
      <li class="nav-item {{ $activePage == 'persentase' || $activePage == 'pembagian' || $activePage == 'shu_pengurus' ? ' active' : '' }}">
        <a class="nav-link m-0 parent-list d-flex align-items-center {{ $activePage == 'persentase' || $activePage == 'pembagian' || $activePage == 'shu_pengurus' ? ' opened' : '' }}" href="#">
          <i class="material-icons">monetization_on</i>
          <p>SHU</p>
        </a>
        <ul class="nav flex-column mt-0 pl-4" data-dropdown="inventory" style="{{ $activePage == 'persentase' || $activePage == 'pembagian' || $activePage == 'shu_pengurus' ? '' : 'display:none' }}">
          <li class="nav-item{{ $activePage == 'persentase' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('shu/persentase') }}">
              <i class="material-icons">percent</i>
              <p>Persentase</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'pembagian' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('shu/pembagian') }}">
              <i class="material-icons">list_alt</i>
              <p>Pembagian SHU</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'shu_pengurus' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('shu/pengurus') }}">
              <i class="material-icons">list_alt</i>
              <p>SHU Pengurus</p>
            </a>
          </li>
        </ul>
      </li>
      @endif -->
      
      @if(in_array(Auth::user()->getRole(), ['admin', 'finance']))
      <li class="nav-item {{ in_array($activePage, ['report-iuran', 'report-trans', 'report-aruskas']) ? ' active' : '' }}">
        <a class="nav-link m-0 parent-list d-flex align-items-center {{ in_array($activePage, ['report-iuran', 'report-trans', 'report-aruskas']) ? ' opened' : '' }}" href="#">
          <i class="material-icons">summarize</i>
          <p>Report</p>
        </a>
        <ul class="nav flex-column mt-0 pl-4" data-dropdown="report" style="{{ in_array($activePage, ['report-iuran', 'report-trans', 'report-aruskas']) ? '' : 'display:none' }}">
          @if(in_array(Auth::user()->getRole(), ['admin', 'finance']))
          <li class="nav-item{{ $activePage == 'report-iuran' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('report/iuran') }}">
              <i class="material-icons">summarize</i>
              <p>Iuran</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'report-trans' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('report/trans') }}">
              <i class="material-icons">summarize</i>
              <p>Transaction</p>
            </a>
          </li>
          <li class="nav-item{{ $activePage == 'report-aruskas' ? ' list-active' : '' }}">
            <a class="nav-link m-0" href="{{ url('report/aruskas') }}">
              <i class="material-icons">summarize</i>
              <p>Arus Kas</p>
            </a>
          </li>
          @endif
        </ul>
      </li>
      @endif

      @if(in_array(Auth::user()->getRole(), ['admin']))
      <!-- <li class="nav-item{{ $activePage == 'log' ? ' active' : '' }}">
        <a class="nav-link" href="{{ url('log') }}">
          <i class="material-icons">view_list</i>
            <p>Log</p>
        </a>
      </li> -->
      @endif
            
    </ul>
  </div>
</div>