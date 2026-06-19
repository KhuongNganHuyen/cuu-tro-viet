@extends('layouts.nhom')

@section('title', 'Sửa đợt phân phối | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  .section-card { margin-bottom: 20px; }

  .phan-phoi-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 18px;
    background-color: #fff;
  }

  .phan-phoi-card:last-child { margin-bottom: 0; }

  .phan-phoi-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
  }

  .phan-phoi-card-title {
    font-weight: 700;
    color: #212529;
  }

  .phan-phoi-card-subtitle {
    font-size: 13px;
    color: #6c757d;
  }

  .phan-phoi-location-block,
  .phan-phoi-request-block {
    display: none;
  }

  .phan-phoi-card.show-location .phan-phoi-location-block {
    display: block;
  }

  .phan-phoi-card.show-request .phan-phoi-request-block {
    display: block;
  }

  .phan-phoi-map {
    width: 100%;
    height: 330px;
    border-radius: 10px;
    border: 1px solid #dee2e6;
    overflow: hidden;
    position: relative;
    z-index: 1;
    margin-bottom: 12px;
  }

  .leaflet-container { z-index: 1 !important; }

  .hang-hoa-table th,
  .hang-hoa-table td {
    vertical-align: middle;
    padding-top: 14px;
    padding-bottom: 14px;
  }

  .hang-hoa-table th { white-space: nowrap; }

  .resource-name-select { min-width: 220px; }

  .sticky-action-bar {
    position: sticky;
    bottom: 0;
    z-index: 20;
    padding: 14px 0 0;
    background-color: #fff;
    border-top: 1px solid #e9ecef;
    margin-top: 20px;
  }

  .info-label {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 4px;
  }

  .info-value {
    font-weight: 600;
    color: #212529;
  }
</style>

