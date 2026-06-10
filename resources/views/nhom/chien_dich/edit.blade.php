@extends('layouts.nhom')

@section('title', 'Sửa chiến dịch | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #chienDichDiaDiemMap {
    height: 380px;
    width: 100%;
    border-radius: 12px;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Sửa chiến dịch cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}">Chiến dịch</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}">Chi tiết</a>
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

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

<form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}" method="POST">
  @csrf
  @method('PUT')

  <div class="card mb-3">
    <div class="card-header">
      <h5 class="mb-0">Thông tin chiến dịch</h5>
    </div>

    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Tên chiến dịch <span class="text-danger">*</span></label>
        <input type="text"
               name="tenChienDich"
               class="form-control"
               value="{{ old('tenChienDich', $chienDich->tenChienDich) }}"
               placeholder="Ví dụ: Cứu trợ mưa lũ Hòa Khánh, Tặng áo ấm vùng cao">
      </div>

      <div class="mb-3">
        <label class="form-label">Sự kiện cứu trợ <span class="text-danger">*</span></label>
        <select name="idSuKien" class="form-control">
          <option value="">-- Chọn sự kiện cứu trợ --</option>

          @foreach ($suKiens as $suKien)
            <option value="{{ $suKien->idSuKien }}"
              {{ old('idSuKien', $chienDich->idSuKien) == $suKien->idSuKien ? 'selected' : '' }}>
              {{ $suKien->tenSuKien }} - {{ $suKien->loaiSuKien }}
            </option>
          @endforeach
        </select>

        <small class="text-muted">
          Sự kiện cứu trợ là chủ đề/bối cảnh chung, ví dụ: Bão lũ miền Trung, Hỗ trợ hộ nghèo, Hỗ trợ trẻ em khó khăn.
        </small>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày bắt đầu</label>
          <input type="date"
                 name="ngayBatDau"
                 class="form-control"
                 value="{{ old('ngayBatDau', $chienDich->ngayBatDau) }}">
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày kết thúc</label>
          <input type="date"
                 name="ngayKetThuc"
                 class="form-control"
                 value="{{ old('ngayKetThuc', $chienDich->ngayKetThuc) }}">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="moTa"
                  class="form-control"
                  rows="4"
                  placeholder="Mô tả mục tiêu, phạm vi hỗ trợ hoặc tình hình thực tế của chiến dịch">{{ old('moTa', $chienDich->moTa) }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Xác nhận cứu trợ</label>

        <div class="form-check">
          <input type="checkbox"
                 name="daXacNhanCuuTro"
                 value="1"
                 class="form-check-input"
                 id="daXacNhanCuuTro"
                 {{ old('daXacNhanCuuTro', $chienDich->daXacNhanCuuTro) ? 'checked' : '' }}>

          <label for="daXacNhanCuuTro" class="form-check-label">
            Đã xác nhận hoạt động cứu trợ
          </label>
        </div>

        <small class="text-muted">
          Có thể là xác nhận từ địa phương, cơ sở tiếp nhận, hoặc trường hợp không cần xác nhận do hỗ trợ cá nhân trực tiếp.
        </small>
      </div>

      <div class="mb-3">
        <label class="form-label">Ghi chú xác nhận</label>
        <textarea name="ghiChuXacNhan"
                  class="form-control"
                  rows="3"
                  placeholder="Ví dụ: Đã thông báo UBND phường..., đã được ban quản lý cơ sở xác nhận, hoặc không cần xác nhận do hỗ trợ cá nhân.">{{ old('ghiChuXacNhan', $chienDich->ghiChuXacNhan) }}</textarea>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h5 class="mb-0">Địa điểm chiến dịch</h5>
    </div>

    <div class="card-body">
      <input type="hidden"
             name="idDiaDiemCoSan"
             id="idDiaDiemCoSan"
             value="{{ old('idDiaDiemCoSan', $chienDich->idDiaDiem) }}">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Tỉnh/Thành <span class="text-danger">*</span></label>
          <select name="tinhThanh" id="tinhThanh" class="form-control">
            <option value="">-- Chọn tỉnh/thành --</option>

            @foreach ($diaDiems->pluck('tinhThanh')->unique()->values() as $tinhThanh)
              <option value="{{ $tinhThanh }}"
                {{ old('tinhThanh', $chienDich->diaDiem->tinhThanh ?? '') == $tinhThanh ? 'selected' : '' }}>
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

        <input type="text"
               name="chiTietDiaDiem"
               id="chiTietDiaDiem"
               class="form-control"
               list="danhSachDiaDiem"
               value="{{ old('chiTietDiaDiem', $chienDich->diaDiem->chiTietDiaDiem ?? '') }}"
               placeholder="Gõ địa chỉ chi tiết hoặc chọn địa điểm đã có">

        <datalist id="danhSachDiaDiem"></datalist>

        <small class="text-muted">
          Nếu chọn địa điểm đã có, hệ thống sẽ dùng lại địa điểm đó. Nếu nhập địa điểm mới, hãy chọn vị trí trên bản đồ.
        </small>
      </div>

      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label mb-0">Chọn vị trí trên bản đồ <span class="text-danger">*</span></label>

          <button type="button" id="btnTimTrenBanDo" class="btn btn-sm btn-outline-primary">
            Tìm trên bản đồ
          </button>
        </div>

        <small class="text-muted">
          Bạn có thể bấm “Tìm trên bản đồ” để hệ thống gợi ý vị trí gần đúng, sau đó click trực tiếp trên bản đồ để chọn chính xác vị trí chiến dịch.
        </small>

        <div id="chienDichDiaDiemMap" class="mt-2"></div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Vĩ độ</label>
          <input type="text"
                 name="viDo"
                 id="viDo"
                 class="form-control"
                 value="{{ old('viDo', $chienDich->diaDiem->viDo ?? '') }}"
                 readonly>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Kinh độ</label>
          <input type="text"
                 name="kinhDo"
                 id="kinhDo"
                 class="form-control"
                 value="{{ old('kinhDo', $chienDich->diaDiem->kinhDo ?? '') }}"
                 readonly>
        </div>
      </div>

      <div class="mb-0">
        <label class="form-label">Trạng thái</label>
        <select name="trangThai" class="form-control">
          <option value="Đang hoạt động" {{ old('trangThai', $chienDich->trangThai) == 'Đang hoạt động' ? 'selected' : '' }}>
            Đang hoạt động
          </option>
          <option value="Tạm ngưng" {{ old('trangThai', $chienDich->trangThai) == 'Tạm ngưng' ? 'selected' : '' }}>
            Tạm ngưng
          </option>
          <option value="Hoàn thành" {{ old('trangThai', $chienDich->trangThai) == 'Hoàn thành' ? 'selected' : '' }}>
            Hoàn thành
          </option>
        </select>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
      Cập nhật
    </button>

    <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}" class="btn btn-secondary">
      Quay lại chi tiết
    </a>
  </div>
