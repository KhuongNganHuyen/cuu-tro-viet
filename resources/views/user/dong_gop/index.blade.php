@extends('layouts.user')

@section('title', 'Đóng góp của tôi | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Đóng góp của tôi</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Đóng góp của tôi</li>
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
      <h5 class="mb-0">Lịch sử đóng góp</h5>
      <small class="text-muted">
        Theo dõi các lượt đóng góp bạn đã gửi cho các chiến dịch cứu trợ.
      </small>
    </div>

    <a href="{{ url('/user/dong-gop/create') }}" class="btn btn-primary">
      Đăng ký đóng góp
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Chiến dịch</th>
            <th class="text-start">Hàng hóa</th>
            <th style="width: 180px;">Thời gian</th>
            <th style="width: 170px;">Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($dongGops as $dongGop)
            @php
              $trangThais = $dongGop->chiTietDongGops->pluck('trangThai')->unique()->values();
              $trangThaiHienThi = $trangThais->count() == 1 ? $trangThais->first() : 'Nhiều trạng thái';
            @endphp

            <tr>
              <td class="text-center">{{ $dongGop->idDongGop }}</td>

              <td>
                <div class="fw-semibold">
                  {{ $dongGop->chienDich->tenChienDich ?? '-' }}
                </div>
                <small class="text-muted">
                  {{ $dongGop->chienDich->nhom->tenNhom ?? '-' }}
                </small>
              </td>

              <td>
                @foreach ($dongGop->chiTietDongGops as $chiTiet)
                  <div>
                    {{ $chiTiet->hangHoa->tenHangHoa ?? '-' }}:
                    {{ $chiTiet->soLuong }}
                    {{ $chiTiet->hangHoa->donViTinh ?? '' }}
                    @if ($chiTiet->hanSuDung)
                      <small class="text-muted">(HSD: {{ $chiTiet->hanSuDung }})</small>
                    @endif
                  </div>
                @endforeach
              </td>

              <td class="text-center">
                {{ $dongGop->thoiGianDongGop ?? '-' }}
              </td>

              <td class="text-center">
                @if ($trangThaiHienThi == 'Chờ xác nhận')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                    Chờ xác nhận
                  </span>
                @elseif ($trangThaiHienThi == 'Đã xác nhận')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    Đã xác nhận
                  </span>
                @elseif ($trangThaiHienThi == 'Từ chối')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                    Từ chối
                  </span>
                @else
                  {{ $trangThaiHienThi ?? '-' }}
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                Bạn chưa có lượt đóng góp nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection