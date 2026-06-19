@php
  $tiepNhanCuaNhom = $yeuCau->tiepNhans->firstWhere('idNhom', $nhom->idNhom);

  $trangThai = $hienThiChienDich
      ? ($tiepNhanCuaNhom->trangThai ?? $yeuCau->trangThai)
      : $yeuCau->trangThai;

  $classTrangThai = match ($trangThai) {
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

  $diaChi = collect([
      $yeuCau->diaDiem->chiTietDiaDiem ?? null,
      $yeuCau->diaDiem->phuongXa ?? null,
      $yeuCau->diaDiem->tinhThanh ?? null,
  ])->filter()->implode(', ');

  $tinhThanh = $yeuCau->diaDiem->tinhThanh ?? '-';

  $tenChienDich = $tiepNhanCuaNhom->chienDich->tenChienDich ?? '-';
@endphp

<tr class="clickable-row"
    data-href="{{ url('/nhom/' . $nhom->idNhom . '/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau) }}">
  <td class="text-center fw-semibold">
    {{ $yeuCau->idYeuCau }}
  </td>

  <td>
    <div class="request-title">
      {{ $yeuCau->tieuDeYeuCau }}
    </div>

    <div class="request-meta">
      Người gửi: {{ $yeuCau->nguoiGui->hoTen ?? '-' }}
    </div>

    <div class="request-meta">
      {{ $diaChi !== '' ? $diaChi : 'Chưa có địa điểm' }}
    </div>
  </td>

  @if ($hienThiChienDich)
    <td class="text-center">
      {{ $tenChienDich }}
    </td>
  @else
    <td class="text-center">
      {{ $yeuCau->soNguoi ?? '-' }}
    </td>
  @endif

  <td class="text-center">
    <span class="d-inline-flex align-items-center justify-content-center gap-2">
      <span class="muc-do-dot {{ $classMucDo }}"></span>
      <span>{{ $yeuCau->mucDoKhanCap ?? '-' }}</span>
    </span>
  </td>

  <td class="text-center">
    <span class="d-inline-flex align-items-center justify-content-center gap-2">
      <span class="status-dot {{ $classTrangThai }}"></span>
      <span>{{ $trangThai }}</span>
    </span>
  </td>

  <td class="text-center">
    {{ $tinhThanh }}
  </td>

  <td class="text-center">
    {{ $yeuCau->thoiGianGui
        ? \Carbon\Carbon::parse($yeuCau->thoiGianGui)->format('d/m/Y H:i')
        : '-' }}
  </td>
</tr>