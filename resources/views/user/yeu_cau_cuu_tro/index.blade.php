@extends('layouts.user')

@section('title', 'Yêu cầu cứu trợ của tôi | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Yêu cầu cứu trợ của tôi</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Tổng quan</a>
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
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Danh sách yêu cầu cứu trợ</h5>
      <small class="text-muted">Theo dõi các yêu cầu hỗ trợ đã gửi.</small>
    </div>

    <a href="{{ url('/user/yeu-cau-cuu-tro/create') }}" class="btn btn-primary">
      Gửi yêu cầu
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Tiêu đề yêu cầu</th>
            <th style="width: 140px;">Số người</th>
            <th style="width: 170px;">Mức độ</th>
            <th style="width: 180px;">Trạng thái</th>
            <th style="width: 180px;">Thời gian gửi</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($yeuCaus as $yeuCau)
            <tr class="clickable-row"
                data-href="{{ url('/user/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}"
                style="cursor: pointer;">
              <td class="text-center">{{ $yeuCau->idYeuCau }}</td>

              <td>
                <div class="fw-semibold">{{ $yeuCau->tieuDeYeuCau }}</div>
                <small class="text-muted">
                  {{ $yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
                  {{ $yeuCau->diaDiem->phuongXa ?? '' }},
                  {{ $yeuCau->diaDiem->tinhThanh ?? '' }}
                </small>
              </td>

              <td class="text-center">
                {{ $yeuCau->soNguoi ?? '-' }}
              </td>

              <td class="text-center">
                {{ $yeuCau->mucDoKhanCap ?? '-' }}
              </td>

              <td class="text-center">
                @if ($yeuCau->trangThai == 'Chờ tiếp nhận')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                    Chờ tiếp nhận
                  </span>
                @elseif ($yeuCau->trangThai == 'Đã tiếp nhận')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary d-inline-block" style="width: 8px; height: 8px;"></span>
                    Đã tiếp nhận
                  </span>
                @elseif ($yeuCau->trangThai == 'Đang hỗ trợ')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-info d-inline-block" style="width: 8px; height: 8px;"></span>
                    Đang hỗ trợ
                  </span>
                @elseif ($yeuCau->trangThai == 'Hoàn thành')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    Hoàn thành
                  </span>
                @else
                  {{ $yeuCau->trangThai }}
                @endif
              </td>

              <td class="text-center">
                {{ $yeuCau->thoiGianGui ?? '-' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                Bạn chưa gửi yêu cầu cứu trợ nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
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