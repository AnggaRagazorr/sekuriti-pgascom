<x-layouts.sekuriti :title="'SEKURITI - Dashboard Security'" :header="'Dashboard Security'">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">Patroli Hari Ini</div>
            <div class="text-2xl font-bold">0/3</div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">Rekap Hari Ini</div>
            <div class="text-2xl font-bold">0</div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">Mobil Tersedia</div>
            <div class="text-2xl font-bold">0</div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <div class="text-sm text-gray-500">Dokumen Hari Ini</div>
            <div class="text-2xl font-bold">0</div>
        </div>
    </div>

    <div class="mt-6 bg-white rounded shadow p-4">
        <div class="font-semibold mb-2">Aktivitas Terakhir</div>
        <div class="text-gray-500 text-sm">Belum ada aktivitas.</div>
    </div>
</x-layouts.sekuriti>
