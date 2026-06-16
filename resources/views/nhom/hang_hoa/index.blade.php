@extends('layouts.nhom')

@section('title', 'Hàng hóa của nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Hàng hóa</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">
              Nhóm của tôi
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              {{ $nhom->tenNhom }}
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Hàng hóa
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

@if (request('tuKhoa'))
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      Đang tìm kiếm:
      <strong>{{ request('tuKhoa') }}</strong>
    </div>

    <a href="{{ url('/nhom/' . $nhom->idNhom . '/hang-hoa'
        . ($idDanhMucDangChon
            ? '?idDanhMucHang=' . $idDanhMucDangChon
            : '')) }}"
       class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

@php
  $cotDangLoc = [];

  if ($idDanhMucDangChon) {
      $cotDangLoc[] = 'Danh mục';
  }

  if ($donViTinhDangChon !== '') {
      $cotDangLoc[] = 'Đơn vị tính';
  }

  if ($phamViDangChon !== '') {
      $cotDangLoc[] = 'Phạm vi';
  }

  if ($trangThaiDangChon !== '') {
      $cotDangLoc[] = 'Trạng thái';
  }

  $dangLoc = count($cotDangLoc) > 0;

  $noiDungCotDangLoc = implode(', ', $cotDangLoc);

  $urlXoaBoLoc = url(
      '/nhom/' . $nhom->idNhom . '/hang-hoa'
  );

  if (request('tuKhoa')) {
      $urlXoaBoLoc .= '?tuKhoa='
          . urlencode(request('tuKhoa'));
  }
@endphp

