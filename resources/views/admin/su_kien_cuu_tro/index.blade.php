@extends('layouts.admin')

@section('title', 'Quản lý sự kiện cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý sự kiện cứu trợ</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Sự kiện cứu trợ</li>
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
      trong nhóm sự kiện
      <strong>{{ $loaiDangChon }}</strong>.
    </div>

    <a href="{{ url('/admin/su-kien-cuu-tro?loai=' . urlencode($loaiDangChon)) }}" class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <h5 class="mb-1">Danh sách sự kiện cứu trợ</h5>
      </div>

      <a href="{{ url('/admin/su-kien-cuu-tro/create') }}" class="btn btn-primary">
        Thêm
      </a>
    </div>

    <ul class="nav nav-tabs mt-3">
      <li class="nav-item">
        <a class="nav-link {{ $loaiDangChon === 'Khẩn cấp' ? 'active' : '' }}"
          href="{{ url('/admin/su-kien-cuu-tro?loai=Khẩn cấp' . (request('tuKhoa') ? '&tuKhoa=' . urlencode(request('tuKhoa')) : '')) }}">
          Khẩn cấp
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ $loaiDangChon === 'Thường nhật' ? 'active' : '' }}"
          href="{{ url('/admin/su-kien-cuu-tro?loai=Thường nhật' . (request('tuKhoa') ? '&tuKhoa=' . urlencode(request('tuKhoa')) : '')) }}">
          Thường nhật
        </a>
      </li>
    </ul>
  </div>

  <div class="card-body">
    <div class="mb-3 d-flex justify-content-left align-items-center flex-wrap gap-2">
      <small class="text-muted">
        Tổng hiển thị: {{ $suKiens->count() }}
      </small>
    </div>

    <div class="table-responsive">
      <table class="table table-hover mb-0 su-kien-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 70px;">Mã</th>
            <th class="text-start" style="width: 52%;">Tên sự kiện</th>
            <th style="width: 150px;">Trạng thái</th>
            <th style="width: 160px;">Ngày tạo</th>
            <th style="width: 110px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($suKiens as $suKien)
            <tr class="su-kien-row {{ session('suKienMoi') == $suKien->idSuKien ? 'table-primary' : '' }}"
                data-id="{{ $suKien->idSuKien }}">
              <td class="text-center fw-semibold">
                {{ $suKien->idSuKien }}
              </td>

              <td class="ten-su-kien-cell">
                <div class="ten-su-kien">
                  {{ $suKien->tenSuKien }}
                </div>

                @if ($suKien->moTa)
                  <div class="text-muted mo-ta-su-kien">
                    {{ $suKien->moTa }}
                  </div>
                @else
                  <div class="text-muted mo-ta-su-kien">
                    Chưa có mô tả
                  </div>
                @endif
              </td>

              <td class="text-center cot-trang-thai">
                @if ($suKien->trangThai == 'Đang diễn ra')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Đang diễn ra</span>
                  </span>
                @elseif ($suKien->trangThai == 'Sắp diễn ra')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Sắp diễn ra</span>
                  </span>
                @elseif ($suKien->trangThai == 'Đã kết thúc')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Đã kết thúc</span>
                  </span>
                @elseif ($suKien->trangThai == 'Ẩn' || $suKien->trangThai == 'Đã ẩn')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-dark d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Ẩn</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-secondary d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>{{ $suKien->trangThai }}</span>
                  </span>
                @endif
              </td>

              <td class="text-center cot-ngay-tao">
                {{ $suKien->ngayTao ?? '-' }}
              </td>

              <td class="text-center cot-thao-tac">
                <div class="d-inline-flex gap-1">
                  <a href="{{ url('/admin/su-kien-cuu-tro/' . $suKien->idSuKien . '/edit') }}"
                     class="btn btn-sm btn-light border"
                     title="Sửa"
                     onclick="event.stopPropagation();">
                    <i class="ti ti-edit"></i>
                  </a>

                  <form action="{{ url('/admin/su-kien-cuu-tro/' . $suKien->idSuKien) }}"
                        method="POST"
                        onclick="event.stopPropagation();"
                        onsubmit="return confirm('Bạn có chắc muốn xóa sự kiện này không? Nếu sự kiện đã được dùng trong chiến dịch, hệ thống sẽ không cho phép xóa.')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Xóa">
                      <i class="ti ti-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy sự kiện phù hợp.
                @else
                  Chưa có sự kiện cứu trợ nào trong nhóm {{ $loaiDangChon }}.
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
  .su-kien-table {
    table-layout: fixed;
    width: 100%;
  }

  .su-kien-table th,
  .su-kien-table td {
    vertical-align: middle;
  }

  .su-kien-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .su-kien-row:hover {
    background-color: #f5f7fb;
  }

  .su-kien-row.expanded {
    background-color: #f3f4f6;
  }

  .ten-su-kien-cell {
    min-width: 0;
  }

  .ten-su-kien {
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .mo-ta-su-kien {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    display: block;
    font-size: 13px;
    line-height: 1.5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .su-kien-row.expanded .ten-su-kien,
  .su-kien-row.expanded .mo-ta-su-kien {
    white-space: normal;
    overflow: visible;
    text-overflow: unset;
  }

  .cot-trang-thai,
  .cot-ngay-tao,
  .cot-thao-tac {
    white-space: normal;
    word-break: break-word;
  }

  .cot-thao-tac .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .nav-tabs .nav-link {
    font-weight: 500;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.su-kien-row');

    rows.forEach(function (row) {
      row.addEventListener('click', function () {
        rows.forEach(function (item) {
          if (item !== row) {
            item.classList.remove('expanded');
            item.classList.remove('table-active');
          }
        });

        row.classList.toggle('expanded');
        row.classList.toggle('table-active');
      });
    });
  });
</script>
@endsection