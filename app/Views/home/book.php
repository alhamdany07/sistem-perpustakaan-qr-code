<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title>Daftar Buku</title>

<style>
    /* Tombol Home Unmus */
    .btn-home-unmus {
        padding: 10px 16px;
        border: 2px solid #C62828;
        color: #C62828;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: .25s;
        background: white;
    }
    .btn-home-unmus:hover {
        background: #C62828;
        color: white;
    }

    /* Styling card daftar buku */
    .book-card {
        border-radius: 18px;
        overflow: hidden;
        background: white;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        transition: .25s;
    }
    .book-card:hover {
        transform: translateY(-6px);
    }

    /* Gambar cover */
    .book-cover {
        height: 260px;
        background-size: cover;
        background-position: center;
        border-radius: 0;
    }

    /* Judul */
    .book-title {
        font-size: 17px;
        font-weight: 600;
        color: #5A3A3A;
    }
</style>

<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div class="card p-4" style="border-radius:18px;">
    <div>

        <!-- 🏠 TOMBOL HOME – kiri atas card -->
        <a href="<?= base_url('/'); ?>" class="btn-home-unmus mb-3">
            <i class="ti ti-home"></i> Home
        </a>

        <!-- Judul + Search -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold" style="color:#8B0A0A;">Daftar Buku</h2>
            </div>

            <div class="col-md-6">
                <form action="" method="get">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               value="<?= $search ?? ''; ?>"
                               placeholder="Cari buku..."
                               style="border-radius:10px 0 0 10px;">
                        <button class="btn" 
                                type="submit"
                                style="
                                    border:1px solid #C62828;
                                    color:#C62828;
                                    border-radius:0 10px 10px 0;
                                ">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- LIST BUKU -->
        <div class="row">
            <?php if (empty($books)) : ?>
                <h4 class="text-center">Buku tidak ditemukan</h4>
            <?php endif; ?>

            <?php foreach ($books as $book) : ?>
                <?php
                    $coverFile = BOOK_COVER_URI . $book['book_cover'];
                    $cover = base_url((!empty($book['book_cover']) && file_exists($coverFile))
                            ? $coverFile
                            : BOOK_COVER_URI . DEFAULT_BOOK_COVER);
                ?>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="book-card">
                        <a href="<?= base_url("admin/books/{$book['slug']}"); ?>">
                            <div class="book-cover" style="background-image:url('<?= $cover ?>');"></div>
                        </a>

                        <div class="p-3">
                            <div class="book-title">
                                <?= substr($book['title'], 0, 60) . (strlen($book['title']) > 60 ? '...' : '') ?>
                                (<?= $book['year'] ?>)
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

        <!-- PAGINATION -->
        <div class="mt-3">
            <?= $pager->links('books', 'my_pager'); ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
