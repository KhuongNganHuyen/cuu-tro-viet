@extends('layouts.admin')

@section('title', 'Chi tiết địa điểm | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #showDiaDiemMap {
    height: 420px;
    width: 100%;
    border-radius: 12px;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết địa điểm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dia-diem') }}">Địa điểm</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Chi tiết</li>
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

<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Thông tin địa điểm</h5>

    <div class="d-flex gap-2">
      <a href="{{ url('/admin/dia-diem') }}" class="btn btn-secondary">
        Quay lại
      </a>

      <a href="{{ url('/admin/dia-diem/' . $diaDiem->idDiaDiem . '/edit') }}" class="btn btn-warning">
        Sửa
      </a>

      <form action="{{ url('/admin/dia-diem/' . $diaDiem->idDiaDiem) }}" method="POST"
        onsubmit="return confirm('Bạn có chắc muốn xóa địa điểm này không?')">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-danger">
          Xóa
        </button>
      </form>
    </div>
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-3 text-muted">Mã địa điểm</div>
      <div class="col-md-9">{{ $diaDiem->idDiaDiem }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Tỉnh/Thành</div>
      <div class="col-md-9">{{ $diaDiem->tinhThanh }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Phường/Xã</div>
      <div class="col-md-9">{{ $diaDiem->phuongXa ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Chi tiết địa điểm</div>
      <div class="col-md-9">{{ $diaDiem->chiTietDiaDiem ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Vĩ độ</div>
      <div class="col-md-9">
        @if ($diaDiem->viDo)
          {{ $diaDiem->viDo }}
        @else
          <span class="text-danger">Chưa có</span>
        @endif
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Kinh độ</div>
      <div class="col-md-9">
        @if ($diaDiem->kinhDo)
          {{ $diaDiem->kinhDo }}
        @else
          <span class="text-danger">Chưa có</span>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Vị trí trên bản đồ</h5>
  </div>

  <div class="card-body">
    @if ($diaDiem->viDo && $diaDiem->kinhDo)
      <div id="showDiaDiemMap"></div>
    @else
      <div class="alert alert-warning mb-0">
        Địa điểm này chưa có tọa độ. Vui lòng bấm <strong>Sửa</strong> để bổ sung vĩ độ và kinh độ.
      </div>
    @endif
  </div>
</div>

@if ($diaDiem->viDo && $diaDiem->kinhDo)
<div
  id="diaDiemData"
  data-vi-do="{{ $diaDiem->viDo }}"
  data-kinh-do="{{ $diaDiem->kinhDo }}"
  data-tinh-thanh="{{ $diaDiem->tinhThanh }}"
  data-phuong-xa="{{ $diaDiem->phuongXa }}"
  data-chi-tiet-dia-diem="{{ $diaDiem->chiTietDiaDiem }}">
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  const diaDiemData = document.getElementById('diaDiemData');

  const lat = parseFloat(diaDiemData.dataset.viDo);
  const lng = parseFloat(diaDiemData.dataset.kinhDo);

  const tinhThanh = diaDiemData.dataset.tinhThanh || 'Địa điểm';
  const phuongXa = diaDiemData.dataset.phuongXa || '';
  const chiTietDiaDiem = diaDiemData.dataset.chiTietDiaDiem || '';

  const map = L.map('showDiaDiemMap').setView([lat, lng], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  const diaChi = [
    chiTietDiaDiem,
    phuongXa,
    tinhThanh
  ].filter(Boolean).join(', ');

  L.marker([lat, lng])
    .addTo(map)
    .bindPopup(`
      <strong>${tinhThanh}</strong><br>
      ${diaChi || 'Chưa có thông tin địa chỉ'}
    `)
    .openPopup();
</script>
@endif
@endsection