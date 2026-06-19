@extends('layouts.user')

@section('title', 'Đăng ký đóng góp | Cứu Trợ Việt')

@section('content')
<style>
  .search-select-wrapper {
    position: relative;
  }

  .search-select-dropdown {
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

  .search-select-dropdown.show {
    display: block;
  }

  .search-select-option {
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f5;
    font-size: 14px;
  }

  .search-select-option:last-child {
    border-bottom: 0;
  }

  .search-select-option:hover {
    background-color: #f5f8ff;
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
          <h5 class="m-b-10">Đăng ký đóng góp</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/dong-gop') }}">Đóng góp của tôi</a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Đăng ký đóng góp
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@php
  $chienDichDangChon = $chienDichs->firstWhere(
      'idChienDich',
      old('idChienDich')
  );

  $tenChienDichDangChon = $chienDichDangChon
      ? $chienDichDangChon->tenChienDich
      : '';

  $thongTinNhomDangChon = '';

  if ($chienDichDangChon) {
      $tenNhom = $chienDichDangChon->nhom->tenNhom ?? '-';
      $nhomTruong = $chienDichDangChon->nhom->nhomTruong ?? null;
      $tenNhomTruong = $nhomTruong->hoTen ?? '-';
      $tenDangNhapNhomTruong = $nhomTruong->tenDangNhap ?? '-';

      $thongTinNhomDangChon =
          '<strong>' . e($tenNhom) . '</strong>'
          . ' của nhóm trưởng '
          . '<strong>' . e($tenNhomTruong . ' - ' . $tenDangNhapNhomTruong) . '</strong>';
  }
@endphp

<form action="{{ url('/user/dong-gop') }}"
      method="POST"
      onsubmit="return confirm('Bạn hãy kiểm tra kỹ thông tin trước khi gửi. Sau khi gửi đăng ký đóng góp, bạn sẽ không thể tự chỉnh sửa nội dung này. Bạn chắc chắn muốn gửi đăng ký đóng góp không?')">
  @csrf

  <div class="alert alert-warning">
    <strong>Lưu ý:</strong> Sau khi gửi đăng ký đóng góp, bạn sẽ không thể tự chỉnh sửa nội dung đã gửi.
    Vui lòng kiểm tra kỹ chiến dịch, hàng hóa, số lượng, hạn sử dụng và ghi chú trước khi bấm gửi.
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h5 class="mb-0">Thông tin đóng góp</h5>
    </div>

    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">
          Chiến dịch <span class="text-danger">*</span>
        </label>

        <input type="hidden"
               name="idChienDich"
               id="idChienDich"
               value="{{ old('idChienDich') }}">

        <div class="search-select-wrapper">
          <input type="text"
                 id="chienDichSearchInput"
                 class="form-control"
                 value="{{ $tenChienDichDangChon }}"
                 placeholder="Gõ tên chiến dịch để tìm..."
                 autocomplete="off">

          <div id="chienDichDropdown" class="search-select-dropdown">
            @foreach ($chienDichs as $chienDich)
              @php
                $nhom = $chienDich->nhom;
                $nhomTruong = $nhom->nhomTruong ?? null;

                $tenNhom = $nhom->tenNhom ?? '-';
                $tenNhomTruong = $nhomTruong->hoTen ?? '-';
                $tenDangNhapNhomTruong = $nhomTruong->tenDangNhap ?? '-';

                $nhanNhom =
                    $tenNhom
                    . ' của nhóm trưởng '
                    . $tenNhomTruong
                    . ' - '
                    . $tenDangNhapNhomTruong;
              @endphp

              <div class="search-select-option chien-dich-option"
                   data-id="{{ $chienDich->idChienDich }}"
                   data-label="{{ $chienDich->tenChienDich }}"
                   data-ten-nhom="{{ $tenNhom }}"
                   data-ten-nhom-truong="{{ $tenNhomTruong }}"
                   data-ten-dang-nhap-nhom-truong="{{ $tenDangNhapNhomTruong }}"
                   data-info="{{ $nhanNhom }}">
                <div class="fw-semibold">
                  {{ $chienDich->tenChienDich }}
                </div>

                <small class="text-muted">
                  <strong>{{ $tenNhom }}</strong>
                  của nhóm trưởng
                  <strong>{{ $tenNhomTruong }} - {{ $tenDangNhapNhomTruong }}</strong>
                </small>
              </div>
            @endforeach
          </div>
        </div>

        <small id="chienDichInfo"
               class="text-muted d-block mt-1">
          @if ($thongTinNhomDangChon !== '')
            {!! $thongTinNhomDangChon !!}
          @endif
        </small>
      </div>

      <div class="mb-3">
        <label class="form-label">Ghi chú</label>

        <textarea name="ghiChu"
                  class="form-control"
                  rows="3"
                  placeholder="Ghi chú">{{ old('ghiChu') }}</textarea>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center flex-wrap nguon-luc-card-header">
        <div>
          <h5 class="mb-1">Hàng hóa đóng góp</h5>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <div class="search-input-wrapper">
            <i data-feather="search" class="icon-search"></i>

            <input type="text"
                   id="timKiemNguonLuc"
                   class="form-control nguon-luc-search-box"
                   placeholder="Tìm kiếm hàng hóa...">
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

                  <ul id="dropdownLocDanhMucNguonLuc"
                      class="dropdown-menu filter-dropdown-menu">
                    <li>
                      <button type="button"
                              class="dropdown-item nguon-luc-filter-option active"
                              data-filter="danhMuc"
                              data-value="">
                        Tất cả danh mục
                      </button>
                    </li>
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

                  <ul id="dropdownLocDonViNguonLuc"
                      class="dropdown-menu filter-dropdown-menu">
                    <li>
                      <button type="button"
                              class="dropdown-item nguon-luc-filter-option active"
                              data-filter="donVi"
                              data-value="">
                        Tất cả đơn vị
                      </button>
                    </li>
                  </ul>
                </div>
              </th>

              <th style="width: 130px;">
                Cần kêu gọi
              </th>

              <th style="width: 170px;">
                Số lượng góp
              </th>

              <th style="width: 170px;">
                Hạn sử dụng
              </th>
            </tr>
          </thead>

          <tbody>
            @forelse ($nguonLucs as $nguonLuc)
              @php
                $hangHoa = $nguonLuc->hangHoa;
                $idHangHoa = $nguonLuc->idHangHoa;
                $idNguonLuc = $nguonLuc->idNguonLuc;

                $daChon = old("hangHoas.$idHangHoa.chon");
                $soLuongCu = old("hangHoas.$idHangHoa.soLuong");
                $hanSuDungCu = old("hangHoas.$idHangHoa.hanSuDung");

                $idSoLuong = 'soLuongDongGop_' . $idNguonLuc;
                $idHanSuDung = 'hanSuDung_' . $idNguonLuc;
              @endphp

              <tr class="nguon-luc-row"
                  data-id-chien-dich="{{ $nguonLuc->idChienDich }}"
                  data-ten="{{ mb_strtolower($hangHoa->tenHangHoa ?? '', 'UTF-8') }}"
                  data-danh-muc="{{ $hangHoa->danhMucHang->tenDanhMucHang ?? '' }}"
                  data-don-vi="{{ $hangHoa->donViTinh ?? '' }}">
                <td class="text-center">
                  <input type="checkbox"
                         class="form-check-input nguon-luc-checkbox"
                         name="hangHoas[{{ $idHangHoa }}][chon]"
                         value="1"
                         data-target="{{ $idSoLuong }}"
                         data-hsd-target="{{ $idHanSuDung }}"
                         {{ $daChon ? 'checked' : '' }}>
                </td>

                <td>
                  <div class="fw-semibold">
                    {{ $hangHoa->tenHangHoa ?? '-' }}
                  </div>
                </td>

                <td>
                  {{ $hangHoa->danhMucHang->tenDanhMucHang ?? '-' }}
                </td>

                <td class="text-center">
                  {{ $hangHoa->donViTinh ?? '-' }}
                </td>

                <td class="text-center">
                  {{ number_format($nguonLuc->soLuongConCan, 2) }}
                </td>

                <td>
                  <input type="number"
                         name="hangHoas[{{ $idHangHoa }}][soLuong]"
                         id="{{ $idSoLuong }}"
                         class="form-control nguon-luc-so-luong"
                         value="{{ $soLuongCu }}"
                         min="1"
                         step="0.01"
                         placeholder="Nhập số lượng"
                         {{ $daChon ? '' : 'disabled' }}>
                </td>

                <td>
                  <input type="date"
                         name="hangHoas[{{ $idHangHoa }}][hanSuDung]"
                         id="{{ $idHanSuDung }}"
                         class="form-control nguon-luc-han-su-dung"
                         value="{{ $hanSuDungCu }}"
                         {{ $daChon ? '' : 'disabled' }}>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7"
                    class="text-center text-muted py-4">
                  Chưa có mặt hàng nào đang được kêu gọi.
                </td>
              </tr>
            @endforelse

            <tr id="khongCoNguonLucPhuHop" style="display: none;">
              <td colspan="7"
                  class="text-center text-muted py-4">
                Không có mặt hàng phù hợp với chiến dịch hoặc điều kiện tìm kiếm/lọc.
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
      Gửi đăng ký
    </button>

    <a href="{{ url('/user/dong-gop') }}"
       class="btn btn-secondary">
      Quay lại
    </a>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const idChienDichInput = document.getElementById('idChienDich');
    const chienDichSearchInput = document.getElementById('chienDichSearchInput');
    const chienDichDropdown = document.getElementById('chienDichDropdown');
    const chienDichInfo = document.getElementById('chienDichInfo');

    const chienDichOptions = Array.from(
      document.querySelectorAll('.chien-dich-option')
    );

    const timKiemNguonLucInput = document.getElementById('timKiemNguonLuc');
    const xoaLocNguonLucButton = document.getElementById('xoaLocNguonLuc');
    const chonChienDichThongBao = document.getElementById('chonChienDichThongBao');

    const nguonLucRows = Array.from(
      document.querySelectorAll('.nguon-luc-row')
    );

    const khongCoNguonLucPhuHop =
      document.getElementById('khongCoNguonLucPhuHop');

    const boLocNguonLuc = {
      danhMuc: '',
      donVi: ''
    };

    function boDauTiengVietJS(value) {
      return (value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');
    }

    function taoThongTinNhom(option) {
      return '<strong>' + option.dataset.tenNhom + '</strong>'
        + ' của nhóm trưởng '
        + '<strong>'
        + option.dataset.tenNhomTruong
        + ' - '
        + option.dataset.tenDangNhapNhomTruong
        + '</strong>';
    }

    function resetNguonLucDaChon() {
      document
        .querySelectorAll('.nguon-luc-checkbox')
        .forEach(function (checkbox) {
          checkbox.checked = false;

          const soLuongInput = document.getElementById(checkbox.dataset.target);
          const hanSuDungInput = document.getElementById(checkbox.dataset.hsdTarget);

          if (soLuongInput) {
            soLuongInput.value = '';
            soLuongInput.disabled = true;
            soLuongInput.required = false;
          }

          if (hanSuDungInput) {
            hanSuDungInput.value = '';
            hanSuDungInput.disabled = true;
          }
        });
    }

    function locChienDich() {
      const tuKhoa = boDauTiengVietJS(
        chienDichSearchInput.value.trim()
      );

      let coKetQua = false;

      chienDichOptions.forEach(function (option) {
        const noiDung = boDauTiengVietJS(
          option.dataset.label + ' ' + option.dataset.info
        );

        const hienThi =
          tuKhoa === '' || noiDung.includes(tuKhoa);

        option.style.display = hienThi ? '' : 'none';

        if (hienThi) {
          coKetQua = true;
        }
      });

      chienDichDropdown.classList.toggle('show', coKetQua);
    }

    function boChonChienDich() {
      idChienDichInput.value = '';
      chienDichInfo.textContent =
        'Chọn chiến dịch để hiển thị thông tin nhóm tiếp nhận.';

      resetNguonLucDaChon();
      locNguonLuc();
    }

    function chonChienDich(option) {
      idChienDichInput.value = option.dataset.id;
      chienDichSearchInput.value = option.dataset.label;
      chienDichInfo.innerHTML = taoThongTinNhom(option);
      chienDichDropdown.classList.remove('show');

      resetNguonLucDaChon();
      locNguonLuc();
    }

    function layGiaTriLocTheoChienDich(tenDataset) {
      const idChienDich = Number(idChienDichInput.value);
      const giaTris = [];

      nguonLucRows.forEach(function (row) {
        const dungChienDich =
          idChienDich
          && Number(row.dataset.idChienDich) === idChienDich;

        if (!dungChienDich) {
          return;
        }

        const giaTri = row.dataset[tenDataset] || '';

        if (giaTri !== '' && !giaTris.includes(giaTri)) {
          giaTris.push(giaTri);
        }
      });

      return giaTris.sort(function (a, b) {
        return a.localeCompare(b, 'vi');
      });
    }

    function taoNutLocNguonLuc(filterName, value, label) {
      const li = document.createElement('li');
      const button = document.createElement('button');

      button.type = 'button';
      button.className = 'dropdown-item nguon-luc-filter-option';
      button.dataset.filter = filterName;
      button.dataset.value = value;
      button.textContent = label;

      button.addEventListener('click', function () {
        boLocNguonLuc[filterName] = value;
        locNguonLuc();
      });

      li.appendChild(button);

      return li;
    }

    function capNhatDanhSachLocNguonLuc() {
      const dropdownDanhMuc =
        document.getElementById('dropdownLocDanhMucNguonLuc');

      const dropdownDonVi =
        document.getElementById('dropdownLocDonViNguonLuc');

      if (!dropdownDanhMuc || !dropdownDonVi) {
        return;
      }

      const danhMucs = layGiaTriLocTheoChienDich('danhMuc');
      const donVis = layGiaTriLocTheoChienDich('donVi');

      if (
        boLocNguonLuc.danhMuc !== ''
        && !danhMucs.includes(boLocNguonLuc.danhMuc)
      ) {
        boLocNguonLuc.danhMuc = '';
      }

      if (
        boLocNguonLuc.donVi !== ''
        && !donVis.includes(boLocNguonLuc.donVi)
      ) {
        boLocNguonLuc.donVi = '';
      }

      dropdownDanhMuc.innerHTML = '';
      dropdownDonVi.innerHTML = '';

      dropdownDanhMuc.appendChild(
        taoNutLocNguonLuc('danhMuc', '', 'Tất cả danh mục')
      );

      danhMucs.forEach(function (danhMuc) {
        dropdownDanhMuc.appendChild(
          taoNutLocNguonLuc('danhMuc', danhMuc, danhMuc)
        );
      });

      dropdownDonVi.appendChild(
        taoNutLocNguonLuc('donVi', '', 'Tất cả đơn vị')
      );

      donVis.forEach(function (donVi) {
        dropdownDonVi.appendChild(
          taoNutLocNguonLuc('donVi', donVi, donVi)
        );
      });
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
      const dangTimKiem =
        (timKiemNguonLucInput?.value.trim() || '') !== '';

      document
        .getElementById('btnLocDanhMucNguonLuc')
        ?.classList.toggle('is-filtering', dangLocDanhMuc);

      document
        .getElementById('btnLocDonViNguonLuc')
        ?.classList.toggle('is-filtering', dangLocDonVi);

      const dangCoLoc =
        dangTimKiem || dangLocDanhMuc || dangLocDonVi;

      if (xoaLocNguonLucButton) {
        xoaLocNguonLucButton.classList.toggle('d-none', !dangCoLoc);
      }
    }

    function locNguonLuc() {
      capNhatDanhSachLocNguonLuc();

      const idChienDich = Number(idChienDichInput.value);
      const tuKhoa = boDauTiengVietJS(
        timKiemNguonLucInput?.value.trim() || ''
      );

      let soDongHienThi = 0;

      nguonLucRows.forEach(function (row) {
        const dungChienDich =
          idChienDich
          && Number(row.dataset.idChienDich) === idChienDich;

        const noiDung = boDauTiengVietJS([
          row.dataset.ten || '',
          row.dataset.danhMuc || '',
          row.dataset.donVi || ''
        ].join(' '));

        const hopTuKhoa =
          tuKhoa === '' || noiDung.includes(tuKhoa);

        const hopDanhMuc =
          boLocNguonLuc.danhMuc === ''
          || row.dataset.danhMuc === boLocNguonLuc.danhMuc;

        const hopDonVi =
          boLocNguonLuc.donVi === ''
          || row.dataset.donVi === boLocNguonLuc.donVi;

        const hienThi =
          dungChienDich && hopTuKhoa && hopDanhMuc && hopDonVi;

        row.style.display = hienThi ? '' : 'none';

        if (hienThi) {
          soDongHienThi++;
        }
      });

      if (chonChienDichThongBao) {
        chonChienDichThongBao.style.display =
          idChienDich ? 'none' : '';
      }

      if (khongCoNguonLucPhuHop) {
        khongCoNguonLucPhuHop.style.display =
          idChienDich && soDongHienThi === 0 ? '' : 'none';
      }

      capNhatNutLocNguonLuc();
    }

    chienDichSearchInput.addEventListener('focus', function () {
      locChienDich();
    });

    chienDichSearchInput.addEventListener('input', function () {
      boChonChienDich();
      locChienDich();
    });

    chienDichOptions.forEach(function (option) {
      option.addEventListener('mousedown', function (event) {
        event.preventDefault();
        chonChienDich(option);
      });
    });

    document.addEventListener('click', function (event) {
      if (
        !chienDichSearchInput.contains(event.target)
        && !chienDichDropdown.contains(event.target)
      ) {
        chienDichDropdown.classList.remove('show');
      }
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

        locNguonLuc();
      });
    }

    document
      .querySelectorAll('.nguon-luc-checkbox')
      .forEach(function (checkbox) {
        const soLuongInput = document.getElementById(checkbox.dataset.target);
        const hanSuDungInput = document.getElementById(checkbox.dataset.hsdTarget);

        function toggleDongGopInput() {
          if (soLuongInput) {
            soLuongInput.disabled = !checkbox.checked;
            soLuongInput.required = checkbox.checked;

            if (!checkbox.checked) {
              soLuongInput.value = '';
            } else {
              soLuongInput.focus();
            }
          }

          if (hanSuDungInput) {
            hanSuDungInput.disabled = !checkbox.checked;

            if (!checkbox.checked) {
              hanSuDungInput.value = '';
            }
          }
        }

        checkbox.addEventListener('change', toggleDongGopInput);
        toggleDongGopInput();
      });

    locNguonLuc();

    if (window.feather) {
      feather.replace();
    }
  });
</script>
@endsection