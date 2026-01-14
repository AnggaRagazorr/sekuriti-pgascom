import { isReportReady } from "./report-form";

let activeAreaKey = null;
let stream = null;

const areaPhotos = new Map(); 

export function getAreaPhotos() {
  return areaPhotos;
}

export function initPatrolCameraCapture() {
  const modal = document.getElementById("cameraModal");
  const btnClose = document.getElementById("btnCloseModal");
  const video = document.getElementById("captureVideo");
  const canvas = document.getElementById("captureCanvas");
  const canvasPlaceholder = document.getElementById("canvasPlaceholder");
  const modalAreaLabel = document.getElementById("modalAreaLabel");

  const btnStartCamera = document.getElementById("btnStartCamera");
  const btnTakePhoto = document.getElementById("btnTakePhoto");
  const btnRetake = document.getElementById("btnRetake");
  const btnUsePhoto = document.getElementById("btnUsePhoto");

  if (!modal || !video || !canvas) return;

  document.querySelectorAll("[data-capture-area]").forEach((btn) => {
    btn.addEventListener("click", () => {
      activeAreaKey = btn.getAttribute("data-capture-area");
      modalAreaLabel.textContent = labelForArea(activeAreaKey);

      
      resetCanvas(canvas, canvasPlaceholder);
      btnTakePhoto.disabled = true;
      btnRetake.disabled = true;
      btnUsePhoto.disabled = true;

      showModal(modal);
    });
  });

  btnClose.addEventListener("click", async () => {
    await stopCamera(video);
    hideModal(modal);
  });

  // mulai kamera
  btnStartCamera.addEventListener("click", async () => {
    try {
      await startCamera(video);
      btnTakePhoto.disabled = false;
    } catch (e) {
      console.error(e);
      alert("Gagal akses kamera. Pastikan permission kamera diizinkan.");
    }
  });

  // ambil foto
  btnTakePhoto.addEventListener("click", () => {
    if (!stream) return;

    const w = video.videoWidth || 1280;
    const h = video.videoHeight || 720;

    canvas.width = w;
    canvas.height = h;

    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, w, h);

    canvas.classList.remove("hidden");
    canvasPlaceholder.classList.add("hidden");

    btnRetake.disabled = false;
    btnUsePhoto.disabled = false;
  });

  // ulang
  btnRetake.addEventListener("click", () => {
    resetCanvas(canvas, canvasPlaceholder);
    btnUsePhoto.disabled = true;
    btnRetake.disabled = true;
  });

  btnUsePhoto.addEventListener("click", async () => {
  if (!activeAreaKey) return;

  // 1) Ambil GPS
  const geo = await getGeolocation();

  // 2) Reverse geocode via backend Laravel (Nominatim)
  let addressLines = [];
  if (geo) {
    try {
      const resp = await reverseGeocode(geo.lat, geo.lng);
      addressLines = resp?.lines || [];
    } catch (e) {
      console.error(e);
    }
  }

  // 3) Susun teks watermark (alamat multi-line + area + waktu)
  const now = new Date();
  const timeStr = now.toLocaleString("id-ID");
  const areaLabel = labelForArea(activeAreaKey);

  const finalLines = [
    ...(addressLines.length ? addressLines : ["Lokasi tidak tersedia"]),
    `Area: ${areaLabel}`,
    timeStr,
  ];

  // 4) Tempel watermark ke canvas (pojok kanan bawah)
  drawWatermark(canvas, finalLines);

  // 5) Baru convert jadi blob (foto sudah ada watermark)
  const blob = await canvasToBlob(canvas);
  if (!blob) {
    alert("Gagal membuat foto. Coba ulangi.");
    return;
  }

  // simpan blob+url per area
  const prev = areaPhotos.get(activeAreaKey);
  if (prev?.url) URL.revokeObjectURL(prev.url);

  const url = URL.createObjectURL(blob);
  areaPhotos.set(activeAreaKey, {
  blob,
  url,
  geo: geo || null,
  addressLines: addressLines || [],
});

  // update preview
  const img = document.getElementById(`preview-${activeAreaKey}`);
  const placeholder = document.getElementById(`placeholder-${activeAreaKey}`);
  const status = document.getElementById(`status-${activeAreaKey}`);

  if (img && placeholder && status) {
    img.src = url;
    img.classList.remove("hidden");
    placeholder.classList.add("hidden");
    status.textContent = "Foto siap ✅";
  }

  // enable submit button
  const submitBtn = document.querySelector(
    `[data-submit-area="${activeAreaKey}"]`
  );
  if (submitBtn) submitBtn.disabled = false;

  await stopCamera(video);
  hideModal(modal);
});

