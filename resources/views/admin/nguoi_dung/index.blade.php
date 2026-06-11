@extends('layouts.admin')

@section('title', 'Quản lý người dùng | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý người dùng</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item" aria-current="page">Người dùng</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

@if (request('tuKhoa'))
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      Đang tìm kiếm:
      <strong>{{ request('tuKhoa') }}</strong>
    </div>

    <a href="{{ url('/admin/nguoi-dung') }}" class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">Danh sách người dùng</h5>
      <small class="text-muted">
        Tổng hiển thị: {{ $nguoiDungs->count() }}
      </small>
    </div>

    <a href="{{ url('/admin/nguoi-dung/create') }}" class="btn btn-primary">
      Thêm
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 nguoi-dung-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 80px;">Mã</th>
            <th class="text-start">Họ tên</th>
            <th class="text-start">Tên đăng nhập</th>
            <th class="text-start">Email</th>
            <th style="width: 130px;">SĐT</th>
            <th style="width: 150px;">Vai trò</th>
            <th style="width: 150px;">Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($nguoiDungs as $nguoiDung)
            <tr class="nguoi-dung-row {{ session('nguoiDungMoi') == $nguoiDung->idNguoiDung ? 'table-primary' : '' }}"
                onclick="window.location='{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung) }}'">
              <td class="text-center fw-semibold">
                {{ $nguoiDung->idNguoiDung }}
              </td>

              <td>
                <div class="fw-semibold">{{ $nguoiDung->hoTen }}</div>
              </td>

              <td>{{ $nguoiDung->tenDangNhap }}</td>

              <td>{{ $nguoiDung->email ?? '-' }}</td>

              <td class="text-center">{{ $nguoiDung->sdt ?? '-' }}</td>

              <td class="text-center">
                {{ $nguoiDung->vaiTro }}
              </td>

              <td class="text-center">
                @if ($nguoiDung->trangThai == 'Hoạt động')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Hoạt động</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>{{ $nguoiDung->trangThai }}</span>
                  </span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy người dùng phù hợp.
                @else
                  Chưa có người dùng nào.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .nguoi-dung-table th,
  .nguoi-dung-table td {
    vertical-align: middle;
  }

  .nguoi-dung-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .nguoi-dung-row:hover {
    background-color: #f5f7fb;
  }
</style>
@endsection