@extends('layouts.nhom')

@section('title', 'Chi tiết chiến dịch | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #chienDichDiaDiemMap {
    height: 360px;
    width: 100%;
    border-radius: 12px;
  }

  .map-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 16px;
    background-color: #ffffff;
  }

  .info-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 16px;
    height: 100%;
    background-color: #ffffff;
  }

  .info-label {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 4px;
  }

  .info-value {
    font-weight: 500;
    color: #212529;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    flex-shrink: 0;
    border-radius: 50%;
  }

  .status-upcoming {
    background-color: #0dcaf0;
  }

  .status-active {
    background-color: #198754;
  }

  .status-paused {
    background-color: #ffc107;
  }

  .status-completed {
    background-color: #0d6efd;
  }

  .status-default {
    background-color: #6c757d;
  }

  .description-box {
    white-space: pre-line;
    line-height: 1.7;
  }

  .clickable-row {
    cursor: pointer;
  }

  .clickable-row:hover {
    background-color: #f5f7fb;
  }

  .confirmation-content {
    padding-left: 0;
  }

  .confirmation-note {
    margin-top: 2px;
    margin-left: 18px;
    color: #495057;
    font-size: 14px;
    line-height: 1.45;
    white-space: normal;
  }

  .cap-nhat-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid #dee2e6;
  }

  .cap-nhat-avatar-placeholder {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #e9ecef;
    color: #495057;
    font-weight: 700;
    font-size: 16px;
  }

  .cap-nhat-title {
    color: #212529;
    font-size: 18px;
    font-weight: 500;
    line-height: 1.5;
    margin-bottom: 6px;
  }

  .cap-nhat-title strong {
    font-weight: 700;
  }

  .cap-nhat-noi-dung {
    white-space: pre-line;
    line-height: 1.7;
    margin-bottom: 12px;
  }

  .cap-nhat-image-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 12px;
  }

  .cap-nhat-image {
    max-width: 100%;
    max-height: 320px;
    object-fit: contain;
    border-radius: 10px;
    border: 1px solid #dee2e6;
  }
</style>

@php
  $trangThaiChienDich = $chienDich->trangThai === 'Đang hoạt động'
      ? 'Đang diễn ra'
      : $chienDich->trangThai;

  $chienDichDaHoanThanh = $trangThaiChienDich === 'Hoàn thành';

  $classTrangThai = match ($trangThaiChienDich) {
      'Sắp diễn ra' => 'status-upcoming',
      'Đang diễn ra' => 'status-active',
      'Tạm ngưng' => 'status-paused',
      'Hoàn thành' => 'status-completed',
      default => 'status-default',
  };

  $diaDiemDayDu = '-';

  if ($chienDich->diaDiem) {
      $diaDiemParts = array_filter([
          $chienDich->diaDiem->chiTietDiaDiem ?? null,
          $chienDich->diaDiem->phuongXa ?? null,
          $chienDich->diaDiem->tinhThanh ?? null,
      ]);

      $diaDiemDayDu = count($diaDiemParts) > 0
          ? implode(', ', $diaDiemParts)
          : '-';
  }

  $viDoChienDich = $chienDich->diaDiem->viDo ?? null;
  $kinhDoChienDich = $chienDich->diaDiem->kinhDo ?? null;

  $coToaDoChienDich = $viDoChienDich && $kinhDoChienDich;
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết chiến dịch</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              {{ $nhom->tenNhom }}
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}">
              Chiến dịch
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Chi tiết
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

@if ($chienDichDaHoanThanh)
  <div class="alert alert-secondary">
    Chiến dịch đã hoàn thành. Các thông tin chỉ được xem, không thể chỉnh sửa hoặc phát sinh thao tác mới.
  </div>
