import { BrowserMultiFormatReader } from "@zxing/browser";

let codeReader = null;
let controls = null;

let activeAreaKey = null;

// simpan barcode per area
const areaBarcodes = new Map(); // key -> barcode

export function getAreaBarcodes() {
  return areaBarcodes;
}

export function initAreaBarcodeScanner() {
  const modal = document.getElementById("scanModal");
  const video = document.getElementById("scanVideo");
  const btnClose = document.getElementById("btnCloseScanModal");
  const btnStart = document.getElementById("btnStartAreaScan");
  const btnStop = document.getElementById("btnStopAreaScan");
  const label = document.getElementById("scanAreaLabel");
  const statusText = document.getElementById("scanStatusText");

  if (!modal || !video || !btnStart || !btnClose) return;

  const setStatus = (t) => (statusText.textContent = t);

  // tombol "Scan" per area (di kartu)
  document.querySelectorAll("[data-scan-area]").forEach((btn) => {
    btn.addEventListener("click", () => {
      activeAreaKey = btn.getAttribute("data-scan-area");
      label.textContent = labelForArea(activeAreaKey);
      setStatus("Belum mulai");
      show(modal);

      // reset tombol
      btnStart.disabled = false;
      btnStop.disabled = true;
    });
  });

  btnClose.addEventListener("click", async () => {
    await stop(video);
    hide(modal);
  });

  btnStart.addEventListener("click", async () => {
    try {
      btnStart.disabled = true;
      btnStop.disabled = false;

      setStatus("Meminta akses kamera...");

      codeReader = new BrowserMultiFormatReader();
      const devices = await BrowserMultiFormatReader.listVideoInputDevices();
      const preferred =
        devices.find((d) => /back|rear|environment/i.test(d.label)) || devices[0];

      if (!preferred) {
        setStatus("Kamera tidak ditemukan");
        btnStart.disabled = false;
        btnStop.disabled = true;
        return;
      }

      setStatus("Kamera aktif. Arahkan barcode...");

      controls = await codeReader.decodeFromVideoDevice(
        preferred.deviceId,
        video,
        async (result) => {
          if (!result) return;

          const code = result.getText();
          areaBarcodes.set(activeAreaKey, code);

          // tampilkan barcode di kartu
          const el = document.getElementById(`barcode-${activeAreaKey}`);
          if (el) el.textContent = code;

          // aktifkan tombol ambil foto untuk area ini
          const captureBtn = document.querySelector(`[data-capture-area="${activeAreaKey}"]`);
          if (captureBtn) captureBtn.disabled = false;

          setStatus("Barcode terbaca ✅");
          await stop(video);
          hide(modal);
        }
      );
    } catch (e) {
      console.error(e);
      setStatus("Gagal akses kamera / permission");
      btnStart.disabled = false;
      btnStop.disabled = true;
    }
  });

  btnStop.addEventListener("click", async () => {
    await stop(video);
    btnStart.disabled = false;
    btnStop.disabled = true;
    setStatus("Scanner berhenti");
  });
}

function labelForArea(key) {
  if (key === "luar") return "Area Luar";
  if (key === "smoking") return "Area Smoking";
  if (key === "balkon") return "Area Balkon";
  return key || "-";
}

function show(modal) {
  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function hide(modal) {
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

async function stop(video) {
  try {
    if (controls) {
      controls.stop();
      controls = null;
    }
    if (codeReader) {
      codeReader = null;
    }
    if (video) {
      video.pause();
      video.srcObject = null;
    }
  } catch (e) {
    console.error(e);
  }
}
