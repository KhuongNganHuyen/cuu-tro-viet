@extends('layouts.admin')

@section('title', 'Thành viên nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thành viên nhóm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admin/nhom-tinh-nguyen') }}">Nhóm tình nguyện</a></li>
          <li class="breadcrumb-item" aria-current="page">Thành viên</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-1">{{ $nhom->tenNhom }}</h5>
      <small class="text-muted">Danh sách thành viên thuộc nhóm tình nguyện</small>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ url('/admin/nhom-tinh-nguyen') }}" class="btn btn-secondary">
        Quay lại nhóm
      </a>
      <a href="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom . '/thanh-vien/create') }}" class="btn btn-primary">
        Thêm thành viên
      </a>
    </div>
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
            <th>Vai trò trong nhóm</th>
            <th>Ngày tham gia</th>
            <th style="width: 120px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($nhom->thanhViens as $thanhVien)
            <tr>
              <td>{{ $thanhVien->idThanhVien }}</td>
              <td>{{ $thanhVien->nguoiDung->hoTen ?? '-' }}</td>
              <td>{{ $thanhVien->nguoiDung->tenDangNhap ?? '-' }}</td>
              <td>{{ $thanhVien->nguoiDung->email ?? '-' }}</td>
              <td>{{ $thanhVien->vaiTro }}</td>
              <td>{{ $thanhVien->ngayThamGia ?? '-' }}</td>
              <td>
                <form action="{{ url('/admin/thanh-vien-nhom/' . $thanhVien->idThanhVien) }}" method="POST"
                  onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này khỏi nhóm không?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">Nhóm chưa có thành viên nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection