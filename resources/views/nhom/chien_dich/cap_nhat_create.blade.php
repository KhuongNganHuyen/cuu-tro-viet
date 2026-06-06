@extends('layouts.nhom')

@section('title', 'Thêm cập nhật tiến độ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm cập nhật tiến độ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}">Chiến dịch</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}">
              {{ $chienDich->tenChienDich }}
            </a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm cập nhật</li>
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

<form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/cap-nhat') }}"
  method="POST"
  enctype="multipart/form-data">
  @csrf

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Nội dung cập nhật</h5>
    </div>

    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Chiến dịch</label>
        <input type="text" class="form-control" value="{{ $chienDich->tenChienDich }}" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Nội dung cập nhật <span class="text-danger">*</span></label>
        <textarea name="noiDung" class="form-control" rows="5"
          placeholder="Ví dụ: Đã tiếp nhận 20 phần quà và khảo sát khu vực cần hỗ trợ.">{{ old('noiDung') }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Hình ảnh minh chứng</label>
        <input type="file" name="hinhAnh" class="form-control" accept="image/*">
        <small class="text-muted">
          Có thể tải ảnh minh chứng hoạt động cứu trợ, tối đa 2MB.
        </small>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          Lưu
        </button>

        <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>
  </div>
</form>
@endsection