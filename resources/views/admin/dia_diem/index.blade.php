@extends('layouts.admin')

@section('title', 'Quản lý địa điểm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý địa điểm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Địa điểm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Danh sách địa điểm</h5>
    <a href="{{ url('/admin/dia-diem/create') }}" class="btn btn-primary">
      Thêm địa điểm
    </a>
  </div>

  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th style="width: 80px;">Mã</th>
            <th>Tỉnh/Thành</th>
            <th>Phường/Xã</th>
            <th>Chi tiết địa điểm</th>
            <th>Vĩ độ</th>
            <th>Kinh độ</th>
            <th style="width: 160px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($diaDiems as $diaDiem)
            <tr>
              <td>{{ $diaDiem->idDiaDiem }}</td>
              <td>{{ $diaDiem->tinhThanh }}</td>
              <td>{{ $diaDiem->phuongXa ?? '-' }}</td>
              <td>{{ $diaDiem->chiTietDiaDiem ?? '-' }}</td>
              <td>{{ $diaDiem->viDo ?? '-' }}</td>
              <td>{{ $diaDiem->kinhDo ?? '-' }}</td>
              <td>
                <a href="{{ url('/admin/dia-diem/' . $diaDiem->idDiaDiem . '/edit') }}" class="btn btn-sm btn-warning">
                  Sửa
                </a>

                <form action="{{ url('/admin/dia-diem/' . $diaDiem->idDiaDiem) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Bạn có chắc muốn xóa địa điểm này không?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">
                    Xóa
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">Chưa có địa điểm nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection