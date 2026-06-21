@extends('layouts.user')

@section('title', 'Chi tiết nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

@php
  if ($nhomTinhNguyen->anhDaiDien ?? false) {
      $duongDanAnhNhom = asset('storage/' . $nhomTinhNguyen->anhDaiDien);
  } else {
      $duongDanAnhNhom = asset('storage/nhom-tinh-nguyen/group.jpg');
  }

  $diaDiemNhom = $nhomTinhNguyen->diaDiem;
  $coToaDoNhom = $diaDiemNhom && $diaDiemNhom->viDo && $diaDiemNhom->kinhDo;

  $trangThaiNhom = $nhomTinhNguyen->trangThai == 'Hoạt động'
      ? 'Đang hoạt động'
      : ($nhomTinhNguyen->trangThai ?? '-');

  $classChamTrangThai = match ($trangThaiNhom) {
      'Đang hoạt động' => 'status-active',
      'Tạm ngừng hoạt động' => 'status-paused',
      'Ngừng hoạt động' => 'status-stopped',
      'Bị khóa' => 'status-locked',
      default => 'status-stopped',
  };
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết nhóm tình nguyện</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-tinh-nguyen') }}">Nhóm tình nguyện</a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Chi tiết
          </li>
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

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <ul class="nav nav-tabs card-header-tabs" id="nhomTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active"
                  id="thong-tin-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#thong-tin"
                  type="button"
                  role="tab">
            Thông tin nhóm
          </button>
        </li>

        <li class="nav-item" role="presentation">
          <button class="nav-link"
                  id="thanh-vien-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#thanh-vien"
                  type="button"
                  role="tab">
            Thành viên
          </button>
        </li>

        <li class="nav-item" role="presentation">
          <button class="nav-link"
                  id="chien-dich-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#chien-dich"
                  type="button"
                  role="tab">
            Chiến dịch
          </button>
        </li>

        <li class="nav-item" role="presentation">
          <button class="nav-link"
                  id="yeu-cau-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#yeu-cau"
                  type="button"
                  role="tab">
            Yêu cầu đã nhận
          </button>
        </li>
      </ul>

      <a href="{{ url('/user/nhom-tinh-nguyen') }}" class="btn btn-secondary">
        Quay lại
      </a>
    </div>
  </div>

  <div class="card-body">
    <div class="tab-content" id="nhomTabsContent">
      <div class="tab-pane fade show active" id="thong-tin" role="tabpanel">
        <div class="group-info-wrapper">
          <div class="row align-items-center justify-content-center">
            <div class="col-lg-5 col-md-5 mb-4 mb-md-0 d-flex justify-content-center">
              <div class="group-avatar-box">
                <img src="{{ $duongDanAnhNhom }}"
                     alt="Ảnh đại diện nhóm"
                     class="rounded-circle mb-3 group-avatar-img">

                <h5 class="mb-1">{{ $nhomTinhNguyen->tenNhom }}</h5>

                <span class="d-inline-flex align-items-center gap-2">
                  <span class="rounded-circle status-dot {{ $classChamTrangThai }} d-inline-block"></span>
                  {{ $trangThaiNhom }}
                </span>
              </div>
            </div>

            <div class="col-lg-7 col-md-7">
              <div class="row mb-3">
                <div class="col-md-4 text-muted">Mã nhóm</div>
                <div class="col-md-8 fw-semibold">{{ $nhomTinhNguyen->idNhom }}</div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">Tên nhóm</div>
                <div class="col-md-8 fw-semibold text-break">{{ $nhomTinhNguyen->tenNhom }}</div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">Nhóm trưởng</div>
                <div class="col-md-8">
                  {{ $nhomTinhNguyen->nhomTruong->hoTen ?? '-' }}

                  @if (!empty($nhomTinhNguyen->nhomTruong?->tenDangNhap))
                    <small class="text-muted d-block">
                      {{ $nhomTinhNguyen->nhomTruong->tenDangNhap }}
                    </small>
                  @endif
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">Địa điểm</div>
                <div class="col-md-8">
                  @if ($nhomTinhNguyen->diaDiem)
                    @if ($nhomTinhNguyen->diaDiem->chiTietDiaDiem)
                      {{ $nhomTinhNguyen->diaDiem->chiTietDiaDiem }},
                    @endif

                    @if ($nhomTinhNguyen->diaDiem->phuongXa)
                      {{ $nhomTinhNguyen->diaDiem->phuongXa }},
                    @endif

                    {{ $nhomTinhNguyen->diaDiem->tinhThanh ?? '-' }}
                  @else
                    -
                  @endif
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">Trạng thái</div>
                <div class="col-md-8">
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle status-dot {{ $classChamTrangThai }} d-inline-block"></span>
                    {{ $trangThaiNhom }}
                  </span>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 text-muted">Ngày tạo</div>
                <div class="col-md-8">{{ $nhomTinhNguyen->ngayTao ?? '-' }}</div>
              </div>

              <div class="row">
                <div class="col-md-4 text-muted">Mô tả</div>
                <div class="col-md-8 text-break">
                  {{ $nhomTinhNguyen->moTa ?? 'Chưa có mô tả cho nhóm này.' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <h6 class="mb-0">Vị trí nhóm trên bản đồ</h6>
              <small class="text-muted">
                @if ($coToaDoNhom)
                  Tọa độ địa điểm của nhóm.
                @else
                  Nhóm này chưa có tọa độ địa điểm.
                @endif
              </small>
            </div>
          </div>

          @if ($coToaDoNhom)
            <div id="nhomViTriMap"></div>
          @else
            <div class="alert alert-warning mb-0">
              Nhóm này chưa có vĩ độ và kinh độ để hiển thị bản đồ.
            </div>
          @endif
        </div>
      </div>

      <div class="tab-pane fade" id="thanh-vien" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase">
                <th style="width: 90px;">Mã</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th style="width: 180px;">Vai trò</th>
                <th style="width: 180px;">Ngày tham gia</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($thanhViens as $thanhVien)
                <tr>
                  <td>{{ $thanhVien->idThanhVien }}</td>
                  <td class="fw-semibold">{{ $thanhVien->nguoiDung->hoTen ?? '-' }}</td>
                  <td>{{ $thanhVien->nguoiDung->email ?? '-' }}</td>
                  <td>{{ $thanhVien->nguoiDung->sdt ?? '-' }}</td>
                  <td>
                    @if (($thanhVien->vaiTro ?? '') === 'Nhóm trưởng')
                      Nhóm trưởng
                    @else
                      {{ $thanhVien->vaiTro ?? '-' }}
                    @endif
                  </td>
                  <td>{{ $thanhVien->ngayThamGia ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    Nhóm này chưa có thành viên nào.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="chien-dich" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Tên chiến dịch</th>
                <th class="text-start">Sự kiện cứu trợ</th>
                <th class="text-start">Địa điểm</th>
                <th style="width: 150px;">Trạng thái</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($chienDichs as $chienDich)
                <tr class="clickable-row"
                    data-href="{{ url('/user/chien-dich/' . $chienDich->idChienDich) }}">
                  <td class="text-center fw-semibold">{{ $chienDich->idChienDich }}</td>
                  <td class="fw-semibold">{{ $chienDich->tenChienDich }}</td>
                  <td>
                    @if ($chienDich->suKien)
                      {{ $chienDich->suKien->loaiSuKien ?? '-' }}
                      -
                      {{ $chienDich->suKien->tenSuKien ?? '-' }}
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    @if ($chienDich->diaDiem)
                      @if ($chienDich->diaDiem->chiTietDiaDiem)
                        {{ $chienDich->diaDiem->chiTietDiaDiem }},
                      @endif

                      @if ($chienDich->diaDiem->phuongXa)
                        {{ $chienDich->diaDiem->phuongXa }},
                      @endif

                      {{ $chienDich->diaDiem->tinhThanh ?? '-' }}
                    @else
                      -
                    @endif
                  </td>
                  <td class="text-center">{{ $chienDich->trangThai ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    Nhóm này chưa có chiến dịch cứu trợ nào.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="yeu-cau" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Yêu cầu cứu trợ</th>
                <th class="text-start">Địa điểm</th>
                <th style="width: 160px;">Trạng thái</th>
                <th style="width: 180px;">Thời gian</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($yeuCauDaNhans as $tiepNhan)
                @php
                  $yeuCau = $tiepNhan->yeuCau;
                @endphp

                <tr class="{{ $yeuCau ? 'clickable-row' : '' }}"
                    @if ($yeuCau)
                      data-href="{{ url('/user/yeu-cau-cong-dong/' . $yeuCau->idYeuCau) }}"
                    @endif>
                  <td class="text-center fw-semibold">{{ $tiepNhan->idTiepNhan }}</td>
                  <td>
                    <div class="fw-semibold">
                      {{ $yeuCau->tieuDeYeuCau ?? '-' }}
                    </div>

                    @if (!empty($yeuCau?->nguoiGui?->hoTen))
                      <small class="text-muted">
                        Người gửi: {{ $yeuCau->nguoiGui->hoTen }}
                      </small>
                    @endif
                  </td>
                  <td>
                    @if ($yeuCau && $yeuCau->diaDiem)
                      @if ($yeuCau->diaDiem->chiTietDiaDiem)
                        {{ $yeuCau->diaDiem->chiTietDiaDiem }},
                      @endif

                      @if ($yeuCau->diaDiem->phuongXa)
                        {{ $yeuCau->diaDiem->phuongXa }},
                      @endif

                      {{ $yeuCau->diaDiem->tinhThanh ?? '-' }}
                    @else
                      -
                    @endif
                  </td>
                  <td class="text-center">{{ $tiepNhan->trangThai ?? '-' }}</td>
                  <td class="text-center">{{ $tiepNhan->thoiGianTiepNhan ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    Nhóm này chưa tiếp nhận yêu cầu cứu trợ nào.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  #nhomTabs .nav-link {
    font-weight: 500;
  }

  .table th,
  .table td {
    vertical-align: middle;
  }
  .clickable-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .clickable-row:hover {
    background-color: #f5f7fb;
  }


  .group-info-wrapper {
    max-width: 1050px;
    margin: 0 auto;
  }

  .group-avatar-box {
    width: 100%;
    max-width: 280px;
    text-align: center;
  }

  .group-avatar-img {
    width: 140px;
    height: 140px;
    object-fit: cover;
  }

  #nhomViTriMap {
    height: 360px;
    width: 100%;
    border-radius: 12px;
  }

  .status-dot {
    width: 8px;
    height: 8px;
  }

  .status-active {
    background-color: #198754;
  }

  .status-paused {
    background-color: #fd7e14;
  }

  .status-stopped {
    background-color: #6c757d;
  }

  .status-locked {
    background-color: #212529;
  }
</style>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.clickable-row').forEach(function (row) {
      row.addEventListener('click', function () {
        const href = row.dataset.href;

        if (href) {
          window.location.href = href;
        }
      });
    });
  });
</script>

@if ($coToaDoNhom)
  <div id="nhomMapData"
       data-lat="{{ $diaDiemNhom->viDo }}"
       data-lng="{{ $diaDiemNhom->kinhDo }}"
       data-ten-nhom="{{ e($nhomTinhNguyen->tenNhom) }}"
       data-chi-tiet="{{ e($diaDiemNhom->chiTietDiaDiem ?? '') }}"
       data-phuong-xa="{{ e($diaDiemNhom->phuongXa ?? '') }}"
       data-tinh-thanh="{{ e($diaDiemNhom->tinhThanh ?? '') }}"
       hidden>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const mapData = document.getElementById('nhomMapData');

      if (!mapData || !window.L) {
        return;
      }

      const lat = parseFloat(mapData.dataset.lat);
      const lng = parseFloat(mapData.dataset.lng);

      if (Number.isNaN(lat) || Number.isNaN(lng)) {
        return;
      }

      const tenNhom = mapData.dataset.tenNhom || 'Nhóm tình nguyện';
      const chiTietDiaDiem = mapData.dataset.chiTiet || '';
      const phuongXa = mapData.dataset.phuongXa || '';
      const tinhThanh = mapData.dataset.tinhThanh || '';

      const diaChiParts = [];

      if (chiTietDiaDiem) {
        diaChiParts.push(chiTietDiaDiem);
      }

      if (phuongXa) {
        diaChiParts.push(phuongXa);
      }

      if (tinhThanh) {
        diaChiParts.push(tinhThanh);
      }

      const diaChi = diaChiParts.length > 0
        ? diaChiParts.join(', ')
        : 'Chưa có thông tin địa chỉ';

      const map = L.map('nhomViTriMap').setView([lat, lng], 15);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);

      L.marker([lat, lng])
        .addTo(map)
        .bindPopup(
          '<strong>' + tenNhom + '</strong><br>' +
          diaChi + '<br>' +
          '<small>Vĩ độ: ' + lat + ', Kinh độ: ' + lng + '</small>'
        )
        .openPopup();
    });
  </script>
@endif
@endsection