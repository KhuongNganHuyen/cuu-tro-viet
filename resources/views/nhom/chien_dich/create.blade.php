@extends('layouts.nhom')

@section('title', 'Thêm chiến dịch | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #chienDichDiaDiemMap {
    height: 380px;
    width: 100%;
    border-radius: 12px;
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
    padding: 6px 0;
    margin-top: 0;
    border: 1px solid #dee2e6;
    border-radius: 6px;
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

  .filter-dropdown-menu::-webkit-scrollbar {
    width: 6px;
  }

  .filter-dropdown-menu::-webkit-scrollbar-track {
    background: #f1f3f5;
    border-radius: 999px;
  }

  .filter-dropdown-menu::-webkit-scrollbar-thumb {
    background: #adb5bd;
    border-radius: 999px;
  }

  .filter-dropdown-menu::-webkit-scrollbar-thumb:hover {
    background: #868e96;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm chiến dịch cứu trợ</h5>
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
            Thêm
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

<form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}"
      method="POST">
  @csrf

  <div class="card mb-3">
    <div class="card-header">
      <h5 class="mb-0">Thông tin chiến dịch</h5>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-lg-7">
          <div class="mb-3">
            <label class="form-label">
              Tên chiến dịch
              <span class="text-danger">*</span>
            </label>

            <input type="text"
                   name="tenChienDich"
                   class="form-control"
                   value="{{ old('tenChienDich') }}"
                   placeholder="Ví dụ: Cứu trợ mưa lũ Hòa Khánh, Tặng áo ấm vùng cao"
                   autocomplete="off">
          </div>

          <div class="mb-3">
            <label class="form-label">
              Sự kiện cứu trợ
              <span class="text-danger">*</span>
            </label>

            @php
              $suKienDangChon = $suKiens->firstWhere(
                  'idSuKien',
                  old('idSuKien')
              );

              $nhanSuKienDangChon = $suKienDangChon
                  ? $suKienDangChon->loaiSuKien . ' - ' . $suKienDangChon->tenSuKien
                  : '';
            @endphp

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
              Mô tả
            </label>

            <textarea name="moTa"
                      class="form-control"
                      rows="7"
                      placeholder="Mô tả mục tiêu, phạm vi hỗ trợ hoặc tình hình thực tế của chiến dịch">{{ old('moTa') }}</textarea>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="row">
            <div class="col-md-6 col-lg-12 mb-3">
              <label class="form-label">
                Ngày bắt đầu
              </label>

              <input type="date"
                     name="ngayBatDau"
                     id="ngayBatDau"
                     class="form-control"
                     value="{{ old('ngayBatDau') }}">
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
              Trạng thái
            </label>

            <select name="trangThai"
                    id="trangThaiChienDich"
                    class="form-control"
                    data-old-value="{{ old('trangThai') }}">
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
      <h5 class="mb-0">Địa điểm chiến dịch</h5>
    </div>

    <div class="card-body">
      <input type="hidden"
             name="idDiaDiemCoSan"
             id="idDiaDiemCoSan"
             value="{{ old('idDiaDiemCoSan') }}">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">
            Tỉnh/Thành
            <span class="text-danger">*</span>
          </label>

          <select name="tinhThanh"
                  id="tinhThanh"
                  class="form-control">
            <option value="">
              -- Chọn tỉnh/thành --
            </option>

            @foreach ($diaDiems->pluck('tinhThanh')->unique()->values() as $tinhThanh)
              <option value="{{ $tinhThanh }}"
                {{ old('tinhThanh') == $tinhThanh ? 'selected' : '' }}>
                {{ $tinhThanh }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">
            Phường/Xã
            <span class="text-danger">*</span>
          </label>

          <select name="phuongXa"
                  id="phuongXa"
                  class="form-control">
            <option value="">
              -- Chọn phường/xã --
            </option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">
          Địa chỉ chi tiết
          <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="chiTietDiaDiem"
               id="chiTietDiaDiem"
               class="form-control"
               list="danhSachDiaDiem"
               value="{{ old('chiTietDiaDiem') }}"
               placeholder="Gõ địa chỉ chi tiết hoặc chọn địa điểm đã có"
               autocomplete="off">

        <datalist id="danhSachDiaDiem"></datalist>
      </div>

      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label mb-0">
            Chọn vị trí trên bản đồ
            <span class="text-danger">*</span>
          </label>

          <button type="button"
                  id="btnTimTrenBanDo"
                  class="btn btn-sm btn-outline-primary">
            Tìm trên bản đồ
          </button>
        </div>

        <div id="chienDichDiaDiemMap"
             class="mt-2"></div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">
            Vĩ độ
          </label>

          <input type="text"
                 name="viDo"
                 id="viDo"
                 class="form-control"
                 value="{{ old('viDo') }}"
                 readonly>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">
            Kinh độ
          </label>

          <input type="text"
                 name="kinhDo"
                 id="kinhDo"
                 class="form-control"
                 value="{{ old('kinhDo') }}"
                 readonly>
        </div>
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

  <div class="d-flex gap-2">
    <button type="submit"
            class="btn btn-primary">
      Lưu
    </button>

    <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}"
       class="btn btn-secondary">
      Quay lại
    </a>
  </div>
</form>

<input type="hidden"
       id="oldPhuongXa"
       value="{{ old('phuongXa') }}">

<script id="diaDiemData" type="application/json">
{!! $diaDiemJson !!}
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const diaDiems = JSON.parse(document.getElementById('diaDiemData').textContent);

    const tinhThanhSelect = document.getElementById('tinhThanh');
    const phuongXaSelect = document.getElementById('phuongXa');
    const oldPhuongXa = document.getElementById('oldPhuongXa').value;

    const chiTietDiaDiemInput = document.getElementById('chiTietDiaDiem');
    const idDiaDiemCoSanInput = document.getElementById('idDiaDiemCoSan');
    const danhSachDiaDiem = document.getElementById('danhSachDiaDiem');

    const viDoInput = document.getElementById('viDo');
    const kinhDoInput = document.getElementById('kinhDo');
    const btnTimTrenBanDo = document.getElementById('btnTimTrenBanDo');

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

    const defaultLat = parseFloat(viDoInput.value) || 16.047079;
    const defaultLng = parseFloat(kinhDoInput.value) || 108.206230;

    const map = L.map('chienDichDiaDiemMap').setView(
      [defaultLat, defaultLng],
      12
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = null;

    function boDauTiengVietJS(value) {
      return value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');
    }

    function setLocation(lat, lng) {
      viDoInput.value = lat.toFixed(7);
      kinhDoInput.value = lng.toFixed(7);

      if (marker) {
        marker.setLatLng([lat, lng]);
      } else {
        marker = L.marker([lat, lng]).addTo(map);
      }

      map.setView([lat, lng], 16);
    }

    function loadPhuongXa() {
      const tinhThanh = tinhThanhSelect.value;

      phuongXaSelect.innerHTML =
        '<option value="">-- Chọn phường/xã --</option>';

      if (!tinhThanh) {
        return;
      }

      const phuongXas = [...new Set(
        diaDiems
          .filter(function (item) {
            return item.tinhThanh === tinhThanh && item.phuongXa;
          })
          .map(function (item) {
            return item.phuongXa;
          })
      )];

      phuongXas.forEach(function (phuongXa) {
        const option = document.createElement('option');
        option.value = phuongXa;
        option.textContent = phuongXa;

        if (oldPhuongXa && oldPhuongXa === phuongXa) {
          option.selected = true;
        }

        phuongXaSelect.appendChild(option);
      });

      loadDiaDiemOptions();
    }

    function loadDiaDiemOptions() {
      const tinhThanh = tinhThanhSelect.value;
      const phuongXa = phuongXaSelect.value;

      danhSachDiaDiem.innerHTML = '';

      const diaDiemPhuHop = diaDiems.filter(function (item) {
        return item.tinhThanh === tinhThanh
          && item.phuongXa === phuongXa
          && item.chiTietDiaDiem;
      });

      diaDiemPhuHop.forEach(function (diaDiem) {
        const option = document.createElement('option');
        option.value = diaDiem.chiTietDiaDiem;
        danhSachDiaDiem.appendChild(option);
      });
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

    tinhThanhSelect.addEventListener('change', function () {
      idDiaDiemCoSanInput.value = '';
      chiTietDiaDiemInput.value = '';
      loadPhuongXa();

      const diaDiemTheoTinh = diaDiems.find(function (item) {
        return item.tinhThanh === tinhThanhSelect.value
          && item.viDo
          && item.kinhDo;
      });

      if (diaDiemTheoTinh) {
        map.setView(
          [
            parseFloat(diaDiemTheoTinh.viDo),
            parseFloat(diaDiemTheoTinh.kinhDo)
          ],
          12
        );
      }
    });

    phuongXaSelect.addEventListener('change', function () {
      idDiaDiemCoSanInput.value = '';
      chiTietDiaDiemInput.value = '';
      loadDiaDiemOptions();

      const diaDiemTheoPhuong = diaDiems.find(function (item) {
        return item.tinhThanh === tinhThanhSelect.value
          && item.phuongXa === phuongXaSelect.value
          && item.viDo
          && item.kinhDo;
      });

      if (diaDiemTheoPhuong) {
        map.setView(
          [
            parseFloat(diaDiemTheoPhuong.viDo),
            parseFloat(diaDiemTheoPhuong.kinhDo)
          ],
          14
        );
      }
    });

    chiTietDiaDiemInput.addEventListener('input', function () {
      const diaDiem = diaDiems.find(function (item) {
        return item.tinhThanh === tinhThanhSelect.value
          && item.phuongXa === phuongXaSelect.value
          && item.chiTietDiaDiem === chiTietDiaDiemInput.value;
      });

      if (diaDiem) {
        idDiaDiemCoSanInput.value = diaDiem.idDiaDiem;

        if (diaDiem.viDo && diaDiem.kinhDo) {
          setLocation(
            parseFloat(diaDiem.viDo),
            parseFloat(diaDiem.kinhDo)
          );
        }
      } else {
        idDiaDiemCoSanInput.value = '';
      }
    });

    if (viDoInput.value && kinhDoInput.value) {
      setLocation(defaultLat, defaultLng);
    }

    map.on('click', function (e) {
      setLocation(e.latlng.lat, e.latlng.lng);
    });

    btnTimTrenBanDo.addEventListener('click', function () {
      const chiTiet = chiTietDiaDiemInput.value.trim();
      const phuongXa = phuongXaSelect.value.trim();
      const tinhThanh = tinhThanhSelect.value.trim();

      const diaChi = [chiTiet, phuongXa, tinhThanh, 'Việt Nam']
        .filter(Boolean)
        .join(', ');

      if (!tinhThanh && !phuongXa && !chiTiet) {
        alert('Vui lòng nhập ít nhất một thông tin địa chỉ trước khi tìm.');
        return;
      }

      fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(diaChi))
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (!data || data.length === 0) {
            alert('Không tìm thấy địa điểm phù hợp. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
            return;
          }

          const lat = parseFloat(data[0].lat);
          const lng = parseFloat(data[0].lon);

          setLocation(lat, lng);
        })
        .catch(function () {
          alert('Không thể tìm địa điểm lúc này. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
        });
    });

    ngayBatDauInput.addEventListener('change', capNhatTrangThaiChienDich);
    ngayKetThucInput.addEventListener('change', capNhatTrangThaiChienDich);

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

    loadPhuongXa();
    loadDiaDiemOptions();
    capNhatTrangThaiChienDich();
    locNguonLuc();

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

    if (window.feather) {
      feather.replace();
    }
  });
</script>
@endsection