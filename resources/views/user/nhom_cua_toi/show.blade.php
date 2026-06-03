@extends('layouts.user')

@section('title', 'Chi tiết đăng ký nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết đăng ký nhóm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm tình nguyện của tôi</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Chi tiết</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body text-center">
        <h4 class="fw-bold mb-2">{{ $nhom->tenNhom }}</h4>

        <span class="d-inline-flex align-items-center justify-content-center gap-2 mb-3">
          <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
          {{ $nhom->trangThai }}
        </span>

        <p class="text-muted">
          {{ $nhom->moTa ?? 'Chưa có mô tả cho nhóm này.' }}
        </p>

        <a href="{{ url('/user/nhom-cua-toi') }}" class="btn btn-secondary w-100">
          Quay lại
        </a>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Thông tin đăng ký</h5>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4 text-muted">Mã nhóm</div>
          <div class="col-md-8">{{ $nhom->idNhom }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Tên nhóm</div>
          <div class="col-md-8">{{ $nhom->tenNhom }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Người đăng ký</div>
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

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Trạng thái</div>
          <div class="col-md-8">{{ $nhom->trangThai }}</div>
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