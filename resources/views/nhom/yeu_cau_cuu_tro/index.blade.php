@extends('layouts.nhom')

@section('title', 'Yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              Tổng quan nhóm
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Yêu cầu cứu trợ
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

    <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro'
        . (
            request('mucDoKhanCap') || request('trangThai') || request('tinhThanh')
              ? '?' . http_build_query(array_filter([
                  'mucDoKhanCap' => request('mucDoKhanCap'),
                  'trangThai' => request('trangThai'),
                  'tinhThanh' => request('tinhThanh'),
                ]))
              : ''
        )) }}"
       class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

@php
  $cotDangLoc = [];

  if ($mucDoDangChon !== '') {
      $cotDangLoc[] = 'Mức độ';
  }

  if ($trangThaiDangChon !== '') {
      $cotDangLoc[] = 'Trạng thái';
  }

  if ($tinhThanhDangChon !== '') {
      $cotDangLoc[] = 'Tỉnh/thành';
  }

  $dangLoc = count($cotDangLoc) > 0;

  $noiDungCotDangLoc = implode(', ', $cotDangLoc);

  $urlXoaBoLoc = url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro');

  if (request('tuKhoa')) {
      $urlXoaBoLoc .= '?tuKhoa=' . urlencode(request('tuKhoa'));
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
        <h5 class="mb-1">Danh sách yêu cầu cứu trợ</h5>

        <small class="text-muted">
          Tổng hiển thị: {{ $yeuCausChoTiepNhan->count() + $yeuCausDaTiepNhan->count() }}
        </small>
      </div>
    </div>
  </div>

  <div class="card-body">
    <form id="boLocYeuCauForm"
          action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}"
          method="GET"
          autocomplete="off">

      <input type="hidden"
             name="tuKhoa"
             value="{{ request('tuKhoa') }}">

      <input type="hidden"
             name="mucDoKhanCap"
             id="filterMucDoKhanCap"
             value="{{ $mucDoDangChon }}">

      <input type="hidden"
             name="trangThai"
             id="filterTrangThai"
             value="{{ $trangThaiDangChon }}">

      <input type="hidden"
             name="tinhThanh"
             id="filterTinhThanh"
             value="{{ $tinhThanhDangChon }}">
    </form>

    <ul class="nav nav-tabs mb-3" id="yeuCauTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active"
                id="da-tiep-nhan-tab"
                data-bs-toggle="tab"
                data-bs-target="#da-tiep-nhan"
                type="button"
                role="tab">
          Nhóm đã tiếp nhận ({{ $yeuCausDaTiepNhan->count() }})
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="cho-tiep-nhan-tab"
                data-bs-toggle="tab"
                data-bs-target="#cho-tiep-nhan"
                type="button"
                role="tab">
          Chưa có tiếp nhận ({{ $yeuCausChoTiepNhan->count() }})
        </button>
      </li>
    </ul>

    <div class="tab-content" id="yeuCauTabsContent">

      {{-- TAB 1 --}}
      <div class="tab-pane fade show active" id="da-tiep-nhan" role="tabpanel">
        <div class="table-responsive yeu-cau-table-wrapper">
          <table class="table table-hover mb-0 yeu-cau-table">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 80px;">Mã</th>

                <th class="text-start">Thông tin yêu cầu</th>

                <th style="width: 200px;">Chiến dịch</th>

                <th class="filter-heading-cell" style="width: 150px;">
                  <div class="dropdown w-100 h-100">
                    <button type="button"
                            class="filter-heading-button text-center"
                            data-bs-toggle="dropdown"
                            data-bs-boundary="viewport"
                            aria-expanded="false">
                      <span>Mức độ</span>

                      @if ($mucDoDangChon !== '')
                        <span class="filter-active-dot"></span>
                      @endif
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu">
                      <li>
                        <button type="button"
                                class="dropdown-item filter-option {{ $mucDoDangChon === '' ? 'active' : '' }}"
                                data-target="filterMucDoKhanCap"
                                data-value="">
                          Tất cả mức độ
                        </button>
                      </li>

                      @foreach (['Khẩn cấp', 'Cao', 'Trung bình', 'Thấp'] as $mucDo)
                        <li>
                          <button type="button"
                                  class="dropdown-item filter-option {{ $mucDoDangChon === $mucDo ? 'active' : '' }}"
                                  data-target="filterMucDoKhanCap"
                                  data-value="{{ $mucDo }}">
                            {{ $mucDo }}
                          </button>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </th>

                <th class="filter-heading-cell" style="width: 180px;">
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

                      @foreach (['Chờ tiếp nhận', 'Đã tiếp nhận', 'Cần thêm hỗ trợ', 'Hoàn thành', 'Đã hủy'] as $trangThai)
                        <li>
                          <button type="button"
                                  class="dropdown-item filter-option {{ $trangThaiDangChon === $trangThai ? 'active' : '' }}"
                                  data-target="filterTrangThai"
                                  data-value="{{ $trangThai }}">
                            {{ $trangThai }}
                          </button>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </th>

                <th class="filter-heading-cell" style="width: 170px;">
                  <div class="dropdown w-100 h-100">
                    <button type="button"
                            class="filter-heading-button text-center"
                            data-bs-toggle="dropdown"
                            data-bs-boundary="viewport"
                            aria-expanded="false">
                      <span>Tỉnh/thành</span>

                      @if ($tinhThanhDangChon !== '')
                        <span class="filter-active-dot"></span>
                      @endif
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu">
                      <li>
                        <button type="button"
                                class="dropdown-item filter-option {{ $tinhThanhDangChon === '' ? 'active' : '' }}"
                                data-target="filterTinhThanh"
                                data-value="">
                          Tất cả tỉnh/thành
                        </button>
                      </li>

                      @foreach ($danhSachTinhThanh as $tinhThanh)
                        <li>
                          <button type="button"
                                  class="dropdown-item filter-option {{ $tinhThanhDangChon === $tinhThanh ? 'active' : '' }}"
                                  data-target="filterTinhThanh"
                                  data-value="{{ $tinhThanh }}">
                            {{ $tinhThanh }}
                          </button>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </th>

                <th style="width: 175px;">Thời gian gửi</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($yeuCausDaTiepNhan as $yeuCau)
                @include('nhom.yeu_cau_cuu_tro.partials.row_yeu_cau', [
                    'yeuCau' => $yeuCau,
                    'nhom' => $nhom,
                    'hienThiChienDich' => true,
                ])
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    @if (request('tuKhoa') || $dangLoc)
                      Không tìm thấy yêu cầu cứu trợ phù hợp.
                    @else
                      Nhóm chưa tiếp nhận yêu cầu cứu trợ nào.
                    @endif
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- TAB 2 --}}
      <div class="tab-pane fade" id="cho-tiep-nhan" role="tabpanel">
        <div class="table-responsive yeu-cau-table-wrapper">
          <table class="table table-hover mb-0 yeu-cau-table">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 80px;">Mã</th>

                <th class="text-start">Thông tin yêu cầu</th>

                <th style="width: 120px;">Số người</th>

                <th class="filter-heading-cell" style="width: 150px;">
                  <div class="dropdown w-100 h-100">
                    <button type="button"
                            class="filter-heading-button text-center"
                            data-bs-toggle="dropdown"
                            data-bs-boundary="viewport"
                            aria-expanded="false">
                      <span>Mức độ</span>

                      @if ($mucDoDangChon !== '')
                        <span class="filter-active-dot"></span>
                      @endif
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu">
                      <li>
                        <button type="button"
                                class="dropdown-item filter-option {{ $mucDoDangChon === '' ? 'active' : '' }}"
                                data-target="filterMucDoKhanCap"
                                data-value="">
                          Tất cả mức độ
                        </button>
                      </li>

                      @foreach (['Khẩn cấp', 'Cao', 'Trung bình', 'Thấp'] as $mucDo)
                        <li>
                          <button type="button"
                                  class="dropdown-item filter-option {{ $mucDoDangChon === $mucDo ? 'active' : '' }}"
                                  data-target="filterMucDoKhanCap"
                                  data-value="{{ $mucDo }}">
                            {{ $mucDo }}
                          </button>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </th>

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

                      @foreach (['Chờ tiếp nhận', 'Đã tiếp nhận', 'Cần thêm hỗ trợ', 'Hoàn thành', 'Đã hủy'] as $trangThai)
                        <li>
                          <button type="button"
                                  class="dropdown-item filter-option {{ $trangThaiDangChon === $trangThai ? 'active' : '' }}"
                                  data-target="filterTrangThai"
                                  data-value="{{ $trangThai }}">
                            {{ $trangThai }}
                          </button>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </th>

                <th class="filter-heading-cell" style="width: 170px;">
                  <div class="dropdown w-100 h-100">
                    <button type="button"
                            class="filter-heading-button text-center"
                            data-bs-toggle="dropdown"
                            data-bs-boundary="viewport"
                            aria-expanded="false">
                      <span>Tỉnh/thành</span>

                      @if ($tinhThanhDangChon !== '')
                        <span class="filter-active-dot"></span>
                      @endif
                    </button>

                    <ul class="dropdown-menu filter-dropdown-menu">
                      <li>
                        <button type="button"
                                class="dropdown-item filter-option {{ $tinhThanhDangChon === '' ? 'active' : '' }}"
                                data-target="filterTinhThanh"
                                data-value="">
                          Tất cả tỉnh/thành
                        </button>
                      </li>

                      @foreach ($danhSachTinhThanh as $tinhThanh)
                        <li>
                          <button type="button"
                                  class="dropdown-item filter-option {{ $tinhThanhDangChon === $tinhThanh ? 'active' : '' }}"
                                  data-target="filterTinhThanh"
                                  data-value="{{ $tinhThanh }}">
                            {{ $tinhThanh }}
                          </button>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </th>

                <th style="width: 175px;">Thời gian gửi</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($yeuCausChoTiepNhan as $yeuCau)
                @include('nhom.yeu_cau_cuu_tro.partials.row_yeu_cau', [
                    'yeuCau' => $yeuCau,
                    'nhom' => $nhom,
                    'hienThiChienDich' => false,
                ])
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    @if (request('tuKhoa') || $dangLoc)
                      Không tìm thấy yêu cầu cứu trợ phù hợp.
                    @else
                      Hiện chưa có yêu cầu cứu trợ nào đang chờ tiếp nhận.
                    @endif
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
  .yeu-cau-table-wrapper {
    overflow: visible;
  }

  .yeu-cau-table {
    width: 100%;
  }

  .yeu-cau-table th,
  .yeu-cau-table td {
    vertical-align: middle;
  }

  .clickable-row {
    cursor: pointer;
  }

  .clickable-row:hover {
    background-color: #f8f9fa;
  }

  .request-title {
    color: #212529;
    font-weight: 600;
  }

  .request-meta {
    color: #6c757d;
    font-size: 13px;
    line-height: 1.45;
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

  @media (max-width: 991.98px) {
    .yeu-cau-table-wrapper {
      overflow-x: auto;
      overflow-y: visible;
    }

    .yeu-cau-table {
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
    const filterForm = document.getElementById('boLocYeuCauForm');

    document
      .querySelectorAll('.filter-option')
      .forEach(function (button) {
        button.addEventListener('click', function () {
          const targetId = button.dataset.target;
          const value = button.dataset.value ?? '';

          const targetInput = document.getElementById(targetId);

          if (!targetInput || !filterForm) {
            return;
          }

          targetInput.value = value;

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

    document.querySelectorAll('.clickable-row').forEach(function (row) {
      row.addEventListener('click', function (event) {
        if (event.target.closest('a, button, form, input, select')) {
          return;
        }

        const href = row.getAttribute('data-href');

        if (href) {
          window.location.href = href;
        }
      });
    });
  });
</script>
@endsection