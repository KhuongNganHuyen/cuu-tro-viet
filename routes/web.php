<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ChienDichCuuTroController;
use App\Http\Controllers\DanhMucHangController;
use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\HangHoaController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\NhomTinhNguyenController;
use App\Http\Controllers\SuKienCuuTroController;
use App\Http\Controllers\ThanhVienNhomController;
use App\Http\Controllers\YeuCauCuuTroController;

use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserNhomTinhNguyenController;
use App\Http\Controllers\User\UserChienDichController;
use App\Http\Controllers\User\UserYeuCauCongDongController;
use App\Http\Controllers\User\UserDongGopController;
use App\Http\Controllers\User\UserNhomController;
use App\Http\Controllers\User\UserYeuCauCuuTroController;

use App\Http\Controllers\Nhom\NhomDashboardController;
use App\Http\Controllers\Nhom\NhomHangHoaController;
use App\Http\Controllers\Nhom\NhomThanhVienController;
use App\Http\Controllers\Nhom\NhomChienDichController;
use App\Http\Controllers\Nhom\NhomNguonLucChienDichController;
use App\Http\Controllers\Nhom\NhomYeuCauCuuTroController;
use App\Http\Controllers\Nhom\NhomPhanPhoiController;

/*
|--------------------------------------------------------------------------
| Trang chính
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect('/login'));

/*
|--------------------------------------------------------------------------
| Đăng nhập / Đăng ký / Đăng xuất
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/ho-so', [ProfileController::class, 'edit']);
Route::put('/ho-so', [ProfileController::class, 'update']);

Route::get('/doi-mat-khau', [ProfileController::class, 'editPassword']);
Route::put('/doi-mat-khau', [ProfileController::class, 'updatePassword']);

Route::get('/thong-bao', [ThongBaoController::class, 'index']);

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', fn() => view('admin.dashboard'));
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);
Route::resource('/admin/dia-diem', DiaDiemController::class);

Route::resource('/admin/su-kien-cuu-tro', SuKienCuuTroController::class);

Route::resource('/admin/danh-muc-hang', DanhMucHangController::class);

Route::get('/admin/danh-muc-hang/{idDanhMucHang}/hang-hoa', [HangHoaController::class, 'index']);
Route::get('/admin/danh-muc-hang/{idDanhMucHang}/hang-hoa/create', [HangHoaController::class, 'create']);
Route::post('/admin/danh-muc-hang/{idDanhMucHang}/hang-hoa', [HangHoaController::class, 'store']);
Route::get('/admin/hang-hoa/{idHangHoa}/edit', [HangHoaController::class, 'edit']);
Route::put('/admin/hang-hoa/{idHangHoa}', [HangHoaController::class, 'update']);
Route::patch('/admin/hang-hoa/{idHangHoa}/doi-trang-thai', [HangHoaController::class, 'doiTrangThai']);

Route::patch('/admin/nguoi-dung/{id}/doi-trang-thai', [NguoiDungController::class, 'doiTrangThai']);
Route::resource('/admin/nguoi-dung', NguoiDungController::class);

Route::patch('/admin/nhom-tinh-nguyen/{id}/duyet', [NhomTinhNguyenController::class, 'duyetNhom']);
Route::patch('/admin/nhom-tinh-nguyen/{id}/tu-choi', [NhomTinhNguyenController::class, 'tuChoiNhom']);
Route::patch('/admin/nhom-tinh-nguyen/{id}/doi-trang-thai', [NhomTinhNguyenController::class, 'doiTrangThai']);
Route::resource('/admin/nhom-tinh-nguyen', NhomTinhNguyenController::class);

Route::get('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'index']);
Route::get('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien/create', [ThanhVienNhomController::class, 'create']);
Route::post('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'store']);
Route::delete('/admin/thanh-vien-nhom/{idThanhVien}', [ThanhVienNhomController::class, 'destroy']);

Route::get('/admin/chien-dich', [ChienDichCuuTroController::class, 'index']);
Route::get('/admin/chien-dich/{idChienDich}', [ChienDichCuuTroController::class, 'show']);

Route::patch('/admin/chien-dich/{idChienDich}/tam-ngung', [ChienDichCuuTroController::class, 'tamNgung']);
Route::patch('/admin/chien-dich/{idChienDich}/mo-lai', [ChienDichCuuTroController::class, 'moLai']);
Route::patch('/admin/chien-dich/{idChienDich}/huy', [ChienDichCuuTroController::class, 'huy']);

Route::get('/admin/yeu-cau-cuu-tro', [YeuCauCuuTroController::class, 'index']);
Route::get('/admin/yeu-cau-cuu-tro/{id}', [YeuCauCuuTroController::class, 'show']);
Route::patch('/admin/yeu-cau-cuu-tro/{id}/huy', [YeuCauCuuTroController::class, 'huyYeuCau']);

/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

Route::get('/user/dashboard', [UserDashboardController::class, 'index']);

Route::get('/user/nhom-tinh-nguyen', [UserNhomTinhNguyenController::class, 'index']);
Route::get('/user/nhom-tinh-nguyen/{idNhom}', [UserNhomTinhNguyenController::class, 'show']);
Route::get('/user/chien-dich', [UserChienDichController::class, 'index']);
Route::get('/user/chien-dich/{idChienDich}', [UserChienDichController::class, 'show']);
Route::get('/user/yeu-cau-cong-dong', [UserYeuCauCongDongController::class, 'index']);
Route::get('/user/yeu-cau-cong-dong/{idYeuCau}', [UserYeuCauCongDongController::class, 'show']);

Route::get('/user/yeu-cau-cuu-tro', [UserYeuCauCuuTroController::class, 'index']);
Route::get('/user/yeu-cau-cuu-tro/create', [UserYeuCauCuuTroController::class, 'create']);
Route::post('/user/yeu-cau-cuu-tro', [UserYeuCauCuuTroController::class, 'store']);
Route::get('/user/yeu-cau-cuu-tro/{idYeuCau}', [UserYeuCauCuuTroController::class, 'show']);
Route::patch('/user/yeu-cau-cuu-tro/{idYeuCau}/huy', [UserYeuCauCuuTroController::class, 'huyYeuCau']);
Route::patch('/user/yeu-cau-cuu-tro/{idYeuCau}/can-them-ho-tro', [UserYeuCauCuuTroController::class, 'canThemHoTro']);
Route::patch('/user/yeu-cau-cuu-tro/{idYeuCau}/thu-hoi-can-them-ho-tro', [UserYeuCauCuuTroController::class, 'thuHoiCanThemHoTro']);
Route::patch('/user/yeu-cau-cuu-tro/{idYeuCau}/xac-nhan-hoan-thanh', [UserYeuCauCuuTroController::class, 'xacNhanHoanThanh']);

Route::get('/user/dong-gop', [UserDongGopController::class, 'index']);
Route::get('/user/dong-gop/create', [UserDongGopController::class, 'create']);
Route::get('/user/dong-gop/{idDongGop}', [UserDongGopController::class, 'show']);
Route::post('/user/dong-gop', [UserDongGopController::class, 'store']);

Route::get('/user/nhom-cua-toi', [UserNhomController::class, 'index']);
Route::get('/user/nhom-cua-toi/create', [UserNhomController::class, 'create']);
Route::post('/user/nhom-cua-toi', [UserNhomController::class, 'store']);
Route::get('/user/nhom-cua-toi/{id}', [UserNhomController::class, 'show']);
/*
|--------------------------------------------------------------------------
| NHÓM
|--------------------------------------------------------------------------
*/

Route::get('/nhom/{idNhom}/dashboard', [NhomDashboardController::class, 'index']);
Route::get('/nhom/{idNhom}/dashboard/edit', [NhomDashboardController::class, 'edit']);
Route::patch('/nhom/{idNhom}/dashboard', [NhomDashboardController::class, 'update']);

Route::get('/nhom/{idNhom}/thanh-vien', [NhomThanhVienController::class, 'index']);
Route::get('/nhom/{idNhom}/thanh-vien/create', [NhomThanhVienController::class, 'create']);
Route::post('/nhom/{idNhom}/thanh-vien', [NhomThanhVienController::class, 'store']);
Route::delete('/nhom/{idNhom}/thanh-vien/{idThanhVien}', [NhomThanhVienController::class, 'destroy']);

Route::prefix('/nhom/{idNhom}/hang-hoa')->group(function () {
    Route::get('/', [NhomHangHoaController::class, 'index']);
    Route::get('/create', [NhomHangHoaController::class, 'create']);
    Route::post('/', [NhomHangHoaController::class, 'store']);
    Route::get('/{idHangHoa}/edit', [NhomHangHoaController::class, 'edit']);
    Route::put('/{idHangHoa}', [NhomHangHoaController::class, 'update']);
    Route::patch('/{idHangHoa}/doi-trang-thai', [NhomHangHoaController::class, 'doiTrangThai']);
});

Route::get('/nhom/{idNhom}/chien-dich', [NhomChienDichController::class, 'index']);
Route::get('/nhom/{idNhom}/chien-dich/create', [NhomChienDichController::class, 'create']);
Route::post('/nhom/{idNhom}/chien-dich', [NhomChienDichController::class, 'store']);
Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}', [NhomChienDichController::class, 'show']);
Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/edit', [NhomChienDichController::class, 'edit']);
Route::put('/nhom/{idNhom}/chien-dich/{idChienDich}', [NhomChienDichController::class, 'update']);

Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/cap-nhat/create', [NhomChienDichController::class, 'createCapNhat']);
Route::post('/nhom/{idNhom}/chien-dich/{idChienDich}/cap-nhat', [NhomChienDichController::class, 'storeCapNhat']);

Route::get('/nhom/{idNhom}/yeu-cau-cuu-tro', [NhomYeuCauCuuTroController::class, 'index']);
Route::get('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}', [NhomYeuCauCuuTroController::class, 'show']);
Route::get('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [NhomYeuCauCuuTroController::class, 'createTiepNhan']);
Route::post('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [NhomYeuCauCuuTroController::class, 'storeTiepNhan']);
Route::patch('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan/{idTiepNhan}/can-them-ho-tro', [NhomYeuCauCuuTroController::class, 'canThemHoTro']);
Route::patch('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan/{idTiepNhan}/thu-hoi-can-them-ho-tro', [NhomYeuCauCuuTroController::class, 'thuHoiCanThemHoTro']);
Route::patch('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan/{idTiepNhan}/ho-tro-nhom-dang-thieu', [NhomYeuCauCuuTroController::class, 'hoTroNhomDangThieu']);
Route::patch('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan/{idTiepNhan}/hoan-thanh', [NhomYeuCauCuuTroController::class, 'hoanThanhTiepNhan']);

Route::get('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tao-chien-dich', [NhomYeuCauCuuTroController::class, 'createChienDichTuYeuCau']);
Route::post('/nhom/{idNhom}/yeu-cau-cuu-tro/{idYeuCau}/tao-chien-dich', [NhomYeuCauCuuTroController::class, 'storeChienDichTuYeuCau']);

Route::patch('/nhom/{idNhom}/chien-dich/{idChienDich}/dong-gop/{idChiTietDongGop}/xac-nhan', [NhomChienDichController::class, 'xacNhanChiTietDongGop']);
Route::patch('/nhom/{idNhom}/chien-dich/{idChienDich}/dong-gop/{idChiTietDongGop}/tu-choi', [NhomChienDichController::class, 'tuChoiChiTietDongGop']);

Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/nguon-luc/cap-nhat', [NhomNguonLucChienDichController::class, 'edit']);
Route::put('/nhom/{idNhom}/chien-dich/{idChienDich}/nguon-luc/cap-nhat', [NhomNguonLucChienDichController::class, 'update']);

Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/phan-phoi/create', [NhomPhanPhoiController::class, 'create']);
Route::post('/nhom/{idNhom}/chien-dich/{idChienDich}/phan-phoi', [NhomPhanPhoiController::class, 'store']);
Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/phan-phoi/{idDotPhanPhoi}', [NhomPhanPhoiController::class, 'show']);
Route::get('/nhom/{idNhom}/chien-dich/{idChienDich}/phan-phoi/{idDotPhanPhoi}/edit', [NhomPhanPhoiController::class, 'edit']);
Route::put('/nhom/{idNhom}/chien-dich/{idChienDich}/phan-phoi/{idDotPhanPhoi}', [NhomPhanPhoiController::class, 'update']);
Route::delete('/nhom/{idNhom}/chien-dich/{idChienDich}/phan-phoi/{idDotPhanPhoi}', [NhomPhanPhoiController::class, 'destroy']);