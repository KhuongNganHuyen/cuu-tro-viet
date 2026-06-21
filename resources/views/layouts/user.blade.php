<!DOCTYPE html>
<html lang="vi">
<head>
  <title>@yield('title', 'Người dùng | Cứu Trợ Việt')</title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <link rel="icon" href="{{ asset('mantis/assets/images/favicon.svg') }}" type="image/x-icon">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
  <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/tabler-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/fontawesome.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/material.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/assets/css/style.css') }}" id="main-style-link">
  <link rel="stylesheet" href="{{ asset('mantis/assets/css/style-preset.css') }}">
</head>

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  <nav class="pc-sidebar">
    <div class="navbar-wrapper">
      <div class="m-header">
        <a href="{{ url('/user/dashboard') }}" class="b-brand text-primary">
          <span class="fw-bold fs-5 text-primary">Cứu Trợ Việt</span>
        </a>
      </div>

      <div class="navbar-content">
        <ul class="pc-navbar">
          <li class="pc-item {{ request()->is('user/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/user/dashboard') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
              <span class="pc-mtext">Tổng quan</span>
            </a>
          </li>

          <li class="pc-item pc-caption">
            <label>Khám phá cứu trợ</label>
            <i class="ti ti-world"></i>
          </li>

          <li class="pc-item {{ request()->is('user/nhom-tinh-nguyen*') ? 'active' : '' }}">
            <a href="{{ url('/user/nhom-tinh-nguyen') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-users"></i></span>
              <span class="pc-mtext">Nhóm tình nguyện</span>
            </a>
          </li>

          <li class="pc-item {{ request()->is('user/chien-dich*') ? 'active' : '' }}">
            <a href="{{ url('/user/chien-dich') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-flag"></i></span>
              <span class="pc-mtext">Chiến dịch cứu trợ</span>
            </a>
          </li>

          <li class="pc-item {{ request()->is('user/yeu-cau-cong-dong*') ? 'active' : '' }}">
            <a href="{{ url('/user/yeu-cau-cong-dong') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-alert-circle"></i></span>
              <span class="pc-mtext">Yêu cầu cộng đồng</span>
            </a>
          </li>

          <li class="pc-item pc-caption">
            <label>Hoạt động của tôi</label>
            <i class="ti ti-heart-handshake"></i>
          </li>

          <li class="pc-item {{ request()->is('user/yeu-cau-cuu-tro*') ? 'active' : '' }}">
            <a href="{{ url('/user/yeu-cau-cuu-tro') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-file-plus"></i></span>
              <span class="pc-mtext">Yêu cầu của tôi</span>
            </a>
          </li>

          <li class="pc-item {{ request()->is('user/dong-gop*') ? 'active' : '' }}">
            <a href="{{ url('/user/dong-gop') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-gift"></i></span>
              <span class="pc-mtext">Đóng góp của tôi</span>
            </a>
          </li>

          <li class="pc-item {{ request()->is('user/nhom-cua-toi*') ? 'active' : '' }}">
            <a href="{{ url('/user/nhom-cua-toi') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-user-check"></i></span>
              <span class="pc-mtext">Nhóm của tôi</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

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
        $vaiTro = session('vaiTro', 'Người dùng');
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

              <ul class="nav drp-tabs nav-fill nav-tabs" id="userDrpTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link active"
                    id="user-drp-t1"
                    data-bs-toggle="tab"
                    data-bs-target="#user-drp-tab-1"
                    type="button"
                    role="tab"
                    aria-controls="user-drp-tab-1"
                    aria-selected="true">
                    <i class="ti ti-user"></i> Tài khoản
                  </button>
                </li>

                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link"
                    id="user-drp-t2"
                    data-bs-toggle="tab"
                    data-bs-target="#user-drp-tab-2"
                    type="button"
                    role="tab"
                    aria-controls="user-drp-tab-2"
                    aria-selected="false">
                    <i class="ti ti-settings"></i> Cài đặt
                  </button>
                </li>
              </ul>

              <div class="tab-content" id="userDrpTabContent">
                <div class="tab-pane fade show active" id="user-drp-tab-1" role="tabpanel" aria-labelledby="user-drp-t1" tabindex="0">
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

                <div class="tab-pane fade" id="user-drp-tab-2" role="tabpanel" aria-labelledby="user-drp-t2" tabindex="0">
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

<form id="logout-form-user" action="{{ url('/logout') }}" method="POST" class="d-none">
  @csrf
</form>

  <div class="pc-container">
    <div class="pc-content">
      @yield('content')
    </div>
  </div>

  <footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
      <div class="row">
        <div class="col my-1">
          <p class="m-0">Cứu Trợ Việt</p>
        </div>
        <div class="col-auto my-1">
          <p class="m-0">Home</p>
        </div>
      </div>
    </div>
  </footer>

  <script src="{{ asset('mantis/assets/js/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('mantis/assets/js/plugins/simplebar.min.js') }}"></script>
  <script src="{{ asset('mantis/assets/js/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('mantis/assets/js/fonts/custom-font.js') }}"></script>
  <script src="{{ asset('mantis/assets/js/pcoded.js') }}"></script>
  <script src="{{ asset('mantis/assets/js/plugins/feather.min.js') }}"></script>

  <script>layout_change('light');</script>
  <script>change_box_container('false');</script>
  <script>layout_rtl_change('false');</script>
  <script>preset_change("preset-1");</script>
  <script>font_change("Public-Sans");</script>
</body>
</html>