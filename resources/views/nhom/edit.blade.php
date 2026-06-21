@extends('layouts.nhom')

@section('title', 'Sửa thông tin nhóm | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #nhomEditMap {
    height: 360px;
    width: 100%;
    border-radius: 12px;
  }

  .nhom-edit-avatar {
    width: 130px;
    height: 130px;
    object-fit: cover;
  }
</style>

@php
  $diaDiem = $nhom->diaDiem;

  $duongDanAnhNhom = $nhom->anhDaiDien
      ? asset('storage/' . $nhom->anhDaiDien)
      : asset('storage/nhom-tinh-nguyen/group.jpg');

  $viDo = old('viDo', $diaDiem->viDo ?? 16.0675777);
  $kinhDo = old('kinhDo', $diaDiem->kinhDo ?? 108.2136447);
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Sửa thông tin nhóm</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              {{ $nhom->tenNhom }}
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Sửa
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if ($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại thông tin:</div>
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}"
      method="POST"
      enctype="multipart/form-data">
  @csrf
  @method('PATCH')

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Thông tin nhóm</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              Tên nhóm <span class="text-danger">*</span>
            </label>

            <input type="text"
                   name="tenNhom"
                   class="form-control"
                   value="{{ old('tenNhom', $nhom->tenNhom) }}"
                   autocomplete="off"
                   required>
          </div>

          <div class="mb-3">
            <label class="form-label">Nhóm trưởng</label>

            <input type="text"
                   class="form-control"
                   value="{{ $nhom->nhomTruong->hoTen ?? '-' }}{{ $nhom->nhomTruong?->tenDangNhap ? ' - ' . $nhom->nhomTruong->tenDangNhap : '' }}"
                   disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Mô tả</label>

            <textarea name="moTa"
                      class="form-control"
                      rows="4">{{ old('moTa', $nhom->moTa) }}</textarea>
          </div>

          <div class="mb-0">
            <label class="form-label">
              Trạng thái <span class="text-danger">*</span>
            </label>

            <select name="trangThai" class="form-select" required>
              @foreach (['Đang hoạt động', 'Tạm ngừng hoạt động', 'Ngừng hoạt động'] as $trangThai)
                <option value="{{ $trangThai }}"
                        {{ old('trangThai', $nhom->trangThai) === $trangThai ? 'selected' : '' }}>
                  {{ $trangThai }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">
          <h5 class="mb-0">Địa điểm hoạt động</h5>
        </div>

        <div class="card-body">
          <input type="hidden"
                name="idDiaDiemCoSan"
                id="idDiaDiemCoSan"
                value="{{ old('idDiaDiemCoSan', $nhom->idDiaDiem) }}">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">
                Tỉnh/Thành <span class="text-danger">*</span>
              </label>

              <select name="tinhThanh" id="tinhThanh" class="form-control" required>
                <option value="">-- Chọn tỉnh/thành --</option>

                @foreach ($diaDiems->pluck('tinhThanh')->unique()->sort()->values() as $tinhThanh)
                  <option value="{{ $tinhThanh }}"
                          {{ old('tinhThanh', $diaDiem->tinhThanh ?? '') == $tinhThanh ? 'selected' : '' }}>
                    {{ $tinhThanh }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                Phường/Xã <span class="text-danger">*</span>
              </label>

              <select name="phuongXa" id="phuongXa" class="form-control" required>
                <option value="">-- Chọn phường/xã --</option>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label">
                Địa chỉ chi tiết <span class="text-danger">*</span>
              </label>

              <input type="text"
                    name="chiTietDiaDiem"
                    id="chiTietDiaDiem"
                    class="form-control"
                    list="danhSachDiaDiem"
                    value="{{ old('chiTietDiaDiem', $diaDiem->chiTietDiaDiem ?? '') }}"
                    placeholder="Ví dụ: 48 Cao Thắng"
                    autocomplete="off"
                    required>

              <datalist id="danhSachDiaDiem"></datalist>

              <small class="text-muted">
                Có thể chọn địa chỉ có sẵn hoặc nhập địa chỉ mới.
              </small>
            </div>

            <div class="col-md-12">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <label class="form-label mb-0">
                    Chọn vị trí trên bản đồ <span class="text-danger">*</span>
                  </label>

                  <div class="small text-muted">
                    Nhập địa chỉ rồi bấm “Tìm trên bản đồ”, hoặc click trực tiếp lên bản đồ để chọn vị trí.
                  </div>
                </div>

                <button type="button"
                        id="btnTimTrenBanDo"
                        class="btn btn-outline-primary btn-sm">
                  Tìm trên bản đồ
                </button>
              </div>

              <div id="nhomEditMap"></div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Vĩ độ</label>

              <input type="text"
                    name="viDo"
                    id="viDo"
                    class="form-control"
                    value="{{ old('viDo', $diaDiem->viDo ?? '') }}"
                    readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Kinh độ</label>

              <input type="text"
                    name="kinhDo"
                    id="kinhDo"
                    class="form-control"
                    value="{{ old('kinhDo', $diaDiem->kinhDo ?? '') }}"
                    readonly>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">
          Cập nhật
        </button>

        <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}"
           class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Ảnh đại diện nhóm</h5>
        </div>

        <div class="card-body text-center">
          <img src="{{ $duongDanAnhNhom }}"
               alt="Ảnh đại diện nhóm"
               class="rounded-circle border nhom-edit-avatar mb-3"
               id="previewAnhNhom">

          <div class="text-start">
            <label class="form-label">Chọn ảnh mới</label>

            <input type="file"
                   name="anhDaiDien"
                   id="anhDaiDien"
                   class="form-control"
                   accept="image/*">

            <small class="text-muted">
              Chấp nhận jpg, jpeg, png, webp. Dung lượng tối đa 2MB.
            </small>
          </div>

          <div class="alert alert-info text-start mt-3 mb-0">
            Nếu không chọn ảnh mới, hệ thống sẽ giữ nguyên ảnh hiện tại.
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<input type="hidden"
       id="oldPhuongXa"
       value="{{ old('phuongXa', $diaDiem->phuongXa ?? '') }}">

<script id="diaDiemData" type="application/json">
{!! $diaDiemJson !!}
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const diaDiems = JSON.parse(document.getElementById('diaDiemData').textContent);

    const tinhThanhSelect = document.getElementById('tinhThanh');
    const phuongXaSelect = document.getElementById('phuongXa');
    const oldPhuongXa = document.getElementById('oldPhuongXa').value;

    const chiTietDiaDiemInput = document.getElementById('chiTietDiaDiem');
    const idDiaDiemCoSanInput = document.getElementById('idDiaDiemCoSan');
    const danhSachDiaDiem = document.getElementById('danhSachDiaDiem');

    const viDoInput = document.getElementById('viDo');
    const kinhDoInput = document.getElementById('kinhDo');
    const btnTimTrenBanDo = document.getElementById('btnTimTrenBanDo');

    const anhDaiDienInput = document.getElementById('anhDaiDien');
    const previewAnhNhom = document.getElementById('previewAnhNhom');

    if (anhDaiDienInput && previewAnhNhom) {
      anhDaiDienInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
          return;
        }

        previewAnhNhom.src = URL.createObjectURL(file);
      });
    }

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

      if (!tinhThanh || !phuongXa) {
        return;
      }

      const diaDiemPhuHop = diaDiems
        .filter(function (item) {
          return item.tinhThanh === tinhThanh
            && item.phuongXa === phuongXa
            && item.chiTietDiaDiem;
        })
        .sort(function (a, b) {
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

    let lat = parseFloat(viDoInput.value || 16.0675777);
    let lng = parseFloat(kinhDoInput.value || 108.2136447);

    if (Number.isNaN(lat)) {
      lat = 16.0675777;
    }

    if (Number.isNaN(lng)) {
      lng = 108.2136447;
    }

    const map = L.map('nhomEditMap').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = null;

    function setLocation(newLat, newLng) {
      viDoInput.value = newLat.toFixed(7);
      kinhDoInput.value = newLng.toFixed(7);

      if (marker) {
        marker.setLatLng([newLat, newLng]);
      } else {
        marker = L.marker([newLat, newLng]).addTo(map);
      }

      marker.bindPopup('Vị trí đã chọn. Bạn có thể click lại nếu chưa chính xác.').openPopup();
      map.setView([newLat, newLng], 16);
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
        return item.tinhThanh === tinhThanhSelect.value
          && item.viDo
          && item.kinhDo;
      });

      if (diaDiemTheoTinh) {
        map.setView(
          [parseFloat(diaDiemTheoTinh.viDo), parseFloat(diaDiemTheoTinh.kinhDo)],
          12
        );
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
        map.setView(
          [parseFloat(diaDiemTheoPhuong.viDo), parseFloat(diaDiemTheoPhuong.kinhDo)],
          14
        );
      }
    });

    chiTietDiaDiemInput.addEventListener('input', function () {
      ganDiaDiemCoSanNeuCo();
    });

    map.on('click', function (event) {
      idDiaDiemCoSanInput.value = '';
      setLocation(event.latlng.lat, event.latlng.lng);
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
        .then(function (response) {
          if (!response.ok) {
            return null;
          }

          return response.json();
        })
        .then(function (data) {
          if (!data || data.length === 0) {
            return null;
          }

          return {
            lat: parseFloat(data[0].lat),
            lng: parseFloat(data[0].lon)
          };
        })
        .catch(function () {
          return null;
        });
    }

    function timBangPhoton(diaChi) {
      const url = 'https://photon.komoot.io/api/'
        + '?limit=1'
        + '&q=' + encodeURIComponent(diaChi);

      return fetch(url)
        .then(function (response) {
          if (!response.ok) {
            return null;
          }

          return response.json();
        })
        .then(function (data) {
          if (!data || !data.features || data.features.length === 0) {
            return null;
          }

          const toaDo = data.features[0].geometry.coordinates;

          return {
            lat: parseFloat(toaDo[1]),
            lng: parseFloat(toaDo[0])
          };
        })
        .catch(function () {
          return null;
        });
    }

    function timMotDiaChi(diaChi) {
      return timBangNominatim(diaChi).then(function (ketQuaNominatim) {
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

      timMotDiaChi(diaChi).then(function (ketQua) {
        if (!ketQua) {
          timDiaChiTheoDanhSach(danhSachDiaChi, index + 1);
          return;
        }

        idDiaDiemCoSanInput.value = '';
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

    loadPhuongXa();
    loadDiaDiemOptions();

    if (viDoInput.value && kinhDoInput.value) {
      setLocation(parseFloat(viDoInput.value), parseFloat(kinhDoInput.value));
    }
  });
</script>
@endsection