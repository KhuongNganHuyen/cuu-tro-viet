<div class="table-responsive">
  <table class="table table-hover mb-0 yeu-cau-table">
    <thead>
      <tr class="text-uppercase text-center">
        <th style="width: 80px;">Mã</th>
        <th class="text-start">Thông tin yêu cầu</th>

        @if ($hienThiNhom)
          <th style="width: 220px;">Nhóm tiếp nhận</th>
        @else
          <th style="width: 120px;">Số người</th>
        @endif

        <th class="filter-heading-cell" style="width: 150px;">
          <div class="dropdown w-100 h-100">
            <button type="button"
                    class="filter-heading-button text-center"
                    data-bs-toggle="dropdown"
                    data-bs-boundary="viewport"
                    aria-expanded="false">
              <span>Mức độ</span>

              @if ($mucDoDangChon !== '')
                <span class="filter-active-dot"></span>
              @endif
            </button>

            <ul class="dropdown-menu filter-dropdown-menu">
              <li>
                <button type="button"
                        class="dropdown-item filter-option {{ $mucDoDangChon === '' ? 'active' : '' }}"
                        data-target="filterMucDoKhanCap"
                        data-value="">
                  Tất cả mức độ
                </button>
              </li>

              @foreach (['Khẩn cấp', 'Cao', 'Trung bình', 'Thấp'] as $mucDo)
                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $mucDoDangChon === $mucDo ? 'active' : '' }}"
                          data-target="filterMucDoKhanCap"
                          data-value="{{ $mucDo }}">
                    {{ $mucDo }}
                  </button>
                </li>
              @endforeach
            </ul>
          </div>
        </th>

        <th class="filter-heading-cell" style="width: 170px;">
          <div class="dropdown w-100 h-100">
            <button type="button"
                    class="filter-heading-button text-center"
                    data-bs-toggle="dropdown"
                    data-bs-boundary="viewport"
                    aria-expanded="false">
              <span>Trạng thái</span>

              @if ($trangThaiDangChon !== '')
                <span class="filter-active-dot"></span>
              @endif
            </button>

            <ul class="dropdown-menu filter-dropdown-menu">
              <li>
                <button type="button"
                        class="dropdown-item filter-option {{ $trangThaiDangChon === '' ? 'active' : '' }}"
                        data-target="filterTrangThai"
                        data-value="">
                  Tất cả trạng thái
                </button>
              </li>

              @foreach (['Chờ tiếp nhận', 'Đã tiếp nhận', 'Cần thêm hỗ trợ', 'Hoàn thành', 'Đã hủy'] as $trangThai)
                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $trangThaiDangChon === $trangThai ? 'active' : '' }}"
                          data-target="filterTrangThai"
                          data-value="{{ $trangThai }}">
                    {{ $trangThai }}
                  </button>
                </li>
              @endforeach
            </ul>
          </div>
        </th>

        <th class="filter-heading-cell" style="width: 170px;">
          <div class="dropdown w-100 h-100">
            <button type="button"
                    class="filter-heading-button text-center"
                    data-bs-toggle="dropdown"
                    data-bs-boundary="viewport"
                    aria-expanded="false">
              <span>Tỉnh/thành</span>

              @if ($tinhThanhDangChon !== '')
                <span class="filter-active-dot"></span>
              @endif
            </button>

            <ul class="dropdown-menu filter-dropdown-menu">
              <li>
                <button type="button"
                        class="dropdown-item filter-option {{ $tinhThanhDangChon === '' ? 'active' : '' }}"
                        data-target="filterTinhThanh"
                        data-value="">
                  Tất cả tỉnh/thành
                </button>
              </li>

              @foreach ($danhSachTinhThanh as $tinhThanh)
                <li>
                  <button type="button"
                          class="dropdown-item filter-option {{ $tinhThanhDangChon === $tinhThanh ? 'active' : '' }}"
                          data-target="filterTinhThanh"
                          data-value="{{ $tinhThanh }}">
                    {{ $tinhThanh }}
                  </button>
                </li>
              @endforeach
            </ul>
          </div>
        </th>

        <th style="width: 175px;">Thời gian gửi</th>
      </tr>
    </thead>

    <tbody>
      @forelse ($yeuCaus as $yeuCau)
        @php
          $diaChi = collect([
              $yeuCau->diaDiem->chiTietDiaDiem ?? null,
              $yeuCau->diaDiem->phuongXa ?? null,
              $yeuCau->diaDiem->tinhThanh ?? null,
          ])->filter()->implode(', ');

          $classTrangThai = match ($yeuCau->trangThai) {
              'Chờ tiếp nhận' => 'status-waiting',
              'Đã tiếp nhận' => 'status-received',
              'Cần thêm hỗ trợ' => 'status-more-help',
              'Hoàn thành' => 'status-completed',
              'Đã hủy' => 'status-cancelled',
              default => 'status-default',
          };

          $classMucDo = match ($yeuCau->mucDoKhanCap) {
              'Khẩn cấp' => 'muc-do-emergency',
              'Cao' => 'muc-do-high',
              'Trung bình' => 'muc-do-medium',
              'Thấp' => 'muc-do-low',
              default => 'muc-do-default',
          };

          $nhomTiepNhanText = $yeuCau->tiepNhans
              ->pluck('nhom.tenNhom')
              ->filter()
              ->unique()
              ->join(', ');
        @endphp

        <tr class="clickable-row"
            data-href="{{ url('/user/yeu-cau-cong-dong/' . $yeuCau->idYeuCau) }}">
          <td class="text-center">
            {{ $yeuCau->idYeuCau }}
          </td>

          <td class="text-start">
            <div class="request-title">
              {{ $yeuCau->tieuDeYeuCau ?? '-' }}
            </div>

            <div class="request-meta mt-1">
              Người gửi: {{ $yeuCau->nguoiGui->hoTen ?? '-' }}
            </div>

            <div class="request-meta mt-1">
              {{ $diaChi !== '' ? $diaChi : '-' }}
            </div>
          </td>

          @if ($hienThiNhom)
            <td class="text-center">
              {{ $nhomTiepNhanText !== '' ? $nhomTiepNhanText : '-' }}
            </td>
          @else
            <td class="text-center">
              {{ $yeuCau->soNguoi ?? '-' }}
            </td>
          @endif

          <td class="text-center">
            <span class="d-inline-flex align-items-center justify-content-center gap-2">
              <span class="muc-do-dot {{ $classMucDo }}"></span>
              {{ $yeuCau->mucDoKhanCap ?? '-' }}
            </span>
          </td>

          <td class="text-center">
            <span class="d-inline-flex align-items-center justify-content-center gap-2">
              <span class="status-dot {{ $classTrangThai }}"></span>
              {{ $yeuCau->trangThai ?? '-' }}
            </span>
          </td>

          <td class="text-center">
            {{ $yeuCau->diaDiem->tinhThanh ?? '-' }}
          </td>

          <td class="text-center">
            {{ $yeuCau->thoiGianGui
                ? \Carbon\Carbon::parse($yeuCau->thoiGianGui)->format('d/m/Y H:i')
                : '-' }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-4">
            @if (request('tuKhoa') || $dangLoc)
              Không tìm thấy yêu cầu cứu trợ phù hợp.
            @else
              Chưa có yêu cầu cứu trợ nào trong mục này.
            @endif
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>