@php
  $ngayBatDauValue = old('ngayPhanPhoi',
      $dotPhanPhoi->ngayPhanPhoi
          ? \Carbon\Carbon::parse($dotPhanPhoi->ngayPhanPhoi)->format('Y-m-d\TH:i')
          : date('Y-m-d\TH:i')
  );

  $trangThaiDotValue = old('trangThaiDot', $dotPhanPhoi->trangThai ?? 'Đang phân phối');

  $danhMucNguonLucs = $nguonLucs
      ->map(fn ($nguonLuc) => $nguonLuc->hangHoa->danhMucHang->tenDanhMucHang ?? null)
      ->filter()
      ->unique()
      ->sort()
      ->values();

  $tinhThanhs = $diaDiems
      ->pluck('tinhThanh')
      ->filter()
      ->unique()
      ->sort()
      ->values();

  $chiTietGroups = $dotPhanPhoi->chiTietPhanPhois->groupBy(function ($chiTiet) {
      return implode('|', [
          $chiTiet->loaiPhanPhoi ?? '',
          $chiTiet->idDiaDiem ?? '',
          $chiTiet->idTiepNhan ?? '',
          $chiTiet->nguoiNhan ?? '',
      ]);
  });
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Sửa đợt phân phối</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}">Chiến dịch</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '#phan-phoi') }}">{{ $chienDich->tenChienDich }}</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi/' . $dotPhanPhoi->idDotPhanPhoi) }}">Chi tiết đợt phân phối</a></li>
          <li class="breadcrumb-item" aria-current="page">Sửa</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if ($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại thông tin.</div>
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST"
      action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi/' . $dotPhanPhoi->idDotPhanPhoi) }}"
      id="formSuaDotPhanPhoi">
  @csrf
  @method('PUT')

  {{-- CARD 1: THÔNG TIN ĐỢT PHÂN PHỐI --}}
  <div class="card section-card">
    <div class="card-header">
      <h5 class="mb-0">Thông tin chung</h5>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-3 mb-3">
          <div class="info-label">Mã đợt</div>
          <div class="info-value">{{ $dotPhanPhoi->idDotPhanPhoi }}</div>
        </div>

        <div class="col-md-5 mb-3">
          <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
          <input type="datetime-local"
                 name="ngayPhanPhoi"
                 id="ngayBatDauDotPhanPhoi"
                 class="form-control"
                 value="{{ $ngayBatDauValue }}">
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Trạng thái đợt <span class="text-danger">*</span></label>
          <select name="trangThaiDot"
                  id="trangThaiDotPhanPhoi"
                  class="form-select"
                  data-old-value="{{ $trangThaiDotValue }}">
          </select>
        </div>
      </div>

      <div class="mb-0">
        <label class="form-label">Ghi chú</label>
        <textarea name="ghiChu"
                  class="form-control"
                  rows="3"
                  placeholder="Ghi chú chung cho đợt phân phối.">{{ old('ghiChu', $dotPhanPhoi->ghiChu) }}</textarea>
      </div>
    </div>
  </div>

  {{-- CARD 2: CHI TIẾT PHÂN PHỐI --}}
  <div class="card section-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <h5 class="mb-0">Chi tiết phân phối</h5>
      </div>

      <button type="button"
              class="btn btn-outline-primary"
              id="btnThemChiTietPhanPhoi">
        Thêm chi tiết phân phối
      </button>
    </div>

    <div class="card-body">
      <div id="chiTietPhanPhoiContainer">
        @foreach ($chiTietGroups as $chiTietIndex => $group)
          @php
            $first = $group->first();

            $loaiPhanPhoi = old("chiTiet.$chiTietIndex.loaiPhanPhoi", $first->loaiPhanPhoi ?? 'Địa điểm');

            $diaDiem = $first->diaDiem;
            $tinhThanh = old("chiTiet.$chiTietIndex.tinhThanh", $diaDiem->tinhThanh ?? '');
            $phuongXa = old("chiTiet.$chiTietIndex.phuongXa", $diaDiem->phuongXa ?? '');
            $chiTietDiaDiem = old("chiTiet.$chiTietIndex.chiTietDiaDiem", $diaDiem->chiTietDiaDiem ?? '');
            $viDo = old("chiTiet.$chiTietIndex.viDo", $diaDiem->viDo ?? '');
            $kinhDo = old("chiTiet.$chiTietIndex.kinhDo", $diaDiem->kinhDo ?? '');

            $thoiGianGiaoValue = old("chiTiet.$chiTietIndex.thoiGianGiao",
                $first->thoiGianGiao
                    ? \Carbon\Carbon::parse($first->thoiGianGiao)->format('Y-m-d\TH:i')
                    : ''
            );
          @endphp

          <div class="phan-phoi-card" data-detail-row>
            <div class="phan-phoi-card-header">
              <div>
                <div class="phan-phoi-card-title" data-detail-title>
                  Chi tiết phân phối {{ $loop->iteration }}
                </div>
              </div>

              <button type="button"
                      class="btn btn-light border text-danger"
                      data-remove-detail>
                Xóa
              </button>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Người nhận</label>
                <input type="text"
                       name="chiTiet[{{ $chiTietIndex }}][nguoiNhan]"
                       class="form-control"
                       value="{{ old("chiTiet.$chiTietIndex.nguoiNhan", $first->nguoiNhan) }}"
                       placeholder="Ví dụ: Ông A / Đại diện hộ dân / Trưởng khu vực"
                       autocomplete="off">
              </div>

              <div class="col-md-4 mb-3">
                <label class="form-label">Loại phân phối <span class="text-danger">*</span></label>
                <select name="chiTiet[{{ $chiTietIndex }}][loaiPhanPhoi]"
                        class="form-select"
                        data-loai-phan-phoi>
                  <option value="Địa điểm" {{ $loaiPhanPhoi === 'Địa điểm' ? 'selected' : '' }}>Địa điểm</option>
                  <option value="Địa điểm và yêu cầu" {{ $loaiPhanPhoi === 'Địa điểm và yêu cầu' ? 'selected' : '' }}>Địa điểm và yêu cầu</option>
                  <option value="Yêu cầu" {{ $loaiPhanPhoi === 'Yêu cầu' ? 'selected' : '' }}>Yêu cầu</option>
                </select>
              </div>

              <div class="col-md-4 mb-3">
                <label class="form-label">Ngày giao</label>
                <input type="datetime-local"
                       name="chiTiet[{{ $chiTietIndex }}][thoiGianGiao]"
                       class="form-control"
                       value="{{ $thoiGianGiaoValue }}">
              </div>
            </div>

            <div class="phan-phoi-request-block">
              <div class="mb-3">
                <label class="form-label">Yêu cầu cứu trợ <span class="text-danger">*</span></label>
                <select name="chiTiet[{{ $chiTietIndex }}][idTiepNhan]"
                        class="form-select"
                        data-request-select>
                  <option value="">-- Chọn yêu cầu --</option>

                  @foreach ($tiepNhanYeuCaus as $tiepNhan)
                    @php
                      $diaChiYeuCau = collect([
                          $tiepNhan->yeuCau->diaDiem->chiTietDiaDiem ?? null,
                          $tiepNhan->yeuCau->diaDiem->phuongXa ?? null,
                          $tiepNhan->yeuCau->diaDiem->tinhThanh ?? null,
                      ])->filter()->implode(', ');
                    @endphp

                    <option value="{{ $tiepNhan->idTiepNhan }}"
                            data-dia-chi="{{ $diaChiYeuCau }}"
                            {{ old("chiTiet.$chiTietIndex.idTiepNhan", $first->idTiepNhan) == $tiepNhan->idTiepNhan ? 'selected' : '' }}>
                      {{ $tiepNhan->yeuCau->tieuDeYeuCau ?? $tiepNhan->yeuCau->loaiYeuCau ?? ('Yêu cầu #' . ($tiepNhan->yeuCau->idYeuCau ?? '')) }}
                    </option>
                  @endforeach
                </select>

                <small class="text-muted d-block mt-1" data-request-info> </small>
              </div>
            </div>

            <div class="phan-phoi-location-block">
              <div class="border rounded p-3 mb-3">
                <div class="fw-semibold mb-3">Địa điểm phân phối</div>

                <input type="hidden"
                       name="chiTiet[{{ $chiTietIndex }}][idDiaDiemCoSan]"
                       value="{{ old("chiTiet.$chiTietIndex.idDiaDiemCoSan", $first->idDiaDiem) }}"
                       data-id-dia-diem-co-san>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Tỉnh/Thành</label>
                    <select name="chiTiet[{{ $chiTietIndex }}][tinhThanh]"
                            class="form-select"
                            data-tinh-thanh
                            data-old-value="{{ $tinhThanh }}">
                      <option value="">-- Chọn tỉnh/thành --</option>

                      @foreach ($tinhThanhs as $tinhThanhOption)
                        <option value="{{ $tinhThanhOption }}" {{ $tinhThanh === $tinhThanhOption ? 'selected' : '' }}>
                          {{ $tinhThanhOption }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Phường/Xã</label>
                    <select name="chiTiet[{{ $chiTietIndex }}][phuongXa]"
                            class="form-select"
                            data-phuong-xa
                            data-old-value="{{ $phuongXa }}">
                      <option value="">-- Chọn phường/xã --</option>
                    </select>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Địa chỉ chi tiết</label>
                  <input type="text"
                         name="chiTiet[{{ $chiTietIndex }}][chiTietDiaDiem]"
                         class="form-control"
                         list="danhSachDiaDiem_{{ $chiTietIndex }}"
                         value="{{ $chiTietDiaDiem }}"
                         data-chi-tiet-dia-diem
                         placeholder="Gõ địa chỉ chi tiết hoặc chọn địa điểm đã có"
                         autocomplete="off">

                  <datalist id="danhSachDiaDiem_{{ $chiTietIndex }}"
                            data-danh-sach-dia-diem></datalist>
                </div>

                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Chọn vị trí trên bản đồ</label>

                    <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            data-tim-tren-ban-do>
                      Tìm trên bản đồ
                    </button>
                  </div>

                  <div class="phan-phoi-map" data-map></div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Vĩ độ</label>
                    <input type="text"
                           name="chiTiet[{{ $chiTietIndex }}][viDo]"
                           class="form-control"
                           value="{{ $viDo }}"
                           data-vi-do
                           readonly>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Kinh độ</label>
                    <input type="text"
                           name="chiTiet[{{ $chiTietIndex }}][kinhDo]"
                           class="form-control"
                           value="{{ $kinhDo }}"
                           data-kinh-do
                           readonly>
                  </div>
                </div>
              </div>
            </div>

            <div class="border rounded p-3">
              <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                <div>
                  <div class="fw-semibold">Hàng hóa phân phối</div>
                </div>

                <button type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-add-hang-hoa>
                  Thêm hàng hóa
                </button>
              </div>

              <div class="table-responsive">
                <table class="table table-hover table-bordered hang-hoa-table mb-0">
                  <thead>
                    <tr class="text-center">
                      <th style="width: 220px;">Danh mục</th>
                      <th class="text-start">Hàng hóa</th>
                      <th style="width: 150px;">Hiện có</th>
                      <th style="width: 150px;">Số lượng giao</th>
                      <th style="width: 170px;">Trạng thái</th>
                      <th style="width: 90px;"></th>
                    </tr>
                  </thead>

                  <tbody data-hang-hoa-container>
                    @foreach ($group as $hangHoaIndex => $chiTiet)
                      @php
                        $oldRow = old("chiTiet.$chiTietIndex.hangHoa.$hangHoaIndex", []);

                        $idNguonLucDangChon = $oldRow['idNguonLuc'] ?? $chiTiet->idNguonLuc;
                        $soLuongGiaoDangChon = $oldRow['soLuongGiao'] ?? $chiTiet->soLuongGiao;
                        $trangThaiChiTietDangChon = $oldRow['trangThai'] ?? $chiTiet->trangThai;
                      @endphp

                      <tr data-hang-hoa-row>
                        <td>
                          <select class="form-select" data-danh-muc-filter>
                            <option value="">Tất cả danh mục</option>

                            @foreach ($danhMucNguonLucs as $danhMuc)
                              <option value="{{ $danhMuc }}">
                                {{ $danhMuc }}
                              </option>
                            @endforeach
                          </select>
                        </td>

                        <td>
                          <input type="hidden"
                                 name="chiTiet[{{ $chiTietIndex }}][hangHoa][{{ $hangHoaIndex }}][idChiTietPhanPhoi]"
                                 value="{{ $chiTiet->idChiTietPhanPhoi }}"
                                 data-id-chi-tiet-phan-phoi>

                          <select name="chiTiet[{{ $chiTietIndex }}][hangHoa][{{ $hangHoaIndex }}][idNguonLuc]"
                                  class="form-select resource-name-select"
                                  data-nguon-luc-select
                                  data-old-nguon-luc-id="{{ $chiTiet->idNguonLuc }}"
                                  data-old-so-luong="{{ $chiTiet->soLuongGiao }}"
                                  data-old-allocated="{{ $chiTiet->trangThai === 'Đã hủy' ? '0' : '1' }}">
                            <option value="">-- Chọn hàng hóa --</option>

                            @foreach ($nguonLucs as $nguonLuc)
                              <option value="{{ $nguonLuc->idNguonLuc }}"
                                      data-so-luong="{{ $nguonLuc->soLuongHienCo }}"
                                      data-don-vi="{{ $nguonLuc->hangHoa->donViTinh ?? '' }}"
                                      data-danh-muc="{{ $nguonLuc->hangHoa->danhMucHang->tenDanhMucHang ?? '' }}"
                                      {{ (string) $idNguonLucDangChon === (string) $nguonLuc->idNguonLuc ? 'selected' : '' }}>
                                {{ $nguonLuc->hangHoa->tenHangHoa ?? '-' }}
                              </option>
                            @endforeach
                          </select>
                        </td>

                        <td class="text-center">
                          <span data-so-luong-hien-co class="text-muted">-</span>
                        </td>

                        <td>
                          <input type="number"
                                 name="chiTiet[{{ $chiTietIndex }}][hangHoa][{{ $hangHoaIndex }}][soLuongGiao]"
                                 class="form-control"
                                 min="0.01"
                                 step="0.01"
                                 value="{{ $soLuongGiaoDangChon }}"
                                 data-so-luong-giao
                                 placeholder="0">
                        </td>

                        <td>
                          <select name="chiTiet[{{ $chiTietIndex }}][hangHoa][{{ $hangHoaIndex }}][trangThai]"
                                  class="form-select"
                                  data-trang-thai-chi-tiet>
                            <option value="Chưa giao" {{ $trangThaiChiTietDangChon === 'Chưa giao' ? 'selected' : '' }}>Chưa giao</option>
                            <option value="Đã giao" {{ $trangThaiChiTietDangChon === 'Đã giao' ? 'selected' : '' }}>Đã giao</option>
                            <option value="Không giao được" {{ $trangThaiChiTietDangChon === 'Không giao được' ? 'selected' : '' }}>Không giao được</option>
                            <option value="Đã hủy" {{ $trangThaiChiTietDangChon === 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                          </select>
                        </td>

                        <td class="text-center">
                          <button type="button"
                                  class="btn btn-sm btn-light border text-danger"
                                  data-remove-hang-hoa>
                            Xóa
                          </button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="sticky-action-bar">
        <div class="d-flex justify-content-end gap-2">
          <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi/' . $dotPhanPhoi->idDotPhanPhoi) }}"
             class="btn btn-secondary">
            Hủy
          </a>

          <button type="submit" class="btn btn-primary">
            Lưu thay đổi
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

<script id="diaDiemData" type="application/json">
{!! $diaDiemJson !!}
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const diaDiems = JSON.parse(document.getElementById('diaDiemData').textContent);
    const container = document.getElementById('chiTietPhanPhoiContainer');
    const btnThemChiTiet = document.getElementById('btnThemChiTietPhanPhoi');
    const ngayBatDauDotInput = document.getElementById('ngayBatDauDotPhanPhoi');
    const trangThaiDotSelect = document.getElementById('trangThaiDotPhanPhoi');
    const form = document.getElementById('formSuaDotPhanPhoi');

    function chuyenDateTimeLocal(value) {
      if (!value) {
        return null;
      }

      return new Date(value);
    }

    function themTrangThaiDot(value, selectedValue) {
      const option = document.createElement('option');
      option.value = value;
      option.textContent = value;

      if (selectedValue && selectedValue === value) {
        option.selected = true;
      }

      trangThaiDotSelect.appendChild(option);
    }

    function capNhatTrangThaiDot() {
      const oldValue = trangThaiDotSelect.dataset.oldValue || '';
      const ngayBatDau = chuyenDateTimeLocal(ngayBatDauDotInput.value);
      const hienTai = new Date();

      trangThaiDotSelect.innerHTML = '';

      if (ngayBatDau && ngayBatDau > hienTai) {
        themTrangThaiDot('Đang chuẩn bị', oldValue);
      }

      themTrangThaiDot('Đang phân phối', oldValue);
      themTrangThaiDot('Hoàn thành', oldValue);
      themTrangThaiDot('Đã hủy', oldValue);

      const allowedValues = Array.from(trangThaiDotSelect.options).map(option => option.value);

      if (!allowedValues.includes(oldValue)) {
        trangThaiDotSelect.value = ngayBatDau && ngayBatDau > hienTai
          ? 'Đang chuẩn bị'
          : 'Đang phân phối';
      }
    }

    function timDiaDiemTheoThongTin(tinhThanh, phuongXa, chiTietDiaDiem) {
      return diaDiems.find(function (item) {
        return item.tinhThanh === tinhThanh
          && item.phuongXa === phuongXa
          && item.chiTietDiaDiem === chiTietDiaDiem;
      });
    }

    function capNhatSoThuTuVaName() {
      container.querySelectorAll('[data-detail-row]').forEach(function (card, detailIndex) {
        const title = card.querySelector('[data-detail-title]');

        if (title) {
          title.textContent = 'Chi tiết phân phối ' + (detailIndex + 1);
        }

        const datalist = card.querySelector('[data-danh-sach-dia-diem]');
        const chiTietInput = card.querySelector('[data-chi-tiet-dia-diem]');

        if (datalist && chiTietInput) {
          datalist.id = 'danhSachDiaDiem_' + detailIndex;
          chiTietInput.setAttribute('list', datalist.id);
        }

        card.querySelectorAll('[name]').forEach(function (input) {
          input.name = input.name.replace(/chiTiet\[\d+\]/, 'chiTiet[' + detailIndex + ']');
        });

        card.querySelectorAll('[data-hang-hoa-row]').forEach(function (row, hangHoaIndex) {
          row.querySelectorAll('[name]').forEach(function (input) {
            input.name = input.name.replace(/hangHoa\]\[\d+\]/, 'hangHoa][' + hangHoaIndex + ']');
          });
        });
      });
    }

    function resetThongTinDiaDiem(card) {
      const idDiaDiemCoSan = card.querySelector('[data-id-dia-diem-co-san]');
      const tinhThanh = card.querySelector('[data-tinh-thanh]');
      const phuongXa = card.querySelector('[data-phuong-xa]');
      const chiTietDiaDiem = card.querySelector('[data-chi-tiet-dia-diem]');
      const viDo = card.querySelector('[data-vi-do]');
      const kinhDo = card.querySelector('[data-kinh-do]');
      const datalist = card.querySelector('[data-danh-sach-dia-diem]');

      if (idDiaDiemCoSan) idDiaDiemCoSan.value = '';
      if (tinhThanh) tinhThanh.value = '';
      if (phuongXa) {
        phuongXa.dataset.oldValue = '';
        phuongXa.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
      }
      if (chiTietDiaDiem) chiTietDiaDiem.value = '';
      if (viDo) viDo.value = '';
      if (kinhDo) kinhDo.value = '';
      if (datalist) datalist.innerHTML = '';
    }

    function resetThongTinYeuCau(card) {
      const requestSelect = card.querySelector('[data-request-select]');
      const requestInfo = card.querySelector('[data-request-info]');

      if (requestSelect) {
        requestSelect.value = '';
      }

      if (requestInfo) {
        requestInfo.textContent = 'Chọn yêu cầu để xem địa điểm của yêu cầu.';
      }
    }

    function capNhatHienThiTheoLoai(card, coResetDuLieu = false) {
      const select = card.querySelector('[data-loai-phan-phoi]');
      const loai = select ? select.value : 'Địa điểm';

      card.classList.toggle('show-location', loai === 'Địa điểm' || loai === 'Địa điểm và yêu cầu');
      card.classList.toggle('show-request', loai === 'Địa điểm và yêu cầu' || loai === 'Yêu cầu');

      if (coResetDuLieu) {
        if (loai === 'Yêu cầu') {
          resetThongTinDiaDiem(card);
        }

        if (loai === 'Địa điểm') {
          resetThongTinYeuCau(card);
        }
      }

      setTimeout(function () {
        if (card._phanPhoiMap) {
          card._phanPhoiMap.map.invalidateSize();
        }
      }, 150);
    }

    function capNhatRequestInfo(card) {
      const select = card.querySelector('[data-request-select]');
      const info = card.querySelector('[data-request-info]');

      if (!select || !info) {
        return;
      }

      const option = select.options[select.selectedIndex];
      const diaChi = option ? option.getAttribute('data-dia-chi') : '';

      if (select.value && diaChi) {
        info.textContent = 'Địa điểm yêu cầu: ' + diaChi;
      } else {
        info.textContent = 'Chọn yêu cầu để xem địa điểm của yêu cầu.';
      }
    }

    function loadPhuongXa(card) {
      const tinhThanhSelect = card.querySelector('[data-tinh-thanh]');
      const phuongXaSelect = card.querySelector('[data-phuong-xa]');
      const oldPhuongXa = phuongXaSelect.dataset.oldValue || '';

      phuongXaSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';

      const tinhThanh = tinhThanhSelect.value;

      if (!tinhThanh) {
        return;
      }

      const phuongXas = [...new Set(
        diaDiems
          .filter(function (item) {
            return item.tinhThanh === tinhThanh && item.phuongXa;
          })
          .map(function (item) {
            return item.phuongXa;
          })
      )].sort(function (a, b) {
        return a.localeCompare(b, 'vi');
      });

      phuongXas.forEach(function (phuongXa) {
        const option = document.createElement('option');
        option.value = phuongXa;
        option.textContent = phuongXa;

        if (oldPhuongXa && oldPhuongXa === phuongXa) {
          option.selected = true;
        }

        phuongXaSelect.appendChild(option);
      });

      loadDiaDiemOptions(card);
    }

    function loadDiaDiemOptions(card) {
      const tinhThanh = card.querySelector('[data-tinh-thanh]').value;
      const phuongXa = card.querySelector('[data-phuong-xa]').value;
      const datalist = card.querySelector('[data-danh-sach-dia-diem]');

      datalist.innerHTML = '';

      diaDiems
        .filter(function (item) {
          return item.tinhThanh === tinhThanh
            && item.phuongXa === phuongXa
            && item.chiTietDiaDiem;
        })
        .forEach(function (diaDiem) {
          const option = document.createElement('option');
          option.value = diaDiem.chiTietDiaDiem;
          datalist.appendChild(option);
        });
    }

    function ganSuKienMap(card) {
      if (!window.L) {
        return;
      }

      const mapElement = card.querySelector('[data-map]');

      if (!mapElement || mapElement.dataset.mapReady === '1') {
        return;
      }

      mapElement.dataset.mapReady = '1';

      const viDoInput = card.querySelector('[data-vi-do]');
      const kinhDoInput = card.querySelector('[data-kinh-do]');

      const viDoMacDinh = parseFloat(viDoInput.value) || 16.047079;
      const kinhDoMacDinh = parseFloat(kinhDoInput.value) || 108.206230;

      const map = L.map(mapElement).setView([viDoMacDinh, kinhDoMacDinh], 12);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);

      let marker = null;

      function setLocation(lat, lng) {
        viDoInput.value = Number(lat).toFixed(7);
        kinhDoInput.value = Number(lng).toFixed(7);

        if (marker) {
          marker.setLatLng([lat, lng]);
        } else {
          marker = L.marker([lat, lng]).addTo(map);
        }

        map.setView([lat, lng], 16);
      }

      if (viDoInput.value && kinhDoInput.value) {
        setLocation(parseFloat(viDoInput.value), parseFloat(kinhDoInput.value));
      }

      map.on('click', function (event) {
        setLocation(event.latlng.lat, event.latlng.lng);
      });

      card._phanPhoiMap = {
        map: map,
        setLocation: setLocation
      };

      setTimeout(function () {
        map.invalidateSize();
      }, 250);
    }

    function timTrenBanDo(card) {
      const chiTiet = card.querySelector('[data-chi-tiet-dia-diem]').value.trim();
      const phuongXa = card.querySelector('[data-phuong-xa]').value.trim();
      const tinhThanh = card.querySelector('[data-tinh-thanh]').value.trim();

      const diaChi = [chiTiet, phuongXa, tinhThanh, 'Việt Nam']
        .filter(Boolean)
        .join(', ');

      if (!tinhThanh && !phuongXa && !chiTiet) {
        alert('Vui lòng nhập ít nhất một thông tin địa chỉ trước khi tìm.');
        return;
      }

      fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(diaChi))
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (!data || data.length === 0) {
            alert('Không tìm thấy địa điểm phù hợp. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
            return;
          }

          const lat = parseFloat(data[0].lat);
          const lng = parseFloat(data[0].lon);

          if (card._phanPhoiMap) {
            card._phanPhoiMap.setLocation(lat, lng);
          }
        })
        .catch(function () {
          alert('Không thể tìm địa điểm lúc này. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
        });
    }

    function capNhatDiaDiemCoSan(card) {
      const tinhThanh = card.querySelector('[data-tinh-thanh]').value;
      const phuongXa = card.querySelector('[data-phuong-xa]').value;
      const chiTietDiaDiem = card.querySelector('[data-chi-tiet-dia-diem]').value;
      const idDiaDiemCoSan = card.querySelector('[data-id-dia-diem-co-san]');

      const diaDiem = timDiaDiemTheoThongTin(tinhThanh, phuongXa, chiTietDiaDiem);

      if (diaDiem) {
        idDiaDiemCoSan.value = diaDiem.idDiaDiem;

        if (diaDiem.viDo && diaDiem.kinhDo && card._phanPhoiMap) {
          card._phanPhoiMap.setLocation(parseFloat(diaDiem.viDo), parseFloat(diaDiem.kinhDo));
        }
      } else {
        idDiaDiemCoSan.value = '';
      }
    }

    function laySoLuongHienCo(row) {
      const select = row.querySelector('[data-nguon-luc-select]');
      const option = select && select.selectedIndex >= 0
        ? select.options[select.selectedIndex]
        : null;

      if (!option || !option.value) {
        return {
          amount: '',
          unit: ''
        };
      }

      let amount = parseFloat(option.getAttribute('data-so-luong')) || 0;
      const unit = option.getAttribute('data-don-vi') || '';

      const oldNguonLucId = select.getAttribute('data-old-nguon-luc-id') || '';
      const oldSoLuong = parseFloat(select.getAttribute('data-old-so-luong')) || 0;
      const oldAllocated = select.getAttribute('data-old-allocated') !== '0';

      if (oldAllocated && String(option.value) === String(oldNguonLucId)) {
        amount += oldSoLuong;
      }

      return {
        amount: amount,
        unit: unit
      };
    }

    function capNhatSoLuongHienCo(row) {
      const input = row.querySelector('[data-so-luong-giao]');
      const text = row.querySelector('[data-so-luong-hien-co]');
      const soLuong = laySoLuongHienCo(row);

      if (soLuong.amount !== '') {
        text.textContent = soLuong.amount.toFixed(2) + ' ' + soLuong.unit;
        input.setAttribute('max', soLuong.amount);
      } else {
        text.textContent = '-';
        input.removeAttribute('max');
      }
    }

    function locHangHoaTheoDanhMuc(row) {
      const danhMucSelect = row.querySelector('[data-danh-muc-filter]');
      const nguonLucSelect = row.querySelector('[data-nguon-luc-select]');
      const danhMuc = danhMucSelect.value;

      Array.from(nguonLucSelect.options).forEach(function (option) {
        if (!option.value) {
          option.hidden = false;
          return;
        }

        const optionDanhMuc = option.getAttribute('data-danh-muc') || '';
        option.hidden = danhMuc !== '' && optionDanhMuc !== danhMuc;
      });

      const selectedOption = nguonLucSelect.options[nguonLucSelect.selectedIndex];

      if (selectedOption && selectedOption.hidden) {
        nguonLucSelect.value = '';
        capNhatSoLuongHienCo(row);
      }
    }

    function ganSuKienHangHoa(row) {
      const danhMucSelect = row.querySelector('[data-danh-muc-filter]');
      const nguonLucSelect = row.querySelector('[data-nguon-luc-select]');
      const removeButton = row.querySelector('[data-remove-hang-hoa]');

      if (danhMucSelect) {
        danhMucSelect.addEventListener('change', function () {
          locHangHoaTheoDanhMuc(row);
        });
      }

      if (nguonLucSelect) {
        nguonLucSelect.addEventListener('change', function () {
          const selectedOption = nguonLucSelect.options[nguonLucSelect.selectedIndex];
          const danhMuc = selectedOption ? selectedOption.getAttribute('data-danh-muc') : '';

          if (danhMuc && danhMucSelect && !danhMucSelect.value) {
            danhMucSelect.value = danhMuc;
            locHangHoaTheoDanhMuc(row);
            nguonLucSelect.value = selectedOption.value;
          }

          capNhatSoLuongHienCo(row);
        });
      }

      if (removeButton) {
        removeButton.addEventListener('click', function () {
          const tbody = row.closest('[data-hang-hoa-container]');
          const rows = tbody.querySelectorAll('[data-hang-hoa-row]');

          if (rows.length <= 1) {
            return;
          }

          row.remove();
          capNhatSoThuTuVaName();
        });
      }

      capNhatSoLuongHienCo(row);
      locHangHoaTheoDanhMuc(row);
    }

    function ganSuKienCard(card) {
      const loaiSelect = card.querySelector('[data-loai-phan-phoi]');
      const requestSelect = card.querySelector('[data-request-select]');
      const tinhThanhSelect = card.querySelector('[data-tinh-thanh]');
      const phuongXaSelect = card.querySelector('[data-phuong-xa]');
      const chiTietDiaDiemInput = card.querySelector('[data-chi-tiet-dia-diem]');
      const timTrenBanDoButton = card.querySelector('[data-tim-tren-ban-do]');
      const addHangHoaButton = card.querySelector('[data-add-hang-hoa]');
      const removeDetailButton = card.querySelector('[data-remove-detail]');

      if (loaiSelect) {
        loaiSelect.addEventListener('change', function () {
          capNhatHienThiTheoLoai(card, true);
        });
      }

      if (requestSelect) {
        requestSelect.addEventListener('change', function () {
          capNhatRequestInfo(card);
        });
      }

      if (tinhThanhSelect) {
        tinhThanhSelect.addEventListener('change', function () {
          phuongXaSelect.dataset.oldValue = '';
          loadPhuongXa(card);
          capNhatDiaDiemCoSan(card);
        });
      }

      if (phuongXaSelect) {
        phuongXaSelect.addEventListener('change', function () {
          loadDiaDiemOptions(card);
          capNhatDiaDiemCoSan(card);
        });
      }

      if (chiTietDiaDiemInput) {
        chiTietDiaDiemInput.addEventListener('input', function () {
          capNhatDiaDiemCoSan(card);
        });
      }

      if (timTrenBanDoButton) {
        timTrenBanDoButton.addEventListener('click', function () {
          timTrenBanDo(card);
        });
      }

      if (addHangHoaButton) {
        addHangHoaButton.addEventListener('click', function () {
          const tbody = card.querySelector('[data-hang-hoa-container]');
          const firstRow = tbody.querySelector('[data-hang-hoa-row]');
          const newRow = firstRow.cloneNode(true);

          newRow.querySelectorAll('input, select').forEach(function (input) {
            input.value = '';
          });

          const hiddenId = newRow.querySelector('[data-id-chi-tiet-phan-phoi]');
          if (hiddenId) {
            hiddenId.value = '';
          }

          const nguonLucSelect = newRow.querySelector('[data-nguon-luc-select]');
          if (nguonLucSelect) {
            nguonLucSelect.setAttribute('data-old-nguon-luc-id', '');
            nguonLucSelect.setAttribute('data-old-so-luong', '0');
            nguonLucSelect.setAttribute('data-old-allocated', '0');
          }

          const statusSelect = newRow.querySelector('[data-trang-thai-chi-tiet]');
          if (statusSelect) {
            statusSelect.value = 'Chưa giao';
          }

          tbody.appendChild(newRow);
          capNhatSoThuTuVaName();
          ganSuKienHangHoa(newRow);
        });
      }

      if (removeDetailButton) {
        removeDetailButton.addEventListener('click', function () {
          const cards = container.querySelectorAll('[data-detail-row]');

          if (cards.length <= 1) {
            return;
          }

          card.remove();
          capNhatSoThuTuVaName();
        });
      }

      card.querySelectorAll('[data-hang-hoa-row]').forEach(function (row) {
        ganSuKienHangHoa(row);
      });

      loadPhuongXa(card);
      loadDiaDiemOptions(card);
      capNhatHienThiTheoLoai(card);
      capNhatRequestInfo(card);
      ganSuKienMap(card);
      capNhatDiaDiemCoSan(card);
    }

    container.querySelectorAll('[data-detail-row]').forEach(function (card) {
      ganSuKienCard(card);
    });

    btnThemChiTiet.addEventListener('click', function () {
      const firstCard = container.querySelector('[data-detail-row]');
      const newCard = firstCard.cloneNode(true);

      newCard.querySelectorAll('input, textarea, select').forEach(function (input) {
        if (input.matches('[data-loai-phan-phoi]')) {
          input.value = 'Địa điểm';
          return;
        }

        input.value = '';
      });

      newCard.querySelectorAll('[data-hang-hoa-row]').forEach(function (row, index) {
        if (index > 0) {
          row.remove();
        }
      });

      newCard.querySelectorAll('[data-id-chi-tiet-phan-phoi]').forEach(function (input) {
        input.value = '';
      });

      newCard.querySelectorAll('[data-nguon-luc-select]').forEach(function (select) {
        select.setAttribute('data-old-nguon-luc-id', '');
        select.setAttribute('data-old-so-luong', '0');
        select.setAttribute('data-old-allocated', '0');
      });

      const oldMap = newCard.querySelector('[data-map]');
      if (oldMap) {
        const freshMap = document.createElement('div');
        freshMap.className = 'phan-phoi-map';
        freshMap.setAttribute('data-map', '');
        oldMap.replaceWith(freshMap);
      }

      delete newCard._phanPhoiMap;

      container.appendChild(newCard);

      capNhatSoThuTuVaName();
      ganSuKienCard(newCard);

      setTimeout(function () {
        if (newCard._phanPhoiMap) {
          newCard._phanPhoiMap.map.invalidateSize();
        }
      }, 250);
    });

    ngayBatDauDotInput.addEventListener('change', function () {
      trangThaiDotSelect.dataset.oldValue = '';
      capNhatTrangThaiDot();
    });

    form.addEventListener('submit', function (event) {
      const trangThai = trangThaiDotSelect.value;

      if (trangThai === 'Hoàn thành') {
        if (!confirm('Bạn có chắc muốn hoàn thành đợt phân phối không? Sau khi lưu, đợt này sẽ không thể sửa nữa. Các chi tiết đang Chưa giao sẽ chuyển sang Đã giao.')) {
          event.preventDefault();
        }
      }

      if (trangThai === 'Đã hủy') {
        if (!confirm('Bạn có chắc muốn hủy đợt phân phối không? Sau khi lưu, đợt này sẽ không thể sửa nữa. Các chi tiết đang Chưa giao sẽ chuyển sang Đã hủy.')) {
          event.preventDefault();
        }
      }
    });

    capNhatTrangThaiDot();
    capNhatSoThuTuVaName();
  });
</script>
@endsection