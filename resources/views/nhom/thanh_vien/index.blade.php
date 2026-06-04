@extends('layouts.nhom')

@section('title', 'Thành viên nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thành viên nhóm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thành viên</li>
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
      <h5 class="mb-0">Danh sách thành viên</h5>
    </div>

    @if ($laNhomTruong)
      <a href="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien/create') }}" class="btn btn-primary">
        Thêm thành viên
      </a>
    @endif
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã TV</th>
            <th class="text-start">Họ tên</th>
            <th class="text-start">Tên đăng nhập</th>
            <th class="text-start">Email</th>
            <th style="width: 150px;">SĐT</th>
            <th style="width: 170px;">Vai trò nhóm</th>
            <th style="width: 170px;">Ngày tham gia</th>
            @if ($laNhomTruong)
              <th style="width: 90px;"> </th>
            @endif
          </tr>
        </thead>

        <tbody>
          @forelse ($thanhViens as $thanhVien)
            <tr>
              <td class="text-center">{{ $thanhVien->idThanhVien }}</td>

              <td>
                <div class="fw-semibold">
                  {{ $thanhVien->nguoiDung->hoTen ?? '-' }}
                </div>
              </td>

              <td>{{ $thanhVien->nguoiDung->tenDangNhap ?? '-' }}</td>
              <td>{{ $thanhVien->nguoiDung->email ?? '-' }}</td>
              <td class="text-center">{{ $thanhVien->nguoiDung->sdt ?? '-' }}</td>

              <td class="text-center">
                {{ $thanhVien->vaiTro ?? 'Thành viên' }}
              </td>

              <td class="text-center">
                {{ $thanhVien->ngayThamGia ?? '-' }}
              </td>

              @if ($laNhomTruong)
                <td class="text-center">
                  @if ($thanhVien->vaiTro != 'Nhóm trưởng')
                    <form action="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien/' . $thanhVien->idThanhVien) }}"
                      method="POST"
                      onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này khỏi nhóm không?')">
                      @csrf
                      @method('DELETE')

                      <button type="submit" class="btn btn-sm btn-light border text-danger" title="Xóa">
                        <i class="ti ti-trash"></i>
                      </button>
                    </form>
                  @else
                    -
                  @endif
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ $laNhomTruong ? 8 : 7 }}" class="text-center text-muted py-4">
                Nhóm chưa có thành viên nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection