@extends('layouts.admin')

@section('title', 'Sửa chiến dịch cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Sửa chiến dịch cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admin/chien-dich') }}">Chiến dịch cứu trợ</a></li>
          <li class="breadcrumb-item" aria-current="page">Sửa</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>Thông tin chiến dịch</h5>
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

    <form action="{{ url('/admin/chien-dich/' . $chienDich->idChienDich) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Tên chiến dịch <span class="text-danger">*</span></label>
        <input type="text" name="tenChienDich" class="form-control"
          value="{{ old('tenChienDich', $chienDich->tenChienDich) }}">
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="moTa" class="form-control" rows="3">{{ old('moTa', $chienDich->moTa) }}</textarea>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Nhóm tình nguyện <span class="text-danger">*</span></label>
          <select name="idNhom" class="form-control">
            <option value="">-- Chọn nhóm --</option>
            @foreach ($nhomTinhNguyens as $nhom)
              <option value="{{ $nhom->idNhom }}"
                {{ old('idNhom', $chienDich->idNhom) == $nhom->idNhom ? 'selected' : '' }}>
                {{ $nhom->tenNhom }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Thiên tai <span class="text-danger">*</span></label>
          <select name="idThienTai" class="form-control">
            <option value="">-- Chọn thiên tai --</option>
            @foreach ($thienTais as $thienTai)
              <option value="{{ $thienTai->idThienTai }}"
                {{ old('idThienTai', $chienDich->idThienTai) == $thienTai->idThienTai ? 'selected' : '' }}>
                {{ $thienTai->tenThienTai }}
                @if ($thienTai->namXayRa)
                  - {{ $thienTai->namXayRa }}
                @endif
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Địa điểm <span class="text-danger">*</span></label>
          <select name="idDiaDiem" class="form-control">
            <option value="">-- Chọn địa điểm --</option>
            @foreach ($diaDiems as $diaDiem)
              <option value="{{ $diaDiem->idDiaDiem }}"
                {{ old('idDiaDiem', $chienDich->idDiaDiem) == $diaDiem->idDiaDiem ? 'selected' : '' }}>
                {{ $diaDiem->tinhThanh }}
                @if ($diaDiem->phuongXa)
                  - {{ $diaDiem->phuongXa }}
                @endif
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày bắt đầu</label>
          <input type="date" name="ngayBatDau" class="form-control"
            value="{{ old('ngayBatDau', $chienDich->ngayBatDau) }}">
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày kết thúc</label>
          <input type="date" name="ngayKetThuc" class="form-control"
            value="{{ old('ngayKetThuc', $chienDich->ngayKetThuc) }}">
        </div>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="daThongBaoUBND" class="form-check-input" id="daThongBaoUBND"
          {{ old('daThongBaoUBND', $chienDich->daThongBaoUBND) ? 'checked' : '' }}>
        <label class="form-check-label" for="daThongBaoUBND">
          Đã thông báo UBND
        </label>
      </div>

      <div class="mb-3">
        <label class="form-label">Ghi chú UBND</label>
        <input type="text" name="ghiChuUBND" class="form-control"
          value="{{ old('ghiChuUBND', $chienDich->ghiChuUBND) }}">
      </div>

      <div class="mb-3">
        <label class="form-label">Trạng thái</label>
        <select name="trangThai" class="form-control">
          <option value="Sắp diễn ra" {{ old('trangThai', $chienDich->trangThai) == 'Sắp diễn ra' ? 'selected' : '' }}>Sắp diễn ra</option>
          <option value="Đang diễn ra" {{ old('trangThai', $chienDich->trangThai) == 'Đang diễn ra' ? 'selected' : '' }}>Đang diễn ra</option>
          <option value="Tạm dừng" {{ old('trangThai', $chienDich->trangThai) == 'Tạm dừng' ? 'selected' : '' }}>Tạm dừng</option>
          <option value="Hoàn thành" {{ old('trangThai', $chienDich->trangThai) == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
          <option value="Đã hủy" {{ old('trangThai', $chienDich->trangThai) == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ url('/admin/chien-dich') }}" class="btn btn-secondary">Quay lại danh sách</a>
      </div>
    </form>
  </div>
</div>
@endsection