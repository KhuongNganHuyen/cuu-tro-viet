<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>@yield('title', 'Cứu Trợ Việt')</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
  <meta name="keywords" content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
  <meta name="author" content="CodedThemes">

  <!-- [Favicon] icon -->
  <link rel="icon" href="{{ asset('mantis/assets') }}/images/favicon.svg" type="image/x-icon"> <!-- [Google Font] Family -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('mantis/assets') }}/fonts/tabler-icons.min.css" >
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('mantis/assets') }}/fonts/feather.css" >
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('mantis/assets') }}/fonts/fontawesome.css" >
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('mantis/assets') }}/fonts/material.css" >
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('mantis/assets') }}/css/style.css" id="main-style-link" >
<link rel="stylesheet" href="{{ asset('mantis/assets') }}/css/style-preset.css" >

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
  <!-- [ Pre-loader ] start -->
<div class="loader-bg">
  <div class="loader-track">
    <div class="loader-fill"></div>
  </div>
</div>
<!-- [ Pre-loader ] End -->
<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ url('/admin/dashboard') }}" class="b-brand text-primary">
        <span class="fw-bold fs-5 text-primary">Cứu Trợ Việt</span>
      </a>
    </div>

    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item">
          <a href="{{ url('/admin/dashboard') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
            <span class="pc-mtext">Tổng quan</span>
          </a>
        </li>

        <li class="pc-item pc-caption">
          <label>Quản lý tài khoản</label>
          <i class="ti ti-users"></i>
        </li>

        <li class="pc-item">
          <a href="{{ url('/admin/nguoi-dung') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-user"></i></span>
            <span class="pc-mtext">Người dùng</span>
          </a>
        </li>

        <li class="pc-item">
          <a href="{{ url('/admin/nhom-tinh-nguyen') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-users"></i></span>
            <span class="pc-mtext">Nhóm tình nguyện</span>
          </a>
        </li>

        <li class="pc-item pc-caption">
          <label>Quản lý cứu trợ</label>
          <i class="ti ti-heart-handshake"></i>
        </li>

        <li class="pc-item">
          <a href="{{ url('/admin/chien-dich') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-flag"></i></span>
            <span class="pc-mtext">Chiến dịch cứu trợ</span>
          </a>
        </li>

        <li class="pc-item">
          <a href="{{ url('/admin/yeu-cau-cuu-tro') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-alert-circle"></i></span>
            <span class="pc-mtext">Yêu cầu cứu trợ</span>
          </a>
        </li>

        <li class="pc-item pc-caption">
          <label>Danh mục hệ thống</label>
          <i class="ti ti-category"></i>
        </li>

        <li class="pc-item">
          <a href="{{ url('/admin/dia-diem') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-map-pin"></i></span>
            <span class="pc-mtext">Địa điểm</span>
          </a>
        </li>

        <li class="pc-item">
          <a href="{{ url('/admin/su-kien-cuu-tro') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-alert-triangle"></i></span>
            <span class="pc-mtext">Sự kiện cứu trợ</span>
          </a>
        </li>

        <li class="pc-item">
          <a href="{{ url('/admin/danh-muc-hang') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-package"></i></span>
            <span class="pc-mtext">Danh mục hàng</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper">
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>

        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>

        <li class="pc-h-item d-none d-md-inline-flex">
          <form class="header-search"
                action="{{ url()->current() }}"
                method="GET"
                autocomplete="off">
            <i data-feather="search" class="icon-search"></i>

            <input type="search"
                  name="tuKhoa"
                  class="form-control"
                  placeholder="Tìm kiếm..."
                  value="{{ request('tuKhoa') }}"
                  autocomplete="off">
          </form>
        </li>
      </ul>
    </div>

    @php
      $anhDaiDien = session('anhDaiDien');
      $duongDanAvatar = $anhDaiDien
        ? asset('storage/' . $anhDaiDien)
        : asset('mantis/assets/images/user/avatar-2.jpg');

      $hoTen = session('hoTen', 'Người dùng');
      $vaiTro = session('vaiTro', 'Chưa xác định');
    @endphp

    @php
      $idNguoiDungDangNhap = session('idNguoiDung');
      $vaiTroDangNhap = session('vaiTro', 'Người dùng');

      $thongBaoHeader = \App\Models\ThongBao::where('trangThai', 'Hiển thị')
          ->where(function ($query) use ($idNguoiDungDangNhap, $vaiTroDangNhap) {
              $query->where('doiTuong', 'Tất cả')
                  ->orWhere('doiTuong', $vaiTroDangNhap)
                  ->orWhere('idNguoiNhan', $idNguoiDungDangNhap);
          })
          ->orderBy('idThongBao', 'desc')
          ->take(3)
          ->get();
    @endphp

    <div class="ms-auto">
      <ul class="list-unstyled">
        <li class="dropdown pc-h-item">
          <a
            class="pc-head-link dropdown-toggle arrow-none me-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            aria-expanded="false"
          >
            <i class="ti ti-bell"></i>
          </a>

          <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
              <h5 class="m-0">Thông báo</h5>
              <a href="#!" class="pc-head-link bg-transparent">
                <i class="ti ti-x text-danger"></i>
              </a>
            </div>

            <div class="dropdown-divider"></div>

            <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative"
              style="max-height: calc(100vh - 215px)">
              <div class="list-group list-group-flush w-100">
                @forelse ($thongBaoHeader as $thongBao)
                  <a href="{{ url('/thong-bao?mo=' . $thongBao->idThongBao) }}"
                    class="list-group-item list-group-item-action">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        @if (!empty($thongBao->anhDaiDien))
                          <img src="{{ asset('storage/' . $thongBao->anhDaiDien) }}"
                              alt="avatar"
                              class="rounded-circle border"
                              style="width: 48px; height: 48px; object-fit: cover;">
                        @else
                          <div class="avtar avtar-s bg-light-primary">
                            <i class="ti ti-bell"></i>
                          </div>
                        @endif
                      </div>

                      <div class="flex-grow-1 ms-2">
                        <p class="text-body mb-1">
                          {{ $thongBao->tieuDe }}
                        </p>

                        <span class="text-muted">
                          {{ $thongBao->doiTuong }} ·
                          {{ $thongBao->thoiGianTao
                              ? \Carbon\Carbon::parse($thongBao->thoiGianTao)->diffForHumans()
                              : '' }}
                        </span>
                      </div>
                    </div>
                  </a>
                @empty
                  <div class="text-center text-muted py-3">
                    Chưa có thông báo nào.
                  </div>
                @endforelse
              </div>

            <div class="dropdown-divider"></div>

            <div class="text-center py-2">
              <a href="{{ url('/thong-bao') }}" class="link-primary">Xem tất cả</a>
            </div>
          </div>
        </li>

        <li class="dropdown pc-h-item header-user-profile">
          <a
            class="pc-head-link dropdown-toggle arrow-none me-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            data-bs-auto-close="outside"
            aria-expanded="false"
          >
            <img src="{{ $duongDanAvatar }}" alt="user-image" class="user-avtar">
            <span>{{ $hoTen }}</span>
          </a>

          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header">
              <div class="d-flex mb-1">
                <div class="flex-shrink-0">
                  <img src="{{ $duongDanAvatar }}" alt="user-image" class="user-avtar wid-35">
                </div>

                <div class="flex-grow-1 ms-3">
                  <h6 class="mb-1">{{ $hoTen }}</h6>
                  <span>{{ $vaiTro }}</span>
                </div>

                <a href="#"
                  class="pc-head-link bg-transparent"
                  onclick="event.preventDefault(); document.getElementById('logout-form-user').submit();">
                  <i class="ti ti-power text-danger"></i>
                </a>
              </div>
            </div>

            <ul class="nav drp-tabs nav-fill nav-tabs" id="mydrpTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link active"
                  id="drp-t1"
                  data-bs-toggle="tab"
                  data-bs-target="#drp-tab-1"
                  type="button"
                  role="tab"
                  aria-controls="drp-tab-1"
                  aria-selected="true">
                  <i class="ti ti-user"></i> Tài khoản
                </button>
              </li>

              <li class="nav-item" role="presentation">
                <button
                  class="nav-link"
                  id="drp-t2"
                  data-bs-toggle="tab"
                  data-bs-target="#drp-tab-2"
                  type="button"
                  role="tab"
                  aria-controls="drp-tab-2"
                  aria-selected="false">
                  <i class="ti ti-settings"></i> Cài đặt
                </button>
              </li>
            </ul>

            <div class="tab-content" id="mysrpTabContent">
              <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel" aria-labelledby="drp-t1" tabindex="0">
                <a href="{{ url('/ho-so') }}" class="dropdown-item">
                  <i class="ti ti-user"></i>
                  <span>Hồ sơ cá nhân</span>
                </a>

                <a href="{{ url('/doi-mat-khau') }}" class="dropdown-item">
                  <i class="ti ti-lock"></i>
                  <span>Đổi mật khẩu</span>
                </a>

                <a href="#"
                  class="dropdown-item"
                  onclick="event.preventDefault(); document.getElementById('logout-form-user').submit();">
                  <i class="ti ti-power"></i>
                  <span>Đăng xuất</span>
                </a>
              </div>

              <div class="tab-pane fade" id="drp-tab-2" role="tabpanel" aria-labelledby="drp-t2" tabindex="0">
                <a href="#!" class="dropdown-item">
                  <i class="ti ti-bell"></i>
                  <span>Thiết lập thông báo</span>
                </a>

                <a href="#!" class="dropdown-item">
                  <i class="ti ti-shield-lock"></i>
                  <span>Bảo mật tài khoản</span>
                </a>

                <a href="#!" class="dropdown-item">
                  <i class="ti ti-help"></i>
                  <span>Hỗ trợ</span>
                </a>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>
<!-- [ Header ] end -->

<form id="logout-form-user" action="{{ url('/logout') }}" method="POST" class="d-none">
  @csrf
</form>

  <!-- [ Main Content ] start -->
<div class="pc-container">
  <div class="pc-content">
    @yield('content')
  </div>
</div>
  <!-- [ Main Content ] end -->
  <footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
      <div class="row">
        <div class="col-sm my-1">
          <p class="m-0"
            >Mantis &#9829; crafted by Team <a href="https://themeforest.net/user/codedthemes" target="_blank">Codedthemes</a> Distributed by <a href="https://themewagon.com/">ThemeWagon</a>.</p
          >
        </div>
        <div class="col-auto my-1">
          <ul class="list-inline footer-link mb-0">
            <li class="list-inline-item"><a href="../index.html">Home</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <!-- [Page Specific JS] start -->
  <script src="{{ asset('mantis/assets') }}/js/plugins/apexcharts.min.js"></script>
  <script src="{{ asset('mantis/assets') }}/js/pages/dashboard-default.js"></script>
  <!-- [Page Specific JS] end -->
  <!-- Required Js -->
  <script src="{{ asset('mantis/assets') }}/js/plugins/popper.min.js"></script>
  <script src="{{ asset('mantis/assets') }}/js/plugins/simplebar.min.js"></script>
  <script src="{{ asset('mantis/assets') }}/js/plugins/bootstrap.min.js"></script>
  <script src="{{ asset('mantis/assets') }}/js/fonts/custom-font.js"></script>
  <script src="{{ asset('mantis/assets') }}/js/pcoded.js"></script>
  <script src="{{ asset('mantis/assets') }}/js/plugins/feather.min.js"></script>

  
  
  
  
  <script>layout_change('light');</script>
  
  
  
  
  <script>change_box_container('false');</script>
  
  
  
  <script>layout_rtl_change('false');</script>
  
  
  <script>preset_change("preset-1");</script>
  
  
  <script>font_change("Public-Sans");</script>
  
    

</body>
<!-- [Body] end -->

</html>