<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
        }

        .card {
            width: 650px;
            padding: 25px;
            border-radius: 18px;
            border: 2px solid #C9A02C;
            background: linear-gradient(135deg, #fff7d6, #ffeeb0);
            margin: 40px auto;
        }

        .header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 3px solid #C9A02C;
            margin-bottom: 20px;
        }

        .header img {
            width: 85px;
        }

        .header .title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 8px;
            color: #4A3A00;
            line-height: 1.3;
        }

        .content {
            display: table;
            width: 100%;
        }

        .left,
        .right {
            display: table-cell;
            vertical-align: top;
        }

        .left {
            width: 55%;
            padding-left: 20px;
        }

        .right {
            width: 45%;
            text-align: center;
        }

        .qr img {
            width: 220px;
            margin-top: 10px;
            border: 2px solid #C9A02C;
            padding: 5px;
            background: #fff;
            border-radius: 10px;
        }

        .data p {
            font-size: 16px;
            margin: 6px 0;
            color: #3d3d3d;
        }

        .data strong {
            color: #4A3A00;
        }

        .footer {
            border-top: 2px solid #C9A02C;
            margin-top: 18px;
            padding-top: 10px;
            text-align: center;
            font-size: 13px;
            color: #5a5a5a;
        }
    </style>
</head>
<body>

<div class="card">

    <!-- BAGIAN HEADER -->
    <div class="header">
        <img src="<?= $logoUrl ?>" alt="Logo UNMUS">
        <div class="title">
            KARTU ANGGOTA PERPUSTAKAAN<br>
            UNIVERSITAS MUSAMUS
        </div>
    </div>

    <!-- BAGIAN KONTEN -->
    <div class="content">

        <!-- DATA ANGGOTA -->
        <div class="left">
            <div class="data">
                <p><strong>Nama:</strong> <?= $member['first_name'] . " " . $member['last_name'] ?></p>
                <p><strong>Email:</strong> <?= $member['email'] ?></p>
                <p><strong>No. Telepon:</strong> <?= $member['phone'] ?></p>
                <p><strong>UID Anggota:</strong> <?= $member['uid'] ?></p>
                <p><strong>Gender:</strong> <?= ucfirst($member['gender']) ?></p>
            </div>
        </div>

        <!-- QR CODE -->
        <div class="right">
            <div class="qr">
                <img src="<?= $qrUrl ?>" alt="QR Anggota">
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        Dicetak otomatis dari Sistem Perpustakaan UNMUS
    </div>

</div>

</body>
</html>
