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
    <div>
      <h5 class="mb-0">Danh sách danh mục hàng</h5>
    </div>

    <a href="{{ url('/admin/danh-muc-hang/create') }}" class="btn btn-primary">
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
          <tr class="text-uppercase">
            <th class="text-center" style="width: 90px;">Mã</th>
            <th>Tên danh mục hàng</th>
            <th class="text-center" style="width: 120px;"></th>
          </tr>
        </thead>

        <tbody>
          @forelse ($danhMucHangs as $danhMucHang)
            <tr>
              <td class="text-center">{{ $danhMucHang->idDanhMucHang }}</td>

              <td class="fw-medium">
                {{ $danhMucHang->tenDanhMucHang }}
              </td>

              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  <a href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/edit') }}"
                     class="btn btn-sm btn-light border"
                     title="Sửa">
                    <i class="ti ti-edit"></i>
                  </a>

                  <form action="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang) }}" method="POST"
                    onsubmit="return confirm('Bạn có chắc muốn xóa danh mục hàng này không?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Xóa">
                      <i class="ti ti-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center text-muted py-4">
                Chưa có danh mục hàng nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection