@extends('layouts.admin')

@section('title', 'Sửa địa điểm | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #editDiaDiemMap {
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
          <h5 class="m-b-10">Sửa địa điểm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dia-diem') }}">Địa điểm</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Sửa</li>
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

    <form action="{{ url('/admin/dia-diem/' . $diaDiem->idDiaDiem) }}" method="POST" autocomplete="off">
      @csrf
      @method('PUT')

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Tỉnh/Thành <span class="text-danger">*</span></label>
          <input type="text" id="tinhThanh" name="tinhThanh" class="form-control"
            value="{{ old('tinhThanh', $diaDiem->tinhThanh) }}"
            placeholder="Ví dụ: Đà Nẵng" autocomplete="off">
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Phường/Xã</label>
          <input type="text" id="phuongXa" name="phuongXa" class="form-control"
            value="{{ old('phuongXa', $diaDiem->phuongXa) }}"
            placeholder="Ví dụ: Hải Châu" autocomplete="off">
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Chi tiết địa điểm</label>
          <input type="text" id="chiTietDiaDiem" name="chiTietDiaDiem" class="form-control"
            value="{{ old('chiTietDiaDiem', $diaDiem->chiTietDiaDiem) }}"
            placeholder="Ví dụ: 48 Cao Thắng" autocomplete="off">
        </div>
      </div>

      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label mb-0">Vị trí trên bản đồ</label>
          <button type="button" id="btnTimTrenBanDo" class="btn btn-sm btn-outline-primary">
            Tìm trên bản đồ
          </button>
        </div>

        <small class="text-muted">
          Bản đồ sẽ hiển thị vị trí hiện tại nếu địa điểm đã có tọa độ. Bạn có thể bấm “Tìm trên bản đồ” hoặc click trực tiếp lên bản đồ để cập nhật vị trí.
        </small>

        <div id="editDiaDiemMap" class="mt-2"></div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Vĩ độ</label>
          <input type="text" id="viDo" name="viDo" class="form-control"
            value="{{ old('viDo', $diaDiem->viDo) }}" readonly>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Kinh độ</label>
          <input type="text" id="kinhDo" name="kinhDo" class="form-control"
            value="{{ old('kinhDo', $diaDiem->kinhDo) }}" readonly>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ url('/admin/dia-diem') }}" class="btn btn-secondary">Quay lại</a>
      </div>
    </form>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  const defaultLat = 16.047079;
  const defaultLng = 108.206230;

  const currentLatValue = document.getElementById('viDo').value;
  const currentLngValue = document.getElementById('kinhDo').value;

  const hasCurrentLocation = currentLatValue && currentLngValue;

  const startLat = hasCurrentLocation ? parseFloat(currentLatValue) : defaultLat;
  const startLng = hasCurrentLocation ? parseFloat(currentLngValue) : defaultLng;
  const startZoom = hasCurrentLocation ? 15 : 13;

  const map = L.map('editDiaDiemMap').setView([startLat, startLng], startZoom);

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

    marker.bindPopup('Vị trí đã chọn. Bạn có thể click lại nếu chưa chính xác.').openPopup();
    map.setView([lat, lng], 16);
  }

  if (hasCurrentLocation) {
    marker = L.marker([startLat, startLng]).addTo(map);
    marker.bindPopup('Vị trí hiện tại của địa điểm').openPopup();
  }

  map.on('click', function(e) {
    setLocation(e.latlng.lat, e.latlng.lng);
  });

  function boDauTiengViet(chuoi) {
    return chuoi
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/đ/g, 'd')
      .replace(/Đ/g, 'D');
  }

  function chuanHoaTinhThanh(chuoi) {
    return chuoi
      .trim()
      .replace(/^TP\.?\s+/i, '')
      .replace(/^Thành phố\s+/i, '')
      .replace(/^Thanh pho\s+/i, '')
      .replace(/^Tỉnh\s+/i, '')
      .replace(/^Tinh\s+/i, '')
      .trim();
  }

  function chuanHoaChiTiet(chuoi) {
    return chuoi
      .trim()
      .replace(/^Số\s+/i, '')
      .replace(/^So\s+/i, '')
      .replace(/^No\.?\s+/i, '')
      .replace(/\b0+(\d+)/g, '$1')
      .trim();
  }

  function taoDanhSachDiaChi() {
    const chiTietGoc = document.getElementById('chiTietDiaDiem').value.trim();
    const phuongXaGoc = document.getElementById('phuongXa').value.trim();
    const tinhThanhGoc = document.getElementById('tinhThanh').value.trim();

    const chiTiet = chuanHoaChiTiet(chiTietGoc);
    const phuongXa = chuanHoaTinhThanh(phuongXaGoc);
    const tinhThanh = chuanHoaTinhThanh(tinhThanhGoc);

    const chiTietKhongDau = boDauTiengViet(chiTiet);
    const phuongXaKhongDau = boDauTiengViet(phuongXa);
    const tinhThanhKhongDau = boDauTiengViet(tinhThanh);

    const danhSach = [];

    if (chiTietGoc && phuongXa && tinhThanh) {
      danhSach.push(`${chiTietGoc}, ${phuongXa}, ${tinhThanh}, Việt Nam`);
    }

    if (chiTiet && phuongXa && tinhThanh) {
      danhSach.push(`${chiTiet}, ${phuongXa}, ${tinhThanh}, Việt Nam`);
    }

    if (chiTiet && tinhThanh) {
      danhSach.push(`${chiTiet}, ${tinhThanh}, Việt Nam`);
    }

    if (chiTietKhongDau && phuongXaKhongDau && tinhThanhKhongDau) {
      danhSach.push(`${chiTietKhongDau}, ${phuongXaKhongDau}, ${tinhThanhKhongDau}, Vietnam`);
    }

    if (chiTietKhongDau && tinhThanhKhongDau) {
      danhSach.push(`${chiTietKhongDau}, ${tinhThanhKhongDau}, Vietnam`);
    }

    if (phuongXa && tinhThanh) {
      danhSach.push(`${phuongXa}, ${tinhThanh}, Việt Nam`);
    }

    if (tinhThanh) {
      danhSach.push(`${tinhThanh}, Việt Nam`);
    }

    return [...new Set(danhSach.filter(Boolean))];
  }

  function timBangNominatim(diaChi) {
    const url = 'https://nominatim.openstreetmap.org/search'
      + '?format=json'
      + '&limit=1'
      + '&countrycodes=vn'
      + '&accept-language=vi'
      + '&q=' + encodeURIComponent(diaChi);

    return fetch(url)
      .then(function(response) {
        if (!response.ok) {
          return null;
        }

        return response.json();
      })
      .then(function(data) {
        if (!data || data.length === 0) {
          return null;
        }

        return {
          lat: parseFloat(data[0].lat),
          lng: parseFloat(data[0].lon)
        };
      })
      .catch(function() {
        return null;
      });
  }

  function timBangPhoton(diaChi) {
    const url = 'https://photon.komoot.io/api/'
      + '?limit=1'
      + '&q=' + encodeURIComponent(diaChi);

    return fetch(url)
      .then(function(response) {
        if (!response.ok) {
          return null;
        }

        return response.json();
      })
      .then(function(data) {
        if (!data || !data.features || data.features.length === 0) {
          return null;
        }

        const toaDo = data.features[0].geometry.coordinates;

        return {
          lat: parseFloat(toaDo[1]),
          lng: parseFloat(toaDo[0])
        };
      })
      .catch(function() {
        return null;
      });
  }

  function timMotDiaChi(diaChi) {
    return timBangNominatim(diaChi).then(function(ketQuaNominatim) {
      if (ketQuaNominatim) {
        return ketQuaNominatim;
      }

      return timBangPhoton(diaChi);
    });
  }

  function timDiaChiTheoDanhSach(danhSachDiaChi, index = 0) {
    if (index >= danhSachDiaChi.length) {
      alert('Không tìm thấy địa điểm phù hợp. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
      return;
    }

    const diaChi = danhSachDiaChi[index];

    timMotDiaChi(diaChi).then(function(ketQua) {
      if (!ketQua) {
        timDiaChiTheoDanhSach(danhSachDiaChi, index + 1);
        return;
      }

      setLocation(ketQua.lat, ketQua.lng);
    });
  }

  document.getElementById('btnTimTrenBanDo').addEventListener('click', function() {
    const danhSachDiaChi = taoDanhSachDiaChi();

    if (danhSachDiaChi.length === 0) {
      alert('Vui lòng nhập ít nhất một thông tin địa chỉ trước khi tìm.');
      return;
    }

    timDiaChiTheoDanhSach(danhSachDiaChi);
  });
</script>
@endsection