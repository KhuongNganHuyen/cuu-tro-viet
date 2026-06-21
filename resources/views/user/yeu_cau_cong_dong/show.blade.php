@extends('layouts.user')

@section('title', 'Chi tiết yêu cầu cộng đồng | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

<style>
  .info-label {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 3px;
  }

  .info-value {
    color: #212529;
    font-weight: 500;
  }

  .status-dot,
  .muc-do-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    flex-shrink: 0;
    border-radius: 50%;
  }

  .status-waiting {
    background-color: #ffc107;
  }

  .status-received {
    background-color: #0d6efd;
  }

  .status-more-help {
    background-color: #fd7e14;
  }

  .status-completed {
    background-color: #198754;
  }

  .status-cancelled {
    background-color: #dc3545;
  }

  .status-default {
    background-color: #6c757d;
  }

  .muc-do-emergency {
    background-color: #dc3545;
  }

  .muc-do-high {
    background-color: #fd7e14;
  }

  .muc-do-medium {
    background-color: #0dcaf0;
  }

  .muc-do-low {
    background-color: #6c757d;
  }

  .muc-do-default {
    background-color: #adb5bd;
  }

  .request-description {
    white-space: pre-line;
    line-height: 1.7;
  }

  .support-image-box {
    width: 100%;
    min-height: 280px;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .support-image {
    width: 100%;
    max-height: 360px;
    object-fit: contain;
  }

  #map {
    height: 360px;
    width: 100%;
    border-radius: 12px;
  }

  .table th,
  .table td {
    vertical-align: middle;
  }

  .tiep-nhan-row {
    cursor: pointer;
  }

  .tiep-nhan-row:hover {
    background-color: #f8f9fa;
  }

  .tiep-nhan-detail-row {
    background-color: #fbfcfd;
  }

  .tiep-nhan-content-box {
    border: 1px dashed #ced4da;
    border-radius: 10px;
    padding: 14px;
    white-space: pre-line;
    line-height: 1.7;
    background-color: #ffffff;
  }

  .tiep-nhan-table {
    table-layout: fixed;
    width: 100%;
  }

  .tiep-nhan-table th {
    white-space: normal;
    line-height: 1.35;
    vertical-align: middle;
    font-size: 13px;
  }

  .tiep-nhan-table td {
    vertical-align: middle;
    font-size: 14px;
  }

  .tiep-nhan-table .col-ma {
    width: 55px;
    white-space: nowrap;
  }

  .tiep-nhan-table .col-nhom {
    width: 26%;
  }

  .tiep-nhan-table .col-chien-dich {
    width: 27%;
  }

  .tiep-nhan-table .col-thoi-gian {
    width: 145px;
  }

  .tiep-nhan-table .col-du-kien {
    width: 130px;
  }

  .tiep-nhan-table .col-trang-thai {
    width: 145px;
  }

  .tiep-nhan-table td.col-thoi-gian,
  .tiep-nhan-table td.col-du-kien,
  .tiep-nhan-table td.col-trang-thai {
    white-space: nowrap;
  }
</style>

