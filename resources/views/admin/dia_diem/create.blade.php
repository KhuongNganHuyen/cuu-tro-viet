@extends('layouts.admin')

@section('title', 'Thêm địa điểm | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #createDiaDiemMap {
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
          <h5 class="m-b-10">Thêm địa điểm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dia-diem') }}">Địa điểm</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>Thông tin địa điểm</h5>
  </div>

  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ url('/admin/dia-diem') }}" method="POST">
      @csrf

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Tỉnh/Thành <span class="text-danger">*</span></label>
          <input type="text" id="tinhThanh" name="tinhThanh" class="form-control"
            value="{{ old('tinhThanh') }}" placeholder="Ví dụ: Đà Nẵng">
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Phường/Xã</label>
          <input type="text" id="phuongXa" name="phuongXa" class="form-control"
            value="{{ old('phuongXa') }}" placeholder="Ví dụ: Hải Châu">
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Chi tiết địa điểm</label>
          <input type="text" id="chiTietDiaDiem" name="chiTietDiaDiem" class="form-control"
            value="{{ old('chiTietDiaDiem') }}" placeholder="Ví dụ: 48 Cao Thắng">
        </div>
      </div>

      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label mb-0">Chọn vị trí trên bản đồ</label>
          <button type="button" id="btnTimTrenBanDo" class="btn btn-sm btn-outline-primary">
            Tìm trên bản đồ
          </button>
        </div>

        <small class="text-muted">
          Bạn có thể bấm “Tìm trên bản đồ” sau khi nhập địa chỉ hoặc click trực tiếp lên bản đồ để chọn vị trí chính xác.
        </small>

        <div id="createDiaDiemMap"></div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Vĩ độ</label>
          <input type="text" id="viDo" name="viDo" class="form-control"
            value="{{ old('viDo') }}" readonly>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Kinh độ</label>
          <input type="text" id="kinhDo" name="kinhDo" class="form-control"
            value="{{ old('kinhDo') }}" readonly>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ url('/admin/dia-diem') }}" class="btn btn-secondary">Quay lại</a>
      </div>
    </form>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  const defaultLat = 16.047079;
  const defaultLng = 108.206230;

  const map = L.map('createDiaDiemMap').setView([defaultLat, defaultLng], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  let marker = null;

  function setLocation(lat, lng) {
    document.getElementById('viDo').value = lat.toFixed(7);
    document.getElementById('kinhDo').value = lng.toFixed(7);

    if (marker) {
      marker.setLatLng([lat, lng]);
    } else {
      marker = L.marker([lat, lng]).addTo(map);
    }

    map.setView([lat, lng], 15);
  }

  map.on('click', function(e) {
    setLocation(e.latlng.lat, e.latlng.lng);
  });

  document.getElementById('btnTimTrenBanDo').addEventListener('click', function() {
    const chiTiet = document.getElementById('chiTietDiaDiem').value.trim();
    const phuongXa = document.getElementById('phuongXa').value.trim();
    const tinhThanh = document.getElementById('tinhThanh').value.trim();

    const diaChi = [chiTiet, phuongXa, tinhThanh, 'Việt Nam']
      .filter(Boolean)
      .join(', ');

    if (!tinhThanh && !phuongXa && !chiTiet) {
      alert('Vui lòng nhập ít nhất một thông tin địa chỉ trước khi tìm.');
      return;
    }

    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(diaChi))
      .then(response => response.json())
      .then(data => {
        if (!data || data.length === 0) {
          alert('Không tìm thấy địa điểm phù hợp. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
          return;
        }

        const lat = parseFloat(data[0].lat);
        const lng = parseFloat(data[0].lon);

        setLocation(lat, lng);
      })
      .catch(() => {
        alert('Không thể tìm địa điểm lúc này. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
      });
  });
</script>
@endsection