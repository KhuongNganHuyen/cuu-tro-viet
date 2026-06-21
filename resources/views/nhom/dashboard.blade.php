@extends('layouts.nhom')

@section('title', 'Tổng quan nhóm | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  .nhom-dashboard-card {
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.04);
  }

  .nhom-avatar-box {
    width: 132px;
    height: 132px;
    margin: 0 auto 16px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #e5e7eb;
    background-color: #f3f4f6;
  }

  .nhom-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .nhom-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9f2ff;
    color: #0d6efd;
    font-size: 44px;
    font-weight: 700;
  }

  .nhom-status-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    border-radius: 50%;
  }

  .nhom-status-active {
    background-color: #198754;
  }

  .nhom-status-warning {
    background-color: #ffc107;
  }

  .nhom-status-danger {
    background-color: #dc3545;
  }

  .nhom-status-muted {
    background-color: #6c757d;
  }

  .nhom-info-table td {
    padding: 9px 0;
    vertical-align: top;
  }

  .nhom-info-label {
    width: 150px;
    color: #6c757d;
    white-space: nowrap;
  }

  .nhom-info-value {
    color: #212529;
    font-weight: 500;
  }

  .overview-card-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
  }

  .overview-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    padding: 22px 26px;
    min-height: 118px;
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.03);
  }

  .overview-card-main {
    display: flex;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 16px;
  }

  .overview-card-number {
    font-size: 42px;
    line-height: 1;
    font-weight: 700;
    flex-shrink: 0;
  }

  .overview-card-title {
    font-size: 15px;
    font-weight: 500;
    color: #212529;
    line-height: 1.35;
    white-space: normal;
  }

  .overview-card-desc {
    display: block;
    width: 100%;
    margin: 0;
    font-size: 13px;
    color: #6c757d;
    line-height: 1.55;
  }

  .overview-blue {
    color: #0d6efd;
  }

  .overview-green {
    color: #198754;
  }

  .overview-yellow {
    color: #f59f00;
  }

  .overview-red {
    color: #dc3545;
  }

  @media (max-width: 1200px) {
    .overview-card-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 768px) {
    .overview-card-grid {
      grid-template-columns: 1fr;
    }

    .overview-card {
      min-height: auto;
      padding: 18px 20px;
    }

    .overview-card-number {
      font-size: 38px;
    }
  }

  .nhom-stat-desc {
    margin-top: 10px;
    color: #6c757d;
    font-size: 12px;
    line-height: 1.45;
  }

  .nhom-chart-box {
    min-height: 355px;
    border: 1px solid #eef0f3;
    border-radius: 12px;
    padding: 18px;
    background-color: #fff;
  }

  .nhom-chart-area {
    min-height: 285px;
  }

  .nhom-summary-list .list-group-item {
    padding: 13px 0;
    border-left: 0;
    border-right: 0;
  }

  .nhom-summary-list .list-group-item:first-child {
    border-top: 0;
  }

  .nhom-summary-list .list-group-item:last-child {
    border-bottom: 0;
  }

  #nhomDashboardMap {
    height: 360px;
    width: 100%;
    border-radius: 12px;
  }

  @media (max-width: 1200px) {
    .nhom-stat-inline {
      grid-template-columns: repeat(2, minmax(160px, 1fr));
    }
  }

  @media (max-width: 768px) {
    .nhom-stat-inline {
      grid-template-columns: 1fr;
    }
  }

  .group-info-wrapper {
    max-width: 1050px;
    margin: 0 auto;
  }

  .group-avatar-box {
    width: 100%;
    max-width: 280px;
    text-align: center;
  }

  .group-avatar-box img,
  .group-avatar-placeholder {
    width: 140px;
    height: 140px;
    object-fit: cover;
  }

  .group-avatar-placeholder {
    margin: 0 auto 16px;
    border-radius: 50%;
    border: 1px solid #dee2e6;
    background-color: #e9f2ff;
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 46px;
  }

  .nhom-info-table-row {
    margin-bottom: 16px;
  }

  .nhom-info-table-row:last-child {
    margin-bottom: 0;
  }

  .nhom-info-label {
    color: #6c757d;
  }

  .nhom-info-value {
    color: #212529;
    font-weight: 500;
  }

  .nhom-desc-text {
    line-height: 1.6;
  }
