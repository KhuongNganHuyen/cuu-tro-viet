@extends('layouts.user')

@section('title', 'Nhóm tình nguyện của tôi | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Nhóm tình nguyện của tôi</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Nhóm tình nguyện của tôi</li>
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
  <div class="col-md-6 col-xl-6">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Nhóm tham gia</h6>
        <h4 class="mb-3">{{ $nhomThamGias->count() }}</h4>
        <p class="mb-0 text-muted text-sm">Nhóm bạn đang tham gia hoặc quản lý</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-6">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Đang chờ duyệt</h6>
        <h4 class="mb-3">{{ $nhomChoDuyets->count() }}</h4>
        <p class="mb-0 text-muted text-sm">Đăng ký tạo nhóm đang chờ admin duyệt</p>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Nhóm tình nguyện của tôi</h5>
      <small class="text-muted">
        Theo dõi nhóm bạn tham gia và các đăng ký tạo nhóm đang chờ duyệt.
      </small>
    </div>

    <a href="{{ url('/user/nhom-cua-toi/create') }}" class="btn btn-primary">
      Đăng ký tạo nhóm mới
    </a>
  </div>

  <div class="card-body">
    <ul class="nav nav-tabs" id="nhomCuaToiTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tham-gia-tab" data-bs-toggle="tab" data-bs-target="#tham-gia"
          type="button" role="tab">
          Nhóm tham gia
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="cho-duyet-tab" data-bs-toggle="tab" data-bs-target="#cho-duyet"
          type="button" role="tab">
          Chờ duyệt
        </button>
      </li>
    </ul>

    <div class="tab-content pt-3" id="nhomCuaToiTabsContent">
      <div class="tab-pane fade show active" id="tham-gia" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã TV</th>
                <th class="text-start">Tên nhóm</th>
                <th class="text-start">Nhóm trưởng</th>
                <th class="text-start">Địa điểm</th>
                <th style="width: 170px;">Vai trò của tôi</th>
                <th style="width: 160px;">Trạng thái</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($nhomThamGias as $thanhVien)
                @php
                  $nhom = $thanhVien->nhom;
                @endphp

                @if ($nhom)
                  <tr class="clickable-row"
                      data-href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}"
                      style="cursor: pointer;">
                    <td class="text-center">{{ $thanhVien->idThanhVien }}</td>

                    <td>
                      <div class="fw-semibold">{{ $nhom->tenNhom }}</div>
                      @if ($nhom->moTa)
                        <small class="text-muted">
                          {{ \Illuminate\Support\Str::limit($nhom->moTa, 70) }}
                        </small>
                      @endif
                    </td>

                    <td>{{ $nhom->nhomTruong->hoTen ?? '-' }}</td>

                    <td>
                      @if ($nhom->diaDiem)
                        @if ($nhom->diaDiem->chiTietDiaDiem)
                          {{ $nhom->diaDiem->chiTietDiaDiem }},
                        @endif
                        @if ($nhom->diaDiem->phuongXa)
                          {{ $nhom->diaDiem->phuongXa }},
                        @endif
                        {{ $nhom->diaDiem->tinhThanh }}
                      @else
                        -
                      @endif
                    </td>

                    <td class="text-center">
                      {{ $thanhVien->vaiTro ?? 'Thành viên' }}
                    </td>

                    <td class="text-center">
                      @if ($nhom->trangThai == 'Đang hoạt động')
                        <span class="d-inline-flex align-items-center gap-2">
                          <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                          {{ $nhom->trangThai }}
                        </span>
                      @else
                        <span class="d-inline-flex align-items-center gap-2">
                          <span class="rounded-circle bg-secondary d-inline-block" style="width: 8px; height: 8px;"></span>
                          {{ $nhom->trangThai }}
                        </span>
                      @endif
                    </td>
                  </tr>
                @endif
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    Bạn chưa tham gia nhóm tình nguyện nào.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="cho-duyet" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Tên nhóm</th>
                <th class="text-start">Địa điểm</th>
                <th style="width: 160px;">Trạng thái</th>
                <th style="width: 180px;">Ngày tạo</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($nhomChoDuyets as $nhom)
                <tr class="clickable-row"
                    data-href="{{ url('/user/nhom-cua-toi/' . $nhom->idNhom) }}"
                    style="cursor: pointer;">
                  <td class="text-center">{{ $nhom->idNhom }}</td>

                  <td>
                    <div class="fw-semibold">{{ $nhom->tenNhom }}</div>
                    @if ($nhom->moTa)
                      <small class="text-muted">
                        {{ \Illuminate\Support\Str::limit($nhom->moTa, 70) }}
                      </small>
                    @endif
                  </td>

                  <td>
                    @if ($nhom->diaDiem)
                      @if ($nhom->diaDiem->chiTietDiaDiem)
                        {{ $nhom->diaDiem->chiTietDiaDiem }},
                      @endif
                      @if ($nhom->diaDiem->phuongXa)
                        {{ $nhom->diaDiem->phuongXa }},
                      @endif
                      {{ $nhom->diaDiem->tinhThanh }}
                    @else
                      -
                    @endif
                  </td>

                  <td class="text-center">
                    <span class="d-inline-flex align-items-center gap-2">
                      <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                      {{ $nhom->trangThai }}
                    </span>
                  </td>

                  <td class="text-center">{{ $nhom->ngayTao ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    Bạn không có đăng ký tạo nhóm nào đang chờ duyệt.
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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.clickable-row').forEach(function (row) {
      row.addEventListener('click', function () {
        const href = row.getAttribute('data-href');

        if (href) {
          window.location.href = href;
        }
      });
    });
  });
</script>
@endsection