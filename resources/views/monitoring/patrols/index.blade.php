<x-layouts.sekuriti :title="'Monitoring - Patrol Logs'" :header="'Monitoring Patroli'">

    <div class="bg-white rounded shadow p-4 mb-4 flex items-center justify-between">
        <div>
            <div class="text-sm text-gray-500">Waktu sekarang</div>
            <div id="liveClock" class="text-xl font-mono font-semibold">--</div>
        </div>

        <div class="text-sm text-gray-500">
            Timezone: <span class="font-mono">{{ config('app.timezone') }}</span>
        </div>
    </div>

    {{-- UBAH BAGIAN FORM INI --}}
    <div class="bg-white rounded shadow p-4 mb-4">
        <form method="GET" class="flex flex-col md:flex-row gap-3 md:items-end">
            <div>
                <label class="text-sm text-gray-600">Tanggal</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="border rounded px-3 py-2 w-full">
            </div>

            {{-- TAMBAHKAN BAGIAN INI --}}
            <div>
                <label class="text-sm text-gray-600">Security</label>
                <select name="user_id" class="border rounded px-3 py-2 w-full">
                    <option value="">Semua Security</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                                {{ $userId = $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- SAMPAI SINI --}}

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
                    Filter
                </button>
                <a href="{{ route('monitoring.patrols.index') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
            <tr class="text-left">
                <th class="p-3">Waktu (Server)</th>
                <th class="p-3">User</th>
                <th class="p-3">Area</th>
                <th class="p-3">Barcode</th>
                <th class="p-3">Alamat</th>
                <th class="p-3">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $r)
                <tr class="border-t">
                    <td class="p-3">{{ $r->submitted_at?->format('Y-m-d H:i:s') }}</td>
                    <td class="p-3">{{ $r->user?->name ?? '-' }}</td>
                    <td class="p-3">{{ ucfirst($r->area) }}</td>
                    <td class="p-3 font-mono">{{ $r->barcode }}</td>
                    <td class="p-3 whitespace-pre-line text-gray-700">
                        {{ $r->address ?? '-' }}
                    </td>
                    <td class="p-3">
                        <a href="{{ route('monitoring.patrols.show', $r->id) }}"
                           class="text-blue-600 hover:underline">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="p-3 text-gray-500" colspan="6">Belum ada data patroli di tanggal ini.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $rows->links() }}
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('liveClock');
  if (!el) return;

  const tick = () => {
    const now = new Date();
    el.textContent = now.toLocaleString('id-ID');
  };

  tick();
  setInterval(tick, 1000);
});
</script>

</x-layouts.sekuriti>