</form>

<input type="hidden" id="oldPhuongXa" value="{{ old('phuongXa', $chienDich->diaDiem->phuongXa ?? '') }}">

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

    const defaultLat = parseFloat(viDoInput.value) || 16.047079;
    const defaultLng = parseFloat(kinhDoInput.value) || 108.206230;

    const map = L.map('chienDichDiaDiemMap').setView([defaultLat, defaultLng], 12);

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

      map.setView([lat, lng], 16);
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
      )];

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
      });

      diaDiemPhuHop.forEach(function (diaDiem) {
        const option = document.createElement('option');
        option.value = diaDiem.chiTietDiaDiem;
        danhSachDiaDiem.appendChild(option);
      });
    }

    tinhThanhSelect.addEventListener('change', function () {
      idDiaDiemCoSanInput.value = '';
      chiTietDiaDiemInput.value = '';
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
      const diaDiem = diaDiems.find(function (item) {
        return item.tinhThanh === tinhThanhSelect.value
          && item.phuongXa === phuongXaSelect.value
          && item.chiTietDiaDiem === chiTietDiaDiemInput.value;
      });

      if (diaDiem) {
        idDiaDiemCoSanInput.value = diaDiem.idDiaDiem;

        if (diaDiem.viDo && diaDiem.kinhDo) {
          setLocation(parseFloat(diaDiem.viDo), parseFloat(diaDiem.kinhDo));
        }
      } else {
        idDiaDiemCoSanInput.value = '';
      }
    });

    if (viDoInput.value && kinhDoInput.value) {
      setLocation(defaultLat, defaultLng);
    }

    map.on('click', function (e) {
      setLocation(e.latlng.lat, e.latlng.lng);
    });

    btnTimTrenBanDo.addEventListener('click', function () {
      const chiTiet = chiTietDiaDiemInput.value.trim();
      const phuongXa = phuongXaSelect.value.trim();
      const tinhThanh = tinhThanhSelect.value.trim();

      const diaChi = [chiTiet, phuongXa, tinhThanh, 'Việt Nam']
        .filter(Boolean)
        .join(', ');

      if (!tinhThanh && !phuongXa && !chiTiet) {
        alert('Vui lòng nhập ít nhất một thông tin địa chỉ trước khi tìm.');
        return;
      }

      fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(diaChi))
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (!data || data.length === 0) {
            alert('Không tìm thấy địa điểm phù hợp. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
            return;
          }

          const lat = parseFloat(data[0].lat);
          const lng = parseFloat(data[0].lon);

          setLocation(lat, lng);
        })
        .catch(function () {
          alert('Không thể tìm địa điểm lúc này. Bạn có thể click trực tiếp trên bản đồ để chọn vị trí.');
        });
    });

    loadPhuongXa();
    loadDiaDiemOptions();
  });
</script>
@endsection