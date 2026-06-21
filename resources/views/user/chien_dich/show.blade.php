@extends('layouts.user')

@section('title', 'Chi tiết chiến dịch | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #chienDichDiaDiemMap,
  #thongKeBanDoMap {
    height: 360px;
    width: 100%;
    border-radius: 12px;
  }

  .map-card,
  .info-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 16px;
    background-color: #ffffff;
  }

  .info-card {
    height: 100%;
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

  .status-upcoming { background-color: #0dcaf0; }
  .status-active,
  .status-success { background-color: #198754; }
  .status-paused,
  .status-warning { background-color: #ffc107; }
  .status-completed { background-color: #0d6efd; }
  .status-danger { background-color: #dc3545; }
  .status-default,
  .status-secondary { background-color: #6c757d; }

  .description-box,
  .cap-nhat-noi-dung {
    white-space: pre-line;
  }

  .description-box,
  .cap-nhat-noi-dung {
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
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
  }

  .cap-nhat-avatar {
    width: 42px;
    height: 42px;
    border: 1px solid #dee2e6;
  }

  .cap-nhat-avatar-placeholder {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
  }

  .cap-nhat-avatar-placeholder {
    background-color: #e9ecef;
    color: #495057;
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
  .chien-dich-yeu-cau-table td,
  .dong-gop-detail-table th,
  .dong-gop-detail-table td,
  .nguon-luc-table th,
  .nguon-luc-table td,
  .chien-dich-phan-phoi-table th,
  .chien-dich-phan-phoi-table td {
    vertical-align: middle;
    padding-top: 14px;
    padding-bottom: 14px;
  }

  .chien-dich-yeu-cau-table th,
  .nguon-luc-table th {
    line-height: 1.35;
    font-weight: 700;
  }

  .chien-dich-yeu-cau-table td,
  .nguon-luc-table td {
    line-height: 1.5;
  }

  .chien-dich-yeu-cau-table .cell-nowrap,
  .chien-dich-phan-phoi-table .cell-nowrap {
    white-space: nowrap;
  }

  .yeu-cau-main-cell {
    min-width: 260px;
  }

  .yeu-cau-title,
  .yeu-cau-address {
    line-height: 1.45;
  }

  .yeu-cau-title {
    color: #212529;
  }

  .muc-do-label { font-weight: 600; color: #495057; }
  .muc-do-danger { color: #dc3545; }
  .muc-do-warning { color: #b58100; }
  .muc-do-info { color: #0d6efd; }
  .muc-do-muted { color: #6c757d; }

  .dong-gop-detail-table {
    min-width: 1180px;
  }

  .dong-gop-detail-table th:nth-child(1),
  .dong-gop-detail-table th:nth-child(3),
  .dong-gop-detail-table th:nth-child(7),
  .dong-gop-detail-table th:nth-child(8),
  .dong-gop-detail-table th:nth-child(9),
  .dong-gop-detail-table th:nth-child(10),
  .dong-gop-detail-table td:nth-child(1),
  .dong-gop-detail-table td:nth-child(3),
  .dong-gop-detail-table td:nth-child(7),
  .dong-gop-detail-table td:nth-child(8),
  .dong-gop-detail-table td:nth-child(9),
  .dong-gop-detail-table td:nth-child(10),
  .nguon-luc-table td:nth-child(1),
  .nguon-luc-table td:nth-child(4),
  .nguon-luc-table td:nth-child(5),
  .nguon-luc-table td:nth-child(6),
  .nguon-luc-table td:nth-child(7),
  .nguon-luc-table td:nth-child(8),
  .nguon-luc-table td:nth-child(9) {
    white-space: nowrap;
  }

  .dong-gop-detail-table td:nth-child(2),
  .dong-gop-detail-table td:nth-child(4) {
    min-width: 180px;
    white-space: nowrap;
  }

  .dong-gop-detail-table td:nth-child(5),
  .dong-gop-detail-table td:nth-child(6) {
    min-width: 210px;
    white-space: normal;
    word-break: break-word;
  }

  .dong-gop-group-odd td {
    background-color: #ffffff;
  }

  .dong-gop-group-even td {
    background-color: #f8fafc;
  }

  .dong-gop-group-start td {
    border-top: 2px solid #dbe3ef !important;
  }

  .dong-gop-detail-table tbody tr:hover td {
    background-color: #f8f9fa;
  }

  .nguon-luc-header {
    padding-bottom: 4px;
  }

  .nguon-luc-divider {
    border-top: 1px solid #dee2e6;
    margin: 12px 0 10px;
  }

  .nguon-luc-table {
    width: 100%;
  }

  .nguon-luc-table td:nth-child(2),
  .nguon-luc-table td:nth-child(3) {
    word-break: break-word;
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

  .search-input-wrapper {
    position: relative;
  }

  .nguon-luc-search-wrapper,
  .dong-gop-search-wrapper {
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

  .nguon-luc-search-box,
  .dong-gop-search-box {
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

  .thong-ke-card {
    border: 1px solid #e5e7eb !important;
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.04) !important;
  }

  .thong-ke-card .card-body {
    padding: 22px 24px;
  }

  .thong-ke-metric-row {
    display: flex;
    align-items: baseline;
    gap: 10px;
    margin-bottom: 12px;
  }

  .thong-ke-number {
    font-size: 38px;
    line-height: 1;
    font-weight: 700;
  }

  .thong-ke-label {
    font-size: 15px;
    color: #495057;
    font-weight: 500;
  }

  .thong-ke-desc {
    font-size: 13px;
    color: #6c757d;
  }

  .thong-ke-primary { color: #0d6efd; }
  .thong-ke-success { color: #198754; }
  .thong-ke-warning { color: #f59f00; }
  .thong-ke-danger { color: #dc3545; }

  .thong-ke-chart-scroll {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
  }

  .thong-ke-chart-scroll #bieuDoNhanPhatHangHoa {
    min-width: 640px;
  }
</style>

@php
  $soYeuCauTiepNhan = $tiepNhanYeuCaus->count();

  $soLuotNhanDongGop = $dongGops->filter(function ($dongGop) {
      return $dongGop->chiTietDongGops->contains('trangThai', 'Đã xác nhận');
  })->count();

  $soHangHoaKeuGoi = $nguonLucs->count();

  $soDotPhanPhoi = $dotPhanPhois->count();

  $tongDaNhan = $nguonLucs->sum('tongSoLuongDaNhan');

  $tongDaPhanPhoi = $dotPhanPhois
      ->flatMap(function ($dotPhanPhoi) {
          return $dotPhanPhoi->chiTietPhanPhois;
      })
      ->sum(function ($chiTiet) {
          return $chiTiet->soLuongGiao ?? 0;
      });

  $phanTramDaPhanPhoi = $tongDaNhan > 0
      ? round(($tongDaPhanPhoi / $tongDaNhan) * 100, 1)
      : 0;

  $phanTramDaPhanPhoiHienThi = min($phanTramDaPhanPhoi, 100);

  $soYeuCauHoanThanh = $tiepNhanYeuCaus
      ->filter(function ($tiepNhan) {
          return $tiepNhan->trangThai === 'Hoàn thành';
      })
      ->count();

  $phanTramYeuCauHoanThanh = $soYeuCauTiepNhan > 0
      ? round(($soYeuCauHoanThanh / $soYeuCauTiepNhan) * 100, 1)
      : 0;

  $phanTramYeuCauHoanThanhHienThi = min($phanTramYeuCauHoanThanh, 100);

  $duLieuNhanPhatTheoHangHoa = $nguonLucs
      ->map(function ($nguonLuc) use ($dotPhanPhois) {
          $idHangHoa = $nguonLuc->idHangHoa;

          $soLuongPhatRa = $dotPhanPhois
              ->flatMap(function ($dotPhanPhoi) {
                  return $dotPhanPhoi->chiTietPhanPhois;
              })
              ->filter(function ($chiTiet) use ($idHangHoa) {
                  return optional($chiTiet->nguonLuc)->idHangHoa == $idHangHoa;
              })
              ->sum(function ($chiTiet) {
                  return $chiTiet->soLuongGiao ?? 0;
              });

          $soLuongNhanVao = (float) ($nguonLuc->tongSoLuongDaNhan ?? 0);

          return [
              'maHangHoa' => '#' . $idHangHoa,
              'tenHangHoa' => $nguonLuc->hangHoa->tenHangHoa ?? 'Không xác định',
              'danhMuc' => $nguonLuc->hangHoa->danhMucHang->tenDanhMucHang ?? 'Chưa phân loại',
              'daNhan' => $soLuongNhanVao,
              'daPhat' => (float) $soLuongPhatRa,
          ];
      })
      ->filter(function ($item) {
          return $item['daNhan'] > 0 || $item['daPhat'] > 0;
      })
      ->values();

  $danhMucThongKeHangHoa = $duLieuNhanPhatTheoHangHoa
      ->pluck('danhMuc')
      ->unique()
      ->values()
      ->toArray();

  $thongKeTrangThaiYeuCau = $tiepNhanYeuCaus
      ->groupBy('trangThai')
      ->map(function ($items) {
          return $items->count();
      });

  $hangHoaDu = $nguonLucs
      ->filter(function ($nguonLuc) {
          return (float) ($nguonLuc->tongSoLuongHienCo ?? 0) > 0;
      })
      ->values();

  $diemBanDoThongKe = collect();

  if ($chienDich->diaDiem && $chienDich->diaDiem->viDo && $chienDich->diaDiem->kinhDo) {
      $diemBanDoThongKe->push([
          'loai' => 'Chiến dịch',
          'ten' => $chienDich->tenChienDich,
          'diaChi' => collect([
              $chienDich->diaDiem->chiTietDiaDiem ?? null,
              $chienDich->diaDiem->phuongXa ?? null,
              $chienDich->diaDiem->tinhThanh ?? null,
          ])->filter()->implode(', '),
          'viDo' => (float) $chienDich->diaDiem->viDo,
          'kinhDo' => (float) $chienDich->diaDiem->kinhDo,
      ]);
  }

  foreach ($tiepNhanYeuCaus as $tiepNhan) {
      $diaDiem = $tiepNhan->yeuCau->diaDiem ?? null;

      if ($diaDiem && $diaDiem->viDo && $diaDiem->kinhDo) {
          $diaChi = collect([
              $diaDiem->chiTietDiaDiem ?? null,
              $diaDiem->phuongXa ?? null,
              $diaDiem->tinhThanh ?? null,
          ])->filter()->implode(', ');

          $diemBanDoThongKe->push([
              'loai' => 'Yêu cầu',
              'ten' => $tiepNhan->yeuCau->tieuDeYeuCau ?? 'Yêu cầu cứu trợ',
              'diaChi' => $diaChi,
              'viDo' => (float) $diaDiem->viDo,
              'kinhDo' => (float) $diaDiem->kinhDo,
          ]);
      }
  }

  foreach ($dotPhanPhois as $dotPhanPhoi) {
      foreach ($dotPhanPhoi->chiTietPhanPhois as $chiTietPhanPhoi) {
          $diaDiem = $chiTietPhanPhoi->diaDiem ?? null;

          if ($diaDiem && $diaDiem->viDo && $diaDiem->kinhDo) {
              $diaChi = collect([
                  $diaDiem->chiTietDiaDiem ?? null,
                  $diaDiem->phuongXa ?? null,
                  $diaDiem->tinhThanh ?? null,
              ])->filter()->implode(', ');

              $diemBanDoThongKe->push([
                  'loai' => 'Phân phối',
                  'ten' => 'Điểm phân phối #' . $chiTietPhanPhoi->idChiTietPhanPhoi,
                  'diaChi' => $diaChi,
                  'viDo' => (float) $diaDiem->viDo,
                  'kinhDo' => (float) $diaDiem->kinhDo,
              ]);
          }
      }
  }

  $duLieuThongKeChienDich = [
      'maHangHoa' => $duLieuNhanPhatTheoHangHoa->pluck('maHangHoa')->toArray(),
      'tenHangHoa' => $duLieuNhanPhatTheoHangHoa->pluck('tenHangHoa')->toArray(),
      'danhMucHangHoa' => $duLieuNhanPhatTheoHangHoa->pluck('danhMuc')->toArray(),
      'danhMucThongKeHangHoa' => $danhMucThongKeHangHoa,
      'duLieuNhanVao' => $duLieuNhanPhatTheoHangHoa->pluck('daNhan')->toArray(),
      'duLieuPhatRa' => $duLieuNhanPhatTheoHangHoa->pluck('daPhat')->toArray(),
      'nhanTrangThaiYeuCau' => $thongKeTrangThaiYeuCau->keys()->values()->toArray(),
      'duLieuTrangThaiYeuCau' => $thongKeTrangThaiYeuCau->values()->toArray(),
      'diemBanDo' => $diemBanDoThongKe->values()->toArray(),
  ];
@endphp

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
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/chien-dich') }}">Chiến dịch cứu trợ</a>
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
        <a href="{{ url('/user/chien-dich') }}"
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
                id="thong-ke-tab"
                data-bs-toggle="tab"
                data-bs-target="#thong-ke"
                type="button"
                role="tab">
          Thống kê
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
                <th style="width: 120px;">Số người</th>
                <th style="width: 120px;">Mức độ</th>
                <th style="width: 145px;">Trạng thái</th>
                <th style="width: 145px;">Dự kiến hỗ trợ</th>
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
                    data-href="{{ url('/user/yeu-cau-cong-dong/' . ($yeuCau->idYeuCau ?? $tiepNhan->idYeuCau)) }}">
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
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
          <h5 class="mb-0">Đóng góp cho chiến dịch</h5>

          <div class="d-flex align-items-center gap-2">
            <div class="search-input-wrapper dong-gop-search-wrapper">
              <i data-feather="search" class="icon-search"></i>

              <input type="text"
                    id="timKiemDongGop"
                    class="form-control dong-gop-search-box"
                    placeholder="Tìm kiếm...">
            </div>

            <button type="button"
                    id="xoaLocDongGop"
                    class="btn btn-light border search-reset-button d-none"
                    title="Xóa tìm kiếm và bộ lọc">
              <i data-feather="x"></i>
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 dong-gop-detail-table">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 110px;">Mã</th>
                <th class="text-start" style="min-width: 180px;">Người gửi</th>
                <th style="width: 150px;">Thời gian gửi</th>
                <th class="text-start" style="min-width: 180px;">Người nhận</th>
                <th class="filter-heading-cell" style="min-width: 190px;">
                  <div class="dropdown">
                    <button type="button"
                            class="filter-heading-button text-start"
                            id="btnLocHangHoaDongGop"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                      Hàng hóa
                      <span class="filter-active-dot"></span>
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu"
                        aria-labelledby="btnLocHangHoaDongGop"
                        id="menuLocHangHoaDongGop">
                      <li>
                        <button type="button"
                                class="dropdown-item dong-gop-filter-option active"
                                data-filter-type="hangHoa"
                                data-filter-value="">
                          Tất cả hàng hóa
                        </button>
                      </li>
                    </ul>
                  </div>
                </th>

                <th class="filter-heading-cell" style="min-width: 180px;">
                  <div class="dropdown">
                    <button type="button"
                            class="filter-heading-button text-start"
                            id="btnLocDanhMucDongGop"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                      Danh mục
                      <span class="filter-active-dot"></span>
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu"
                        aria-labelledby="btnLocDanhMucDongGop"
                        id="menuLocDanhMucDongGop">
                      <li>
                        <button type="button"
                                class="dropdown-item dong-gop-filter-option active"
                                data-filter-type="danhMuc"
                                data-filter-value="">
                          Tất cả danh mục
                        </button>
                      </li>
                    </ul>
                  </div>
                </th>

                <th style="width: 150px;">Số lượng</th>
                <th style="width: 135px;">Hạn sử dụng</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($dongGops as $dongGop)
                @php
                  $classNhomDongGop = $loop->iteration % 2 === 0
                      ? 'dong-gop-group-even'
                      : 'dong-gop-group-odd';

                  $nguoiUngHo = $dongGop->nguoiUngHo ?? null;
                  $nguoiTiepNhan = $dongGop->thanhVienTiepNhan->nguoiDung ?? null;

                  $thoiGianDongGop = $dongGop->thoiGianDongGop
                      ? \Carbon\Carbon::parse($dongGop->thoiGianDongGop)->format('d/m/Y H:i')
                      : '-';
                @endphp

                @forelse ($dongGop->chiTietDongGops as $chiTiet)
                  @php
                    $trangThaiChiTiet = $chiTiet->trangThai ?? '-';

                    $classTrangThaiDongGop = match ($trangThaiChiTiet) {
                        'Chờ xác nhận' => 'status-warning',
                        'Đã xác nhận' => 'status-success',
                        'Từ chối' => 'status-danger',
                        default => 'status-secondary',
                    };

                    $tenHangHoaDongGop = $chiTiet->hangHoa->tenHangHoa ?? '-';
                    $tenDanhMucDongGop = $chiTiet->hangHoa->danhMucHang->tenDanhMucHang ?? '-';
                    $donViTinhDongGop = $chiTiet->hangHoa->donViTinh ?? '-';

                    $noiDungTimKiemDongGop = mb_strtolower(
                        implode(' ', [
                            $dongGop->idDongGop,
                            $nguoiUngHo->hoTen ?? '',
                            $nguoiUngHo->tenDangNhap ?? '',
                            $nguoiTiepNhan->hoTen ?? '',
                            $nguoiTiepNhan->tenDangNhap ?? '',
                            $tenHangHoaDongGop,
                            $tenDanhMucDongGop,
                            $donViTinhDongGop,
                            $trangThaiChiTiet,
                            $chiTiet->soLuong ?? '',
                            $thoiGianDongGop,
                        ]),
                        'UTF-8'
                    );
                  @endphp

                  <tr class="dong-gop-row {{ $classNhomDongGop }} {{ $loop->first ? 'dong-gop-group-start' : '' }}"
                      data-search="{{ $noiDungTimKiemDongGop }}"
                      data-hang-hoa="{{ $tenHangHoaDongGop }}"
                      data-danh-muc="{{ $tenDanhMucDongGop }}"
                      data-trang-thai="{{ $trangThaiChiTiet }}">
                    <td class="text-center">
                      {{ $dongGop->idDongGop }}
                    </td>

                    <td class="text-start">
                      <div class="fw-semibold">
                        {{ $nguoiUngHo->hoTen ?? 'Người ủng hộ' }}
                      </div>

                      <small class="text-muted">
                        {{ $nguoiUngHo->tenDangNhap ?? '-' }}
                      </small>
                    </td>

                    <td class="text-center">
                      {{ $thoiGianDongGop }}
                    </td>

                    <td class="text-start">
                      @if ($nguoiTiepNhan)
                        <div class="fw-semibold">
                          {{ $nguoiTiepNhan->hoTen ?? '-' }}
                        </div>

                        <small class="text-muted">
                          {{ $nguoiTiepNhan->tenDangNhap ?? '-' }}
                        </small>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>

                    <td class="text-start">
                      <div class="fw-semibold">
                        {{ $tenHangHoaDongGop }}
                      </div>

                      <small class="text-muted">
                        Đơn vị: {{ $donViTinhDongGop }}
                      </small>
                    </td>

                    <td class="text-start">
                      {{ $tenDanhMucDongGop }}
                    </td>

                    <td class="text-center">
                      {{ number_format($chiTiet->soLuong ?? 0, 0, ',', '.') }}
                    </td>

                    <td class="text-center">
                      @if ($chiTiet->hanSuDung)
                        {{ \Carbon\Carbon::parse($chiTiet->hanSuDung)->format('d/m/Y') }}
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                      Lượt đóng góp #{{ $dongGop->idDongGop }} chưa có chi tiết hàng hóa.
                    </td>
                  </tr>
                @endforelse
              @empty
                <tr>
                  <td colspan="8" class="text-center text-muted py-4">
                    Chưa có lượt đóng góp nào cho chiến dịch này.
                  </td>
                </tr>
              @endforelse

              <tr id="khongCoDongGopPhuHop" style="display: none;">
                <td colspan="8" class="text-center text-muted py-4">
                  Không có đóng góp phù hợp với điều kiện tìm kiếm/lọc.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- TAB 5: NGUỒN LỰC --}}
      <div class="tab-pane fade" id="nguon-luc" role="tabpanel">
        <div class="nguon-luc-header mb-3">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
              <h5 class="mb-0">Nguồn lực chiến dịch</h5>
            </div>
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
                  $tenHangHoa = $nguonLuc->hangHoa->tenHangHoa ?? '-';
                  $tenDanhMuc = $nguonLuc->hangHoa->danhMucHang->tenDanhMucHang ?? '-';
                  $donViTinh = $nguonLuc->hangHoa->donViTinh ?? '-';

                  // Trang người dùng chỉ xem dữ liệu. Một số màn nhóm dùng các field tổng hợp
                  // như tongSoLuongCanKeuGoi/tongSoLuongDaNhan/tongSoLuongHienCo,
                  // còn dữ liệu lấy trực tiếp từ bảng nguồn lực có thể dùng tên field gốc.
                  $soLuongCanKeuGoi = (float) (
                      $nguonLuc->tongSoLuongCanKeuGoi
                      ?? $nguonLuc->soLuongCanKeuGoi
                      ?? $nguonLuc->soLuongCan
                      ?? $nguonLuc->soLuongMucTieu
                      ?? 0
                  );

                  $soLuongDaNhan = (float) (
                      $nguonLuc->tongSoLuongDaNhan
                      ?? $nguonLuc->soLuongDaNhan
                      ?? $nguonLuc->soLuongDaDongGop
                      ?? 0
                  );

                  if ($soLuongDaNhan <= 0 && isset($dongGops)) {
                      $soLuongDaNhan = (float) $dongGops
                          ->flatMap(function ($dongGop) {
                              return $dongGop->chiTietDongGops;
                          })
                          ->filter(function ($chiTiet) use ($nguonLuc) {
                              return (int) ($chiTiet->idHangHoa ?? optional($chiTiet->hangHoa)->idHangHoa ?? 0)
                                  === (int) ($nguonLuc->idHangHoa ?? 0);
                          })
                          ->sum(function ($chiTiet) {
                              return $chiTiet->soLuong ?? 0;
                          });
                  }

                  $soLuongDaPhanPhoi = (float) (
                      $nguonLuc->tongSoLuongDaPhanPhoi
                      ?? $nguonLuc->soLuongDaPhanPhoi
                      ?? 0
                  );

                  if ($soLuongDaPhanPhoi <= 0 && isset($dotPhanPhois)) {
                      $soLuongDaPhanPhoi = (float) $dotPhanPhois
                          ->flatMap(function ($dotPhanPhoi) {
                              return $dotPhanPhoi->chiTietPhanPhois;
                          })
                          ->filter(function ($chiTiet) use ($nguonLuc) {
                              return (int) (optional($chiTiet->nguonLuc)->idHangHoa ?? 0)
                                  === (int) ($nguonLuc->idHangHoa ?? 0);
                          })
                          ->sum(function ($chiTiet) {
                              return $chiTiet->soLuongGiao ?? 0;
                          });
                  }

                  $soLuongHienCo = (float) (
                      $nguonLuc->tongSoLuongHienCo
                      ?? $nguonLuc->soLuongHienCo
                      ?? $nguonLuc->soLuongConLai
                      ?? 0
                  );

                  if ($soLuongHienCo <= 0 && $soLuongDaNhan > 0) {
                      $soLuongHienCo = max($soLuongDaNhan - $soLuongDaPhanPhoi, 0);
                  }

                  $tongCanKeuGoi = (float) ($nguonLuc->tongSoLuongCanKeuGoi
                      ?? $nguonLuc->soLuongCanKeuGoi
                      ?? 0);

                  $tongDaNhan = (float) ($nguonLuc->tongSoLuongDaNhan
                      ?? $nguonLuc->soLuongDaNhan
                      ?? 0);

                  $tongHienCo = (float) ($nguonLuc->tongSoLuongHienCo
                      ?? $nguonLuc->soLuongHienCo
                      ?? $tongDaNhan);

                  $trangThaiNguonLuc = $nguonLuc->trangThaiTong
                      ?? $nguonLuc->trangThai
                      ?? null;

                  if (!$trangThaiNguonLuc || $trangThaiNguonLuc === '-') {
                      if ($tongCanKeuGoi > 0 && $tongDaNhan >= $tongCanKeuGoi) {
                          $trangThaiNguonLuc = 'Đủ số lượng';
                      } elseif ($tongCanKeuGoi > 0) {
                          $trangThaiNguonLuc = 'Đang kêu gọi';
                      } else {
                          $trangThaiNguonLuc = 'Chưa cập nhật';
                      }
                  }

                  if (!$trangThaiNguonLuc || $trangThaiNguonLuc === '-') {
                      if ($soLuongCanKeuGoi <= 0 && $soLuongDaNhan <= 0) {
                          $trangThaiNguonLuc = 'Chưa cập nhật';
                      } elseif ($soLuongCanKeuGoi > 0 && $soLuongDaNhan >= $soLuongCanKeuGoi) {
                          $trangThaiNguonLuc = 'Đủ số lượng';
                      } else {
                          $trangThaiNguonLuc = 'Đang kêu gọi';
                      }
                  }

                  $classTrangThaiNguonLuc = match ($trangThaiNguonLuc) {
                      'Đang kêu gọi' => 'status-active',
                      'Đủ số lượng' => 'status-completed',
                      'Đã đóng' => 'status-secondary',
                      'Chưa cập nhật' => 'status-warning',
                      default => 'status-default',
                  };

                  $ngayCapNhatNguonLuc = $nguonLuc->ngayCapNhatMoiNhat
                      ?? $nguonLuc->ngayCapNhat
                      ?? $nguonLuc->updated_at
                      ?? null;

                  $noiDungTimKiemNguonLuc = mb_strtolower(
                      implode(' ', [
                          $nguonLuc->idHangHoa,
                          $tenHangHoa,
                          $tenDanhMuc,
                          $donViTinh,
                          $trangThaiNguonLuc,
                          $soLuongCanKeuGoi,
                          $soLuongDaNhan,
                          $soLuongHienCo,
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
                    {{ number_format($tongCanKeuGoi, 2) }}
                  </td>

                  <td class="text-center">
                    {{ number_format($tongDaNhan, 2) }}
                  </td>

                  <td class="text-center">
                    {{ number_format($tongHienCo, 2) }}
                  </td>

                  <td class="text-center">
                    <span class="d-inline-flex align-items-center gap-2">
                      <span class="status-dot {{ $classTrangThaiNguonLuc }}"></span>
                      {{ $trangThaiNguonLuc }}
                    </span>
                  </td>

                  <td class="text-center">
                    {{ $ngayCapNhatNguonLuc
                        ? \Carbon\Carbon::parse($ngayCapNhatNguonLuc)->format('d/m/Y H:i')
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

      {{-- TAB 7: THỐNG KÊ --}}
      <div class="tab-pane fade" id="thong-ke" role="tabpanel">
        <div class="mb-4">
          <h5 class="mb-1">Thống kê chiến dịch</h5>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="card h-100 thong-ke-card">
              <div class="card-body">
                <div class="thong-ke-metric-row">
                  <h4 class="mb-0 thong-ke-number thong-ke-primary">
                    {{ $soYeuCauTiepNhan }}
                  </h4>
                  <span class="thong-ke-label">Yêu cầu tiếp nhận</span>
                </div>

                <div class="thong-ke-desc">
                  Số yêu cầu được gắn với chiến dịch
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card h-100 thong-ke-card">
              <div class="card-body">
                <div class="thong-ke-metric-row">
                  <h4 class="mb-0 thong-ke-number thong-ke-success">
                    {{ $soLuotNhanDongGop }}
                  </h4>
                  <span class="thong-ke-label">Lượt nhận đóng góp</span>
                </div>

                <div class="thong-ke-desc">
                  Lượt đóng góp đã được xác nhận
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card h-100 thong-ke-card">
              <div class="card-body">
                <div class="thong-ke-metric-row">
                  <h4 class="mb-0 thong-ke-number thong-ke-warning">
                    {{ $soHangHoaKeuGoi }}
                  </h4>
                  <span class="thong-ke-label">Hàng hóa kêu gọi</span>
                </div>

                <div class="thong-ke-desc">
                  Số loại hàng hóa trong nguồn lực
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card h-100 thong-ke-card">
              <div class="card-body">
                <div class="thong-ke-metric-row">
                  <h4 class="mb-0 thong-ke-number thong-ke-danger">
                    {{ $soDotPhanPhoi }}
                  </h4>
                  <span class="thong-ke-label">Đợt phân phối</span>
                </div>

                <div class="thong-ke-desc">
                  Số đợt phân phối đã tạo
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100 thong-ke-chart-card">
              <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                  <div>
                    <h6 class="mb-0">Nhận vào và phát ra theo mặt hàng</h6>
                  </div>

                  <div style="min-width: 220px;">
                    <select id="locDanhMucThongKeHangHoa" class="form-select form-select-sm">
                      <option value="">Tất cả danh mục</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="card-body">
                @if ($duLieuNhanPhatTheoHangHoa->count() > 0)
                  <div class="thong-ke-chart-scroll">
                    <div id="bieuDoNhanPhatHangHoa" style="min-height: 300px;"></div>
                  </div>
                @else
                  <div class="text-center text-muted py-5">
                    Chưa có dữ liệu nhận/phát hàng hóa.
                  </div>
                @endif
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 thong-ke-chart-card">
              <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Trạng thái yêu cầu cứu trợ</h6>
              </div>

              <div class="card-body">
                @if ($thongKeTrangThaiYeuCau->count() > 0)
                  <div id="bieuDoTrangThaiYeuCau" style="min-height: 300px;"></div>
                @else
                  <div class="text-center text-muted py-5">
                    Chưa có dữ liệu yêu cầu cứu trợ.
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <h6 class="mb-3">Tổng kết phân phối</h6>

                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Đã phân phối</span>
                  <strong>{{ $phanTramDaPhanPhoi }}%</strong>
                </div>

                <div class="progress mb-3" style="height: 8px;">
                  <div class="progress-bar"
                      role="progressbar"
                      data-width="{{ $phanTramDaPhanPhoiHienThi }}"
                      style="width: 0%;"
                      aria-valuenow="{{ $phanTramDaPhanPhoi }}"
                      aria-valuemin="0"
                      aria-valuemax="100">
                  </div>
                </div>

                <small class="text-muted">
                  Đã phân phối {{ $phanTramDaPhanPhoi }}% tổng nguồn lực đã xác nhận của chiến dịch.
                </small>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <h6 class="mb-3">Tổng kết yêu cầu cứu trợ</h6>

                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Yêu cầu hoàn thành</span>
                  <strong>{{ $soYeuCauHoanThanh }}/{{ $soYeuCauTiepNhan }}</strong>
                </div>

                <div class="progress mb-3" style="height: 8px;">
                  <div class="progress-bar bg-success"
                      role="progressbar"
                      data-width="{{ $phanTramYeuCauHoanThanhHienThi }}"
                      style="width: 0%;"
                      aria-valuenow="{{ $phanTramYeuCauHoanThanh }}"
                      aria-valuemin="0"
                      aria-valuemax="100">
                  </div>
                </div>

                <small class="text-muted">
                  Tỷ lệ hoàn thành: {{ $phanTramYeuCauHoanThanh }}%.
                </small>
              </div>
            </div>
          </div>
        </div>

        @if ($chienDichDaHoanThanh && $hangHoaDu->count() > 0)
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
              <h6 class="mb-0">Hàng hóa còn dư sau chiến dịch</h6>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0" id="bangHangHoaDu">
                  <thead>
                    <tr class="text-uppercase text-center">
                      <th style="width: 80px;">STT</th>
                      <th class="text-start">Hàng hóa</th>

                      <th class="filter-heading-cell" style="width: 220px;">
                        <div class="dropdown">
                          <button type="button"
                                  class="filter-heading-button"
                                  id="btnLocDanhMucHangHoaDu"
                                  data-bs-toggle="dropdown"
                                  aria-expanded="false">
                            Danh mục
                            <span class="filter-active-dot"></span>
                          </button>

                          <ul class="dropdown-menu filter-dropdown-menu"
                              aria-labelledby="btnLocDanhMucHangHoaDu"
                              id="menuLocDanhMucHangHoaDu">
                            <li>
                              <button type="button"
                                      class="dropdown-item hang-hoa-du-filter-option active"
                                      data-filter-value="">
                                Tất cả danh mục
                              </button>
                            </li>
                          </ul>
                        </div>
                      </th>

                      <th style="width: 120px;">Đơn vị</th>
                      <th style="width: 150px;">Còn dư</th>
                    </tr>
                  </thead>

                  <tbody>
                    @foreach ($hangHoaDu as $index => $nguonLuc)
                      @php
                        $tenDanhMucHangHoaDu = $nguonLuc->hangHoa->danhMucHang->tenDanhMucHang ?? '-';
                      @endphp

                      <tr class="hang-hoa-du-row"
                          data-danh-muc="{{ $tenDanhMucHangHoaDu }}">
                        <td class="text-center">
                          {{ $index + 1 }}
                        </td>

                        <td class="text-start fw-semibold">
                          {{ $nguonLuc->hangHoa->tenHangHoa ?? '-' }}
                        </td>

                        <td class="text-center">
                          {{ $tenDanhMucHangHoaDu }}
                        </td>

                        <td class="text-center">
                          {{ $nguonLuc->hangHoa->donViTinh ?? '-' }}
                        </td>

                        <td class="text-center fw-semibold">
                          {{ number_format($nguonLuc->tongSoLuongHienCo ?? 0, 0, ',', '.') }}
                        </td>
                      </tr>
                    @endforeach

                    <tr id="khongCoHangHoaDuPhuHop" style="display: none;">
                      <td colspan="5" class="text-center text-muted py-4">
                        Không có hàng hóa dư phù hợp với danh mục đã chọn.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @endif

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">Bản đồ vị trí liên quan</h6>
          </div>

          <div class="card-body">
            @if ($diemBanDoThongKe->count() > 0)
              <div id="thongKeBanDoMap" style="height: 360px; width: 100%; border-radius: 12px;"></div>

              <div class="d-flex flex-wrap gap-3 mt-3 small">
                <span class="d-inline-flex align-items-center gap-1">
                  <span style="width:10px; height:10px; border-radius:50%; background:#0d6efd; display:inline-block;"></span>
                  <span class="text-muted">Chiến dịch</span>
                </span>

                <span class="d-inline-flex align-items-center gap-1">
                  <span style="width:10px; height:10px; border-radius:50%; background:#dc3545; display:inline-block;"></span>
                  <span class="text-muted">Yêu cầu cứu trợ</span>
                </span>

                <span class="d-inline-flex align-items-center gap-1">
                  <span style="width:10px; height:10px; border-radius:50%; background:#198754; display:inline-block;"></span>
                  <span class="text-muted">Phân phối</span>
                </span>
              </div>
            @else
              <div class="text-center text-muted py-5">
                Chưa có dữ liệu vị trí để hiển thị bản đồ.
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if ($coToaDoChienDich || count($duLieuThongKeChienDich['diemBanDo']) > 0)
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script id="duLieuThongKeChienDich" type="application/json">
{!! json_encode($duLieuThongKeChienDich, JSON_UNESCAPED_UNICODE) !!}
</script>

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

    const dongGopRows = Array.from(
      document.querySelectorAll('.dong-gop-row')
    );

    const timKiemDongGopInput = document.getElementById('timKiemDongGop');
    const xoaLocDongGopButton = document.getElementById('xoaLocDongGop');
    const khongCoDongGopPhuHop = document.getElementById('khongCoDongGopPhuHop');

    const boLocDongGop = {
      hangHoa: '',
      danhMuc: '',
    };

    const cauHinhLocDongGop = {
      hangHoa: {
        menuId: 'menuLocHangHoaDongGop',
        buttonId: 'btnLocHangHoaDongGop',
        dataKey: 'hangHoa',
        allText: 'Tất cả hàng hóa',
      },
      danhMuc: {
        menuId: 'menuLocDanhMucDongGop',
        buttonId: 'btnLocDanhMucDongGop',
        dataKey: 'danhMuc',
        allText: 'Tất cả danh mục',
      },
    };

    function layGiaTriLocDongGop(dataKey) {
      const values = [];

      dongGopRows.forEach(function (row) {
        const value = row.dataset[dataKey] || '';

        if (value !== '' && value !== '-' && !values.includes(value)) {
          values.push(value);
        }
      });

      return values.sort(function (a, b) {
        return a.localeCompare(b, 'vi');
      });
    }

    function taoNutLocDongGop(filterType, value, label) {
      const li = document.createElement('li');
      const button = document.createElement('button');

      button.type = 'button';
      button.className = 'dropdown-item dong-gop-filter-option';
      button.dataset.filterType = filterType;
      button.dataset.filterValue = value;
      button.textContent = label;

      button.addEventListener('click', function () {
        boLocDongGop[filterType] = value;
        locDongGop();
      });

      li.appendChild(button);

      return li;
    }

    function khoiTaoMenuLocDongGop() {
      Object.keys(cauHinhLocDongGop).forEach(function (filterType) {
        const config = cauHinhLocDongGop[filterType];
        const menu = document.getElementById(config.menuId);

        if (!menu) {
          return;
        }

        menu.innerHTML = '';
        menu.appendChild(taoNutLocDongGop(filterType, '', config.allText));

        layGiaTriLocDongGop(config.dataKey).forEach(function (value) {
          menu.appendChild(taoNutLocDongGop(filterType, value, value));
        });
      });
    }

    function capNhatTrangThaiNutLocDongGop() {
      document
        .querySelectorAll('.dong-gop-filter-option')
        .forEach(function (button) {
          const filterType = button.dataset.filterType;
          const filterValue = button.dataset.filterValue || '';

          button.classList.toggle(
            'active',
            boLocDongGop[filterType] === filterValue
          );
        });

      Object.keys(cauHinhLocDongGop).forEach(function (filterType) {
        const config = cauHinhLocDongGop[filterType];
        const button = document.getElementById(config.buttonId);

        if (!button) {
          return;
        }

        button.classList.toggle(
          'is-filtering',
          boLocDongGop[filterType] !== ''
        );
      });

      const dangTimKiem =
        (timKiemDongGopInput?.value.trim() || '') !== '';

      const dangLoc =
        dangTimKiem
        || boLocDongGop.hangHoa !== ''
        || boLocDongGop.danhMuc !== '';

      if (xoaLocDongGopButton) {
        xoaLocDongGopButton.classList.toggle('d-none', !dangLoc);
      }
    }

    function locDongGop() {
      const tuKhoa = boDauTiengVietJS(
        timKiemDongGopInput?.value.trim() || ''
      );

      let soDongHienThi = 0;

      dongGopRows.forEach(function (row) {
        const noiDung = boDauTiengVietJS(row.dataset.search || '');

        const hopTuKhoa =
          tuKhoa === '' || noiDung.includes(tuKhoa);

        const hopHangHoa =
          boLocDongGop.hangHoa === ''
          || row.dataset.hangHoa === boLocDongGop.hangHoa;

        const hopDanhMuc =
          boLocDongGop.danhMuc === ''
          || row.dataset.danhMuc === boLocDongGop.danhMuc;

        const hienThi =
          hopTuKhoa && hopHangHoa && hopDanhMuc;

        row.style.display = hienThi ? '' : 'none';

        if (hienThi) {
          soDongHienThi++;
        }
      });

      if (khongCoDongGopPhuHop) {
        khongCoDongGopPhuHop.style.display =
          soDongHienThi === 0 && dongGopRows.length > 0 ? '' : 'none';
      }

      capNhatTrangThaiNutLocDongGop();
    }

    khoiTaoMenuLocDongGop();
    locDongGop();

    if (timKiemDongGopInput) {
      timKiemDongGopInput.addEventListener('input', locDongGop);
    }

    if (xoaLocDongGopButton) {
      xoaLocDongGopButton.addEventListener('click', function () {
        if (timKiemDongGopInput) {
          timKiemDongGopInput.value = '';
        }

        boLocDongGop.hangHoa = '';
        boLocDongGop.danhMuc = '';
        boLocDongGop.trangThai = '';

        locDongGop();
      });
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

      if (dataKey === 'trangThai') {
        ['Đang kêu gọi', 'Đủ số lượng', 'Đã đóng'].forEach(function (value) {
          if (!values.includes(value)) {
            values.push(value);
          }
        });
      }

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

        const trangThaiDong = row.dataset.trangThai || '';
        const danhMucDong = row.dataset.danhMuc || '';
        const donViDong = row.dataset.donVi || '';

        const hopDanhMuc =
          boLocNguonLuc.danhMuc === ''
          || danhMucDong === boLocNguonLuc.danhMuc;

        const hopDonVi =
          boLocNguonLuc.donVi === ''
          || donViDong === boLocNguonLuc.donVi;

        const hopTrangThai =
          boLocNguonLuc.trangThai === ''
          || trangThaiDong === boLocNguonLuc.trangThai;

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

    const hangHoaDuRows = Array.from(
      document.querySelectorAll('.hang-hoa-du-row')
    );

    const menuLocDanhMucHangHoaDu = document.getElementById('menuLocDanhMucHangHoaDu');
    const btnLocDanhMucHangHoaDu = document.getElementById('btnLocDanhMucHangHoaDu');
    const khongCoHangHoaDuPhuHop = document.getElementById('khongCoHangHoaDuPhuHop');

    let danhMucHangHoaDuDangLoc = '';

    function layDanhMucHangHoaDu() {
      const danhMucs = [];

      hangHoaDuRows.forEach(function (row) {
        const danhMuc = row.dataset.danhMuc || '';

        if (danhMuc !== '' && danhMuc !== '-' && !danhMucs.includes(danhMuc)) {
          danhMucs.push(danhMuc);
        }
      });

      return danhMucs.sort(function (a, b) {
        return a.localeCompare(b, 'vi');
      });
    }

    function taoNutLocHangHoaDu(value, label) {
      const li = document.createElement('li');
      const button = document.createElement('button');

      button.type = 'button';
      button.className = 'dropdown-item hang-hoa-du-filter-option';
      button.dataset.filterValue = value;
      button.textContent = label;

      button.addEventListener('click', function () {
        danhMucHangHoaDuDangLoc = value;
        locHangHoaDuTheoDanhMuc();

        const selectBieuDo = document.getElementById('locDanhMucThongKeHangHoa');

        if (selectBieuDo) {
          selectBieuDo.value = value;
          selectBieuDo.dispatchEvent(new Event('change'));
        }
      });

      li.appendChild(button);

      return li;
    }

    function khoiTaoLocHangHoaDu() {
      if (!menuLocDanhMucHangHoaDu || hangHoaDuRows.length === 0) {
        return;
      }

      menuLocDanhMucHangHoaDu.innerHTML = '';

      menuLocDanhMucHangHoaDu.appendChild(
        taoNutLocHangHoaDu('', 'Tất cả danh mục')
      );

      layDanhMucHangHoaDu().forEach(function (danhMuc) {
        menuLocDanhMucHangHoaDu.appendChild(
          taoNutLocHangHoaDu(danhMuc, danhMuc)
        );
      });
    }

    function locHangHoaDuTheoDanhMuc() {
      let soDongHienThi = 0;

      hangHoaDuRows.forEach(function (row) {
        const hopDanhMuc =
          danhMucHangHoaDuDangLoc === ''
          || row.dataset.danhMuc === danhMucHangHoaDuDangLoc;

        row.style.display = hopDanhMuc ? '' : 'none';

        if (hopDanhMuc) {
          soDongHienThi++;
        }
      });

      document
        .querySelectorAll('.hang-hoa-du-filter-option')
        .forEach(function (button) {
          const value = button.dataset.filterValue || '';

          button.classList.toggle(
            'active',
            value === danhMucHangHoaDuDangLoc
          );
        });

      if (btnLocDanhMucHangHoaDu) {
        btnLocDanhMucHangHoaDu.classList.toggle(
          'is-filtering',
          danhMucHangHoaDuDangLoc !== ''
        );
      }

      if (khongCoHangHoaDuPhuHop) {
        khongCoHangHoaDuPhuHop.style.display =
          soDongHienThi === 0 && hangHoaDuRows.length > 0 ? '' : 'none';
      }
    }

    khoiTaoLocHangHoaDu();
    locHangHoaDuTheoDanhMuc();

    let thongKeMapInstance = null;

    const duLieuThongKeElement = document.getElementById('duLieuThongKeChienDich');

    if (duLieuThongKeElement) {
      const duLieuThongKe = JSON.parse(duLieuThongKeElement.textContent);

      const bieuDoNhanPhatElement = document.getElementById('bieuDoNhanPhatHangHoa');
      const locDanhMucThongKeHangHoa = document.getElementById('locDanhMucThongKeHangHoa');

      let bieuDoNhanPhat = null;

      function rutGonTenHangHoa(ten) {
        if (!ten) {
          return '';
        }

        return ten.length > 18 ? ten.substring(0, 18) + '...' : ten;
      }

      function layDuLieuNhanPhatTheoDanhMuc(danhMuc) {
        const ketQua = {
          maHangHoa: [],
          tenHangHoa: [],
          duLieuNhanVao: [],
          duLieuPhatRa: []
        };

        duLieuThongKe.maHangHoa.forEach(function (ma, index) {
          const danhMucHienTai = duLieuThongKe.danhMucHangHoa[index] || '';

          if (danhMuc === '' || danhMucHienTai === danhMuc) {
            ketQua.maHangHoa.push(ma);
            ketQua.tenHangHoa.push(duLieuThongKe.tenHangHoa[index]);
            ketQua.duLieuNhanVao.push(duLieuThongKe.duLieuNhanVao[index]);
            ketQua.duLieuPhatRa.push(duLieuThongKe.duLieuPhatRa[index]);
          }
        });

        return ketQua;
      }

      function veBieuDoNhanPhat(danhMuc = '') {
        if (!bieuDoNhanPhatElement || !window.ApexCharts) {
          return;
        }

        const duLieuLoc = layDuLieuNhanPhatTheoDanhMuc(danhMuc);

        if (bieuDoNhanPhat) {
          bieuDoNhanPhat.destroy();
        }

        if (duLieuLoc.maHangHoa.length === 0) {
          bieuDoNhanPhatElement.innerHTML =
            '<div class="text-center text-muted py-5">Không có dữ liệu nhận/phát trong danh mục này.</div>';
          return;
        }

        const nhieuHangHoa = duLieuLoc.maHangHoa.length > 6;

        const nhanTrucX = nhieuHangHoa
          ? duLieuLoc.maHangHoa
          : duLieuLoc.tenHangHoa.map(function (ten) {
              return rutGonTenHangHoa(ten);
            });

        const chartWidth = nhieuHangHoa
          ? Math.max(640, duLieuLoc.maHangHoa.length * 90)
          : 640;

        bieuDoNhanPhatElement.style.width = chartWidth + 'px';

        bieuDoNhanPhat = new ApexCharts(bieuDoNhanPhatElement, {
          chart: {
            type: 'bar',
            height: 300,
            toolbar: {
              show: false
            }
          },
          series: [
            {
              name: 'Nhận vào',
              data: duLieuLoc.duLieuNhanVao
            },
            {
              name: 'Phát ra',
              data: duLieuLoc.duLieuPhatRa
            }
          ],
          xaxis: {
            categories: nhanTrucX,
            labels: {
              rotate: nhieuHangHoa ? 0 : -20,
              trim: false
            }
          },
          plotOptions: {
            bar: {
              borderRadius: 5,
              columnWidth: '45%'
            }
          },
          dataLabels: {
            enabled: false
          },
          legend: {
            position: 'bottom'
          },
          grid: {
            borderColor: '#f1f1f1'
          },
          yaxis: {
            min: 0
          },
          tooltip: {
            x: {
              formatter: function (value, options) {
                const index = options.dataPointIndex;
                const maHangHoa = duLieuLoc.maHangHoa[index] || '';
                const tenHangHoa = duLieuLoc.tenHangHoa[index] || value;

                return maHangHoa + ' - ' + tenHangHoa;
              }
            }
          }
        });

        bieuDoNhanPhat.render();
      }

      if (
        bieuDoNhanPhatElement
        && window.ApexCharts
        && duLieuThongKe.maHangHoa
        && duLieuThongKe.maHangHoa.length > 0
      ) {
        if (locDanhMucThongKeHangHoa) {
          duLieuThongKe.danhMucThongKeHangHoa.forEach(function (danhMuc) {
            const option = document.createElement('option');
            option.value = danhMuc;
            option.textContent = danhMuc;
            locDanhMucThongKeHangHoa.appendChild(option);
          });

          locDanhMucThongKeHangHoa.addEventListener('change', function () {
            const danhMuc = this.value;

            veBieuDoNhanPhat(danhMuc);

            danhMucHangHoaDuDangLoc = danhMuc;
            locHangHoaDuTheoDanhMuc();
          });
        }

        veBieuDoNhanPhat('');
      }

      const bieuDoTrangThaiElement = document.getElementById('bieuDoTrangThaiYeuCau');

      if (bieuDoTrangThaiElement && window.ApexCharts && duLieuThongKe.nhanTrangThaiYeuCau.length > 0) {
        const bieuDoTrangThai = new ApexCharts(bieuDoTrangThaiElement, {
          chart: {
            type: 'donut',
            height: 300
          },
          series: duLieuThongKe.duLieuTrangThaiYeuCau,
          labels: duLieuThongKe.nhanTrangThaiYeuCau,
          legend: {
            position: 'bottom'
          },
          dataLabels: {
            enabled: true
          },
          plotOptions: {
            pie: {
              donut: {
                size: '65%'
              }
            }
          }
        });

        bieuDoTrangThai.render();
      }

      const thongKeBanDoElement = document.getElementById('thongKeBanDoMap');

      if (
        thongKeBanDoElement
        && window.L
        && duLieuThongKe.diemBanDo
        && duLieuThongKe.diemBanDo.length > 0
      ) {
        const diemDauTien = duLieuThongKe.diemBanDo[0];

        thongKeMapInstance = L.map('thongKeBanDoMap').setView(
          [diemDauTien.viDo, diemDauTien.kinhDo],
          12
        );

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; OpenStreetMap'
        }).addTo(thongKeMapInstance);

        const bounds = [];

        duLieuThongKe.diemBanDo.forEach(function (diem) {
          const lat = parseFloat(diem.viDo);
          const lng = parseFloat(diem.kinhDo);

          if (Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
          }

          bounds.push([lat, lng]);

          let mauMarker = '#0d6efd';

          if (diem.loai === 'Yêu cầu') {
            mauMarker = '#dc3545';
          }

          if (diem.loai === 'Phân phối') {
            mauMarker = '#198754';
          }

          const markerIcon = L.divIcon({
            className: '',
            html:
              '<div style="' +
                'width:26px;' +
                'height:26px;' +
                'background:' + mauMarker + ';' +
                'border:3px solid white;' +
                'border-radius:50% 50% 50% 0;' +
                'transform:rotate(-45deg);' +
                'box-shadow:0 2px 8px rgba(0,0,0,.35);' +
                'position:relative;' +
              '">' +
                '<div style="' +
                  'width:8px;' +
                  'height:8px;' +
                  'background:white;' +
                  'border-radius:50%;' +
                  'position:absolute;' +
                  'top:6px;' +
                  'left:6px;' +
                '"></div>' +
              '</div>',
            iconSize: [26, 26],
            iconAnchor: [13, 26],
            popupAnchor: [0, -26]
          });

          L.marker([lat, lng], {
            icon: markerIcon
          })
            .addTo(thongKeMapInstance)
            .bindPopup(
              '<strong>' + diem.loai + ': ' + diem.ten + '</strong><br>' + diem.diaChi
            );
        });

        if (bounds.length > 1) {
          thongKeMapInstance.fitBounds(bounds, {
            padding: [30, 30]
          });
        }
      }
    }

    document.querySelectorAll('.progress-bar[data-width]').forEach(function (bar) {
      const width = parseFloat(bar.dataset.width || 0);
      bar.style.width = width + '%';
    });

    const thongKeTabButton = document.getElementById('thong-ke-tab');

    if (thongKeTabButton) {
      thongKeTabButton.addEventListener('shown.bs.tab', function () {
        setTimeout(function () {
          if (thongKeMapInstance) {
            thongKeMapInstance.invalidateSize();
          }
        }, 200);
      });
    }

    if (window.feather) {
      feather.replace();
    }
  });
</script>
@endsection