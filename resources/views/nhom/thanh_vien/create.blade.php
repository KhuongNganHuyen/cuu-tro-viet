@extends('layouts.nhom')

@section('title', 'Thêm thành viên nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm thành viên nhóm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien') }}">Thành viên</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien') }}" method="POST">
  @csrf

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Thông tin thành viên</h5>
    </div>

    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Người dùng <span class="text-danger">*</span></label>

        <input type="text" id="nguoiDungInput" class="form-control"
          list="danhSachNguoiDung"
          value="{{ old('tenNguoiDungHienThi') }}"
          placeholder="Nhập họ tên hoặc tên đăng nhập để thêm thành viên">

        <input type="hidden" name="idNguoiDung" id="idNguoiDung"
          value="{{ old('idNguoiDung') }}">

        <datalist id="danhSachNguoiDung"></datalist>
      </div>

      <div class="mb-3">
        <label class="form-label">Vai trò trong nhóm</label>

        <input type="text" name="vaiTro" id="vaiTro" class="form-control"
          value="{{ old('vaiTro') }}"
          placeholder="Thành viên">

        <small class="text-muted">
          Hệ thống sẽ mặc định là “Thành viên”. Không nhập “Nhóm trưởng” tại đây.
        </small>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          Lưu
        </button>

        <a href="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien') }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>
  </div>
</form>

<script id="nguoiDungData" type="application/json">
{!! $nguoiDungJson !!}
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const nguoiDungs = JSON.parse(document.getElementById('nguoiDungData').textContent);

    const nguoiDungInput = document.getElementById('nguoiDungInput');
    const idNguoiDungInput = document.getElementById('idNguoiDung');
    const danhSachNguoiDung = document.getElementById('danhSachNguoiDung');

    const vaiTroInput = document.getElementById('vaiTro');

    function loadNguoiDungOptions() {
      danhSachNguoiDung.innerHTML = '';

      nguoiDungs.forEach(function (nguoiDung) {
        const option = document.createElement('option');
        option.value = nguoiDung.label;
        danhSachNguoiDung.appendChild(option);
      });
    }

    nguoiDungInput.addEventListener('input', function () {
      const selected = nguoiDungs.find(function (nguoiDung) {
        return nguoiDung.label === nguoiDungInput.value;
      });

      idNguoiDungInput.value = selected ? selected.idNguoiDung : '';
    });

    vaiTroInput.addEventListener('input', function () {
      const vaiTro = vaiTroInput.value.trim().toLowerCase();

      if (vaiTro === 'nhóm trưởng' || vaiTro === 'nhom truong') {
        vaiTroInput.setCustomValidity('Không thêm Nhóm trưởng tại đây. Nếu muốn chuyển nhượng nhóm trưởng, vui lòng vào phần Sửa thông tin nhóm.');
      } else {
        vaiTroInput.setCustomValidity('');
      }
    });

    loadNguoiDungOptions();
  });
</script>
@endsection