document.querySelectorAll("[data-submit-area]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const area = btn.getAttribute("data-submit-area");
      const payload = areaPhotos.get(area);

      if (!payload?.blob) {
        alert("Foto belum ada. Ambil foto dulu.");
        return;
      }

      // barcode dari hasil scan
      const barcode = document.getElementById(`barcode-${area}`)?.textContent?.trim();
      if (!barcode || barcode === "-") {
        alert("Barcode area belum discan. Scan dulu area ini.");
      return;
      }

      btn.disabled = true;
      const originalText = btn.textContent;
      btn.textContent = "Mengirim...";

      if (!isReportReady()) {
        alert('Isi dulu "HASIL PATROLI KANTOR" sebelum submit area.');
        btn.disabled = false;
        btn.textContent = originalText;
        return;
        
  }

      try {
        const fd = new FormData();
        fd.append("area", area);
        fd.append("barcode", barcode);
        const now = new Date();
        const pad = (n) => String(n).padStart(2, "0");

        const realtime = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

        fd.append("submitted_at", realtime);

        if (payload.geo?.lat) fd.append("lat", payload.geo.lat);
        if (payload.geo?.lng) fd.append("lng", payload.geo.lng);

        (payload.addressLines || []).forEach((line, idx) => {
          fd.append(`address_lines[${idx}]`, line);
        });

        fd.append("photo", payload.blob, `patrol-${area}.jpg`);

        const token = document
          .querySelector('meta[name="csrf-token"]')
          ?.getAttribute("content");

        const res = await fetch("/security/patrol/submit", {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": token || "",
            Accept: "application/json",
          },
          body: fd,
        });

        
        const text = await res.text();
        let json = null;
        try {
          json = JSON.parse(text);
        } catch {
          console.error("Response bukan JSON:", text);
          throw new Error("Response bukan JSON");
        }

        if (!json.ok) {
          console.error(json);
          alert("Gagal submit. Cek console.");
          btn.disabled = false;
          btn.textContent = originalText;
          return;
        }

        // update status UI
        const status = document.getElementById(`status-${area}`);
        if (status) status.textContent = `Terkirim ✅ (${json.submitted_at})`;

        btn.textContent = "Terkirim ✅";
      } catch (e) {
        console.error(e);
        alert("Gagal submit (network/server).");
        btn.disabled = false;
        btn.textContent = originalText;
      }
    });
  });

}

/* helpers */

function labelForArea(key) {
  switch (key) {
    case "luar": return "Area Luar";
    case "smoking": return "Area Smoking";
    case "balkon": return "Area Balkon";
    default: return key || "-";
  }
}

function showModal(modal) {
  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function hideModal(modal) {
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

function resetCanvas(canvas, placeholder) {
  const ctx = canvas.getContext("2d");
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  canvas.classList.add("hidden");
  placeholder.classList.remove("hidden");
}

async function startCamera(video) {
  // stop old stream first
  await stopCamera(video);

  // prefer back camera on phones
  const constraints = {
    video: { facingMode: { ideal: "environment" } },
    audio: false,
  };

  stream = await navigator.mediaDevices.getUserMedia(constraints);
  video.srcObject = stream;
  await video.play();
}

async function stopCamera(video) {
  if (video) {
    video.pause();
    video.srcObject = null;
  }
  if (stream) {
    stream.getTracks().forEach((t) => t.stop());
    stream = null;
  }
}

async function getGeolocation() {
  return new Promise((resolve) => {
    if (!navigator.geolocation) return resolve(null);

    navigator.geolocation.getCurrentPosition(
      (pos) =>
        resolve({
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
          accuracy: pos.coords.accuracy,
        }),
      () => resolve(null),
      { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
    );
  });
}

async function reverseGeocode(lat, lng) {
  const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

  const res = await fetch("/geo/reverse", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": token || "",
      Accept: "application/json",
    },
    body: JSON.stringify({ lat, lng }),
  });

  return res.json();
}

function drawWatermark(canvas, lines) {
  const ctx = canvas.getContext("2d");

  ctx.save();

  // font adaptif
  const fontSize = Math.max(18, Math.floor(canvas.width * 0.022));
  ctx.font = `${fontSize}px Arial`;

  // teks putih + shadow hitam biar mirip contoh kamu
  ctx.fillStyle = "white";
  ctx.textAlign = "right";
  ctx.textBaseline = "bottom";
  ctx.shadowColor = "rgba(0,0,0,0.85)";
  ctx.shadowBlur = 6;

  const padding = Math.floor(canvas.width * 0.03);
  const lineHeight = Math.floor(fontSize * 1.25);

  let x = canvas.width - padding;
  let y = canvas.height - padding;

  // gambar dari bawah ke atas
  for (let i = lines.length - 1; i >= 0; i--) {
    ctx.fillText(lines[i], x, y);
    y -= lineHeight;
  }

  ctx.restore();
}

function canvasToBlob(canvas) {
  return new Promise((resolve) => {
    canvas.toBlob((blob) => resolve(blob), "image/jpeg", 0.9);
  }); 
}