@php
  $trangThaiYeuCau = $yeuCau->trangThai;

  $classTrangThaiYeuCau = match ($trangThaiYeuCau) {
      'Chờ tiếp nhận' => 'status-waiting',
      'Đã tiếp nhận' => 'status-received',
      'Cần thêm hỗ trợ' => 'status-more-help',
      'Hoàn thành' => 'status-completed',
      'Đã hủy' => 'status-cancelled',
      default => 'status-default',
  };

  $classMucDo = match ($yeuCau->mucDoKhanCap) {
      'Khẩn cấp' => 'muc-do-emergency',
      'Cao' => 'muc-do-high',
      'Trung bình' => 'muc-do-medium',
      'Thấp' => 'muc-do-low',
      default => 'muc-do-default',
  };

  $diaChiDayDu = collect([
      $yeuCau->diaDiem->chiTietDiaDiem ?? null,
      $yeuCau->diaDiem->phuongXa ?? null,
      $yeuCau->diaDiem->tinhThanh ?? null,
  ])->filter()->implode(', ');

  $coNhomTiepNhan = $tiepNhans->isNotEmpty();
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết yêu cầu cộng đồng</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/yeu-cau-cong-dong') }}">Yêu cầu cộng đồng</a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Chi tiết
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <ul class="nav nav-tabs card-header-tabs" id="yeuCauDetailTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active"
                  id="thong-tin-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#thong-tin"
                  type="button"
                  role="tab">
            Thông tin yêu cầu
          </button>
        </li>

        @if ($coNhomTiepNhan)
          <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="nhom-tiep-nhan-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nhom-tiep-nhan"
                    type="button"
                    role="tab">
              Nhóm tiếp nhận ({{ $tiepNhans->count() }})
            </button>
          </li>
        @endif
      </ul>

      <div>
        <a href="{{ url('/user/yeu-cau-cong-dong') }}"
           class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>
  </div>

  <div class="card-body">
    <div class="tab-content" id="yeuCauDetailTabsContent">
      <div class="tab-pane fade show active" id="thong-tin" role="tabpanel">
        <div class="row">
          <div class="col-lg-7">
            <h5 class="mb-1">
              {{ $yeuCau->tieuDeYeuCau }}
            </h5>

            <small class="text-muted d-block mb-4">
              Mã yêu cầu: {{ $yeuCau->idYeuCau }}
            </small>

            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="info-label">Người gửi</div>
                <div class="info-value">{{ $yeuCau->nguoiGui->hoTen ?? '-' }}</div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Số điện thoại</div>
                <div class="info-value">{{ $yeuCau->nguoiGui->sdt ?? '-' }}</div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $yeuCau->nguoiGui->email ?? '-' }}</div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Số người cần hỗ trợ</div>
                <div class="info-value">{{ $yeuCau->soNguoi ?? '-' }}</div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Mức độ khẩn cấp</div>
                <div class="info-value d-inline-flex align-items-center gap-2">
                  <span class="muc-do-dot {{ $classMucDo }}"></span>
                  <span>{{ $yeuCau->mucDoKhanCap ?? '-' }}</span>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Trạng thái yêu cầu</div>
                <div class="info-value d-inline-flex align-items-center gap-2">
                  <span class="status-dot {{ $classTrangThaiYeuCau }}"></span>
                  <span>{{ $trangThaiYeuCau }}</span>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Thời gian gửi</div>
                <div class="info-value">
                  {{ $yeuCau->thoiGianGui
                      ? \Carbon\Carbon::parse($yeuCau->thoiGianGui)->format('d/m/Y H:i')
                      : '-' }}
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Địa chỉ</div>
                <div class="info-value">
                  {{ $diaChiDayDu !== '' ? $diaChiDayDu : '-' }}
                </div>
              </div>
            </div>

            <hr>

            <div class="mb-3">
              <div class="info-label">Mô tả tình hình</div>
              <div class="request-description">
                {{ $yeuCau->moTa }}
              </div>
            </div>
          </div>

          <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="info-label mb-2">Hình ảnh minh chứng</div>

            <div class="support-image-box">
              @if ($yeuCau->hinhAnh)
                <img src="{{ asset('storage/' . $yeuCau->hinhAnh) }}"
                     alt="Hình ảnh minh chứng"
                     class="support-image">
              @else
                <div class="text-muted text-center px-3">
                  Yêu cầu này chưa có hình ảnh minh chứng.
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="mt-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <h6 class="mb-0">Địa điểm cần hỗ trợ</h6>
              <small class="text-muted">
                {{ $diaChiDayDu !== '' ? $diaChiDayDu : 'Chưa có thông tin địa chỉ đầy đủ.' }}
              </small>
            </div>
          </div>

          <div id="map" class="rounded border"></div>
        </div>
      </div>

      @if ($coNhomTiepNhan)
        <div class="tab-pane fade" id="nhom-tiep-nhan" role="tabpanel">
          <div class="card h-100">
            <div class="card-header">
              <h5 class="mb-0">Danh sách nhóm tiếp nhận yêu cầu</h5>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0 tiep-nhan-table">
                  <thead>
                    <tr class="text-uppercase">
                      <th class="col-ma">Mã</th>
                      <th class="col-nhom">Nhóm tiếp nhận</th>
                      <th class="col-chien-dich">Chiến dịch</th>
                      <th class="col-thoi-gian">Thời gian tiếp nhận</th>
                      <th class="col-du-kien">Dự kiến hỗ trợ</th>
                      <th class="col-trang-thai">Trạng thái</th>
                    </tr>
                  </thead>

                  <tbody>
                    @foreach ($tiepNhans as $tiepNhan)
                      @php
                        $classTrangThaiTiepNhan = match ($tiepNhan->trangThai) {
                            'Đã tiếp nhận' => 'status-received',
                            'Cần thêm hỗ trợ' => 'status-more-help',
                            'Hoàn thành' => 'status-completed',
                            default => 'status-default',
                        };

                        $idDongChiTiet = 'noi-dung-tiep-nhan-' . $tiepNhan->idTiepNhan;
                      @endphp

                      <tr class="tiep-nhan-row"
                          data-target-row="{{ $idDongChiTiet }}">
                        <td class="col-ma">{{ $tiepNhan->idTiepNhan }}</td>

                        <td class="col-nhom">
                          <strong>{{ $tiepNhan->nhom->tenNhom ?? '-' }}</strong>
                        </td>

                        <td class="col-chien-dich">
                          {{ $tiepNhan->chienDich->tenChienDich ?? '-' }}
                        </td>

                        <td class="col-thoi-gian">
                          {{ $tiepNhan->thoiGianTiepNhan
                              ? \Carbon\Carbon::parse($tiepNhan->thoiGianTiepNhan)->format('d/m/Y H:i')
                              : '-' }}
                        </td>

                        <td class="col-du-kien">
                          {{ $tiepNhan->thoiGianDuKienHoTro
                              ? \Carbon\Carbon::parse($tiepNhan->thoiGianDuKienHoTro)->format('d/m/Y')
                              : '-' }}
                        </td>

                        <td class="col-trang-thai">
                          <span class="d-inline-flex align-items-center gap-2">
                            <span class="status-dot {{ $classTrangThaiTiepNhan }}"></span>
                            <span>{{ $tiepNhan->trangThai ?? '-' }}</span>
                          </span>
                        </td>
                      </tr>

                      <tr id="{{ $idDongChiTiet }}"
                          class="tiep-nhan-detail-row d-none">
                        <td colspan="6">
                          <div class="info-label">Nội dung đảm nhận</div>

                          <div class="tiep-nhan-content-box">
                            {{ $tiepNhan->noiDungDamNhan ?: 'Chưa có nội dung đảm nhận.' }}
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const lat = Number('{{ $yeuCau->diaDiem->viDo ?? 16.047079 }}');
    const lng = Number('{{ $yeuCau->diaDiem->kinhDo ?? 108.206230 }}');

    const mapElement = document.getElementById('map');

    if (mapElement) {
      const map = L.map('map').setView([lat, lng], 14);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);

      L.marker([lat, lng]).addTo(map);

      document
        .querySelectorAll('button[data-bs-toggle="tab"]')
        .forEach(function (tabButton) {
          tabButton.addEventListener('shown.bs.tab', function () {
            setTimeout(function () {
              map.invalidateSize();
            }, 150);
          });
        });
    }

    document
      .querySelectorAll('.tiep-nhan-row')
      .forEach(function (row) {
        row.addEventListener('click', function () {
          const targetId = row.getAttribute('data-target-row');
          const targetRow = document.getElementById(targetId);

          if (!targetRow) {
            return;
          }

          targetRow.classList.toggle('d-none');
        });
      });
  });
</script>
@endsection