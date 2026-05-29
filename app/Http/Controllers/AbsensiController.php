<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;
use App\Models\User;
use App\Models\Absensi; 
use Illuminate\Support\Facades\Log; 

class AbsensiController extends Controller
{
    /**
     * @param  int  $mapelId ID mata pelajaran yang akan diabsen.
     * @param  \Illuminate\Http\Request  $request Objek permintaan HTTP.
     * @return \Illuminate\View\View
     */
    public function show($mapelId, Request $request)
    {
        // Mencari data mata pelajaran berdasarkan $mapelId. Jika tidak ditemukan, akan melempar 404.
        $mapel = Mapel::findOrFail($mapelId);
        // Mengambil semua pengguna dengan peran 'siswa'.
        $siswa = User::where('role', 'siswa')->get();

        // Mengambil nilai 'minggu' dari parameter query string (misal: ?minggu=2).
        // Jika parameter 'minggu' tidak ada, nilai defaultnya adalah 1.
        $minggu = $request->query('minggu', 1);

        // Mengambil data absensi yang sudah ada untuk mata pelajaran dan minggu yang spesifik.
        $absensiTersimpan = Absensi::where('id_mapel', $mapelId)
                                   ->where('minggu_ke', $minggu)
                                   ->get()
                                   ->keyBy('id_siswa'); // Mengubah koleksi menjadi array asosiatif dengan id_siswa sebagai kunci.

        // Menentukan tipe pengguna (dalam kasus ini 'guru') untuk keperluan view.
        $tipe = 'guru';
        // Mengembalikan view 'guru.absensi' dengan data yang diperlukan.
        return view('guru.absensi', compact('mapel', 'siswa', 'minggu', 'tipe', 'absensiTersimpan'));
    }

    /**
     * Menyimpan data absensi yang dikirim oleh guru.
     * @param  \Illuminate\Http\Request  $request Objek permintaan HTTP yang berisi data absensi.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi Input dari permintaan.
        $request->validate([
            'mapel_id' => ['required', 'integer', 'exists:mapel,id'], // ID mapel wajib, integer, dan harus ada di tabel 'mapel'.
            'minggu_ke' => ['required', 'integer', 'min:1', 'max:16'], // Minggu ke- wajib, integer, minimal 1, maksimal 16.
            'kehadiran' => ['required', 'array'], // 'kehadiran' wajib dan harus berupa array.
            // Setiap nilai dalam array 'kehadiran' harus salah satu dari 'hadir', 'izin', 'sakit', atau 'alpha'.
            'kehadiran.*' => ['required', 'string', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'array'], // 'keterangan' opsional dan bisa berupa array.
            'keterangan.*' => ['nullable', 'string', 'max:255'], // Setiap keterangan dalam array bersifat opsional, string, maks 255 karakter.
        ]);

        // Mengambil nilai mapel_id dari input permintaan.
        $mapelId = $request->input('mapel_id');
        // Mengambil nilai minggu_ke dari input permintaan.
        $minggu = $request->input('minggu_ke');
        // Mengambil array data kehadiran (format: [siswa_id => status_kehadiran]).
        $kehadiranData = $request->input('kehadiran');
        // Mengambil array data keterangan (format: [siswa_id => keterangan]).
        $keteranganData = $request->input('keterangan');

        Log::info('Absensi Store Request:', [
            'mapel_id' => $mapelId,
            'minggu_ke' => $minggu,
            'kehadiranData' => $kehadiranData,
            'keteranganData' => $keteranganData,
        ]);
        // --- Akhir Logging ---

        // Melakukan iterasi untuk setiap siswa dalam data kehadiran yang diterima.
        foreach ($kehadiranData as $siswaId => $status) {
            try {
                Absensi::updateOrCreate(
                    [
                        'id_siswa' => $siswaId, // Kriteria pencarian: ID Siswa
                        'id_mapel' => $mapelId, // Kriteria pencarian: ID Mata Pelajaran
                        'minggu_ke' => $minggu, // Kriteria pencarian: Minggu ke-
                    ],
                    [
                        'kehadiran' => $status,
                        'keterangan' => $keteranganData[$siswaId] ?? null,
                    ]
                );
                // Mencatat keberhasilan penyimpanan/pembaruan absensi untuk setiap siswa.
                Log::info("Absensi untuk Siswa ID: {$siswaId} berhasil disimpan/diperbarui dengan status: {$status}.");

            } catch (\Exception $e) {
                // Menangani kesalahan jika terjadi masalah saat menyimpan atau memperbarui absensi untuk siswa tertentu.
                Log::error("Gagal menyimpan/memperbarui absensi untuk Siswa ID: {$siswaId}. Error: " . $e->getMessage());
            }
        }
        return redirect()->route('guru.absensi.show', ['mapelId' => $mapelId, 'minggu' => $minggu])
                         ->with('success', 'Absensi berhasil disimpan.');
    }
}
