@extends('layouts.user')

@section('title', 'Đăng ký tạo nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Đăng ký tạo nhóm tình nguyện</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm tình nguyện của tôi</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Đăng ký tạo nhóm</li>
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

<form action="{{ url('/user/nhom-cua-toi') }}" method="POST">
  @csrf

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">Thông tin nhóm</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Tên nhóm <span class="text-danger">*</span></label>
            <input type="text" name="tenNhom" class="form-control"
              value="{{ old('tenNhom') }}"
              placeholder="Ví dụ: Nhóm cứu trợ Hòa Khánh">
          </div>

          <div class="mb-3">
            <label class="form-label">Mô tả nhóm</label>
            <textarea name="moTa" class="form-control" rows="4"
              placeholder="Mô tả ngắn về mục tiêu, khu vực hoạt động hoặc khả năng hỗ trợ của nhóm">{{ old('moTa') }}</textarea>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Địa điểm hoạt động</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Tỉnh/Thành <span class="text-danger">*</span></label>
            <input type="text" name="tinhThanh" class="form-control"
              value="{{ old('tinhThanh') }}"
              placeholder="Ví dụ: Đà Nẵng">
          </div>

          <div class="mb-3">
            <label class="form-label">Phường/Xã</label>
            <input type="text" name="phuongXa" class="form-control"
              value="{{ old('phuongXa') }}"
              placeholder="Ví dụ: Hòa Khánh Bắc">
          </div>

          <div class="mb-3">
            <label class="form-label">Chi tiết địa điểm</label>
            <input type="text" name="chiTietDiaDiem" class="form-control"
              value="{{ old('chiTietDiaDiem') }}"
              placeholder="Ví dụ: Nhà văn hóa khu A, đường Nguyễn Lương Bằng">
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Vĩ độ</label>
              <input type="text" name="viDo" class="form-control"
                value="{{ old('viDo') }}"
                placeholder="Ví dụ: 16.047079">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Kinh độ</label>
              <input type="text" name="kinhDo" class="form-control"
                value="{{ old('kinhDo') }}"
                placeholder="Ví dụ: 108.206230">
            </div>
          </div>

          <small class="text-muted">
            Tọa độ có thể bổ sung sau. Khi hoàn thiện, hệ thống sẽ cho chọn vị trí trực tiếp trên bản đồ.
          </small>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">
          Gửi đăng ký
        </button>

        <a href="{{ url('/user/nhom-cua-toi') }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Quy trình duyệt nhóm</h5>
        </div>

        <div class="card-body">
          <p class="text-muted">
            Sau khi gửi đăng ký, nhóm sẽ có trạng thái <strong>Chờ duyệt</strong>.
            Quản trị viên sẽ kiểm tra thông tin trước khi cho phép nhóm hoạt động.
          </p>

          <div class="alert alert-info mb-0">
            Khi nhóm được duyệt, bạn sẽ trở thành nhóm trưởng và có thể quản lý nhóm,
            thành viên, chiến dịch và các hoạt động cứu trợ liên quan.
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection