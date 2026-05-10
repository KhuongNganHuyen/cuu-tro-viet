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
    <h5 class="mb-0">Danh sách người dùng</h5>
    <a href="{{ url('/admin/nguoi-dung/create') }}" class="btn btn-primary">Thêm người dùng</a>
  </div>

  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th>Mã</th>
            <th>Họ tên</th>
            <th>Tên đăng nhập</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th style="width: 160px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($nguoiDungs as $nguoiDung)
            <tr>
              <td>{{ $nguoiDung->idNguoiDung }}</td>
              <td>{{ $nguoiDung->hoTen }}</td>
              <td>{{ $nguoiDung->tenDangNhap }}</td>
              <td>{{ $nguoiDung->email ?? '-' }}</td>
              <td>{{ $nguoiDung->sdt ?? '-' }}</td>
              <td>{{ $nguoiDung->vaiTro }}</td>
              <td>{{ $nguoiDung->trangThai }}</td>
              <td>
                <a href="{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung . '/edit') }}" class="btn btn-sm btn-warning">Sửa</a>

                <form action="{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này không?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center">Chưa có người dùng nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection