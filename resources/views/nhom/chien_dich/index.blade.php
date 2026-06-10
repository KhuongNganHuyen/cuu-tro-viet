@extends('layouts.nhom')

@section('title', 'Chiến dịch của nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chiến dịch của nhóm</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Chiến dịch</li>
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

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Danh sách chiến dịch</h5>
      <small class="text-muted">
        Theo dõi các chiến dịch cứu trợ do nhóm phụ trách.
      </small>
    </div>

    @if ($laNhomTruong)
      <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/create') }}" class="btn btn-primary">
        Thêm chiến dịch
      </a>
    @endif
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 chien-dich-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 70px;">Mã</th>
            <th class="text-start" style="width: 24%;">Tên chiến dịch</th>
            <th class="text-start" style="width: 18%;">Sự kiện cứu trợ</th>
            <th class="text-start" style="width: 20%;">Địa điểm</th>
            <th style="width: 110px;">Bắt đầu</th>
            <th style="width: 110px;">Kết thúc</th>
            <th style="width: 120px;">Xác nhận</th>
            <th style="width: 130px;">Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($chienDichs as $chienDich)
            <tr class="clickable-row"
                data-href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}">
              <td class="text-center">
                {{ $chienDich->idChienDich }}
              </td>

              <td class="ten-chien-dich-cell">
                <div class="ten-chien-dich">
                  {{ $chienDich->tenChienDich }}
                </div>

                @if ($chienDich->moTa)
                  <div class="text-muted mo-ta-chien-dich">
                    {{ \Illuminate\Support\Str::limit($chienDich->moTa, 90) }}
                  </div>
                @else
                  <div class="text-muted mo-ta-chien-dich">
                    Chưa có mô tả
                  </div>
                @endif
              </td>

              <td class="su-kien-cell">
                @if ($chienDich->suKien)
                  <div class="fw-semibold text-truncate">
                    {{ $chienDich->suKien->tenSuKien }}
                  </div>
                  <small class="text-muted">
                    {{ $chienDich->suKien->loaiSuKien }}
                  </small>
                @else
                  -
                @endif
              </td>

              <td class="dia-diem-cell">
                @if ($chienDich->diaDiem)
                  <div class="dia-diem-text">
                    @if ($chienDich->diaDiem->chiTietDiaDiem)
                      {{ $chienDich->diaDiem->chiTietDiaDiem }},
                    @endif

                    @if ($chienDich->diaDiem->phuongXa)
                      {{ $chienDich->diaDiem->phuongXa }},
                    @endif

                    {{ $chienDich->diaDiem->tinhThanh }}
                  </div>
                @else
                  -
                @endif
              </td>

              <td class="text-center">
                {{ $chienDich->ngayBatDau ?? '-' }}
              </td>

              <td class="text-center">
                {{ $chienDich->ngayKetThuc ?? '-' }}
              </td>

              <td class="text-center">
                @if ($chienDich->daXacNhanCuuTro)
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Đã xác nhận</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Chưa xác nhận</span>
                  </span>
                @endif
              </td>

              <td class="text-center">
                @if ($chienDich->trangThai == 'Đang hoạt động')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>{{ $chienDich->trangThai }}</span>
                  </span>
                @elseif ($chienDich->trangThai == 'Hoàn thành')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-primary d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>{{ $chienDich->trangThai }}</span>
                  </span>
                @elseif ($chienDich->trangThai == 'Tạm ngưng')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>{{ $chienDich->trangThai }}</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-secondary d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>{{ $chienDich->trangThai ?? '-' }}</span>
                  </span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-4">
                Nhóm này chưa có chiến dịch cứu trợ nào.
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
  }

  .clickable-row:hover {
    background-color: #f5f7fb;
  }

  .ten-chien-dich,
  .mo-ta-chien-dich,
  .dia-diem-text {
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

  .su-kien-cell,
  .dia-diem-cell,
  .ten-chien-dich-cell {
    min-width: 0;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.clickable-row').forEach(function (row) {
      row.addEventListener('click', function () {
        const href = row.getAttribute('data-href');

        if (href) {
          window.location.href = href;
        }
      });
    });
  });
</script>
@endsection