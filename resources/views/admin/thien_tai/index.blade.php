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
    <div>
      <h5 class="mb-0">Danh sách thiên tai</h5>
    </div>

    <a href="{{ url('/admin/thien-tai/create') }}" class="btn btn-primary">
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
          <tr class="text-uppercase">
            <th class="text-center" style="width: 90px;">Mã</th>
            <th>Tên thiên tai</th>
            <th class="text-center" style="width: 160px;">Năm xảy ra</th>
            <th class="text-center" style="width: 120px;"></th>
          </tr>
        </thead>

        <tbody>
          @forelse ($thienTais as $thienTai)
            <tr>
              <td class="text-center">{{ $thienTai->idThienTai }}</td>

              <td class="fw-medium">
                {{ $thienTai->tenThienTai }}
              </td>

              <td class="text-center">
                {{ $thienTai->namXayRa ?? '-' }}
              </td>

              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  <a href="{{ url('/admin/thien-tai/' . $thienTai->idThienTai . '/edit') }}"
                     class="btn btn-sm btn-light border"
                     title="Sửa">
                    <i class="ti ti-edit"></i>
                  </a>

                  <form action="{{ url('/admin/thien-tai/' . $thienTai->idThienTai) }}" method="POST"
                    onsubmit="return confirm('Bạn có chắc muốn xóa thiên tai này không?')">
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
              <td colspan="4" class="text-center text-muted py-4">
                Chưa có thiên tai nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection