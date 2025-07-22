<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Diperlukan untuk Auth::routes() atau rute logout

// Pindahkan semua use statements ke bagian paling atas
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileeController; // Pertimbangkan untuk menggabungkan dengan ProfileController
use App\Http\Controllers\DaftarHadirController;
use App\Http\Controllers\DaftarNilaiController;
use App\Http\Controllers\KursusGuruController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\MateriController; // Ini adalah satu-satunya deklarasi use untuk MateriController
use App\Http\Controllers\KursussiswaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController; // Ditambahkan
use App\Http\Controllers\Auth\PasswordController; // Ditambahkan
use App\Http\Controllers\PengumpulanTugasController; // Ditambahkan dari blok pertama

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Rute Umum / Landing Page ---
Route::get('/', function () {
    return view('welcome');
});

Route::get('/daftar_hadir', function () {
    return view('daftar_hadir');
})->name('daftar_hadir');

Route::get('/profil_siswa', function () {
    return view('profil_siswa');
})->name('profil_siswa');

Route::middleware(['auth', 'role:siswa'])->group(function () {
    return view('siswa.daftar_nilai');
})->name('nilai_siswa');

Route::get('/kursus_siswa', function () {
    return view('kursus_siswa');
})->name('kursus_siswa');

// --- Rute Admin (Tanpa Auth Middleware Awal) ---
// Dashboard Admin
Route::get('/admin.dashboard-admin', [DashboardController::class, 'index'])->name('dashboard'); // Pertimbangkan untuk mengubah nama menjadi 'admin.dashboard' dan URI ke /admin/dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard'); // Ini duplikat dari yang di atas, pilih salah satu.

// Profil Guru (dari sisi Admin)
Route::get('/admin.profile_guru', [GuruController::class, 'index'])->name('guru.index'); // Nama rute 'guru.index' mungkin kurang jelas untuk tampilan admin
Route::get('/admin.profile_guru', [GuruController::class, 'index'])->name('admin.profile_guru'); // Ini duplikat dari yang di atas, pilih salah satu.
Route::put('/admin/guru/{id}/atur-mapel', [GuruController::class, 'aturMapel'])->name('admin.guru.aturMapel');
Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('admin.guru.destroy'); // Menghapus guru

