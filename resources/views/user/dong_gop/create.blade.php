@extends('layouts.user')

@section('title', 'Đăng ký đóng góp | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Đăng ký đóng góp</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/user/dong-gop') }}">Đóng góp của tôi</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Đăng ký đóng góp</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ url('/user/dong-gop') }}" method="POST">
  @csrf

  <div class="card mb-3">
    <div class="card-header">
      <h5 class="mb-0">Thông tin đóng góp</h5>
    </div>

    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Chiến dịch <span class="text-danger">*</span></label>
        <select name="idChienDich" class="form-control">
          <option value="">-- Chọn chiến dịch --</option>

          @foreach ($chienDichs as $chienDich)
            <option value="{{ $chienDich->idChienDich }}"
              {{ old('idChienDich') == $chienDich->idChienDich ? 'selected' : '' }}>
              {{ $chienDich->tenChienDich }}
              - {{ $chienDich->nhom->tenNhom ?? 'Nhóm chưa rõ' }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Ghi chú</label>
        <textarea name="ghiChu" class="form-control" rows="3"
          placeholder="Ví dụ: Tôi có thể giao hàng vào buổi chiều, vui lòng liên hệ trước.">{{ old('ghiChu') }}</textarea>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-0">Hàng hóa đóng góp</h5>
        <small class="text-muted">
          Có thể thêm nhiều loại hàng hóa trong cùng một lượt đóng góp.
        </small>
      </div>

      <button type="button" id="btnThemHang" class="btn btn-sm btn-primary">
        Thêm hàng
      </button>
    </div>

    <div class="card-body">
      <div id="hangHoaContainer">
        <div class="row hang-hoa-item mb-3">
          <div class="col-md-5">
            <label class="form-label">Hàng hóa <span class="text-danger">*</span></label>
            <select name="hangHoas[0][idHangHoa]" class="form-control">
              <option value="">-- Chọn hàng hóa --</option>

              @foreach ($hangHoas as $hangHoa)
                <option value="{{ $hangHoa->idHangHoa }}">
                  {{ $hangHoa->tenHangHoa }}
                  @if ($hangHoa->donViTinh)
                    ({{ $hangHoa->donViTinh }})
                  @endif
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="1" name="hangHoas[0][soLuong]" class="form-control"
              placeholder="Ví dụ: 10">
          </div>

          <div class="col-md-3">
            <label class="form-label">Hạn sử dụng</label>
            <input type="date" name="hangHoas[0][hanSuDung]" class="form-control">
          </div>

          <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-light border text-danger btn-xoa-hang" title="Xóa">
              <i class="ti ti-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <small class="text-muted">
        Sau khi gửi, nhóm tình nguyện sẽ kiểm tra và xác nhận. Chỉ những đóng góp được xác nhận mới được cộng vào nguồn lực chiến dịch.
      </small>
    </div>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
      Gửi đăng ký
    </button>

    <a href="{{ url('/user/dong-gop') }}" class="btn btn-secondary">
      Quay lại
    </a>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    let index = 1;

    const btnThemHang = document.getElementById('btnThemHang');
    const container = document.getElementById('hangHoaContainer');

    btnThemHang.addEventListener('click', function () {
      const firstItem = container.querySelector('.hang-hoa-item');
      const newItem = firstItem.cloneNode(true);

      newItem.querySelectorAll('select, input').forEach(function (input) {
        const name = input.getAttribute('name');

        if (name) {
          input.setAttribute('name', name.replace(/\[\d+\]/, '[' + index + ']'));
        }

        input.value = '';
      });

      container.appendChild(newItem);
      index++;
    });

    container.addEventListener('click', function (e) {
      const btn = e.target.closest('.btn-xoa-hang');

      if (!btn) {
        return;
      }

      const items = container.querySelectorAll('.hang-hoa-item');

      if (items.length <= 1) {
        alert('Cần có ít nhất một loại hàng hóa đóng góp.');
        return;
      }

      btn.closest('.hang-hoa-item').remove();
    });
  });
</script>
@endsection