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

    <a href="{{ url('/admin/danh-muc-hang') }}" class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">Danh sách danh mục hàng</h5>
      <small class="text-muted">
        Tổng hiển thị: {{ $danhMucHangs->count() }}
      </small>
    </div>

    <a href="{{ url('/admin/danh-muc-hang/create') }}" class="btn btn-primary">
      Thêm
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 danh-muc-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Tên danh mục hàng</th>
            <th style="width: 130px;">Số hàng hóa</th>
            <th style="width: 120px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($danhMucHangs as $danhMucHang)
            <tr class="clickable-row {{ session('danhMucHangMoi') == $danhMucHang->idDanhMucHang ? 'table-primary' : '' }}"
                data-href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/hang-hoa') }}">
              <td class="text-center fw-semibold">
                {{ $danhMucHang->idDanhMucHang }}
              </td>

              <td>
                <div class="fw-semibold">
                  {{ $danhMucHang->tenDanhMucHang }}
                </div>
              </td>

              <td class="text-center fw-semibold">
                {{ $soHangHoaTheoDanhMuc[$danhMucHang->idDanhMucHang] ?? 0 }}
              </td>

              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  <a href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/edit') }}"
                     class="btn btn-sm btn-light border"
                     title="Sửa">
                    <i class="ti ti-edit"></i>
                  </a>

                  <form action="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn xóa danh mục hàng này không? Nếu danh mục đã có hàng hóa, hệ thống sẽ không cho phép xóa.')">
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
              <td colspan="4" class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy danh mục hàng phù hợp.
                @else
                  Chưa có danh mục hàng nào.
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
  .danh-muc-table th,
  .danh-muc-table td {
    vertical-align: middle;
  }

  .clickable-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .clickable-row:hover {
    background-color: #f5f7fb;
  }

  .danh-muc-table .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.clickable-row').forEach(function (row) {
      row.addEventListener('click', function (e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) {
          return;
        }

        const href = row.getAttribute('data-href');

        if (href) {
          window.location.href = href;
        }
      });
    });
  });
</script>
@endsection