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

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Hàng hóa thuộc danh mục: {{ $danhMucHang->tenDanhMucHang }}</h5>
      <small class="text-muted">
        Quản lý các mặt hàng cụ thể để người dùng chọn khi đăng ký đóng góp.
      </small>
    </div>

    <a href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/hang-hoa/create') }}" class="btn btn-primary">
      Thêm hàng hóa
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Tên hàng hóa</th>
            <th style="width: 160px;">Đơn vị tính</th>
            <th style="width: 170px;">Trạng thái</th>
            <th style="width: 120px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($hangHoas as $hangHoa)
            <tr>
              <td class="text-center">{{ $hangHoa->idHangHoa }}</td>

              <td class="fw-medium">
                {{ $hangHoa->tenHangHoa }}
              </td>

              <td class="text-center">
                {{ $hangHoa->donViTinh }}
              </td>

              <td class="text-center">
                @if ($hangHoa->trangThai == 'Đang sử dụng')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    Đang sử dụng
                  </span>
                @else
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-secondary d-inline-block" style="width: 8px; height: 8px;"></span>
                    {{ $hangHoa->trangThai }}
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

                  <form action="{{ url('/admin/hang-hoa/' . $hangHoa->idHangHoa . '/doi-trang-thai') }}" method="POST"
                    onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái hàng hóa này không?')">
                    @csrf
                    @method('PATCH')

                    @if ($hangHoa->trangThai == 'Đang sử dụng')
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
                Chưa có hàng hóa nào trong danh mục này.
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
@endsection