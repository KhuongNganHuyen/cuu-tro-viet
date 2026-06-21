@extends('layouts.admin')

@section('title', 'Tổng quan | Cứu Trợ Việt')

@section('content')
<style>
  .admin-overview-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 24px;
  }

  .admin-overview-card {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background-color: #ffffff;
    padding: 22px 24px;
    min-height: 126px;
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.03);
  }

  .admin-overview-main {
    display: flex;
    align-items: baseline;
    gap: 14px;
    margin-bottom: 16px;
  }

  .admin-overview-number {
    font-size: 44px;
    line-height: 1;
    font-weight: 700;
    flex-shrink: 0;
  }

  .admin-overview-title {
    font-size: 18px;
    font-weight: 500;
    color: #212529;
    line-height: 1.35;
  }

  .admin-overview-desc {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
    line-height: 1.55;
  }

  .overview-blue { color: #0d6efd; }
  .overview-green { color: #198754; }
  .overview-yellow { color: #f59f00; }
  .overview-red { color: #dc3545; }

  .admin-dashboard-card {
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.04);
  }

  .admin-chart-box {
    min-height: 355px;
    border: 1px solid #eef0f3;
    border-radius: 12px;
    padding: 18px;
    background-color: #ffffff;
  }

  .admin-chart-area {
    min-height: 285px;
  }

  .notification-item {
    display: block;
    text-decoration: none;
    color: #212529;
    border: 1px solid #eef0f3;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 10px;
    background-color: #ffffff;
  }

  .notification-item:hover {
    background-color: #f8fafc;
    color: #212529;
  }

  .admin-task-item {
    padding: 13px 0;
    border-bottom: 1px solid #eef0f3;
  }

  .admin-task-item:last-child {
    border-bottom: 0;
  }

  @media (max-width: 1200px) {
    .admin-overview-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 768px) {
    .admin-overview-grid {
      grid-template-columns: 1fr;
    }

    .admin-overview-card {
      min-height: auto;
      padding: 18px 20px;
    }

    .admin-overview-number {
      font-size: 40px;
    }
  }
</style>

@php
  $soNguoiDung = $thongKe['soNguoiDung'] ?? 0;
  $soNhomTinhNguyen = $thongKe['soNhomTinhNguyen'] ?? 0;
  $soNhomChoDuyet = $thongKe['soNhomChoDuyet'] ?? 0;
  $soChienDich = $thongKe['soChienDich'] ?? 0;
  $soChienDichDangHoatDong = $thongKe['soChienDichDangHoatDong'] ?? 0;
  $soYeuCau = $thongKe['soYeuCau'] ?? 0;
  $soYeuCauChoTiepNhan = $thongKe['soYeuCauChoTiepNhan'] ?? 0;
  $soDongGop = $thongKe['soDongGop'] ?? 0;
@endphp

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Tổng quan</h5>
        </div>

        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Trang chủ</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Tổng quan</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="admin-overview-grid">
  <div class="admin-overview-card">
    <div class="admin-overview-main">
      <div class="admin-overview-number overview-blue">{{ $soNguoiDung }}</div>
      <div class="admin-overview-title">Người dùng</div>
    </div>
    <p class="admin-overview-desc">Tổng số tài khoản người dùng trong hệ thống.</p>
  </div>

  <div class="admin-overview-card">
    <div class="admin-overview-main">
      <div class="admin-overview-number overview-green">{{ $soNhomTinhNguyen }}</div>
      <div class="admin-overview-title">Nhóm tình nguyện</div>
    </div>
    <p class="admin-overview-desc">Có {{ $soNhomChoDuyet }} nhóm đang chờ quản trị viên duyệt.</p>
  </div>

  <div class="admin-overview-card">
    <div class="admin-overview-main">
      <div class="admin-overview-number overview-yellow">{{ $soChienDich }}</div>
      <div class="admin-overview-title">Chiến dịch</div>
    </div>
    <p class="admin-overview-desc">Có {{ $soChienDichDangHoatDong }} chiến dịch đang hoạt động.</p>
  </div>

  <div class="admin-overview-card">
    <div class="admin-overview-main">
      <div class="admin-overview-number overview-red">{{ $soYeuCau }}</div>
      <div class="admin-overview-title">Yêu cầu cứu trợ</div>
    </div>
    <p class="admin-overview-desc">Có {{ $soYeuCauChoTiepNhan }} yêu cầu đang chờ tiếp nhận.</p>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="card admin-dashboard-card h-100">
      <div class="card-body">
        <div class="admin-chart-box h-100">
          <h6 class="mb-1">Tổng quan hệ thống</h6>
          <small class="text-muted d-block mb-3">
            Một số lượng chính để quản trị viên theo dõi nhanh.
          </small>

          <div id="bieuDoTongQuanAdmin" class="admin-chart-area"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card admin-dashboard-card h-100">
      <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Thông báo mới nhất</h5>
          <a href="{{ url('/thong-bao') }}" class="small text-primary">
            Xem tất cả
          </a>
        </div>
      </div>

      <div class="card-body">
        @forelse ($thongBaoDashboard as $thongBao)
          <a href="{{ url('/thong-bao?mo=' . $thongBao->idThongBao) }}"
             class="notification-item">
            <div class="fw-semibold mb-1">
              {{ $thongBao->tieuDe }}
            </div>

            <small class="text-muted">
              {{ $thongBao->thoiGianTao
                  ? \Carbon\Carbon::parse($thongBao->thoiGianTao)->diffForHumans()
                  : '' }}
            </small>
          </a>
        @empty
          <div class="text-muted">
            Chưa có thông báo nào.
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card admin-dashboard-card h-100">
      <div class="card-body">
        <div class="admin-chart-box h-100">
          <h6 class="mb-1">Trạng thái yêu cầu cứu trợ</h6>
          <small class="text-muted d-block mb-3">
            Cơ cấu yêu cầu cứu trợ theo trạng thái hiện tại.
          </small>

          @if (count($duLieuDashboard['trangThaiYeuCau']['data'] ?? []) > 0)
            <div id="bieuDoTrangThaiYeuCauAdmin" class="admin-chart-area"></div>
          @else
            <div class="text-center text-muted py-5">
              Chưa có dữ liệu yêu cầu cứu trợ.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card admin-dashboard-card h-100">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">Việc cần xử lý</h5>
      </div>

      <div class="card-body">
        <div class="admin-task-item d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold">Nhóm chờ duyệt</div>
            <small class="text-muted">Các nhóm mới cần admin xét duyệt.</small>
          </div>
          <a href="{{ url('/admin/nhom-tinh-nguyen?trangThai=Chờ duyệt') }}" class="btn btn-sm btn-outline-primary">
            {{ $soNhomChoDuyet }}
          </a>
        </div>

        <div class="admin-task-item d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold">Yêu cầu chờ tiếp nhận</div>
            <small class="text-muted">Các yêu cầu cứu trợ chưa có nhóm tiếp nhận.</small>
          </div>
          <a href="{{ url('/admin/yeu-cau-cuu-tro?trangThai=Chờ tiếp nhận') }}" class="btn btn-sm btn-outline-primary">
            {{ $soYeuCauChoTiepNhan }}
          </a>
        </div>

        <div class="admin-task-item d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold">Chiến dịch đang hoạt động</div>
            <small class="text-muted">Theo dõi các chiến dịch đang triển khai.</small>
          </div>
          <a href="{{ url('/admin/chien-dich') }}" class="btn btn-sm btn-outline-primary">
            {{ $soChienDichDangHoatDong }}
          </a>
        </div>

        <div class="admin-task-item d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold">Lượt đóng góp</div>
            <small class="text-muted">Tổng số lượt đóng góp đã ghi nhận.</small>
          </div>
          <a href="{{ url('/admin/dong-gop') }}" class="btn btn-sm btn-outline-primary">
            {{ $soDongGop }}
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script id="duLieuDashboardAdmin" type="application/json">
{!! json_encode($duLieuDashboard, JSON_UNESCAPED_UNICODE) !!}
</script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const duLieuDashboardElement = document.getElementById('duLieuDashboardAdmin');

    if (!duLieuDashboardElement || !window.ApexCharts) {
      return;
    }

    const duLieuDashboard = JSON.parse(duLieuDashboardElement.textContent);

    const dinhDangSo = function (value) {
      return Number(value || 0).toLocaleString('vi-VN');
    };

    const cauHinhChung = {
      toolbar: { show: false },
      fontFamily: 'inherit'
    };

    const bieuDoTongQuanElement = document.getElementById('bieuDoTongQuanAdmin');

    if (bieuDoTongQuanElement && duLieuDashboard.tongQuanHeThong) {
      const bieuDoTongQuan = new ApexCharts(bieuDoTongQuanElement, {
        chart: {
          type: 'bar',
          height: 285,
          ...cauHinhChung
        },
        series: [
          {
            name: 'Số lượng',
            data: duLieuDashboard.tongQuanHeThong.data || []
          }
        ],
        xaxis: {
          categories: duLieuDashboard.tongQuanHeThong.labels || [],
          labels: {
            rotate: 0,
            trim: true
          }
        },
        yaxis: {
          min: 0,
          labels: {
            formatter: dinhDangSo
          }
        },
        plotOptions: {
          bar: {
            borderRadius: 5,
            columnWidth: '45%'
          }
        },
        dataLabels: {
          enabled: false
        },
        grid: {
          borderColor: '#f1f1f1'
        },
        tooltip: {
          y: {
            formatter: dinhDangSo
          }
        }
      });

      bieuDoTongQuan.render();
    }

    const bieuDoTrangThaiYeuCauElement = document.getElementById('bieuDoTrangThaiYeuCauAdmin');

    if (
      bieuDoTrangThaiYeuCauElement
      && duLieuDashboard.trangThaiYeuCau
      && duLieuDashboard.trangThaiYeuCau.data.length > 0
    ) {
      const bieuDoTrangThaiYeuCau = new ApexCharts(bieuDoTrangThaiYeuCauElement, {
        chart: {
          type: 'donut',
          height: 285,
          fontFamily: 'inherit'
        },
        series: duLieuDashboard.trangThaiYeuCau.data,
        labels: duLieuDashboard.trangThaiYeuCau.labels,
        legend: {
          position: 'bottom'
        },
        dataLabels: {
          enabled: true,
          formatter: function (value) {
            return value.toFixed(1) + '%';
          }
        },
        plotOptions: {
          pie: {
            donut: {
              size: '68%'
            }
          }
        },
        tooltip: {
          y: {
            formatter: dinhDangSo
          }
        }
      });

      bieuDoTrangThaiYeuCau.render();
    }
  });
</script>
@endsection