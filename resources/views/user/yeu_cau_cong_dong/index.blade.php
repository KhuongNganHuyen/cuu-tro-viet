@extends('layouts.user')

@section('title', 'Yêu cầu cộng đồng | Cứu Trợ Việt')

@section('content')
<style>
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
    display: inline-block;
    border-radius: 50%;
    background-color: #0d6efd;
  }

  .filter-dropdown-menu {
    min-width: 210px;
    max-width: 320px;
    max-height: 260px;
    overflow-y: auto;
    padding: 6px 0;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    z-index: 1080;
  }

  .filter-dropdown-menu .dropdown-item {
    padding: 8px 14px;
    font-size: 14px;
    white-space: normal;
  }

  .filter-dropdown-menu .dropdown-item.active {
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
    .yeu-cau-table {
      min-width: 1050px;
    }
  }
</style>

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
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Yêu cầu cộng đồng</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Yêu cầu cộng đồng
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

    <a href="{{ url('/user/yeu-cau-cong-dong') }}"
       class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

@if ($dangLoc)
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      Đang lọc:
      <strong>{{ $noiDungCotDangLoc }}</strong>
    </div>

    <a href="{{ url('/user/yeu-cau-cong-dong' . (request('tuKhoa') ? '?tuKhoa=' . urlencode(request('tuKhoa')) : '')) }}"
       class="btn btn-sm btn-light">
      Xóa bộ lọc
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <h5 class="mb-1">Danh sách yêu cầu cứu trợ cộng đồng</h5>

        <small class="text-muted">
          Tổng hiển thị: {{ $yeuCausChuaTiepNhan->count() + $yeuCausDaTiepNhan->count() }}
        </small>
      </div>
    </div>
  </div>

  <div class="card-body">
    <form id="boLocYeuCauForm"
          action="{{ url('/user/yeu-cau-cong-dong') }}"
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
                id="chua-tiep-nhan-tab"
                data-bs-toggle="tab"
                data-bs-target="#chua-tiep-nhan"
                type="button"
                role="tab">
          Chưa tiếp nhận ({{ $yeuCausChuaTiepNhan->count() }})
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="da-tiep-nhan-tab"
                data-bs-toggle="tab"
                data-bs-target="#da-tiep-nhan"
                type="button"
                role="tab">
          Đã tiếp nhận ({{ $yeuCausDaTiepNhan->count() }})
        </button>
      </li>
    </ul>

    <div class="tab-content" id="yeuCauTabsContent">
      <div class="tab-pane fade show active" id="chua-tiep-nhan" role="tabpanel">
        @include('user.yeu_cau_cong_dong.partials.bang_yeu_cau', [
            'yeuCaus' => $yeuCausChuaTiepNhan,
            'hienThiNhom' => false,
            'danhSachTinhThanh' => $danhSachTinhThanh,
            'mucDoDangChon' => $mucDoDangChon,
            'trangThaiDangChon' => $trangThaiDangChon,
            'tinhThanhDangChon' => $tinhThanhDangChon,
            'dangLoc' => $dangLoc,
        ])
      </div>

      <div class="tab-pane fade" id="da-tiep-nhan" role="tabpanel">
        @include('user.yeu_cau_cong_dong.partials.bang_yeu_cau', [
            'yeuCaus' => $yeuCausDaTiepNhan,
            'hienThiNhom' => true,
            'danhSachTinhThanh' => $danhSachTinhThanh,
            'mucDoDangChon' => $mucDoDangChon,
            'trangThaiDangChon' => $trangThaiDangChon,
            'tinhThanhDangChon' => $tinhThanhDangChon,
            'dangLoc' => $dangLoc,
        ])
      </div>
    </div>
  </div>
</div>

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