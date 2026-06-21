@extends('layouts.user')

@section('title', 'Tổng quan người dùng | Cứu Trợ Việt')

@section('content')
<style>
  .user-dashboard-card {
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.04);
  }

  .user-overview-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
  }

  .user-overview-card {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background-color: #ffffff;
    padding: 22px 24px;
    min-height: 126px;
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.03);
  }

  .user-overview-main {
    display: flex;
    align-items: baseline;
    gap: 14px;
    margin-bottom: 16px;
  }

  .user-overview-number {
    font-size: 44px;
    line-height: 1;
    font-weight: 700;
    flex-shrink: 0;
  }

  .user-overview-title {
    font-size: 18px;
    font-weight: 500;
    color: #212529;
    line-height: 1.35;
  }

  .user-overview-desc {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
    line-height: 1.55;
  }

  .overview-blue { color: #0d6efd; }
  .overview-green { color: #198754; }
  .overview-yellow { color: #f59f00; }
  .overview-red { color: #dc3545; }

  .user-chart-box {
    min-height: 355px;
    border: 1px solid #eef0f3;
    border-radius: 12px;
    padding: 18px;
    background-color: #ffffff;
  }

  .user-chart-area {
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

  @media (max-width: 1200px) {
    .user-overview-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 768px) {
    .user-overview-grid {
      grid-template-columns: 1fr;
    }

    .user-overview-card {
      min-height: auto;
      padding: 18px 20px;
    }

    .user-overview-number {
      font-size: 40px;
    }

    .user-overview-title {
      font-size: 17px;
    }
  }
</style>

@php
  $soYeuCau = $thongKe['soYeuCau'] ?? 0;
  $soYeuCauHoanThanh = $thongKe['soYeuCauHoanThanh'] ?? 0;
  $soDongGop = $thongKe['soDongGop'] ?? 0;
  $soDongGopDaXacNhan = $thongKe['soDongGopDaXacNhan'] ?? 0;
  $soNhomThamGia = $thongKe['soNhomThamGia'] ?? 0;
  $soNhomChoDuyet = $thongKe['soNhomChoDuyet'] ?? 0;
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
            <a href="{{ url('/user/dashboard') }}">Người dùng</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Tổng quan</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="user-overview-grid mb-4">
  <div class="user-overview-card">
    <div class="user-overview-main">
      <div class="user-overview-number overview-blue">
        {{ $soYeuCau }}
      </div>

      <div class="user-overview-title">
        Yêu cầu cứu trợ
      </div>
    </div>

    <p class="user-overview-desc">
      Số yêu cầu cứu trợ bạn đã gửi lên hệ thống.
    </p>
  </div>

  <div class="user-overview-card">
    <div class="user-overview-main">
      <div class="user-overview-number overview-green">
        {{ $soDongGop }}
      </div>

      <div class="user-overview-title">
        Lượt đóng góp
      </div>
    </div>

    <p class="user-overview-desc">
      Số lần bạn đăng ký đóng góp cho các chiến dịch.
    </p>
  </div>

  <div class="user-overview-card">
    <div class="user-overview-main">
      <div class="user-overview-number overview-yellow">
        {{ $soNhomThamGia }}
      </div>

      <div class="user-overview-title">
        Nhóm tham gia
      </div>
    </div>

    <p class="user-overview-desc">
      Số nhóm tình nguyện bạn đang là thành viên.
    </p>
  </div>

  <div class="user-overview-card">
    <div class="user-overview-main">
      <div class="user-overview-number overview-red">
        {{ $soNhomChoDuyet }}
      </div>

      <div class="user-overview-title">
        Nhóm chờ duyệt
      </div>
    </div>

    <p class="user-overview-desc">
      Số yêu cầu tạo nhóm đang chờ quản trị viên duyệt.
    </p>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="card user-dashboard-card h-100">
      <div class="card-body">
        <div class="user-chart-box h-100">
          <h6 class="mb-1">Hoạt động của người dùng</h6>
          <small class="text-muted d-block mb-3">
            Tổng hợp các hoạt động chính của bạn trên hệ thống.
          </small>

          <div id="bieuDoTongQuanNguoiDung" class="user-chart-area"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card user-dashboard-card h-100">
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
  <div class="col-lg-8">
    <div class="card user-dashboard-card h-100">
      <div class="card-body">
        <div class="user-chart-box h-100">
          <h6 class="mb-1">Trạng thái đóng góp</h6>
          <small class="text-muted d-block mb-3">
            Theo dõi tình trạng các mặt hàng bạn đã đăng ký đóng góp.
          </small>

          @if (count($duLieuDashboard['trangThaiDongGop']['data'] ?? []) > 0)
            <div id="bieuDoTrangThaiDongGopNguoiDung" class="user-chart-area"></div>
          @else
            <div class="text-center text-muted py-5">
              Chưa có dữ liệu đóng góp.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card user-dashboard-card h-100">
      <div class="card-body">
        <div class="user-chart-box h-100">
          <h6 class="mb-1">Trạng thái yêu cầu cứu trợ</h6>
          <small class="text-muted d-block mb-3">
            Cơ cấu trạng thái các yêu cầu bạn đã gửi.
          </small>

          @if (count($duLieuDashboard['trangThaiYeuCau']['data'] ?? []) > 0)
            <div id="bieuDoTrangThaiYeuCauNguoiDung" class="user-chart-area"></div>
          @else
            <div class="text-center text-muted py-5">
              Chưa có dữ liệu yêu cầu cứu trợ.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<script id="duLieuDashboardNguoiDung" type="application/json">
{!! json_encode($duLieuDashboard, JSON_UNESCAPED_UNICODE) !!}
</script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const duLieuDashboardElement = document.getElementById('duLieuDashboardNguoiDung');

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

    const bieuDoTongQuanElement = document.getElementById('bieuDoTongQuanNguoiDung');

    if (bieuDoTongQuanElement && duLieuDashboard.tongQuanCaNhan) {
      const bieuDoTongQuan = new ApexCharts(bieuDoTongQuanElement, {
        chart: {
          type: 'bar',
          height: 285,
          ...cauHinhChung
        },
        series: [
          {
            name: 'Số lượng',
            data: duLieuDashboard.tongQuanCaNhan.data || []
          }
        ],
        xaxis: {
          categories: duLieuDashboard.tongQuanCaNhan.labels || [],
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

    const bieuDoTrangThaiYeuCauElement = document.getElementById('bieuDoTrangThaiYeuCauNguoiDung');

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

    const bieuDoTrangThaiDongGopElement = document.getElementById('bieuDoTrangThaiDongGopNguoiDung');

    if (
      bieuDoTrangThaiDongGopElement
      && duLieuDashboard.trangThaiDongGop
      && duLieuDashboard.trangThaiDongGop.data.length > 0
    ) {
      const bieuDoTrangThaiDongGop = new ApexCharts(bieuDoTrangThaiDongGopElement, {
        chart: {
          type: 'bar',
          height: 285,
          ...cauHinhChung
        },
        series: [
          {
            name: 'Mặt hàng đóng góp',
            data: duLieuDashboard.trangThaiDongGop.data
          }
        ],
        xaxis: {
          categories: duLieuDashboard.trangThaiDongGop.labels,
          labels: {
            formatter: dinhDangSo
          }
        },
        yaxis: {
          labels: {
            maxWidth: 140
          }
        },
        plotOptions: {
          bar: {
            horizontal: true,
            borderRadius: 5,
            barHeight: '45%'
          }
        },
        dataLabels: {
          enabled: true,
          formatter: dinhDangSo
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

      bieuDoTrangThaiDongGop.render();
    }
  });
</script>
@endsection