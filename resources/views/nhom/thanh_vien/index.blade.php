@extends('layouts.nhom')

@section('title', 'Thành viên nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">
            Thành viên nhóm
          </h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">
              Nhóm của tôi
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              {{ $nhom->tenNhom }}
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Thành viên
          </li>
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

    <a href="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien') }}"
       class="btn btn-sm btn-light">
      Xóa tìm kiếm
    </a>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">
        Danh sách thành viên
      </h5>

      <small class="text-muted">
        Tổng hiển thị: {{ $thanhViens->count() }}
      </small>
    </div>

    @if ($laNhomTruong)
      <a href="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien/create') }}"
         class="btn btn-primary">
        Thêm thành viên
      </a>
    @endif
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0 thanh-vien-table">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 50px;"></th>
            <th style="width: 80px;">Mã</th>
            <th class="text-start">Họ tên</th>
            <th class="text-start">Tên đăng nhập</th>
            <th class="text-start">Email</th>
            <th style="width: 145px;">SĐT</th>
            <th style="width: 160px;">Vai trò</th>
            <th style="width: 170px;">Ngày tham gia</th>

            @if ($laNhomTruong)
              <th style="width: 50px;"></th>
            @endif
          </tr>
        </thead>

        <tbody>
          @forelse ($thanhViens as $thanhVien)
            @php
              $nguoiDung = $thanhVien->nguoiDung;

              $duongDanAvatar = !empty($nguoiDung->anhDaiDien)
                  ? asset('storage/' . $nguoiDung->anhDaiDien)
                  : asset('mantis/assets/images/user/avatar-2.jpg');
            @endphp

            <tr class="{{ session('thanhVienMoi') == $thanhVien->idThanhVien ? 'table-primary' : '' }}">
              <td class="text-center">
                <img src="{{ $duongDanAvatar }}"
                     alt="{{ $nguoiDung->hoTen ?? 'Thành viên' }}"
                     class="member-avatar">
              </td>

              <td class="text-center fw-semibold">
                {{ $thanhVien->idThanhVien }}
              </td>

              <td>
                <div class="fw-semibold">
                  {{ $nguoiDung->hoTen ?? '-' }}
                </div>
              </td>

              <td>
                {{ $nguoiDung->tenDangNhap ?? '-' }}
              </td>

              <td>
                {{ $nguoiDung->email ?? '-' }}
              </td>

              <td class="text-center">
                {{ $nguoiDung->sdt ?? '-' }}
              </td>

              <td class="text-center">
                {{ $thanhVien->vaiTro ?? 'Thành viên' }}
              </td>

              <td class="text-center">
                @if ($thanhVien->ngayThamGia)
                  {{ \Carbon\Carbon::parse($thanhVien->ngayThamGia)->format('d/m/Y H:i') }}
                @else
                  -
                @endif
              </td>

              @if ($laNhomTruong)
                <td class="text-center">
                  @if ($thanhVien->vaiTro !== 'Nhóm trưởng')
                    <form action="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien/' . $thanhVien->idThanhVien) }}"
                          method="POST"
                          onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này khỏi nhóm không?')">
                      @csrf
                      @method('DELETE')

                      <button type="submit"
                              class="btn btn-sm btn-light border text-danger action-button"
                              title="Xóa">
                        <i class="ti ti-trash"></i>
                      </button>
                    </form>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ $laNhomTruong ? 9 : 8 }}"
                  class="text-center text-muted py-4">
                @if (request('tuKhoa'))
                  Không tìm thấy thành viên phù hợp.
                @else
                  Nhóm chưa có thành viên nào.
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
  .thanh-vien-table th,
  .thanh-vien-table td {
    vertical-align: middle;
  }

  .member-avatar {
    width: 32px;
    height: 32px;
    object-fit: cover;
    border-radius: 50%;
    border: 1px solid #dee2e6;
  }

  .action-button {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
</style>
@endsection