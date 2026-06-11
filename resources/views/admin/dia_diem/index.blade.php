@extends('layouts.admin')

@section('title', 'Quản lý địa điểm | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  #diaDiemMap {
    height: 520px;
    width: 100%;
    border-radius: 12px;
  }

  .dia-diem-row {
    cursor: pointer;
  }

  .dia-diem-row:hover {
    background-color: #f5f7fb;
  }

  .map-note {
    font-size: 13px;
  }

  .table th,
  .table td {
    vertical-align: middle;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý địa điểm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Địa điểm</li>
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

@if (request('tuKhoa'))
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      Đang tìm kiếm:
      <strong>{{ request('tuKhoa') }}</strong>
    </div>

    <a href="{{ url('/admin/dia-diem') }}" class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="row">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
          <h5 class="mb-1">Danh sách địa điểm</h5>
          <small class="text-muted">
            Tổng hiển thị: {{ $diaDiems->count() }}
          </small>
        </div>

        <a href="{{ url('/admin/dia-diem/create') }}" class="btn btn-primary">
          Thêm
        </a>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 70px;">Mã</th>
                <th class="text-start">Tỉnh/Thành</th>
                <th class="text-start">Phường/Xã</th>
                <th class="text-start">Chi tiết</th>
                <th style="width: 120px;">Tọa độ</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($diaDiems as $diaDiem)
                <tr class="dia-diem-row {{ session('diaDiemMoi') == $diaDiem->idDiaDiem ? 'table-primary' : '' }}"
                    data-id="{{ $diaDiem->idDiaDiem }}"
                    data-tinh-thanh="{{ $diaDiem->tinhThanh }}"
                    data-phuong-xa="{{ $diaDiem->phuongXa }}"
                    data-chi-tiet="{{ $diaDiem->chiTietDiaDiem }}"
                    data-vi-do="{{ $diaDiem->viDo }}"
                    data-kinh-do="{{ $diaDiem->kinhDo }}">
                  <td class="text-center fw-semibold">
                    {{ $diaDiem->idDiaDiem }}
                  </td>

                  <td>
                    {{ $diaDiem->tinhThanh }}
                  </td>

                  <td>
                    {{ $diaDiem->phuongXa ?? '-' }}
                  </td>

                  <td>
                    {{ $diaDiem->chiTietDiaDiem ?? '-' }}
                  </td>

                  <td class="text-center">
                    @if ($diaDiem->viDo && $diaDiem->kinhDo)
                      <span class="d-inline-flex align-items-center justify-content-center gap-2">
                        <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                        <span>Có</span>
                      </span>
                    @else
                      <span class="d-inline-flex align-items-center justify-content-center gap-2 text-muted">
                        <span class="rounded-circle bg-secondary d-inline-block" style="width: 8px; height: 8px;"></span>
                        <span>Chưa có</span>
                      </span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    @if (request('tuKhoa'))
                      Không tìm thấy địa điểm phù hợp.
                    @else
                      Chưa có địa điểm nào.
                    @endif
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Bản đồ địa điểm</h5>
        <small class="text-muted">Hiển thị địa điểm đã có tọa độ</small>
      </div>

      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <div class="fw-semibold" id="selectedTitle">Chưa chọn địa điểm</div>
            <div class="text-muted" id="selectedAddress">
              Click vào một hàng trong bảng để xem vị trí.
            </div>
            <small class="text-danger d-none" id="selectedWarning">
              Địa điểm này chưa có tọa độ, cần bổ sung vĩ độ và kinh độ.
            </small>
          </div>

          <a href="#" id="selectedDetailBtn" class="btn btn-sm btn-primary d-none">
            Chi tiết
          </a>
        </div>

        <div id="diaDiemMap"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script id="diaDiemsData" type="application/json">
  @json($diaDiems)
</script>

<script>
  const diaDiems = JSON.parse(document.getElementById('diaDiemsData').textContent);

  const defaultLat = 16.047079;
  const defaultLng = 108.206230;

  const map = L.map('diaDiemMap').setView([defaultLat, defaultLng], 6);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  const markers = {};

  function buildAddress(diaDiem) {
    const parts = [];

    if (diaDiem.chiTietDiaDiem) {
      parts.push(diaDiem.chiTietDiaDiem);
    }

    if (diaDiem.phuongXa) {
      parts.push(diaDiem.phuongXa);
    }

    if (diaDiem.tinhThanh) {
      parts.push(diaDiem.tinhThanh);
    }

    return parts.length ? parts.join(', ') : 'Chưa có thông tin địa chỉ';
  }

  diaDiems.forEach(function(diaDiem) {
    if (diaDiem.viDo && diaDiem.kinhDo) {
      const lat = parseFloat(diaDiem.viDo);
      const lng = parseFloat(diaDiem.kinhDo);

      const popupContent = `
        <strong>${diaDiem.tinhThanh ?? 'Địa điểm'}</strong><br>
        ${buildAddress(diaDiem)}<br>
        <small>Vĩ độ: ${lat}, Kinh độ: ${lng}</small>
      `;

      const marker = L.marker([lat, lng])
        .addTo(map)
        .bindPopup(popupContent);

      markers[diaDiem.idDiaDiem] = marker;
    }
  });

  const allMarkerList = Object.values(markers);

  if (allMarkerList.length > 0) {
    const group = L.featureGroup(allMarkerList);
    map.fitBounds(group.getBounds().pad(0.2));
  }

  document.querySelectorAll('.dia-diem-row').forEach(function(row) {
    row.addEventListener('click', function() {
      const id = this.dataset.id;
      const tinhThanh = this.dataset.tinhThanh || '';
      const phuongXa = this.dataset.phuongXa || '';
      const chiTiet = this.dataset.chiTiet || '';
      const viDo = this.dataset.viDo;
      const kinhDo = this.dataset.kinhDo;

      document.getElementById('selectedTitle').innerText = tinhThanh || 'Địa điểm';
      document.getElementById('selectedAddress').innerText =
        [chiTiet, phuongXa, tinhThanh].filter(Boolean).join(', ') || 'Chưa có thông tin địa chỉ';

      const detailBtn = document.getElementById('selectedDetailBtn');
      const warningText = document.getElementById('selectedWarning');

      detailBtn.href = '/admin/dia-diem/' + id;
      detailBtn.classList.remove('d-none');

      if (viDo && kinhDo) {
        warningText.classList.add('d-none');

        const lat = parseFloat(viDo);
        const lng = parseFloat(kinhDo);

        map.setView([lat, lng], 14);

        if (markers[id]) {
          markers[id].openPopup();
        }
      } else {
        warningText.classList.remove('d-none');
      }
    });
  });
</script>
@endsection