@extends('layouts.admin')

@section('title', 'Tổng quan | Cứu Trợ Việt')

@section('content')
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

<div class="row">
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Người dùng</h6>
        <h4 class="mb-3">0</h4>
        <p class="mb-0 text-muted text-sm">Tổng số người dùng trong hệ thống</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Nhóm tình nguyện</h6>
        <h4 class="mb-3">0</h4>
        <p class="mb-0 text-muted text-sm">Tổng số nhóm tình nguyện</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Chiến dịch</h6>
        <h4 class="mb-3">0</h4>
        <p class="mb-0 text-muted text-sm">Tổng số chiến dịch cứu trợ</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2 f-w-400 text-muted">Yêu cầu cứu trợ</h6>
        <h4 class="mb-3">0</h4>
        <p class="mb-0 text-muted text-sm">Tổng số yêu cầu cứu trợ</p>
      </div>
    </div>
  </div>
</div>
@endsection