<x-layouts.sekuriti :title="'SEKURITI - Patroli'" :header="'Patroli'">

    {{-- : BLOK GLOBAL HASIL PATROLI KANTOR --}}
    <div class="bg-white rounded shadow p-4 mb-4 flex items-center justify-between">
        <div>
            <div class="text-sm text-gray-500">Hasil Patroli Kantor</div>
            <div id="reportStatus" class="font-semibold text-red-600">Belum diisi</div>
        </div>

        <button type="button"
                class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700"
                id="btnOpenReport">
            HASIL PATROLI KANTOR
        </button>
    </div>

    {{-- STEP 1: Foto Area --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        @php
            $areas = [
                'luar' => 'Area Luar',
                'smoking' => 'Area Smoking',
                'balkon' => 'Area Balkon',
            ];
        @endphp

        @foreach($areas as $key => $label)
            <div class="bg-white rounded shadow p-4">
                <h3 class="font-semibold mb-2">{{ $label }}</h3>

                {{-- Status barcode per area --}}
                <div class="text-xs text-gray-500">Barcode Area</div>
                <div class="flex items-center justify-between gap-2 mt-1">
                    <div class="font-mono text-sm" id="barcode-{{ $key }}">-</div>
                    <button
                        class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-sm"
                        data-scan-area="{{ $key }}">
                        Scan
                    </button>
                </div>

                <div class="border rounded h-40 flex items-center justify-center overflow-hidden bg-gray-50 mt-3">
                    <img id="preview-{{ $key }}" class="hidden w-full h-full object-cover" alt="preview {{ $label }}">
                    <span id="placeholder-{{ $key }}" class="text-gray-400">Belum ada foto</span>
                </div>

                <button
                    class="mt-3 w-full px-3 py-2 bg-gray-900 text-white rounded hover:bg-gray-800"
                    data-capture-area="{{ $key }}"
                    disabled>
                    Ambil Foto
                </button>

                <button
                    class="mt-2 w-full px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    data-submit-area="{{ $key }}"
                    disabled>
                    Submit
                </button>

                <div class="mt-2 text-xs text-gray-500">
                    Status: <span id="status-{{ $key }}">Belum</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modal Kamera --}}
    <div id="cameraModal" class="fixed inset-0 hidden items-center justify-center bg-black/60 p-4 z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl overflow-hidden">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <div class="font-semibold">Ambil Foto: <span id="modalAreaLabel">-</span></div>
                <button id="btnCloseModal" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">Tutup</button>
            </div>

            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded p-2">
                    <video id="captureVideo" class="w-full rounded" autoplay playsinline></video>
                </div>

                <div class="border rounded p-2">
                    <canvas id="captureCanvas" class="w-full rounded hidden"></canvas>
                    <div id="canvasPlaceholder" class="h-full flex items-center justify-center text-gray-400">
                        Hasil foto akan muncul di sini
                    </div>
                </div>
            </div>

            <div class="px-4 pb-4 flex flex-wrap gap-2">
                <button id="btnStartCamera" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    Nyalakan Kamera
                </button>
                <button id="btnTakePhoto" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-gray-800" disabled>
                    Ambil
                </button>
                <button id="btnRetake" class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-800" disabled>
                    Ulangi
                </button>
                <button id="btnUsePhoto" class="ml-auto px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700" disabled>
                    Pakai Foto
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Scan Barcode --}}
    <div id="scanModal" class="fixed inset-0 hidden items-center justify-center bg-black/60 p-4 z-50">
        <div class="bg-white rounded-lg w-full max-w-xl overflow-hidden">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <div class="font-semibold">Scan Barcode: <span id="scanAreaLabel">-</span></div>
                <button id="btnCloseScanModal" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">Tutup</button>
            </div>

            <div class="p-4">
                <div class="border rounded p-2">
                    <video id="scanVideo" class="w-full rounded" autoplay playsinline></video>
                </div>

                <div class="mt-3 text-sm text-gray-600">
                    Status: <span id="scanStatusText" class="font-semibold">Belum mulai</span>
                </div>

                <div class="mt-4 flex gap-2">
                    <button id="btnStartAreaScan" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                        Mulai Scan
                    </button>
                    <button id="btnStopAreaScan" class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-800" disabled>
                        Stop
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div id="reportModal" class="fixed inset-0 hidden items-center justify-center bg-black/60 p-4 z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl overflow-hidden">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <div class="font-semibold">Form Hasil Patroli Kantor</div>
                <button id="btnCloseReport" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">Tutup</button>
            </div>

            <div class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Hari/Tanggal (otomatis)</label>
                        <input id="reportDateText" class="mt-1 w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Waktu Submit (otomatis)</label>
                        <input id="reportTimeText" class="mt-1 w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-600">Situasi</label>
                    <textarea id="situasi" class="mt-1 w-full border rounded px-3 py-2" rows="3"></textarea>
                </div>

                <div>
                    <label class="text-sm text-gray-600">AGHT</label>
                    <textarea id="aght" class="mt-1 w-full border rounded px-3 py-2" rows="3"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Cuaca</label>
                        <input id="cuaca" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">PDAM</label>
                        <input id="pdam" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">PERSONEL WFO</label>
                        <input id="personel_wfo" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">PERSONEL TAMBAHAN</label>
                        <input id="personel_tambahan" class="mt-1 w-full border rounded px-3 py-2"
                               placeholder="Isi 0 jika tidak ada personel tambahan">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button id="btnSubmitReport" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-layouts.sekuriti>