</style>

@php
  if ($nhom->anhDaiDien ?? false) {
      $anhNhom = asset('storage/' . $nhom->anhDaiDien);
  } else {
      $anhNhom = asset('storage/nhom-tinh-nguyen/group.jpg');
  }

  $chuCaiNhom = mb_substr($nhom->tenNhom ?? 'N', 0, 1, 'UTF-8');

  $trangThaiNhom = $nhom->trangThai ?? '-';

  $classTrangThaiNhom = match ($trangThaiNhom) {
      'Đang hoạt động' => 'nhom-status-active',
      'Chờ duyệt' => 'nhom-status-warning',
      'Tạm ngưng' => 'nhom-status-danger',
      default => 'nhom-status-muted',
  };

  $diaChiNhom = '-';

  if ($nhom->diaDiem) {
      $diaChiParts = array_filter([
          $nhom->diaDiem->chiTietDiaDiem ?? null,
          $nhom->diaDiem->phuongXa ?? null,
          $nhom->diaDiem->tinhThanh ?? null,
      ]);

      $diaChiNhom = count($diaChiParts) > 0
          ? implode(', ', $diaChiParts)
          : '-';
  }

  $ngayTaoNhom = $nhom->ngayTao
      ? \Carbon\Carbon::parse($nhom->ngayTao)->format('d/m/Y H:i:s')
      : '-';

  $viDoNhom = $nhom->diaDiem->viDo ?? null;
  $kinhDoNhom = $nhom->diaDiem->kinhDo ?? null;
  $coToaDoNhom = $viDoNhom && $kinhDoNhom;

  $soThanhVien = $thongKe['soThanhVien'] ?? 0;
  $soChienDich = $thongKe['soChienDich'] ?? 0;
  $soChienDichDangDienRa = $thongKe['soChienDichDangDienRa'] ?? 0;
  $soYeuCauTiepNhan = $thongKe['soYeuCauTiepNhan'] ?? 0;
  $soYeuCauDangXuLy = $thongKe['soYeuCauDangXuLy'] ?? 0;
  $soYeuCauHoanThanh = $thongKe['soYeuCauHoanThanh'] ?? 0;
  $soDongGop = $thongKe['soDongGop'] ?? 0;
  $soDongGopDaXacNhan = $thongKe['soDongGopDaXacNhan'] ?? 0;
  $soDotPhanPhoi = $thongKe['soDotPhanPhoi'] ?? 0;
  $soDotPhanPhoiHoanThanh = $thongKe['soDotPhanPhoiHoanThanh'] ?? 0;
  $soHangHoaSuDung = $thongKe['soHangHoaSuDung'] ?? 0;

  $tyLeYeuCauHoanThanh = $soYeuCauTiepNhan > 0
      ? round(($soYeuCauHoanThanh / $soYeuCauTiepNhan) * 100, 1)
      : 0;
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tổng quan nhóm</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            {{ $nhom->tenNhom }}
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

