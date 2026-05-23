@extends('layouts.admin')

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
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admin/yeu-cau-cuu-tro') }}">Yêu cầu cứu trợ</a></li>
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

<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Thông tin yêu cầu</h5>

    <div class="d-flex gap-2">
      <a href="{{ url('/admin/yeu-cau-cuu-tro') }}" class="btn btn-secondary">
        Quay lại
      </a>

      <a href="{{ url('/admin/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/edit') }}" class="btn btn-warning">
        Sửa
      </a>

      <a href="{{ url('/admin/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan') }}" class="btn btn-primary">
        Tiếp nhận yêu cầu
      </a>
    </div>
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-3 text-muted">Mã yêu cầu</div>
      <div class="col-md-9">{{ $yeuCau->idYeuCau }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Người gửi</div>
      <div class="col-md-9">{{ $yeuCau->nguoiGui->hoTen ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Địa điểm</div>
      <div class="col-md-9">
        {{ $yeuCau->diaDiem->tinhThanh ?? '-' }}
        @if (!empty($yeuCau->diaDiem?->phuongXa))
          - {{ $yeuCau->diaDiem->phuongXa }}
        @endif
        @if (!empty($yeuCau->diaDiem?->chiTietDiaDiem))
          - {{ $yeuCau->diaDiem->chiTietDiaDiem }}
        @endif
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Loại yêu cầu</div>
      <div class="col-md-9">{{ $yeuCau->loaiYeuCau }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Mô tả</div>
      <div class="col-md-9">{{ $yeuCau->moTa }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Số hộ dân</div>
      <div class="col-md-9">{{ $yeuCau->soHoDan ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Mức độ khẩn cấp</div>
      <div class="col-md-9">{{ $yeuCau->mucDoKhanCap ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Trạng thái</div>
      <div class="col-md-9">
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
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3 text-muted">Thời gian gửi</div>
      <div class="col-md-9">{{ $yeuCau->thoiGianGui ?? '-' }}</div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Thông tin tiếp nhận</h5>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th>Mã</th>
            <th class="text-start">Chiến dịch</th>
            <th class="text-start">Nhóm tiếp nhận</th>
            <th>Thời gian tiếp nhận</th>
            <th>Dự kiến hỗ trợ</th>
            <th>Trạng thái</th>
            <th>Nội dung đảm nhận</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($yeuCau->tiepNhans as $tiepNhan)
            <tr>
              <td class="text-center">{{ $tiepNhan->idTiepNhan }}</td>
              <td>{{ $tiepNhan->chienDich->tenChienDich ?? '-' }}</td>
              <td>{{ $tiepNhan->nhom->tenNhom ?? '-' }}</td>
              <td class="text-center">{{ $tiepNhan->thoiGianTiepNhan ?? '-' }}</td>
              <td class="text-center">{{ $tiepNhan->thoiGianDuKienHoTro ?? '-' }}</td>
              <td class="text-center">{{ $tiepNhan->trangThai }}</td>
              <td>{{ $tiepNhan->noiDungDamNhan ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                Yêu cầu này chưa được nhóm nào tiếp nhận.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection