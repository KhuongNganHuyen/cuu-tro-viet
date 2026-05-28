@extends('layouts.admin')

@section('title', 'Chi tiết nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết nhóm tình nguyện</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/nhom-tinh-nguyen') }}">Nhóm tình nguyện</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Chi tiết</li>
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

<div class="row">
  <div class="col-lg-4">
    <div class="card">
        <div class="card-body">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-2">{{ $nhomTinhNguyen->tenNhom }}</h4>

            @if ($nhomTinhNguyen->trangThai == 'Đang hoạt động' || $nhomTinhNguyen->trangThai == 'Hoạt động')
            <span class="d-inline-flex align-items-center justify-content-center gap-2">
                <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                {{ $nhomTinhNguyen->trangThai }}
            </span>
            @else
            <span class="d-inline-flex align-items-center justify-content-center gap-2">
                <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                {{ $nhomTinhNguyen->trangThai }}
            </span>
            @endif
        </div>

        <p class="text-muted mb-4">
            {{ $nhomTinhNguyen->moTa ?? 'Chưa có mô tả cho nhóm tình nguyện này.' }}
        </p>

        <div class="d-grid gap-2">
          <a href="{{ url('/admin/nhom-tinh-nguyen/' . $nhomTinhNguyen->idNhom . '/edit') }}" class="btn btn-warning">
            Sửa thông tin
          </a>

          <form action="{{ url('/admin/nhom-tinh-nguyen/' . $nhomTinhNguyen->idNhom . '/doi-trang-thai') }}" method="POST"
            onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái nhóm này không?')">
            @csrf
            @method('PATCH')

            @if ($nhomTinhNguyen->trangThai == 'Đang hoạt động' || $nhomTinhNguyen->trangThai == 'Hoạt động')
              <button type="submit" class="btn btn-outline-danger w-100">
                Khóa nhóm
              </button>
            @else
              <button type="submit" class="btn btn-outline-success w-100">
                Mở nhóm
              </button>
            @endif
          </form>

          <form action="{{ url('/admin/nhom-tinh-nguyen/' . $nhomTinhNguyen->idNhom) }}" method="POST"
            onsubmit="return confirm('Bạn có chắc muốn xóa nhóm tình nguyện này không?')">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-outline-danger w-100">
              Xóa nhóm
            </button>
          </form>

          <a href="{{ url('/admin/nhom-tinh-nguyen') }}" class="btn btn-secondary">
            Quay lại danh sách
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Thông tin nhóm</h5>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4 text-muted">Mã nhóm</div>
          <div class="col-md-8">{{ $nhomTinhNguyen->idNhom }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Tên nhóm</div>
          <div class="col-md-8">{{ $nhomTinhNguyen->tenNhom }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Nhóm trưởng</div>
          <div class="col-md-8">
            {{ $nhomTinhNguyen->nhomTruong->hoTen ?? '-' }}
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Địa điểm</div>
          <div class="col-md-8">
            @if ($nhomTinhNguyen->diaDiem)
              @if ($nhomTinhNguyen->diaDiem->chiTietDiaDiem)
                {{ $nhomTinhNguyen->diaDiem->chiTietDiaDiem }},
              @endif

              @if ($nhomTinhNguyen->diaDiem->phuongXa)
                {{ $nhomTinhNguyen->diaDiem->phuongXa }},
              @endif

              {{ $nhomTinhNguyen->diaDiem->tinhThanh ?? '-' }}
            @else
              -
            @endif
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Trạng thái</div>
          <div class="col-md-8">
            {{ $nhomTinhNguyen->trangThai }}
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Ngày tạo</div>
          <div class="col-md-8">{{ $nhomTinhNguyen->ngayTao ?? '-' }}</div>
        </div>

        <div class="row">
          <div class="col-md-4 text-muted">Mô tả</div>
          <div class="col-md-8">
            {{ $nhomTinhNguyen->moTa ?? '-' }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" id="nhomTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="thanh-vien-tab" data-bs-toggle="tab" data-bs-target="#thanh-vien"
          type="button" role="tab">
          Thành viên
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="chien-dich-tab" data-bs-toggle="tab" data-bs-target="#chien-dich"
          type="button" role="tab">
          Chiến dịch
        </button>
      </li>
    </ul>
  </div>

  <div class="card-body">
    <div class="tab-content" id="nhomTabsContent">
      <div class="tab-pane fade show active" id="thanh-vien" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase">
                <th style="width: 90px;">Mã TV</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th style="width: 180px;">Vai trò trong nhóm</th>
                <th style="width: 180px;">Ngày tham gia</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($thanhViens as $thanhVien)
                <tr>
                  <td>{{ $thanhVien->idThanhVien }}</td>
                  <td>{{ $thanhVien->nguoiDung->hoTen ?? '-' }}</td>
                  <td>{{ $thanhVien->nguoiDung->email ?? '-' }}</td>
                  <td>{{ $thanhVien->nguoiDung->sdt ?? '-' }}</td>
                  <td>{{ $thanhVien->vaiTro ?? '-' }}</td>
                  <td>{{ $thanhVien->ngayThamGia ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    Nhóm này chưa có thành viên nào.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="chien-dich" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase">
                <th style="width: 90px;">Mã</th>
                <th>Tên chiến dịch</th>
                <th>Thiên tai</th>
                <th>Địa điểm</th>
                <th style="width: 160px;">Trạng thái</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($chienDichs as $chienDich)
                <tr>
                  <td>{{ $chienDich->idChienDich }}</td>
                  <td>{{ $chienDich->tenChienDich }}</td>
                  <td>{{ $chienDich->thienTai->tenThienTai ?? '-' }}</td>
                  <td>
                    @if ($chienDich->diaDiem)
                      @if ($chienDich->diaDiem->chiTietDiaDiem)
                        {{ $chienDich->diaDiem->chiTietDiaDiem }},
                      @endif

                      @if ($chienDich->diaDiem->phuongXa)
                        {{ $chienDich->diaDiem->phuongXa }},
                      @endif

                      {{ $chienDich->diaDiem->tinhThanh ?? '-' }}
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $chienDich->trangThai ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    Nhóm này chưa có chiến dịch cứu trợ nào.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection