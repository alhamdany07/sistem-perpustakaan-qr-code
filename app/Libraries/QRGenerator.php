<?php

namespace App\Libraries;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Logo\Logo;

class QRGenerator
{
    public function generateQRCode(
        string $data,
        string $labelText = null,
        string $dir = QR_CODES_PATH,
        string $filename = 'qr_code'
    ) {
        // Buat folder jika belum ada
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        // Format nama file
        $filename = url_title(substr($filename, 0, 16), true) . '_' . time() . '.png';

        // QR dasar
        $qr = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setSize(300)
            ->setMargin(10)
            ->setForegroundColor(new Color(10, 15, 30))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Writer
        $writer = new PngWriter();

        // -----------------------------------
        // ⭐ Tambahkan LOGO UNMUS di tengah QR
        // -----------------------------------
        $logoPath = FCPATH . 'logo-unmus.png';

        if (file_exists($logoPath)) {
            // ukuran logo real
            $logo = Logo::create($logoPath)
                ->setResizeToWidth(80)   // atur ukuran logo
                ->setPunchoutBackground(false);

            // Tulis QR + logo
            $result = $writer->write($qr, $logo);

        } else {
            // Tanpa logo
            $result = $writer->write($qr);
        }

        // Simpan ke file
        $result->saveToFile($dir . $filename);

        return $filename;
    }
}
