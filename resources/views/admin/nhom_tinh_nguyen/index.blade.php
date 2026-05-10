@extends('layouts.admin')

@section('title', 'Quản lý nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý nhóm tình nguyện</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item" aria-current="page">Nhóm tình nguyện</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Danh sách nhóm tình nguyện</h5>
    <a href="{{ url('/admin/nhom-tinh-nguyen/create') }}" class="btn btn-primary">Thêm nhóm</a>
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
            <th>Tên nhóm</th>
            <th>Nhóm trưởng</th>
            <th>Địa điểm</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th style="width: 160px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($nhomTinhNguyens as $nhom)
            <tr>
              <td>{{ $nhom->idNhom }}</td>
              <td>{{ $nhom->tenNhom }}</td>
              <td>{{ $nhom->nhomTruong->hoTen ?? '-' }}</td>
              <td>
                {{ $nhom->diaDiem->tinhThanh ?? '-' }}
                @if (!empty($nhom->diaDiem?->phuongXa))
                  - {{ $nhom->diaDiem->phuongXa }}
                @endif
              </td>
              <td>{{ $nhom->trangThai }}</td>
              <td>{{ $nhom->ngayTao ?? '-' }}</td>
              <td>
                <a href="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom . '/thanh-vien') }}" class="btn btn-sm btn-info">Thành viên</a>
                <a href="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom . '/edit') }}" class="btn btn-sm btn-warning">Sửa</a>
                <form action="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Bạn có chắc muốn xóa nhóm tình nguyện này không?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">Chưa có nhóm tình nguyện nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection