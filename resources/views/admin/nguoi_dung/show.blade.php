@extends('layouts.admin')

@section('title', 'Chi tiết người dùng | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết người dùng</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/nguoi-dung') }}">Người dùng</a>
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
      <div class="card-body text-center">
        @if ($nguoiDung->anhDaiDien)
          <img src="{{ asset('storage/' . $nguoiDung->anhDaiDien) }}"
               alt="Ảnh đại diện"
               class="rounded-circle mb-3"
               style="width: 130px; height: 130px; object-fit: cover;">
        @else
          <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
               style="width: 130px; height: 130px;">
            <i class="ti ti-user fs-1 text-muted"></i>
          </div>
        @endif

        <h5 class="mb-1">{{ $nguoiDung->hoTen }}</h5>
        <div class="text-muted mb-3">{{ $nguoiDung->tenDangNhap }}</div>

        <div class="mb-3">
          @if ($nguoiDung->trangThai == 'Hoạt động')
            <span class="d-inline-flex align-items-center gap-2">
              <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
              Hoạt động
            </span>
          @else
            <span class="d-inline-flex align-items-center gap-2">
              <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
              {{ $nguoiDung->trangThai }}
            </span>
          @endif
        </div>

        <div class="d-grid gap-2">
          <a href="{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung . '/edit') }}" class="btn btn-warning">
            Sửa thông tin
          </a>

          <form action="{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung . '/doi-trang-thai') }}" method="POST"
            onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái tài khoản này không?')">
            @csrf
            @method('PATCH')

            @if ($nguoiDung->trangThai == 'Hoạt động')
              <button type="submit" class="btn btn-outline-danger w-100">
                Khóa tài khoản
              </button>
            @else
              <button type="submit" class="btn btn-outline-success w-100">
                Mở khóa tài khoản
              </button>
            @endif
          </form>

          <form action="{{ url('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung) }}" method="POST"
            onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này không?')">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-outline-danger w-100">
              Xóa người dùng
            </button>
          </form>

          <a href="{{ url('/admin/nguoi-dung') }}" class="btn btn-secondary">
            Quay lại danh sách
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Thông tin cá nhân</h5>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4 text-muted">Mã người dùng</div>
          <div class="col-md-8">{{ $nguoiDung->idNguoiDung }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Họ tên</div>
          <div class="col-md-8">{{ $nguoiDung->hoTen }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Tên đăng nhập</div>
          <div class="col-md-8">{{ $nguoiDung->tenDangNhap }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Email</div>
          <div class="col-md-8">{{ $nguoiDung->email ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Số điện thoại</div>
          <div class="col-md-8">{{ $nguoiDung->sdt ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Giới tính</div>
          <div class="col-md-8">{{ $nguoiDung->gioiTinh ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Ngày sinh</div>
          <div class="col-md-8">{{ $nguoiDung->ngaySinh ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Vai trò</div>
          <div class="col-md-8">{{ $nguoiDung->vaiTro }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Trạng thái</div>
          <div class="col-md-8">
            @if ($nguoiDung->trangThai == 'Hoạt động')
              <span class="d-inline-flex align-items-center gap-2">
                <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                Hoạt động
              </span>
            @else
              <span class="d-inline-flex align-items-center gap-2">
                <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                {{ $nguoiDung->trangThai }}
              </span>
            @endif
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 text-muted">Ngày tạo</div>
          <div class="col-md-8">{{ $nguoiDung->ngayTao ?? '-' }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" id="nguoiDungTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="yeu-cau-tab" data-bs-toggle="tab" data-bs-target="#yeu-cau"
          type="button" role="tab">
          Yêu cầu cứu trợ
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="dong-gop-tab" data-bs-toggle="tab" data-bs-target="#dong-gop"
          type="button" role="tab">
          Đóng góp
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="nhom-tab" data-bs-toggle="tab" data-bs-target="#nhom"
          type="button" role="tab">
          Nhóm tình nguyện
        </button>
      </li>
    </ul>
  </div>

  <div class="card-body">
    <div class="tab-content" id="nguoiDungTabsContent">
      <div class="tab-pane fade show active" id="yeu-cau" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase">
                <th style="width: 90px;">Mã</th>
                <th>Loại yêu cầu</th>
                <th>Địa điểm</th>
                <th style="width: 150px;">Mức độ</th>
                <th style="width: 150px;">Trạng thái</th>
                <th style="width: 180px;">Thời gian gửi</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($yeuCaus as $yeuCau)
                <tr>
                  <td>{{ $yeuCau->idYeuCau }}</td>
                  <td>{{ $yeuCau->loaiYeuCau ?? '-' }}</td>
                  <td>
                    @if ($yeuCau->diaDiem)
                      {{ $yeuCau->diaDiem->chiTietDiaDiem ?? '' }}
                      @if ($yeuCau->diaDiem->phuongXa)
                        , {{ $yeuCau->diaDiem->phuongXa }}
                      @endif
                      @if ($yeuCau->diaDiem->tinhThanh)
                        , {{ $yeuCau->diaDiem->tinhThanh }}
                      @endif
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $yeuCau->mucDoKhanCap ?? '-' }}</td>
                  <td>{{ $yeuCau->trangThai ?? '-' }}</td>
                  <td>{{ $yeuCau->thoiGianGui ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    Người dùng này chưa gửi yêu cầu cứu trợ nào.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="dong-gop" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase">
                <th style="width: 90px;">Mã</th>
                <th>Chiến dịch</th>
                <th>Ghi chú</th>
                <th style="width: 160px;">Số loại hàng</th>
                <th style="width: 180px;">Thời gian đóng góp</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($dongGops as $dongGop)
                <tr>
                  <td>{{ $dongGop->idDongGop }}</td>
                  <td>{{ $dongGop->chienDich->tenChienDich ?? '-' }}</td>
                  <td>{{ $dongGop->ghiChu ?? '-' }}</td>
                  <td>{{ $dongGop->chiTietDongGops->count() }}</td>
                  <td>{{ $dongGop->thoiGianDongGop ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    Người dùng này chưa có lịch sử đóng góp.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="nhom" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase">
                <th style="width: 90px;">Mã TV</th>
                <th>Nhóm tình nguyện</th>
                <th style="width: 180px;">Vai trò</th>
                <th style="width: 180px;">Ngày tham gia</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($thanhVienNhoms as $thanhVien)
                <tr>
                  <td>{{ $thanhVien->idThanhVien }}</td>
                  <td>{{ $thanhVien->nhom->tenNhom ?? '-' }}</td>
                  <td>{{ $thanhVien->vaiTro ?? '-' }}</td>
                  <td>{{ $thanhVien->ngayThamGia ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    Người dùng này chưa tham gia nhóm tình nguyện nào.
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