@extends('layouts.nhom')

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
            <a href="{{ url('/user/nhom-cua-toi') }}">
              Nhóm của tôi
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              {{ $nhom->tenNhom }}
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/hang-hoa') }}">
              Hàng hóa
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Thêm
          </li>
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
    <form action="{{ url('/nhom/' . $nhom->idNhom . '/hang-hoa') }}"
          method="POST"
          autocomplete="off">
      @csrf

      <div class="mb-3">
        <label class="form-label">
          Danh mục hàng
          <span class="text-danger">*</span>
        </label>

        <select name="idDanhMucHang"
                class="form-select">
          <option value="">
            -- Chọn danh mục hàng --
          </option>

          @foreach ($danhMucHangs as $danhMucHang)
            <option value="{{ $danhMucHang->idDanhMucHang }}"
              {{ old('idDanhMucHang') == $danhMucHang->idDanhMucHang
                  ? 'selected'
                  : '' }}>
              {{ $danhMucHang->tenDanhMucHang }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">
          Tên hàng hóa
          <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="tenHangHoa"
               class="form-control"
               value="{{ old('tenHangHoa') }}"
               placeholder="Ví dụ: Gạo, áo phao, đèn pin"
               maxlength="255"
               autocomplete="off">
      </div>

      <div class="mb-3">
        <label class="form-label">
          Đơn vị tính
          <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="donViTinh"
               class="form-control"
               value="{{ old('donViTinh') }}"
               placeholder="Ví dụ: kg, thùng, bộ, hộp"
               maxlength="100"
               autocomplete="off">
      </div>

      <div class="mb-3">
        <label class="form-label">
          Trạng thái
        </label>

        <input type="text"
              class="form-control"
              value="Đang sử dụng"
              readonly>
      </div>

      <div class="d-flex gap-2">
        <button type="submit"
                class="btn btn-primary">
          Lưu
        </button>

        <a href="{{ url('/nhom/' . $nhom->idNhom . '/hang-hoa') }}"
           class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </form>
  </div>
</div>
@endsection