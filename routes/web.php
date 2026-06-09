<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\ThienTaiController;
use App\Http\Controllers\DanhMucHangController;
use App\Http\Controllers\HangHoaController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\NhomTinhNguyenController;
use App\Http\Controllers\ThanhVienNhomController;
use App\Http\Controllers\ChienDichCuuTroController;
use App\Http\Controllers\YeuCauCuuTroController;
use App\Http\Controllers\TiepNhanYeuCauController;

use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserNhomController;
use App\Http\Controllers\User\UserDongGopController;
use App\Http\Controllers\User\UserYeuCauCuuTroController;

use App\Http\Controllers\Nhom\NhomDashboardController;
use App\Http\Controllers\Nhom\NhomThanhVienController;
use App\Http\Controllers\Nhom\NhomChienDichController;
use App\Http\Controllers\Nhom\NhomYeuCauCuuTroController;

/*
|--------------------------------------------------------------------------
| Trang chính
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Auth - Đăng nhập / Đăng ký / Đăng xuất
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Admin - Tổng quan
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin - Danh mục hệ thống
|--------------------------------------------------------------------------
*/

Route::resource('/admin/dia-diem', DiaDiemController::class);
Route::resource('/admin/thien-tai', ThienTaiController::class);
Route::resource('/admin/danh-muc-hang', DanhMucHangController::class);
Route::get('/admin/danh-muc-hang/{idDanhMucHang}/hang-hoa', [HangHoaController::class, 'index']);
Route::get('/admin/danh-muc-hang/{idDanhMucHang}/hang-hoa/create', [HangHoaController::class, 'create']);
Route::post('/admin/danh-muc-hang/{idDanhMucHang}/hang-hoa', [HangHoaController::class, 'store']);

Route::get('/admin/hang-hoa/{idHangHoa}/edit', [HangHoaController::class, 'edit']);
Route::put('/admin/hang-hoa/{idHangHoa}', [HangHoaController::class, 'update']);
Route::patch('/admin/hang-hoa/{idHangHoa}/doi-trang-thai', [HangHoaController::class, 'doiTrangThai']);

/*
|--------------------------------------------------------------------------
| Admin - Quản lý người dùng
|--------------------------------------------------------------------------
*/

Route::patch('/admin/nguoi-dung/{id}/doi-trang-thai', [NguoiDungController::class, 'doiTrangThai']);
Route::resource('/admin/nguoi-dung', NguoiDungController::class);

/*
|--------------------------------------------------------------------------
| Admin - Quản lý nhóm tình nguyện
|--------------------------------------------------------------------------
*/

Route::patch('/admin/nhom-tinh-nguyen/{id}/duyet', [NhomTinhNguyenController::class, 'duyetNhom']);
Route::patch('/admin/nhom-tinh-nguyen/{id}/tu-choi', [NhomTinhNguyenController::class, 'tuChoiNhom']);
Route::patch('/admin/nhom-tinh-nguyen/{id}/doi-trang-thai', [NhomTinhNguyenController::class, 'doiTrangThai']);
Route::resource('/admin/nhom-tinh-nguyen', NhomTinhNguyenController::class);

/*
|--------------------------------------------------------------------------
| Admin - Thành viên nhóm tình nguyện
|--------------------------------------------------------------------------
| Tạm giữ route này để xem/test dữ liệu.
| Về nghiệp vụ chính thức, chức năng thêm/xóa thành viên sẽ chuyển sang
| giao diện nhóm trưởng/tình nguyện viên sau này.
|--------------------------------------------------------------------------
*/

