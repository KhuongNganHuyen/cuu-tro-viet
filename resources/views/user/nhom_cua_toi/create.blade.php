@extends('layouts.user')

@section('title', 'Đăng ký tạo nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #nhomDiaDiemMap {
    height: 380px;
    width: 100%;
    border-radius: 12px;
  }

  .group-preview-image {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border: 1px solid #dee2e6;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">
            Đăng ký tạo nhóm tình nguyện
          </h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">
              Tổng quan
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">
              Nhóm tình nguyện của tôi
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Đăng ký tạo nhóm
          </li>
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

<form action="{{ url('/user/nhom-cua-toi') }}"
      method="POST"
      enctype="multipart/form-data"
      onsubmit="return confirm('Bạn đã kiểm tra chính xác thông tin đăng ký tạo nhóm chưa?')">
  @csrf

  <div class="row">
    {{-- CỘT TRÁI --}}
    <div class="col-lg-8">

      {{-- THÔNG TIN NHÓM --}}
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">
            Thông tin nhóm
          </h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              Tên nhóm
              <span class="text-danger">*</span>
            </label>

            <input type="text"
                   name="tenNhom"
                   class="form-control @error('tenNhom') is-invalid @enderror"
                   value="{{ old('tenNhom') }}"
                   maxlength="255"
                   placeholder="Ví dụ: Nhóm cứu trợ Đà Nẵng"
                   autocomplete="off">

            @error('tenNhom')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">
              Mô tả nhóm
            </label>

            <textarea name="moTa"
                      class="form-control @error('moTa') is-invalid @enderror"
                      rows="4"
                      maxlength="255"
                      placeholder="Mô tả ngắn về mục tiêu, khu vực hoạt động hoặc khả năng hỗ trợ của nhóm">{{ old('moTa') }}</textarea>

            @error('moTa')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>

          <div class="mb-0">
            <label class="form-label">
              Trạng thái đăng ký
            </label>

            <input type="text"
                   class="form-control"
                   value="Chờ duyệt"
                   readonly>

            <small class="text-muted">
              Nhóm sẽ được phép hoạt động sau khi quản trị viên duyệt.
            </small>
          </div>
        </div>
      </div>

      {{-- ĐỊA ĐIỂM --}}
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">
            Địa điểm hoạt động
          </h5>
        </div>

        <div class="card-body">
          <input type="hidden"
                 name="idDiaDiemCoSan"
                 id="idDiaDiemCoSan"
                 value="{{ old('idDiaDiemCoSan') }}">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">
                Tỉnh/Thành
                <span class="text-danger">*</span>
              </label>

              <select name="tinhThanh"
                      id="tinhThanh"
                      class="form-select @error('tinhThanh') is-invalid @enderror">

                <option value="">
                  -- Chọn tỉnh/thành --
                </option>

                @foreach (
                  $diaDiems
                    ->pluck('tinhThanh')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                  as $tinhThanh
                )
                  <option value="{{ $tinhThanh }}"
                    {{ old('tinhThanh') == $tinhThanh ? 'selected' : '' }}>
                    {{ $tinhThanh }}
                  </option>
                @endforeach
              </select>

              @error('tinhThanh')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">
                Phường/Xã
                <span class="text-danger">*</span>
              </label>

              <select name="phuongXa"
                      id="phuongXa"
                      class="form-select @error('phuongXa') is-invalid @enderror">

                <option value="">
                  -- Chọn phường/xã --
                </option>
              </select>

              @error('phuongXa')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">
              Địa chỉ chi tiết
              <span class="text-danger">*</span>
            </label>

            <input type="text"
                   name="chiTietDiaDiem"
                   id="chiTietDiaDiem"
                   class="form-control @error('chiTietDiaDiem') is-invalid @enderror"
                   list="danhSachDiaDiem"
                   value="{{ old('chiTietDiaDiem') }}"
                   placeholder="Ví dụ: 48 Cao Thắng"
                   autocomplete="off">

            <datalist id="danhSachDiaDiem"></datalist>

            @error('chiTietDiaDiem')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label mb-0">
                Chọn vị trí trên bản đồ
                <span class="text-danger">*</span>
              </label>

              <button type="button"
                      id="btnTimTrenBanDo"
                      class="btn btn-sm btn-outline-primary">
                Tìm trên bản đồ
              </button>
            </div>

            <div id="nhomDiaDiemMap"></div>

            <small class="text-muted">
              Nhập địa chỉ rồi bấm “Tìm trên bản đồ” hoặc nhấn trực tiếp lên bản đồ để chọn vị trí.
            </small>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
              <label class="form-label">
                Vĩ độ
              </label>

              <input type="text"
                     name="viDo"
                     id="viDo"
                     class="form-control @error('viDo') is-invalid @enderror"
                     value="{{ old('viDo') }}"
                     readonly>

              @error('viDo')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">
                Kinh độ
              </label>

              <input type="text"
                     name="kinhDo"
                     id="kinhDo"
                     class="form-control @error('kinhDo') is-invalid @enderror"
                     value="{{ old('kinhDo') }}"
                     readonly>

              @error('kinhDo')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- NÚT HÀNH ĐỘNG --}}
      <div class="d-flex gap-2">
        <button type="submit"
                class="btn btn-primary">
          Gửi đăng ký
        </button>

        <a href="{{ url('/user/nhom-cua-toi') }}"
           class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>

    {{-- CỘT PHẢI --}}
    <div class="col-lg-4 mt-3 mt-lg-0">

      {{-- ẢNH ĐẠI DIỆN --}}
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">
            Ảnh đại diện nhóm
          </h5>
        </div>

        <div class="card-body">
          <div class="text-center mb-3">
            <img src="{{ asset('storage/nhom-tinh-nguyen/group.jpg') }}"
                 alt="Ảnh đại diện nhóm"
                 class="rounded-circle group-preview-image"
                 id="previewAnhDaiDien">
          </div>

          <div class="mb-3">
            <label class="form-label">
              Chọn ảnh
            </label>

            <input type="file"
                   name="anhDaiDien"
                   id="anhDaiDien"
                   class="form-control @error('anhDaiDien') is-invalid @enderror"
                   accept=".jpg,.jpeg,.png,.webp,image/*">

            @error('anhDaiDien')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror

            <small class="text-muted">
              Chấp nhận JPG, JPEG, PNG hoặc WEBP. Dung lượng tối đa 2 MB.
            </small>
          </div>

          <button type="button"
                  id="btnXoaAnh"
                  class="btn btn-sm btn-outline-danger d-none">
            Bỏ ảnh đã chọn
          </button>

          <div class="alert alert-info mt-3 mb-0">
            Nếu không chọn ảnh, hệ thống sẽ sử dụng ảnh mặc định.
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<input type="hidden"
       id="oldPhuongXa"
       value="{{ old('phuongXa') }}">

