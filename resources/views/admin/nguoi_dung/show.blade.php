@extends('layouts.admin')

@section('title', 'Chi tiết người dùng | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết người dùng</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/nguoi-dung') }}">Người dùng</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Chi tiết</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $nguoiDung->hoTen }}</h5>

    <div class="d-flex gap-2">
      <a href="{{ url('/admin/nguoi-dung') }}" class="btn btn-secondary">
        Quay lại
      </a>

      <a href="{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung . '/edit') }}" class="btn btn-warning">
        Sửa
      </a>

      <form action="{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung) }}" method="POST"
        onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này không?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
          Xóa
        </button>
      </form>
    </div>
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-4 text-muted">Mã người dùng</div>
      <div class="col-md-8">{{ $nguoiDung->idNguoiDung }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Họ tên</div>
      <div class="col-md-8">{{ $nguoiDung->hoTen }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Tên đăng nhập</div>
      <div class="col-md-8">{{ $nguoiDung->tenDangNhap }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Email</div>
      <div class="col-md-8">{{ $nguoiDung->email ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Số điện thoại</div>
      <div class="col-md-8">{{ $nguoiDung->sdt ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Giới tính</div>
      <div class="col-md-8">{{ $nguoiDung->gioiTinh ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Ngày sinh</div>
      <div class="col-md-8">{{ $nguoiDung->ngaySinh ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Vai trò</div>
      <div class="col-md-8">{{ $nguoiDung->vaiTro }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Trạng thái</div>
      <div class="col-md-8">
        @if ($nguoiDung->trangThai == 'Hoạt động')
          <span class="d-inline-flex align-items-center gap-2">
            <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
            Hoạt động
          </span>
        @else
          <span class="d-inline-flex align-items-center gap-2">
            <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
            {{ $nguoiDung->trangThai }}
          </span>
        @endif
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4 text-muted">Ngày tạo</div>
      <div class="col-md-8">{{ $nguoiDung->ngayTao ?? '-' }}</div>
    </div>
  </div>
</div>
@endsection