@extends('layouts.admin')

@section('title', 'Sửa thiên tai | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Sửa thiên tai</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/thien-tai') }}">Thiên tai</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Sửa</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>Thông tin thiên tai</h5>
  </div>

  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ url('/admin/thien-tai/' . $thienTai->idThienTai) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Tên thiên tai <span class="text-danger">*</span></label>
        <input type="text" name="tenThienTai" class="form-control"
          value="{{ old('tenThienTai', $thienTai->tenThienTai) }}">
      </div>

      <div class="mb-3">
        <label class="form-label">Năm xảy ra</label>
        <input type="number" name="namXayRa" class="form-control"
          value="{{ old('namXayRa', $thienTai->namXayRa) }}">
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ url('/admin/thien-tai') }}" class="btn btn-secondary">Quay lại danh sách</a>
      </div>
    </form>
  </div>
</div>
@endsection