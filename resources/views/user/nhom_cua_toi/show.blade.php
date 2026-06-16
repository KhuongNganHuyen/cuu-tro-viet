@extends('layouts.user')

@section('title', 'Chi tiết đăng ký nhóm | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

@php
  $duongDanAnhNhom = !empty($nhom->anhDaiDien)
      ? asset('storage/' . $nhom->anhDaiDien)
      : asset('storage/nhom-tinh-nguyen/group.jpg');

  $diaDiemNhom = $nhom->diaDiem;

  $coToaDoNhom = $diaDiemNhom
      && $diaDiemNhom->viDo
      && $diaDiemNhom->kinhDo;

  $classTrangThai = match ($nhom->trangThai) {
      'Chờ duyệt' => 'status-pending',
      'Từ chối' => 'status-rejected',
      default => 'status-stopped',
  };
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">
            Chi tiết đăng ký nhóm
          </h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">
              Tổng quan
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">
              Nhóm tình nguyện của tôi
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Chi tiết đăng ký
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
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h5 class="mb-0">
        Thông tin đăng ký nhóm
      </h5>

      <a href="{{ url('/user/nhom-cua-toi') }}"
         class="btn btn-secondary">
        Quay lại
      </a>
    </div>
  </div>

  <div class="card-body">
    <div class="group-info-wrapper">
      <div class="row align-items-center justify-content-center">

        {{-- Ảnh, tên và trạng thái --}}
        <div class="col-lg-5 col-md-5 mb-4 mb-md-0">
          <div class="group-avatar-box">
            <img src="{{ $duongDanAnhNhom }}"
                 alt="Ảnh đại diện nhóm"
                 class="rounded-circle group-avatar mb-3">

            <h5 class="mb-2 text-break">
              {{ $nhom->tenNhom }}
            </h5>

            <span class="d-inline-flex align-items-center gap-2">
              <span class="status-dot {{ $classTrangThai }}"></span>
              {{ $nhom->trangThai }}
            </span>
          </div>
        </div>

        {{-- Thông tin chi tiết --}}
        <div class="col-lg-7 col-md-7">
          <div class="row mb-3">
            <div class="col-md-4 text-muted">
              Mã nhóm
            </div>

            <div class="col-md-8">
              {{ $nhom->idNhom }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4 text-muted">
              Tên nhóm
            </div>

            <div class="col-md-8 text-break">
              {{ $nhom->tenNhom }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4 text-muted">
              Người đăng ký
            </div>

            <div class="col-md-8">
              {{ $nhom->nhomTruong->hoTen ?? '-' }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4 text-muted">
              Địa điểm
            </div>

            <div class="col-md-8">
              @if ($nhom->diaDiem)
                @if (!empty($nhom->diaDiem->chiTietDiaDiem))
                  {{ $nhom->diaDiem->chiTietDiaDiem }},
                @endif

                @if (!empty($nhom->diaDiem->phuongXa))
                  {{ $nhom->diaDiem->phuongXa }},
                @endif

                {{ $nhom->diaDiem->tinhThanh ?? '-' }}
              @else
                -
              @endif
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4 text-muted">
              Trạng thái
            </div>

            <div class="col-md-8">
              <span class="d-inline-flex align-items-center gap-2">
                <span class="status-dot {{ $classTrangThai }}"></span>
                {{ $nhom->trangThai }}
              </span>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4 text-muted">
              Ngày đăng ký
            </div>

            <div class="col-md-8">
              @if ($nhom->ngayTao)
                {{ \Carbon\Carbon::parse($nhom->ngayTao)->format('d/m/Y H:i') }}
              @else
                -
              @endif
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 text-muted">
              Mô tả
            </div>

            <div class="col-md-8 text-break">
              @if (!empty($nhom->moTa))
                {!! nl2br(e($nhom->moTa)) !!}
              @else
                <span class="text-muted">
                  Chưa có mô tả.
                </span>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Bản đồ --}}
    <div class="mt-5">
      <div class="mb-2">
        <h6 class="mb-1">
          Vị trí hoạt động của nhóm
        </h6>
      </div>

      @if ($coToaDoNhom)
        <div id="nhomViTriMap"></div>
      @else
        <div class="alert alert-warning mb-0">
          Chưa có vĩ độ và kinh độ để hiển thị vị trí trên bản đồ.
        </div>
      @endif
    </div>
  </div>
</div>

<style>
  .group-info-wrapper {
    max-width: 1050px;
    margin: 0 auto;
  }

  .group-avatar-box {
    width: 100%;
    max-width: 310px;
    margin: 0 auto;
    text-align: center;
  }

  .group-avatar {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border: 1px solid #dee2e6;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .status-pending {
    background-color: #ffc107;
  }

  .status-rejected {
    background-color: #dc3545;
  }

  .status-stopped {
    background-color: #6c757d;
  }

  #nhomViTriMap {
    width: 100%;
    height: 360px;
    border-radius: 12px;
  }
</style>

@if ($coToaDoNhom)
  <div id="nhomMapData"
       data-lat="{{ $diaDiemNhom->viDo }}"
       data-lng="{{ $diaDiemNhom->kinhDo }}"
       data-ten-nhom="{{ e($nhom->tenNhom) }}"
       data-chi-tiet="{{ e($diaDiemNhom->chiTietDiaDiem ?? '') }}"
       data-phuong-xa="{{ e($diaDiemNhom->phuongXa ?? '') }}"
       data-tinh-thanh="{{ e($diaDiemNhom->tinhThanh ?? '') }}"
       hidden>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const mapData = document.getElementById('nhomMapData');

      if (!mapData) {
        return;
      }

      const lat = parseFloat(mapData.dataset.lat);
      const lng = parseFloat(mapData.dataset.lng);

      if (
        !Number.isFinite(lat)
        || !Number.isFinite(lng)
      ) {
        return;
      }

      const tenNhom =
        mapData.dataset.tenNhom
        || 'Nhóm tình nguyện';

      const diaChiParts = [
        mapData.dataset.chiTiet,
        mapData.dataset.phuongXa,
        mapData.dataset.tinhThanh
      ].filter(function (value) {
        return value && value.trim() !== '';
      });

      const diaChi = diaChiParts.length > 0
        ? diaChiParts.join(', ')
        : 'Chưa có thông tin địa chỉ';

      const map = L.map('nhomViTriMap')
        .setView([lat, lng], 15);

      L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
          maxZoom: 19,
          attribution: '&copy; OpenStreetMap'
        }
      ).addTo(map);

      const popupContent =
        '<strong>' + tenNhom + '</strong><br>'
        + diaChi;

      L.marker([lat, lng])
        .addTo(map)
        .bindPopup(popupContent)
        .openPopup();

      setTimeout(function () {
        map.invalidateSize();
      }, 200);
    });
  </script>
@endif
@endsection