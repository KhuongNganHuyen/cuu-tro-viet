@extends('layouts.nhom')

@section('title', 'Tạo chiến dịch từ yêu cầu | Cứu Trợ Việt')

@section('content')
<style>
  .su-kien-search-wrapper {
    position: relative;
  }

  .su-kien-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    z-index: 1050;
    max-height: 280px;
    overflow-y: auto;
    background-color: #ffffff;
    border: 1px solid #dbe0e5;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    display: none;
  }

  .su-kien-dropdown.show {
    display: block;
  }

  .su-kien-option {
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f5;
    font-size: 14px;
  }

  .su-kien-option:last-child {
    border-bottom: 0;
  }

  .su-kien-option:hover,
  .su-kien-option.active {
    background-color: #f5f8ff;
  }

  .su-kien-option .loai-su-kien {
    font-weight: 600;
  }

  .su-kien-option .ten-su-kien {
    color: #212529;
  }

  .info-label {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 3px;
  }

  .info-value {
    color: #212529;
    font-weight: 500;
  }

  .request-summary {
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 14px;
    background-color: #f8f9fa;
  }

  .request-description {
    white-space: pre-line;
    line-height: 1.6;
  }

  .nguon-luc-table th,
  .nguon-luc-table td {
    vertical-align: middle;
  }

  .scope-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    flex-shrink: 0;
    border-radius: 50%;
  }

  .scope-system {
    background-color: #adb5bd;
  }

  .scope-group {
    background-color: #198754;
  }

  .nguon-luc-card-header {
    gap: 12px;
  }

  .nguon-luc-search-box {
    max-width: 460px;
    min-width: 280px;
  }

  .search-input-wrapper {
    position: relative;
    min-width: 300px;
    max-width: 460px;
    flex: 1;
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

  .search-input-wrapper input {
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

  .filter-heading-cell {
    position: relative;
    padding: 0 !important;
  }

  .filter-heading-button {
    width: 100%;
    min-height: 56px;
    padding: 12px;
    border: 0;
    outline: 0;
    background: transparent;
    color: inherit;
    font: inherit;
    font-weight: 600;
    text-transform: uppercase;
    text-decoration: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .filter-heading-button.text-start {
    justify-content: flex-start;
  }

  .filter-heading-button:hover,
  .filter-heading-button:focus,
  .filter-heading-button:active {
    color: inherit;
    background-color: #f8f9fa;
    text-decoration: none;
    box-shadow: none;
  }

  .filter-heading-button::after {
    display: none !important;
    content: none !important;
  }

  .filter-active-dot {
    width: 6px;
    height: 6px;
    display: none;
    flex-shrink: 0;
    border-radius: 50%;
    background-color: #0d6efd;
  }

  .filter-heading-button.is-filtering .filter-active-dot {
    display: inline-block;
  }

  .filter-dropdown-menu {
    min-width: 210px;
    width: max-content;
    max-width: 320px;
    max-height: 220px;
    overflow-y: auto;
    padding: 6px 0;
    margin-top: 0;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    z-index: 1080;
  }

  .filter-dropdown-menu .dropdown-item {
    padding: 8px 14px;
    font-size: 14px;
    font-weight: 400;
    text-transform: none;
    white-space: normal;
    word-break: break-word;
  }

  .filter-dropdown-menu .dropdown-item:hover {
    background-color: #f5f7fb;
  }

  .filter-dropdown-menu .dropdown-item.active,
  .filter-dropdown-menu .dropdown-item:active {
    color: #212529;
    background-color: #e9ecef;
  }

  .nguon-luc-table-wrapper {
    overflow-x: auto;
    overflow-y: visible;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tạo chiến dịch từ yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              Tổng quan nhóm
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}">
              Yêu cầu cứu trợ
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}">
              Chi tiết
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Tạo chiến dịch
          </li>
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

@php
  $diaChiYeuCau = collect([
      $yeuCau->diaDiem->chiTietDiaDiem ?? null,
      $yeuCau->diaDiem->phuongXa ?? null,
      $yeuCau->diaDiem->tinhThanh ?? null,
  ])->filter()->implode(', ');

  $tenChienDichMacDinh =
      'Chiến dịch hỗ trợ ' .
      ($yeuCau->diaDiem->phuongXa
          ?? $yeuCau->diaDiem->tinhThanh
          ?? 'khu vực cần cứu trợ');

  $moTaMacDinh =
      'Chiến dịch được tạo dựa trên yêu cầu cứu trợ: '
      . $yeuCau->tieuDeYeuCau
      . '. '
      . $yeuCau->moTa;

  $suKienDangChon = $suKiens->firstWhere(
      'idSuKien',
      old('idSuKien')
  );

  $nhanSuKienDangChon = $suKienDangChon
      ? $suKienDangChon->loaiSuKien . ' - ' . $suKienDangChon->tenSuKien
      : '';
@endphp

<form action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tao-chien-dich') }}"
      method="POST"
      onsubmit="return confirm('Tạo chiến dịch mới và tiếp nhận yêu cầu này?')">
  @csrf

  <div class="card mb-3">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
          <h5 class="mb-1">Thông tin chiến dịch mới</h5>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-lg-7">
          <div class="mb-3">
            <label class="form-label">
              Tên chiến dịch <span class="text-danger">*</span>
            </label>

            <input type="text"
                   name="tenChienDich"
                   class="form-control"
                   value="{{ old('tenChienDich', $tenChienDichMacDinh) }}"
                   placeholder="Nhập tên chiến dịch"
                   autocomplete="off">
          </div>

          <div class="mb-3">
            <label class="form-label">
              Sự kiện cứu trợ <span class="text-danger">*</span>
            </label>

            <input type="hidden"
                   name="idSuKien"
                   id="idSuKien"
                   value="{{ old('idSuKien') }}">

            <div class="su-kien-search-wrapper">
              <input type="text"
                     id="suKienSearchInput"
                     class="form-control"
                     value="{{ $nhanSuKienDangChon }}"
                     placeholder="Gõ loại hoặc tên sự kiện để tìm..."
                     autocomplete="off">

              <div id="suKienDropdown" class="su-kien-dropdown">
                @foreach ($suKiens as $suKien)
                  @php
                    $nhanSuKien = $suKien->loaiSuKien . ' - ' . $suKien->tenSuKien;
                  @endphp

                  <div class="su-kien-option"
                       data-id="{{ $suKien->idSuKien }}"
                       data-label="{{ $nhanSuKien }}">
                    <span class="loai-su-kien">{{ $suKien->loaiSuKien }}</span>
                    <span> - </span>
                    <span class="ten-su-kien">{{ $suKien->tenSuKien }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="mb-0">
            <label class="form-label">
              Mô tả chiến dịch
            </label>

            <textarea name="moTa"
                      class="form-control"
                      rows="7"
                      placeholder="Mô tả mục tiêu, phạm vi và nội dung hỗ trợ của chiến dịch.">{{ old('moTa', $moTaMacDinh) }}</textarea>
          </div>
        </div>

        <div class="col-lg-5 mt-3 mt-lg-0">
          <div class="row">
            <div class="col-md-6 col-lg-12 mb-3">
              <label class="form-label">
                Ngày bắt đầu <span class="text-danger">*</span>
              </label>

              <input type="date"
                     name="ngayBatDau"
                     id="ngayBatDau"
                     class="form-control"
                     value="{{ old('ngayBatDau', now()->format('Y-m-d')) }}">
            </div>

            <div class="col-md-6 col-lg-12 mb-3">
              <label class="form-label">
                Ngày kết thúc
              </label>

              <input type="date"
                     name="ngayKetThuc"
                     id="ngayKetThuc"
                     class="form-control"
                     value="{{ old('ngayKetThuc') }}">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">
              Trạng thái chiến dịch <span class="text-danger">*</span>
            </label>

            <select name="trangThaiChienDich"
                    id="trangThaiChienDich"
                    class="form-control"
                    data-old-value="{{ old('trangThaiChienDich') }}">
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">
              Xác nhận cứu trợ
            </label>

            <div class="form-check">
              <input type="checkbox"
                     name="daXacNhanCuuTro"
                     value="1"
                     class="form-check-input"
                     id="daXacNhanCuuTro"
                     {{ old('daXacNhanCuuTro') ? 'checked' : '' }}>

              <label for="daXacNhanCuuTro"
                     class="form-check-label">
                Đã xác nhận hoạt động cứu trợ
              </label>
            </div>
          </div>

          <div class="mb-0">
            <label class="form-label">
              Ghi chú xác nhận
            </label>

            <textarea name="ghiChuXacNhan"
                      class="form-control"
                      rows="4"
                      placeholder="Ghi chú về quá trình xác nhận...">{{ old('ghiChuXacNhan') }}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h5 class="mb-0">Thông tin tiếp nhận yêu cầu</h5>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-lg-6 mb-3">
          <label class="form-label">
            Ngày dự kiến hỗ trợ
          </label>

          <input type="date"
                 name="thoiGianDuKienHoTro"
                 class="form-control"
                 min="{{ now()->format('Y-m-d') }}"
                 value="{{ old('thoiGianDuKienHoTro') }}">
        </div>

        <div class="col-lg-6 mb-3">
          <label class="form-label">
            Trạng thái tiếp nhận
          </label>

          <div class="form-control bg-light">
            Đã tiếp nhận
          </div>
        </div>
      </div>

      <div class="mb-0">
        <label class="form-label">
          Nội dung đảm nhận <span class="text-danger">*</span>
        </label>

        <textarea name="noiDungDamNhan"
                  class="form-control"
                  rows="5"
                  required
                  placeholder="Ghi rõ phần việc hoặc nguồn lực nhóm sẽ đảm nhận.">{{ old('noiDungDamNhan') }}</textarea>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center flex-wrap nguon-luc-card-header">
        <div>
          <h5 class="mb-1">Nguồn lực cần kêu gọi</h5>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <div class="search-input-wrapper">
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

    <div class="card-body">
      <div class="table-responsive nguon-luc-table-wrapper">
        @php
          $danhMucNguonLucs = $hangHoas
              ->map(fn ($hangHoa) => $hangHoa->danhMucHang->tenDanhMucHang ?? null)
              ->filter()
              ->unique()
              ->sort()
              ->values();

          $donViNguonLucs = $hangHoas
              ->pluck('donViTinh')
              ->filter()
              ->unique()
              ->sort()
              ->values();
        @endphp

        <table class="table table-hover mb-0 nguon-luc-table">
          <thead>
            <tr class="text-uppercase text-center">
              <th style="width: 70px;">
                Chọn
              </th>

              <th class="text-start">
                Hàng hóa
              </th>

              <th class="filter-heading-cell text-start" style="width: 24%;">
                <div class="dropdown w-100 h-100">
                  <button type="button"
                          id="btnLocDanhMucNguonLuc"
                          class="filter-heading-button text-start"
                          data-bs-toggle="dropdown"
                          data-bs-boundary="viewport"
                          aria-expanded="false">
                    <span>Danh mục</span>
                    <span class="filter-active-dot"></span>
                  </button>

                  <ul class="dropdown-menu filter-dropdown-menu">
                    <li>
                      <button type="button"
                              class="dropdown-item nguon-luc-filter-option active"
                              data-filter="danhMuc"
                              data-value="">
                        Tất cả danh mục
                      </button>
                    </li>

                    @foreach ($danhMucNguonLucs as $danhMuc)
                      <li>
                        <button type="button"
                                class="dropdown-item nguon-luc-filter-option"
                                data-filter="danhMuc"
                                data-value="{{ $danhMuc }}">
                          {{ $danhMuc }}
                        </button>
                      </li>
                    @endforeach
                  </ul>
                </div>
              </th>

              <th class="filter-heading-cell" style="width: 120px;">
                <div class="dropdown w-100 h-100">
                  <button type="button"
                          id="btnLocDonViNguonLuc"
                          class="filter-heading-button text-center"
                          data-bs-toggle="dropdown"
                          data-bs-boundary="viewport"
                          aria-expanded="false">
                    <span>Đơn vị</span>
                    <span class="filter-active-dot"></span>
                  </button>

                  <ul class="dropdown-menu filter-dropdown-menu">
                    <li>
                      <button type="button"
                              class="dropdown-item nguon-luc-filter-option active"
                              data-filter="donVi"
                              data-value="">
                        Tất cả đơn vị
                      </button>
                    </li>

                    @foreach ($donViNguonLucs as $donVi)
                      <li>
                        <button type="button"
                                class="dropdown-item nguon-luc-filter-option"
                                data-filter="donVi"
                                data-value="{{ $donVi }}">
                          {{ $donVi }}
                        </button>
                      </li>
                    @endforeach
                  </ul>
                </div>
              </th>

              <th class="filter-heading-cell" style="width: 150px;">
                <div class="dropdown w-100 h-100">
                  <button type="button"
                          id="btnLocPhamViNguonLuc"
                          class="filter-heading-button text-center"
                          data-bs-toggle="dropdown"
                          data-bs-boundary="viewport"
                          aria-expanded="false">
                    <span>Phạm vi</span>
                    <span class="filter-active-dot"></span>
                  </button>

                  <ul class="dropdown-menu filter-dropdown-menu">
                    <li>
                      <button type="button"
                              class="dropdown-item nguon-luc-filter-option active"
                              data-filter="phamVi"
                              data-value="">
                        Tất cả
                      </button>
                    </li>

                    <li>
                      <button type="button"
                              class="dropdown-item nguon-luc-filter-option"
                              data-filter="phamVi"
                              data-value="Của nhóm">
                        Của nhóm
                      </button>
                    </li>

                    <li>
                      <button type="button"
                              class="dropdown-item nguon-luc-filter-option"
                              data-filter="phamVi"
                              data-value="Hệ thống">
                        Hệ thống
                      </button>
                    </li>
                  </ul>
                </div>
              </th>

              <th style="width: 180px;">
                Số lượng cần
              </th>
            </tr>
          </thead>

          <tbody>
            @forelse ($hangHoas as $hangHoa)
              @php
                $idHangHoa = $hangHoa->idHangHoa;
                $daChon = old("nguonLuc.$idHangHoa.chon");
                $soLuongCu = old("nguonLuc.$idHangHoa.soLuongCanKeuGoi");
                $laHangHeThong = is_null($hangHoa->idNhom);
              @endphp

              <tr class="nguon-luc-row"
                  data-ten="{{ mb_strtolower($hangHoa->tenHangHoa ?? '', 'UTF-8') }}"
                  data-danh-muc="{{ $hangHoa->danhMucHang->tenDanhMucHang ?? '' }}"
                  data-don-vi="{{ $hangHoa->donViTinh ?? '' }}"
                  data-pham-vi="{{ $laHangHeThong ? 'Hệ thống' : 'Của nhóm' }}">
                <td class="text-center">
                  <input type="checkbox"
                        class="form-check-input nguon-luc-checkbox"
                        name="nguonLuc[{{ $idHangHoa }}][chon]"
                        value="1"
                        data-target="soLuongCanKeuGoi_{{ $idHangHoa }}"
                        {{ $daChon ? 'checked' : '' }}>
                </td>

                <td>
                  <div class="fw-semibold">
                    {{ $hangHoa->tenHangHoa }}
                  </div>
                </td>

                <td>
                  {{ $hangHoa->danhMucHang->tenDanhMucHang ?? '-' }}
                </td>

                <td class="text-center">
                  {{ $hangHoa->donViTinh }}
                </td>

                <td class="text-center">
                  @if ($laHangHeThong)
                    <span class="d-inline-flex align-items-center justify-content-center gap-2 text-muted">
                      <span class="scope-dot scope-system"></span>
                      <span>Hệ thống</span>
                    </span>
                  @else
                    <span class="d-inline-flex align-items-center justify-content-center gap-2">
                      <span class="scope-dot scope-group"></span>
                      <span>Của nhóm</span>
                    </span>
                  @endif
                </td>

                <td>
                  <input type="number"
                        name="nguonLuc[{{ $idHangHoa }}][soLuongCanKeuGoi]"
                        id="soLuongCanKeuGoi_{{ $idHangHoa }}"
                        class="form-control nguon-luc-so-luong"
                        value="{{ $soLuongCu }}"
                        min="0.01"
                        step="0.01"
                        placeholder="Nhập số lượng"
                        {{ $daChon ? '' : 'disabled' }}>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6"
                    class="text-center text-muted py-4">
                  Chưa có hàng hóa đang sử dụng để chọn cho chiến dịch.
                </td>
              </tr>
            @endforelse

            <tr id="khongCoNguonLucPhuHop" style="display: none;">
              <td colspan="6"
                  class="text-center text-muted py-4">
                Không có nguồn lực phù hợp với điều kiện tìm kiếm/lọc.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 flex-wrap">
    <button type="submit"
            class="btn btn-primary">
      Tạo chiến dịch
    </button>

    <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}"
       class="btn btn-secondary">
      Quay lại
    </a>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const ngayBatDauInput = document.getElementById('ngayBatDau');
    const ngayKetThucInput = document.getElementById('ngayKetThuc');
    const trangThaiSelect = document.getElementById('trangThaiChienDich');

    const suKienSearchInput = document.getElementById('suKienSearchInput');
    const idSuKienInput = document.getElementById('idSuKien');
    const suKienDropdown = document.getElementById('suKienDropdown');
    const suKienOptions = Array.from(
      document.querySelectorAll('.su-kien-option')
    );

    const timKiemNguonLucInput = document.getElementById('timKiemNguonLuc');
    const xoaLocNguonLucButton = document.getElementById('xoaLocNguonLuc');

    const nguonLucRows = Array.from(
      document.querySelectorAll('.nguon-luc-row')
    );

    const khongCoNguonLucPhuHop =
      document.getElementById('khongCoNguonLucPhuHop');

    const boLocNguonLuc = {
      danhMuc: '',
      donVi: '',
      phamVi: ''
    };

    function boDauTiengVietJS(value) {
      return value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');
    }

    function layNgayHomNay() {
      const homNay = new Date();
      homNay.setHours(0, 0, 0, 0);

      return homNay;
    }

    function chuyenNgay(value) {
      if (!value) {
        return null;
      }

      const ngay = new Date(value + 'T00:00:00');
      ngay.setHours(0, 0, 0, 0);

      return ngay;
    }

    function themTrangThai(value, text, selectedValue) {
      const option = document.createElement('option');

      option.value = value;
      option.textContent = text;

      if (selectedValue && selectedValue === value) {
        option.selected = true;
      }

      trangThaiSelect.appendChild(option);
    }

    function capNhatTrangThaiChienDich() {
      if (!trangThaiSelect) {
        return;
      }

      const oldValue = trangThaiSelect.dataset.oldValue || '';
      const homNay = layNgayHomNay();
      const ngayBatDau = chuyenNgay(ngayBatDauInput.value);
      const ngayKetThuc = chuyenNgay(ngayKetThucInput.value);

      trangThaiSelect.innerHTML = '';

      if (ngayBatDau && homNay < ngayBatDau) {
        themTrangThai('Sắp diễn ra', 'Sắp diễn ra', oldValue);
        themTrangThai('Đang diễn ra', 'Đang diễn ra', oldValue);

        if (!oldValue || !['Sắp diễn ra', 'Đang diễn ra'].includes(oldValue)) {
          trangThaiSelect.value = 'Sắp diễn ra';
        }

        return;
      }

      if (ngayKetThuc && homNay > ngayKetThuc) {
        themTrangThai('Đang diễn ra', 'Đang diễn ra', oldValue);
        themTrangThai('Hoàn thành', 'Hoàn thành', oldValue);

        if (!oldValue || !['Đang diễn ra', 'Hoàn thành'].includes(oldValue)) {
          trangThaiSelect.value = 'Đang diễn ra';
        }

        return;
      }

      themTrangThai('Đang diễn ra', 'Đang diễn ra', oldValue);

      if (!oldValue || oldValue !== 'Đang diễn ra') {
        trangThaiSelect.value = 'Đang diễn ra';
      }
    }

    function hienThiDropdownSuKien() {
      if (suKienDropdown) {
        suKienDropdown.classList.add('show');
      }
    }

    function anDropdownSuKien() {
      if (suKienDropdown) {
        suKienDropdown.classList.remove('show');
      }
    }

    function chonSuKien(option) {
      suKienSearchInput.value = option.dataset.label || '';
      idSuKienInput.value = option.dataset.id || '';
      anDropdownSuKien();
    }

    function locSuKienDropdown() {
      if (!suKienSearchInput || !suKienDropdown) {
        return;
      }

      const tuKhoa = boDauTiengVietJS(
        suKienSearchInput.value.trim()
      );

      let coKetQua = false;

      suKienOptions.forEach(function (option) {
        const noiDung = boDauTiengVietJS(
          option.dataset.label || option.textContent
        );

        const hienThi = tuKhoa === '' || noiDung.includes(tuKhoa);

        option.style.display = hienThi ? '' : 'none';

        if (hienThi) {
          coKetQua = true;
        }
      });

      if (coKetQua) {
        hienThiDropdownSuKien();
      } else {
        anDropdownSuKien();
      }

      idSuKienInput.value = '';
    }

    if (ngayBatDauInput) {
      ngayBatDauInput.addEventListener('change', capNhatTrangThaiChienDich);
    }

    if (ngayKetThucInput) {
      ngayKetThucInput.addEventListener('change', capNhatTrangThaiChienDich);
    }

    if (suKienSearchInput) {
      suKienSearchInput.addEventListener('focus', function () {
        locSuKienDropdown();
      });

      suKienSearchInput.addEventListener('input', function () {
        locSuKienDropdown();
      });

      suKienOptions.forEach(function (option) {
        option.addEventListener('mousedown', function (event) {
          event.preventDefault();
          chonSuKien(option);
        });
      });

      document.addEventListener('click', function (event) {
        if (
          suKienDropdown
          && !suKienSearchInput.contains(event.target)
          && !suKienDropdown.contains(event.target)
        ) {
          anDropdownSuKien();
        }
      });
    }

    const formTaoChienDich = document.querySelector('form');

    if (formTaoChienDich) {
      formTaoChienDich.addEventListener('submit', function (event) {
        if (!idSuKienInput.value) {
          event.preventDefault();
          alert('Vui lòng chọn một sự kiện cứu trợ hợp lệ trong danh sách gợi ý.');
          suKienSearchInput.focus();
        }
      });
    }

    document
      .querySelectorAll('.nguon-luc-checkbox')
      .forEach(function (checkbox) {
        const targetInput =
          document.getElementById(checkbox.dataset.target);

        function toggleSoLuong() {
          if (!targetInput) {
            return;
          }

          targetInput.disabled = !checkbox.checked;

          if (!checkbox.checked) {
            targetInput.value = '';
          } else {
            targetInput.focus();
          }
        }

        checkbox.addEventListener('change', toggleSoLuong);
      });

    function capNhatNutLocNguonLuc() {
      document
        .querySelectorAll('.nguon-luc-filter-option')
        .forEach(function (button) {
          const filterName = button.dataset.filter;
          const value = button.dataset.value || '';

          button.classList.toggle(
            'active',
            boLocNguonLuc[filterName] === value
          );
        });

      const dangLocDanhMuc = boLocNguonLuc.danhMuc !== '';
      const dangLocDonVi = boLocNguonLuc.donVi !== '';
      const dangLocPhamVi = boLocNguonLuc.phamVi !== '';

      document
        .getElementById('btnLocDanhMucNguonLuc')
        ?.classList.toggle('is-filtering', dangLocDanhMuc);

      document
        .getElementById('btnLocDonViNguonLuc')
        ?.classList.toggle('is-filtering', dangLocDonVi);

      document
        .getElementById('btnLocPhamViNguonLuc')
        ?.classList.toggle('is-filtering', dangLocPhamVi);

      const dangTimKiem =
        (timKiemNguonLucInput?.value.trim() || '') !== '';

      const dangCoLoc =
        dangTimKiem
        || dangLocDanhMuc
        || dangLocDonVi
        || dangLocPhamVi;

      if (xoaLocNguonLucButton) {
        xoaLocNguonLucButton.classList.toggle('d-none', !dangCoLoc);
      }
    }

    function locNguonLuc() {
      const tuKhoa = boDauTiengVietJS(
        timKiemNguonLucInput?.value.trim() || ''
      );

      let soDongHienThi = 0;

      nguonLucRows.forEach(function (row) {
        const noiDung = boDauTiengVietJS([
          row.dataset.ten || '',
          row.dataset.danhMuc || '',
          row.dataset.donVi || '',
          row.dataset.phamVi || ''
        ].join(' '));

        const hopTuKhoa =
          tuKhoa === '' || noiDung.includes(tuKhoa);

        const hopDanhMuc =
          boLocNguonLuc.danhMuc === ''
          || row.dataset.danhMuc === boLocNguonLuc.danhMuc;

        const hopDonVi =
          boLocNguonLuc.donVi === ''
          || row.dataset.donVi === boLocNguonLuc.donVi;

        const hopPhamVi =
          boLocNguonLuc.phamVi === ''
          || row.dataset.phamVi === boLocNguonLuc.phamVi;

        const hienThi =
          hopTuKhoa && hopDanhMuc && hopDonVi && hopPhamVi;

        row.style.display = hienThi ? '' : 'none';

        if (hienThi) {
          soDongHienThi++;
        }
      });

      if (khongCoNguonLucPhuHop) {
        khongCoNguonLucPhuHop.style.display =
          soDongHienThi === 0 ? '' : 'none';
      }

      capNhatNutLocNguonLuc();
    }

    document
      .querySelectorAll('.nguon-luc-filter-option')
      .forEach(function (button) {
        button.addEventListener('click', function () {
          const filterName = button.dataset.filter;
          const value = button.dataset.value || '';

          if (!filterName) {
            return;
          }

          boLocNguonLuc[filterName] = value;

          locNguonLuc();
        });
      });

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
        boLocNguonLuc.phamVi = '';

        locNguonLuc();
      });
    }

    document
      .querySelectorAll('.nguon-luc-checkbox')
      .forEach(function (checkbox) {
        const targetInput =
          document.getElementById(checkbox.dataset.target);

        function toggleSoLuong() {
          if (!targetInput) {
            return;
          }

          targetInput.disabled = !checkbox.checked;

          if (!checkbox.checked) {
            targetInput.value = '';
          } else {
            targetInput.focus();
          }
        }

        checkbox.addEventListener('change', toggleSoLuong);
      });

    locNguonLuc();

    if (window.feather) {
      feather.replace();
    }

    capNhatTrangThaiChienDich();
  });
</script>
@endsection