import { initReportForm } from "./patrol/report-form";
import { initAreaBarcodeScanner } from "./patrol/scan-barcode";
import { initPatrolCameraCapture } from "./patrol/camera-capture";

document.addEventListener("DOMContentLoaded", () => {
  initReportForm();
  initAreaBarcodeScanner();
  initPatrolCameraCapture();

});
