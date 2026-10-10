<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Mata Pelajaran yang Diampu') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pilih satu atau lebih mata pelajaran yang Anda ampu di sekolah untuk mengakses bank soal dan membuat ujian.') }}
        </p>
    </header>

    <form method="post" action="{{ route('guru.subjects.sync') }}" class="mt-6 space-y-4">
        @csrf

        <div class="space-y-2 max-h-80 overflow-y-auto pr-2">
            @foreach($subjects as $sub)
                @php
                    $isChecked = in_array($sub->id, $mySubjectIds ?? []);
                @endphp
                <label class="flex items-center gap-3 p-3 rounded-xl border {{ $isChecked ? 'border-indigo-400 bg-indigo-50/40' : 'border-gray-200 bg-white' }} hover:border-indigo-400 cursor-pointer transition">
                    <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}"
                        {{ $isChecked ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="text-sm font-medium text-gray-800 flex-1">{{ $sub->name }}</span>
                    @if($isChecked)
                        <span class="text-xs font-semibold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-full">
                            Aktif
                        </span>
                    @endif
                </label>
            @endforeach
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Simpan Mata Pelajaran') }}</x-primary-button>
        </div>
    </form>
</section>
