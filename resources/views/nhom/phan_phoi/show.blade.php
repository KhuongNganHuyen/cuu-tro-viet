@extends('layouts.nhom')

@section('title', 'Chi tiết đợt phân phối | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  .info-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    background-color: #fff;
  }

  .info-label {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 4px;
  }

  .info-value {
    font-weight: 600;
    color: #212529;
  }

  .phan-phoi-block {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 18px;
    background-color: #fff;
  }

  .phan-phoi-block:last-child {
    margin-bottom: 0;
  }

  .phan-phoi-block-title {
    font-weight: 700;
    color: #212529;
  }

  .phan-phoi-block-subtitle {
    font-size: 13px;
    color: #6c757d;
  }

  .detail-table th,
  .detail-table td {
    vertical-align: middle;
    padding-top: 14px;
    padding-bottom: 14px;
  }

  .detail-table th {
    white-space: nowrap;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
  }

  .status-active {
    background-color: #0d6efd;
  }

  .status-completed {
    background-color: #198754;
  }

  .status-paused {
    background-color: #ffc107;
  }

  .status-danger {
    background-color: #dc3545;
  }

  .status-default {
    background-color: #adb5bd;
  }

  .cell-nowrap {
    white-space: nowrap;
  }

  .phan-phoi-map {
    width: 100%;
    min-height: 260px;
    height: 100%;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    z-index: 1;
  }

  .leaflet-container {
    z-index: 1 !important;
  }

  .map-empty {
    min-height: 260px;
    border: 1px dashed #dee2e6;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    background-color: #f8f9fa;
    text-align: center;
    padding: 16px;
  }

  .detail-info-line {
    margin-bottom: 12px;
  }

  .detail-info-line:last-child {
    margin-bottom: 0;
  }

  .btn-warning-custom {
    background-color: #f59f00;
    border-color: #f59f00;
    color: #fff;
  }

  .btn-warning-custom:hover {
    background-color: #e67700;
    border-color: #e67700;
    color: #fff;
  }
</style>

@php
  $classTrangThaiDot = match ($dotPhanPhoi->trangThai ?? '') {
      'Đang chuẩn bị' => 'status-paused',
      'Đang phân phối' => 'status-active',
      'Hoàn thành' => 'status-completed',
      'Đã hủy' => 'status-danger',
      default => 'status-default',
  };

  $chiTietGroups = $dotPhanPhoi->chiTietPhanPhois->groupBy(function ($chiTiet) {
      return implode('|', [
          $chiTiet->loaiPhanPhoi ?? '',
          $chiTiet->idDiaDiem ?? '',
          $chiTiet->idTiepNhan ?? '',
          $chiTiet->nguoiNhan ?? '',
          $chiTiet->thoiGianGiao ?? '',
      ]);
  });
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết đợt phân phối</h5>
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

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '#phan-phoi') }}">
              {{ $chienDich->tenChienDich }}
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Chi tiết đợt phân phối
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

