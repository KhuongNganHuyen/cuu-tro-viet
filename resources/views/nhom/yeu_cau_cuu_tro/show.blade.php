@extends('layouts.nhom')

@section('title', 'Chi tiết yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet/dist/leaflet.css">

<style>
  .info-label {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 3px;
  }

  .info-value {
    color: #212529;
    font-weight: 500;
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

  .request-description {
    white-space: pre-line;
    line-height: 1.7;
  }

  .support-image-box {
    width: 100%;
    min-height: 280px;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .support-image {
    width: 100%;
    max-height: 360px;
    object-fit: contain;
  }

  #map {
    height: 360px;
    width: 100%;
    border-radius: 12px;
  }

  .action-card textarea {
    min-height: 110px;
    resize: vertical;
  }

  .support-other-box {
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 12px;
    background-color: #f8f9fa;
  }

  .support-other-list {
    padding-left: 18px;
    margin-bottom: 0;
  }

  .support-other-list li {
    margin-bottom: 4px;
  }

  .table th,
  .table td {
    vertical-align: middle;
  }

  .tiep-nhan-row {
    cursor: pointer;
  }

  .tiep-nhan-row:hover {
    background-color: #f8f9fa;
  }

  .tiep-nhan-detail-row {
    background-color: #fbfcfd;
  }

  .tiep-nhan-content-box {
    border: 1px dashed #ced4da;
    border-radius: 10px;
    padding: 14px;
    white-space: pre-line;
    line-height: 1.7;
    background-color: #ffffff;
  }

  .current-group-row {
    background-color: #f8fbff;
  }

  .current-group-row td:first-child {
    border-left: 3px solid #0d6efd;
  }

  .tab-card-header {
    gap: 12px;
  }

  .tiep-nhan-table {
    table-layout: fixed;
    width: 100%;
  }

  .tiep-nhan-table th {
    white-space: normal;
    line-height: 1.35;
    vertical-align: middle;
    font-size: 13px;
  }

  .tiep-nhan-table td {
    vertical-align: middle;
    font-size: 14px;
  }

  .tiep-nhan-table .col-ma {
    width: 50px;
    white-space: nowrap;
  }

  .tiep-nhan-table .col-nhom {
    width: 24%;
  }

  .tiep-nhan-table .col-chien-dich {
    width: 25%;
  }

  .tiep-nhan-table .col-thoi-gian {
    width: 130px;
  }

  .tiep-nhan-table .col-du-kien {
    width: 115px;
  }

  .tiep-nhan-table .col-trang-thai {
    width: 135px;
  }

  .tiep-nhan-table td.col-thoi-gian,
  .tiep-nhan-table td.col-du-kien,
  .tiep-nhan-table td.col-trang-thai {
    white-space: nowrap;
  }

  @media (max-width: 991.98px) {
    #map {
      height: 300px;
    }
  }
</style>

@php
  $trangThaiYeuCau = $yeuCau->trangThai;

  $classTrangThaiYeuCau = match ($trangThaiYeuCau) {
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

  $yeuCauCoTheTiepNhan = in_array($yeuCau->trangThai, [
      'Chờ tiếp nhận',
      'Cần thêm hỗ trợ',
  ], true) && !$daDuocNhomTiepNhan;

  $diaChiDayDu = collect([
      $yeuCau->diaDiem->chiTietDiaDiem ?? null,
      $yeuCau->diaDiem->phuongXa ?? null,
      $yeuCau->diaDiem->tinhThanh ?? null,
  ])->filter()->implode(', ');

  $coNhomTiepNhan = $yeuCau->tiepNhans->isNotEmpty();

  $coNhomKhacDangCanThem =
      isset($cacTiepNhanKhacDangCanThem)
      && $cacTiepNhanKhacDangCanThem->isNotEmpty();

  $nhomHienTaiCoTheHoTroNhomKhac =
      $tiepNhanCuaNhom
      && $daDuocNhomTiepNhan
      && !in_array($tiepNhanCuaNhom->trangThai, ['Hoàn thành'], true)
      && !in_array($yeuCau->trangThai, ['Hoàn thành', 'Đã hủy'], true)
      && $coNhomKhacDangCanThem;

  $tiepNhansSapXep = $yeuCau->tiepNhans
      ->sortBy(function ($tiepNhan) use ($nhom) {
          $uuTienNhomHienTai =
              (int) $tiepNhan->idNhom === (int) $nhom->idNhom
                  ? 0
                  : 1;

          return $uuTienNhomHienTai . '-'
              . str_pad((string) $tiepNhan->idTiepNhan, 10, '0', STR_PAD_LEFT);
      })
      ->values();

  $soNhomKhacCanHoTro = $coNhomKhacDangCanThem
      ? $cacTiepNhanKhacDangCanThem->count()
      : 0;

  $coTheHoanThanhPhanHoTro =
      $tiepNhanCuaNhom
      && $daDuocNhomTiepNhan
      && !in_array($tiepNhanCuaNhom->trangThai, ['Hoàn thành'], true)
      && !in_array($yeuCau->trangThai, ['Hoàn thành', 'Đã hủy'], true);
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              Tổng quan nhóm
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}">
              Yêu cầu cứu trợ
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Chi tiết
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

@if ($yeuCau->trangThai === 'Cần thêm hỗ trợ' && $tiepNhanDangCanThem && !$daDuocNhomTiepNhan)
  <div class="alert alert-warning">
    <strong>Có nhóm đang báo cần thêm hỗ trợ.</strong>
    Nhóm {{ $tiepNhanDangCanThem->nhom->tenNhom ?? '-' }} đã hỗ trợ nhưng vẫn còn phần cần bổ sung.
    Nhóm có thể tiếp nhận yêu cầu này nếu phù hợp với khả năng hỗ trợ.
  </div>
@endif

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap tab-card-header">
      <ul class="nav nav-tabs card-header-tabs" id="yeuCauDetailTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active"
                  id="thong-tin-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#thong-tin"
                  type="button"
                  role="tab">
            Thông tin yêu cầu
          </button>
        </li>

        @if ($coNhomTiepNhan)
          <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="nhom-tiep-nhan-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nhom-tiep-nhan"
                    type="button"
                    role="tab">
              Nhóm tiếp nhận ({{ $yeuCau->tiepNhans->count() }})
            </button>
          </li>
        @endif
      </ul>

      <div class="d-flex flex-wrap gap-2 justify-content-end">
        @if ($yeuCauCoTheTiepNhan)
          <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan') }}"
            class="btn btn-primary">
            Tiếp nhận vào chiến dịch
          </a>

          @if ($laNhomTruong)
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tao-chien-dich') }}"
              class="btn btn-outline-primary">
              Tạo chiến dịch từ yêu cầu
            </a>
          @endif
        @endif

        @if ($coTheHoanThanhPhanHoTro)
          <form action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan/' . $tiepNhanCuaNhom->idTiepNhan . '/hoan-thanh') }}"
                method="POST"
                onsubmit="return confirm('Xác nhận nhóm đã hoàn thành phần hỗ trợ của yêu cầu này?')">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="btn btn-success">
              Hoàn thành
            </button>
          </form>
        @endif

        <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}"
          class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>
  </div>

  <div class="card-body">
    <div class="tab-content" id="yeuCauDetailTabsContent">

      {{-- TAB 1: THÔNG TIN YÊU CẦU --}}
      <div class="tab-pane fade show active" id="thong-tin" role="tabpanel">
        <div class="row">
          <div class="col-lg-7">
            <h5 class="mb-1">
              {{ $yeuCau->tieuDeYeuCau }}
            </h5>

            <small class="text-muted d-block mb-4">
              Mã yêu cầu: #{{ $yeuCau->idYeuCau }}
            </small>

            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="info-label">Người gửi</div>
                <div class="info-value">{{ $yeuCau->nguoiGui->hoTen ?? '-' }}</div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Số điện thoại</div>
                <div class="info-value">{{ $yeuCau->nguoiGui->sdt ?? '-' }}</div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $yeuCau->nguoiGui->email ?? '-' }}</div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Số người cần hỗ trợ</div>
                <div class="info-value">{{ $yeuCau->soNguoi ?? '-' }}</div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Mức độ khẩn cấp</div>

                <div class="info-value d-inline-flex align-items-center gap-2">
                  <span class="muc-do-dot {{ $classMucDo }}"></span>
                  <span>{{ $yeuCau->mucDoKhanCap ?? '-' }}</span>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Trạng thái yêu cầu</div>

                <div class="info-value d-inline-flex align-items-center gap-2">
                  <span class="status-dot {{ $classTrangThaiYeuCau }}"></span>
                  <span>{{ $trangThaiYeuCau }}</span>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Thời gian gửi</div>

                <div class="info-value">
                  {{ $yeuCau->thoiGianGui
                      ? \Carbon\Carbon::parse($yeuCau->thoiGianGui)->format('d/m/Y H:i')
                      : '-' }}
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="info-label">Địa chỉ</div>
                <div class="info-value">
                  {{ $diaChiDayDu !== '' ? $diaChiDayDu : '-' }}
                </div>
              </div>
            </div>

            <hr>

            <div class="mb-3">
              <div class="info-label">Mô tả tình hình</div>
              <div class="request-description">{{ $yeuCau->moTa }}</div>
            </div>
          </div>

          <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="info-label mb-2">Hình ảnh minh chứng</div>

            <div class="support-image-box">
              @if ($yeuCau->hinhAnh)
                <img src="{{ asset('storage/' . $yeuCau->hinhAnh) }}"
                     alt="Hình ảnh minh chứng"
                     class="support-image">
              @else
                <div class="text-muted text-center px-3">
                  Yêu cầu này chưa có hình ảnh minh chứng.
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="mt-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <h6 class="mb-0">Địa điểm cần hỗ trợ</h6>
              <small class="text-muted">
                {{ $diaChiDayDu !== '' ? $diaChiDayDu : 'Chưa có thông tin địa chỉ đầy đủ.' }}
              </small>
            </div>
          </div>

          <div id="map" class="rounded border"></div>
        </div>
      </div>

      {{-- TAB 2: NHÓM TIẾP NHẬN --}}
      @if ($coNhomTiepNhan)
        <div class="tab-pane fade" id="nhom-tiep-nhan" role="tabpanel">
          <div class="row">

            {{-- BÊN TRÁI: BẢNG DANH SÁCH NHÓM TIẾP NHẬN --}}
            <div class="col-xl-9 col-lg-8 mb-3 mb-lg-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="mb-0">Danh sách nhóm tiếp nhận yêu cầu</h5>
                </div>

                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0 tiep-nhan-table">
                      <thead>
                        <tr class="text-uppercase">
                          <th class="col-ma">Mã</th>
                          <th class="col-nhom">Nhóm tiếp nhận</th>
                          <th class="col-chien-dich">Chiến dịch</th>
                          <th class="col-thoi-gian">Thời gian tiếp nhận</th>
                          <th class="col-du-kien">Dự kiến hỗ trợ</th>
                          <th class="col-trang-thai">Trạng thái</th>
                        </tr>
                      </thead>

                      <tbody>
                        @foreach ($tiepNhansSapXep as $tiepNhan)
                          @php
                            $laLuotCuaNhomHienTai =
                                (int) $tiepNhan->idNhom === (int) $nhom->idNhom;

                            $classTrangThaiTiepNhan = match ($tiepNhan->trangThai) {
                                'Đã tiếp nhận' => 'status-received',
                                'Cần thêm hỗ trợ' => 'status-more-help',
                                'Hoàn thành' => 'status-completed',
                                default => 'status-default',
                            };

                            $idDongChiTiet = 'noi-dung-tiep-nhan-' . $tiepNhan->idTiepNhan;
                          @endphp

                          <tr class="tiep-nhan-row {{ $laLuotCuaNhomHienTai ? 'current-group-row' : '' }}"
                              data-target-row="{{ $idDongChiTiet }}">
                            <td class="col-ma">{{ $tiepNhan->idTiepNhan }}</td>

                            <td class="col-nhom">
                              <strong>{{ $tiepNhan->nhom->tenNhom ?? '-' }}</strong>
                            </td>

                            <td class="col-chien-dich">
                              {{ $tiepNhan->chienDich->tenChienDich ?? '-' }}
                            </td>

                            <td class="col-thoi-gian">
                              {{ $tiepNhan->thoiGianTiepNhan
                                  ? \Carbon\Carbon::parse($tiepNhan->thoiGianTiepNhan)->format('d/m/Y H:i')
                                  : '-' }}
                            </td>

                            <td class="col-du-kien">
                              {{ $tiepNhan->thoiGianDuKienHoTro
                                  ? \Carbon\Carbon::parse($tiepNhan->thoiGianDuKienHoTro)->format('d/m/Y')
                                  : '-' }}
                            </td>

                            <td class="col-trang-thai">
                              <span class="d-inline-flex align-items-center gap-2">
                                <span class="status-dot {{ $classTrangThaiTiepNhan }}"></span>
                                <span>{{ $tiepNhan->trangThai ?? '-' }}</span>
                              </span>
                            </td>
                          </tr>
                          
                          <tr id="{{ $idDongChiTiet }}"
                              class="tiep-nhan-detail-row d-none">
                            <td colspan="6">
                              <div class="info-label">Nội dung đảm nhận</div>

                              <div class="tiep-nhan-content-box">
                                {{ $tiepNhan->noiDungDamNhan ?: 'Chưa có nội dung đảm nhận.' }}
                              </div>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            {{-- BÊN PHẢI: KHU VỰC HỖ TRỢ --}}
            <div class="col-xl-3 col-lg-4">
              @if ($tiepNhanCuaNhom && $daDuocNhomTiepNhan)
                <div class="card action-card">
                  <div class="card-header">
                    <h5 class="mb-0">Hỗ trợ</h5>
                  </div>

                  <div class="card-body">
                    @if ($tiepNhanCuaNhom->trangThai === 'Hoàn thành')
                      <div class="alert alert-success mb-0">
                        Nhóm đã hoàn thành phần hỗ trợ của yêu cầu này.
                      </div>

                    @elseif ($yeuCau->trangThai === 'Đã hủy')
                      <div class="alert alert-danger mb-0">
                        Yêu cầu đã bị hủy nên không thể cập nhật tiếp nhận.
                      </div>

                    @else
                      {{-- Ô điền cần thêm hỗ trợ --}}
                      @if ($tiepNhanCuaNhom->trangThai === 'Cần thêm hỗ trợ')
                        <div class="alert alert-warning">
                          Chờ hỗ trợ.
                        </div>
                      @else
                        <form action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan/' . $tiepNhanCuaNhom->idTiepNhan . '/can-them-ho-tro') }}"
                              method="POST"
                              class="mb-3"
                              onsubmit="return confirm('Bạn có chắc muốn báo yêu cầu này cần thêm hỗ trợ không?')">
                          @csrf
                          @method('PATCH')

                          <label class="form-label">
                            Nội dung cần thêm hỗ trợ
                          </label>

                          <textarea name="noiDungDamNhan"
                                    class="form-control mb-2"
                                    placeholder="Ghi rõ nội dung cần hỗ trợ..."></textarea>

                          <button type="submit"
                                  class="btn btn-outline-warning w-100">
                            Yêu cầu hỗ trợ
                          </button>
                        </form>
                      @endif

                      {{-- Nút yêu cầu / thu hồi --}}
                      @if ($tiepNhanCuaNhom->trangThai === 'Cần thêm hỗ trợ')
                        <form action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan/' . $tiepNhanCuaNhom->idTiepNhan . '/thu-hoi-can-them-ho-tro') }}"
                              method="POST"
                              class="mb-3"
                              onsubmit="return confirm('Bạn có chắc muốn thu hồi trạng thái cần thêm hỗ trợ không?')">
                          @csrf
                          @method('PATCH')

                          <button type="submit"
                                  class="btn btn-outline-secondary w-100">
                            Thu hồi yêu cầu hỗ trợ
                          </button>
                        </form>
                      @endif

                      <hr>

                      {{-- Số nhóm cần hỗ trợ --}}
                      <div class="mb-3">
                        <div class="info-label">Số nhóm cần hỗ trợ: <strong class="info-value">{{ $soNhomKhacCanHoTro }} nhóm</strong></div>
                      </div>

                      {{-- Hỗ trợ nhóm đang thiếu --}}
                      @if ($nhomHienTaiCoTheHoTroNhomKhac)
                        <form action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan/' . $tiepNhanCuaNhom->idTiepNhan . '/ho-tro-nhom-dang-thieu') }}"
                              method="POST"
                              onsubmit="return confirm('Xác nhận cập nhật hỗ trợ cho nhóm đang thiếu?')">
                          @csrf
                          @method('PATCH')

                          <div class="mb-3">
                            <label class="form-label">
                              Hình thức hỗ trợ
                            </label>

                            <select name="loaiHoTro"
                                    id="loaiHoTro"
                                    class="form-select"
                                    required>
                              <option value="">-- Chọn hình thức --</option>
                              <option value="ho_tro_mot_phan">Hỗ trợ 1 phần</option>
                              <option value="ho_tro_day_du">Hỗ trợ đầy đủ</option>
                              <option value="khong_the_ho_tro">Không thể hỗ trợ</option>
                            </select>
                          </div>

                          <div class="mb-3"
                              id="noiDungHoTroWrapper">
                            <label class="form-label">
                              Nội dung hỗ trợ
                            </label>

                            <textarea name="noiDungHoTro"
                                      id="noiDungHoTro"
                                      class="form-control"
                                      placeholder="Ghi rõ nhóm hỗ trợ thêm nội dung gì..."></textarea>
                          </div>

                          <button type="submit"
                                  class="btn btn-primary w-100">
                            Cập nhật hỗ trợ
                          </button>
                        </form>
                      @else
                        <div class="alert alert-light border mb-0">
                          Không yêu cầu hỗ trợ.
                        </div>
                      @endif
                    @endif
                  </div>
                </div>
              @else
                <div class="card">
                  <div class="card-header">
                    <h5 class="mb-0">Hỗ trợ</h5>
                  </div>

                  <div class="card-body">
                    <div class="alert alert-light border mb-0">
                      Nhóm chưa tiếp nhận yêu cầu này nên chưa có thao tác hỗ trợ.
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const lat = Number('{{ $yeuCau->diaDiem->viDo ?? 16.047079 }}');
    const lng = Number('{{ $yeuCau->diaDiem->kinhDo ?? 108.206230 }}');

    const mapElement = document.getElementById('map');

    if (mapElement) {
      const map = L.map('map').setView([lat, lng], 14);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);

      L.marker([lat, lng]).addTo(map);

      document
        .querySelectorAll('button[data-bs-toggle="tab"]')
        .forEach(function (tabButton) {
          tabButton.addEventListener('shown.bs.tab', function () {
            setTimeout(function () {
              map.invalidateSize();
            }, 150);
          });
        });
    }

    const loaiHoTro = document.getElementById('loaiHoTro');
    const noiDungHoTroWrapper = document.getElementById('noiDungHoTroWrapper');
    const noiDungHoTro = document.getElementById('noiDungHoTro');

    if (loaiHoTro && noiDungHoTroWrapper && noiDungHoTro) {
      const capNhatHienThiNoiDung = function () {
        if (loaiHoTro.value === 'khong_the_ho_tro') {
          noiDungHoTroWrapper.style.display = 'none';
          noiDungHoTro.value = '';
          noiDungHoTro.removeAttribute('required');
        } else {
          noiDungHoTroWrapper.style.display = 'block';
          noiDungHoTro.setAttribute('required', 'required');
        }
      };

      loaiHoTro.addEventListener('change', capNhatHienThiNoiDung);
      capNhatHienThiNoiDung();
    }

    document
      .querySelectorAll('.tiep-nhan-row')
      .forEach(function (row) {
        row.addEventListener('click', function () {
          const targetId = row.getAttribute('data-target-row');
          const targetRow = document.getElementById(targetId);

          if (!targetRow) {
            return;
          }

          targetRow.classList.toggle('d-none');
        });
      });
  });
</script>
@endsection