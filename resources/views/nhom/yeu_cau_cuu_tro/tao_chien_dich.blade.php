@extends('layouts.nhom')

@section('title', 'Tạo chiến dịch từ yêu cầu | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tạo chiến dịch từ yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">Tổng quan nhóm</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}">Yêu cầu cứu trợ</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Tạo chiến dịch</li>
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
        <h5 class="mb-0">Yêu cầu làm cơ sở tạo chiến dịch</h5>
      </div>

      <div class="card-body">
        <p>
          <strong>Loại yêu cầu:</strong><br>
          {{ $yeuCau->loaiYeuCau }}
        </p>

        <p>
          <strong>Người gửi:</strong><br>
          {{ $yeuCau->nguoiGui->hoTen ?? '-' }}
        </p>

        <p>
          <strong>Số hộ dân:</strong>
          {{ $yeuCau->soHoDan ?? '-' }}
        </p>

        <p>
          <strong>Mức độ khẩn cấp:</strong>
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

        <div class="alert alert-info mb-0">
          Chiến dịch mới sẽ sử dụng địa điểm của yêu cầu này làm địa điểm chiến dịch.
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Thông tin chiến dịch mới</h5>
      </div>

      <div class="card-body">
        <form action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tao-chien-dich') }}" method="POST">
          @csrf

          <div class="mb-3">
            <label class="form-label">Thiên tai <span class="text-danger">*</span></label>
            <select name="idThienTai" class="form-control">
              <option value="">-- Chọn thiên tai --</option>
              @foreach ($thienTais as $thienTai)
                <option value="{{ $thienTai->idThienTai }}"
                  {{ old('idThienTai') == $thienTai->idThienTai ? 'selected' : '' }}>
                  {{ $thienTai->tenThienTai ?? ('Thiên tai #' . $thienTai->idThienTai) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Tên chiến dịch <span class="text-danger">*</span></label>
            <input type="text" name="tenChienDich" class="form-control"
              value="{{ old('tenChienDich', 'Chiến dịch hỗ trợ ' . ($yeuCau->diaDiem->phuongXa ?? $yeuCau->diaDiem->tinhThanh ?? 'khu vực bị ảnh hưởng')) }}"
              placeholder="Nhập tên chiến dịch">
          </div>

          <div class="mb-3">
            <label class="form-label">Mô tả chiến dịch</label>
            <textarea name="moTa" class="form-control" rows="4"
              placeholder="Mô tả mục tiêu, phạm vi và nội dung hỗ trợ của chiến dịch.">{{ old('moTa', 'Chiến dịch được tạo dựa trên yêu cầu cứu trợ: ' . $yeuCau->loaiYeuCau . '. ' . $yeuCau->moTa) }}</textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
              <input type="date" name="ngayBatDau" class="form-control"
                value="{{ old('ngayBatDau', date('Y-m-d')) }}">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Ngày kết thúc</label>
              <input type="date" name="ngayKetThuc" class="form-control"
                value="{{ old('ngayKetThuc') }}">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Trạng thái chiến dịch <span class="text-danger">*</span></label>
            <select name="trangThaiChienDich" class="form-control">
              <option value="Đang diễn ra" {{ old('trangThaiChienDich', 'Đang diễn ra') == 'Đang diễn ra' ? 'selected' : '' }}>
                Đang diễn ra
              </option>
              <option value="Sắp diễn ra" {{ old('trangThaiChienDich') == 'Sắp diễn ra' ? 'selected' : '' }}>
                Sắp diễn ra
              </option>
            </select>
          </div>

          <hr>

          <h6 class="mb-3">Thông tin tiếp nhận yêu cầu</h6>

          <div class="mb-3">
            <label class="form-label">Thời gian dự kiến hỗ trợ</label>
            <input type="datetime-local" name="thoiGianDuKienHoTro" class="form-control"
              value="{{ old('thoiGianDuKienHoTro') }}">
          </div>

          <div class="mb-3">
            <label class="form-label">Nội dung đảm nhận</label>
            <textarea name="noiDungDamNhan" class="form-control" rows="3"
              placeholder="Ví dụ: Nhóm sẽ hỗ trợ nhu yếu phẩm, nước uống và các vật dụng cần thiết.">{{ old('noiDungDamNhan') }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Trạng thái tiếp nhận <span class="text-danger">*</span></label>
            <select name="trangThaiTiepNhan" class="form-control">
              <option value="Đã tiếp nhận" {{ old('trangThaiTiepNhan', 'Đã tiếp nhận') == 'Đã tiếp nhận' ? 'selected' : '' }}>
                Đã tiếp nhận
              </option>
              <option value="Đang hỗ trợ" {{ old('trangThaiTiepNhan') == 'Đang hỗ trợ' ? 'selected' : '' }}>
                Đang hỗ trợ
              </option>
            </select>
          </div>

          <hr>

          <h6 class="mb-3">Thông báo UBND</h6>

          <div class="mb-3">
            <label class="form-label">Trạng thái thông báo UBND</label>
                <select name="daThongBaoUBND" class="form-control">
                <option value="0" {{ old('daThongBaoUBND', '0') == '0' ? 'selected' : '' }}>
                    Chưa thông báo
                </option>
                <option value="1" {{ old('daThongBaoUBND') == '1' ? 'selected' : '' }}>
                    Đã thông báo
                </option>
                </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Ghi chú UBND</label>
            <textarea name="ghiChuUBND" class="form-control" rows="3"
              placeholder="Nhập ghi chú nếu đã thông báo hoặc cần phối hợp với địa phương.">{{ old('ghiChuUBND') }}</textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"
              onclick="return confirm('Tạo chiến dịch mới và tiếp nhận yêu cầu này?')">
              Tạo chiến dịch
            </button>

            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}" class="btn btn-secondary">
              Quay lại
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection