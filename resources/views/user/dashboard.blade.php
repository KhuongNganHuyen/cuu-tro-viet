@extends('layouts.user')

@section('title', 'Tổng quan người dùng | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tổng quan</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Tổng quan</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Yêu cầu cứu trợ</h6>
        <h4 class="mb-3">{{ $thongKe['soYeuCau'] }}</h4>
        <p class="mb-0 text-muted text-sm">Số yêu cầu bạn đã gửi</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Đóng góp</h6>
        <h4 class="mb-3">{{ $thongKe['soDongGop'] }}</h4>
        <p class="mb-0 text-muted text-sm">Số lần bạn đăng ký đóng góp</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Nhóm tham gia</h6>
        <h4 class="mb-3">{{ $thongKe['soNhomThamGia'] }}</h4>
        <p class="mb-0 text-muted text-sm">Nhóm tình nguyện bạn tham gia</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Nhóm chờ duyệt</h6>
        <h4 class="mb-3">{{ $thongKe['soNhomChoDuyet'] }}</h4>
        <p class="mb-0 text-muted text-sm">Yêu cầu tạo nhóm đang chờ</p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Hoạt động của tôi</h5>
      </div>

      <div class="card-body">
        <p class="text-muted">
          Đây là khu vực dành cho người dân và nhà hảo tâm. Bạn có thể gửi yêu cầu cứu trợ,
          đăng ký đóng góp cho chiến dịch hoặc đăng ký thành lập nhóm tình nguyện.
        </p>

        <div class="d-flex flex-wrap gap-2">
          <a href="{{ url('/user/yeu-cau-cuu-tro/create') }}" class="btn btn-primary">
            Gửi yêu cầu cứu trợ
          </a>

          <a href="{{ url('/user/dong-gop/create') }}" class="btn btn-outline-primary">
            Đăng ký đóng góp
          </a>

          <a href="{{ url('/user/dang-ky-nhom') }}" class="btn btn-outline-secondary">
            Đăng ký tạo nhóm
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Ghi chú</h5>
      </div>

      <div class="card-body">
        <p class="text-muted mb-0">
          Sau khi đăng nhập, hệ thống sẽ chỉ hiển thị dữ liệu thuộc về tài khoản của bạn.
          Hiện tại trang này đang dựng khung giao diện trước khi gắn chức năng đăng nhập.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection