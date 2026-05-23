@extends('layouts.admin')

@section('title', 'Sửa yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Sửa yêu cầu cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admin/yeu-cau-cuu-tro') }}">Yêu cầu cứu trợ</a></li>
          <li class="breadcrumb-item" aria-current="page">Sửa</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>Thông tin yêu cầu cứu trợ</h5>
  </div>

  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ url('/admin/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Người gửi <span class="text-danger">*</span></label>
          <select name="idNguoiGui" class="form-control">
            <option value="">-- Chọn người gửi --</option>
            @foreach ($nguoiDungs as $nguoiDung)
              <option value="{{ $nguoiDung->idNguoiDung }}"
                {{ old('idNguoiGui', $yeuCau->idNguoiGui) == $nguoiDung->idNguoiDung ? 'selected' : '' }}>
                {{ $nguoiDung->hoTen }} - {{ $nguoiDung->tenDangNhap }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Địa điểm <span class="text-danger">*</span></label>
          <select name="idDiaDiem" class="form-control">
            <option value="">-- Chọn địa điểm --</option>
            @foreach ($diaDiems as $diaDiem)
              <option value="{{ $diaDiem->idDiaDiem }}"
                {{ old('idDiaDiem', $yeuCau->idDiaDiem) == $diaDiem->idDiaDiem ? 'selected' : '' }}>
                {{ $diaDiem->tinhThanh }}
                @if ($diaDiem->phuongXa)
                  - {{ $diaDiem->phuongXa }}
                @endif
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Loại yêu cầu <span class="text-danger">*</span></label>
        <input type="text" name="loaiYeuCau" class="form-control"
          value="{{ old('loaiYeuCau', $yeuCau->loaiYeuCau) }}"
          placeholder="Ví dụ: Lương thực, nước uống, chỗ ở tạm">
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả <span class="text-danger">*</span></label>
        <textarea name="moTa" class="form-control" rows="4">{{ old('moTa', $yeuCau->moTa) }}</textarea>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Số hộ dân</label>
          <input type="number" name="soHoDan" class="form-control"
            value="{{ old('soHoDan', $yeuCau->soHoDan) }}">
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Mức độ khẩn cấp</label>
          <select name="mucDoKhanCap" class="form-control">
            <option value="">-- Chọn mức độ --</option>
            <option value="Thấp" {{ old('mucDoKhanCap', $yeuCau->mucDoKhanCap) == 'Thấp' ? 'selected' : '' }}>Thấp</option>
            <option value="Trung bình" {{ old('mucDoKhanCap', $yeuCau->mucDoKhanCap) == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
            <option value="Cao" {{ old('mucDoKhanCap', $yeuCau->mucDoKhanCap) == 'Cao' ? 'selected' : '' }}>Cao</option>
            <option value="Khẩn cấp" {{ old('mucDoKhanCap', $yeuCau->mucDoKhanCap) == 'Khẩn cấp' ? 'selected' : '' }}>Khẩn cấp</option>
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Trạng thái</label>
          <select name="trangThai" class="form-control">
            <option value="Chờ tiếp nhận" {{ old('trangThai', $yeuCau->trangThai) == 'Chờ tiếp nhận' ? 'selected' : '' }}>Chờ tiếp nhận</option>
            <option value="Đã tiếp nhận" {{ old('trangThai', $yeuCau->trangThai) == 'Đã tiếp nhận' ? 'selected' : '' }}>Đã tiếp nhận</option>
            <option value="Đang xử lý" {{ old('trangThai', $yeuCau->trangThai) == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
            <option value="Hoàn thành" {{ old('trangThai', $yeuCau->trangThai) == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
            <option value="Từ chối" {{ old('trangThai', $yeuCau->trangThai) == 'Từ chối' ? 'selected' : '' }}>Từ chối</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Hình ảnh</label>
        <input type="text" name="hinhAnh" class="form-control"
          value="{{ old('hinhAnh', $yeuCau->hinhAnh) }}"
          placeholder="Tạm thời để trống">
        <small class="text-muted">Phần upload ảnh sẽ làm sau.</small>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ url('/admin/yeu-cau-cuu-tro') }}" class="btn btn-secondary">Quay lại danh sách</a>
      </div>
    </form>
  </div>
</div>
@endsection