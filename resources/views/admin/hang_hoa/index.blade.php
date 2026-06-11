@extends('layouts.admin')

@section('title', 'Quản lý hàng hóa | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý hàng hóa</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/danh-muc-hang') }}">Danh mục hàng</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">{{ $danhMucHang->tenDanhMucHang }}</li>
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

    <a href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/hang-hoa') }}" class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">Hàng hóa thuộc danh mục: {{ $danhMucHang->tenDanhMucHang }}</h5>
      <small class="text-muted">
        Tổng hiển thị: {{ $hangHoas->count() }}
      </small>
    </div>

    <a href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/hang-hoa/create') }}" class="btn btn-primary">
      Thêm hàng hóa
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 hang-hoa-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Tên hàng hóa</th>
            <th style="width: 150px;">Đơn vị tính</th>
            <th style="width: 170px;">Trạng thái</th>
            <th style="width: 120px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($hangHoas as $hangHoa)
            @php
              $dangHoatDong = in_array($hangHoa->trangThai, ['Đang sử dụng']);
            @endphp

            <tr class="{{ session('hangHoaMoi') == $hangHoa->idHangHoa ? 'table-primary' : '' }}">
              <td class="text-center fw-semibold">
                {{ $hangHoa->idHangHoa }}
              </td>

              <td>
                <div class="fw-semibold">
                  {{ $hangHoa->tenHangHoa }}
                </div>
              </td>

              <td class="text-center">
                {{ $hangHoa->donViTinh }}
              </td>

              <td class="text-center">
                @if ($dangHoatDong)
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Đang sử dụng</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-dark d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Ngừng sử dụng</span>
                  </span>
                @endif
              </td>

              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  <a href="{{ url('/admin/hang-hoa/' . $hangHoa->idHangHoa . '/edit') }}"
                     class="btn btn-sm btn-light border"
                     title="Sửa">
                    <i class="ti ti-edit"></i>
                  </a>

                  <form action="{{ url('/admin/hang-hoa/' . $hangHoa->idHangHoa . '/doi-trang-thai') }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái hàng hóa này không?')">
                    @csrf
                    @method('PATCH')

                    @if ($dangHoatDong)
                      <button type="submit" class="btn btn-sm btn-light border text-danger" title="Ngừng sử dụng">
                        <i class="ti ti-lock"></i>
                      </button>
                    @else
                      <button type="submit" class="btn btn-sm btn-light border text-success" title="Mở sử dụng">
                        <i class="ti ti-lock-open"></i>
                      </button>
                    @endif
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy hàng hóa phù hợp.
                @else
                  Chưa có hàng hóa nào trong danh mục này.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      <a href="{{ url('/admin/danh-muc-hang') }}" class="btn btn-secondary">
        Quay lại danh mục hàng
      </a>
    </div>
  </div>
</div>

<style>
  .hang-hoa-table th,
  .hang-hoa-table td {
    vertical-align: middle;
  }

  .hang-hoa-table .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
</style>
@endsection