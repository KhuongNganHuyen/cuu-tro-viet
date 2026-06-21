@php
  $vaiTro = session('vaiTro');

  $layout = match ($vaiTro) {
      'Quản trị viên' => 'layouts.admin',
      'Người dùng' => 'layouts.user',
      default => 'layouts.user',
  };
@endphp

@extends($layout)

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-xl-10 mx-auto">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
              <h5 class="mb-1">Thông báo</h5>
              <small class="text-muted">
                Danh sách các thông báo mới nhất từ hệ thống.
              </small>
            </div>

            <a href="javascript:history.back()" class="btn btn-light border">
              Quay lại
            </a>
          </div>
        </div>

        <div class="card-body">
          @forelse ($thongBaos as $thongBao)
            @php
              $dangMo = (string) $idMoThongBao === (string) $thongBao->idThongBao;
            @endphp

            <div class="notification-item border rounded-3 mb-3 overflow-hidden">
              <button type="button"
                      class="notification-toggle w-100 border-0 bg-white text-start p-3"
                      data-target="thong-bao-{{ $thongBao->idThongBao }}">
                <div class="d-flex gap-3">
                  <div class="flex-shrink-0">
                    @if (!empty($thongBao->anhDaiDien))
                      <img src="{{ asset('storage/' . $thongBao->anhDaiDien) }}"
                          alt="avatar"
                          class="rounded-circle border"
                          style="width: 48px; height: 48px; object-fit: cover;">
                    @else
                      <div class="avtar avtar-s bg-light-primary">
                        <i class="ti ti-bell"></i>
                      </div>
                    @endif
                  </div>

                  <div class="flex-grow-1">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                      <h6 class="mb-1">
                        {{ $thongBao->tieuDe }}
                      </h6>

                      <small class="text-muted">
                        {{ $thongBao->thoiGianTao
                            ? \Carbon\Carbon::parse($thongBao->thoiGianTao)->diffForHumans()
                            : '' }}
                      </small>
                    </div>

                    <small class="text-muted">
                      {{ $thongBao->nguoiTao ?: $thongBao->doiTuong }}
                      ·
                      {{ $thongBao->thoiGianTao
                            ? \Carbon\Carbon::parse($thongBao->thoiGianTao)->format('d/m/Y H:i')
                            : '' }}
                    </small>
                  </div>
                </div>
              </button>

              <div id="thong-bao-{{ $thongBao->idThongBao }}"
                  class="notification-detail {{ $dangMo ? '' : 'd-none' }}">
                <div class="px-3 pb-3 ps-md-5 ms-md-4">
                  <div class="mb-3" style="white-space: normal; line-height: 1.7;">
                    {!! nl2br(e($thongBao->noiDung ?: 'Không có nội dung chi tiết.')) !!}
                  </div>

                  @if (!empty($thongBao->hinhAnh))
                    <div class="text-center my-3">
                      <img src="{{ asset('storage/' . $thongBao->hinhAnh) }}"
                          alt="Hình ảnh thông báo"
                          class="img-fluid rounded"
                          style="max-height: 360px; object-fit: contain;">
                    </div>
                  @endif

                  @if (!empty($thongBao->duongDan) && $thongBao->duongDan !== '/thong-bao')
                    <a href="{{ url($thongBao->duongDan) }}" class="btn btn-sm btn-primary">
                      Chi tiết
                    </a>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div class="text-center text-muted py-5">
              Chưa có thông báo nào.
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.notification-toggle');

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        const targetId = button.dataset.target;
        const target = document.getElementById(targetId);

        document.querySelectorAll('.notification-detail').forEach(function (detail) {
          if (detail.id !== targetId) {
            detail.classList.add('d-none');
          }
        });

        if (target) {
          target.classList.toggle('d-none');
        }
      });
    });

    const opened = document.querySelector('.notification-detail:not(.d-none)');

    if (opened) {
      opened.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
    }
  });
</script>
@endsection