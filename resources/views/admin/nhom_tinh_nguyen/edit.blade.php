@extends('layouts.admin')

@section('title', 'Sửa nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Sửa nhóm tình nguyện</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/nhom-tinh-nguyen') }}">Nhóm tình nguyện</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Sửa</li>
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

<form action="{{ url('/admin/nhom-tinh-nguyen/' . $nhomTinhNguyen->idNhom) }}" method="POST">
  @csrf
  @method('PUT')

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
              value="{{ old('tenNhom', $nhomTinhNguyen->tenNhom) }}"
              placeholder="Ví dụ: Nhóm cứu trợ Đà Nẵng">
          </div>

          <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="moTa" class="form-control" rows="4"
              placeholder="Mô tả ngắn về phạm vi hoạt động, mục tiêu hoặc đặc điểm của nhóm">{{ old('moTa', $nhomTinhNguyen->moTa) }}</textarea>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Thông tin quản lý</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Nhóm trưởng <span class="text-danger">*</span></label>
            <select name="idNhomTruong" class="form-control">
              <option value="">-- Chọn nhóm trưởng --</option>

              @foreach ($nguoiDungs as $nguoiDung)
                <option value="{{ $nguoiDung->idNguoiDung }}"
                  {{ old('idNhomTruong', $nhomTinhNguyen->idNhomTruong) == $nguoiDung->idNguoiDung ? 'selected' : '' }}>
                  {{ $nguoiDung->hoTen }} - {{ $nguoiDung->tenDangNhap }}
                </option>
              @endforeach
            </select>

            <small class="text-muted">
              Có thể đổi nhóm trưởng khi cần cập nhật người đại diện hoặc điều chỉnh thông tin nhóm.
            </small>
          </div>

          <div class="mb-3">
            <label class="form-label">Địa điểm <span class="text-danger">*</span></label>
            <select name="idDiaDiem" class="form-control">
              <option value="">-- Chọn địa điểm --</option>

              @foreach ($diaDiems as $diaDiem)
                <option value="{{ $diaDiem->idDiaDiem }}"
                  {{ old('idDiaDiem', $nhomTinhNguyen->idDiaDiem) == $diaDiem->idDiaDiem ? 'selected' : '' }}>
                  @if ($diaDiem->chiTietDiaDiem)
                    {{ $diaDiem->chiTietDiaDiem }},
                  @endif

                  @if ($diaDiem->phuongXa)
                    {{ $diaDiem->phuongXa }},
                  @endif

                  {{ $diaDiem->tinhThanh }}
                </option>
              @endforeach
            </select>

            <small class="text-muted">
              Nếu chưa có địa điểm phù hợp, hãy thêm địa điểm trước trong mục Danh mục hệ thống.
            </small>
          </div>

          <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="trangThai" class="form-control">
              <option value="Đang hoạt động"
                {{ old('trangThai', $nhomTinhNguyen->trangThai) == 'Đang hoạt động' ? 'selected' : '' }}>
                Đang hoạt động
              </option>

              <option value="Tạm ngưng"
                {{ old('trangThai', $nhomTinhNguyen->trangThai) == 'Tạm ngưng' ? 'selected' : '' }}>
                Tạm ngưng
              </option>

              <option value="Bị khóa"
                {{ old('trangThai', $nhomTinhNguyen->trangThai) == 'Bị khóa' ? 'selected' : '' }}>
                Bị khóa
              </option>
            </select>

            <small class="text-muted">
              “Chờ duyệt” dùng cho nhóm do người dùng/tình nguyện viên đăng ký sau này. Admin có thể duyệt bằng cách chuyển sang “Đang hoạt động”.
            </small>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">
          Cập nhật
        </button>

        <a href="{{ url('/admin/nhom-tinh-nguyen/' . $nhomTinhNguyen->idNhom) }}" class="btn btn-secondary">
          Quay lại chi tiết
        </a>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Ghi chú nghiệp vụ</h5>
        </div>

        <div class="card-body">
          <p class="text-muted mb-3">
            Admin có thể cập nhật thông tin nhóm, nhóm trưởng, địa điểm và trạng thái nhóm khi cần quản trị hệ thống.
          </p>

          <div class="alert alert-info mb-0">
            Thành viên nhóm nên được quản lý bởi nhóm trưởng/tình nguyện viên ở giao diện riêng. Admin chủ yếu xem và giám sát.
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection