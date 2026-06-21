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
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
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

@if (request('tuKhoa'))
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      Đang tìm kiếm:
      <strong>{{ request('tuKhoa') }}</strong>
    </div>

    <a href="{{ url('/user/nhom-cua-toi') }}"
       class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Nhóm tình nguyện của tôi</h5>
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
          Nhóm tham gia ({{ $nhomThamGias->count() }})
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="cho-duyet-tab" data-bs-toggle="tab" data-bs-target="#cho-duyet"
          type="button" role="tab">
          Chờ duyệt ({{ $nhomChoDuyets->count() }})
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tu-choi-tab" data-bs-toggle="tab" data-bs-target="#tu-choi"
                type="button" role="tab">
          Từ chối ({{ $nhomTuChois->count() }})
        </button>
      </li>
    </ul>

    <div class="tab-content pt-3" id="nhomCuaToiTabsContent">
      <div class="tab-pane fade show active" id="tham-gia" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Tên nhóm</th>
                <th class="text-start" style="width: 150px;">Nhóm trưởng</th>
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
                    <td class="text-center">{{ $nhom->idNhom }}</td>

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

                  <td class="text-center">
                    @if ($nhom->ngayTao)
                      {{ \Carbon\Carbon::parse($nhom->ngayTao)->format('d/m/Y H:i') }}
                    @else
                      -
                    @endif
                  </td>
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

      <div class="tab-pane fade" id="tu-choi" role="tabpanel" aria-labelledby="tu-choi-tab">
        <div class="table-responsive">
          <table class="table table-hover mb-0 nhom-tu-choi-table">
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
              @forelse ($nhomTuChois as $nhom)
                <tr class="nhom-tu-choi-row"
                    data-id="{{ $nhom->idNhom }}">

                  <td class="text-center fw-semibold">
                    {{ $nhom->idNhom }}
                  </td>

                  <td class="ten-nhom-tu-choi-cell">
                    <div class="ten-nhom-tu-choi">
                      {{ $nhom->tenNhom }}
                    </div>

                    <div class="text-muted mo-ta-nhom-tu-choi">
                      {{ $nhom->moTa ?: 'Chưa có mô tả' }}
                    </div>
                  </td>

                  <td class="dia-diem-tu-choi">
                    @if ($nhom->diaDiem)
                      @if (!empty($nhom->diaDiem->chiTietDiaDiem))
                        {{ $nhom->diaDiem->chiTietDiaDiem }},
                      @endif

                      @if (!empty($nhom->diaDiem->phuongXa))
                        {{ $nhom->diaDiem->phuongXa }},
                      @endif

                      {{ $nhom->diaDiem->tinhThanh ?? '-' }}
                    @else
                      -
                    @endif
                  </td>

                  <td class="text-center cot-trang-thai-tu-choi">
                    <span class="d-inline-flex align-items-center justify-content-center gap-2">
                      <span class="rounded-circle bg-danger d-inline-block"
                            style="width: 8px; height: 8px;"></span>

                      <span>Từ chối</span>
                    </span>
                  </td>

                  <td class="text-center cot-ngay-tao-tu-choi">
                    @if ($nhom->ngayTao)
                      {{ \Carbon\Carbon::parse($nhom->ngayTao)->format('d/m/Y H:i') }}
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5"
                      class="text-center text-muted py-4">
                    Bạn không có đăng ký tạo nhóm nào bị từ chối.
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
  .nhom-tu-choi-table {
    table-layout: fixed;
    width: 100%;
  }

  .nhom-tu-choi-table th,
  .nhom-tu-choi-table td {
    vertical-align: middle;
  }

  .nhom-tu-choi-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .nhom-tu-choi-row:hover {
    background-color: #f5f7fb;
  }

  .nhom-tu-choi-row.expanded {
    background-color: #f3f4f6;
  }

  .ten-nhom-tu-choi-cell {
    min-width: 0;
  }

  .ten-nhom-tu-choi {
    width: 100%;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .mo-ta-nhom-tu-choi {
    width: 100%;
    min-width: 0;
    margin-top: 2px;
    display: block;
    font-size: 13px;
    line-height: 1.5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .nhom-tu-choi-row.expanded .ten-nhom-tu-choi,
  .nhom-tu-choi-row.expanded .mo-ta-nhom-tu-choi {
    white-space: normal;
    overflow: visible;
    text-overflow: unset;
    word-break: break-word;
  }

  .nhom-tu-choi-row.expanded .mo-ta-nhom-tu-choi {
    margin-top: 6px;
  }

  .dia-diem-tu-choi,
  .cot-trang-thai-tu-choi,
  .cot-ngay-tao-tu-choi {
    word-break: break-word;
  }
</style>

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

    const tuChoiRows =
      document.querySelectorAll('.nhom-tu-choi-row');

    tuChoiRows.forEach(function (row) {
      row.addEventListener('click', function () {
        const dangMoRong =
          row.classList.contains('expanded');

        tuChoiRows.forEach(function (item) {
          item.classList.remove('expanded');
          item.classList.remove('table-active');
        });

        if (!dangMoRong) {
          row.classList.add('expanded');
          row.classList.add('table-active');
        }
      });
    });
  });
</script>
@endsection