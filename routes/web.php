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
| Trang mặc định
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Xác thực tài khoản
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Hồ sơ cá nhân & thông báo
|--------------------------------------------------------------------------
*/

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

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    Route::resource('/dia-diem', DiaDiemController::class);
    Route::resource('/su-kien-cuu-tro', SuKienCuuTroController::class);
    Route::resource('/danh-muc-hang', DanhMucHangController::class);

    /*
    |--------------------------------------------------------------------------
    | Admin - Hàng hóa theo danh mục
    |--------------------------------------------------------------------------
    */

    Route::get('/danh-muc-hang/{idDanhMucHang}/hang-hoa', [HangHoaController::class, 'index']);
    Route::get('/danh-muc-hang/{idDanhMucHang}/hang-hoa/create', [HangHoaController::class, 'create']);
    Route::post('/danh-muc-hang/{idDanhMucHang}/hang-hoa', [HangHoaController::class, 'store']);
    Route::get('/hang-hoa/{idHangHoa}/edit', [HangHoaController::class, 'edit']);
    Route::put('/hang-hoa/{idHangHoa}', [HangHoaController::class, 'update']);
    Route::patch('/hang-hoa/{idHangHoa}/doi-trang-thai', [HangHoaController::class, 'doiTrangThai']);

    /*
    |--------------------------------------------------------------------------
    | Admin - Người dùng
    |--------------------------------------------------------------------------
    */

    Route::patch('/nguoi-dung/{id}/doi-trang-thai', [NguoiDungController::class, 'doiTrangThai']);
    Route::resource('/nguoi-dung', NguoiDungController::class);

    /*
    |--------------------------------------------------------------------------
    | Admin - Nhóm tình nguyện
    |--------------------------------------------------------------------------
    */

    Route::patch('/nhom-tinh-nguyen/{id}/duyet', [NhomTinhNguyenController::class, 'duyetNhom']);
    Route::patch('/nhom-tinh-nguyen/{id}/tu-choi', [NhomTinhNguyenController::class, 'tuChoiNhom']);
    Route::patch('/nhom-tinh-nguyen/{id}/doi-trang-thai', [NhomTinhNguyenController::class, 'doiTrangThai']);
    Route::resource('/nhom-tinh-nguyen', NhomTinhNguyenController::class);

    Route::get('/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'index']);
    Route::get('/nhom-tinh-nguyen/{idNhom}/thanh-vien/create', [ThanhVienNhomController::class, 'create']);
    Route::post('/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'store']);
    Route::delete('/thanh-vien-nhom/{idThanhVien}', [ThanhVienNhomController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Admin - Chiến dịch cứu trợ
    |--------------------------------------------------------------------------
    */

    Route::get('/chien-dich', [ChienDichCuuTroController::class, 'index']);
    Route::get('/chien-dich/{idChienDich}', [ChienDichCuuTroController::class, 'show']);
    Route::patch('/chien-dich/{idChienDich}/tam-ngung', [ChienDichCuuTroController::class, 'tamNgung']);
    Route::patch('/chien-dich/{idChienDich}/mo-lai', [ChienDichCuuTroController::class, 'moLai']);
    Route::patch('/chien-dich/{idChienDich}/huy', [ChienDichCuuTroController::class, 'huy']);

    /*
    |--------------------------------------------------------------------------
    | Admin - Yêu cầu cứu trợ
    |--------------------------------------------------------------------------
    */

    Route::get('/yeu-cau-cuu-tro', [YeuCauCuuTroController::class, 'index']);
    Route::get('/yeu-cau-cuu-tro/{id}', [YeuCauCuuTroController::class, 'show']);
    Route::patch('/yeu-cau-cuu-tro/{id}/huy', [YeuCauCuuTroController::class, 'huyYeuCau']);
});

/*
|--------------------------------------------------------------------------
| NGƯỜI DÙNG
|--------------------------------------------------------------------------
*/

Route::prefix('user')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | User - Nhóm tình nguyện
    |--------------------------------------------------------------------------
    */

    Route::get('/nhom-tinh-nguyen', [UserNhomTinhNguyenController::class, 'index']);
    Route::get('/nhom-tinh-nguyen/{idNhom}', [UserNhomTinhNguyenController::class, 'show']);

    Route::get('/nhom-cua-toi', [UserNhomController::class, 'index']);
    Route::get('/nhom-cua-toi/create', [UserNhomController::class, 'create']);
    Route::post('/nhom-cua-toi', [UserNhomController::class, 'store']);
    Route::get('/nhom-cua-toi/{id}', [UserNhomController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | User - Chiến dịch & yêu cầu cộng đồng
    |--------------------------------------------------------------------------
    */

    Route::get('/chien-dich', [UserChienDichController::class, 'index']);
    Route::get('/chien-dich/{idChienDich}', [UserChienDichController::class, 'show']);

    Route::get('/yeu-cau-cong-dong', [UserYeuCauCongDongController::class, 'index']);
    Route::get('/yeu-cau-cong-dong/{idYeuCau}', [UserYeuCauCongDongController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | User - Yêu cầu cứu trợ cá nhân
    |--------------------------------------------------------------------------
    */

    Route::get('/yeu-cau-cuu-tro', [UserYeuCauCuuTroController::class, 'index']);
    Route::get('/yeu-cau-cuu-tro/create', [UserYeuCauCuuTroController::class, 'create']);
    Route::post('/yeu-cau-cuu-tro', [UserYeuCauCuuTroController::class, 'store']);
    Route::get('/yeu-cau-cuu-tro/{idYeuCau}', [UserYeuCauCuuTroController::class, 'show']);
    Route::patch('/yeu-cau-cuu-tro/{idYeuCau}/huy', [UserYeuCauCuuTroController::class, 'huyYeuCau']);
    Route::patch('/yeu-cau-cuu-tro/{idYeuCau}/can-them-ho-tro', [UserYeuCauCuuTroController::class, 'canThemHoTro']);
    Route::patch('/yeu-cau-cuu-tro/{idYeuCau}/thu-hoi-can-them-ho-tro', [UserYeuCauCuuTroController::class, 'thuHoiCanThemHoTro']);
    Route::patch('/yeu-cau-cuu-tro/{idYeuCau}/xac-nhan-hoan-thanh', [UserYeuCauCuuTroController::class, 'xacNhanHoanThanh']);

    /*
    |--------------------------------------------------------------------------
    | User - Đóng góp
    |--------------------------------------------------------------------------
    */

    Route::get('/dong-gop', [UserDongGopController::class, 'index']);
    Route::get('/dong-gop/create', [UserDongGopController::class, 'create']);
    Route::post('/dong-gop', [UserDongGopController::class, 'store']);
    Route::get('/dong-gop/{idDongGop}', [UserDongGopController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| NHÓM TÌNH NGUYỆN
|--------------------------------------------------------------------------
*/

Route::prefix('nhom/{idNhom}')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Nhóm - Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [NhomDashboardController::class, 'index']);
    Route::get('/dashboard/edit', [NhomDashboardController::class, 'edit']);
    Route::patch('/dashboard', [NhomDashboardController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Nhóm - Thành viên
    |--------------------------------------------------------------------------
    */

    Route::get('/thanh-vien', [NhomThanhVienController::class, 'index']);
    Route::get('/thanh-vien/create', [NhomThanhVienController::class, 'create']);
    Route::post('/thanh-vien', [NhomThanhVienController::class, 'store']);
    Route::delete('/thanh-vien/{idThanhVien}', [NhomThanhVienController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Nhóm - Hàng hóa
    |--------------------------------------------------------------------------
    */

    Route::prefix('hang-hoa')->group(function () {
        Route::get('/', [NhomHangHoaController::class, 'index']);
        Route::get('/create', [NhomHangHoaController::class, 'create']);
        Route::post('/', [NhomHangHoaController::class, 'store']);
        Route::get('/{idHangHoa}/edit', [NhomHangHoaController::class, 'edit']);
        Route::put('/{idHangHoa}', [NhomHangHoaController::class, 'update']);
        Route::patch('/{idHangHoa}/doi-trang-thai', [NhomHangHoaController::class, 'doiTrangThai']);
    });

    /*
    |--------------------------------------------------------------------------
    | Nhóm - Chiến dịch
    |--------------------------------------------------------------------------
    */

    Route::get('/chien-dich', [NhomChienDichController::class, 'index']);
    Route::get('/chien-dich/create', [NhomChienDichController::class, 'create']);
    Route::post('/chien-dich', [NhomChienDichController::class, 'store']);
    Route::get('/chien-dich/{idChienDich}', [NhomChienDichController::class, 'show']);
    Route::get('/chien-dich/{idChienDich}/edit', [NhomChienDichController::class, 'edit']);
    Route::put('/chien-dich/{idChienDich}', [NhomChienDichController::class, 'update']);

    Route::get('/chien-dich/{idChienDich}/cap-nhat/create', [NhomChienDichController::class, 'createCapNhat']);
    Route::post('/chien-dich/{idChienDich}/cap-nhat', [NhomChienDichController::class, 'storeCapNhat']);

    Route::patch('/chien-dich/{idChienDich}/dong-gop/{idChiTietDongGop}/xac-nhan', [NhomChienDichController::class, 'xacNhanChiTietDongGop']);
    Route::patch('/chien-dich/{idChienDich}/dong-gop/{idChiTietDongGop}/tu-choi', [NhomChienDichController::class, 'tuChoiChiTietDongGop']);

    /*
    |--------------------------------------------------------------------------
    | Nhóm - Nguồn lực chiến dịch
    |--------------------------------------------------------------------------
    */

    Route::get('/chien-dich/{idChienDich}/nguon-luc/cap-nhat', [NhomNguonLucChienDichController::class, 'edit']);
    Route::put('/chien-dich/{idChienDich}/nguon-luc/cap-nhat', [NhomNguonLucChienDichController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Nhóm - Yêu cầu cứu trợ
    |--------------------------------------------------------------------------
    */

    Route::get('/yeu-cau-cuu-tro', [NhomYeuCauCuuTroController::class, 'index']);
    Route::get('/yeu-cau-cuu-tro/{idYeuCau}', [NhomYeuCauCuuTroController::class, 'show']);

    Route::get('/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [NhomYeuCauCuuTroController::class, 'createTiepNhan']);
    Route::post('/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [NhomYeuCauCuuTroController::class, 'storeTiepNhan']);

    Route::patch('/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan/{idTiepNhan}/can-them-ho-tro', [NhomYeuCauCuuTroController::class, 'canThemHoTro']);
    Route::patch('/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan/{idTiepNhan}/thu-hoi-can-them-ho-tro', [NhomYeuCauCuuTroController::class, 'thuHoiCanThemHoTro']);
    Route::patch('/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan/{idTiepNhan}/ho-tro-nhom-dang-thieu', [NhomYeuCauCuuTroController::class, 'hoTroNhomDangThieu']);
    Route::patch('/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan/{idTiepNhan}/hoan-thanh', [NhomYeuCauCuuTroController::class, 'hoanThanhTiepNhan']);

    Route::get('/yeu-cau-cuu-tro/{idYeuCau}/tao-chien-dich', [NhomYeuCauCuuTroController::class, 'createChienDichTuYeuCau']);
    Route::post('/yeu-cau-cuu-tro/{idYeuCau}/tao-chien-dich', [NhomYeuCauCuuTroController::class, 'storeChienDichTuYeuCau']);

    /*
    |--------------------------------------------------------------------------
    | Nhóm - Phân phối
    |--------------------------------------------------------------------------
    */

    Route::get('/chien-dich/{idChienDich}/phan-phoi/create', [NhomPhanPhoiController::class, 'create']);
    Route::post('/chien-dich/{idChienDich}/phan-phoi', [NhomPhanPhoiController::class, 'store']);
    Route::get('/chien-dich/{idChienDich}/phan-phoi/{idDotPhanPhoi}', [NhomPhanPhoiController::class, 'show']);
    Route::get('/chien-dich/{idChienDich}/phan-phoi/{idDotPhanPhoi}/edit', [NhomPhanPhoiController::class, 'edit']);
    Route::put('/chien-dich/{idChienDich}/phan-phoi/{idDotPhanPhoi}', [NhomPhanPhoiController::class, 'update']);
    Route::delete('/chien-dich/{idChienDich}/phan-phoi/{idDotPhanPhoi}', [NhomPhanPhoiController::class, 'destroy']);
});