@endif

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
      <div>
        <h4 class="fw-bold mb-1">
          {{ $chienDich->tenChienDich }}
        </h4>

        <div class="d-flex flex-wrap align-items-center gap-3 text-muted">
          <span>
            Nhóm phụ trách:
            <strong class="text-body">{{ $nhom->tenNhom }}</strong>
          </span>

          <span>
            Mã chiến dịch:
            <strong class="text-body">#{{ $chienDich->idChienDich }}</strong>
          </span>

          <span class="d-inline-flex align-items-center gap-2">
            <span class="status-dot {{ $classTrangThai }}"></span>
            <span>{{ $trangThaiChienDich ?? '-' }}</span>
          </span>
        </div>
      </div>

      <div class="d-flex gap-2 flex-shrink-0">
        @if ($laNhomTruong && !$chienDichDaHoanThanh)
          <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/edit') }}"
             class="btn btn-warning">
            Sửa thông tin
          </a>
        @endif

        <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}"
           class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>

    <ul class="nav nav-tabs card-header-tabs" id="chienDichTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active"
                id="thong-tin-tab"
                data-bs-toggle="tab"
                data-bs-target="#thong-tin"
                type="button"
                role="tab">
          Thông tin chiến dịch
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="cap-nhat-tab"
                data-bs-toggle="tab"
                data-bs-target="#cap-nhat"
                type="button"
                role="tab">
          Cập nhật tiến độ
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="yeu-cau-tab"
                data-bs-toggle="tab"
                data-bs-target="#yeu-cau"
                type="button"
                role="tab">
          Yêu cầu cứu trợ
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="dong-gop-tab"
                data-bs-toggle="tab"
                data-bs-target="#dong-gop"
                type="button"
                role="tab">
          Đóng góp
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="nguon-luc-tab"
                data-bs-toggle="tab"
                data-bs-target="#nguon-luc"
                type="button"
                role="tab">
          Nguồn lực
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="phan-phoi-tab"
                data-bs-toggle="tab"
                data-bs-target="#phan-phoi"
                type="button"
                role="tab">
          Phân phối
        </button>
      </li>
    </ul>
  </div>

  <div class="card-body">
    <div class="tab-content" id="chienDichTabsContent">

      {{-- TAB 1: THÔNG TIN CHIẾN DỊCH --}}
      <div class="tab-pane fade show active" id="thong-tin" role="tabpanel">
        <div class="row g-3">
          <div class="col-lg-7">
            <div class="info-card">
              <div class="mb-3">
                <h5 class="mb-1">Thông tin chung</h5>

                <small class="text-muted">
                  Thông tin tổng quan về chiến dịch cứu trợ do nhóm phụ trách.
                </small>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="info-label">Mã chiến dịch</div>
                  <div class="info-value">#{{ $chienDich->idChienDich }}</div>
                </div>

                <div class="col-md-6">
                  <div class="info-label">Nhóm phụ trách</div>
                  <div class="info-value">{{ $nhom->tenNhom }}</div>
                </div>

                <div class="col-md-12">
                  <div class="info-label">Tên chiến dịch</div>
                  <div class="info-value">{{ $chienDich->tenChienDich }}</div>
                </div>

                <div class="col-md-12">
                  <div class="info-label">Sự kiện cứu trợ</div>

                  @if ($chienDich->suKien)
                    <div class="info-value">
                      {{ $chienDich->suKien->loaiSuKien ?? '-' }}
                      -
                      {{ $chienDich->suKien->tenSuKien ?? '-' }}
                    </div>
                  @else
                    <div class="info-value">-</div>
                  @endif
                </div>

                <div class="col-md-4">
                  <div class="info-label">Ngày tạo</div>
                  <div class="info-value">
                    {{ $chienDich->ngayTao
                        ? \Carbon\Carbon::parse($chienDich->ngayTao)->format('d/m/Y')
                        : '-' }}
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="info-label">Ngày bắt đầu</div>
                  <div class="info-value">
                    {{ $chienDich->ngayBatDau
                        ? \Carbon\Carbon::parse($chienDich->ngayBatDau)->format('d/m/Y')
                        : '-' }}
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="info-label">Ngày kết thúc</div>
                  <div class="info-value">
                    {{ $chienDich->ngayKetThuc
                        ? \Carbon\Carbon::parse($chienDich->ngayKetThuc)->format('d/m/Y')
                        : '-' }}
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="info-label">Địa điểm</div>
                  <div class="info-value">{{ $diaDiemDayDu }}</div>
                </div>

                <div class="col-md-12">
                  <div class="info-label">Xác nhận cứu trợ</div>

                  <div class="confirmation-content">
                    @if ($chienDich->daXacNhanCuuTro)
                      <div class="info-value text-success d-inline-flex align-items-center gap-2">
                        <span class="status-dot status-active"></span>
                        <span>Đã xác nhận</span>
                      </div>
                    @else
                      <div class="info-value text-warning d-inline-flex align-items-center gap-2">
                        <span class="status-dot status-paused"></span>
                        <span>Chưa xác nhận</span>
                      </div>
                    @endif

                    <div class="confirmation-note">{{ $chienDich->ghiChuXacNhan ?: 'Chưa có ghi chú xác nhận.' }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="info-card">
              <h5 class="mb-3">Mô tả chiến dịch</h5>

              <div class="description-box">
                {{ $chienDich->moTa ?? 'Chưa có mô tả cho chiến dịch này.' }}
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="map-card">
              <div class="mb-3">
                <h5 class="mb-1">Bản đồ vị trí chiến dịch</h5>

                <small class="text-muted">
                  Tọa độ địa điểm triển khai chiến dịch.
                </small>
              </div>

              @if ($coToaDoChienDich)
                <div id="chienDichDiaDiemMap"></div>

                <small class="text-muted d-block mt-2">
                  Tọa độ:
                  {{ $viDoChienDich }},
                  {{ $kinhDoChienDich }}
                </small>
              @else
                <div class="text-center text-muted py-4">
                  Chưa có tọa độ vị trí cho chiến dịch này.
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- TAB 2: CẬP NHẬT TIẾN ĐỘ --}}
      <div class="tab-pane fade" id="cap-nhat" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Cập nhật tiến độ</h5>

            <small class="text-muted">
              Ghi nhận các hoạt động, tình hình và minh chứng trong quá trình triển khai chiến dịch.
            </small>
          </div>

          @if (!$chienDichDaHoanThanh)
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/cap-nhat/create') }}"
              class="btn btn-primary">
              Thêm cập nhật
            </a>
          @endif
        </div>

        @forelse ($capNhats as $capNhat)
          @php
            $nguoiCapNhat = $capNhat->thanhVien->nguoiDung ?? null;

            $tenNguoiCapNhat = $nguoiCapNhat->hoTen ?? 'Thành viên nhóm';

            $chuCaiDaiDien = mb_substr($tenNguoiCapNhat, 0, 1, 'UTF-8');

            $anhDaiDien = $nguoiCapNhat->anhDaiDien ?? null;

            $ngayCapNhat = $capNhat->thoiGianCapNhat
                ? \Carbon\Carbon::parse($capNhat->thoiGianCapNhat)->format('d/m/Y')
                : '-';
          @endphp

          <div class="border rounded p-3 mb-3">
            <div class="d-flex align-items-center gap-3 mb-3">
              @if ($anhDaiDien)
                <img src="{{ asset('storage/' . $anhDaiDien) }}"
                    alt="{{ $tenNguoiCapNhat }}"
                    class="cap-nhat-avatar">
              @else
                <div class="cap-nhat-avatar cap-nhat-avatar-placeholder">
                  {{ $chuCaiDaiDien }}
                </div>
              @endif

              <div>
                <div class="fw-semibold">
                  {{ $tenNguoiCapNhat }}
                </div>

                <small class="text-muted">
                  {{ $capNhat->thoiGianCapNhat ?? '-' }}
                </small>
              </div>
            </div>

            <div class="mb-3">
              <div class="cap-nhat-title">
                Cập nhật chiến dịch <strong>{{ $chienDich->tenChienDich }}</strong> ngày <strong>{{ $ngayCapNhat }}</strong>
              </div>

              <div class="cap-nhat-noi-dung">{{ $capNhat->noiDung }}</div>

              @if ($capNhat->hinhAnh)
                <div class="cap-nhat-image-wrapper">
                  <img src="{{ asset('storage/' . $capNhat->hinhAnh) }}"
                      alt="Hình ảnh cập nhật"
                      class="cap-nhat-image">
                </div>
              @endif
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">
            Chưa có cập nhật tiến độ cho chiến dịch này.
          </div>
        @endforelse
      </div>

      {{-- TAB 3: YÊU CẦU CỨU TRỢ --}}
      <div class="tab-pane fade" id="yeu-cau" role="tabpanel">
        <div class="mb-3">
          <h5 class="mb-0">Yêu cầu cứu trợ thuộc chiến dịch</h5>

          <small class="text-muted">
            Danh sách các yêu cầu đã được nhóm tiếp nhận và gắn vào chiến dịch này.
          </small>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Thông tin yêu cầu</th>
                <th style="width: 120px;">Số người</th>
                <th style="width: 140px;">Mức độ</th>
                <th style="width: 150px;">Trạng thái</th>
                <th style="width: 180px;">Dự kiến hỗ trợ</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($tiepNhanYeuCaus as $tiepNhan)
                <tr class="clickable-row"
                    data-href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $tiepNhan->idYeuCau) }}">
                  <td class="text-center">
                    {{ $tiepNhan->yeuCau->idYeuCau ?? '-' }}
                  </td>

                  <td>
                    <div class="fw-semibold">
                      {{ $tiepNhan->yeuCau->tieuDeYeuCau ?? '-' }}
                    </div>

                    <small class="text-muted">
                      Người gửi: {{ $tiepNhan->yeuCau->nguoiGui->hoTen ?? '-' }}
                    </small>

                    <br>

                    <small class="text-muted">
                      {{ $tiepNhan->yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
                      {{ $tiepNhan->yeuCau->diaDiem->phuongXa ?? '' }},
                      {{ $tiepNhan->yeuCau->diaDiem->tinhThanh ?? '' }}
                    </small>

                    @if ($tiepNhan->noiDungDamNhan)
                      <div class="mt-1">
                        <small>
                          <strong>Nội dung đảm nhận:</strong>
                          {{ $tiepNhan->noiDungDamNhan }}
                        </small>
                      </div>
                    @endif
                  </td>

                  <td class="text-center">
                    {{ $tiepNhan->yeuCau->soNguoi ?? '-' }}
                  </td>

                  <td class="text-center">
                    @if (($tiepNhan->yeuCau->mucDoKhanCap ?? '') == 'Khẩn cấp')
                      <span class="text-danger fw-semibold">Khẩn cấp</span>
                    @elseif (($tiepNhan->yeuCau->mucDoKhanCap ?? '') == 'Cao')
                      <span class="text-warning fw-semibold">Cao</span>
                    @else
                      {{ $tiepNhan->yeuCau->mucDoKhanCap ?? '-' }}
                    @endif
                  </td>

                  <td class="text-center">
                    {{ $tiepNhan->trangThai ?? '-' }}
                  </td>

                  <td class="text-center">
                    {{ $tiepNhan->thoiGianDuKienHoTro ?? '-' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6"
                      class="text-center text-muted py-4">
                    Chưa có yêu cầu cứu trợ nào được gắn vào chiến dịch này.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- TAB 4: ĐÓNG GÓP --}}
      <div class="tab-pane fade" id="dong-gop" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Đóng góp cho chiến dịch</h5>

            <small class="text-muted">
              Danh sách các lượt đóng góp từ người dùng. Chỉ đóng góp đã xác nhận mới được cộng vào nguồn lực.
            </small>
          </div>
        </div>

        @forelse ($dongGops as $dongGop)
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <div class="fw-semibold">
                  Người ủng hộ: {{ $dongGop->nguoiUngHo->hoTen ?? '-' }}
                </div>

                <small class="text-muted">
                  Thời gian gửi: {{ $dongGop->thoiGianDongGop ?? '-' }}
                </small>
              </div>

              <div class="text-end">
                <small class="text-muted">
                  Tiếp nhận:
                  {{ $dongGop->thanhVienTiepNhan->nguoiDung->hoTen ?? 'Chưa có' }}
                </small>
              </div>
            </div>

            @if ($dongGop->ghiChu)
              <p class="mb-2">
                <strong>Ghi chú:</strong> {{ $dongGop->ghiChu }}
              </p>
            @endif

            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead>
                  <tr class="text-center">
                    <th class="text-start">Hàng hóa</th>
                    <th style="width: 120px;">Số lượng</th>
                    <th style="width: 140px;">Hạn sử dụng</th>
                    <th style="width: 150px;">Trạng thái</th>
                    <th style="width: 150px;">Thao tác</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach ($dongGop->chiTietDongGops as $chiTiet)
                    <tr>
                      <td>
                        {{ $chiTiet->hangHoa->tenHangHoa ?? '-' }}

                        @if ($chiTiet->hangHoa?->donViTinh)
                          <small class="text-muted">
                            ({{ $chiTiet->hangHoa->donViTinh }})
                          </small>
                        @endif
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->soLuong }}
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->hanSuDung ?? '-' }}
                      </td>

                      <td class="text-center">
                        @if ($chiTiet->trangThai == 'Chờ xác nhận')
                          <span class="d-inline-flex align-items-center gap-2">
                            <span class="status-dot status-paused"></span>
                            Chờ xác nhận
                          </span>
                        @elseif ($chiTiet->trangThai == 'Đã xác nhận')
                          <span class="d-inline-flex align-items-center gap-2">
                            <span class="status-dot status-active"></span>
                            Đã xác nhận
                          </span>
                        @elseif ($chiTiet->trangThai == 'Từ chối')
                          <span class="d-inline-flex align-items-center gap-2">
                            <span class="status-dot bg-danger"></span>
                            Từ chối
                          </span>
                        @else
                          {{ $chiTiet->trangThai ?? '-' }}
                        @endif
                      </td>

                      <td class="text-center">
                        @if ($chiTiet->trangThai == 'Chờ xác nhận' && !$chienDichDaHoanThanh)
                          <div class="d-inline-flex gap-1">
                            <form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/dong-gop/' . $chiTiet->idChiTietDongGop . '/xac-nhan') }}"
                                  method="POST"
                                  onsubmit="return confirm('Xác nhận đóng góp này và cộng vào nguồn lực chiến dịch?')">
                              @csrf
                              @method('PATCH')

                              <button type="submit"
                                      class="btn btn-sm btn-light border text-success"
                                      title="Xác nhận">
                                <i class="ti ti-check"></i>
                              </button>
                            </form>

                            <form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/dong-gop/' . $chiTiet->idChiTietDongGop . '/tu-choi') }}"
                                  method="POST"
                                  onsubmit="return confirm('Bạn có chắc muốn từ chối đóng góp này không?')">
                              @csrf
                              @method('PATCH')

                              <button type="submit"
                                      class="btn btn-sm btn-light border text-danger"
                                      title="Từ chối">
                                <i class="ti ti-x"></i>
                              </button>
                            </form>
                          </div>
                        @else
                          -
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">
            Chưa có lượt đóng góp nào cho chiến dịch này.
          </div>
        @endforelse
      </div>

      {{-- TAB 5: NGUỒN LỰC --}}
      <div class="tab-pane fade" id="nguon-luc" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Nguồn lực chiến dịch</h5>

            <small class="text-muted">
              Danh sách mặt hàng cần kêu gọi, số lượng đã nhận và số lượng hiện còn của chiến dịch.
            </small>
          </div>

          @if ($laNhomTruong && !$chienDichDaHoanThanh)
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/nguon-luc/cap-nhat') }}"
              class="btn btn-primary">
              Cập nhật nguồn lực
            </a>
          @endif
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Hàng hóa</th>
                <th style="width: 150px;">Cần kêu gọi</th>
                <th style="width: 150px;">Đã nhận</th>
                <th style="width: 150px;">Hiện còn</th>
                <th style="width: 150px;">Hạn sử dụng</th>
                <th style="width: 170px;">Trạng thái</th>
                <th style="width: 170px;">Ngày cập nhật</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($nguonLucs as $nguonLuc)
                @php
                  $trangThaiNguonLuc = $nguonLuc->trangThai ?? '-';

                  $classNguonLuc = match ($trangThaiNguonLuc) {
                      'Đang kêu gọi' => 'status-active',
                      'Ngừng tiếp nhận' => 'status-paused',
                      'Đã đủ' => 'status-completed',
                      default => 'status-default',
                  };
                @endphp

                <tr>
                  <td class="text-center">
                    {{ $nguonLuc->idNguonLuc }}
                  </td>

                  <td>
                    <div class="fw-semibold">
                      {{ $nguonLuc->hangHoa->tenHangHoa ?? '-' }}
                    </div>

                    @if ($nguonLuc->hangHoa?->danhMucHang?->tenDanhMucHang)
                      <small class="text-muted">
                        {{ $nguonLuc->hangHoa->danhMucHang->tenDanhMucHang }}
                      </small>
                    @endif

                    @if ($nguonLuc->hangHoa?->donViTinh)
                      <small class="text-muted">
                        - {{ $nguonLuc->hangHoa->donViTinh }}
                      </small>
                    @endif
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->soLuongCanKeuGoi ?? 0 }}
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->soLuongDaNhan ?? 0 }}
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->soLuongHienCo ?? 0 }}
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->hanSuDung ?? '-' }}
                  </td>

                  <td class="text-center">
                    <span class="d-inline-flex align-items-center gap-2">
                      <span class="status-dot {{ $classNguonLuc }}"></span>
                      <span>{{ $trangThaiNguonLuc }}</span>
                    </span>
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->ngayCapNhat
                        ? \Carbon\Carbon::parse($nguonLuc->ngayCapNhat)->format('d/m/Y H:i')
                        : '-' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8"
                      class="text-center text-muted py-4">
                    Chưa có nguồn lực nào được khai báo cho chiến dịch này.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- TAB 6: PHÂN PHỐI --}}
      <div class="tab-pane fade" id="phan-phoi" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Đợt phân phối</h5>

            <small class="text-muted">
              Danh sách các đợt phân phối hàng cứu trợ từ nguồn lực chiến dịch.
            </small>
          </div>

          @if (!$chienDichDaHoanThanh)
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi/create') }}"
               class="btn btn-primary">
              Tạo đợt phân phối
            </a>
          @endif
        </div>

        @forelse ($dotPhanPhois as $dotPhanPhoi)
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <div class="fw-semibold">
                  Đợt phân phối #{{ $dotPhanPhoi->idDotPhanPhoi }}
                </div>

                <small class="text-muted">
                  Ngày phân phối: {{ $dotPhanPhoi->ngayPhanPhoi ?? '-' }}
                </small>
              </div>

              <div class="text-end">
                <span>{{ $dotPhanPhoi->trangThai ?? '-' }}</span>
              </div>
            </div>

            @if ($dotPhanPhoi->ghiChu)
              <p class="mb-2">
                <strong>Ghi chú:</strong> {{ $dotPhanPhoi->ghiChu }}
              </p>
            @endif

            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead>
                  <tr class="text-center">
                    <th class="text-start">Nguồn lực</th>
                    <th style="width: 130px;">Số lượng giao</th>
                    <th class="text-start">Yêu cầu nhận hỗ trợ</th>
                    <th style="width: 160px;">Người nhận</th>
                    <th style="width: 140px;">Trạng thái</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach ($dotPhanPhoi->chiTietPhanPhois as $chiTiet)
                    <tr>
                      <td>
                        {{ $chiTiet->nguonLuc->hangHoa->tenHangHoa ?? '-' }}

                        @if ($chiTiet->nguonLuc?->hangHoa?->donViTinh)
                          <small class="text-muted">
                            ({{ $chiTiet->nguonLuc->hangHoa->donViTinh }})
                          </small>
                        @endif
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->soLuongGiao }}
                      </td>

                      <td>
                        #{{ $chiTiet->tiepNhan->yeuCau->idYeuCau ?? '-' }}
                        - {{ $chiTiet->tiepNhan->yeuCau->tieuDeYeuCau ?? '-' }}

                        <br>

                        <small class="text-muted">
                          {{ $chiTiet->tiepNhan->yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
                          {{ $chiTiet->tiepNhan->yeuCau->diaDiem->phuongXa ?? '' }},
                          {{ $chiTiet->tiepNhan->yeuCau->diaDiem->tinhThanh ?? '' }}
                        </small>
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->nguoiNhan ?? '-' }}
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->trangThai ?? '-' }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">
            Chưa có đợt phân phối nào cho chiến dịch này.
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@if ($coToaDoChienDich)
  <script id="chienDichToaDoData" type="application/json">
  {!! json_encode([
      'viDo' => $viDoChienDich,
      'kinhDo' => $kinhDoChienDich,
      'tenChienDich' => $chienDich->tenChienDich,
      'diaDiem' => $diaDiemDayDu,
  ], JSON_UNESCAPED_UNICODE) !!}
  </script>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.clickable-row').forEach(function (row) {
      row.addEventListener('click', function () {
        const href = row.getAttribute('data-href');

        if (href) {
          window.location.href = href;
        }
      });
    });

    const toaDoDataElement = document.getElementById('chienDichToaDoData');

    if (toaDoDataElement && document.getElementById('chienDichDiaDiemMap')) {
      const toaDoData = JSON.parse(toaDoDataElement.textContent);

      const lat = parseFloat(toaDoData.viDo);
      const lng = parseFloat(toaDoData.kinhDo);

      if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
        const map = L.map('chienDichDiaDiemMap').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker([lat, lng])
          .addTo(map)
          .bindPopup(
            '<strong>' + toaDoData.tenChienDich + '</strong><br>' + toaDoData.diaDiem
          )
          .openPopup();
      }
    }
  });
</script>
@endsection