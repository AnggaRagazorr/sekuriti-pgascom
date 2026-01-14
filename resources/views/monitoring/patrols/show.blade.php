<x-layouts.sekuriti :title="'Monitoring - Detail Patroli'" :header="'Detail Patroli'">

    <div class="bg-white rounded shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <div class="text-sm text-gray-500">User</div>
                <div class="font-semibold">{{ $row->user?->name ?? '-' }}</div>

                <div class="mt-3 text-sm text-gray-500">Waktu (Server)</div>
                <div class="font-mono">{{ $row->submitted_at?->format('Y-m-d H:i:s') }}</div>

                <div class="mt-3 text-sm text-gray-500">Area</div>
                <div class="font-semibold">{{ ucfirst($row->area) }}</div>

                <div class="mt-3 text-sm text-gray-500">Barcode</div>
                <div class="font-mono">{{ $row->barcode }}</div>

                <div class="mt-3 text-sm text-gray-500">Lokasi</div>
                <div class="whitespace-pre-line">{{ $row->address ?? '-' }}</div>

                <div class="mt-3 text-sm text-gray-500">Lat/Lng</div>
                <div class="font-mono">{{ $row->lat }}, {{ $row->lng }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500 mb-2">Foto</div>
                <img src="{{ asset('storage/'.$row->photo_path) }}"
                     class="w-full rounded border" alt="foto patroli">
            </div>

        </div>

        <div class="mt-6">
            <a href="{{ route('monitoring.patrols.index') }}"
               class="inline-block px-4 py-2 rounded bg-gray-900 text-white hover:bg-gray-800">
                Kembali
            </a>
        </div>
    </div>

</x-layouts.sekuriti>
