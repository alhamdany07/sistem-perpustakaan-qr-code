<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Peminjaman Baru</title>

<style>
  /* Wrapper scanner */
  .qr-wrapper {
    max-width: 420px;
    border-radius: 16px;
    padding: 16px;
    border: 2px solid #4f46e5;
    background: linear-gradient(145deg, #eef2ff, #ffffff);
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
  }

  /* Kotak reader bawaan html5-qrcode */
  #reader {
    border-radius: 12px;
    overflow: hidden;
    background: #0f172a;
  }

  .qr-helper-text {
    font-size: 13px;
    color: #6b7280;
    margin-top: 6px;
  }

  #scanLoading {
    display: none;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<a href="<?= base_url('admin/loans'); ?>" class="btn btn-outline-primary mb-3">
  <i class="ti ti-arrow-left"></i>
  Kembali
</a>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert <?= (session()->getFlashdata('error') ?? false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <div class="row">
      <!-- ======================= SCAN QR ======================= -->
      <div class="col-12 col-md-6 mb-4">
        <h5 class="card-title fw-semibold">Scan QR Anggota</h5>

        <div class="qr-wrapper mt-3">
          <div id="reader"></div>

          <div id="scanLoading" class="alert alert-info mt-3 py-2 px-3">
            <div class="d-flex align-items-center">
              <div class="spinner-border spinner-border-sm me-2" role="status"></div>
              <span>Sedang membaca QR dan mengambil data anggota...</span>
            </div>
          </div>

          <button class="btn btn-outline-primary w-100 mt-3" id="resumeBtn"
                  style="display:none;" onclick="restartScanner();">
            Scan ulang
          </button>

          <p class="qr-helper-text mb-0">
            • Arahkan kamera ke kartu anggota<br>
            • Atau klik tombol <b>Choose file</b> untuk unggah gambar QR<br>
            • Pastikan QR tidak blur dan cukup terang
          </p>
        </div>
      </div>

      <!-- ======================= SEARCH MANUAL ======================= -->
      <div class="col-12 col-md-6">
        <h5 class="card-title fw-semibold mb-4">Atau cari anggota</h5>
        <div class="mb-3">
          <label for="search" class="form-label">Cari UID, nama atau email</label>
          <input type="text" class="form-control" id="search" name="search"
                 placeholder="'Ikhsan', 'xibox@gmail.com'">
        </div>
        <button class="btn btn-primary" onclick="getMemberData(document.querySelector('#search').value)">
          Cari
        </button>
      </div>
    </div>

    <!-- HASIL ANGGOTA -->
    <div class="row">
      <div class="col-12">
        <div id="memberResult">
          <p class="text-center mt-4">Data anggota muncul di sini setelah scan / pencarian.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url("assets/libs/html5-qrcode/html5-qrcode.min.js") ?>"></script>

<script>
  // ================== AJAX: Ambil data anggota ==================
  function getMemberData(param) {
    if (!param || param.trim() === '') {
      $('#memberResult').html('<p class="text-danger mt-3">Masukkan UID / nama / email dulu.</p>');
      return;
    }

    $('#scanLoading').show();

    $.ajax({
      url: "<?= base_url('admin/loans/new/members/search'); ?>",
      type: 'get',
      data: { 'param': param },
      success: function (response) {
        $('#memberResult').html(response);
      },
      error: function (xhr, status, error) {
        $('#memberResult').html(`<p class="text-danger mt-3">${error}</p>`);
      },
      complete: function () {
        $('#scanLoading').hide();
        document.querySelector('#resumeBtn').style.display = 'block';
      }
    });
  }

  // ================== QR SCANNER CONFIG ==================
  let htmlScanner;
  let isProcessing = false; // biar 1 QR tidak diproses berkali-kali

  function startScanner() {
    htmlScanner = new Html5QrcodeScanner(
      "reader",
      {
        fps: 30,
        qrbox: { width: 250, height: 250 },
        rememberLastUsedCamera: true,
        showTorchButtonIfSupported: true,
        showZoomSliderIfSupported: true,
        /* coba pakai kamera belakang kalau di HP */
        videoConstraints: {
          facingMode: "environment"
        },
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
      },
      false
    );

    htmlScanner.render(onScanSuccess, onScanFailure);
  }

  function restartScanner() {
    isProcessing = false;
    document.querySelector('#resumeBtn').style.display = 'none';
    document.querySelector('#reader').innerHTML = "";
    startScanner();
  }

  // ================== CALLBACK QR BERHASIL ==================
  function onScanSuccess(decodedText, decodedResult) {
    if (isProcessing) return; // cegah double trigger
    isProcessing = true;

    console.log("QR:", decodedText);

    // Jangan pause – kita gunakan clear agar scanner berhenti rapi
    htmlScanner.clear();

    // Tampilkan loading
    $('#scanLoading').show();

    // Ambil data anggota dari hasil QR (biasanya UID)
    getMemberData(decodedText);
  }

  function onScanFailure(error) {
    // boleh diabaikan, scanner akan tetap mencoba
    // console.warn(error);
  }

  // Jalankan scanner pertama kali
  startScanner();

  // ================== Percantik tombol bawaan ==================
  setTimeout(() => {
    const startBtn = document.querySelector('#html5-qrcode-button-camera-start');
    const stopBtn = document.querySelector('#html5-qrcode-button-camera-stop');
    const fileBtn = document.querySelector('#html5-qrcode-button-file-selection');

    if (startBtn) startBtn.classList.add('btn', 'btn-primary', 'btn-sm', 'me-2', 'mt-2');
    if (stopBtn) stopBtn.classList.add('btn', 'btn-outline-secondary', 'btn-sm', 'me-2', 'mt-2');
    if (fileBtn) fileBtn.classList.add('btn', 'btn-outline-primary', 'btn-sm', 'mt-2');
  }, 1500);
</script>
<?= $this->endSection() ?>
