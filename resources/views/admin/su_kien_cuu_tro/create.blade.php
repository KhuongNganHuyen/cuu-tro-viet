@extends('layouts.admin')

@section('title', 'Thêm sự kiện cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm sự kiện cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/su-kien-cuu-tro') }}">Sự kiện cứu trợ</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

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
    <h5 class="mb-0">Thông tin sự kiện cứu trợ</h5>
  </div>

  <div class="card-body">
    <form action="{{ url('/admin/su-kien-cuu-tro') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Tên sự kiện <span class="text-danger">*</span></label>
        <input type="text" name="tenSuKien" class="form-control"
          value="{{ old('tenSuKien') }}"
          placeholder="Ví dụ: Hỗ trợ hộ nghèo, Bão lũ miền Trung">
      </div>

      <div class="mb-3">
        <label class="form-label">Loại sự kiện <span class="text-danger">*</span></label>
        <select name="loaiSuKien" class="form-control">
          <option value="">-- Chọn loại sự kiện --</option>
          <option value="Khẩn cấp" {{ old('loaiSuKien') == 'Khẩn cấp' ? 'selected' : '' }}>Khẩn cấp</option>
          <option value="Thường nhật" {{ old('loaiSuKien') == 'Thường nhật' ? 'selected' : '' }}>Thường nhật</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="moTa" class="form-control" rows="4"
          placeholder="Mô tả mục đích, phạm vi hoặc nhóm đối tượng của sự kiện cứu trợ.">{{ old('moTa') }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
        <select name="trangThai" class="form-control">
          <option value="Sắp diễn ra" {{ old('trangThai') == 'Sắp diễn ra' ? 'selected' : '' }}>Sắp diễn ra</option>
          <option value="Đang diễn ra" {{ old('trangThai', 'Đang diễn ra') == 'Đang diễn ra' ? 'selected' : '' }}>Đang diễn ra</option>
          <option value="Đã kết thúc" {{ old('trangThai') == 'Đã kết thúc' ? 'selected' : '' }}>Đã kết thúc</option>
          <option value="Đã ẩn" {{ old('trangThai') == 'Đã ẩn' ? 'selected' : '' }}>Đã ẩn</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          Lưu
        </button>

        <a href="{{ url('/admin/su-kien-cuu-tro') }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </form>
  </div>
</div>
@endsection