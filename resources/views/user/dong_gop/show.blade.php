@extends('layouts.user')

@section('title', 'Chi tiết đóng góp | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết đóng góp</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/dong-gop') }}">Đóng góp của tôi</a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Chi tiết đóng góp
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

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
  $nguoiTiepNhan = $dongGop->thanhVienTiepNhan?->nguoiDung;

  $trangThais = $dongGop->chiTietDongGops
      ->pluck('trangThai')
      ->unique()
      ->values();

  $trangThaiTong = $trangThais->count() === 1
      ? $trangThais->first()
      : 'Nhiều trạng thái';

  $classTrangThaiTong = match ($trangThaiTong) {
      'Chờ xác nhận' => 'status-waiting',
      'Đã xác nhận' => 'status-confirmed',
      'Từ chối' => 'status-rejected',
      default => 'status-default',
  };
@endphp

<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">Thông tin chung</h5>
    </div>

    <a href="{{ url('/user/dong-gop') }}"
       class="btn btn-secondary">
      Quay lại
    </a>
  </div>

  <div class="card-body">
    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="info-label">Mã đóng góp</div>
        <div class="info-value">{{ $dongGop->idDongGop }}</div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="info-label">Trạng thái</div>

        <div class="info-value">
          <span class="d-inline-flex align-items-center gap-2">
            <span class="status-dot {{ $classTrangThaiTong }}"></span>
            {{ $trangThaiTong ?? '-' }}
          </span>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="info-label">Thời gian đóng góp</div>

        <div class="info-value">
          @if ($dongGop->thoiGianDongGop)
            {{ \Carbon\Carbon::parse($dongGop->thoiGianDongGop)->format('d/m/Y H:i') }}
          @else
            -
          @endif
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="info-label">Người tiếp nhận</div>

        <div class="info-value">
          @if ($nguoiTiepNhan)
            {{ $nguoiTiepNhan->hoTen ?? '-' }}
            <small class="text-muted">
              - {{ $nguoiTiepNhan->tenDangNhap ?? '-' }}
            </small>
          @else
            -
          @endif
        </div>
      </div>

      <div class="col-md-6 mb-3">
        <div class="info-label">Chiến dịch</div>

        <div class="info-value">
          {{ $chienDich->tenChienDich ?? '-' }}
        </div>

        <small class="text-muted">
          <strong>Địa chỉ:</strong>
          {{ $diaChiChienDich !== '' ? $diaChiChienDich : '-' }}
        </small>
      </div>

      <div class="col-md-6 mb-3">
        <div class="info-label">Nhóm tiếp nhận</div>

        <div class="info-value">
          {{ $nhom->tenNhom ?? '-' }}
        </div>

        <small class="text-muted">
          <strong>Nhóm trưởng:</strong>
          {{ $nguoiDungNhomTruong->hoTen ?? '-' }}
          -
          {{ $nguoiDungNhomTruong->tenDangNhap ?? '-' }}
        </small>
      </div>

      <div class="col-md-12 mb-0">
        <div class="info-label">Ghi chú</div>

        <div class="info-value">
          {{ $dongGop->ghiChu ?: 'Không có ghi chú.' }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-1">Danh sách hàng hóa đóng góp</h5>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 chi-tiet-dong-gop-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 80px;">Mã</th>
            <th class="text-start">Hàng hóa</th>
            <th style="width: 180px;">Danh mục</th>
            <th style="width: 120px;">Số lượng</th>
            <th style="width: 150px;">Hạn sử dụng</th>
            <th style="width: 170px;">Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($dongGop->chiTietDongGops as $chiTiet)
            @php
              $hangHoa = $chiTiet->hangHoa;

              $classTrangThai = match ($chiTiet->trangThai) {
                  'Chờ xác nhận' => 'status-waiting',
                  'Đã xác nhận' => 'status-confirmed',
                  'Từ chối' => 'status-rejected',
                  default => 'status-default',
              };
            @endphp

            <tr>
              <td class="text-center fw-semibold">
                {{ $chiTiet->idChiTietDongGop }}
              </td>

              <td>
                <div class="fw-semibold">
                  {{ $hangHoa->tenHangHoa ?? '-' }}
                </div>

                <small class="text-muted">
                  Đơn vị: {{ $hangHoa->donViTinh ?? '-' }}
                </small>
              </td>

              <td>
                {{ $hangHoa->danhMucHang->tenDanhMucHang ?? '-' }}
              </td>

              <td class="text-center">
                {{ $chiTiet->soLuong }}
                {{ $hangHoa->donViTinh ?? '' }}
              </td>

              <td class="text-center">
                @if ($chiTiet->hanSuDung)
                  {{ \Carbon\Carbon::parse($chiTiet->hanSuDung)->format('d/m/Y') }}
                @else
                  -
                @endif
              </td>

              <td class="text-center">
                <span class="d-inline-flex align-items-center gap-2">
                  <span class="status-dot {{ $classTrangThai }}"></span>
                  {{ $chiTiet->trangThai ?? '-' }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6"
                  class="text-center text-muted py-4">
                Không có hàng hóa nào trong lượt đóng góp này.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .info-label {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 3px;
  }

  .info-value {
    color: #212529;
    font-weight: 600;
  }

  .note-box {
    border: 1px dashed #ced4da;
    border-radius: 10px;
    padding: 12px 14px;
    background-color: #fbfcfd;
    white-space: pre-line;
    line-height: 1.6;
  }

  .chi-tiet-dong-gop-table th,
  .chi-tiet-dong-gop-table td {
    vertical-align: middle;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    display: inline-block;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .status-waiting {
    background-color: #ffc107;
  }

  .status-confirmed {
    background-color: #198754;
  }

  .status-rejected {
    background-color: #dc3545;
  }

  .status-default {
    background-color: #6c757d;
  }
</style>
@endsection