Route::get('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'index']);
Route::get('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien/create', [ThanhVienNhomController::class, 'create']);
Route::post('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'store']);
Route::delete('/admin/thanh-vien-nhom/{idThanhVien}', [ThanhVienNhomController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Admin - Quản lý chiến dịch cứu trợ
|--------------------------------------------------------------------------
*/

Route::resource('/admin/chien-dich', ChienDichCuuTroController::class);

/*
|--------------------------------------------------------------------------
| Admin - Quản lý yêu cầu cứu trợ
|--------------------------------------------------------------------------
*/

Route::resource('/admin/yeu-cau-cuu-tro', YeuCauCuuTroController::class);

/*
|--------------------------------------------------------------------------
| Admin - Tiếp nhận yêu cầu cứu trợ
|--------------------------------------------------------------------------
*/

Route::get('/admin/tiep-nhan-yeu-cau', [TiepNhanYeuCauController::class, 'index']);
Route::get('/admin/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [TiepNhanYeuCauController::class, 'create']);
Route::post('/admin/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [TiepNhanYeuCauController::class, 'store']);
Route::delete('/admin/tiep-nhan-yeu-cau/{idTiepNhan}', [TiepNhanYeuCauController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| User - Người dân / Nhà hảo tâm
|--------------------------------------------------------------------------
*/

Route::get('/user/dashboard', [UserDashboardController::class, 'index']);
Route::get('/user/nhom-cua-toi', [UserNhomController::class, 'index']);
Route::get('/user/nhom-cua-toi/create', [UserNhomController::class, 'create']);
Route::post('/user/nhom-cua-toi', [UserNhomController::class, 'store']);
Route::get('/user/nhom-cua-toi/{id}', [UserNhomController::class, 'show']);
Route::get('/user/dong-gop', [UserDongGopController::class, 'index']);
Route::get('/user/dong-gop/create', [UserDongGopController::class, 'create']);
Route::post('/user/dong-gop', [UserDongGopController::class, 'store']);

Route::get('/user/yeu-cau-cuu-tro', [UserYeuCauCuuTroController::class, 'index']);
Route::get('/user/yeu-cau-cuu-tro/create', [UserYeuCauCuuTroController::class, 'create']);
Route::post('/user/yeu-cau-cuu-tro', [UserYeuCauCuuTroController::class, 'store']);
Route::get('/user/yeu-cau-cuu-tro/{idYeuCau}', [UserYeuCauCuuTroController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Nhom - Nhóm tình nguyện
|--------------------------------------------------------------------------
*/

Route::get('/nhom/{idNhom}/dashboard', [NhomDashboardController::class, 'index']);

Route::get('/nhom/{idNhom}/thanh-vien', [NhomThanhVienController::class, 'index']);
Route::get('/nhom/{idNhom}/thanh-vien/create', [NhomThanhVienController::class, 'create']);
Route::post('/nhom/{idNhom}/thanh-vien', [NhomThanhVienController::class, 'store']);
Route::delete('/nhom/{idNhom}/thanh-vien/{idThanhVien}', [NhomThanhVienController::class, 'destroy']);

Route::get('/nhom/{idNhom}/chien-dich', [NhomChienDichController::class, 'index']);
Route::get('/nhom/{idNhom}/chien-dich/create', [NhomChienDichController::class, 'create']);
Route::post('/nhom/{idNhom}/chien-dich', [NhomChienDichController::class, 'store']);
Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}', [NhomChienDichController::class, 'show']);
Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/edit', [NhomChienDichController::class, 'edit']);
Route::put('/nhom/{idNhom}/chien-dich/{idChienDich}', [NhomChienDichController::class, 'update']);
Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/cap-nhat/create', [NhomChienDichController::class, 'createCapNhat']);
Route::post('/nhom/{idNhom}/chien-dich/{idChienDich}/cap-nhat', [NhomChienDichController::class, 'storeCapNhat']);
Route::patch('/nhom/{idNhom}/chien-dich/{idChienDich}/dong-gop/{idChiTietDongGop}/xac-nhan', [NhomChienDichController::class, 'xacNhanChiTietDongGop']);
Route::patch('/nhom/{idNhom}/chien-dich/{idChienDich}/dong-gop/{idChiTietDongGop}/tu-choi', [NhomChienDichController::class, 'tuChoiChiTietDongGop']);
Route::get('/nhom/{idNhom}/yeu-cau-cuu-tro', [NhomYeuCauCuuTroController::class, 'index']);

Route::get('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}', [NhomYeuCauCuuTroController::class, 'show']);

Route::get('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [NhomYeuCauCuuTroController::class, 'createTiepNhan']);

Route::post('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [NhomYeuCauCuuTroController::class, 'storeTiepNhan']);
Route::get('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tao-chien-dich', [NhomYeuCauCuuTroController::class, 'createChienDichTuYeuCau']);

Route::post('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tao-chien-dich', [NhomYeuCauCuuTroController::class, 'storeChienDichTuYeuCau']);
Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/phan-phoi/create', [NhomChienDichController::class, 'createDotPhanPhoi']);

Route::post('/nhom/{idNhom}/chien-dich/{idChienDich}/phan-phoi', [NhomChienDichController::class, 'storeDotPhanPhoi']);