<div class="card nhom-dashboard-card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Thông tin nhóm</h5>
  </div>

  <div class="card-body">
    <div class="group-info-wrapper">
      <div class="row align-items-center justify-content-center">
        <div class="col-lg-5 col-md-5 mb-4 mb-md-0 d-flex justify-content-center">
          <div class="group-avatar-box">
            @if ($anhNhom)
              <img src="{{ $anhNhom }}"
                  alt="Ảnh đại diện nhóm"
                  class="rounded-circle mb-3">
            @else
              <div class="group-avatar-placeholder mb-3">
                <i class="ti ti-users"></i>
              </div>
            @endif

            <h5 class="mb-1">
              {{ $nhom->tenNhom }}
            </h5>

            <span class="d-inline-flex align-items-center justify-content-center gap-2 mb-3">
              <span class="nhom-status-dot {{ $classTrangThaiNhom }}"></span>
              {{ $trangThaiNhom }}
            </span>

            <div class="alert alert-info text-start mb-3">
              <strong>Vai trò của bạn:</strong> {{ $vaiTroTrongNhom }}
            </div>

            @if ($laNhomTruong)
              <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard/edit') }}"
                 class="btn btn-warning">
                Sửa thông tin nhóm
              </a>
            @endif
          </div>
        </div>

        <div class="col-lg-7 col-md-7">
          <div class="row nhom-info-table-row">
            <div class="col-md-4 nhom-info-label">Mã nhóm</div>
            <div class="col-md-8 nhom-info-value">
              {{ $nhom->idNhom }}
            </div>
          </div>

          <div class="row nhom-info-table-row">
            <div class="col-md-4 nhom-info-label">Tên nhóm</div>
            <div class="col-md-8 nhom-info-value text-break">
              {{ $nhom->tenNhom }}
            </div>
          </div>

          <div class="row nhom-info-table-row">
            <div class="col-md-4 nhom-info-label">Nhóm trưởng</div>
            <div class="col-md-8 nhom-info-value">
              {{ $nhom->nhomTruong->hoTen ?? '-' }}

              @if (!empty($nhom->nhomTruong?->tenDangNhap))
                <small class="text-muted d-block">
                  {{ $nhom->nhomTruong->tenDangNhap }}
                </small>
              @endif
            </div>
          </div>

          <div class="row nhom-info-table-row">
            <div class="col-md-4 nhom-info-label">Địa điểm</div>
            <div class="col-md-8 nhom-info-value">
              {{ $diaChiNhom }}
            </div>
          </div>

          <div class="row nhom-info-table-row">
            <div class="col-md-4 nhom-info-label">Trạng thái</div>
            <div class="col-md-8 nhom-info-value">
              <span class="d-inline-flex align-items-center gap-2">
                <span class="nhom-status-dot {{ $classTrangThaiNhom }}"></span>
                {{ $trangThaiNhom }}
              </span>
            </div>
          </div>

          <div class="row nhom-info-table-row">
            <div class="col-md-4 nhom-info-label">Ngày tạo</div>
            <div class="col-md-8 nhom-info-value">
              {{ $ngayTaoNhom }}
            </div>
          </div>

          <div class="row nhom-info-table-row">
            <div class="col-md-4 nhom-info-label">Mô tả</div>
            <div class="col-md-8 nhom-info-value text-break nhom-desc-text">
              {{ $nhom->moTa ?? 'Chưa có mô tả cho nhóm này.' }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card nhom-dashboard-card mb-4">
  <div class="card-header">
    <div>
      <h5 class="mb-1">Tổng quan vận hành nhóm</h5>
    </div>
  </div>

  <div class="card-body">
    <div class="overview-card-grid mb-4">
      <div class="overview-card">
        <div class="overview-card-main">
          <div class="overview-card-number overview-blue">
            {{ $soThanhVien }}
          </div>

          <div class="overview-card-title">
            Thành viên
          </div>
        </div>

        <p class="overview-card-desc">
          Tổng số thành viên hiện có trong nhóm tình nguyện.
        </p>
      </div>

      <div class="overview-card">
        <div class="overview-card-main">
          <div class="overview-card-number overview-green">
            {{ $soChienDich }}
          </div>

          <div class="overview-card-title">
            Chiến dịch
          </div>
        </div>

        <p class="overview-card-desc">
          Tổng số chiến dịch cứu trợ mà nhóm đã tham gia hoặc tạo.
        </p>
      </div>

      <div class="overview-card">
        <div class="overview-card-main">
          <div class="overview-card-number overview-yellow">
            {{ $soYeuCauTiepNhan }}
          </div>

          <div class="overview-card-title">
            Yêu cầu tiếp nhận
          </div>
        </div>

        <p class="overview-card-desc">
          Tổng số yêu cầu cứu trợ mà nhóm đã tiếp nhận xử lý.
        </p>
      </div>

      <div class="overview-card">
        <div class="overview-card-main">
          <div class="overview-card-number overview-red">
            {{ $soDongGop }}
          </div>

          <div class="overview-card-title">
            Lượt đóng góp
          </div>
        </div>

        <p class="overview-card-desc">
          Tổng số lượt đóng góp thuộc các chiến dịch của nhóm.
        </p>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-lg-8">
        <div class="nhom-chart-box h-100">
          <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
              <h6 class="mb-1">So sánh hoạt động theo chiến dịch</h6>
              <small class="text-muted">So sánh yêu cầu, đóng góp đã xác nhận, đợt phân phối và nguồn lực giữa các chiến dịch.</small>
            </div>
          </div>

          @if (count($duLieuDashboard['soSanhChienDich']['labels']) > 0)
            <div id="bieuDoSoSanhChienDichNhom" class="nhom-chart-area"></div>
            <small class="text-muted d-block mt-2">Biểu đồ dùng tỷ lệ tương đối theo từng chỉ số để tránh cột đóng góp làm chìm các chỉ số nhỏ hơn. Di chuột vào cột để xem số lượng thật.</small>
          @else
            <div class="text-center text-muted py-5">
              Chưa có dữ liệu chiến dịch để so sánh.
            </div>
          @endif
        </div>
      </div>

      <div class="col-lg-4">
        <div class="nhom-chart-box h-100">
          <h6 class="mb-1">Trạng thái chiến dịch</h6>
          <small class="text-muted d-block mb-3">Cơ cấu chiến dịch theo trạng thái hiện tại.</small>

          @if (count($duLieuDashboard['trangThaiChienDich']['data']) > 0)
            <div id="bieuDoTrangThaiChienDich" class="nhom-chart-area"></div>
          @else
            <div class="text-center text-muted py-5">
              Chưa có dữ liệu chiến dịch.
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="nhom-chart-box h-100">
          <h6 class="mb-1">Tình trạng yêu cầu cứu trợ</h6>
          <small class="text-muted d-block mb-3">Theo dõi số yêu cầu còn tồn và số yêu cầu đã hoàn thành.</small>

          @if (count($duLieuDashboard['trangThaiYeuCau']['data']) > 0)
            <div id="bieuDoTrangThaiYeuCauNhom" class="nhom-chart-area"></div>
          @else
            <div class="text-center text-muted py-5">
              Chưa có dữ liệu yêu cầu cứu trợ.
            </div>
          @endif
        </div>
      </div>

      <div class="col-lg-4">
        <div class="nhom-chart-box h-100">
          <h6 class="mb-1">Tóm tắt hoạt động</h6>
          <small class="text-muted d-block mb-3">Các số tổng chỉ dùng để tham khảo nhanh.</small>

          <div class="list-group list-group-flush nhom-summary-list">
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <span class="text-muted">Thành viên</span>
              <strong>{{ $soThanhVien }}</strong>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center">
              <span class="text-muted">Tổng chiến dịch</span>
              <strong>{{ $soChienDich }}</strong>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center">
              <span class="text-muted">Tổng lượt đóng góp</span>
              <strong>{{ $soDongGop }}</strong>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center">
              <span class="text-muted">Đợt phân phối hoàn thành</span>
              <strong>{{ $soDotPhanPhoiHoanThanh }}/{{ $soDotPhanPhoi }}</strong>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center">
              <span class="text-muted">Mặt hàng đã sử dụng</span>
              <strong>{{ $soHangHoaSuDung }}</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card nhom-dashboard-card">
  <div class="card-body">
    <h6 class="mb-1">Vị trí nhóm trên bản đồ</h6>
    <small class="text-muted d-block mb-3">
      Tọa độ địa điểm của nhóm.
    </small>

    @if ($coToaDoNhom)
      <div id="nhomDashboardMap"></div>
    @else
      <div class="text-center text-muted py-5">
        Nhóm chưa có tọa độ để hiển thị bản đồ.
      </div>
    @endif
  </div>
</div>

<script id="duLieuDashboardNhom" type="application/json">
{!! json_encode($duLieuDashboard, JSON_UNESCAPED_UNICODE) !!}
</script>

@if ($coToaDoNhom)
  <script id="toaDoNhomData" type="application/json">
  {!! json_encode([
      'viDo' => $viDoNhom,
      'kinhDo' => $kinhDoNhom,
      'tenNhom' => $nhom->tenNhom,
      'diaChi' => $diaChiNhom,
  ], JSON_UNESCAPED_UNICODE) !!}
  </script>
@endif

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const duLieuDashboardElement = document.getElementById('duLieuDashboardNhom');

    if (duLieuDashboardElement && window.ApexCharts) {
      const duLieuDashboard = JSON.parse(duLieuDashboardElement.textContent);

      const dinhDangSo = function (value) {
        return Number(value || 0).toLocaleString('vi-VN');
      };

      const cauHinhChung = {
        toolbar: { show: false },
        fontFamily: 'inherit'
      };

      const bieuDoSoSanhChienDichElement = document.getElementById('bieuDoSoSanhChienDichNhom');

      if (bieuDoSoSanhChienDichElement && duLieuDashboard.soSanhChienDich) {
        const soSanhChienDich = duLieuDashboard.soSanhChienDich;

        const duLieuThucTe = [
          soSanhChienDich.yeuCau || [],
          soSanhChienDich.dongGop || [],
          soSanhChienDich.phanPhoi || [],
          soSanhChienDich.nguonLuc || []
        ];

        const chuanHoaTheoChiSo = function (data) {
          const max = Math.max(...data.map(function (value) { return Number(value || 0); }), 0);

          if (max <= 0) {
            return data.map(function () { return 0; });
          }

          return data.map(function (value) {
            return Number(((Number(value || 0) / max) * 100).toFixed(1));
          });
        };

        const seriesSoSanhChienDich = [
          { name: 'Yêu cầu', data: chuanHoaTheoChiSo(duLieuThucTe[0]) },
          { name: 'Đóng góp', data: chuanHoaTheoChiSo(duLieuThucTe[1]) },
          { name: 'Phân phối', data: chuanHoaTheoChiSo(duLieuThucTe[2]) },
          { name: 'Nguồn lực', data: chuanHoaTheoChiSo(duLieuThucTe[3]) }
        ];

        const bieuDoSoSanhChienDich = new ApexCharts(bieuDoSoSanhChienDichElement, {
          chart: {
            type: 'bar',
            height: 285,
            ...cauHinhChung
          },
          series: seriesSoSanhChienDich,
          xaxis: {
            categories: soSanhChienDich.labels || [],
            labels: {
              rotate: 0,
              trim: true
            }
          },
          yaxis: {
            min: 0,
            max: 100,
            labels: {
              formatter: function (value) {
                return Math.round(value) + '%';
              }
            }
          },
          plotOptions: {
            bar: {
              borderRadius: 5,
              columnWidth: '58%'
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
          tooltip: {
            custom: function ({ seriesIndex, dataPointIndex, w }) {
              const tenChiSo = w.globals.seriesNames[seriesIndex] || '';
              const maChienDich = soSanhChienDich.labels[dataPointIndex] || '';
              const tenChienDich = soSanhChienDich.tenChienDich[dataPointIndex] || maChienDich;
              const trangThai = soSanhChienDich.trangThai[dataPointIndex] || '-';
              const giaTriThucTe = duLieuThucTe[seriesIndex][dataPointIndex] || 0;
              const giaTriTuongDoi = w.globals.series[seriesIndex][dataPointIndex] || 0;

              return '<div class="p-2">' +
                '<div class="fw-semibold mb-1">' + maChienDich + ' - ' + tenChienDich + '</div>' +
                '<div class="small text-muted mb-1">Trạng thái: ' + trangThai + '</div>' +
                '<div>' + tenChiSo + ': <strong>' + dinhDangSo(giaTriThucTe) + '</strong></div>' +
                '<div class="small text-muted">Mức tương đối: ' + giaTriTuongDoi + '%</div>' +
              '</div>';
            }
          }
        });

        bieuDoSoSanhChienDich.render();
      }

      const bieuDoTrangThaiElement = document.getElementById('bieuDoTrangThaiChienDich');

      if (
        bieuDoTrangThaiElement
        && duLieuDashboard.trangThaiChienDich
        && duLieuDashboard.trangThaiChienDich.data.length > 0
      ) {
        const bieuDoTrangThai = new ApexCharts(bieuDoTrangThaiElement, {
          chart: {
            type: 'donut',
            height: 285,
            fontFamily: 'inherit'
          },
          series: duLieuDashboard.trangThaiChienDich.data,
          labels: duLieuDashboard.trangThaiChienDich.labels,
          legend: {
            position: 'bottom'
          },
          dataLabels: {
            enabled: true,
            formatter: function (value) {
              return value.toFixed(1) + '%';
            }
          },
          plotOptions: {
            pie: {
              donut: {
                size: '68%'
              }
            }
          },
          tooltip: {
            y: {
              formatter: dinhDangSo
            }
          }
        });

        bieuDoTrangThai.render();
      }

      const bieuDoTrangThaiYeuCauElement = document.getElementById('bieuDoTrangThaiYeuCauNhom');

      if (
        bieuDoTrangThaiYeuCauElement
        && duLieuDashboard.trangThaiYeuCau
        && duLieuDashboard.trangThaiYeuCau.data.length > 0
      ) {
        const bieuDoTrangThaiYeuCau = new ApexCharts(bieuDoTrangThaiYeuCauElement, {
          chart: {
            type: 'bar',
            height: 285,
            ...cauHinhChung
          },
          series: [
            {
              name: 'Yêu cầu',
              data: duLieuDashboard.trangThaiYeuCau.data
            }
          ],
          xaxis: {
            categories: duLieuDashboard.trangThaiYeuCau.labels,
            labels: {
              formatter: dinhDangSo
            }
          },
          yaxis: {
            labels: {
              maxWidth: 140
            }
          },
          plotOptions: {
            bar: {
              horizontal: true,
              borderRadius: 5,
              barHeight: '46%'
            }
          },
          dataLabels: {
            enabled: true,
            formatter: dinhDangSo
          },
          grid: {
            borderColor: '#f1f1f1'
          },
          tooltip: {
            y: {
              formatter: dinhDangSo
            }
          }
        });

        bieuDoTrangThaiYeuCau.render();
      }
    }

    const toaDoNhomElement = document.getElementById('toaDoNhomData');

    if (toaDoNhomElement && document.getElementById('nhomDashboardMap') && window.L) {
      const toaDoNhom = JSON.parse(toaDoNhomElement.textContent);

      const lat = parseFloat(toaDoNhom.viDo);
      const lng = parseFloat(toaDoNhom.kinhDo);

      if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
        const map = L.map('nhomDashboardMap').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker([lat, lng])
          .addTo(map)
          .bindPopup(
            '<strong>' + toaDoNhom.tenNhom + '</strong><br>' +
            toaDoNhom.diaChi + '<br>' +
            'Vĩ độ: ' + lat + ', Kinh độ: ' + lng
          )
          .openPopup();
      }
    }
  });
</script>
@endsection