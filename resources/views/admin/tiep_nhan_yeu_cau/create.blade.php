@extends('layouts.admin')

@section('title', 'Tiếp nhận yêu cầu | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tiếp nhận yêu cầu cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admin/yeu-cau-cuu-tro') }}">Yêu cầu cứu trợ</a></li>
          <li class="breadcrumb-item" aria-current="page">Tiếp nhận</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">
    <h5 class="mb-0">Thông tin yêu cầu</h5>
  </div>

  <div class="card-body">
    <p><strong>Người gửi:</strong> {{ $yeuCau->nguoiGui->hoTen ?? '-' }}</p>
    <p><strong>Loại yêu cầu:</strong> {{ $yeuCau->loaiYeuCau }}</p>
    <p><strong>Mô tả:</strong> {{ $yeuCau->moTa }}</p>
    <p>
      <strong>Địa điểm:</strong>
      {{ $yeuCau->diaDiem->tinhThanh ?? '-' }}
      @if (!empty($yeuCau->diaDiem?->phuongXa))
        - {{ $yeuCau->diaDiem->phuongXa }}
      @endif
    </p>
    <p><strong>Mức độ khẩn cấp:</strong> {{ $yeuCau->mucDoKhanCap ?? '-' }}</p>
    <p><strong>Trạng thái hiện tại:</strong> {{ $yeuCau->trangThai }}</p>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Thông tin tiếp nhận</h5>
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

    <form action="{{ url('/admin/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan') }}" method="POST">
      @csrf

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Chiến dịch <span class="text-danger">*</span></label>
          <select name="idChienDich" class="form-control">
            <option value="">-- Chọn chiến dịch --</option>
            @foreach ($chienDichs as $chienDich)
              <option value="{{ $chienDich->idChienDich }}" {{ old('idChienDich') == $chienDich->idChienDich ? 'selected' : '' }}>
                {{ $chienDich->tenChienDich }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Nhóm tình nguyện tiếp nhận <span class="text-danger">*</span></label>
          <select name="idNhom" class="form-control">
            <option value="">-- Chọn nhóm tình nguyện --</option>
            @foreach ($nhomTinhNguyens as $nhom)
              <option value="{{ $nhom->idNhom }}" {{ old('idNhom') == $nhom->idNhom ? 'selected' : '' }}>
                {{ $nhom->tenNhom }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Thời gian dự kiến hỗ trợ</label>
        <input type="date" name="thoiGianDuKienHoTro" class="form-control"
          value="{{ old('thoiGianDuKienHoTro') }}">
      </div>

      <div class="mb-3">
        <label class="form-label">Nội dung đảm nhận</label>
        <textarea name="noiDungDamNhan" class="form-control" rows="3">{{ old('noiDungDamNhan') }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Trạng thái</label>
        <select name="trangThai" class="form-control">
          <option value="Đã tiếp nhận" {{ old('trangThai', 'Đã tiếp nhận') == 'Đã tiếp nhận' ? 'selected' : '' }}>Đã tiếp nhận</option>
          <option value="Đang xử lý" {{ old('trangThai') == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
          <option value="Hoàn thành" {{ old('trangThai') == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Lưu tiếp nhận</button>
        <a href="{{ url('/admin/yeu-cau-cuu-tro') }}" class="btn btn-secondary">Quay lại</a>
      </div>
    </form>
  </div>
</div>
@endsection