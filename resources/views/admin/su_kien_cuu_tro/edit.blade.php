@extends('layouts.admin')

@section('title', 'Sửa sự kiện cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Sửa sự kiện cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/su-kien-cuu-tro?loai=' . urlencode($suKien->loaiSuKien)) }}">Sự kiện cứu trợ</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Sửa</li>
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

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Thông tin sự kiện cứu trợ</h5>
  </div>

  <div class="card-body">
    <form action="{{ url('/admin/su-kien-cuu-tro/' . $suKien->idSuKien) }}"
          method="POST"
          autocomplete="off"
          onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerText = 'Đang cập nhật...';">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Tên sự kiện <span class="text-danger">*</span></label>
        <input type="text"
               name="tenSuKien"
               class="form-control"
               value="{{ old('tenSuKien', $suKien->tenSuKien) }}"
               autocomplete="off">
      </div>

      <div class="mb-3">
        <label class="form-label">Loại sự kiện <span class="text-danger">*</span></label>
        <select name="loaiSuKien"
                id="loaiSuKien"
                class="form-control"
                autocomplete="off">
          <option value="Khẩn cấp" {{ old('loaiSuKien', $suKien->loaiSuKien) == 'Khẩn cấp' ? 'selected' : '' }}>
            Khẩn cấp
          </option>
          <option value="Thường nhật" {{ old('loaiSuKien', $suKien->loaiSuKien) == 'Thường nhật' ? 'selected' : '' }}>
            Thường nhật
          </option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="moTa"
                  class="form-control"
                  rows="4"
                  autocomplete="off">{{ old('moTa', $suKien->moTa) }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
        <select name="trangThai"
                id="trangThai"
                class="form-control"
                autocomplete="off"
                data-old="{{ old('trangThai', $suKien->trangThai == 'Đã ẩn' ? 'Ẩn' : $suKien->trangThai) }}">
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          Cập nhật
        </button>

        <a href="{{ url('/admin/su-kien-cuu-tro?loai=' . urlencode(old('loaiSuKien', $suKien->loaiSuKien))) }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const loaiSelect = document.getElementById('loaiSuKien');
    const trangThaiSelect = document.getElementById('trangThai');
    let oldTrangThai = trangThaiSelect.dataset.old || '';

    const trangThaiTheoLoai = {
      'Khẩn cấp': [
        'Sắp diễn ra',
        'Đang diễn ra',
        'Đã kết thúc',
        'Ẩn'
      ],
      'Thường nhật': [
        'Đang diễn ra',
        'Ẩn'
      ]
    };

    function capNhatTrangThai() {
      const loai = loaiSelect.value;
      const danhSachTrangThai = trangThaiTheoLoai[loai] || [];

      trangThaiSelect.innerHTML = '';

      danhSachTrangThai.forEach(function (trangThai) {
        const option = document.createElement('option');
        option.value = trangThai;
        option.textContent = trangThai;

        if (oldTrangThai === trangThai) {
          option.selected = true;
        }

        trangThaiSelect.appendChild(option);
      });

      if (!danhSachTrangThai.includes(oldTrangThai)) {
        trangThaiSelect.value = 'Đang diễn ra';
      }
    }

    loaiSelect.addEventListener('change', function () {
      oldTrangThai = '';
      capNhatTrangThai();
    });

    capNhatTrangThai();
  });
</script>
@endsection