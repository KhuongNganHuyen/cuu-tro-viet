@extends('layouts.nhom')

@section('title', 'Tạo đợt phân phối | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tạo đợt phân phối</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">Tổng quan nhóm</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}">Chiến dịch</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}">
              {{ $chienDich->tenChienDich }}
            </a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Tạo đợt phân phối</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

@if ($tiepNhanYeuCaus->count() == 0)
  <div class="alert alert-warning">
    Chiến dịch chưa có yêu cầu cứu trợ nào được tiếp nhận. Cần tiếp nhận yêu cầu trước khi phân phối.
  </div>
@endif

@if ($nguonLucs->count() == 0)
  <div class="alert alert-warning">
    Chiến dịch chưa có nguồn lực khả dụng. Cần xác nhận đóng góp trước khi phân phối.
  </div>
@endif

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Thông tin phân phối</h5>
  </div>

  <div class="card-body">
    <form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Yêu cầu cứu trợ cần phân phối <span class="text-danger">*</span></label>
        <select name="idTiepNhan" class="form-control">
          <option value="">-- Chọn yêu cầu --</option>
          @foreach ($tiepNhanYeuCaus as $tiepNhan)
            <option value="{{ $tiepNhan->idTiepNhan }}"
              {{ old('idTiepNhan') == $tiepNhan->idTiepNhan ? 'selected' : '' }}>
              #{{ $tiepNhan->yeuCau->idYeuCau ?? '' }}
              - {{ $tiepNhan->yeuCau->loaiYeuCau ?? '-' }}
              - {{ $tiepNhan->yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
              {{ $tiepNhan->yeuCau->diaDiem->phuongXa ?? '' }},
              {{ $tiepNhan->yeuCau->diaDiem->tinhThanh ?? '' }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày phân phối <span class="text-danger">*</span></label>
          <input type="datetime-local" name="ngayPhanPhoi" class="form-control"
            value="{{ old('ngayPhanPhoi', date('Y-m-d\TH:i')) }}">
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Người nhận</label>
          <input type="text" name="nguoiNhan" class="form-control"
            value="{{ old('nguoiNhan') }}"
            placeholder="Ví dụ: Đại diện hộ dân / Trưởng khu vực">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Ghi chú</label>
        <textarea name="ghiChu" class="form-control" rows="3"
          placeholder="Ghi chú thêm về đợt phân phối.">{{ old('ghiChu') }}</textarea>
      </div>

      <hr>

      <h6 class="mb-3">Nguồn lực phân phối</h6>

      <div id="nguonLucContainer">
        <div class="row align-items-end mb-2 nguon-luc-row">
          <div class="col-md-7">
            <label class="form-label">Nguồn lực <span class="text-danger">*</span></label>
            <select name="idNguonLuc[]" class="form-control nguon-luc-select">
              <option value="">-- Chọn nguồn lực --</option>
              @foreach ($nguonLucs as $nguonLuc)
                <option value="{{ $nguonLuc->idNguonLuc }}"
                  data-soluong="{{ $nguonLuc->soLuongHienCo }}"
                  data-donvi="{{ $nguonLuc->hangHoa->donViTinh ?? '' }}">
                  {{ $nguonLuc->hangHoa->tenHangHoa ?? '-' }}
                  - còn {{ $nguonLuc->soLuongHienCo }} {{ $nguonLuc->hangHoa->donViTinh ?? '' }}
                  @if ($nguonLuc->hanSuDung)
                    - HSD: {{ $nguonLuc->hanSuDung }}
                  @endif
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Số lượng giao <span class="text-danger">*</span></label>
            <input type="number" name="soLuongGiao[]" class="form-control so-luong-input"
              min="1" step="0.01" placeholder="Nhập số lượng">
            <small class="text-muted so-luong-hien-co"></small>
          </div>

          <div class="col-md-2">
            <button type="button" class="btn btn-light border text-danger btn-remove-row w-100">
              Xóa
            </button>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <button type="button" class="btn btn-outline-primary" id="btnThemNguonLuc">
          Thêm nguồn lực
        </button>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"
          onclick="return confirm('Tạo đợt phân phối và trừ nguồn lực?')"
          {{ $tiepNhanYeuCaus->count() == 0 || $nguonLucs->count() == 0 ? 'disabled' : '' }}>
          Lưu phân phối
        </button>

        <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('nguonLucContainer');
    const btnThem = document.getElementById('btnThemNguonLuc');

    function capNhatSoLuong(row) {
      const select = row.querySelector('.nguon-luc-select');
      const input = row.querySelector('.so-luong-input');
      const text = row.querySelector('.so-luong-hien-co');

      const option = select.options[select.selectedIndex];
      const soLuong = option ? option.getAttribute('data-soluong') : '';
      const donVi = option ? option.getAttribute('data-donvi') : '';

      if (soLuong) {
        text.textContent = 'Hiện có: ' + soLuong + ' ' + donVi;
        input.setAttribute('max', soLuong);
      } else {
        text.textContent = '';
        input.removeAttribute('max');
      }
    }

    function ganSuKien(row) {
      row.querySelector('.nguon-luc-select').addEventListener('change', function () {
        capNhatSoLuong(row);
      });

      row.querySelector('.btn-remove-row').addEventListener('click', function () {
        const rows = container.querySelectorAll('.nguon-luc-row');

        if (rows.length > 1) {
          row.remove();
        }
      });
    }

    container.querySelectorAll('.nguon-luc-row').forEach(function (row) {
      ganSuKien(row);
    });

    btnThem.addEventListener('click', function () {
      const firstRow = container.querySelector('.nguon-luc-row');
      const newRow = firstRow.cloneNode(true);

      newRow.querySelector('.nguon-luc-select').value = '';
      newRow.querySelector('.so-luong-input').value = '';
      newRow.querySelector('.so-luong-hien-co').textContent = '';

      container.appendChild(newRow);
      ganSuKien(newRow);
    });
  });
</script>
@endsection