Route::get('/admin.profile_siswa', [SiswaController::class, 'index'])->name('admin.profile_siswa');
Route::delete('/admin/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('admin.siswa.destroy');

// Guru Mata Pelajaran (dari sisi Admin) - dari blok kedua
Route::get('/admin.guru_mapel', function () {
    $tipe = 'admin';
    return view('admin.guru_mapel', compact('tipe'));
});


// --- Rute Autentikasi (Pendaftaran, Login, Logout) ---
Route::get('/daftar', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/daftar', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// --- Rute yang Membutuhkan Autentikasi (Middleware 'auth') ---
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Ganti Password (Untuk semua peran yang terautentikasi)
    Route::get('/ganti_sandi', [PasswordController::class, 'showChangePasswordForm']);
    Route::post('/ganti_sandi', [PasswordController::class, 'changePassword']);

    // --- Rute Admin (Membutuhkan Auth) ---
    // Verifikasi Pengguna (Guru dan Siswa)
    Route::get('/admin.verifikasi', [AdminController::class, 'verifikasi'])->name('admin.verifikasi');
    Route::post('/verify-user/{userId}', [AdminController::class, 'verifyUser'])->name('admin.verify-user');
    Route::post('/admin/verifikasi-semua', [AdminController::class, 'verifikasiSemua'])->name('admin.verifikasi.semua');

    // --- Rute Guru (Membutuhkan Auth) ---
    // Profil Guru (dari sisi Guru itu sendiri)
    Route::get('/guru.profile', [ProfileController::class, 'showProfile'])->name('guru.profile'); // Menggunakan nama 'guru.profile'
    Route::put('/guru/profile/{id}', [ProfileController::class, 'updateProfile'])->name('profile.update'); // Update profil umum (dari blok pertama)
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update'); // Update profil umum (dari blok kedua, akan menimpa rute dengan nama yang sama)

   // Route Guru
Route::post('/guru/materi', [MateriController::class, 'store'])->name('guru.materi.store');
Route::get('/guru/pengumpulan', [PengumpulanTugasController::class, 'rekapGuru'])->name('guru.pengumpulan');
// Route GET untuk edit
Route::get('/tugas/{id}/edit', [TugasController::class, 'edit'])->name('guru.tugas.edit');

// Route PUT untuk update nilai
Route::put('/tugas/{id}', [TugasController::class, 'update'])->name('guru.tugas.update');


Route::delete('/guru/materi/{id}', [MateriController::class, 'destroy'])->name('guru.materi.destroy');
Route::get('/guru/materi/download/{id}', [MateriController::class, 'download'])->name('guru.materi.download');

Route::delete('/guru/tugas/{id}', [TugasController::class, 'destroy'])->name('guru.tugas.destroy');
Route::get('/guru/tugas/download/{id}', [TugasController::class, 'download'])->name('guru.tugas.download');

    Route::get('/guru.kursus', [KursusGuruController::class, 'index'])->name('guru.kursus');
    // Absensi
    Route::middleware(['auth'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/absensi', action: [AbsensiController::class, 'index'])->name('absensi');
        Route::get('/absensi/{mapelId}', [AbsensiController::class, 'show'])->name('absensi.show'); // <== YANG INI
        Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    });

    // Penilaian
Route::get('/guru/penilaian/{mapelId}', [PenilaianController::class, 'index'])->name('penilaian.index');
Route::post('/guru/penilaian/simpan', [PenilaianController::class, 'simpan'])->name('penilaian.simpan');
Route::post('/guru/penilaian/store', [PenilaianController::class, 'store'])->name('penilaian.store');
// Untuk edit nilai dari rekap tugas
Route::get('/guru/penilaian/{siswa_id}/{tugas_id}/{minggu}', [PenilaianController::class, 'edit'])->name('edit_tugas');


    // Tugas (untuk guru menambah tugas)
    Route::post('/guru/tugas', [App\Http\Controllers\TugasController::class, 'store'])->name('guru.tugas.store'); // dari blok pertama
    Route::post('/guru.modal_tambah_tugas', [TugasController::class, 'index'])->name('tugas.store'); // dari blok kedua (Index biasanya untuk GET, pertimbangkan method 'store')

    // --- Rute Siswa (Membutuhkan Auth) ---
    // Profil Siswa (dari sisi Siswa itu sendiri)
    // Pertimbangkan untuk menggabungkan ProfileeController ke ProfileController
    Route::get('/siswa.profile', [ProfileeController::class, 'index'])->name('siswa.profile');
    Route::post('/siswa.profile', [ProfileeController::class, 'update'])->name('siswa.profile.update');

    // Daftar Hadir Siswa
    Route::get('/siswa.daftar_hadir', [DaftarHadirController::class, 'index'])->name('daftar_hadir.index');

    // Daftar Nilai Siswa
    Route::get('/siswa.daftar_nilai', [DaftarNilaiController::class, 'index'])->name('daftar_nilai.index');

    // Kursus Siswa (view)
    Route::get('/siswa.kursus', [KursusSiswaController::class, 'index'])->name('siswa.kursus.index');
     Route::get('/siswa.pengumpulan_tugas', [PengumpulanTugasController::class, 'index'])->name('pengumpulan_tugas');
    // Rute POST untuk menyimpan file tugas yang diunggah
    Route::post('/siswa.pengumpulan_tugas', [PengumpulanTugasController::class, 'store'])->name('siswa.pengumpulan_tugas.store');
    Route::delete('/pengumpulan/{id}', [PengumpulanTugasController::class, 'destroy'])->name('pengumpulan.destroy');

    Route::get('/siswa/profile/{id}', [SiswaController::class, 'profil']);
Route::get('/guru/profile/{id}', [GuruController::class, 'profil']);
});

