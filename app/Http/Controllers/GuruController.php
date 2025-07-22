<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Mengimpor kelas Request untuk menangani permintaan HTTP
use App\Models\User; // Mengimpor model User untuk berinteraksi dengan data pengguna (guru)
use App\Models\Mapel; // Mengimpor model Mapel untuk berinteraksi dengan data mata pelajaran

class GuruController extends Controller
{
    /**
     * Menampilkan daftar guru yang sudah diverifikasi untuk admin.
     * Metode ini juga mendukung fungsionalitas pencarian berdasarkan ID kustom atau nama guru.
     *
     * @param  \Illuminate\Http\Request  $request Objek permintaan HTTP yang mungkin berisi parameter pencarian.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Memulai query untuk mengambil pengguna dengan peran 'guru' yang sudah diverifikasi.
        $query = User::where('role', 'guru')
                     ->where('is_verified', true); // Hanya guru yang sudah diverifikasi yang akan ditampilkan.

        // Logika pencarian berdasarkan ID kustom atau nama guru.
        // Memeriksa apakah parameter 'search' ada dalam permintaan dan tidak kosong.
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search; // Mengambil nilai pencarian.
            // Menambahkan kondisi WHERE untuk mencari di kolom 'custom_identifier' atau 'name'.
            $query->where(function ($q) use ($search) {
                $q->where('custom_identifier', 'like', "%{$search}%") // Mencari ID kustom yang mengandung string pencarian.
                  ->orWhere('name', 'like', "%{$search}%"); // Atau mencari nama yang mengandung string pencarian.
            });
        }

        // Mengambil data guru dengan paginasi (10 item per halaman) dan eager loading relasi 'mapel'.
        $dataGuru = $query->with('mapel')->paginate(10); // 'with('mapel')' akan memuat data mata pelajaran terkait untuk setiap guru.
        // Mengambil semua data mata pelajaran untuk digunakan dalam dropdown atau daftar.
        $mapelList = Mapel::all();
        // Menentukan tipe pengguna sebagai 'admin' untuk keperluan view.
        $tipe = 'admin';

        // Mengembalikan view 'admin.profile_guru' dengan data guru, daftar mata pelajaran, dan tipe.
        return view('admin.profile_guru', compact('dataGuru', 'mapelList', 'tipe'));
    }

    /**
     * Admin mengatur mata pelajaran yang diajarkan oleh guru.
     * Metode ini memungkinkan admin untuk mengaitkan seorang guru dengan mata pelajaran tertentu.
     *
     * @param  \Illuminate\Http\Request  $request Objek permintaan HTTP yang berisi ID mata pelajaran.
     * @param  int  $id ID pengguna (guru) yang akan diatur mata pelajarannya.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function aturMapel(Request $request, $id)
    {
        // Validasi input 'mapel_id'.
        // Memastikan 'mapel_id' bersifat nullable (bisa kosong) dan jika ada, harus ada di tabel 'mapel'.
        $request->validate([
            'mapel_id' => 'nullable|exists:mapel,id',
        ], [
            'mapel_id.exists' => 'Mata pelajaran yang dipilih tidak valid.' // Pesan kustom jika validasi 'exists' gagal.
        ]);

        // Mencari guru berdasarkan ID. Jika tidak ditemukan, akan melempar 404.
        $guru = User::findOrFail($id);

        // Memastikan hanya pengguna dengan peran 'guru' yang bisa diubah mata pelajarannya.
        if ($guru->role !== 'guru') {
            return redirect()->back()->with('error', 'User yang Anda coba perbarui bukan seorang guru.');
        }

        // Memperbarui kolom 'mapel_id' pada model guru dengan nilai dari permintaan.
        $guru->mapel_id = $request->mapel_id;
        // Menyimpan perubahan ke database.
        $guru->save();

        // Mengarahkan kembali ke halaman sebelumnya dengan pesan sukses.
        return redirect()->back()->with('success', 'Mata pelajaran guru berhasil diperbarui.');
    }

    /**
     * Admin menghapus akun guru.
     * Metode ini memungkinkan admin untuk menghapus akun guru dari sistem.
     *
     * @param  int  $id ID pengguna (guru) yang akan dihapus.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Mencari guru berdasarkan ID dan memastikan perannya adalah 'guru'.
        // Jika tidak ditemukan atau perannya bukan guru, akan melempar 404.
        $guru = User::where('role', 'guru')->findOrFail($id);

        // Menghapus record guru dari database.
        $guru->delete();

        // Mengarahkan kembali ke halaman sebelumnya dengan pesan sukses.
        return redirect()->back()->with('success', 'Akun guru berhasil dihapus.');
    }
    public function profil($id)
{
    $guru = User::where('role', 'guru')->where('custom_identifier', $id)->firstOrFail();
    return view('guru.profile', compact('guru'));
}

}
