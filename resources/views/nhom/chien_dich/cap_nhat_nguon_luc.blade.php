@extends('layouts.nhom')

@section('title', 'Cập nhật nguồn lực chiến dịch | Cứu Trợ Việt')

@section('content')
<style>
  .nguon-luc-table th,
  .nguon-luc-table td {
    vertical-align: middle;
  }

  .nguon-luc-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  .nguon-luc-row-muted {
    background-color: #f8f9fa;
  }

  .nguon-luc-status {
    min-width: 150px;
  }

  .nguon-luc-quantity {
    width: 110px;
    min-width: 110px;
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
    min-width: 280px;
    max-width: 420px;
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

  .resource-search-wrapper input {
    padding-right: 42px;
  }

  .resource-search-reset.show {
    display: inline-flex;
  }

  .selected-label {
    font-size: 12px;
    color: #198754;
    font-weight: 600;
  }

  .not-selected-label {
    font-size: 12px;
    color: #6c757d;
  }

  .sticky-action-bar {
    position: sticky;
    bottom: 0;
    z-index: 10;
    padding: 14px 0;
    background-color: #fff;
    border-top: 1px solid #e9ecef;
  }
</style>

@php
  $danhSachTrangThai = [
      'Đang kêu gọi',
      'Đủ số lượng',
      'Đã đóng',
  ];
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Cập nhật nguồn lực chiến dịch</h5>
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
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}">
              Chi tiết chiến dịch
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Cập nhật nguồn lực
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

@if ($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại thông tin.</div>

    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card">
  <form method="POST"
        action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/nguon-luc/cap-nhat') }}">
    @csrf
    @method('PUT')

    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
        <div>
          <h5 class="mb-1">Cập nhật nguồn lực</h5>
          <small class="text-muted">
            Chiến dịch:
            <strong class="text-body">{{ $chienDich->tenChienDich }}</strong>
          </small>
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

      <div class="table-responsive">
        <table class="table table-hover nguon-luc-table mb-0">
          <thead>
            <tr class="text-uppercase text-center">
              <th style="width: 70px;">Chọn</th>

              <th class="text-start">Hàng hóa</th>

              <th class="filter-heading-cell" style="width: 160px;">
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

              <th class="filter-heading-cell" style="width: 120px;">
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

              <th class="filter-heading-cell" style="width: 130px;">
                <div class="dropdown">
                  <button type="button"
                          class="filter-heading-button"
                          id="btnLocPhamViNguonLuc"
                          data-bs-toggle="dropdown"
                          aria-expanded="false">
                    Phạm vi
                    <span class="filter-active-dot"></span>
                  </button>

                  <ul class="dropdown-menu filter-dropdown-menu"
                      aria-labelledby="btnLocPhamViNguonLuc"
                      id="menuLocPhamViNguonLuc">
                    <li>
                      <button type="button"
                              class="dropdown-item nguon-luc-filter-option active"
                              data-filter-type="phamVi"
                              data-filter-value="">
                        Tất cả phạm vi
                      </button>
                    </li>
                  </ul>
                </div>
              </th>

              <th style="width: 120px;">Cần kêu gọi</th>
              <th style="width: 120px;">Đã nhận</th>
              <th style="width: 120px;">Hiện còn</th>
              <th style="width: 170px;">Trạng thái</th>
            </tr>
          </thead>

          <tbody>
            @forelse ($hangHoas as $hangHoa)
              @php
                $nguonLuc = $nguonLucTheoHangHoa->get($hangHoa->idHangHoa);

                $dangChon = old(
                    'nguonLuc.' . $hangHoa->idHangHoa . '.chon',
                    $nguonLuc ? 1 : 0
                );

                $soLuongCanKeuGoi = old(
                    'nguonLuc.' . $hangHoa->idHangHoa . '.soLuongCanKeuGoi',
                    $nguonLuc->soLuongCanKeuGoi ?? ''
                );

                $trangThai = old(
                    'nguonLuc.' . $hangHoa->idHangHoa . '.trangThai',
                    $nguonLuc->trangThai ?? 'Đang kêu gọi'
                );

                $laHangCuaNhom = (int) $hangHoa->idNhom === (int) $nhom->idNhom;

                $noiDungTimKiem = mb_strtolower(
                    implode(' ', [
                        $hangHoa->tenHangHoa,
                        $hangHoa->danhMucHang->tenDanhMucHang ?? '',
                        $hangHoa->donViTinh ?? '',
                        $laHangCuaNhom ? 'của nhóm' : 'hệ thống',
                    ]),
                    'UTF-8'
                );
              @endphp

              <tr class="nguon-luc-row {{ $dangChon ? '' : 'nguon-luc-row-muted' }}"
                  data-search="{{ $noiDungTimKiem }}"
                  data-danh-muc="{{ $hangHoa->danhMucHang->tenDanhMucHang ?? '' }}"
                  data-don-vi="{{ $hangHoa->donViTinh ?? '' }}"
                  data-pham-vi="{{ $laHangCuaNhom ? 'Của nhóm' : 'Hệ thống' }}">
                <td class="text-center">
                  <input type="checkbox"
                         name="nguonLuc[{{ $hangHoa->idHangHoa }}][chon]"
                         value="1"
                         class="form-check-input nguon-luc-checkbox"
                         data-resource-checkbox
                         {{ $dangChon ? 'checked' : '' }}>
                </td>

                <td>
                  <div class="fw-semibold">
                    {{ $hangHoa->tenHangHoa }}
                  </div>
                </td>

                <td class="text-center">
                  {{ $hangHoa->danhMucHang->tenDanhMucHang ?? '-' }}
                </td>

                <td class="text-center">
                  {{ $hangHoa->donViTinh ?? '-' }}
                </td>

                <td class="text-center text-muted">
                  {{ $laHangCuaNhom ? 'Của nhóm' : 'Hệ thống' }}
                </td>

                <td>
                  <input type="number"
                         name="nguonLuc[{{ $hangHoa->idHangHoa }}][soLuongCanKeuGoi]"
                         class="form-control nguon-luc-quantity"
                         min="1"
                         step="1"
                         value="{{ $soLuongCanKeuGoi }}"
                         placeholder="0"
                         data-resource-input>
                </td>

                <td class="text-center">
                  {{ $nguonLuc->soLuongDaNhan ?? 0 }}
                </td>

                <td class="text-center">
                  {{ $nguonLuc->soLuongHienCo ?? 0 }}
                </td>

                <td>
                  <select name="nguonLuc[{{ $hangHoa->idHangHoa }}][trangThai]"
                          class="form-select nguon-luc-status"
                          data-resource-input>
                    @foreach ($danhSachTrangThai as $itemTrangThai)
                      <option value="{{ $itemTrangThai }}"
                              {{ $trangThai === $itemTrangThai ? 'selected' : '' }}>
                        {{ $itemTrangThai }}
                      </option>
                    @endforeach
                  </select>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9"
                    class="text-center text-muted py-4">
                  Chưa có hàng hóa nào để chọn làm nguồn lực chiến dịch.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="sticky-action-bar mt-3">
        <div class="d-flex justify-content-end gap-2">
          <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '#nguon-luc') }}"
             class="btn btn-secondary">
            Hủy
          </a>

          <button type="submit"
                  class="btn btn-primary">
            Lưu cập nhật
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.nguon-luc-row');
    const searchInput = document.getElementById('timKiemNguonLuc');
    const resetButton = document.getElementById('xoaLocNguonLuc');

    const boLoc = {
      danhMuc: '',
      donVi: '',
      phamVi: '',
    };

    const cauHinhLoc = {
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
      phamVi: {
        menuId: 'menuLocPhamViNguonLuc',
        buttonId: 'btnLocPhamViNguonLuc',
        dataKey: 'phamVi',
        allText: 'Tất cả phạm vi',
      },
    };

    function capNhatTrangThaiDong(row) {
      const checkbox = row.querySelector('[data-resource-checkbox]');
      const inputs = row.querySelectorAll('[data-resource-input]');

      if (!checkbox) {
        return;
      }

      const dangChon = checkbox.checked;

      row.classList.toggle('nguon-luc-row-muted', !dangChon);

      inputs.forEach(function (input) {
        input.disabled = !dangChon;
      });
    }

    function layDanhSachGiaTri(type) {
      const key = cauHinhLoc[type].dataKey;
      const values = new Set();

      rows.forEach(function (row) {
        const value = row.dataset[key] || '';

        if (value.trim() !== '') {
          values.add(value.trim());
        }
      });

      return Array.from(values).sort(function (a, b) {
        return a.localeCompare(b, 'vi');
      });
    }

    function taoMenuLoc(type) {
      const config = cauHinhLoc[type];
      const menu = document.getElementById(config.menuId);

      if (!menu) {
        return;
      }

      const values = layDanhSachGiaTri(type);

      values.forEach(function (value) {
        const li = document.createElement('li');
        const button = document.createElement('button');

        button.type = 'button';
        button.className = 'dropdown-item nguon-luc-filter-option';
        button.dataset.filterType = type;
        button.dataset.filterValue = value;
        button.textContent = value;

        li.appendChild(button);
        menu.appendChild(li);
      });
    }

    function capNhatNutReset() {
      const coTimKiem = searchInput && searchInput.value.trim() !== '';
      const coLoc = boLoc.danhMuc || boLoc.donVi || boLoc.phamVi;

      if (resetButton) {
        resetButton.classList.toggle('d-none', !(coTimKiem || coLoc));
      }
    }

    function capNhatTrangThaiNutLoc() {
      Object.keys(cauHinhLoc).forEach(function (type) {
        const button = document.getElementById(cauHinhLoc[type].buttonId);

        if (button) {
          button.classList.toggle('is-filtering', !!boLoc[type]);
        }
      });
    }

    function locNguonLuc() {
      const keyword = searchInput
        ? searchInput.value.trim().toLowerCase()
        : '';

      rows.forEach(function (row) {
        const searchContent = row.getAttribute('data-search') || '';
        const matchKeyword = searchContent.includes(keyword);

        const matchDanhMuc =
          !boLoc.danhMuc || row.dataset.danhMuc === boLoc.danhMuc;

        const matchDonVi =
          !boLoc.donVi || row.dataset.donVi === boLoc.donVi;

        const matchPhamVi =
          !boLoc.phamVi || row.dataset.phamVi === boLoc.phamVi;

        row.style.display =
          matchKeyword && matchDanhMuc && matchDonVi && matchPhamVi
            ? ''
            : 'none';
      });

      capNhatNutReset();
      capNhatTrangThaiNutLoc();
    }

    rows.forEach(function (row) {
      const checkbox = row.querySelector('[data-resource-checkbox]');

      capNhatTrangThaiDong(row);

      if (checkbox) {
        checkbox.addEventListener('change', function () {
          capNhatTrangThaiDong(row);
        });
      }
    });

    Object.keys(cauHinhLoc).forEach(function (type) {
      taoMenuLoc(type);
    });

    document.querySelectorAll('.nguon-luc-filter-option').forEach(function (button) {
      button.addEventListener('click', function () {
        const type = button.dataset.filterType;
        const value = button.dataset.filterValue || '';

        boLoc[type] = value;

        document
          .querySelectorAll('[data-filter-type="' + type + '"]')
          .forEach(function (item) {
            item.classList.toggle('active', item === button);
          });

        locNguonLuc();
      });
    });

    if (searchInput) {
      searchInput.addEventListener('input', locNguonLuc);
    }

    if (resetButton) {
      resetButton.addEventListener('click', function () {
        if (searchInput) {
          searchInput.value = '';
        }

        boLoc.danhMuc = '';
        boLoc.donVi = '';
        boLoc.phamVi = '';

        document.querySelectorAll('.nguon-luc-filter-option').forEach(function (button) {
          const value = button.dataset.filterValue || '';
          button.classList.toggle('active', value === '');
        });

        locNguonLuc();
      });
    }

    if (window.feather) {
      feather.replace();
    }

    locNguonLuc();
  });
</script>
@endsection