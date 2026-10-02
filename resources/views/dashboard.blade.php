<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg p-6">
                @if(Auth::user()->isGuru())
                    <div class="text-center py-6">
                        <h3 class="text-xl font-bold text-gray-900">Halo, Guru {{ Auth::user()->name }}!</h3>
                        <p class="text-sm text-gray-500 mt-1">Kelola bank soal, ujian untuk kelas, dan tinjau rekap nilai siswa.</p>
                        <div class="mt-6">
                            <a href="{{ route('guru.ujian.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold text-sm">
                                Buka Manajemen Ujian & Soal
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <h3 class="text-xl font-bold text-gray-900">Halo, Siswa {{ Auth::user()->name }}!</h3>
                        <p class="text-sm text-gray-500 mt-1">Lihat ujian aktif yang tersedia dan kerjakan soal ujian Anda.</p>
                        <div class="mt-6">
                            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold text-sm">
                                Buka Daftar Ujian Saya
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
