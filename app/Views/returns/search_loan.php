<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Pengembalian Buku</title>

<style>
  .qr-wrapper {
    max-width: 420px;
    border-radius: 16px;
    padding: 16px;
    border: 2px solid #4f46e5;
    background: linear-gradient(145deg, #eef2ff, #ffffff);
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
  }

  #reader {
    border-radius: 12px;
    overflow: hidden;
    background: #0f172a;
    min-height: 300px;
  }

  #scanLoading {
    display: none;
  }

  .qr-helper-text {
    font-size: 13px;
    color: #6b7280;
    margin-top: 6px;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<a href="<?= base_url('admin/returns'); ?>" class="btn btn-outline-primary mb-3">
  <i class="ti ti-arrow-left"></i> Kembali
</a>

<div class="card">
  <div class="card-body">

    <div class="row">
      <!-- SCAN QR -->
      <div class="col-12 col-md-6">
        <h5 class="fw-semibold">Scan QR peminjaman / anggota</h5>

        <div class="qr-wrapper mt-3">

          <div id="reader"></div>

          <div id="scanLoading" class="alert alert-info mt-3 py-2 px-3">
            <div class="d-flex align-items-center">
              <div class="spinner-border spinner-border-sm me-2"></div>
              Sedang membaca QR dan mencari data...
            </div>
          </div>

          <button id="resumeBtn" class="btn btn-outline-primary w-100 mt-3"
                  style="display:none;" onclick="restartScanner();">
            Scan ulang
          </button>

          <p class="qr-helper-text">
            • Bisa scan QR bukti peminjaman<br>
            • Bisa scan QR anggota<br>
            • Atau klik “Scan an Image File” untuk upload foto QR
          </p>
        </div>
      </div>

      <!-- SEARCH MANUAL -->
      <div class="col-12 col-md-6">
        <h5 class="fw-semibold mb-4">Atau cari anggota / buku</h5>

        <div class="mb-3">
          <label class="form-label">Cari UID, nama, email, judul buku</label>
          <input type="text" id="search" class="form-control"
                 placeholder="'Ikhsan', 'xibox@gmail.com', 'Lorem Ipsum'">
        </div>

        <button class="btn btn-primary" onclick="getLoanData(document.querySelector('#search').value)">
          Cari
        </button>
      </div>
    </div>

    <!-- HASIL -->
    <div class="row">
      <div class="col-12">
        <div id="loanResult">
          <p class="text-center mt-4">Data peminjaman muncul di sini</p>
        </div>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>

<!-- ================= SCRIPTS ================= -->
<?= $this->section('scripts') ?>

<script src="<?= base_url("assets/libs/html5-qrcode/html5-qrcode.min.js") ?>"></script>

<script>
  let htmlScanner;
  let isProcessing = false;

  // ====================== AMBIL DATA AJAX ======================
  function getLoanData(param) {
    if (!param || param.trim() === '') {
      $('#loanResult').html('<p class="text-danger mt-3">Masukkan data untuk mencari.</p>');
      return;
    }

    $('#scanLoading').show();

    $.ajax({
      url: "<?= base_url('admin/returns/new/search'); ?>",
      type: 'get',
      data: { 'param': param },
      success: function(response) {
        $('#loanResult').html(response);
      },
      error: function(_, __, error) {
        $('#loanResult').html(`<p class="text-danger mt-3">${error}</p>`);
      },
      complete: function() {
        $('#scanLoading').hide();
        document.querySelector('#resumeBtn').style.display = 'block';
      }
    });
  }

  // ====================== START SCANNER ======================
  function startScanner() {
    htmlScanner = new Html5QrcodeScanner(
      "reader",
      {
        fps: 25,
        qrbox: { width: 250, height: 250 },
        rememberLastUsedCamera: true,
        showTorchButtonIfSupported: true,
        showZoomSliderIfSupported: true,
        videoConstraints: {
          facingMode: "environment"
        },
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
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

  // ====================== QR SUKSES ======================
  function onScanSuccess(decodedText) {
    if (isProcessing) return;
    isProcessing = true;

    htmlScanner.clear();

    $('#scanLoading').show();

    getLoanData(decodedText);
  }

  function onScanFailure(error) {
    // console.log(error);
  }

  startScanner();

  // ====================== PERCANTIK TOMBOL ======================
  setTimeout(() => {
    const startBtn = document.querySelector('#html5-qrcode-button-camera-start');
    const stopBtn = document.querySelector('#html5-qrcode-button-camera-stop');
    const fileBtn = document.querySelector('#html5-qrcode-button-file-selection');

    if (startBtn) startBtn.classList.add('btn', 'btn-primary', 'btn-sm', 'me-2', 'mt-2');
    if (stopBtn) stopBtn.classList.add('btn', 'btn-outline-secondary', 'btn-sm', 'me-2', 'mt-2');
    if (fileBtn) fileBtn.classList.add('btn', 'btn-outline-primary', 'btn-sm', 'mt-2');
  }, 1200);
</script>

<?= $this->endSection() ?>
