@extends('layouts.nhom')

@section('title', 'Thêm cập nhật tiến độ | Cứu Trợ Việt')

@section('content')
<style>
  .campaign-name-block {
    margin-bottom: 18px;
  }

  .campaign-name-label {
    color: #6c757d;
    font-size: 15px;
    margin-bottom: 4px;
  }

  .campaign-name-text {
    color: #212529;
    font-size: 17px;
    font-weight: 600;
    line-height: 1.5;
    margin-top: -2px;
    padding-left: 12px;
  }

  .update-textarea {
    min-height: 260px;
    resize: vertical;
    line-height: 1.7;
  }

  .image-placeholder {
    min-height: 190px;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 20px;
    color: #6c757d;
  }

  .image-placeholder-icon {
    font-size: 38px;
    margin-bottom: 8px;
    color: #495057;
  }

  .image-preview-wrapper {
    display: none;
  }

  .image-preview {
    width: 100%;
    max-height: 260px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #dee2e6;
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm cập nhật tiến độ</h5>
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

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}">
              Chiến dịch
            </a>
          </li>

          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}">
              {{ $chienDich->tenChienDich }}
            </a>
          </li>

          <li class="breadcrumb-item" aria-current="page">
            Thêm cập nhật
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

<form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/cap-nhat') }}"
      method="POST"
      enctype="multipart/form-data">
  @csrf

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Nội dung cập nhật</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              Chiến dịch
            </label>

            <div class="campaign-name-text">
              {{ $chienDich->tenChienDich }}
            </div>
          </div>

          <div class="mb-0">
            <label class="form-label">
              Nội dung cập nhật
              <span class="text-danger">*</span>
            </label>

            <textarea name="noiDung"
                      class="form-control update-textarea"
                      rows="10"
                      placeholder="Nhập nội dung cập nhật chiến dịch...">{{ old('noiDung') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Ảnh minh chứng</h5>
        </div>

        <div class="card-body">
          <div id="imagePlaceholder" class="image-placeholder mb-3">
            <div>
              <div class="image-placeholder-icon">
                <i class="ti ti-photo"></i>
              </div>

              <div class="fw-semibold">
                Chưa chọn hình ảnh
              </div>

              <small>
                Ảnh giúp minh chứng hoạt động cập nhật tiến độ chính xác hơn.
              </small>
            </div>
          </div>

          <div class="image-preview-wrapper mb-3" id="imagePreviewWrapper">
            <img src=""
                 alt="Ảnh minh chứng đã chọn"
                 class="image-preview"
                 id="imagePreview">
          </div>

          <div class="mb-2">
            <label class="form-label">
              Chọn ảnh
            </label>

            <input type="file"
                   name="hinhAnh"
                   id="hinhAnh"
                   class="form-control"
                   accept="image/*">
          </div>

          <small class="text-muted d-block mb-3">
            Chấp nhận JPG, JPEG, PNG hoặc WEBP. Dung lượng tối đa 2MB.
          </small>

          <div class="alert alert-info mb-0">
            Nếu không có ảnh minh chứng, bạn vẫn có thể lưu cập nhật.
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 mt-3">
    <button type="submit"
            class="btn btn-primary">
      Lưu
    </button>

    <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}"
       class="btn btn-secondary">
      Quay lại
    </a>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const hinhAnhInput = document.getElementById('hinhAnh');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const imagePreviewWrapper = document.getElementById('imagePreviewWrapper');
    const imagePreview = document.getElementById('imagePreview');

    if (hinhAnhInput) {
      hinhAnhInput.addEventListener('change', function () {
        const file = hinhAnhInput.files && hinhAnhInput.files[0];

        if (!file) {
          imagePlaceholder.style.display = 'flex';
          imagePreviewWrapper.style.display = 'none';
          imagePreview.src = '';
          return;
        }

        if (!file.type.startsWith('image/')) {
          alert('Vui lòng chọn đúng tệp hình ảnh.');
          hinhAnhInput.value = '';
          imagePlaceholder.style.display = 'flex';
          imagePreviewWrapper.style.display = 'none';
          imagePreview.src = '';
          return;
        }

        imagePreview.src = URL.createObjectURL(file);
        imagePlaceholder.style.display = 'none';
        imagePreviewWrapper.style.display = 'block';
      });
    }
  });
</script>
@endsection