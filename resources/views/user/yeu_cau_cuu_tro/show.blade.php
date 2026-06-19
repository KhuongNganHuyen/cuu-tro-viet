@extends('layouts.user')

@section('title', 'Chi tiết yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

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

  $coTiepNhan = $yeuCau->tiepNhans->isNotEmpty();

  $diaDiemYeuCau = $yeuCau->diaDiem;

  $coToaDo = $diaDiemYeuCau
      && $diaDiemYeuCau->viDo
      && $diaDiemYeuCau->kinhDo;

  $diaChiDayDu = collect([
      $diaDiemYeuCau->chiTietDiaDiem ?? null,
      $diaDiemYeuCau->phuongXa ?? null,
      $diaDiemYeuCau->tinhThanh ?? null,
  ])->filter()->implode(', ');

  $tiepNhansDangXuLy = $yeuCau->tiepNhans
      ->where('trangThai', '!=', 'Hoàn thành');

  $coNhomDaTiepNhan = $tiepNhansDangXuLy
      ->contains('trangThai', 'Đã tiếp nhận');

  $coNhomCanThemHoTro = $tiepNhansDangXuLy
      ->contains('trangThai', 'Cần thêm hỗ trợ');

  $coTheDoiCanThemHoTro =
      in_array($trangThaiYeuCau, ['Đã tiếp nhận', 'Cần thêm hỗ trợ'], true)
      && $tiepNhansDangXuLy->isNotEmpty();
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/yeu-cau-cuu-tro') }}">
              Yêu cầu cứu trợ
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

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <ul class="nav nav-tabs card-header-tabs"
          id="yeuCauTabs"
          role="tablist">

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

        <li class="nav-item" role="presentation">
          <button class="nav-link"
                  id="tiep-nhan-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#tiep-nhan"
                  type="button"
                  role="tab">
            Nhóm tiếp nhận
            @if ($coTiepNhan)
              ({{ $yeuCau->tiepNhans->count() }})
            @endif
          </button>
        </li>
      </ul>

      <div class="d-flex flex-wrap gap-2 justify-content-end">
        @if (
          $trangThaiYeuCau === 'Chờ tiếp nhận'
          && !$coTiepNhan
        )
          <form action="{{ url('/user/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/huy') }}"
                method="POST"
                onsubmit="return confirm('Bạn có chắc muốn hủy yêu cầu cứu trợ này không?')">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="btn btn-outline-danger">
              Hủy yêu cầu
            </button>
          </form>
        @endif

        @if ($coTheDoiCanThemHoTro && $coNhomDaTiepNhan)
          <form action="{{ url('/user/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/can-them-ho-tro') }}"
                method="POST"
                onsubmit="return confirm('Bạn muốn báo yêu cầu này cần thêm hỗ trợ?')">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="btn btn-outline-warning">
              Cần thêm hỗ trợ
            </button>
          </form>
        @endif

        @if ($coTheDoiCanThemHoTro && $coNhomCanThemHoTro)
          <form action="{{ url('/user/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/thu-hoi-can-them-ho-tro') }}"
                method="POST"
                onsubmit="return confirm('Bạn muốn thu hồi trạng thái cần thêm hỗ trợ?')">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="btn btn-outline-primary">
              Hủy thêm hỗ trợ
            </button>
          </form>
        @endif

        @if (
          in_array($trangThaiYeuCau, ['Đã tiếp nhận', 'Cần thêm hỗ trợ'])
          && $coTiepNhan
        )
          <form action="{{ url('/user/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/xac-nhan-hoan-thanh') }}"
                method="POST"
                onsubmit="return confirm('Bạn xác nhận đã nhận đủ hỗ trợ và muốn hoàn thành yêu cầu này?')">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="btn btn-success">
              Xác nhận đã nhận đủ
            </button>
          </form>
        @endif

        <a href="{{ url('/user/yeu-cau-cuu-tro') }}"
           class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>
  </div>

  <div class="card-body">
    <div class="tab-content" id="yeuCauTabsContent">

      {{-- TAB THÔNG TIN YÊU CẦU --}}
      <div class="tab-pane fade show active"
           id="thong-tin"
           role="tabpanel">

        <div class="mb-4">
          <div class="text-muted small mb-1">
            Yêu cầu #{{ $yeuCau->idYeuCau }}
          </div>

          <h4 class="mb-2">
            {{ $yeuCau->tieuDeYeuCau }}
          </h4>

          <span class="d-inline-flex align-items-center gap-2">
            <span class="status-dot {{ $classTrangThaiYeuCau }}"></span>
            {{ $trangThaiYeuCau }}
          </span>
        </div>

        <div class="row">
          {{-- THÔNG TIN BÊN TRÁI --}}
          <div class="col-lg-7">
            <div class="request-info-wrapper">
              <div class="row mb-3">
                <div class="col-md-4 text-muted">
                  Mã yêu cầu
                </div>

                <div class="col-md-8">
                  {{ $yeuCau->idYeuCau }}
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">
                  Tiêu đề
                </div>

                <div class="col-md-8 text-break">
                  {{ $yeuCau->tieuDeYeuCau }}
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">
                  Mức độ khẩn cấp
                </div>

                <div class="col-md-8">
                  {{ $yeuCau->mucDoKhanCap ?? '-' }}
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">
                  Số người cần hỗ trợ
                </div>

                <div class="col-md-8">
                  {{ $yeuCau->soNguoi ?? '-' }}
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">
                  Trạng thái
                </div>

                <div class="col-md-8">
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="status-dot {{ $classTrangThaiYeuCau }}"></span>
                    {{ $trangThaiYeuCau }}
                  </span>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">
                  Thời gian gửi
                </div>

                <div class="col-md-8">
                  @if ($yeuCau->thoiGianGui)
                    {{ \Carbon\Carbon::parse($yeuCau->thoiGianGui)->format('d/m/Y H:i') }}
                  @else
                    -
                  @endif
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 text-muted">
                  Mô tả tình hình
                </div>

                <div class="col-md-8 text-break request-description">
                  {!! nl2br(e($yeuCau->moTa ?? '-')) !!}
                </div>
              </div>
            </div>
          </div>

          {{-- ẢNH BÊN PHẢI --}}
          <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="request-image-box">
              @if ($yeuCau->hinhAnh)
                <a href="{{ asset('storage/' . $yeuCau->hinhAnh) }}"
                   target="_blank"
                   class="d-block">

                  <img src="{{ asset('storage/' . $yeuCau->hinhAnh) }}"
                       alt="Hình ảnh minh chứng"
                       class="img-fluid rounded">
                </a>

                <small class="text-muted d-block text-center mt-2">
                  Nhấn vào ảnh để xem kích thước đầy đủ.
                </small>
              @else
                <div class="request-image-empty">
                  <i class="ti ti-photo-off"></i>

                  <div class="mt-2">
                    Không có hình ảnh minh chứng.
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>

        <hr class="my-4">

        {{-- ĐỊA ĐIỂM VÀ BẢN ĐỒ TRẢI NGANG --}}
        <div>
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h6 class="mb-1">Địa điểm cần hỗ trợ</h6>

              <small class="text-muted">
                {{ $diaChiDayDu !== '' ? $diaChiDayDu : 'Chưa có thông tin địa điểm.' }}
              </small>
            </div>

            @if ($coToaDo)
              <div class="text-muted small">
                Vĩ độ: {{ $diaDiemYeuCau->viDo }},
                Kinh độ: {{ $diaDiemYeuCau->kinhDo }}
              </div>
            @endif
          </div>

          <div class="row mb-3">
            <div class="col-md-4 mb-2">
              <div class="text-muted small">
                Tỉnh/Thành
              </div>

              <div>
                {{ $diaDiemYeuCau->tinhThanh ?? '-' }}
              </div>
            </div>

            <div class="col-md-4 mb-2">
              <div class="text-muted small">
                Phường/Xã
              </div>

              <div>
                {{ $diaDiemYeuCau->phuongXa ?? '-' }}
              </div>
            </div>

            <div class="col-md-4 mb-2">
              <div class="text-muted small">
                Địa chỉ chi tiết
              </div>

              <div>
                {{ $diaDiemYeuCau->chiTietDiaDiem ?? '-' }}
              </div>
            </div>
          </div>

          @if ($coToaDo)
            <div id="yeuCauMap"></div>
          @else
            <div class="alert alert-warning mb-0">
              Yêu cầu này chưa có thông tin vĩ độ và kinh độ.
            </div>
          @endif
        </div>
      </div>

      {{-- TAB NHÓM TIẾP NHẬN --}}
      <div class="tab-pane fade"
          id="tiep-nhan"
          role="tabpanel">

        <div class="mb-3">
          <h6 class="mb-1">
            Danh sách nhóm và chiến dịch tiếp nhận
          </h6>

          <small class="text-muted">
            Nhấn vào từng nhóm để xem nội dung đảm nhận.
          </small>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0 tiep-nhan-table">
            <thead>
              <tr class="text-uppercase">
                <th style="width: 80px;">Mã</th>
                <th>Nhóm tình nguyện</th>
                <th>Chiến dịch</th>
                <th style="width: 180px;">Thời gian tiếp nhận</th>
                <th style="width: 170px;">Trạng thái</th>
                <th style="width: 170px;">Dự kiến hỗ trợ</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($yeuCau->tiepNhans as $tiepNhan)
                @php
                  $trangThaiTiepNhan = $tiepNhan->trangThai;

                  $classTrangThaiTiepNhan = match ($trangThaiTiepNhan) {
                      'Đã tiếp nhận' => 'status-received',
                      'Cần thêm hỗ trợ' => 'status-more-help',
                      'Hoàn thành' => 'status-completed',
                      default => 'status-default',
                  };

                  $idChiTietTiepNhan = 'chi-tiet-tiep-nhan-' . $tiepNhan->idTiepNhan;
                @endphp

                {{-- HÀNG CHÍNH --}}
                <tr class="tiep-nhan-row"
                    data-bs-toggle="collapse"
                    data-bs-target="#{{ $idChiTietTiepNhan }}"
                    aria-expanded="false"
                    aria-controls="{{ $idChiTietTiepNhan }}">

                  <td class="fw-semibold">
                    {{ $tiepNhan->idTiepNhan }}
                  </td>

                  <td>
                    <div class="fw-semibold">
                      {{ $tiepNhan->nhom->tenNhom ?? '-' }}
                    </div>
                  </td>

                  <td>
                    {{ $tiepNhan->chienDich->tenChienDich ?? '-' }}
                  </td>

                  <td>
                    @if ($tiepNhan->thoiGianTiepNhan)
                      {{ \Carbon\Carbon::parse($tiepNhan->thoiGianTiepNhan)->format('d/m/Y H:i') }}
                    @else
                      -
                    @endif
                  </td>

                  <td>
                    <span class="d-inline-flex align-items-center gap-2">
                      <span class="status-dot {{ $classTrangThaiTiepNhan }}"></span>
                      {{ $trangThaiTiepNhan ?? '-' }}
                    </span>
                  </td>

                  <td>
                    @if ($tiepNhan->thoiGianDuKienHoTro)
                      {{ \Carbon\Carbon::parse($tiepNhan->thoiGianDuKienHoTro)->format('d/m/Y') }}
                    @else
                      -
                    @endif
                  </td>
                </tr>

                {{-- HÀNG MỞ RỘNG: CHỈ HIỆN NỘI DUNG ĐẢM NHẬN --}}
                <tr id="{{ $idChiTietTiepNhan }}"
                    class="collapse tiep-nhan-detail-row">

                  <td colspan="6">
                    <div class="tiep-nhan-detail">
                      <div class="text-muted small mb-2">
                        Nội dung đảm nhận
                      </div>

                      <div class="noi-dung-dam-nhan">
                        @if ($tiepNhan->noiDungDamNhan)
                          {!! nl2br(e($tiepNhan->noiDungDamNhan)) !!}
                        @else
                          <span class="text-muted">
                            Chưa có nội dung đảm nhận.
                          </span>
                        @endif
                      </div>

                      @if ($trangThaiTiepNhan === 'Cần thêm hỗ trợ')
                        <div class="mt-3 text-warning">
                          Nhóm này đang cần thêm nguồn lực hỗ trợ.
                        </div>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6"
                      class="text-center text-muted py-4">
                    Yêu cầu này chưa được nhóm tình nguyện nào tiếp nhận.
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

<style>
  #yeuCauTabs .nav-link {
    font-weight: 500;
  }

  .table th,
  .table td {
    vertical-align: middle;
  }

  .request-info-wrapper {
    max-width: 780px;
  }

  .request-description {
    line-height: 1.7;
  }

  .request-image-box {
    width: 100%;
    min-height: 300px;
  }

  .request-image-box img {
    display: block;
    width: 100%;
    height: 320px;
    object-fit: contain;
    border: 1px solid #e5e7eb;
    background-color: #f8f9fa;
  }

  .request-image-empty {
    height: 320px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background-color: #f8f9fa;
    color: #6c757d;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .request-image-empty i {
    font-size: 44px;
  }

  #yeuCauMap {
    width: 100%;
    height: 380px;
    border-radius: 10px;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    border-radius: 50%;
    flex-shrink: 0;
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

  .tiep-nhan-table th,
  .tiep-nhan-table td {
    vertical-align: middle;
  }

  .tiep-nhan-row {
    cursor: pointer;
  }

  .tiep-nhan-row:hover {
    background-color: #f5f7fb;
  }

  .tiep-nhan-detail-row td {
    padding: 0;
    background-color: transparent;
    border-top: 0;
  }

  .tiep-nhan-detail {
    margin-left: 20px;
    padding: 18px 20px;
    border-left: 2px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    background-color: #ffffff;
  }

  .noi-dung-dam-nhan {
    line-height: 1.8;
    white-space: normal;
    word-break: break-word;
  }
</style>

@if ($coToaDo)
  <div id="yeuCauMapData"
       data-lat="{{ $diaDiemYeuCau->viDo }}"
       data-lng="{{ $diaDiemYeuCau->kinhDo }}"
       data-tieu-de="{{ e($yeuCau->tieuDeYeuCau) }}"
       data-chi-tiet="{{ e($diaDiemYeuCau->chiTietDiaDiem ?? '') }}"
       data-phuong-xa="{{ e($diaDiemYeuCau->phuongXa ?? '') }}"
       data-tinh-thanh="{{ e($diaDiemYeuCau->tinhThanh ?? '') }}"
       hidden>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const mapData = document.getElementById('yeuCauMapData');

      const lat = parseFloat(mapData.dataset.lat);
      const lng = parseFloat(mapData.dataset.lng);

      const tieuDe = mapData.dataset.tieuDe || 'Yêu cầu cứu trợ';
      const chiTiet = mapData.dataset.chiTiet || '';
      const phuongXa = mapData.dataset.phuongXa || '';
      const tinhThanh = mapData.dataset.tinhThanh || '';

      const diaChiParts = [];

      if (chiTiet) {
        diaChiParts.push(chiTiet);
      }

      if (phuongXa) {
        diaChiParts.push(phuongXa);
      }

      if (tinhThanh) {
        diaChiParts.push(tinhThanh);
      }

      const diaChi = diaChiParts.length > 0
        ? diaChiParts.join(', ')
        : 'Chưa có thông tin địa chỉ';

      const map = L.map('yeuCauMap').setView([lat, lng], 15);

      L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
          maxZoom: 19,
          attribution: '&copy; OpenStreetMap'
        }
      ).addTo(map);

      const popupContent =
        '<strong>' + tieuDe + '</strong><br>' +
        diaChi;

      L.marker([lat, lng])
        .addTo(map)
        .bindPopup(popupContent)
        .openPopup();

      document
        .getElementById('thong-tin-tab')
        .addEventListener('shown.bs.tab', function () {
          map.invalidateSize();
        });

      setTimeout(function () {
        map.invalidateSize();
      }, 200);
    });
  </script>
@endif
@endsection