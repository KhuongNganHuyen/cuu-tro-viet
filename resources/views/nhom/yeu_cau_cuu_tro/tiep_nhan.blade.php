@extends('layouts.nhom')

@section('title', 'Tiếp nhận yêu cầu cứu trợ | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tiếp nhận yêu cầu cứu trợ</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">
              Tổng quan nhóm
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro') }}">
              Yêu cầu cứu trợ
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}">
              Chi tiết
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Tiếp nhận
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <h5 class="mb-1">Thông tin tiếp nhận</h5>
      </div>
    </div>
  </div>

  <div class="card-body">
    @if ($chienDichs->count() == 0)
      <div class="alert alert-warning">
        Nhóm chưa có chiến dịch nào có thể tiếp nhận yêu cầu. Bạn cần tạo chiến dịch trước khi tiếp nhận.
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/create') }}"
           class="btn btn-primary">
          Tạo chiến dịch
        </a>

        <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}"
           class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    @else
      <form action="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau . '/tiep-nhan') }}"
            method="POST"
            onsubmit="return confirm('Xác nhận tiếp nhận yêu cầu cứu trợ này?')">
        @csrf

        <div class="row">
          <div class="col-lg-6 mb-3">
            <label class="form-label">
              Chiến dịch tiếp nhận <span class="text-danger">*</span>
            </label>

            <select name="idChienDich"
                    class="form-select"
                    required>
              <option value="">-- Chọn chiến dịch --</option>

              @foreach ($chienDichs as $chienDich)
                <option value="{{ $chienDich->idChienDich }}"
                  {{ old('idChienDich') == $chienDich->idChienDich ? 'selected' : '' }}>
                  {{ $chienDich->tenChienDich }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-lg-6 mb-3">
            <label class="form-label">
              Ngày dự kiến hỗ trợ
            </label>

            <input type="date"
                   name="thoiGianDuKienHoTro"
                   class="form-control"
                   min="{{ now()->format('Y-m-d') }}"
                   value="{{ old('thoiGianDuKienHoTro') }}">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">
            Nội dung đảm nhận <span class="text-danger">*</span>
          </label>

          <textarea name="noiDungDamNhan"
                    class="form-control"
                    rows="6"
                    required
                    placeholder="Ghi rõ phần việc hoặc nguồn lực nhóm sẽ đảm nhận.">{{ old('noiDungDamNhan') }}</textarea>
        </div>

        <div class="mb-4">
          <label class="form-label">
            Trạng thái sau khi tiếp nhận
          </label>

          <div class="form-control bg-light">
            Đã tiếp nhận
          </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
          <button type="submit"
                  class="btn btn-primary">
            Lưu tiếp nhận
          </button>

          <a href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}"
             class="btn btn-secondary">
            Quay lại
          </a>
        </div>
      </form>
    @endif
  </div>
</div>
@endsection