@if ($dangLoc)
  <div class="alert alert-info filter-alert">
    <div class="filter-alert-content">
      <span>Đang lọc:</span>

      <strong class="filter-alert-text">
        {{ $noiDungCotDangLoc }}
      </strong>
    </div>

    <a href="{{ $urlXoaBoLoc }}"
       class="btn btn-sm btn-light filter-clear-button">
      Xóa bộ lọc
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <h5 class="mb-1">Danh sách hàng hóa</h5>

        <small class="text-muted">
          Tổng hiển thị: {{ $hangHoas->count() }}
        </small>
      </div>

      @if ($laNhomTruong)
        <a href="{{ url('/nhom/' . $nhom->idNhom . '/hang-hoa/create') }}"
           class="btn btn-primary">
          Thêm hàng hóa
        </a>
      @endif
    </div>
  </div>

  <div class="card-body">
    <form id="boLocHangHoaForm"
          action="{{ url('/nhom/' . $nhom->idNhom . '/hang-hoa') }}"
          method="GET"
          autocomplete="off">

      <input type="hidden"
            name="tuKhoa"
            value="{{ request('tuKhoa') }}">

      <input type="hidden"
            name="idDanhMucHang"
            id="filterIdDanhMucHang"
            value="{{ $idDanhMucDangChon }}">

      <input type="hidden"
            name="donViTinh"
            id="filterDonViTinh"
            value="{{ $donViTinhDangChon }}">

      <input type="hidden"
            name="phamVi"
            id="filterPhamVi"
            value="{{ $phamViDangChon }}">

      <input type="hidden"
            name="trangThai"
            id="filterTrangThai"
            value="{{ $trangThaiDangChon }}">
    </form>

    <div class="table-responsive hang-hoa-table-wrapper">
      <table class="table table-hover mb-0 hang-hoa-table">
      <thead>
        <tr class="text-uppercase text-center">
          <th style="width: 80px;">
            Mã
          </th>

          <th class="text-start">
            Tên hàng hóa
          </th>

          {{-- Lọc danh mục --}}
          <th class="filter-heading-cell text-start">
            <div class="dropdown w-100 h-100">
              <button type="button"
                      class="filter-heading-button text-start"
                      data-bs-toggle="dropdown"
                      data-bs-boundary="viewport"
                      aria-expanded="false">
                <span>Danh mục</span>

                @if ($idDanhMucDangChon)
                  <span class="filter-active-dot"></span>
                @endif
              </button>

              <ul class="dropdown-menu filter-dropdown-menu">
                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ !$idDanhMucDangChon ? 'active' : '' }}"
                          data-target="filterIdDanhMucHang"
                          data-value="">
                    Tất cả danh mục
                  </button>
                </li>

                @foreach ($danhMucHangs as $danhMucHang)
                  <li>
                    <button type="button"
                            class="dropdown-item filter-option
                              {{ (string) $idDanhMucDangChon === (string) $danhMucHang->idDanhMucHang
                                  ? 'active'
                                  : '' }}"
                            data-target="filterIdDanhMucHang"
                            data-value="{{ $danhMucHang->idDanhMucHang }}">
                      {{ $danhMucHang->tenDanhMucHang }}
                    </button>
                  </li>
                @endforeach
              </ul>
            </div>
          </th>

          {{-- Lọc đơn vị tính --}}
          <th class="filter-heading-cell" style="width: 150px;">
            <div class="dropdown w-100 h-100">
              <button type="button"
                      class="filter-heading-button text-center"
                      data-bs-toggle="dropdown"
                      data-bs-boundary="viewport"
                      aria-expanded="false">
                <span>Đơn vị tính</span>

                @if ($donViTinhDangChon !== '')
                  <span class="filter-active-dot"></span>
                @endif
              </button>

              <ul class="dropdown-menu filter-dropdown-menu">
                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $donViTinhDangChon === '' ? 'active' : '' }}"
                          data-target="filterDonViTinh"
                          data-value="">
                    Tất cả đơn vị
                  </button>
                </li>

                @foreach ($donViTinhs as $donViTinh)
                  <li>
                    <button type="button"
                            class="dropdown-item filter-option
                              {{ $donViTinhDangChon === $donViTinh ? 'active' : '' }}"
                            data-target="filterDonViTinh"
                            data-value="{{ $donViTinh }}">
                      {{ $donViTinh }}
                    </button>
                  </li>
                @endforeach
              </ul>
            </div>
          </th>

          {{-- Lọc phạm vi --}}
          <th class="filter-heading-cell" style="width: 140px;">
            <div class="dropdown w-100 h-100">
              <button type="button"
                      class="filter-heading-button text-center"
                      data-bs-toggle="dropdown"
                      data-bs-boundary="viewport"
                      aria-expanded="false">
                <span>Phạm vi</span>

                @if ($phamViDangChon !== '')
                  <span class="filter-active-dot"></span>
                @endif
              </button>

              <ul class="dropdown-menu filter-dropdown-menu">
                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $phamViDangChon === '' ? 'active' : '' }}"
                          data-target="filterPhamVi"
                          data-value="">
                    Tất cả
                  </button>
                </li>

                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $phamViDangChon === 'he-thong' ? 'active' : '' }}"
                          data-target="filterPhamVi"
                          data-value="he-thong">
                    Hệ thống
                  </button>
                </li>

                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $phamViDangChon === 'nhom' ? 'active' : '' }}"
                          data-target="filterPhamVi"
                          data-value="nhom">
                    Của nhóm
                  </button>
                </li>
              </ul>
            </div>
          </th>

          {{-- Lọc trạng thái --}}
          <th class="filter-heading-cell" style="width: 170px;">
            <div class="dropdown w-100 h-100">
              <button type="button"
                      class="filter-heading-button text-center"
                      data-bs-toggle="dropdown"
                      data-bs-boundary="viewport"
                      aria-expanded="false">
                <span>Trạng thái</span>

                @if ($trangThaiDangChon !== '')
                  <span class="filter-active-dot"></span>
                @endif
              </button>

              <ul class="dropdown-menu filter-dropdown-menu">
                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $trangThaiDangChon === '' ? 'active' : '' }}"
                          data-target="filterTrangThai"
                          data-value="">
                    Tất cả trạng thái
                  </button>
                </li>

                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $trangThaiDangChon === 'Đang sử dụng' ? 'active' : '' }}"
                          data-target="filterTrangThai"
                          data-value="Đang sử dụng">
                    Đang sử dụng
                  </button>
                </li>

                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $trangThaiDangChon === 'Ngừng sử dụng' ? 'active' : '' }}"
                          data-target="filterTrangThai"
                          data-value="Ngừng sử dụng">
                    Ngừng sử dụng
                  </button>
                </li>
              </ul>
            </div>
          </th>

          @if ($laNhomTruong)
            <th style="width: 110px;"></th>
          @endif
        </tr>
      </thead>

        <tbody>
          @forelse ($hangHoas as $hangHoa)
            @php
              $laHangHeThong = is_null($hangHoa->idNhom);
              $laHangCuaNhom = (int) $hangHoa->idNhom === (int) $nhom->idNhom;
              $dangSuDung = $hangHoa->trangThai === 'Đang sử dụng';
            @endphp

            <tr class="
              {{ $laHangHeThong ? 'hang-he-thong-row' : '' }}
              {{ session('hangHoaMoi') == $hangHoa->idHangHoa ? 'table-primary' : '' }}
            ">
              <td class="text-center fw-semibold">
                {{ $hangHoa->idHangHoa }}
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
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
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

              <td class="text-center">
                @if ($dangSuDung)
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="status-dot status-active"></span>
                    <span>Đang sử dụng</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="status-dot status-stopped"></span>
                    <span>Ngừng sử dụng</span>
                  </span>
                @endif
              </td>

              @if ($laNhomTruong)
                <td class="text-center">
                  @if ($laHangCuaNhom)
                    <div class="d-inline-flex gap-1">
                      <a href="{{ url('/nhom/' . $nhom->idNhom
                          . '/hang-hoa/' . $hangHoa->idHangHoa . '/edit') }}"
                         class="btn btn-sm btn-light border action-button"
                         title="Sửa">
                        <i class="ti ti-edit"></i>
                      </a>

                      <form action="{{ url('/nhom/' . $nhom->idNhom
                              . '/hang-hoa/' . $hangHoa->idHangHoa
                              . '/doi-trang-thai') }}"
                            method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái hàng hóa này không?')">
                        @csrf
                        @method('PATCH')

                        @if ($dangSuDung)
                          <button type="submit"
                                  class="btn btn-sm btn-light border text-danger action-button"
                                  title="Ngừng sử dụng">
                            <i class="ti ti-lock"></i>
                          </button>
                        @else
                          <button type="submit"
                                  class="btn btn-sm btn-light border text-success action-button"
                                  title="Mở sử dụng">
                            <i class="ti ti-lock-open"></i>
                          </button>
                        @endif
                      </form>
                    </div>
                  @else
                    <span class="text-muted"
                          title="Hàng hóa hệ thống không thể sửa">
                      —
                    </span>
                  @endif
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ $laNhomTruong ? 7 : 6 }}"
                  class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy hàng hóa phù hợp.
                @elseif ($idDanhMucDangChon)
                  Danh mục này chưa có hàng hóa phù hợp với nhóm.
                @else
                  Chưa có hàng hóa nào.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .hang-hoa-table-wrapper {
    overflow: visible;
  }

  .hang-hoa-table {
    width: 100%;
  }

  .hang-hoa-table th,
  .hang-hoa-table td {
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
    justify-content: inherit;
    gap: 6px;
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
    display: inline-block;
    flex-shrink: 0;
    border-radius: 50%;
    background-color: #0d6efd;
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

  .hang-he-thong-row {
    background-color: #ffffff;
    color: #8a9199;
  }

  .hang-he-thong-row:hover {
    background-color: #f8f9fa;
  }

  .hang-he-thong-row .fw-semibold {
    color: #adb5bd;
  }

  .hang-he-thong-row td:last-child {
    color: #adb5bd;
  }

  .status-dot,
  .scope-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    flex-shrink: 0;
    border-radius: 50%;
  }

  .status-active {
    background-color: #198754;
  }

  .status-stopped {
    background-color: #212529;
  }

  .scope-system {
    background-color: #6c757d;
  }

  .scope-group {
    background-color: #198754;
  }

  .action-button {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  @media (max-width: 991.98px) {
    .hang-hoa-table-wrapper {
      overflow-x: auto;
      overflow-y: visible;
    }

    .hang-hoa-table {
      min-width: 1050px;
    }
  }

  .filter-alert {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
  }

  .filter-alert-content {
    display: flex;
    align-items: center;
    gap: 4px;
    min-width: 0;
    flex: 1;
  }

  .filter-alert-text {
    display: block;
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }

  .filter-clear-button {
    flex-shrink: 0;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const filterForm =
      document.getElementById('boLocHangHoaForm');

    document
      .querySelectorAll('.filter-option')
      .forEach(function (button) {
        button.addEventListener('click', function () {
          const targetId = button.dataset.target;
          const value = button.dataset.value ?? '';

          const targetInput =
            document.getElementById(targetId);

          if (!targetInput || !filterForm) {
            return;
          }

          targetInput.value = value;

          /*
           * Loại bỏ các input rỗng khỏi URL,
           * tránh tạo query dạng ?phamVi=&trangThai=
           */
          filterForm
            .querySelectorAll('input[type="hidden"]')
            .forEach(function (input) {
              if (input.value.trim() === '') {
                input.disabled = true;
              }
            });

          filterForm.submit();
        });
      });
  });
</script>
@endsection