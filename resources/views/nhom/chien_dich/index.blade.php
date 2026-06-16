@extends('layouts.nhom')

@section('title', 'Chiến dịch của nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">
            Chiến dịch của nhóm
          </h5>
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
            Chiến dịch
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
  <div class="alert alert-info d-flex justify-content-between align-items-center gap-3">
    <div class="text-truncate">
      Đang tìm kiếm:
      <strong>{{ request('tuKhoa') }}</strong>
    </div>

    <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}"
       class="btn btn-sm btn-light flex-shrink-0">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">
        Danh sách chiến dịch
      </h5>

      <small class="text-muted">
        Tổng hiển thị: {{ $chienDichs->count() }}
      </small>
    </div>

    @if ($laNhomTruong)
      <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/create') }}"
         class="btn btn-primary">
        Thêm chiến dịch
      </a>
    @endif
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 chien-dich-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 70px;">
              Mã
            </th>

            <th class="text-start" style="width: 24%;">
              Tên chiến dịch
            </th>

            <th class="text-start" style="width: 18%;">
              Sự kiện cứu trợ
            </th>

            <th class="text-start" style="width: 20%;">
              Địa điểm
            </th>

            <th style="width: 110px;">
              Bắt đầu
            </th>

            <th style="width: 110px;">
              Kết thúc
            </th>

            <th style="width: 130px;">
              Xác nhận
            </th>

            <th style="width: 140px;">
              Trạng thái
            </th>
          </tr>
        </thead>

        <tbody>
          @forelse ($chienDichs as $chienDich)
            <tr class="clickable-row"
                data-href="{{ url(
                    '/nhom/' . $nhom->idNhom
                    . '/chien-dich/'
                    . $chienDich->idChienDich
                ) }}">

              <td class="text-center fw-semibold">
                {{ $chienDich->idChienDich }}
              </td>

              <td class="ten-chien-dich-cell">
                <div class="ten-chien-dich">
                  {{ $chienDich->tenChienDich }}
                </div>

                <div class="text-muted mo-ta-chien-dich">
                  {{ $chienDich->moTa
                      ? \Illuminate\Support\Str::limit(
                          $chienDich->moTa,
                          90
                        )
                      : 'Chưa có mô tả' }}
                </div>
              </td>

              <td class="su-kien-cell">
                @if ($chienDich->suKien)
                  <div class="fw-semibold su-kien-name">
                    {{ $chienDich->suKien->tenSuKien }}
                  </div>

                  <small class="text-muted">
                    {{ $chienDich->suKien->loaiSuKien ?? '-' }}
                  </small>
                @else
                  -
                @endif
              </td>

              <td class="dia-diem-cell">
                @if ($chienDich->diaDiem)
                  <div class="dia-diem-text">
                    @if (!empty($chienDich->diaDiem->chiTietDiaDiem))
                      {{ $chienDich->diaDiem->chiTietDiaDiem }},
                    @endif

                    @if (!empty($chienDich->diaDiem->phuongXa))
                      {{ $chienDich->diaDiem->phuongXa }},
                    @endif

                    {{ $chienDich->diaDiem->tinhThanh ?? '-' }}
                  </div>
                @else
                  -
                @endif
              </td>

              <td class="text-center">
                @if ($chienDich->ngayBatDau)
                  {{ \Carbon\Carbon::parse(
                      $chienDich->ngayBatDau
                    )->format('d/m/Y') }}
                @else
                  -
                @endif
              </td>

              <td class="text-center">
                @if ($chienDich->ngayKetThuc)
                  {{ \Carbon\Carbon::parse(
                      $chienDich->ngayKetThuc
                    )->format('d/m/Y') }}
                @else
                  -
                @endif
              </td>

              <td class="text-center">
                @if ($chienDich->daXacNhanCuuTro)
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="status-dot status-confirmed"></span>
                    <span>Đã xác nhận</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="status-dot status-unconfirmed"></span>
                    <span>Chưa xác nhận</span>
                  </span>
                @endif
              </td>

              <td class="text-center">
                @php
                  $classTrangThai = match ($chienDich->trangThai) {
                      'Đang hoạt động' => 'status-active',
                      'Hoàn thành' => 'status-completed',
                      'Tạm ngưng' => 'status-paused',
                      default => 'status-default',
                  };
                @endphp

                <span class="d-inline-flex align-items-center justify-content-center gap-2">
                  <span class="status-dot {{ $classTrangThai }}"></span>

                  <span>
                    {{ $chienDich->trangThai ?? '-' }}
                  </span>
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8"
                  class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy chiến dịch phù hợp.
                @else
                  Nhóm này chưa có chiến dịch cứu trợ nào.
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
  .chien-dich-table {
    table-layout: fixed;
    width: 100%;
  }

  .chien-dich-table th,
  .chien-dich-table td {
    vertical-align: middle;
  }

  .clickable-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .clickable-row:hover {
    background-color: #f5f7fb;
  }

  .ten-chien-dich-cell,
  .su-kien-cell,
  .dia-diem-cell {
    min-width: 0;
  }

  .ten-chien-dich,
  .mo-ta-chien-dich,
  .su-kien-name,
  .dia-diem-text {
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .ten-chien-dich {
    font-weight: 600;
  }

  .mo-ta-chien-dich {
    font-size: 13px;
    line-height: 1.5;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    flex-shrink: 0;
    border-radius: 50%;
  }

  .status-confirmed,
  .status-active {
    background-color: #198754;
  }

  .status-unconfirmed,
  .status-paused {
    background-color: #ffc107;
  }

  .status-completed {
    background-color: #0d6efd;
  }

  .status-default {
    background-color: #6c757d;
  }

  @media (max-width: 1199.98px) {
    .chien-dich-table {
      min-width: 1100px;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document
      .querySelectorAll('.clickable-row')
      .forEach(function (row) {
        row.addEventListener('click', function () {
          const href = row.dataset.href;

          if (href) {
            window.location.href = href;
          }
        });
      });
  });
</script>
@endsection