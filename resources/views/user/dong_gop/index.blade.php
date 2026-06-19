@extends('layouts.user')

@section('title', 'Đóng góp của tôi | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Đóng góp của tôi</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Đóng góp của tôi
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

    <a href="{{ url('/user/dong-gop') }}"
       class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">Lịch sử đóng góp</h5>

      <small class="text-muted">
        Tổng hiển thị: {{ $dongGops->count() }}
      </small>
    </div>

    <a href="{{ url('/user/dong-gop/create') }}"
       class="btn btn-primary">
      Đăng ký đóng góp
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 dong-gop-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Chiến dịch</th>
            <th class="text-start">Tên nhóm</th>
            <th style="width: 180px;">Thời gian đóng góp</th>
            <th style="width: 190px;">Người tiếp nhận</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($dongGops as $dongGop)
            @php
              $chienDich = $dongGop->chienDich;
              $nhom = $chienDich->nhom ?? null;
              $diaDiem = $chienDich->diaDiem ?? null;

              $diaChiChienDich = collect([
                  $diaDiem->chiTietDiaDiem ?? null,
                  $diaDiem->phuongXa ?? null,
                  $diaDiem->tinhThanh ?? null,
              ])->filter()->implode(', ');

              $nhomTruong = $nhom?->thanhViens
                  ?->firstWhere('vaiTro', 'Nhóm trưởng');

              $nguoiDungNhomTruong = $nhomTruong?->nguoiDung;

              $tenNhomTruong = $nguoiDungNhomTruong->hoTen ?? '-';
              $tenDangNhapNhomTruong = $nguoiDungNhomTruong->tenDangNhap ?? '-';

              $nguoiTiepNhan = $dongGop->thanhVienTiepNhan?->nguoiDung;
            @endphp

            <tr class="clickable-row"
                data-href="{{ url('/user/dong-gop/' . $dongGop->idDongGop) }}">
              <td class="text-center fw-semibold">
                {{ $dongGop->idDongGop }}
              </td>

              <td>
                <div class="fw-semibold">
                  {{ $chienDich->tenChienDich ?? '-' }}
                </div>

                <small class="text-muted">
                  {{ $diaChiChienDich !== '' ? $diaChiChienDich : '-' }}
                </small>
              </td>

              <td>
                <div class="fw-semibold">
                  {{ $nhom->tenNhom ?? '-' }}
                </div>

                <small class="text-muted">
                  {{ $tenNhomTruong }} - {{ $tenDangNhapNhomTruong }}
                </small>
              </td>

              <td class="text-center">
                @if ($dongGop->thoiGianDongGop)
                  {{ \Carbon\Carbon::parse($dongGop->thoiGianDongGop)->format('d/m/Y H:i') }}
                @else
                  -
                @endif
              </td>

              <td class="text-center">
                @if ($nguoiTiepNhan)
                  <div class="fw-semibold">
                    {{ $nguoiTiepNhan->hoTen ?? '-' }}
                  </div>

                  <small class="text-muted">
                    {{ $nguoiTiepNhan->tenDangNhap ?? '-' }}
                  </small>
                @else
                  -
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5"
                  class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy lượt đóng góp phù hợp.
                @else
                  Bạn chưa có lượt đóng góp nào.
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
  .dong-gop-table th,
  .dong-gop-table td {
    vertical-align: middle;
  }

  .clickable-row {
    cursor: pointer;
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