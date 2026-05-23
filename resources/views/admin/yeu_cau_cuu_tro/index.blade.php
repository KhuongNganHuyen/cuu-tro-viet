@extends('layouts.admin')

@section('title', 'Quản lý yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý yêu cầu cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item" aria-current="page">Yêu cầu cứu trợ</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Danh sách yêu cầu cứu trợ</h5>
    <a href="{{ url('/admin/yeu-cau-cuu-tro/create') }}" class="btn btn-primary">
      Thêm yêu cầu
    </a>
  </div>

  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th>Mã</th>
            <th class="text-start">Người gửi</th>
            <th class="text-start">Loại yêu cầu</th>
            <th>Địa điểm</th>
            <th>Số hộ</th>
            <th>Mức độ</th>
            <th>Trạng thái</th>
            <th>Thời gian gửi</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($yeuCaus as $yeuCau)
            <tr onclick="window.location='{{ url('/admin/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}'" style="cursor: pointer;">
              <td class="text-center">{{ $yeuCau->idYeuCau }}</td>

              <td>{{ $yeuCau->nguoiGui->hoTen ?? '-' }}</td>

              <td>{{ $yeuCau->loaiYeuCau }}</td>

              <td class="text-center">
                {{ $yeuCau->diaDiem->tinhThanh ?? '-' }}
                @if (!empty($yeuCau->diaDiem?->phuongXa))
                  - {{ $yeuCau->diaDiem->phuongXa }}
                @endif
              </td>

              <td class="text-center">{{ $yeuCau->soHoDan ?? '-' }}</td>

              <td class="text-center">{{ $yeuCau->mucDoKhanCap ?? '-' }}</td>

              <td class="text-center">
                @if ($yeuCau->trangThai == 'Chờ tiếp nhận')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                    Chờ tiếp nhận
                  </span>
                @elseif ($yeuCau->trangThai == 'Hoàn thành')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    Hoàn thành
                  </span>
                @elseif ($yeuCau->trangThai == 'Từ chối')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                    Từ chối
                  </span>
                @else
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary d-inline-block" style="width: 8px; height: 8px;"></span>
                    {{ $yeuCau->trangThai }}
                  </span>
                @endif
              </td>

              <td class="text-center">{{ $yeuCau->thoiGianGui ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-muted py-4">
                Chưa có yêu cầu cứu trợ nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection