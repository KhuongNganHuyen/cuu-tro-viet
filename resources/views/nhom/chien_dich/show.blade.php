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

  .chien-dich-yeu-cau-table th,
  .chien-dich-yeu-cau-table td {
    vertical-align: middle;
    padding-top: 14px;
    padding-bottom: 14px;
  }

  .chien-dich-yeu-cau-table th {
    line-height: 1.35;
    font-weight: 700;
  }

  .chien-dich-yeu-cau-table td {
    line-height: 1.5;
  }

  .chien-dich-yeu-cau-table .cell-nowrap {
    white-space: nowrap;
  }

  .yeu-cau-main-cell {
    min-width: 260px;
  }

  .yeu-cau-title {
    color: #212529;
    line-height: 1.45;
  }

  .yeu-cau-address {
    line-height: 1.45;
  }

  .muc-do-label {
    font-weight: 600;
    color: #495057;
  }

  .muc-do-danger {
    color: #dc3545;
  }

  .muc-do-warning {
    color: #b58100;
  }

  .muc-do-info {
    color: #0d6efd;
  }

  .muc-do-muted {
    color: #6c757d;
  }

  .dong-gop-card {
    background-color: #fff;
  }

  .dong-gop-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
  }

  .dong-gop-avatar-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e9f2ff;
    color: #0d6efd;
    font-weight: 700;
    border: 1px solid #cfe2ff;
  }

  .dong-gop-ghi-chu {
    white-space: pre-line;
    line-height: 1.6;
  }

  .dong-gop-detail-table th,
  .dong-gop-detail-table td {
    vertical-align: middle;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .status-warning {
    background-color: #ffc107;
  }

  .status-success {
    background-color: #198754;
  }

  .status-danger {
    background-color: #dc3545;
  }

  .status-secondary {
    background-color: #6c757d;
  }

  .nguon-luc-table th,
  .nguon-luc-table td {
    vertical-align: middle;
  }

  .filter-heading-cell {
    position: relative;
    padding: 0 !important;
  }

  .filter-heading-button {
    width: 100%;
    min-height: 52px;
    padding: 12px;
    border: 0;
    background: transparent;
    color: inherit;
    font: inherit;
    font-weight: 600;
    text-transform: uppercase;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .filter-heading-button:hover,
  .filter-heading-button:focus {
    background-color: #f8f9fa;
  }

  .filter-heading-button::after {
    display: none !important;
  }

  .filter-active-dot {
    width: 6px;
    height: 6px;
    display: none;
    border-radius: 50%;
    background-color: #0d6efd;
  }

  .filter-heading-button.is-filtering .filter-active-dot {
    display: inline-block;
  }

  .filter-dropdown-menu {
    min-width: 190px;
    max-height: 220px;
    overflow-y: auto;
    padding: 6px 0;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
  }

  .filter-dropdown-menu .dropdown-item {
    padding: 8px 14px;
    font-size: 14px;
  }

  .nguon-luc-header {
    padding-bottom: 4px;
  }

  .nguon-luc-divider {
    border-top: 1px solid #dee2e6;
    margin: 12px 0 10px;
  }

  .search-input-wrapper {
    position: relative;
  }

  .nguon-luc-search-wrapper {
    width: 300px;
    max-width: 300px;
    flex: 0 0 300px;
  }

  .search-input-wrapper .icon-search {
    position: absolute;
    left: 12px;
    top: 50%;
    width: 16px;
    height: 16px;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
  }

  .nguon-luc-search-box {
    height: 38px;
    padding-left: 38px;
  }

  .search-reset-button {
    width: 38px;
    height: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .nguon-luc-table {
    width: 100%;
  }

  .nguon-luc-table th,
  .nguon-luc-table td {
    vertical-align: middle;
    padding-top: 14px;
    padding-bottom: 14px;
  }

  .nguon-luc-table th {
    line-height: 1.35;
    font-weight: 700;
  }

  .nguon-luc-table td {
    line-height: 1.5;
  }

  .nguon-luc-table td:nth-child(1),
  .nguon-luc-table td:nth-child(4),
  .nguon-luc-table td:nth-child(5),
  .nguon-luc-table td:nth-child(6),
  .nguon-luc-table td:nth-child(7),
  .nguon-luc-table td:nth-child(8),
  .nguon-luc-table td:nth-child(9) {
    white-space: nowrap;
  }

  .nguon-luc-table td:nth-child(2),
  .nguon-luc-table td:nth-child(3) {
    word-break: break-word;
  }

  .search-input-wrapper .icon-search {
    position: absolute;
    left: 12px;
    top: 50%;
    width: 16px;
    height: 16px;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
  }

  .nguon-luc-search-box {
    padding-left: 38px;
  }

  .search-reset-button {
    width: 38px;
    height: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .chien-dich-phan-phoi-table th,
  .chien-dich-phan-phoi-table td {
    vertical-align: middle;
    padding-top: 14px;
    padding-bottom: 14px;
  }

  .chien-dich-phan-phoi-table .cell-nowrap {
    white-space: nowrap;
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
            <strong class="text-body">{{ $chienDich->idChienDich }}</strong>
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
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="info-label">Mã chiến dịch</div>
                  <div class="info-value">{{ $chienDich->idChienDich }}</div>
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
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0 chien-dich-yeu-cau-table">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 70px;">Mã</th>
                <th class="text-start">Yêu cầu cứu trợ</th>
                <th style="width: 95px;">Số người</th>
                <th style="width: 120px;">Mức độ</th>
                <th style="width: 145px;">Trạng thái</th>
                <th style="width: 135px;">Dự kiến hỗ trợ</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($tiepNhanYeuCaus as $tiepNhan)
                @php
                  $yeuCau = $tiepNhan->yeuCau;

                  $diaChiYeuCau = collect([
                      $yeuCau->diaDiem->chiTietDiaDiem ?? null,
                      $yeuCau->diaDiem->phuongXa ?? null,
                      $yeuCau->diaDiem->tinhThanh ?? null,
                  ])->filter()->implode(', ');

                  $mucDoKhanCap = $yeuCau->mucDoKhanCap ?? '-';

                  $classMucDo = match ($mucDoKhanCap) {
                      'Khẩn cấp' => 'muc-do-danger',
                      'Cao' => 'muc-do-warning',
                      'Trung bình' => 'muc-do-info',
                      'Thấp' => 'muc-do-muted',
                      default => 'muc-do-muted',
                  };

                  $trangThaiTiepNhan = $tiepNhan->trangThai ?? '-';

                  $classTrangThaiTiepNhan = match ($trangThaiTiepNhan) {
                      'Đã tiếp nhận' => 'status-active',
                      'Đang hỗ trợ' => 'status-active',
                      'Cần thêm hỗ trợ' => 'status-paused',
                      'Hoàn thành' => 'status-completed',
                      default => 'status-default',
                  };
                @endphp

                <tr class="clickable-row"
                    data-href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $tiepNhan->idYeuCau) }}">
                  <td class="text-center">
                    {{ $yeuCau->idYeuCau ?? '-' }}
                  </td>

                  <td class="yeu-cau-main-cell">
                    <div class="fw-semibold yeu-cau-title">
                      {{ $yeuCau->tieuDeYeuCau ?? '-' }}
                    </div>

                    <div class="small text-muted mt-1">
                      Người gửi: {{ $yeuCau->nguoiGui->hoTen ?? '-' }}
                    </div>

                    <div class="small text-muted mt-1 yeu-cau-address">
                      {{ $diaChiYeuCau !== '' ? $diaChiYeuCau : '-' }}
                    </div>
                  </td>

                  <td class="text-center cell-nowrap">
                    {{ $yeuCau->soNguoi ?? '-' }}
                  </td>

                  <td class="text-center cell-nowrap">
                    <span class="muc-do-label {{ $classMucDo }}">
                      {{ $mucDoKhanCap }}
                    </span>
                  </td>

                  <td class="text-center cell-nowrap">
                    <span class="d-inline-flex align-items-center justify-content-center gap-2">
                      <span class="status-dot {{ $classTrangThaiTiepNhan }}"></span>
                      <span>{{ $trangThaiTiepNhan }}</span>
                    </span>
                  </td>

                  <td class="text-center cell-nowrap">
                    {{ $tiepNhan->thoiGianDuKienHoTro
                        ? \Carbon\Carbon::parse($tiepNhan->thoiGianDuKienHoTro)->format('d/m/Y')
                        : '-' }}
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
        <div class="mb-3">
          <h5 class="mb-0">Đóng góp cho chiến dịch</h5>
        </div>

        @forelse ($dongGops as $dongGop)
          @php
            $nguoiUngHo = $dongGop->nguoiUngHo ?? null;
            $nguoiTiepNhan = $dongGop->thanhVienTiepNhan->nguoiDung ?? null;

            $tenNguoiUngHo = $nguoiUngHo->hoTen ?? 'Người ủng hộ';
            $chuCaiDaiDien = mb_substr($tenNguoiUngHo, 0, 1, 'UTF-8');

            $thoiGianDongGop = $dongGop->thoiGianDongGop
                ? \Carbon\Carbon::parse($dongGop->thoiGianDongGop)->format('d/m/Y H:i')
                : '-';
          @endphp

          <div class="border rounded-3 p-3 mb-3 dong-gop-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
              <div class="d-flex align-items-center gap-3">
                @if (!empty($nguoiUngHo?->anhDaiDien))
                  <img src="{{ asset('storage/' . $nguoiUngHo->anhDaiDien) }}"
                      alt="{{ $tenNguoiUngHo }}"
                      class="dong-gop-avatar">
                @else
                  <div class="dong-gop-avatar dong-gop-avatar-placeholder">
                    {{ $chuCaiDaiDien }}
                  </div>
                @endif

                <div>
                  <div class="fw-semibold">
                    {{ $tenNguoiUngHo }}
                  </div>

                  <small class="text-muted d-block">
                    {{ $thoiGianDongGop }}
                  </small>

                  <small class="text-muted d-block mt-1">
                    Người tiếp nhận:
                    @if ($nguoiTiepNhan)
                      {{ $nguoiTiepNhan->hoTen ?? '-' }} - {{ $nguoiTiepNhan->tenDangNhap ?? '-' }}
                    @else
                      -
                    @endif
                  </small>
                </div>
              </div>

              <div class="text-muted small">
                Mã đóng góp: {{ $dongGop->idDongGop }}
              </div>
            </div>

            @if (!empty($dongGop->ghiChu))
              <div class="mb-3">
                <div class="small text-muted mb-1">Ghi chú</div>

                <div class="dong-gop-ghi-chu">
                  {{ $dongGop->ghiChu }}
                </div>
              </div>
            @endif

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 dong-gop-detail-table">
                <thead>
                  <tr class="text-uppercase text-center">
                    <th style="width: 70px;">Mã</th>
                    <th class="text-start">Hàng hóa</th>
                    <th class="text-start" style="width: 210px;">Danh mục</th>
                    <th style="width: 110px;">Số lượng</th>
                    <th style="width: 140px;">Hạn sử dụng</th>
                    <th style="width: 150px;">Trạng thái</th>
                    <th style="width: 140px;">Thao tác</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach ($dongGop->chiTietDongGops as $chiTiet)
                    @php
                      $trangThaiChiTiet = $chiTiet->trangThai ?? '-';

                      $classTrangThaiDongGop = match ($trangThaiChiTiet) {
                          'Chờ xác nhận' => 'status-warning',
                          'Đã xác nhận' => 'status-success',
                          'Từ chối' => 'status-danger',
                          default => 'status-secondary',
                      };
                    @endphp

                    <tr>
                      <td class="text-center">
                        {{ $chiTiet->idChiTietDongGop ?? '-' }}
                      </td>

                      <td>
                        <div class="fw-semibold">
                          {{ $chiTiet->hangHoa->tenHangHoa ?? '-' }}
                        </div>

                        @if (!empty($chiTiet->hangHoa?->donViTinh))
                          <small class="text-muted">
                            Đơn vị: {{ $chiTiet->hangHoa->donViTinh }}
                          </small>
                        @endif
                      </td>

                      <td>
                        {{ $chiTiet->hangHoa->danhMucHang->tenDanhMucHang ?? '-' }}
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->soLuong ?? 0 }}
                      </td>

                      <td class="text-center">
                        @if ($chiTiet->hanSuDung)
                          {{ \Carbon\Carbon::parse($chiTiet->hanSuDung)->format('d/m/Y') }}
                        @else
                          -
                        @endif
                      </td>

                      <td class="text-center">
                        <span class="d-inline-flex align-items-center gap-2">
                          <span class="status-dot {{ $classTrangThaiDongGop }}"></span>
                          {{ $trangThaiChiTiet }}
                        </span>
                      </td>

                      <td class="text-center">
                        @if ($chiTiet->trangThai === 'Chờ xác nhận' && !$chienDichDaHoanThanh)
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
        <div class="nguon-luc-header mb-3">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
              <h5 class="mb-0">Nguồn lực chiến dịch</h5>
            </div>

            @if ($laNhomTruong && !$chienDichDaHoanThanh)
              <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/nguon-luc/cap-nhat') }}"
                class="btn btn-primary">
                Cập nhật nguồn lực
              </a>
            @endif
          </div>

          <div class="nguon-luc-divider"></div>

          <div class="d-flex justify-content-end">
            <div class="d-flex align-items-center gap-2">
              <div class="search-input-wrapper nguon-luc-search-wrapper">
                <i data-feather="search" class="icon-search"></i>

                <input type="text"
                      id="timKiemNguonLuc"
                      class="form-control nguon-luc-search-box"
                      placeholder="Tìm kiếm...">
              </div>

              <button type="button"
                      id="xoaLocNguonLuc"
                      class="btn btn-light border search-reset-button d-none"
                      title="Xóa tìm kiếm và bộ lọc">
                <i data-feather="x"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0 nguon-luc-table">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 60px;">Mã</th>

                <th class="text-start" style="width: 170px;">Hàng hóa</th>

                <th class="filter-heading-cell" style="width: 175px;">
                  <div class="dropdown">
                    <button type="button"
                            class="filter-heading-button"
                            id="btnLocDanhMucNguonLuc"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                      Danh mục
                      <span class="filter-active-dot"></span>
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu"
                        aria-labelledby="btnLocDanhMucNguonLuc"
                        id="menuLocDanhMucNguonLuc">
                      <li>
                        <button type="button"
                                class="dropdown-item nguon-luc-filter-option active"
                                data-filter-type="danhMuc"
                                data-filter-value="">
                          Tất cả danh mục
                        </button>
                      </li>
                    </ul>
                  </div>
                </th>

                <th class="filter-heading-cell" style="width: 90px;">
                  <div class="dropdown">
                    <button type="button"
                            class="filter-heading-button"
                            id="btnLocDonViNguonLuc"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                      Đơn vị
                      <span class="filter-active-dot"></span>
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu"
                        aria-labelledby="btnLocDonViNguonLuc"
                        id="menuLocDonViNguonLuc">
                      <li>
                        <button type="button"
                                class="dropdown-item nguon-luc-filter-option active"
                                data-filter-type="donVi"
                                data-filter-value="">
                          Tất cả đơn vị
                        </button>
                      </li>
                    </ul>
                  </div>
                </th>

                <th style="width: 115px;">Cần kêu gọi</th>
                <th style="width: 105px;">Đã nhận</th>
                <th style="width: 105px;">Hiện còn</th>

                <th class="filter-heading-cell" style="width: 145px;">
                  <div class="dropdown">
                    <button type="button"
                            class="filter-heading-button"
                            id="btnLocTrangThaiNguonLuc"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                      Trạng thái
                      <span class="filter-active-dot"></span>
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu"
                        aria-labelledby="btnLocTrangThaiNguonLuc"
                        id="menuLocTrangThaiNguonLuc">
                      <li>
                        <button type="button"
                                class="dropdown-item nguon-luc-filter-option active"
                                data-filter-type="trangThai"
                                data-filter-value="">
                          Tất cả trạng thái
                        </button>
                      </li>
                    </ul>
                  </div>
                </th>

                <th style="width: 135px;">Ngày cập nhật</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($nguonLucs as $nguonLuc)
                @php
                  $trangThaiNguonLuc = $nguonLuc->trangThaiTong ?? '-';

                  $classTrangThaiNguonLuc = match ($trangThaiNguonLuc) {
                      'Đang kêu gọi' => 'status-active',
                      'Đủ số lượng' => 'status-completed',
                      'Đã đóng' => 'status-secondary',
                      default => 'status-default',
                  };

                  $tenHangHoa = $nguonLuc->hangHoa->tenHangHoa ?? '-';
                  $tenDanhMuc = $nguonLuc->hangHoa->danhMucHang->tenDanhMucHang ?? '-';
                  $donViTinh = $nguonLuc->hangHoa->donViTinh ?? '-';

                  $noiDungTimKiemNguonLuc = mb_strtolower(
                      implode(' ', [
                          $nguonLuc->idHangHoa,
                          $tenHangHoa,
                          $tenDanhMuc,
                          $donViTinh,
                          $trangThaiNguonLuc,
                          $nguonLuc->tongSoLuongCanKeuGoi ?? '',
                          $nguonLuc->tongSoLuongDaNhan ?? '',
                          $nguonLuc->tongSoLuongHienCo ?? '',
                      ]),
                      'UTF-8'
                  );
                @endphp

                <tr class="nguon-luc-row"
                    data-search="{{ $noiDungTimKiemNguonLuc }}"
                    data-danh-muc="{{ $tenDanhMuc }}"
                    data-don-vi="{{ $donViTinh }}"
                    data-trang-thai="{{ $trangThaiNguonLuc }}">
                  <td class="text-center">
                    {{ $nguonLuc->idHangHoa ?? '-' }}
                  </td>

                  <td>
                    <div class="fw-semibold">
                      {{ $tenHangHoa }}
                    </div>
                  </td>

                  <td class="text-center">
                    {{ $tenDanhMuc }}
                  </td>

                  <td class="text-center">
                    {{ $donViTinh }}
                  </td>

                  <td class="text-center">
                    {{ number_format($nguonLuc->tongSoLuongCanKeuGoi ?? 0, 2) }}
                  </td>

                  <td class="text-center">
                    {{ number_format($nguonLuc->tongSoLuongDaNhan ?? 0, 2) }}
                  </td>

                  <td class="text-center">
                    {{ number_format($nguonLuc->tongSoLuongHienCo ?? 0, 2) }}
                  </td>

                  <td class="text-center">
                    <span class="d-inline-flex align-items-center gap-2">
                      <span class="status-dot {{ $classTrangThaiNguonLuc }}"></span>
                      {{ $trangThaiNguonLuc }}
                    </span>
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->ngayCapNhatMoiNhat
                        ? \Carbon\Carbon::parse($nguonLuc->ngayCapNhatMoiNhat)->format('d/m/Y H:i')
                        : '-' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9"
                      class="text-center text-muted py-4">
                    Chưa có nguồn lực nào cho chiến dịch này.
                  </td>
                </tr>
              @endforelse

              <tr id="khongCoNguonLucPhuHop" style="display: none;">
                <td colspan="9"
                    class="text-center text-muted py-4">
                  Không có nguồn lực phù hợp với điều kiện tìm kiếm/lọc.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- TAB 6: PHÂN PHỐI --}}
      <div class="tab-pane fade" id="phan-phoi" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
          <div>
            <h5 class="mb-0">Đợt phân phối</h5>
          </div>

          @if (!$chienDichDaHoanThanh)
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi/create') }}"
              class="btn btn-primary">
              Tạo đợt phân phối
            </a>
          @endif
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0 chien-dich-phan-phoi-table">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 80px;">Mã</th>
                <th style="width: 180px;">Ngày phân phối</th>
                <th style="width: 150px;">Số dòng</th>
                <th style="width: 180px;">Tổng lượng giao</th>
                <th style="width: 180px;">Trạng thái</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($dotPhanPhois as $dotPhanPhoi)
                @php
                  $soDongChiTiet = $dotPhanPhoi->chiTietPhanPhois->count();
                  $tongLuongGiao = $dotPhanPhoi->chiTietPhanPhois->sum('soLuongGiao');

                  $classTrangThaiDot = match ($dotPhanPhoi->trangThai ?? '') {
                      'Đang chuẩn bị' => 'status-paused',
                      'Đang phân phối' => 'status-active',
                      'Hoàn thành' => 'status-completed',
                      'Đã hủy' => 'status-default',
                      default => 'status-default',
                  };
                @endphp

                <tr class="clickable-row"
                    data-href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi/' . $dotPhanPhoi->idDotPhanPhoi) }}">
                  <td class="text-center">
                    {{ $dotPhanPhoi->idDotPhanPhoi }}
                  </td>

                  <td class="text-center cell-nowrap">
                    {{ $dotPhanPhoi->ngayPhanPhoi
                        ? \Carbon\Carbon::parse($dotPhanPhoi->ngayPhanPhoi)->format('d/m/Y H:i')
                        : '-' }}
                  </td>

                  <td class="text-center cell-nowrap">
                    {{ $soDongChiTiet }}
                  </td>

                  <td class="text-center cell-nowrap">
                    {{ number_format($tongLuongGiao, 2) }}
                  </td>

                  <td class="text-center cell-nowrap">
                    <span class="d-inline-flex align-items-center justify-content-center gap-2">
                      <span class="status-dot {{ $classTrangThaiDot }}"></span>
                      <span>{{ $dotPhanPhoi->trangThai ?? '-' }}</span>
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5"
                      class="text-center text-muted py-4">
                    Chưa có đợt phân phối nào cho chiến dịch này.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
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

    const nguonLucRows = Array.from(
      document.querySelectorAll('.nguon-luc-row')
    );

    const timKiemNguonLucInput = document.getElementById('timKiemNguonLuc');
    const xoaLocNguonLucButton = document.getElementById('xoaLocNguonLuc');
    const khongCoNguonLucPhuHop = document.getElementById('khongCoNguonLucPhuHop');

    const boLocNguonLuc = {
      danhMuc: '',
      donVi: '',
      trangThai: '',
    };

    const cauHinhLocNguonLuc = {
      danhMuc: {
        menuId: 'menuLocDanhMucNguonLuc',
        buttonId: 'btnLocDanhMucNguonLuc',
        dataKey: 'danhMuc',
        allText: 'Tất cả danh mục',
      },
      donVi: {
        menuId: 'menuLocDonViNguonLuc',
        buttonId: 'btnLocDonViNguonLuc',
        dataKey: 'donVi',
        allText: 'Tất cả đơn vị',
      },
      trangThai: {
        menuId: 'menuLocTrangThaiNguonLuc',
        buttonId: 'btnLocTrangThaiNguonLuc',
        dataKey: 'trangThai',
        allText: 'Tất cả trạng thái',
      },
    };

    function boDauTiengVietJS(value) {
      return (value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');
    }

    function layGiaTriLocNguonLuc(dataKey) {
      const values = [];

      nguonLucRows.forEach(function (row) {
        const value = row.dataset[dataKey] || '';

        if (value !== '' && value !== '-' && !values.includes(value)) {
          values.push(value);
        }
      });

      return values.sort(function (a, b) {
        return a.localeCompare(b, 'vi');
      });
    }

    function taoNutLocNguonLuc(filterType, value, label) {
      const li = document.createElement('li');
      const button = document.createElement('button');

      button.type = 'button';
      button.className = 'dropdown-item nguon-luc-filter-option';
      button.dataset.filterType = filterType;
      button.dataset.filterValue = value;
      button.textContent = label;

      button.addEventListener('click', function () {
        boLocNguonLuc[filterType] = value;
        locNguonLuc();
      });

      li.appendChild(button);

      return li;
    }

    function khoiTaoMenuLocNguonLuc() {
      Object.keys(cauHinhLocNguonLuc).forEach(function (filterType) {
        const config = cauHinhLocNguonLuc[filterType];
        const menu = document.getElementById(config.menuId);

        if (!menu) {
          return;
        }

        menu.innerHTML = '';

        menu.appendChild(
          taoNutLocNguonLuc(filterType, '', config.allText)
        );

        layGiaTriLocNguonLuc(config.dataKey).forEach(function (value) {
          menu.appendChild(
            taoNutLocNguonLuc(filterType, value, value)
          );
        });
      });
    }

    function capNhatTrangThaiNutLocNguonLuc() {
      document
        .querySelectorAll('.nguon-luc-filter-option')
        .forEach(function (button) {
          const filterType = button.dataset.filterType;
          const filterValue = button.dataset.filterValue || '';

          button.classList.toggle(
            'active',
            boLocNguonLuc[filterType] === filterValue
          );
        });

      Object.keys(cauHinhLocNguonLuc).forEach(function (filterType) {
        const config = cauHinhLocNguonLuc[filterType];
        const button = document.getElementById(config.buttonId);

        if (!button) {
          return;
        }

        button.classList.toggle(
          'is-filtering',
          boLocNguonLuc[filterType] !== ''
        );
      });

      const dangTimKiem =
        (timKiemNguonLucInput?.value.trim() || '') !== '';

      const dangLoc =
        dangTimKiem
        || boLocNguonLuc.danhMuc !== ''
        || boLocNguonLuc.donVi !== ''
        || boLocNguonLuc.trangThai !== '';

      if (xoaLocNguonLucButton) {
        xoaLocNguonLucButton.classList.toggle('d-none', !dangLoc);
      }
    }

    function locNguonLuc() {
      const tuKhoa = boDauTiengVietJS(
        timKiemNguonLucInput?.value.trim() || ''
      );

      let soDongHienThi = 0;

      nguonLucRows.forEach(function (row) {
        const noiDung = boDauTiengVietJS(row.dataset.search || '');

        const hopTuKhoa =
          tuKhoa === '' || noiDung.includes(tuKhoa);

        const hopDanhMuc =
          boLocNguonLuc.danhMuc === ''
          || row.dataset.danhMuc === boLocNguonLuc.danhMuc;

        const hopDonVi =
          boLocNguonLuc.donVi === ''
          || row.dataset.donVi === boLocNguonLuc.donVi;

        const hopTrangThai =
          boLocNguonLuc.trangThai === ''
          || row.dataset.trangThai === boLocNguonLuc.trangThai;

        const hienThi =
          hopTuKhoa && hopDanhMuc && hopDonVi && hopTrangThai;

        row.style.display = hienThi ? '' : 'none';

        if (hienThi) {
          soDongHienThi++;
        }
      });

      if (khongCoNguonLucPhuHop) {
        khongCoNguonLucPhuHop.style.display =
          soDongHienThi === 0 && nguonLucRows.length > 0 ? '' : 'none';
      }

      capNhatTrangThaiNutLocNguonLuc();
    }

    khoiTaoMenuLocNguonLuc();
    locNguonLuc();

    if (timKiemNguonLucInput) {
      timKiemNguonLucInput.addEventListener('input', locNguonLuc);
    }

    if (xoaLocNguonLucButton) {
      xoaLocNguonLucButton.addEventListener('click', function () {
        if (timKiemNguonLucInput) {
          timKiemNguonLucInput.value = '';
        }

        boLocNguonLuc.danhMuc = '';
        boLocNguonLuc.donVi = '';
        boLocNguonLuc.trangThai = '';

        locNguonLuc();
      });
    }

    if (window.feather) {
      feather.replace();
    }
  });
</script>
@endsection