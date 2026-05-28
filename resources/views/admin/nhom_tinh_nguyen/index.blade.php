@extends('layouts.admin')

@section('title', 'Quản lý nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý nhóm tình nguyện</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Nhóm tình nguyện</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Danh sách nhóm tình nguyện</h5>
      <small class="text-muted">
        Theo dõi các nhóm tình nguyện, nhóm trưởng, khu vực hoạt động và trạng thái nhóm
      </small>
    </div>

    <a href="{{ url('/admin/nhom-tinh-nguyen/create') }}" class="btn btn-primary">
      Thêm
    </a>
  </div>

  <div class="card-body">
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

    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 80px;">Mã</th>
            <th class="text-start">Tên nhóm</th>
            <th class="text-start">Nhóm trưởng</th>
            <th class="text-start">Địa điểm</th>
            <th style="width: 160px;">Trạng thái</th>
            <th style="width: 160px;">Ngày tạo</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($nhomTinhNguyens as $nhom)
            <tr onclick="window.location='{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom) }}'"
                style="cursor: pointer;">
              <td class="text-center">
                {{ $nhom->idNhom }}
              </td>

              <td>
                <div class="fw-semibold">{{ $nhom->tenNhom }}</div>

                @if (!empty($nhom->moTa))
                  <small class="text-muted">
                    {{ \Illuminate\Support\Str::limit($nhom->moTa, 70) }}
                  </small>
                @endif
              </td>

              <td>
                {{ $nhom->nhomTruong->hoTen ?? '-' }}
              </td>

              <td>
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

              <td class="text-center">
                @if ($nhom->trangThai == 'Đang hoạt động' || $nhom->trangThai == 'Hoạt động')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    {{ $nhom->trangThai }}
                  </span>
                @else
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                    {{ $nhom->trangThai }}
                  </span>
                @endif
              </td>

              <td class="text-center">
                {{ $nhom->ngayTao ?? '-' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                Chưa có nhóm tình nguyện nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection