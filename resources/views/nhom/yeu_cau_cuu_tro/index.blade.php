@extends('layouts.nhom')

@section('title', 'Yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">Tổng quan nhóm</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Yêu cầu cứu trợ</li>
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
    <ul class="nav nav-tabs card-header-tabs" id="yeuCauTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="cho-tiep-nhan-tab" data-bs-toggle="tab" data-bs-target="#cho-tiep-nhan"
          type="button" role="tab">
          Chờ tiếp nhận ({{ $yeuCausChoTiepNhan->count() }})
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="da-tiep-nhan-tab" data-bs-toggle="tab" data-bs-target="#da-tiep-nhan"
          type="button" role="tab">
          Nhóm đã tiếp nhận ({{ $yeuCausDaTiepNhan->count() }})
        </button>
      </li>
    </ul>
  </div>

  <div class="card-body">
    <div class="tab-content" id="yeuCauTabsContent">
      <div class="tab-pane fade show active" id="cho-tiep-nhan" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Thông tin yêu cầu</th>
                <th style="width: 130px;">Số hộ</th>
                <th style="width: 150px;">Mức độ</th>
                <th style="width: 180px;">Thời gian gửi</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($yeuCausChoTiepNhan as $yeuCau)
                <tr class="clickable-row"
                    data-href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}"
                    style="cursor: pointer;">
                  <td class="text-center">{{ $yeuCau->idYeuCau }}</td>

                  <td>
                    <div class="fw-semibold">{{ $yeuCau->loaiYeuCau }}</div>
                    <small class="text-muted">
                      Người gửi: {{ $yeuCau->nguoiGui->hoTen ?? '-' }}
                    </small>
                    <br>
                    <small class="text-muted">
                      {{ $yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
                      {{ $yeuCau->diaDiem->phuongXa ?? '' }},
                      {{ $yeuCau->diaDiem->tinhThanh ?? '' }}
                    </small>
                  </td>

                  <td class="text-center">{{ $yeuCau->soHoDan ?? '-' }}</td>

                  <td class="text-center">
                    @if ($yeuCau->mucDoKhanCap == 'Khẩn cấp')
                      <span class="text-danger fw-semibold">Khẩn cấp</span>
                    @elseif ($yeuCau->mucDoKhanCap == 'Cao')
                      <span class="text-warning fw-semibold">Cao</span>
                    @else
                      {{ $yeuCau->mucDoKhanCap ?? '-' }}
                    @endif
                  </td>

                  <td class="text-center">{{ $yeuCau->thoiGianGui ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    Hiện chưa có yêu cầu cứu trợ nào đang chờ tiếp nhận.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="da-tiep-nhan" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Thông tin yêu cầu</th>
                <th style="width: 160px;">Chiến dịch</th>
                <th style="width: 160px;">Trạng thái</th>
                <th style="width: 180px;">Thời gian gửi</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($yeuCausDaTiepNhan as $yeuCau)
                @php
                  $tiepNhanCuaNhom = $yeuCau->tiepNhans->firstWhere('idNhom', $nhom->idNhom);
                @endphp

                <tr class="clickable-row"
                    data-href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}"
                    style="cursor: pointer;">
                  <td class="text-center">{{ $yeuCau->idYeuCau }}</td>

                  <td>
                    <div class="fw-semibold">{{ $yeuCau->loaiYeuCau }}</div>
                    <small class="text-muted">
                      Người gửi: {{ $yeuCau->nguoiGui->hoTen ?? '-' }}
                    </small>
                    <br>
                    <small class="text-muted">
                      {{ $yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
                      {{ $yeuCau->diaDiem->phuongXa ?? '' }},
                      {{ $yeuCau->diaDiem->tinhThanh ?? '' }}
                    </small>
                  </td>

                  <td class="text-center">
                    {{ $tiepNhanCuaNhom->chienDich->tenChienDich ?? '-' }}
                  </td>

                  <td class="text-center">
                    {{ $tiepNhanCuaNhom->trangThai ?? $yeuCau->trangThai }}
                  </td>

                  <td class="text-center">{{ $yeuCau->thoiGianGui ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    Nhóm chưa tiếp nhận yêu cầu cứu trợ nào.
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