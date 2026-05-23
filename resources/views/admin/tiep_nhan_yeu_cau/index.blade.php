@extends('layouts.admin')

@section('title', 'Tiếp nhận yêu cầu | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tiếp nhận yêu cầu cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item" aria-current="page">Tiếp nhận yêu cầu</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Danh sách yêu cầu đã tiếp nhận</h5>
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
            <th class="text-start">Yêu cầu</th>
            <th>Chiến dịch</th>
            <th>Nhóm tiếp nhận</th>
            <th>Thời gian tiếp nhận</th>
            <th>Dự kiến hỗ trợ</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($tiepNhans as $tiepNhan)
            <tr>
              <td class="text-center">{{ $tiepNhan->idTiepNhan }}</td>

              <td>{{ $tiepNhan->yeuCau->nguoiGui->hoTen ?? '-' }}</td>

              <td>{{ $tiepNhan->yeuCau->loaiYeuCau ?? '-' }}</td>

              <td class="text-center">{{ $tiepNhan->chienDich->tenChienDich ?? '-' }}</td>

              <td class="text-center">{{ $tiepNhan->nhom->tenNhom ?? '-' }}</td>

              <td class="text-center">{{ $tiepNhan->thoiGianTiepNhan ?? '-' }}</td>

              <td class="text-center">{{ $tiepNhan->thoiGianDuKienHoTro ?? '-' }}</td>

              <td class="text-center">
                @if ($tiepNhan->trangThai == 'Đã tiếp nhận')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary d-inline-block" style="width: 8px; height: 8px;"></span>
                    Đã tiếp nhận
                  </span>
                @elseif ($tiepNhan->trangThai == 'Đang xử lý')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                    Đang xử lý
                  </span>
                @elseif ($tiepNhan->trangThai == 'Hoàn thành')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    Hoàn thành
                  </span>
                @else
                  {{ $tiepNhan->trangThai }}
                @endif
              </td>

              <td class="text-center">
                <form action="{{ url('/admin/tiep-nhan-yeu-cau/' . $tiepNhan->idTiepNhan) }}" method="POST"
                  onsubmit="return confirm('Bạn có chắc muốn xóa thông tin tiếp nhận này không?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-muted py-4">
                Chưa có yêu cầu nào được tiếp nhận.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection