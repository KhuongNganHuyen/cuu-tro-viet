@extends('layouts.user')

@section('title', 'Nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Nhóm tình nguyện</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Nhóm tình nguyện
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if (request('tuKhoa'))
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      Đang tìm kiếm:
      <strong>{{ request('tuKhoa') }}</strong>
    </div>

    <a href="{{ url('/user/nhom-tinh-nguyen') }}" class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-1">Danh sách nhóm tình nguyện</h5>

      <small class="text-muted">
        Tổng hiển thị: {{ $nhomTinhNguyens->count() }}
      </small>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 nhom-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 80px;">Mã</th>
            <th class="text-start">Tên nhóm</th>
            <th class="text-start">Nhóm trưởng</th>
            <th class="text-start">Địa điểm</th>
            <th style="width: 170px;">Trạng thái</th>
            <th style="width: 160px;">Ngày tạo</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($nhomTinhNguyens as $nhom)
            @php
              $trangThaiNhom = $nhom->trangThai;

              $classTrangThai = match ($trangThaiNhom) {
                  'Đang hoạt động', 'Hoạt động' => 'status-active',
                  'Tạm ngừng hoạt động' => 'status-paused',
                  'Ngừng hoạt động' => 'status-stopped',
                  'Bị khóa' => 'status-locked',
                  default => 'status-stopped',
              };

              $tenTrangThaiHienThi = $trangThaiNhom == 'Hoạt động'
                  ? 'Đang hoạt động'
                  : $trangThaiNhom;

              $diaChi = '-';

              if ($nhom->diaDiem) {
                  $diaChiParts = array_filter([
                      $nhom->diaDiem->chiTietDiaDiem ?? null,
                      $nhom->diaDiem->phuongXa ?? null,
                      $nhom->diaDiem->tinhThanh ?? null,
                  ]);

                  $diaChi = count($diaChiParts) > 0
                      ? implode(', ', $diaChiParts)
                      : '-';
              }
            @endphp

            <tr class="clickable-row"
                data-href="{{ url('/user/nhom-tinh-nguyen/' . $nhom->idNhom) }}">
              <td class="text-center fw-semibold">
                {{ $nhom->idNhom }}
              </td>

              <td>
                <div class="fw-semibold">{{ $nhom->tenNhom }}</div>

                @if (!empty($nhom->moTa))
                  <small class="text-muted">
                    {{ \Illuminate\Support\Str::limit($nhom->moTa, 70) }}
                  </small>
                @endif
              </td>

              <td>
                {{ $nhom->nhomTruong->hoTen ?? '-' }}
              </td>

              <td>
                {{ $diaChi }}
              </td>

              <td class="text-center">
                <span class="d-inline-flex align-items-center justify-content-center gap-2">
                  <span class="rounded-circle status-dot {{ $classTrangThai }} d-inline-block"></span>
                  <span>{{ $tenTrangThaiHienThi }}</span>
                </span>
              </td>

              <td class="text-center">
                {{ $nhom->ngayTao ?? '-' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy nhóm tình nguyện phù hợp.
                @else
                  Chưa có nhóm tình nguyện nào.
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
  .nhom-table th,
  .nhom-table td {
    vertical-align: middle;
  }

  .clickable-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .clickable-row:hover {
    background-color: #f5f7fb;
  }

  .status-dot {
    width: 8px;
    height: 8px;
  }

  .status-active {
    background-color: #198754;
  }

  .status-paused {
    background-color: #fd7e14;
  }

  .status-stopped {
    background-color: #6c757d;
  }

  .status-locked {
    background-color: #212529;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.clickable-row').forEach(function (row) {
      row.addEventListener('click', function (event) {
        if (event.target.closest('a, button, form, input, select')) {
          return;
        }

        const href = row.dataset.href;

        if (href) {
          window.location.href = href;
        }
      });
    });
  });
</script>
@endsection