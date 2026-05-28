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

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Danh sách người dùng</h5>
    </div>

    <a href="{{ url('/admin/nguoi-dung/create') }}" class="btn btn-primary">
      Thêm
    </a>
  </div>

  <div class="card-body">
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

    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 80px;">Mã</th>
            <th class="text-start">Họ tên</th>
            <th class="text-start">Tên đăng nhập</th>
            <th class="text-start">Email</th>
            <th style="width: 130px;">SĐT</th>
            <th style="width: 160px;">Vai trò</th>
            <th style="width: 160px;">Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($nguoiDungs as $nguoiDung)
            <tr onclick="window.location='{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung) }}'"
                style="cursor: pointer;">
              <td class="text-center">{{ $nguoiDung->idNguoiDung }}</td>

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
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                Chưa có người dùng nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection