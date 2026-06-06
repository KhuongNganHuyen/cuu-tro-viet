@extends('layouts.nhom')

@section('title', 'Chiến dịch của nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Chiến dịch của nhóm</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Chiến dịch</li>
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

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Danh sách chiến dịch</h5>
      <small class="text-muted">
        Theo dõi các chiến dịch cứu trợ do nhóm phụ trách.
      </small>
    </div>

    @if ($laNhomTruong)
      <a href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/create') }}" class="btn btn-primary">
        Thêm chiến dịch
      </a>
    @endif
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr class="text-uppercase text-center">
            <th style="width: 90px;">Mã</th>
            <th class="text-start">Tên chiến dịch</th>
            <th class="text-start">Thiên tai</th>
            <th class="text-start">Địa điểm</th>
            <th style="width: 130px;">Bắt đầu</th>
            <th style="width: 130px;">Kết thúc</th>
            <th style="width: 160px;">UBND</th>
            <th style="width: 160px;">Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($chienDichs as $chienDich)
            <tr class="clickable-row"
                data-href="{{ url('/nhom/' . $nhom->idNhom . '/chien-dich/' . $chienDich->idChienDich) }}"
                style="cursor: pointer;">
              <td class="text-center">{{ $chienDich->idChienDich }}</td>

              <td>
                <div class="fw-semibold">
                  {{ $chienDich->tenChienDich }}
                </div>

                @if ($chienDich->moTa)
                  <small class="text-muted">
                    {{ \Illuminate\Support\Str::limit($chienDich->moTa, 80) }}
                  </small>
                @endif
              </td>

              <td>
                @if ($chienDich->thienTai)
                  {{ $chienDich->thienTai->tenThienTai }}
                  @if ($chienDich->thienTai->namXayRa)
                    <small class="text-muted">({{ $chienDich->thienTai->namXayRa }})</small>
                  @endif
                @else
                  -
                @endif
              </td>

              <td>
                @if ($chienDich->diaDiem)
                  @if ($chienDich->diaDiem->chiTietDiaDiem)
                    {{ $chienDich->diaDiem->chiTietDiaDiem }},
                  @endif

                  @if ($chienDich->diaDiem->phuongXa)
                    {{ $chienDich->diaDiem->phuongXa }},
                  @endif

                  {{ $chienDich->diaDiem->tinhThanh }}
                @else
                  -
                @endif
              </td>

              <td class="text-center">
                {{ $chienDich->ngayBatDau ?? '-' }}
              </td>

              <td class="text-center">
                {{ $chienDich->ngayKetThuc ?? '-' }}
              </td>

              <td class="text-center">
                @if ($chienDich->daThongBaoUBND)
                  Đã thông báo
                @else
                  Chưa thông báo
                @endif
              </td>

              <td class="text-center">
                @if ($chienDich->trangThai == 'Đang hoạt động')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                    {{ $chienDich->trangThai }}
                  </span>
                @elseif ($chienDich->trangThai == 'Hoàn thành')
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary d-inline-block" style="width: 8px; height: 8px;"></span>
                    {{ $chienDich->trangThai }}
                  </span>
                @else
                  <span class="d-inline-flex align-items-center gap-2">
                    <span class="rounded-circle bg-secondary d-inline-block" style="width: 8px; height: 8px;"></span>
                    {{ $chienDich->trangThai ?? '-' }}
                  </span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-4">
                Nhóm này chưa có chiến dịch cứu trợ nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
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