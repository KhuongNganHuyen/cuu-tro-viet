@extends('layouts.nhom')

@section('title', 'Tổng quan nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tổng quan nhóm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">{{ $nhom->tenNhom }}</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body text-center">
        <h4 class="fw-bold mb-2">{{ $nhom->tenNhom }}</h4>

        <span class="d-inline-flex align-items-center justify-content-center gap-2 mb-3">
          <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
          {{ $nhom->trangThai }}
        </span>

        <p class="text-muted">
          {{ $nhom->moTa ?? 'Chưa có mô tả cho nhóm này.' }}
        </p>

        <div class="alert alert-info mb-0 text-start">
          <strong>Vai trò của bạn:</strong> {{ $vaiTroTrongNhom }}
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h6 class="mb-2 f-w-400 text-muted">Thành viên</h6>
            <h4 class="mb-3">{{ $thongKe['soThanhVien'] }}</h4>
            <p class="mb-0 text-muted text-sm">Số thành viên trong nhóm</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h6 class="mb-2 f-w-400 text-muted">Chiến dịch</h6>
            <h4 class="mb-3">{{ $thongKe['soChienDich'] }}</h4>
            <p class="mb-0 text-muted text-sm">Chiến dịch của nhóm</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h6 class="mb-2 f-w-400 text-muted">Yêu cầu tiếp nhận</h6>
            <h4 class="mb-3">{{ $thongKe['soYeuCauTiepNhan'] }}</h4>
            <p class="mb-0 text-muted text-sm">Yêu cầu nhóm đang xử lý</p>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Thông tin nhóm</h5>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4 text-muted">Mã nhóm</div>
          <div class="col-md-8">{{ $nhom->idNhom }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Nhóm trưởng</div>
          <div class="col-md-8">{{ $nhom->nhomTruong->hoTen ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Địa điểm</div>
          <div class="col-md-8">
            @if ($nhom->diaDiem)
              @if ($nhom->diaDiem->chiTietDiaDiem)
                {{ $nhom->diaDiem->chiTietDiaDiem }},
              @endif
              @if ($nhom->diaDiem->phuongXa)
                {{ $nhom->diaDiem->phuongXa }},
              @endif
              {{ $nhom->diaDiem->tinhThanh }}
            @else
              -
            @endif
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 text-muted">Ngày tạo</div>
          <div class="col-md-8">{{ $nhom->ngayTao ?? '-' }}</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection