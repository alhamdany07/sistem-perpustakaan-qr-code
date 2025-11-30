<?php

namespace App\Controllers\Members;

use App\Libraries\QRGenerator;
use App\Models\BookModel;
use App\Models\BookStockModel;
use App\Models\FineModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;

class MembersController extends ResourceController
{
    protected MemberModel $memberModel;
    protected BookModel $bookModel;
    protected BookStockModel $bookStockModel;
    protected LoanModel $loanModel;
    protected FineModel $fineModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel;
        $this->bookModel = new BookModel;
        $this->bookStockModel = new BookStockModel;
        $this->loanModel = new LoanModel;
        $this->fineModel = new FineModel;

        helper('upload');
    }

    public function index()
    {
        $itemPerPage = 20;

        if ($this->request->getGet('search')) {
            $keyword = $this->request->getGet('search');
            $members = $this->memberModel
                ->like('first_name', $keyword, insensitiveSearch: true)
                ->orLike('last_name', $keyword, insensitiveSearch: true)
                ->orLike('email', $keyword, insensitiveSearch: true)
                ->paginate($itemPerPage, 'members');

            $members = array_filter($members, function ($member) {
                return $member['deleted_at'] == null;
            });
        } else {
            $members = $this->memberModel->paginate($itemPerPage, 'members');
        }

        $data = [
            'members'      => $members,
            'pager'        => $this->memberModel->pager,
            'currentPage'  => $this->request->getVar('page_categories') ?? 1,
            'itemPerPage'  => $itemPerPage,
            'search'       => $this->request->getGet('search')
        ];

        return view('members/index', $data);
    }

    public function show($uid = null)
    {
        $member = $this->memberModel->where('uid', $uid)->first();

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        $loans = $this->loanModel->where([
            'member_id' => $member['id'],
            'return_date' => null
        ])->findAll();

        $fines = $this->loanModel
            ->select('loans.id, fines.amount_paid, fines.fine_amount, fines.paid_at')
            ->join('fines', 'loans.id=fines.loan_id', 'LEFT')
            ->where('member_id', $member['id'])
            ->findAll();

        $totalBooksLent = empty($loans) ? 0 : array_reduce(
            array_map(fn($loan) => $loan['quantity'], $loans),
            fn($carry, $item) => ($carry + $item)
        );

        $return = array_filter($loans, fn($loan) => $loan['return_date'] != null);

        $lateLoans = array_filter($loans, fn($loan) =>
            $loan['return_date'] == null && Time::now()->isAfter(Time::parse($loan['due_date']))
        );

        $totalFines = array_reduce(
            array_map(fn($fine) => $fine['fine_amount'], $fines),
            fn($carry, $item) => ($carry + $item)
        );

        $paidFines = array_reduce(
            array_map(fn($fine) => $fine['amount_paid'], $fines),
            fn($carry, $item) => ($carry + $item)
        );

        $unpaidFines = $totalFines - $paidFines;

        // Generate QR jika belum ada (tanpa label supaya tidak dobel)
        if (!file_exists(MEMBERS_QR_CODE_PATH . $member['qr_code']) || empty($member['qr_code'])) {

            $qrGenerator = new QRGenerator();
            $qrCode = $qrGenerator->generateQRCode(
                data: $member['uid'],
                labelText: null,
                dir: MEMBERS_QR_CODE_PATH,
                filename: $member['uid'] . '.png'
            );

            $this->memberModel->update($member['id'], ['qr_code' => $qrCode]);
            $member = $this->memberModel->where('uid', $uid)->first();
        }

        $data = [
            'member'         => $member,
            'totalBooksLent' => $totalBooksLent,
            'loanCount'      => count($loans),
            'returnCount'    => count($return),
            'lateCount'      => count($lateLoans),
            'unpaidFines'    => $unpaidFines,
            'paidFines'      => $paidFines,
        ];

        return view('members/show', $data);
    }

    public function new()
    {
        return view('members/create', [
            'validation' => \Config\Services::validation()
        ]);
    }

    public function create()
    {
        if (!$this->validate([
            'first_name'    => 'required|alpha_numeric_punct|max_length[100]',
            'last_name'     => 'permit_empty|alpha_numeric_punct|max_length[100]',
            'email'         => 'required|valid_email|max_length[255]',
            'phone'         => 'required|alpha_numeric_punct|min_length[4]|max_length[20]',
            'address'       => 'required|string|min_length[5]|max_length[511]',
            'date_of_birth' => 'required|valid_date',
            'gender'        => 'required|alpha_numeric_punct',
        ])) {
            return view('members/create', [
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ]);
        }

        $uid = sha1(
            $this->request->getVar('first_name')
            . $this->request->getVar('email')
            . $this->request->getVar('phone')
            . rand(0, 1000)
            . md5($this->request->getVar('gender'))
        );

        $qrGenerator = new QRGenerator();
        $qrCode = $qrGenerator->generateQRCode(
            data: $uid,
            labelText: null,
            dir: MEMBERS_QR_CODE_PATH,
            filename: $uid . '.png'
        );

        if (!$this->memberModel->save([
            'uid'           => $uid,
            'first_name'    => $this->request->getVar('first_name'),
            'last_name'     => $this->request->getVar('last_name'),
            'email'         => $this->request->getVar('email'),
            'phone'         => $this->request->getVar('phone'),
            'address'       => $this->request->getVar('address'),
            'date_of_birth' => $this->request->getVar('date_of_birth'),
            'gender'        => $this->request->getVar('gender'),
            'qr_code'       => $qrCode
        ])) {
            session()->setFlashdata(['msg' => 'Insert failed']);
            return view('members/create', [
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ]);
        }

        session()->setFlashdata(['msg' => 'Insert new member successful']);
        return redirect()->to('admin/members');
    }

    public function edit($uid = null)
    {
        $member = $this->memberModel->where('uid', $uid)->first();

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        return view('members/edit', [
            'member'     => $member,
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function update($uid = null)
    {
        $member = $this->memberModel->where('uid', $uid)->first();

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        if (!$this->validate([
            'first_name'    => 'required|alpha_numeric_punct|max_length[100]',
            'last_name'     => 'permit_empty|alpha_numeric_punct|max_length[100]',
            'email'         => 'required|valid_email|max_length[255]',
            'phone'         => 'required|alpha_numeric_punct|min_length[4]|max_length[20]',
            'address'       => 'required|string|min_length[5]|max_length[511]',
            'date_of_birth' => 'required|valid_date',
            'gender'        => 'required|alpha_numeric_punct',
        ])) {
            return view('members/edit', [
                'member'     => $member,
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ]);
        }

        $firstName = $this->request->getVar('first_name');
        $email = $this->request->getVar('email');
        $phone = $this->request->getVar('phone');
        $gender = $this->request->getVar('gender');

        $isChanged = ($firstName != $member['first_name']
            || $email != $member['email']
            || $phone != $member['phone']);

        $uid = $isChanged
            ? sha1($firstName . $email . $phone . rand(0, 1000) . md5($gender))
            : $member['uid'];

        if ($isChanged) {
            $qrGenerator = new QRGenerator();
            $qrCode = $qrGenerator->generateQRCode(
                data: $uid,
                labelText: null,
                dir: MEMBERS_QR_CODE_PATH,
                filename: $uid . '.png'
            );
            deleteMembersQRCode($member['qr_code']);
        } else {
            $qrCode = $member['qr_code'];
        }

        if (!$this->memberModel->save([
            'id'            => $member['id'],
            'uid'           => $uid,
            'first_name'    => $this->request->getVar('first_name'),
            'last_name'     => $this->request->getVar('last_name'),
            'email'         => $this->request->getVar('email'),
            'phone'         => $this->request->getVar('phone'),
            'address'       => $this->request->getVar('address'),
            'date_of_birth' => $this->request->getVar('date_of_birth'),
            'gender'        => $this->request->getVar('gender'),
            'qr_code'       => $qrCode
        ])) {
            session()->setFlashdata(['msg' => 'Insert failed']);
            return view('members/edit', [
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ]);
        }

        session()->setFlashdata(['msg' => 'Update member successful']);
        return redirect()->to('admin/members');
    }

    public function delete($uid = null)
    {
        $member = $this->memberModel->where('uid', $uid)->first();

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        if (!$this->memberModel->delete($member['id'])) {
            session()->setFlashdata(['msg' => 'Failed to delete member', 'error' => true]);
            return redirect()->back();
        }

        deleteMembersQRCode($member['qr_code']);

        session()->setFlashdata(['msg' => 'Member deleted successfully']);
        return redirect()->to('admin/members');
    }

    /** -----------------------------------------
     *  FUNGSI REGENERATE QR LAMA
     * -----------------------------------------
     */
    public function regenerateQR()
    {
        $qr = new QRGenerator();
        $members = $this->memberModel->findAll();

        echo "<h2>Regenerating QR Codes...</h2><br>";

        foreach ($members as $member) {
            $uid = $member['uid'];
            $filename = $member['qr_code'];

            if (empty($filename)) {
                continue;
            }

            // Buat ulang QR dengan logo baru
            $qr->generateQRCode(
                data: $uid,
                labelText: null,
                dir: MEMBERS_QR_CODE_PATH,
                filename: $filename
            );

            echo "✔ Regenerated: {$member['first_name']} {$member['last_name']}<br>";
        }

        echo "<br><strong>SELESAI! Semua QR anggota berhasil diperbarui.</strong>";
    }

    public function memberCard($uid = null)
{
    $member = $this->memberModel->where('uid', $uid)->first();

    if (empty($member)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Member not found');
    }

    $public = FCPATH;

    // Lokasi file asli PNG/JPG
    $qrFile   = $public . 'uploads/qr_codes/members/' . $member['qr_code'];
    $logoFile = $public . 'logo-unmus.jpg'; // sesuai folder kamu

    if (!file_exists($qrFile)) die("QR file not found: " . $qrFile);
    if (!file_exists($logoFile)) die("Logo not found: " . $logoFile);

    // KONVERSI KE BASE64
    $qrBase64   = $this->imgToBase64($qrFile);
    $logoBase64 = $this->imgToBase64($logoFile);

    // Load ke view
    $html = view('members/card_pdf', [
        'member'    => $member,
        'qrUrl'     => $qrBase64,
        'logoUrl'   => $logoBase64
    ]);

    // GENERATE PDF
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->set_option('isRemoteEnabled', true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $dompdf->stream("kartu_anggota_{$member['uid']}.pdf", ["Attachment" => true]);
}

private function imgToBase64($path)
{
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

}
