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

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Danh sách sự kiện cứu trợ</h5>
      <small class="text-muted">
        Quản lý các sự kiện/chủ đề cứu trợ như thiên tai, hộ nghèo, trẻ em khó khăn, bệnh nhân khó khăn...
      </small>
    </div>

    <a href="{{ url('/admin/su-kien-cuu-tro/create') }}" class="btn btn-primary">
      Thêm
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 su-kien-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 70px;">Mã</th>
            <th class="text-start" style="width: 42%;">Tên sự kiện</th>
            <th style="width: 120px;">Loại</th>
            <th style="width: 130px;">Trạng thái</th>
            <th style="width: 150px;">Ngày tạo</th>
            <th style="width: 110px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($suKiens as $suKien)
            <tr class="su-kien-row" data-id="{{ $suKien->idSuKien }}">
              <td class="text-center">
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

              <td class="text-center cot-loai">
                {{ $suKien->loaiSuKien }}
              </td>

              <td class="text-center cot-trang-thai">
                @if ($suKien->trangThai == 'Đang diễn ra')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Đang diễn ra</span>
                  </span>
                @elseif ($suKien->trangThai == 'Đã ẩn')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-secondary d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Đã ẩn</span>
                  </span>
                @elseif ($suKien->trangThai == 'Đã kết thúc')
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-dark d-inline-block" style="width: 8px; height: 8px;"></span>
                    <span>Đã kết thúc</span>
                  </span>
                @else
                  <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
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
                        onsubmit="return confirm('Bạn có chắc muốn xóa sự kiện này không?')">
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
              <td colspan="6" class="text-center text-muted py-4">
                Chưa có sự kiện cứu trợ nào.
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

  .cot-loai,
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