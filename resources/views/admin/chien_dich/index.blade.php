@extends('layouts.admin')

@section('title', 'Quản lý chiến dịch cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý chiến dịch cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item" aria-current="page">Chiến dịch cứu trợ</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Danh sách chiến dịch</h5>
    <a href="{{ url('/admin/chien-dich/create') }}" class="btn btn-primary">Thêm chiến dịch</a>
  </div>

  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th>Mã</th>
            <th>Tên chiến dịch</th>
            <th>Nhóm thực hiện</th>
            <th>Thiên tai</th>
            <th>Địa điểm</th>
            <th>Thời gian</th>
            <th>UBND</th>
            <th>Trạng thái</th>
            <th style="width: 160px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($chienDichs as $chienDich)
            <tr>
              <td>{{ $chienDich->idChienDich }}</td>
              <td>{{ $chienDich->tenChienDich }}</td>
              <td>{{ $chienDich->nhom->tenNhom ?? '-' }}</td>
              <td>
                {{ $chienDich->thienTai->tenThienTai ?? '-' }}
                @if (!empty($chienDich->thienTai?->namXayRa))
                  - {{ $chienDich->thienTai->namXayRa }}
                @endif
              </td>
              <td>
                {{ $chienDich->diaDiem->tinhThanh ?? '-' }}
                @if (!empty($chienDich->diaDiem?->phuongXa))
                  - {{ $chienDich->diaDiem->phuongXa }}
                @endif
              </td>
              <td>
                {{ $chienDich->ngayBatDau ?? '-' }}
                @if ($chienDich->ngayKetThuc)
                  → {{ $chienDich->ngayKetThuc }}
                @endif
              </td>
              <td>
                @if ($chienDich->daThongBaoUBND)
                  <span class="badge bg-success">Đã thông báo</span>
                @else
                  <span class="badge bg-secondary">Chưa thông báo</span>
                @endif
              </td>
              <td>{{ $chienDich->trangThai }}</td>
              <td>
                <a href="{{ url('/admin/chien-dich/' . $chienDich->idChienDich . '/edit') }}" class="btn btn-sm btn-warning">
                  Sửa
                </a>

                <form action="{{ url('/admin/chien-dich/' . $chienDich->idChienDich) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Bạn có chắc muốn xóa chiến dịch này không?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center">Chưa có chiến dịch nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection