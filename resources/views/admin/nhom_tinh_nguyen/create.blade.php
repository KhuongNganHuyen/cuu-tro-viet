@extends('layouts.admin')

@section('title', 'Thêm nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #nhomDiaDiemMap {
    height: 380px;
    width: 100%;
    border-radius: 12px;
  }

  .group-preview-image {
    width: 120px;
    height: 120px;
    object-fit: cover;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm nhóm tình nguyện</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/nhom-tinh-nguyen') }}">Nhóm tình nguyện</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
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

<form action="{{ url('/admin/nhom-tinh-nguyen') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">Thông tin nhóm</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Tên nhóm <span class="text-danger">*</span></label>
            <input type="text" name="tenNhom" class="form-control"
              value="{{ old('tenNhom') }}"
              placeholder="Ví dụ: Nhóm cứu trợ Đà Nẵng"
              autocomplete="off">
          </div>

          <div class="mb-3">
            <label class="form-label">Nhóm trưởng <span class="text-danger">*</span></label>

            <input type="text" id="nhomTruongInput" class="form-control"
              list="danhSachNhomTruong"
              value="{{ old('tenNhomTruongHienThi') }}"
              placeholder="Nhập họ tên hoặc tên đăng nhập nhóm trưởng"
              autocomplete="off">

            <input type="hidden" name="idNhomTruong" id="idNhomTruong"
              value="{{ old('idNhomTruong') }}">

            <datalist id="danhSachNhomTruong"></datalist>
          </div>

          <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="moTa" class="form-control" rows="4"
              placeholder="Mô tả ngắn về phạm vi hoạt động, mục tiêu hoặc đặc điểm của nhóm">{{ old('moTa') }}</textarea>
          </div>

          <div class="mb-0">
            <label class="form-label">Trạng thái</label>
            <input type="hidden" name="trangThai" value="Đang hoạt động">
            <input type="text" class="form-control" value="Đang hoạt động" readonly>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">Địa điểm hoạt động</h5>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Tỉnh/Thành <span class="text-danger">*</span></label>
              <select name="tinhThanh" id="tinhThanh" class="form-control">
                <option value="">-- Chọn tỉnh/thành --</option>

                @foreach ($diaDiems->pluck('tinhThanh')->unique()->sort()->values() as $tinhThanh)
                  <option value="{{ $tinhThanh }}" {{ old('tinhThanh') == $tinhThanh ? 'selected' : '' }}>
                    {{ $tinhThanh }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
              <select name="phuongXa" id="phuongXa" class="form-control">
                <option value="">-- Chọn phường/xã --</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>

            <input type="hidden" name="idDiaDiemCoSan" id="idDiaDiemCoSan"
              value="{{ old('idDiaDiemCoSan') }}">

            <input type="text" name="chiTietDiaDiem" id="chiTietDiaDiem" class="form-control"
              list="danhSachDiaDiem"
              value="{{ old('chiTietDiaDiem') }}"
              placeholder="Ví dụ: 48 Cao Thắng"
              autocomplete="off">

            <datalist id="danhSachDiaDiem"></datalist>

            <small class="text-muted">
              Có thể chọn địa chỉ có sẵn hoặc nhập địa chỉ mới.
            </small>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label mb-0">Chọn vị trí trên bản đồ <span class="text-danger">*</span></label>

              <button type="button" id="btnTimTrenBanDo" class="btn btn-sm btn-outline-primary">
                Tìm trên bản đồ
              </button>
            </div>

            <div id="nhomDiaDiemMap"></div>
            <small class="text-muted">
              Nhập địa chỉ rồi bấm “Tìm trên bản đồ”, hoặc click trực tiếp lên bản đồ để chọn vị trí.
            </small>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Vĩ độ</label>
              <input type="text" name="viDo" id="viDo" class="form-control"
                value="{{ old('viDo') }}"
                readonly>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Kinh độ</label>
              <input type="text" name="kinhDo" id="kinhDo" class="form-control"
                value="{{ old('kinhDo') }}"
                readonly>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          Lưu
        </button>

        <a href="{{ url('/admin/nhom-tinh-nguyen') }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Ảnh đại diện nhóm</h5>
        </div>

        <div class="card-body">
          <div class="text-center mb-3">
            <img src="{{ asset('storage/nhom-tinh-nguyen/group.jpg') }}"
                 alt="Ảnh đại diện nhóm"
                 class="rounded-circle group-preview-image"
                 id="previewAnhDaiDien">
          </div>

          <div class="mb-3">
            <label class="form-label">Chọn ảnh</label>
            <input type="file"
                   name="anhDaiDien"
                   id="anhDaiDien"
                   class="form-control"
                   accept="image/*">

            <small class="text-muted">
              Chấp nhận jpg, jpeg, png, webp. Dung lượng tối đa 2MB.
            </small>
          </div>

          <div class="alert alert-info mb-0">
            Nếu không chọn ảnh, hệ thống sẽ dùng ảnh mặc định khi hiển thị.
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<input type="hidden" id="oldPhuongXa" value="{{ old('phuongXa') }}">

<script id="diaDiemData" type="application/json">
{!! $diaDiemJson !!}
</script>

<script id="nguoiDungData" type="application/json">
{!! $nguoiDungJson !!}
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const diaDiems = JSON.parse(document.getElementById('diaDiemData').textContent);
    const nguoiDungs = JSON.parse(document.getElementById('nguoiDungData').textContent);

    const tinhThanhSelect = document.getElementById('tinhThanh');
    const phuongXaSelect = document.getElementById('phuongXa');
    const oldPhuongXa = document.getElementById('oldPhuongXa').value;

    const nhomTruongInput = document.getElementById('nhomTruongInput');
    const idNhomTruongInput = document.getElementById('idNhomTruong');
    const danhSachNhomTruong = document.getElementById('danhSachNhomTruong');

    const chiTietDiaDiemInput = document.getElementById('chiTietDiaDiem');
    const idDiaDiemCoSanInput = document.getElementById('idDiaDiemCoSan');
    const danhSachDiaDiem = document.getElementById('danhSachDiaDiem');

    const viDoInput = document.getElementById('viDo');
    const kinhDoInput = document.getElementById('kinhDo');
    const btnTimTrenBanDo = document.getElementById('btnTimTrenBanDo');

    const anhDaiDienInput = document.getElementById('anhDaiDien');
    const previewAnhDaiDien = document.getElementById('previewAnhDaiDien');

    if (anhDaiDienInput) {
      anhDaiDienInput.addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
          previewAnhDaiDien.src = URL.createObjectURL(file);
        }
      });
    }

    function loadNhomTruongOptions() {
      danhSachNhomTruong.innerHTML = '';

      nguoiDungs.forEach(function (nguoiDung) {
        const option = document.createElement('option');
        option.value = nguoiDung.label;
        danhSachNhomTruong.appendChild(option);
      });
    }

    nhomTruongInput.addEventListener('input', function () {
      const selected = nguoiDungs.find(function (nguoiDung) {
        return nguoiDung.label === nhomTruongInput.value;
      });

      idNhomTruongInput.value = selected ? selected.idNguoiDung : '';
    });

    function loadPhuongXa() {
      const tinhThanh = tinhThanhSelect.value;

      phuongXaSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';

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

      loadDiaDiemOptions();
    }

    function loadDiaDiemOptions() {
      const tinhThanh = tinhThanhSelect.value;
      const phuongXa = phuongXaSelect.value;

      danhSachDiaDiem.innerHTML = '';

      const diaDiemPhuHop = diaDiems.filter(function (item) {
        return item.tinhThanh === tinhThanh
          && item.phuongXa === phuongXa
          && item.chiTietDiaDiem;
      }).sort(function (a, b) {
        return a.chiTietDiaDiem.localeCompare(b.chiTietDiaDiem, 'vi');
      });

      diaDiemPhuHop.forEach(function (diaDiem) {
        const option = document.createElement('option');
        option.value = diaDiem.chiTietDiaDiem;
        danhSachDiaDiem.appendChild(option);
      });
    }

    function timDiaDiemCoSan() {
      return diaDiems.find(function (item) {
        return item.tinhThanh === tinhThanhSelect.value
          && item.phuongXa === phuongXaSelect.value
          && item.chiTietDiaDiem === chiTietDiaDiemInput.value.trim();
      });
    }

    const defaultLat = parseFloat(viDoInput.value) || 16.047079;
    const defaultLng = parseFloat(kinhDoInput.value) || 108.206230;

    const map = L.map('nhomDiaDiemMap').setView([defaultLat, defaultLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = null;

    function setLocation(lat, lng) {
      viDoInput.value = lat.toFixed(7);
      kinhDoInput.value = lng.toFixed(7);

      if (marker) {
        marker.setLatLng([lat, lng]);
      } else {
        marker = L.marker([lat, lng]).addTo(map);
      }

      marker.bindPopup('Vị trí đã chọn. Bạn có thể click lại nếu chưa chính xác.').openPopup();
      map.setView([lat, lng], 16);
    }

    function clearLocation() {
      viDoInput.value = '';
      kinhDoInput.value = '';

      if (marker) {
        map.removeLayer(marker);
        marker = null;
      }
    }

    function ganDiaDiemCoSanNeuCo() {
      const diaDiem = timDiaDiemCoSan();

      if (diaDiem) {
        idDiaDiemCoSanInput.value = diaDiem.idDiaDiem;

        if (diaDiem.viDo && diaDiem.kinhDo) {
          setLocation(parseFloat(diaDiem.viDo), parseFloat(diaDiem.kinhDo));
        }

        return true;
      }

      idDiaDiemCoSanInput.value = '';
      return false;
    }

    tinhThanhSelect.addEventListener('change', function () {
      idDiaDiemCoSanInput.value = '';
      chiTietDiaDiemInput.value = '';
      clearLocation();

      loadPhuongXa();

      const diaDiemTheoTinh = diaDiems.find(function (item) {
        return item.tinhThanh === tinhThanhSelect.value && item.viDo && item.kinhDo;
      });

      if (diaDiemTheoTinh) {
        map.setView([parseFloat(diaDiemTheoTinh.viDo), parseFloat(diaDiemTheoTinh.kinhDo)], 12);
      }
    });

    phuongXaSelect.addEventListener('change', function () {
      idDiaDiemCoSanInput.value = '';
      chiTietDiaDiemInput.value = '';
      clearLocation();

      loadDiaDiemOptions();

      const diaDiemTheoPhuong = diaDiems.find(function (item) {
        return item.tinhThanh === tinhThanhSelect.value
          && item.phuongXa === phuongXaSelect.value
          && item.viDo
          && item.kinhDo;
      });

      if (diaDiemTheoPhuong) {
        map.setView([parseFloat(diaDiemTheoPhuong.viDo), parseFloat(diaDiemTheoPhuong.kinhDo)], 14);
      }
    });

    chiTietDiaDiemInput.addEventListener('input', function () {
      ganDiaDiemCoSanNeuCo();
    });

    map.on('click', function (e) {
      ganDiaDiemCoSanNeuCo();
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
      const chiTietGoc = chiTietDiaDiemInput.value.trim();
      const phuongXaGoc = phuongXaSelect.value.trim();
      const tinhThanhGoc = tinhThanhSelect.value.trim();

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

        ganDiaDiemCoSanNeuCo();
        setLocation(ketQua.lat, ketQua.lng);
      });
    }

    btnTimTrenBanDo.addEventListener('click', function () {
      const danhSachDiaChi = taoDanhSachDiaChi();

      if (danhSachDiaChi.length === 0) {
        alert('Vui lòng nhập ít nhất một thông tin địa chỉ trước khi tìm.');
        return;
      }

      timDiaChiTheoDanhSach(danhSachDiaChi);
    });

    loadNhomTruongOptions();
    loadPhuongXa();
    loadDiaDiemOptions();

    if (viDoInput.value && kinhDoInput.value) {
      setLocation(parseFloat(viDoInput.value), parseFloat(kinhDoInput.value));
    }
  });
</script>
@endsection