<div class="card mb-3 info-card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="mb-0">Thông tin chung</h5>

    <div class="d-flex gap-2">
      @if (($dotPhanPhoi->trangThai ?? '') !== 'Hoàn thành' && ($dotPhanPhoi->trangThai ?? '') !== 'Đã hủy')
        <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi/' . $dotPhanPhoi->idDotPhanPhoi . '/edit') }}"
           class="btn btn-warning-custom">
          Sửa
        </a>
      @endif

      <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '#phan-phoi') }}"
         class="btn btn-secondary">
        Quay lại
      </a>
    </div>
  </div>

  <div class="card-body">
    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="info-label">Mã đợt</div>
        <div class="info-value">
          {{ $dotPhanPhoi->idDotPhanPhoi }}
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="info-label">Trạng thái</div>
        <div class="info-value">
          <span class="d-inline-flex align-items-center gap-2">
            <span class="status-dot {{ $classTrangThaiDot }}"></span>
            <span>{{ $dotPhanPhoi->trangThai ?? '-' }}</span>
          </span>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="info-label">Ngày bắt đầu</div>
        <div class="info-value">
          {{ $dotPhanPhoi->ngayPhanPhoi
              ? \Carbon\Carbon::parse($dotPhanPhoi->ngayPhanPhoi)->format('d/m/Y H:i')
              : '-' }}
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="info-label">Số dòng phân phối</div>
        <div class="info-value">
          {{ $dotPhanPhoi->chiTietPhanPhois->count() }}
        </div>
      </div>

      <div class="col-md-12">
        <div class="info-label">Ghi chú</div>
        <div class="info-value">
          {{ $dotPhanPhoi->ghiChu ?: 'Không có ghi chú.' }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Chi tiết phân phối</h5>
  </div>

  <div class="card-body">
    @forelse ($chiTietGroups as $group)
      @php
        $first = $group->first();

        $loaiPhanPhoi = $first->loaiPhanPhoi ?? '-';
        $yeuCau = $first->tiepNhan->yeuCau ?? null;

        $diaDiemPhanPhoi = $first->diaDiem ?? null;
        $diaDiemYeuCau = $yeuCau->diaDiem ?? null;

        $diaChiPhanPhoi = collect([
            $diaDiemPhanPhoi->chiTietDiaDiem ?? null,
            $diaDiemPhanPhoi->phuongXa ?? null,
            $diaDiemPhanPhoi->tinhThanh ?? null,
        ])->filter()->implode(', ');

        $diaChiYeuCau = collect([
            $diaDiemYeuCau->chiTietDiaDiem ?? null,
            $diaDiemYeuCau->phuongXa ?? null,
            $diaDiemYeuCau->tinhThanh ?? null,
        ])->filter()->implode(', ');

        $diaDiemChinh = $loaiPhanPhoi === 'Yêu cầu'
            ? $diaDiemYeuCau
            : $diaDiemPhanPhoi;

        $diaChiChinh = $loaiPhanPhoi === 'Yêu cầu'
            ? $diaChiYeuCau
            : $diaChiPhanPhoi;

        $markers = [];

        if ($diaDiemPhanPhoi && $diaDiemPhanPhoi->viDo && $diaDiemPhanPhoi->kinhDo) {
            $markers[] = [
                'lat' => (float) $diaDiemPhanPhoi->viDo,
                'lng' => (float) $diaDiemPhanPhoi->kinhDo,
                'label' => 'Địa điểm phân phối',
                'address' => $diaChiPhanPhoi,
            ];
        }

        if ($yeuCau && $diaDiemYeuCau && $diaDiemYeuCau->viDo && $diaDiemYeuCau->kinhDo) {
            $markers[] = [
                'lat' => (float) $diaDiemYeuCau->viDo,
                'lng' => (float) $diaDiemYeuCau->kinhDo,
                'label' => 'Địa điểm yêu cầu',
                'address' => $diaChiYeuCau,
            ];
        }

        $markersJson = json_encode($markers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      @endphp

      <div class="phan-phoi-block">
        <div class="row g-3 mb-3">
          <div class="col-lg-5">
            <div class="detail-info-line">
              <div class="info-label">Loại phân phối</div>
              <div class="info-value">
                {{ $loaiPhanPhoi }}
              </div>
            </div>

            <div class="detail-info-line">
              <div class="info-label">Người nhận</div>
              <div class="info-value">
                {{ $first->nguoiNhan ?: '-' }}
              </div>
            </div>

            <div class="detail-info-line">
              <div class="info-label">Ngày giao</div>
              <div class="info-value">
                {{ $first->thoiGianGiao
                    ? \Carbon\Carbon::parse($first->thoiGianGiao)->format('d/m/Y H:i')
                    : '-' }}
              </div>
            </div>

            <div class="detail-info-line">
              <div class="info-label">
                {{ $loaiPhanPhoi === 'Yêu cầu' ? 'Địa điểm yêu cầu' : 'Địa điểm phân phối' }}
              </div>

              <div class="info-value">
                {{ $diaChiChinh !== '' ? $diaChiChinh : '-' }}
              </div>
            </div>

            @if ($yeuCau)
              <div class="detail-info-line">
                <div class="info-label">Yêu cầu cứu trợ</div>

                <div class="info-value">
                  {{ $yeuCau->tieuDeYeuCau ?? $yeuCau->loaiYeuCau ?? '-' }}
                </div>

                <small class="text-muted">
                  {{ $diaChiYeuCau !== '' ? $diaChiYeuCau : '-' }}
                </small>
              </div>
            @endif
          </div>

          <div class="col-lg-7">
            @if (count($markers) > 0)
              <div class="phan-phoi-map"
                   data-phan-phoi-map></div>

              <script type="application/json" data-map-markers>
{!! $markersJson !!}
              </script>
            @else
              <div class="map-empty">
                Chưa có tọa độ để hiển thị bản đồ cho chi tiết phân phối này.
              </div>
            @endif
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0 detail-table">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 80px;">Mã</th>
                <th class="text-start">Hàng hóa</th>
                <th style="width: 260px;">Danh mục</th>
                <th style="width: 160px;">Số lượng giao</th>
                <th style="width: 170px;">Trạng thái</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($group as $chiTiet)
                @php
                  $classTrangThaiChiTiet = match ($chiTiet->trangThai ?? '') {
                      'Chưa giao' => 'status-paused',
                      'Đã giao' => 'status-completed',
                      'Không giao được' => 'status-danger',
                      'Đã hủy' => 'status-default',
                      default => 'status-default',
                  };
                @endphp

                <tr>
                  <td class="text-center cell-nowrap">
                    {{ $chiTiet->idChiTietPhanPhoi }}
                  </td>

                  <td class="text-start">
                    <div class="fw-semibold">
                      {{ $chiTiet->nguonLuc->hangHoa->tenHangHoa ?? '-' }}
                    </div>

                    <small class="text-muted">
                      Đơn vị:
                      {{ $chiTiet->nguonLuc->hangHoa->donViTinh ?? '-' }}
                    </small>
                  </td>

                  <td>
                    {{ $chiTiet->nguonLuc->hangHoa->danhMucHang->tenDanhMucHang ?? '-' }}
                  </td>

                  <td class="text-center cell-nowrap">
                    {{ number_format($chiTiet->soLuongGiao, 2) }}
                    {{ $chiTiet->nguonLuc->hangHoa->donViTinh ?? '' }}
                  </td>

                  <td class="text-center cell-nowrap">
                    <span class="d-inline-flex align-items-center justify-content-center gap-2">
                      <span class="status-dot {{ $classTrangThaiChiTiet }}"></span>
                      <span>{{ $chiTiet->trangThai ?? '-' }}</span>
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @empty
      <div class="text-center text-muted py-4">
        Đợt phân phối này chưa có chi tiết phân phối.
      </div>
    @endforelse
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (!window.L) {
      return;
    }

    document.querySelectorAll('[data-phan-phoi-map]').forEach(function (mapElement) {
      const markersScript = mapElement.parentElement.querySelector('[data-map-markers]');

      if (!markersScript) {
        return;
      }

      let markers = [];

      try {
        markers = JSON.parse(markersScript.textContent);
      } catch (error) {
        markers = [];
      }

      if (!markers.length) {
        return;
      }

      const firstMarker = markers[0];

      const map = L.map(mapElement).setView(
        [firstMarker.lat, firstMarker.lng],
        14
      );

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);

      const bounds = [];

      markers.forEach(function (markerData) {
        const marker = L.marker([
          markerData.lat,
          markerData.lng
        ]).addTo(map);

        marker.bindPopup(
          '<strong>' + markerData.label + '</strong><br>' +
          (markerData.address || '')
        );

        bounds.push([markerData.lat, markerData.lng]);
      });

      if (bounds.length > 1) {
        map.fitBounds(bounds, {
          padding: [30, 30]
        });
      }

      setTimeout(function () {
        map.invalidateSize();
      }, 200);
    });

    if (window.feather) {
      feather.replace();
    }
  });
</script>
@endsection