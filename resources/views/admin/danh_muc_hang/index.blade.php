@extends('layouts.admin')

@section('title', 'Quản lý danh mục hàng | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý danh mục hàng</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Danh mục hàng</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Danh sách danh mục hàng</h5>
    <a href="{{ url('/admin/danh-muc-hang/create') }}" class="btn btn-primary">
      Thêm danh mục
    </a>
  </div>

  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th style="width: 100px;">Mã</th>
            <th>Tên danh mục hàng</th>
            <th style="width: 160px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($danhMucHangs as $danhMucHang)
            <tr>
              <td>{{ $danhMucHang->idDanhMucHang }}</td>
              <td>{{ $danhMucHang->tenDanhMucHang }}</td>
              <td>
                <a href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/edit') }}" class="btn btn-sm btn-warning">
                  Sửa
                </a>

                <form action="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Bạn có chắc muốn xóa danh mục hàng này không?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">
                    Xóa
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center">Chưa có danh mục hàng nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection