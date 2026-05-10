@extends('layouts.admin')

@section('title', 'Quản lý thiên tai | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Quản lý thiên tai</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thiên tai</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Danh sách thiên tai</h5>
    <a href="{{ url('/admin/thien-tai/create') }}" class="btn btn-primary">
      Thêm thiên tai
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
            <th style="width: 100px;">Mã</th>
            <th>Tên thiên tai</th>
            <th style="width: 150px;">Năm xảy ra</th>
            <th style="width: 160px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($thienTais as $thienTai)
            <tr>
              <td>{{ $thienTai->idThienTai }}</td>
              <td>{{ $thienTai->tenThienTai }}</td>
              <td>{{ $thienTai->namXayRa ?? '-' }}</td>
              <td>
                <a href="{{ url('/admin/thien-tai/' . $thienTai->idThienTai . '/edit') }}" class="btn btn-sm btn-warning">
                  Sửa
                </a>

                <form action="{{ url('/admin/thien-tai/' . $thienTai->idThienTai) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Bạn có chắc muốn xóa thiên tai này không?')">
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
              <td colspan="4" class="text-center">Chưa có thiên tai nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection