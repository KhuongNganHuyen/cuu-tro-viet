@extends('layouts.nhom')

@section('title', 'Tiếp nhận yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tiếp nhận yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">Tổng quan nhóm</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}">Yêu cầu cứu trợ</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Tiếp nhận</li>
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

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

<div class="row">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Thông tin yêu cầu</h5>
      </div>

      <div class="card-body">
        <p>
          <strong>Tiêu đề yêu cầu:</strong><br>
          {{ $yeuCau->tieuDeYeuCau }}
        </p>

        <p>
          <strong>Người gửi:</strong><br>
          {{ $yeuCau->nguoiGui->hoTen ?? '-' }}
        </p>

        <p>
          <strong>Số người:</strong>
          {{ $yeuCau->soNguoi ?? '-' }}
        </p>

        <p>
          <strong>Mức độ:</strong>
          {{ $yeuCau->mucDoKhanCap ?? '-' }}
        </p>

        <p>
          <strong>Địa điểm:</strong><br>
          {{ $yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
          {{ $yeuCau->diaDiem->phuongXa ?? '' }},
          {{ $yeuCau->diaDiem->tinhThanh ?? '' }}
        </p>

        <p>
          <strong>Mô tả:</strong><br>
          {{ $yeuCau->moTa }}
        </p>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Thông tin tiếp nhận</h5>
      </div>

      <div class="card-body">
        @if ($chienDichs->count() == 0)
          <div class="alert alert-warning">
            Nhóm chưa có chiến dịch nào đang hoạt động hoặc sắp diễn ra. Bạn cần tạo chiến dịch trước khi tiếp nhận yêu cầu.
          </div>

          <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/create') }}" class="btn btn-primary">
            Tạo chiến dịch
          </a>

          <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}" class="btn btn-secondary">
            Quay lại
          </a>
        @else
          <form action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan') }}" method="POST">
            @csrf

            <div class="mb-3">
              <label class="form-label">Chiến dịch tiếp nhận <span class="text-danger">*</span></label>
              <select name="idChienDich" class="form-control">
                <option value="">-- Chọn chiến dịch --</option>
                @foreach ($chienDichs as $chienDich)
                  <option value="{{ $chienDich->idChienDich }}"
                    {{ old('idChienDich') == $chienDich->idChienDich ? 'selected' : '' }}>
                    {{ $chienDich->tenChienDich }} - {{ $chienDich->trangThai }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Ngày dự kiến hỗ trợ</label>
              <input type="date" name="thoiGianDuKienHoTro" class="form-control"
                value="{{ old('thoiGianDuKienHoTro') }}">
            </div>

            <div class="mb-3">
              <label class="form-label">Nội dung đảm nhận</label>
              <textarea name="noiDungDamNhan" class="form-control" rows="4"
                placeholder="Ví dụ: Nhóm sẽ hỗ trợ lương thực, nước uống và nhu yếu phẩm cần thiết.">{{ old('noiDungDamNhan') }}</textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Trạng thái sau khi tiếp nhận <span class="text-danger">*</span></label>
              <select name="trangThai" class="form-control">
                <option value="Đã tiếp nhận" {{ old('trangThai', 'Đã tiếp nhận') == 'Đã tiếp nhận' ? 'selected' : '' }}>
                  Đã tiếp nhận
                </option>
                <option value="Đang hỗ trợ" {{ old('trangThai') == 'Đang hỗ trợ' ? 'selected' : '' }}>
                  Đang hỗ trợ
                </option>
              </select>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary"
                onclick="return confirm('Xác nhận tiếp nhận yêu cầu cứu trợ này?')">
                Lưu tiếp nhận
              </button>

              <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}" class="btn btn-secondary">
                Quay lại
              </a>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection