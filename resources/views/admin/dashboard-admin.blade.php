@extends('layouts.app') {{-- Mengextends layout utama aplikasi --}}

@section('title', 'Dashboard') {{-- Menetapkan judul halaman --}}

@section('content') {{-- Memulai bagian konten --}}
    <style>
        /* Keyframes untuk animasi 'floatingFade' */
        @keyframes floatingFade {
            0% {
                transform: translateX(30px); /* Mulai dari posisi 30px ke kanan */
                opacity: 0.6; /* Opasitas awal */
            }

            25% {
                opacity: 1; /* Opasitas penuh pada 25% animasi */
            }

            50% {
                transform: translateX(-10px); /* Bergerak ke posisi -10px ke kiri */
                opacity: 2; /* Opasitas lebih tinggi dari 1 untuk efek 'terang' */
            }

            75% {
                opacity: 1; /* Kembali ke opasitas penuh */
            }

            100% {
                transform: translateX(0px); /* Kembali ke posisi awal */
                opacity: 0.6; /* Kembali ke opasitas awal */
            }
        }

        /* Menerapkan animasi ke elemen dengan kelas 'animate-floating-fade' */
        .animate-floating-fade {
            animation: floatingFade 15s ease-in-out infinite; /* Durasi 15s, easing ease-in-out, berulang tak terbatas */
        }
    </style>

    <!-- background animasi -->
    {{-- Div ini berfungsi sebagai kontainer untuk gambar latar belakang animasi --}}
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        {{-- Gambar latar belakang --}}
        <img src="{{ asset('images/10.png') }}" alt="Background"
            class="absolute w-full h-full object-cover opacity-5 animate-floating-fade" />
    </div>

    {{-- Konten utama dashboard --}}
    <div class="relative z-10 px-4 sm:px-6 lg:px-8 mt-3">
        <h1 class="text-2xl font-bold text-gray-800">HALAMAN UTAMA</h1>

        {{-- Bagian kartu statistik --}}
        <div class="relative z-10 flex justify-center mt-20">
            <div class="flex flex-wrap gap-10 pl-4 pr-4">
                {{-- Kartu Data Pendaftaran --}}
                <div
                    class="w-64 h-64 bg-[#1F1AA1] text-white rounded-2xl flex flex-col justify-center items-center shadow-md px-4 py-6 transition duration-300 ease-in-out transform hover:scale-105">
                    <h5 class="text-lg font-semibold mb-2">DATA PENDAFTARAN</h5>
                    {{-- Menampilkan jumlah total pengguna yang belum diverifikasi. Default ke 0 jika null. --}}
                    <p class="text-4xl font-bold mb-2">{{ $totalUnverifiedUsers ?? 0 }}</p>
                    <p class="text-sm mb-4 text-center">TOTAL PENDAFTARAN BARU</p> {{-- Ubah teks agar lebih jelas --}}
                    {{-- Link untuk melihat detail pendaftaran baru --}}
                    <a href="{{ route('admin.verifikasi') }}" {{-- Menggunakan route() helper untuk menghasilkan URL berdasarkan nama rute --}}
                        class="bg-white text-[#1F1AA1] text-sm font-semibold px-4 py-1 rounded-md hover:bg-gray-100 transition duration-300 ease-in-out transform hover:scale-105">
                        LIHAT
                    </a>
                </div>

                {{-- Kartu Data Siswa --}}
                <div
                    class="w-64 h-64 bg-[#1F1AA1] text-white rounded-2xl flex flex-col justify-center items-center shadow-md px-4 py-6 transition duration-300 ease-in-out transform hover:scale-105">
                    <h5 class="text-lg font-semibold mb-2">DATA SISWA</h5>
                    {{-- Menampilkan jumlah total siswa. Default ke 0 jika null. --}}
                    <p class="text-4xl font-bold mb-2">{{ $totalSiswa ?? 0 }}</p>
                    <p class="text-sm mb-4 text-center">TOTAL AKUN SISWA</p>
                    {{-- Link untuk melihat daftar siswa --}}
                    <a href="{{ route('admin.profile_siswa') }}" {{-- Menggunakan route() helper dan nama rute yang benar --}}
                        class="bg-white text-[#1F1AA1] text-sm font-semibold px-4 py-1 rounded-md hover:bg-gray-100 transition duration-300 ease-in-out transform hover:scale-105">
                        LIHAT
                    </a>
                </div>

                {{-- Kartu Data Guru --}}
                <div
                    class="w-64 h-64 bg-[#1F1AA1] text-white rounded-2xl flex flex-col justify-center items-center shadow-md px-4 py-6 transition duration-300 ease-in-out transform hover:scale-105">
                    <h5 class="text-lg font-semibold mb-2">DATA GURU</h5>
                    {{-- Menampilkan jumlah total guru. Default ke 0 jika null. --}}
                    <p class="text-4xl font-bold mb-2">{{ $totalGuru ?? 0 }}</p>
                    <p class="text-sm mb-4 text-center">TOTAL AKUN GURU</p>
                    {{-- Link untuk melihat daftar guru --}}
                    <a href="{{ route('admin.profile_guru') }}" {{-- Menggunakan route() helper dan nama rute yang benar --}}
                        class="bg-white text-[#1F1AA1] text-sm font-semibold px-4 py-1 rounded-md hover:bg-gray-100 transition duration-300 ease-in-out transform hover:scale-105">
                        LIHAT
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection {{-- Mengakhiri bagian konten --}}
