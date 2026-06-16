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
    <div class="table-responsive">
      <table class="table table-hover mb-0 yeu-cau-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Tiêu đề yêu cầu</th>
            <th style="width: 120px;">Số người</th>
            <th style="width: 150px;">Mức độ</th>
            <th style="width: 190px;">Trạng thái</th>
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
</script>
@endsection