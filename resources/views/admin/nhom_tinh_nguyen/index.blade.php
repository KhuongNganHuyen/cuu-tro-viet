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

    <a href="{{ url('/admin/nhom-tinh-nguyen') }}" class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-1">Danh sách nhóm tình nguyện</h5>
      <small class="text-muted">
        Tổng hiển thị: {{ $nhomTinhNguyens->count() }}
      </small>
    </div>

    <a href="{{ url('/admin/nhom-tinh-nguyen/create') }}" class="btn btn-primary">
      Thêm
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 nhom-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 80px;">Mã</th>
            <th class="text-start">Tên nhóm</th>
            <th class="text-start">Nhóm trưởng</th>
            <th class="text-start">Địa điểm</th>
            <th style="width: 160px;">Trạng thái</th>
            <th style="width: 160px;">Ngày tạo</th>
            <th style="width: 90px;"></th>
          </tr>
        </thead>

        <tbody>
          @forelse ($nhomTinhNguyens as $nhom)
            <tr class="nhom-row {{ session('nhomMoi') == $nhom->idNhom ? 'table-primary' : '' }}"
                onclick="window.location='{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom) }}'">
              <td class="text-center fw-semibold">
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
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle status-dot status-active d-inline-block"></span>
                    <span>Đang hoạt động</span>
                  </span>
                @elseif ($nhom->trangThai == 'Chờ duyệt')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle status-dot status-pending d-inline-block"></span>
                    <span>Chờ duyệt</span>
                  </span>
                @elseif ($nhom->trangThai == 'Tạm ngừng hoạt động')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle status-dot status-paused d-inline-block"></span>
                    <span>Tạm ngừng hoạt động</span>
                  </span>
                @elseif ($nhom->trangThai == 'Ngừng hoạt động')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle status-dot status-stopped d-inline-block"></span>
                    <span>Ngừng hoạt động</span>
                  </span>
                @elseif ($nhom->trangThai == 'Bị khóa')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle status-dot status-locked d-inline-block"></span>
                    <span>Bị khóa</span>
                  </span>
                @elseif ($nhom->trangThai == 'Từ chối')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle status-dot status-rejected d-inline-block"></span>
                    <span>Từ chối</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle status-dot status-stopped d-inline-block"></span>
                    <span>{{ $nhom->trangThai }}</span>
                  </span>
                @endif
              </td>

              <td class="text-center">
                {{ $nhom->ngayTao ?? '-' }}
              </td>

              <td class="text-center" onclick="event.stopPropagation();">
                @if ($nhom->trangThai == 'Chờ duyệt')
                  <div class="d-inline-flex gap-1">
                    <form action="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom . '/duyet') }}"
                          method="POST"
                          onsubmit="return confirm('Bạn có chắc muốn duyệt nhóm này không?')">
                      @csrf
                      @method('PATCH')

                      <button type="submit"
                              class="btn btn-sm btn-outline-success"
                              title="Duyệt">
                        <i class="ti ti-check"></i>
                      </button>
                    </form>

                    <form action="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom . '/tu-choi') }}"
                          method="POST"
                          onsubmit="return confirm('Bạn có chắc muốn từ chối nhóm này không?')">
                      @csrf
                      @method('PATCH')

                      <button type="submit"
                              class="btn btn-sm btn-outline-danger"
                              title="Từ chối">
                        <i class="ti ti-x"></i>
                      </button>
                    </form>
                  </div>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy nhóm tình nguyện phù hợp.
                @else
                  Chưa có nhóm tình nguyện nào.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .nhom-table th,
  .nhom-table td {
    vertical-align: middle;
  }

  .nhom-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .nhom-row:hover {
    background-color: #f5f7fb;
  }

  .status-dot {
    width: 8px;
    height: 8px;
  }

  .status-active {
    background-color: #198754;
  }

  .status-pending {
    background-color: #ffc107;
  }

  .status-paused {
    background-color: #fd7e14;
  }

  .status-stopped {
    background-color: #6c757d;
  }

  .status-locked {
    background-color: #212529;
  }

  .status-rejected {
    background-color: #dc3545;
  }
</style>
@endsection