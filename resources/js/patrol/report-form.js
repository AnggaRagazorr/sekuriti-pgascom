let reportReady = false;

export function isReportReady() {
  return reportReady;
}

export function initReportForm() {
  const modal = document.getElementById("reportModal");
  const btnOpen = document.getElementById("btnOpenReport");
  const btnClose = document.getElementById("btnCloseReport");
  const btnSubmit = document.getElementById("btnSubmitReport");
  const statusEl = document.getElementById("reportStatus");

  if (!modal || !btnOpen || !btnSubmit) return;

  const reportDateText = document.getElementById("reportDateText");
  const reportTimeText = document.getElementById("reportTimeText");

  const pad = (n) => String(n).padStart(2, "0");
  const nowLocalDate = () => {
    const d = new Date();
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
  };
  const nowLocalDateTime = () => {
    const d = new Date();
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
  };
  const dayNameID = () => new Date().toLocaleDateString("id-ID", { weekday: "long" });

  const open = () => {
    reportDateText.value = `${dayNameID()}, ${nowLocalDate()}`;
    reportTimeText.value = nowLocalDateTime();
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  };

  const close = () => {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  };

  btnOpen.addEventListener("click", open);
  btnClose?.addEventListener("click", close);

  btnSubmit.addEventListener("click", async () => {
    const situasi = document.getElementById("situasi").value.trim();
    const aght = document.getElementById("aght").value.trim();
    const cuaca = document.getElementById("cuaca").value.trim();
    const pdam = document.getElementById("pdam").value.trim();
    const personel_wfo = document.getElementById("personel_wfo").value.trim();
    const personel_tambahan = document.getElementById("personel_tambahan").value.trim();

    if (!personel_wfo) {
      alert("PERSONEL WFO wajib dipilih.");
      return;
    }

    const payload = {
      report_date: nowLocalDate(),
      report_day: dayNameID(),
      submitted_time: nowLocalDateTime(),
      situasi,
      aght,
      cuaca,
      pdam,
      personel_wfo,
      personel_tambahan,
    };

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

    btnSubmit.disabled = true;
    btnSubmit.textContent = "Mengirim...";

    try {
      const res = await fetch("/security/patrol/report", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": token || "",
          Accept: "application/json",
        },
        body: JSON.stringify(payload),
      });

      const json = await res.json();
      if (!json.ok) throw new Error("Gagal submit report");

      reportReady = true;
      if (statusEl) {
        statusEl.textContent = "Sudah diisi ✅";
        statusEl.classList.remove("text-red-600");
        statusEl.classList.add("text-green-600");
      }

      close();
    } catch (e) {
      console.error(e);
      alert("Gagal submit hasil patroli kantor.");
    } finally {
      btnSubmit.disabled = false;
      btnSubmit.textContent = "Submit";
    }
  });
}
