@extends('layouts.user')

@section('title', 'Yêu cầu cứu trợ của tôi | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Yêu cầu cứu trợ của tôi</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
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

@php
  $cotDangLoc = [];

  if ($mucDoDangChon !== '') {
      $cotDangLoc[] = 'Mức độ';
  }

  if ($trangThaiDangChon !== '') {
      $cotDangLoc[] = 'Trạng thái';
  }

  $dangLoc = count($cotDangLoc) > 0;
  $noiDungCotDangLoc = implode(', ', $cotDangLoc);
@endphp

@if (request('tuKhoa'))
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      Đang tìm kiếm:
      <strong>{{ request('tuKhoa') }}</strong>
    </div>

    <a href="{{ url('/user/yeu-cau-cuu-tro') }}"
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

    <a href="{{ url('/user/yeu-cau-cuu-tro' . (request('tuKhoa') ? '?tuKhoa=' . urlencode(request('tuKhoa')) : '')) }}"
       class="btn btn-sm btn-light">
      Xóa bộ lọc
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">Danh sách yêu cầu cứu trợ</h5>

      <small class="text-muted">
        Tổng hiển thị: {{ $yeuCaus->count() }}
      </small>
    </div>

    <a href="{{ url('/user/yeu-cau-cuu-tro/create') }}"
       class="btn btn-primary">
      Gửi yêu cầu
    </a>
  </div>

  <div class="card-body">
    <form id="boLocYeuCauForm"
          action="{{ url('/user/yeu-cau-cuu-tro') }}"
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
    </form>

    <div class="table-responsive">
      <table class="table table-hover mb-0 yeu-cau-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Tiêu đề yêu cầu</th>
            <th style="width: 120px;">Số người</th>
            <th class="filter-heading-cell" style="width: 150px;">
              <div class="dropdown">
                <button class="filter-heading-button dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown">
                  Mức độ
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
                      Tất cả
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

            <th class="filter-heading-cell" style="width: 190px;">
              <div class="dropdown">
                <button class="filter-heading-button dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown">
                  Trạng thái
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
                      Tất cả
                    </button>
                  </li>

                  @foreach (['Chờ tiếp nhận', 'Đã tiếp nhận', 'Đang hỗ trợ', 'Cần thêm hỗ trợ', 'Hoàn thành', 'Đã hủy'] as $trangThaiLoc)
                    <li>
                      <button type="button"
                              class="dropdown-item filter-option {{ $trangThaiDangChon === $trangThaiLoc ? 'active' : '' }}"
                              data-target="filterTrangThai"
                              data-value="{{ $trangThaiLoc }}">
                        {{ $trangThaiLoc }}
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
          @forelse ($yeuCaus as $yeuCau)
            @php
              $trangThai = $yeuCau->trangThai;

              $classTrangThai = match ($trangThai) {
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

              $diaChi = collect([
                  $yeuCau->diaDiem->chiTietDiaDiem ?? null,
                  $yeuCau->diaDiem->phuongXa ?? null,
                  $yeuCau->diaDiem->tinhThanh ?? null,
              ])->filter()->implode(', ');
            @endphp

            <tr class="clickable-row"
                data-href="{{ url('/user/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}">

              <td class="text-center fw-semibold">
                {{ $yeuCau->idYeuCau }}
              </td>

              <td>
                <div class="fw-semibold">
                  {{ $yeuCau->tieuDeYeuCau }}
                </div>

                <small class="text-muted">
                  {{ $diaChi !== '' ? $diaChi : 'Chưa có địa điểm' }}
                </small>
              </td>

              <td class="text-center">
                {{ $yeuCau->soNguoi ?? '-' }}
              </td>

              <td class="text-center">
                <span class="d-inline-flex align-items-center gap-2">
                  <span class="status-dot {{ $classMucDo }}"></span>
                  {{ $yeuCau->mucDoKhanCap ?? '-' }}
                </span>
              </td>

              <td class="text-center">
                <span class="d-inline-flex align-items-center gap-2">
                  <span class="status-dot {{ $classTrangThai }}"></span>
                  {{ $trangThai }}
                </span>
              </td>

              <td class="text-center">
                @if ($yeuCau->thoiGianGui)
                  {{ \Carbon\Carbon::parse($yeuCau->thoiGianGui)->format('d/m/Y H:i') }}
                @else
                  -
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy yêu cầu cứu trợ phù hợp.
                @else
                  Bạn chưa gửi yêu cầu cứu trợ nào.
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
  .yeu-cau-table th,
  .yeu-cau-table td {
    vertical-align: middle;
  }

  .clickable-row {
    cursor: pointer;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    border-radius: 50%;
    flex-shrink: 0;
  }

  /* Trạng thái yêu cầu */
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

  /* Mức độ khẩn cấp */
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
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
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
</script>
@endsection