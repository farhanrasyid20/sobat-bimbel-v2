<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-yzK+xOuhvEfrF9D3MGoUczFayVmEr0mTwqkqMZWTtfJLPbOP+6FfqDloTfByywvqlwDZcKQhOj9MYj8+1qJZPQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @keyframes floatingFade {
            0% {
                transform: translateX(0);
                opacity: 0.5;
            }

            25% {
                opacity: 1.5;
            }

            50% {
                transform: translateX(0);
                opacity: 2;
            }

            75% {
                opacity: 1.5;
            }

            100% {
                transform: translateX(0);
                opacity: 0.5;
            }
        }

        .animate-floating-fade {
            animation: floatingFade 15s ease-in-out infinite;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-transparent">
    {{-- Background --}}
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <img src="{{ asset('images/8.png') }}" alt="Background"
            class="absolute w-full h-full object-cover opacity-5 animate-floating-fade" />
    </div>

    <div class="relative z-10 text-white p-12 rounded-3xl w-full max-w-md bg-transparent">
        @php
            $user = Auth::user();
            $id = $user->custom_identifier;
            $role = $user->role;
        @endphp

        <h2 class="text-3xl font-bold text-center mb-6 text-blue-900">GANTI KATA SANDI</h2>

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div class="text-green-600 text-center mb-4 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tampilkan error --}}
        @if ($errors->any())
            <div class="text-red-600 text-sm mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li class="mb-1">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/ganti_sandi" method="POST">
            @csrf

            <!-- Kata Sandi Lama -->
            <div class="relative mb-6">
                <label for="current_password" class="block text-gray-700 text-base font-medium mb-2">Kata Sandi
                    Lama</label>
                <input type="password" id="current_password" name="current_password" placeholder="Kata Sandi Lama"
                    class="w-full pl-10 pr-4 py-3 rounded-full text-gray-700 placeholder-gray-400 bg-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105"
                    required />
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
            </div>

            <!-- Kata Sandi Baru -->
            <div class="relative mb-6">
                <label for="password" class="block text-gray-700 text-base font-medium mb-2">Kata Sandi Baru</label>
                <input type="password" id="password" name="password" placeholder="Kata Sandi Baru"
                    class="w-full pl-10 pr-4 py-3 rounded-full text-gray-700 placeholder-gray-400 bg-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105"
                    required />
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
            </div>

            <!-- Konfirmasi Kata Sandi Baru -->
            <div class="relative mb-6">
                <label for="password_confirmation" class="block text-gray-700 text-base font-medium mb-2">Konfirmasi
                    Kata Sandi Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="Konfirmasi Kata Sandi Baru"
                    class="w-full pl-10 pr-4 py-3 rounded-full text-gray-700 placeholder-gray-400 bg-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105"
                    required />
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
            </div>

            <p class="text-gray-700 text-sm italic text-center mb-3">*Kata sandi minimal 8 karakter & berbeda dari
                sebelumnya</p>

            <a href="{{ $role === 'siswa' ? route('siswa.profile', $id) : route('guru.profile', $id) }}"
                class=" inline-block w-full text-center bg-gray-400 text-white font-bold py-1.5 px-3 rounded-xl hover:bg-gray-500 transition duration-300 text-sm ease-in-out transform hover:scale-105">
                KEMBALI KE PROFIL
            </a>

            <!-- Tombol Ganti -->
            <button type="submit"
                class="mt-3 w-full bg-blue-800 text-white font-bold py-1.5 px-3 rounded-xl hover:bg-blue-900 transition duration-300 mx-auto block text-sm ease-in-out transform hover:scale-105">
                GANTI KATA SANDI
            </button>

        </form>
    </div>
</body>

</html>