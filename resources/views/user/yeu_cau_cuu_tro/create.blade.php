@extends('layouts.user')

@section('title', 'Gửi yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Gửi yêu cầu cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/user/yeu-cau-cuu-tro') }}">Yêu cầu cứu trợ</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Gửi yêu cầu</li>
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
    <h5 class="mb-0">Thông tin yêu cầu cứu trợ</h5>
  </div>

  <div class="card-body">
    <form action="{{ url('/user/yeu-cau-cuu-tro') }}"
      method="POST"
      enctype="multipart/form-data"
      onsubmit="return confirm('Bạn hãy kiểm tra kỹ thông tin trước khi gửi. Sau khi gửi yêu cầu, bạn sẽ không thể tự chỉnh sửa nội dung này. Bạn chắc chắn muốn gửi yêu cầu cứu trợ không?')">
      @csrf

      <div class="alert alert-warning">
        <strong>Lưu ý:</strong> Sau khi gửi yêu cầu cứu trợ, bạn sẽ không thể tự chỉnh sửa nội dung đã gửi.
        Vui lòng kiểm tra kỹ tiêu đề, mô tả, số người cần hỗ trợ, địa điểm và hình ảnh minh chứng trước khi bấm gửi.
      </div>

      <div class="mb-3">
        <label class="form-label">Tiêu đề yêu cầu <span class="text-danger">*</span></label>
        <input type="text" name="tieuDeYeuCau" class="form-control"
          value="{{ old('tieuDeYeuCau') }}"
          placeholder="Ví dụ: Cần hỗ trợ lương thực cho 5 người, Cần thuốc men và nước uống">
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả tình hình <span class="text-danger">*</span></label>
        <textarea name="moTa" class="form-control" rows="4"
          placeholder="Mô tả tình hình hiện tại, nhu cầu cần hỗ trợ...">{{ old('moTa') }}</textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Số người cần hỗ trợ</label>
          <input type="number" name="soNguoi" class="form-control"
            value="{{ old('soNguoi') }}"
            min="1"
            placeholder="Ví dụ: 5">
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Mức độ khẩn cấp <span class="text-danger">*</span></label>
          <select name="mucDoKhanCap" class="form-control">
            <option value="">-- Chọn mức độ --</option>
            <option value="Thấp" {{ old('mucDoKhanCap') == 'Thấp' ? 'selected' : '' }}>Thấp</option>
            <option value="Trung bình" {{ old('mucDoKhanCap') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
            <option value="Cao" {{ old('mucDoKhanCap') == 'Cao' ? 'selected' : '' }}>Cao</option>
            <option value="Khẩn cấp" {{ old('mucDoKhanCap') == 'Khẩn cấp' ? 'selected' : '' }}>Khẩn cấp</option>
          </select>
        </div>
      </div>

      <hr>

      <h6 class="mb-3">Địa điểm cần hỗ trợ</h6>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Tỉnh/Thành <span class="text-danger">*</span></label>
          <input type="text" name="tinhThanh" id="tinhThanh" class="form-control"
            value="{{ old('tinhThanh') }}"
            placeholder="Ví dụ: Đà Nẵng">
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Phường/Xã</label>
          <input type="text" name="phuongXa" id="phuongXa" class="form-control"
            value="{{ old('phuongXa') }}"
            placeholder="Ví dụ: Hòa Khánh Bắc">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
        <input type="text" name="chiTietDiaDiem" id="chiTietDiaDiem" class="form-control"
          value="{{ old('chiTietDiaDiem') }}"
          placeholder="Ví dụ: Kiệt 12, đường Nguyễn Lương Bằng">
      </div>

      <input type="hidden" name="viDo" id="viDo" value="{{ old('viDo') }}">
      <input type="hidden" name="kinhDo" id="kinhDo" value="{{ old('kinhDo') }}">

      <div class="mb-3">
        <button type="button" class="btn btn-outline-primary" id="btnTimBanDo">
          Tìm trên bản đồ
        </button>
        <small class="text-muted ms-2">
          Có thể click trên bản đồ để chọn chính xác vị trí.
        </small>
      </div>

      <div id="map" style="height: 350px;" class="mb-3 rounded border"></div>

      <div class="mb-3">
        <label class="form-label">Hình ảnh minh chứng</label>
        <input type="file" name="hinhAnh" class="form-control" accept="image/*">
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          Gửi yêu cầu
        </button>

        <a href="{{ url('/user/yeu-cau-cuu-tro') }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </form>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    let defaultLat = 16.047079;
    let defaultLng = 108.206230;

    let oldLat = document.getElementById('viDo').value;
    let oldLng = document.getElementById('kinhDo').value;

    let map = L.map('map').setView([
      oldLat || defaultLat,
      oldLng || defaultLng
    ], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
    }).addTo(map);

    let marker = null;

    function setMarker(lat, lng) {
      document.getElementById('viDo').value = lat;
      document.getElementById('kinhDo').value = lng;

      if (marker) {
        marker.setLatLng([lat, lng]);
      } else {
        marker = L.marker([lat, lng]).addTo(map);
      }

      map.setView([lat, lng], 16);
    }

    if (oldLat && oldLng) {
      setMarker(oldLat, oldLng);
    }

    map.on('click', function (e) {
      setMarker(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('btnTimBanDo').addEventListener('click', function () {
      const tinhThanh = document.getElementById('tinhThanh').value;
      const phuongXa = document.getElementById('phuongXa').value;
      const chiTietDiaDiem = document.getElementById('chiTietDiaDiem').value;

      const diaChi = `${chiTietDiaDiem}, ${phuongXa}, ${tinhThanh}, Việt Nam`;

      fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(diaChi))
        .then(response => response.json())
        .then(data => {
          if (data.length > 0) {
            setMarker(data[0].lat, data[0].lon);
          } else {
            alert('Không tìm thấy địa điểm. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
          }
        })
        .catch(() => {
          alert('Không thể tìm địa điểm trên bản đồ.');
        });
    });
  });
</script>
@endsection