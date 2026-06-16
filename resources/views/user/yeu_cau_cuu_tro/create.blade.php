@extends('layouts.user')

@section('title', 'Gửi yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #map {
    height: 380px;
    width: 100%;
    border-radius: 12px;
  }

  .request-preview-box {
    width: 100%;
    min-height: 230px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .request-preview-box img {
    width: 100%;
    height: 230px;
    object-fit: contain;
  }

  .request-preview-empty i {
    font-size: 48px;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Gửi yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">
              Tổng quan
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/yeu-cau-cuu-tro') }}">
              Yêu cầu cứu trợ
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Gửi yêu cầu
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

<div class="alert alert-warning">
  <strong>Lưu ý:</strong>
  Sau khi gửi, bạn không thể tự chỉnh sửa nội dung yêu cầu.
  Bạn chỉ có thể hủy khi yêu cầu chưa được nhóm tình nguyện nào tiếp nhận.
</div>

<form action="{{ url('/user/yeu-cau-cuu-tro') }}"
      method="POST"
      enctype="multipart/form-data"
      onsubmit="return confirm('Bạn hãy kiểm tra kỹ thông tin trước khi gửi. Sau khi gửi yêu cầu, bạn sẽ không thể tự chỉnh sửa nội dung này. Bạn chắc chắn muốn gửi yêu cầu cứu trợ không?')">
  @csrf

  <div class="row">
    {{-- CỘT TRÁI --}}
    <div class="col-lg-8">

      {{-- THÔNG TIN YÊU CẦU --}}
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">Thông tin yêu cầu</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              Tiêu đề yêu cầu
              <span class="text-danger">*</span>
            </label>

            <input type="text"
                   name="tieuDeYeuCau"
                   class="form-control @error('tieuDeYeuCau') is-invalid @enderror"
                   value="{{ old('tieuDeYeuCau') }}"
                   maxlength="255"
                   placeholder="Ví dụ: Cần hỗ trợ lương thực và nước uống"
                   autocomplete="off">

            @error('tieuDeYeuCau')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">
              Mô tả tình hình
              <span class="text-danger">*</span>
            </label>

            <textarea name="moTa"
                      class="form-control @error('moTa') is-invalid @enderror"
                      rows="5"
                      placeholder="Mô tả rõ tình hình hiện tại, nhu cầu cần hỗ trợ và số lượng dự kiến...">{{ old('moTa') }}</textarea>

            @error('moTa')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
              <label class="form-label">
                Số người cần hỗ trợ
              </label>

              <input type="number"
                     name="soNguoi"
                     class="form-control @error('soNguoi') is-invalid @enderror"
                     value="{{ old('soNguoi') }}"
                     min="1"
                     placeholder="Ví dụ: 5">

              @error('soNguoi')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">
                Mức độ khẩn cấp
                <span class="text-danger">*</span>
              </label>

              <select name="mucDoKhanCap"
                      class="form-select @error('mucDoKhanCap') is-invalid @enderror">
                <option value="">
                  -- Chọn mức độ --
                </option>

                <option value="Thấp"
                  {{ old('mucDoKhanCap') === 'Thấp' ? 'selected' : '' }}>
                  Thấp
                </option>

                <option value="Trung bình"
                  {{ old('mucDoKhanCap') === 'Trung bình' ? 'selected' : '' }}>
                  Trung bình
                </option>

                <option value="Cao"
                  {{ old('mucDoKhanCap') === 'Cao' ? 'selected' : '' }}>
                  Cao
                </option>

                <option value="Khẩn cấp"
                  {{ old('mucDoKhanCap') === 'Khẩn cấp' ? 'selected' : '' }}>
                  Khẩn cấp
                </option>
              </select>

              @error('mucDoKhanCap')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- ĐỊA ĐIỂM --}}
      <div class="card mb-3">
        <div class="card-header">
          <div>
            <h5 class="mb-1">
              Địa điểm cần hỗ trợ
            </h5>
          </div>
        </div>

        <div class="card-body">
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
                      class="form-select @error('phuongXa') is-invalid @enderror"
                      disabled>
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
                   list="danhSachDiaChi"
                   class="form-control @error('chiTietDiaDiem') is-invalid @enderror"
                   value="{{ old('chiTietDiaDiem') }}"
                   placeholder="Ví dụ: 48 Cao Thắng"
                   autocomplete="off">

            <datalist id="danhSachDiaChi"></datalist>

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
                      id="btnTimBanDo"
                      class="btn btn-sm btn-outline-primary">
                Tìm trên bản đồ
              </button>
            </div>

            <div id="map"></div>

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

      {{-- NÚT --}}
      <div class="d-flex gap-2">
        <button type="submit"
                class="btn btn-primary">
          Gửi yêu cầu
        </button>

        <a href="{{ url('/user/yeu-cau-cuu-tro') }}"
           class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>

    {{-- CỘT PHẢI: ẢNH --}}
    <div class="col-lg-4 mt-3 mt-lg-0">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            Ảnh minh chứng
          </h5>
        </div>

        <div class="card-body">
          <div id="khungXemTruocAnh"
               class="request-preview-box mb-3">

            <div id="noiDungMacDinhAnh"
                 class="request-preview-empty text-center text-muted px-3">
              <i class="ti ti-photo d-block mb-2"></i>

              <div class="mb-1">
                Chưa chọn hình ảnh
              </div>

              <small>
                Ảnh giúp nhóm tình nguyện đánh giá tình hình chính xác hơn.
              </small>
            </div>

            <img id="anhXemTruoc"
                 src=""
                 alt="Ảnh minh chứng"
                 class="d-none">
          </div>

          <div class="mb-3">
            <label class="form-label">
              Chọn ảnh
            </label>

            <input type="file"
                   name="hinhAnh"
                   id="hinhAnh"
                   class="form-control @error('hinhAnh') is-invalid @enderror"
                   accept=".jpg,.jpeg,.png,.webp,image/*">

            @error('hinhAnh')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror

            <small class="text-muted">
              Chấp nhận JPG, JPEG, PNG hoặc WEBP. Dung lượng tối đa 4 MB.
            </small>
          </div>

          <button type="button"
                  id="btnXoaAnh"
                  class="btn btn-sm btn-outline-danger d-none">
            Bỏ ảnh đã chọn
          </button>

          <div class="alert alert-info mt-3 mb-0">
            Nếu không có ảnh minh chứng, bạn vẫn có thể gửi yêu cầu.
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<input type="hidden"
       id="oldTinhThanh"
       value="{{ old('tinhThanh') }}">

<input type="hidden"
       id="oldPhuongXa"
       value="{{ old('phuongXa') }}">

<input type="hidden"
       id="oldChiTietDiaDiem"
       value="{{ old('chiTietDiaDiem') }}">

<script id="diaDiemData" type="application/json">
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
    const chiTietInput = document.getElementById('chiTietDiaDiem');
    const danhSachDiaChi = document.getElementById('danhSachDiaChi');
    const viDoInput = document.getElementById('viDo');
    const kinhDoInput = document.getElementById('kinhDo');
    const btnTimBanDo = document.getElementById('btnTimBanDo');

    const oldTinhThanh = document.getElementById('oldTinhThanh').value;
    const oldPhuongXa = document.getElementById('oldPhuongXa').value;
    const oldChiTiet = document.getElementById('oldChiTietDiaDiem').value;

    const defaultLat = 16.047079;
    const defaultLng = 108.206230;

    let marker = null;

    const map = L.map('map').setView([
      Number(viDoInput.value) || defaultLat,
      Number(kinhDoInput.value) || defaultLng
    ], 13);

    L.tileLayer(
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }
    ).addTo(map);

    function uniqueValues(values) {
      return [...new Set(
        values
          .map(function (value) {
            return String(value || '').trim();
          })
          .filter(function (value) {
            return value !== '';
          })
      )].sort(function (a, b) {
        return a.localeCompare(b, 'vi');
      });
    }

    function setMarker(lat, lng, zoom = 16) {
      const latitude = Number(lat);
      const longitude = Number(lng);

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

      map.setView([latitude, longitude], zoom);
    }

    function clearMarker() {
      viDoInput.value = '';
      kinhDoInput.value = '';

      if (marker) {
        map.removeLayer(marker);
        marker = null;
      }
    }

    function loadTinhThanh() {
      const danhSachTinh = uniqueValues(
        diaDiems.map(function (item) {
          return item.tinhThanh;
        })
      );

      tinhThanhSelect.innerHTML =
        '<option value="">-- Chọn tỉnh/thành --</option>';

      danhSachTinh.forEach(function (tinhThanh) {
        const option = document.createElement('option');

        option.value = tinhThanh;
        option.textContent = tinhThanh;

        tinhThanhSelect.appendChild(option);
      });

      if (oldTinhThanh) {
        tinhThanhSelect.value = oldTinhThanh;
      }
    }

    function loadPhuongXa(tinhThanh, selectedValue = '') {
      const danhSachPhuong = uniqueValues(
        diaDiems
          .filter(function (item) {
            return item.tinhThanh === tinhThanh;
          })
          .map(function (item) {
            return item.phuongXa;
          })
      );

      phuongXaSelect.innerHTML =
        '<option value="">-- Chọn phường/xã --</option>';

      danhSachPhuong.forEach(function (phuongXa) {
        const option = document.createElement('option');

        option.value = phuongXa;
        option.textContent = phuongXa;

        phuongXaSelect.appendChild(option);
      });

      phuongXaSelect.disabled = tinhThanh === '';

      if (selectedValue) {
        phuongXaSelect.value = selectedValue;
      }

      loadDiaChiChiTiet();
    }

    function loadDiaChiChiTiet() {
      const tinhThanh = tinhThanhSelect.value;
      const phuongXa = phuongXaSelect.value;

      const danhSachChiTiet = uniqueValues(
        diaDiems
          .filter(function (item) {
            return item.tinhThanh === tinhThanh
              && item.phuongXa === phuongXa;
          })
          .map(function (item) {
            return item.chiTietDiaDiem;
          })
      );

      danhSachDiaChi.innerHTML = '';

      danhSachChiTiet.forEach(function (chiTiet) {
        const option = document.createElement('option');

        option.value = chiTiet;

        danhSachDiaChi.appendChild(option);
      });
    }

    function timDiaDiemCoSan() {
      const tinhThanh = tinhThanhSelect.value.trim();
      const phuongXa = phuongXaSelect.value.trim();
      const chiTiet = chiTietInput.value.trim();

      return diaDiems.find(function (item) {
        return String(item.tinhThanh || '').trim() === tinhThanh
          && String(item.phuongXa || '').trim() === phuongXa
          && String(item.chiTietDiaDiem || '').trim() === chiTiet;
      });
    }

    function timTrenNominatim(diaChi) {
      const url =
        'https://nominatim.openstreetmap.org/search'
        + '?format=json'
        + '&countrycodes=vn'
        + '&limit=1'
        + '&accept-language=vi'
        + '&q=' + encodeURIComponent(diaChi);

      return fetch(url)
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (
            Array.isArray(data)
            && data.length > 0
          ) {
            return {
              lat: data[0].lat,
              lng: data[0].lon
            };
          }

          return null;
        })
        .catch(function () {
          return null;
        });
    }

    function timTrenPhoton(diaChi) {
      const url =
        'https://photon.komoot.io/api/'
        + '?limit=1'
        + '&q=' + encodeURIComponent(diaChi);

      return fetch(url)
        .then(function (response) {
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

          const toaDo = data.features[0].geometry.coordinates;

          return {
            lat: toaDo[1],
            lng: toaDo[0]
          };
        })
        .catch(function () {
          return null;
        });
    }

    loadTinhThanh();

    if (oldTinhThanh) {
      loadPhuongXa(oldTinhThanh, oldPhuongXa);
    }

    if (oldChiTiet) {
      chiTietInput.value = oldChiTiet;
    }

    if (
      viDoInput.value
      && kinhDoInput.value
    ) {
      setMarker(
        viDoInput.value,
        kinhDoInput.value
      );
    } else {
      const diaDiemCu = timDiaDiemCoSan();

      if (
        diaDiemCu
        && diaDiemCu.viDo
        && diaDiemCu.kinhDo
      ) {
        setMarker(
          diaDiemCu.viDo,
          diaDiemCu.kinhDo
        );
      }
    }

    tinhThanhSelect.addEventListener('change', function () {
      loadPhuongXa(this.value);

      chiTietInput.value = '';
      clearMarker();
    });

    phuongXaSelect.addEventListener('change', function () {
      loadDiaChiChiTiet();

      chiTietInput.value = '';
      clearMarker();
    });

    chiTietInput.addEventListener('change', function () {
      const diaDiemCoSan = timDiaDiemCoSan();

      if (
        diaDiemCoSan
        && diaDiemCoSan.viDo
        && diaDiemCoSan.kinhDo
      ) {
        setMarker(
          diaDiemCoSan.viDo,
          diaDiemCoSan.kinhDo
        );
      }
    });

    map.on('click', function (event) {
      setMarker(
        event.latlng.lat,
        event.latlng.lng
      );
    });

    btnTimBanDo.addEventListener('click', async function () {
      const tinhThanh = tinhThanhSelect.value.trim();
      const phuongXa = phuongXaSelect.value.trim();
      const chiTiet = chiTietInput.value.trim();

      if (
        !tinhThanh
        || !phuongXa
        || !chiTiet
      ) {
        alert(
          'Vui lòng chọn tỉnh/thành, phường/xã và nhập địa chỉ chi tiết trước khi tìm.'
        );

        return;
      }

      const diaDiemCoSan = timDiaDiemCoSan();

      if (
        diaDiemCoSan
        && diaDiemCoSan.viDo
        && diaDiemCoSan.kinhDo
      ) {
        setMarker(
          diaDiemCoSan.viDo,
          diaDiemCoSan.kinhDo
        );

        return;
      }

      const diaChi =
        chiTiet
        + ', ' + phuongXa
        + ', ' + tinhThanh
        + ', Việt Nam';

      btnTimBanDo.disabled = true;
      btnTimBanDo.textContent = 'Đang tìm...';

      try {
        let ketQua = await timTrenNominatim(diaChi);

        if (!ketQua) {
          ketQua = await timTrenPhoton(diaChi);
        }

        if (ketQua) {
          setMarker(
            ketQua.lat,
            ketQua.lng
          );
        } else {
          alert(
            'Không tìm thấy địa điểm. Bạn hãy nhấn trực tiếp lên bản đồ để chọn vị trí.'
          );
        }
      } catch (error) {
        alert(
          'Không thể tìm địa điểm lúc này. Bạn hãy nhấn trực tiếp lên bản đồ để chọn vị trí.'
        );
      } finally {
        btnTimBanDo.disabled = false;
        btnTimBanDo.textContent = 'Tìm trên bản đồ';
      }
    });

    const hinhAnhInput = document.getElementById('hinhAnh');
    const anhXemTruoc = document.getElementById('anhXemTruoc');
    const noiDungMacDinhAnh = document.getElementById('noiDungMacDinhAnh');
    const btnXoaAnh = document.getElementById('btnXoaAnh');

    hinhAnhInput.addEventListener('change', function () {
      const file = this.files[0];

      if (!file) {
        return;
      }

      const reader = new FileReader();

      reader.onload = function (event) {
        anhXemTruoc.src = event.target.result;
        anhXemTruoc.classList.remove('d-none');
        noiDungMacDinhAnh.classList.add('d-none');
        btnXoaAnh.classList.remove('d-none');
      };

      reader.readAsDataURL(file);
    });

    btnXoaAnh.addEventListener('click', function () {
      hinhAnhInput.value = '';
      anhXemTruoc.src = '';
      anhXemTruoc.classList.add('d-none');
      noiDungMacDinhAnh.classList.remove('d-none');
      btnXoaAnh.classList.add('d-none');
    });

    setTimeout(function () {
      map.invalidateSize();
    }, 200);
  });
</script>
@endsection