<script id="diaDiemData"
        type="application/json">
{!! $diaDiemJson !!}
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const diaDiems = JSON.parse(
      document.getElementById('diaDiemData').textContent || '[]'
    );

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
    const previewAnhDaiDien = document.getElementById('previewAnhDaiDien');
    const btnXoaAnh = document.getElementById('btnXoaAnh');

    const anhMacDinh = previewAnhDaiDien.src;

    if (anhDaiDienInput) {
      anhDaiDienInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
          previewAnhDaiDien.src = anhMacDinh;
          btnXoaAnh.classList.add('d-none');
          return;
        }

        previewAnhDaiDien.src = URL.createObjectURL(file);
        btnXoaAnh.classList.remove('d-none');
      });
    }

    btnXoaAnh.addEventListener('click', function () {
      anhDaiDienInput.value = '';
      previewAnhDaiDien.src = anhMacDinh;
      btnXoaAnh.classList.add('d-none');
    });

    function loadPhuongXa() {
      const tinhThanh = tinhThanhSelect.value;

      phuongXaSelect.innerHTML =
        '<option value="">-- Chọn phường/xã --</option>';

      if (!tinhThanh) {
        return;
      }

      const phuongXas = [...new Set(
        diaDiems
          .filter(function (item) {
            return item.tinhThanh === tinhThanh
              && item.phuongXa;
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

        if (
          oldPhuongXa
          && oldPhuongXa === phuongXa
        ) {
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

      const diaDiemPhuHop = diaDiems
        .filter(function (item) {
          return item.tinhThanh === tinhThanh
            && item.phuongXa === phuongXa
            && item.chiTietDiaDiem;
        })
        .sort(function (a, b) {
          return a.chiTietDiaDiem.localeCompare(
            b.chiTietDiaDiem,
            'vi'
          );
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

    const map = L.map('nhomDiaDiemMap')
      .setView([defaultLat, defaultLng], 12);

    L.tileLayer(
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }
    ).addTo(map);

    let marker = null;

    function setLocation(lat, lng) {
      const latitude = parseFloat(lat);
      const longitude = parseFloat(lng);

      if (
        !Number.isFinite(latitude)
        || !Number.isFinite(longitude)
      ) {
        return;
      }

      viDoInput.value = latitude.toFixed(7);
      kinhDoInput.value = longitude.toFixed(7);

      if (marker) {
        marker.setLatLng([latitude, longitude]);
      } else {
        marker = L.marker(
          [latitude, longitude],
          {
            draggable: true
          }
        ).addTo(map);

        marker.on('dragend', function () {
          const position = marker.getLatLng();

          viDoInput.value = position.lat.toFixed(7);
          kinhDoInput.value = position.lng.toFixed(7);
        });
      }

      marker
        .bindPopup('Vị trí đã chọn.')
        .openPopup();

      map.setView([latitude, longitude], 16);
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

      if (!diaDiem) {
        idDiaDiemCoSanInput.value = '';
        return false;
      }

      idDiaDiemCoSanInput.value = diaDiem.idDiaDiem;

      if (
        diaDiem.viDo
        && diaDiem.kinhDo
      ) {
        setLocation(
          diaDiem.viDo,
          diaDiem.kinhDo
        );
      }

      return true;
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
          [
            parseFloat(diaDiemTheoTinh.viDo),
            parseFloat(diaDiemTheoTinh.kinhDo)
          ],
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
          [
            parseFloat(diaDiemTheoPhuong.viDo),
            parseFloat(diaDiemTheoPhuong.kinhDo)
          ],
          14
        );
      }
    });

    chiTietDiaDiemInput.addEventListener('input', function () {
      ganDiaDiemCoSanNeuCo();
    });

    map.on('click', function (event) {
      idDiaDiemCoSanInput.value = '';

      setLocation(
        event.latlng.lat,
        event.latlng.lng
      );
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

      if (
        chiTietGoc
        && phuongXa
        && tinhThanh
      ) {
        danhSach.push(
          chiTietGoc
          + ', ' + phuongXa
          + ', ' + tinhThanh
          + ', Việt Nam'
        );
      }

      if (
        chiTiet
        && phuongXa
        && tinhThanh
      ) {
        danhSach.push(
          chiTiet
          + ', ' + phuongXa
          + ', ' + tinhThanh
          + ', Việt Nam'
        );
      }

      if (
        chiTiet
        && tinhThanh
      ) {
        danhSach.push(
          chiTiet
          + ', ' + tinhThanh
          + ', Việt Nam'
        );
      }

      if (
        chiTietKhongDau
        && phuongXaKhongDau
        && tinhThanhKhongDau
      ) {
        danhSach.push(
          chiTietKhongDau
          + ', ' + phuongXaKhongDau
          + ', ' + tinhThanhKhongDau
          + ', Vietnam'
        );
      }

      if (
        phuongXa
        && tinhThanh
      ) {
        danhSach.push(
          phuongXa
          + ', ' + tinhThanh
          + ', Việt Nam'
        );
      }

      if (tinhThanh) {
        danhSach.push(
          tinhThanh + ', Việt Nam'
        );
      }

      return [...new Set(
        danhSach.filter(Boolean)
      )];
    }

    function timBangNominatim(diaChi) {
      const url =
        'https://nominatim.openstreetmap.org/search'
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
          if (
            !data
            || data.length === 0
          ) {
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
      const url =
        'https://photon.komoot.io/api/'
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
          if (
            !data
            || !data.features
            || data.features.length === 0
          ) {
            return null;
          }

          const toaDo =
            data.features[0].geometry.coordinates;

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
      return timBangNominatim(diaChi)
        .then(function (ketQuaNominatim) {
          if (ketQuaNominatim) {
            return ketQuaNominatim;
          }

          return timBangPhoton(diaChi);
        });
    }

    function timDiaChiTheoDanhSach(
      danhSachDiaChi,
      index = 0
    ) {
      if (index >= danhSachDiaChi.length) {
        alert(
          'Không tìm thấy địa điểm phù hợp. Bạn có thể nhấn trực tiếp trên bản đồ để chọn vị trí.'
        );

        return;
      }

      timMotDiaChi(danhSachDiaChi[index])
        .then(function (ketQua) {
          if (!ketQua) {
            timDiaChiTheoDanhSach(
              danhSachDiaChi,
              index + 1
            );

            return;
          }

          idDiaDiemCoSanInput.value = '';

          setLocation(
            ketQua.lat,
            ketQua.lng
          );
        });
    }

    btnTimTrenBanDo.addEventListener('click', function () {
      const danhSachDiaChi = taoDanhSachDiaChi();

      if (danhSachDiaChi.length === 0) {
        alert(
          'Vui lòng nhập ít nhất một thông tin địa chỉ trước khi tìm.'
        );

        return;
      }

      btnTimTrenBanDo.disabled = true;
      btnTimTrenBanDo.textContent = 'Đang tìm...';

      timMotDiaChi(danhSachDiaChi[0])
        .then(function (ketQua) {
          if (ketQua) {
            idDiaDiemCoSanInput.value = '';

            setLocation(
              ketQua.lat,
              ketQua.lng
            );

            return;
          }

          timDiaChiTheoDanhSach(
            danhSachDiaChi,
            1
          );
        })
        .finally(function () {
          btnTimTrenBanDo.disabled = false;
          btnTimTrenBanDo.textContent =
            'Tìm trên bản đồ';
        });
    });

    loadPhuongXa();
    loadDiaDiemOptions();

    if (
      viDoInput.value
      && kinhDoInput.value
    ) {
      setLocation(
        viDoInput.value,
        kinhDoInput.value
      );
    }

    setTimeout(function () {
      map.invalidateSize();
    }, 200);
  });
</script>
@endsection