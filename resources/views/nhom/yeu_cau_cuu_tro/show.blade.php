@extends('layouts.nhom')

@section('title', 'Chi tiết yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">Tổng quan nhóm</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}">Yêu cầu cứu trợ</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Chi tiết</li>
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

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0">{{ $yeuCau->tieuDeYeuCau }}</h5>
          <small class="text-muted">Mã yêu cầu: {{ $yeuCau->idYeuCau }}</small>
        </div>

        @if ($yeuCau->trangThai == 'Chờ tiếp nhận' && !$daDuocNhomTiepNhan)
          <div class="d-flex gap-2">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan') }}"
              class="btn btn-primary">
              Tiếp nhận vào chiến dịch có sẵn
            </a>

            @if ($laNhomTruong)
              <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tao-chien-dich') }}"
                class="btn btn-outline-primary">
                Tạo chiến dịch từ yêu cầu
              </a>
            @endif
          </div>
        @endif
      </div>

      <div class="card-body">
        <p>
          <strong>Người gửi:</strong>
          {{ $yeuCau->nguoiGui->hoTen ?? '-' }}
        </p>

        <p>
          <strong>Số điện thoại:</strong>
          {{ $yeuCau->nguoiGui->sdt ?? '-' }}
        </p>

        <p>
          <strong>Email:</strong>
          {{ $yeuCau->nguoiGui->email ?? '-' }}
        </p>

        <p>
          <strong>Trạng thái:</strong>
          {{ $yeuCau->trangThai }}
        </p>

        <p>
          <strong>Mức độ khẩn cấp:</strong>
          {{ $yeuCau->mucDoKhanCap ?? '-' }}
        </p>

        <p>
          <strong>Số người cần hỗ trợ:</strong>
          {{ $yeuCau->soNguoi ?? '-' }}
        </p>

        <p>
          <strong>Thời gian gửi:</strong>
          {{ $yeuCau->thoiGianGui ?? '-' }}
        </p>

        <p>
          <strong>Mô tả:</strong><br>
          {{ $yeuCau->moTa }}
        </p>

        @if ($yeuCau->hinhAnh)
          <div class="mt-3">
            <strong>Hình ảnh minh chứng:</strong>
            <div class="mt-2">
              <img src="{{ asset('storage/' . $yeuCau->hinhAnh) }}"
                class="img-fluid rounded border"
                style="max-height: 350px;">
            </div>
          </div>
        @endif
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header">
        <h5 class="mb-0">Thông tin tiếp nhận</h5>
      </div>

      <div class="card-body">
        @forelse ($yeuCau->tiepNhans as $tiepNhan)
          <div class="border rounded p-3 mb-3">
            <p class="mb-1">
              <strong>Nhóm tiếp nhận:</strong>
              {{ $tiepNhan->nhom->tenNhom ?? '-' }}
            </p>

            <p class="mb-1">
              <strong>Chiến dịch:</strong>
              {{ $tiepNhan->chienDich->tenChienDich ?? '-' }}
            </p>

            <p class="mb-1">
              <strong>Thời gian tiếp nhận:</strong>
              {{ $tiepNhan->thoiGianTiepNhan ?? '-' }}
            </p>

            <p class="mb-1">
              <strong>Dự kiến hỗ trợ:</strong>
              {{ $tiepNhan->thoiGianDuKienHoTro ?? '-' }}
            </p>

            <p class="mb-1">
              <strong>Trạng thái:</strong>
              {{ $tiepNhan->trangThai ?? '-' }}
            </p>

            @if ($tiepNhan->noiDungDamNhan)
              <p class="mb-0">
                <strong>Nội dung đảm nhận:</strong><br>
                {{ $tiepNhan->noiDungDamNhan }}
              </p>
            @endif
          </div>
        @empty
          <div class="text-muted">
            Yêu cầu này chưa được nhóm tình nguyện nào tiếp nhận.
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Địa điểm cần hỗ trợ</h5>
      </div>

      <div class="card-body">
        <p class="mb-1">
          <strong>Tỉnh/Thành:</strong>
          {{ $yeuCau->diaDiem->tinhThanh ?? '-' }}
        </p>

        <p class="mb-1">
          <strong>Phường/Xã:</strong>
          {{ $yeuCau->diaDiem->phuongXa ?? '-' }}
        </p>

        <p class="mb-3">
          <strong>Địa chỉ:</strong>
          {{ $yeuCau->diaDiem->chiTietDiaDiem ?? '-' }}
        </p>

        <div id="map" style="height: 280px;" class="rounded border"></div>
      </div>
    </div>

    <div class="mt-3">
      <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}" class="btn btn-secondary w-100">
        Quay lại danh sách
      </a>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const lat = Number('{{ $yeuCau->diaDiem->viDo ?? 16.047079 }}');
    const lng = Number('{{ $yeuCau->diaDiem->kinhDo ?? 108.206230 }}');

    const map = L.map('map').setView([lat, lng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
    }).addTo(map);

    L.marker([lat, lng]).addTo(map);
  });
</script>
@endsection