@extends('layouts.nhom')

@section('title', 'Chi tiết chiến dịch | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chi tiết chiến dịch</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}">Chiến dịch</a>
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
        <h4 class="fw-bold mb-2">{{ $chienDich->tenChienDich }}</h4>

        @if ($chienDich->trangThai == 'Đang hoạt động')
          <span class="d-inline-flex align-items-center justify-content-center gap-2 mb-3">
            <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
            {{ $chienDich->trangThai }}
          </span>
        @elseif ($chienDich->trangThai == 'Hoàn thành')
          <span class="d-inline-flex align-items-center justify-content-center gap-2 mb-3">
            <span class="rounded-circle bg-primary d-inline-block" style="width: 8px; height: 8px;"></span>
            {{ $chienDich->trangThai }}
          </span>
        @else
          <span class="d-inline-flex align-items-center justify-content-center gap-2 mb-3">
            <span class="rounded-circle bg-secondary d-inline-block" style="width: 8px; height: 8px;"></span>
            {{ $chienDich->trangThai ?? '-' }}
          </span>
        @endif

        <p class="text-muted">
          {{ $chienDich->moTa ?? 'Chưa có mô tả cho chiến dịch này.' }}
        </p>

        <div class="d-grid gap-2">
          @if ($laNhomTruong)
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/edit') }}"
              class="btn btn-warning">
              Sửa thông tin
            </a>
          @endif

          <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich') }}" class="btn btn-secondary">
            Quay lại danh sách
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Thông tin chiến dịch</h5>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4 text-muted">Mã chiến dịch</div>
          <div class="col-md-8">{{ $chienDich->idChienDich }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Tên chiến dịch</div>
          <div class="col-md-8">{{ $chienDich->tenChienDich }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Nhóm phụ trách</div>
          <div class="col-md-8">{{ $nhom->tenNhom }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Thiên tai</div>
          <div class="col-md-8">
            @if ($chienDich->thienTai)
              {{ $chienDich->thienTai->tenThienTai }}
              @if ($chienDich->thienTai->namXayRa)
                - {{ $chienDich->thienTai->namXayRa }}
              @endif
            @else
              -
            @endif
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Địa điểm</div>
          <div class="col-md-8">
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
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Ngày tạo</div>
          <div class="col-md-8">{{ $chienDich->ngayTao ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Ngày bắt đầu</div>
          <div class="col-md-8">{{ $chienDich->ngayBatDau ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Ngày kết thúc</div>
          <div class="col-md-8">{{ $chienDich->ngayKetThuc ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Thông báo UBND</div>
          <div class="col-md-8">
            {{ $chienDich->daThongBaoUBND ? 'Đã thông báo' : 'Chưa thông báo' }}
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Ghi chú UBND</div>
          <div class="col-md-8">{{ $chienDich->ghiChuUBND ?? '-' }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4 text-muted">Trạng thái</div>
          <div class="col-md-8">{{ $chienDich->trangThai ?? '-' }}</div>
        </div>

        <div class="row">
          <div class="col-md-4 text-muted">Mô tả</div>
          <div class="col-md-8">{{ $chienDich->moTa ?? '-' }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" id="chienDichTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="cap-nhat-tab" data-bs-toggle="tab" data-bs-target="#cap-nhat"
          type="button" role="tab">
          Cập nhật tiến độ
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="yeu-cau-tab" data-bs-toggle="tab" data-bs-target="#yeu-cau"
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
        <button class="nav-link" id="nguon-luc-tab" data-bs-toggle="tab" data-bs-target="#nguon-luc"
          type="button" role="tab">
          Nguồn lực
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="phan-phoi-tab" data-bs-toggle="tab" data-bs-target="#phan-phoi"
          type="button" role="tab">
          Phân phối
        </button>
      </li>
    </ul>
  </div>

  <div class="card-body">
    <div class="tab-content" id="chienDichTabsContent">
      <div class="tab-pane fade show active" id="cap-nhat" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Cập nhật tiến độ</h5>
            <small class="text-muted">
              Ghi nhận các hoạt động, tình hình và minh chứng trong quá trình triển khai chiến dịch.
            </small>
          </div>

          <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/cap-nhat/create') }}"
            class="btn btn-primary">
            Thêm cập nhật
          </a>
        </div>

        @forelse ($capNhats as $capNhat)
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <div class="fw-semibold">
                  {{ $capNhat->thanhVien->nguoiDung->hoTen ?? 'Thành viên nhóm' }}
                </div>

                <small class="text-muted">
                  {{ $capNhat->thoiGianCapNhat ?? '-' }}
                </small>
              </div>
            </div>

            <p class="mb-2">
              {{ $capNhat->noiDung }}
            </p>

            @if ($capNhat->hinhAnh)
              <div class="mt-2">
                <img src="{{ asset('storage/' . $capNhat->hinhAnh) }}"
                  alt="Hình ảnh cập nhật"
                  class="img-fluid rounded border"
                  style="max-height: 260px;">
              </div>
            @endif
          </div>
        @empty
          <div class="text-center text-muted py-4">
            Chưa có cập nhật tiến độ cho chiến dịch này.
          </div>
        @endforelse
      </div>

      <div class="tab-pane fade" id="yeu-cau" role="tabpanel">
        <div class="mb-3">
          <h5 class="mb-0">Yêu cầu cứu trợ thuộc chiến dịch</h5>
          <small class="text-muted">
            Danh sách các yêu cầu đã được nhóm tiếp nhận và gắn vào chiến dịch này.
          </small>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Thông tin yêu cầu</th>
                <th style="width: 120px;">Số hộ</th>
                <th style="width: 140px;">Mức độ</th>
                <th style="width: 150px;">Trạng thái</th>
                <th style="width: 180px;">Dự kiến hỗ trợ</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($tiepNhanYeuCaus as $tiepNhan)
                <tr class="clickable-row"
                    data-href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $tiepNhan->idYeuCau) }}"
                    style="cursor: pointer;">
                  <td class="text-center">
                    {{ $tiepNhan->yeuCau->idYeuCau ?? '-' }}
                  </td>

                  <td>
                    <div class="fw-semibold">
                      {{ $tiepNhan->yeuCau->loaiYeuCau ?? '-' }}
                    </div>

                    <small class="text-muted">
                      Người gửi: {{ $tiepNhan->yeuCau->nguoiGui->hoTen ?? '-' }}
                    </small>

                    <br>

                    <small class="text-muted">
                      {{ $tiepNhan->yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
                      {{ $tiepNhan->yeuCau->diaDiem->phuongXa ?? '' }},
                      {{ $tiepNhan->yeuCau->diaDiem->tinhThanh ?? '' }}
                    </small>

                    @if ($tiepNhan->noiDungDamNhan)
                      <div class="mt-1">
                        <small>
                          <strong>Nội dung đảm nhận:</strong>
                          {{ $tiepNhan->noiDungDamNhan }}
                        </small>
                      </div>
                    @endif
                  </td>

                  <td class="text-center">
                    {{ $tiepNhan->yeuCau->soHoDan ?? '-' }}
                  </td>

                  <td class="text-center">
                    @if (($tiepNhan->yeuCau->mucDoKhanCap ?? '') == 'Khẩn cấp')
                      <span class="text-danger fw-semibold">Khẩn cấp</span>
                    @elseif (($tiepNhan->yeuCau->mucDoKhanCap ?? '') == 'Cao')
                      <span class="text-warning fw-semibold">Cao</span>
                    @else
                      {{ $tiepNhan->yeuCau->mucDoKhanCap ?? '-' }}
                    @endif
                  </td>

                  <td class="text-center">
                    {{ $tiepNhan->trangThai ?? '-' }}
                  </td>

                  <td class="text-center">
                    {{ $tiepNhan->thoiGianDuKienHoTro ?? '-' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    Chưa có yêu cầu cứu trợ nào được gắn vào chiến dịch này.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="dong-gop" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Đóng góp cho chiến dịch</h5>
            <small class="text-muted">
              Danh sách các lượt đóng góp từ người dùng. Chỉ đóng góp đã xác nhận mới được cộng vào nguồn lực.
            </small>
          </div>
        </div>

        @forelse ($dongGops as $dongGop)
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <div class="fw-semibold">
                  Người ủng hộ: {{ $dongGop->nguoiUngHo->hoTen ?? '-' }}
                </div>

                <small class="text-muted">
                  Thời gian gửi: {{ $dongGop->thoiGianDongGop ?? '-' }}
                </small>
              </div>

              <div class="text-end">
                <small class="text-muted">
                  Tiếp nhận:
                  {{ $dongGop->thanhVienTiepNhan->nguoiDung->hoTen ?? 'Chưa có' }}
                </small>
              </div>
            </div>

            @if ($dongGop->ghiChu)
              <p class="mb-2">
                <strong>Ghi chú:</strong> {{ $dongGop->ghiChu }}
              </p>
            @endif

            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead>
                  <tr class="text-center">
                    <th class="text-start">Hàng hóa</th>
                    <th style="width: 120px;">Số lượng</th>
                    <th style="width: 140px;">Hạn sử dụng</th>
                    <th style="width: 150px;">Trạng thái</th>
                    <th style="width: 150px;">Thao tác</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach ($dongGop->chiTietDongGops as $chiTiet)
                    <tr>
                      <td>
                        {{ $chiTiet->hangHoa->tenHangHoa ?? '-' }}
                        @if ($chiTiet->hangHoa?->donViTinh)
                          <small class="text-muted">({{ $chiTiet->hangHoa->donViTinh }})</small>
                        @endif
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->soLuong }}
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->hanSuDung ?? '-' }}
                      </td>

                      <td class="text-center">
                        @if ($chiTiet->trangThai == 'Chờ xác nhận')
                          <span class="d-inline-flex align-items-center gap-2">
                            <span class="rounded-circle bg-warning d-inline-block" style="width: 8px; height: 8px;"></span>
                            Chờ xác nhận
                          </span>
                        @elseif ($chiTiet->trangThai == 'Đã xác nhận')
                          <span class="d-inline-flex align-items-center gap-2">
                            <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                            Đã xác nhận
                          </span>
                        @elseif ($chiTiet->trangThai == 'Từ chối')
                          <span class="d-inline-flex align-items-center gap-2">
                            <span class="rounded-circle bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                            Từ chối
                          </span>
                        @else
                          {{ $chiTiet->trangThai ?? '-' }}
                        @endif
                      </td>

                      <td class="text-center">
                        @if ($chiTiet->trangThai == 'Chờ xác nhận')
                          <div class="d-inline-flex gap-1">
                            <form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/dong-gop/' . $chiTiet->idChiTietDongGop . '/xac-nhan') }}"
                              method="POST"
                              onsubmit="return confirm('Xác nhận đóng góp này và cộng vào nguồn lực chiến dịch?')">
                              @csrf
                              @method('PATCH')

                              <button type="submit" class="btn btn-sm btn-light border text-success" title="Xác nhận">
                                <i class="ti ti-check"></i>
                              </button>
                            </form>

                            <form action="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/dong-gop/' . $chiTiet->idChiTietDongGop . '/tu-choi') }}"
                              method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn từ chối đóng góp này không?')">
                              @csrf
                              @method('PATCH')

                              <button type="submit" class="btn btn-sm btn-light border text-danger" title="Từ chối">
                                <i class="ti ti-x"></i>
                              </button>
                            </form>
                          </div>
                        @else
                          -
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">
            Chưa có lượt đóng góp nào cho chiến dịch này.
          </div>
        @endforelse
      </div>

      <div class="tab-pane fade" id="nguon-luc" role="tabpanel">
        <div class="mb-3">
          <h5 class="mb-0">Nguồn lực hiện có</h5>
          <small class="text-muted">
            Nguồn lực được cộng từ các đóng góp đã xác nhận.
          </small>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="text-uppercase text-center">
                <th style="width: 90px;">Mã</th>
                <th class="text-start">Hàng hóa</th>
                <th style="width: 160px;">Số lượng hiện có</th>
                <th style="width: 150px;">Hạn sử dụng</th>
                <th style="width: 150px;">Trạng thái</th>
                <th style="width: 170px;">Ngày cập nhật</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($nguonLucs as $nguonLuc)
                <tr>
                  <td class="text-center">{{ $nguonLuc->idNguonLuc }}</td>

                  <td>
                    {{ $nguonLuc->hangHoa->tenHangHoa ?? '-' }}
                    @if ($nguonLuc->hangHoa?->donViTinh)
                      <small class="text-muted">({{ $nguonLuc->hangHoa->donViTinh }})</small>
                    @endif
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->soLuongHienCo }}
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->hanSuDung ?? '-' }}
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->trangThai ?? '-' }}
                  </td>

                  <td class="text-center">
                    {{ $nguonLuc->ngayCapNhat ?? '-' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    Chưa có nguồn lực nào. Nguồn lực sẽ được cộng khi nhóm xác nhận đóng góp.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="phan-phoi" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Đợt phân phối</h5>
            <small class="text-muted">
              Danh sách các đợt phân phối hàng cứu trợ từ nguồn lực chiến dịch.
            </small>
          </div>

          <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich . '/phan-phoi/create') }}"
            class="btn btn-primary">
            Tạo đợt phân phối
          </a>
        </div>

        @forelse ($dotPhanPhois as $dotPhanPhoi)
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <div class="fw-semibold">
                  Đợt phân phối #{{ $dotPhanPhoi->idDotPhanPhoi }}
                </div>

                <small class="text-muted">
                  Ngày phân phối: {{ $dotPhanPhoi->ngayPhanPhoi ?? '-' }}
                </small>
              </div>

              <div class="text-end">
                <span>{{ $dotPhanPhoi->trangThai ?? '-' }}</span>
              </div>
            </div>

            @if ($dotPhanPhoi->ghiChu)
              <p class="mb-2">
                <strong>Ghi chú:</strong> {{ $dotPhanPhoi->ghiChu }}
              </p>
            @endif

            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead>
                  <tr class="text-center">
                    <th class="text-start">Nguồn lực</th>
                    <th style="width: 130px;">Số lượng giao</th>
                    <th class="text-start">Yêu cầu nhận hỗ trợ</th>
                    <th style="width: 160px;">Người nhận</th>
                    <th style="width: 140px;">Trạng thái</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach ($dotPhanPhoi->chiTietPhanPhois as $chiTiet)
                    <tr>
                      <td>
                        {{ $chiTiet->nguonLuc->hangHoa->tenHangHoa ?? '-' }}
                        @if ($chiTiet->nguonLuc?->hangHoa?->donViTinh)
                          <small class="text-muted">
                            ({{ $chiTiet->nguonLuc->hangHoa->donViTinh }})
                          </small>
                        @endif
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->soLuongGiao }}
                      </td>

                      <td>
                        #{{ $chiTiet->tiepNhan->yeuCau->idYeuCau ?? '-' }}
                        - {{ $chiTiet->tiepNhan->yeuCau->loaiYeuCau ?? '-' }}

                        <br>

                        <small class="text-muted">
                          {{ $chiTiet->tiepNhan->yeuCau->diaDiem->chiTietDiaDiem ?? '' }},
                          {{ $chiTiet->tiepNhan->yeuCau->diaDiem->phuongXa ?? '' }},
                          {{ $chiTiet->tiepNhan->yeuCau->diaDiem->tinhThanh ?? '' }}
                        </small>
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->nguoiNhan ?? '-' }}
                      </td>

                      <td class="text-center">
                        {{ $chiTiet->trangThai ?? '-' }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">
            Chưa có đợt phân phối nào cho chiến dịch này.
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.clickable-row').forEach(function (row) {
      row.addEventListener('click', function () {
        const href = row.getAttribute('data-href');

        if (href) {
          window.location.href = href;
        }
      });
    });
  });
</script>
@endsection