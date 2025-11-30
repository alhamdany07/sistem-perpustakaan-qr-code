<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title><?= lang('Auth.login') ?></title>

<style>
    body {
        background: linear-gradient(to bottom right, #f4e3ff, #cae6ff, #ffd9e8);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    .login-wrapper {
        min-height: 90vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        border-radius: 18px;
        padding: 30px;
        background: #ffffff;
        box-shadow: 0 10px 35px rgba(0, 0, 0, .1);
        animation: fadeIn .4s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0);   }
    }

    .login-logo {
        text-align: center;
        margin-bottom: 12px;
    }

    .login-logo img {
        width: 80px;
        margin-bottom: 10px;
    }

    .login-title {
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .form-control {
        padding: 12px;
        font-size: 15px;
        border-radius: 10px;
    }

    .btn-primary {
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: .3px;
        background-color: #4c6fff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #3455ff;
    }

    .alert {
        border-radius: 10px;
        font-size: 14px;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('back'); ?>
<a href="<?= base_url(); ?>" class="btn btn-outline-primary m-3 position-absolute">
  <i class="ti ti-arrow-left"></i> Kembali
</a>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>

<div class="login-wrapper">

    <div class="login-card">

        <!-- LOGO -->
        <div class="login-logo">
            <img src="<?= base_url('assets/logo-unmus.png'); ?>" alt="Logo UNMUS">
        </div>

        <div class="login-title"><?= lang('Auth.login') ?></div>

        <!-- ERROR / MESSAGE -->
        <?php if (session('error')) : ?>
            <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif ?>

        <?php if (session('message')) : ?>
            <div class="alert alert-success"><?= session('message') ?></div>
        <?php endif ?>

        <?php if (session('errors')) : ?>
            <div class="alert alert-danger">
                <?php foreach ((array) session('errors') as $error) : ?>
                    <?= $error ?><br>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <!-- FORM LOGIN -->
        <form action="<?= url_to('login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <input type="email" 
                       class="form-control" 
                       name="email"
                       placeholder="<?= lang('Auth.email') ?>" 
                       value="<?= old('email') ?>"
                       required>
            </div>

            <div class="mb-3">
                <input type="password" 
                       class="form-control" 
                       name="password"
                       placeholder="<?= lang('Auth.password') ?>"
                       required>
            </div>

            <?php if (setting('Auth.sessionConfig')['allowRemembering']) : ?>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input" <?= old('remember') ? 'checked' : '' ?>>
                <label class="form-check-label"><?= lang('Auth.rememberMe') ?></label>
            </div>
            <?php endif ?>

            <button type="submit" class="btn btn-primary w-100">
                <?= lang('Auth.login') ?>
            </button>

            <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                <p class="text-center mt-3">
                    <?= lang('Auth.forgotPassword') ?> 
                    <a href="<?= url_to('magic-link') ?>"><?= lang('Auth.useMagicLink') ?></a>
                </p>
            <?php endif ?>

        </form>
    </div>
</div>

<?= $this->endSection() ?>
