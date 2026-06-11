@extends('layouts.admin')

@section('title', 'Thêm hàng hóa | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm hàng hóa</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/danh-muc-hang') }}">Danh mục hàng</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/hang-hoa') }}">
              {{ $danhMucHang->tenDanhMucHang }}
            </a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Thông tin hàng hóa</h5>
  </div>

  <div class="card-body">
    <form action="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/hang-hoa') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Danh mục hàng</label>
        <input type="text" class="form-control" value="{{ $danhMucHang->tenDanhMucHang }}" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Tên hàng hóa <span class="text-danger">*</span></label>
        <input type="text" name="tenHangHoa" class="form-control"
          value="{{ old('tenHangHoa') }}"
          placeholder="Ví dụ: Gạo, Mì tôm, Nước uống đóng chai"
          autocomplete="off">
      </div>

      <div class="mb-3">
        <label class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
        <input type="text" name="donViTinh" class="form-control"
          value="{{ old('donViTinh') }}"
          placeholder="Ví dụ: kg, thùng, chai, bộ, hộp"
          autocomplete="off">
      </div>

      <div class="mb-3">
        <label class="form-label">Trạng thái</label>
        <select name="trangThai" class="form-control">
          <option value="Đang sử dụng" {{ old('trangThai', 'Đang sử dụng') == 'Đang sử dụng' ? 'selected' : '' }}>
            Đang sử dụng
          </option>
          <option value="Ngừng sử dụng" {{ old('trangThai') == 'Ngừng sử dụng' ? 'selected' : '' }}>
            Ngừng sử dụng
          </option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          Lưu
        </button>

        <a href="{{ url('/admin/danh-muc-hang/' . $danhMucHang->idDanhMucHang . '/hang-hoa') }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </form>
  </div>
</div>
@endsection