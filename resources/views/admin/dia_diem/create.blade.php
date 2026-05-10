@extends('layouts.admin')

@section('title', 'Thêm địa điểm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm địa điểm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dia-diem') }}">Địa điểm</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>Thông tin địa điểm</h5>
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

    <form action="{{ url('/admin/dia-diem') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Tỉnh/Thành <span class="text-danger">*</span></label>
        <input type="text" name="tinhThanh" class="form-control" value="{{ old('tinhThanh') }}"
          placeholder="Ví dụ: Đà Nẵng">
      </div>

      <div class="mb-3">
        <label class="form-label">Phường/Xã</label>
        <input type="text" name="phuongXa" class="form-control" value="{{ old('phuongXa') }}"
          placeholder="Ví dụ: Hòa Khánh Bắc">
      </div>

      <div class="mb-3">
        <label class="form-label">Chi tiết địa điểm</label>
        <input type="text" name="chiTietDiaDiem" class="form-control" value="{{ old('chiTietDiaDiem') }}"
          placeholder="Ví dụ: Quận Liên Chiểu">
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Vĩ độ</label>
          <input type="text" name="viDo" class="form-control" value="{{ old('viDo') }}"
            placeholder="Ví dụ: 16.047079">
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Kinh độ</label>
          <input type="text" name="kinhDo" class="form-control" value="{{ old('kinhDo') }}"
            placeholder="Ví dụ: 108.206230">
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ url('/admin/dia-diem') }}" class="btn btn-secondary">Quay lại</a>
      </div>
    </form>
  </div>
</div>
@endsection