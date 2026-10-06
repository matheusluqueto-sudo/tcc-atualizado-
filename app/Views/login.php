<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar • EPI Control</title>
    <link rel="stylesheet" href="css/app.css?v=<?= filemtime(__DIR__.'/../../css/app.css') ?>">
    <script src="js/app.js?v=<?= filemtime(__DIR__.'/../../js/app.js') ?>" defer></script>
</head>
<body class="login-page">
    <main class="login-shell">
        <div class="login-art">
            <img src="img/logo.png" alt="EPI Control — Gestão e Segurança" width="1254" height="1254">
        </div>
        <section class="login-card" aria-labelledby="login-title">
            <span class="eyebrow">BEM-VINDO AO EPI CONTROL</span>
            <h1 id="login-title">Entrar na sua conta</h1>
            <p>Acompanhe seus equipamentos e cuide da segurança no trabalho.</p>
            <?php if ($error): ?>
                <div class="notice danger" role="alert"><?= e($error) ?></div>
            <?php endif ?>
            <form method="post">
                <?= csrf() ?>
                <label>E-mail
                    <input type="email" name="usuario" autocomplete="username" placeholder="Digite seu e-mail" value="<?= e($_POST['usuario'] ?? '') ?>" required maxlength="100">
                </label>
                <label>Senha
                    <input type="password" name="senha" autocomplete="current-password" placeholder="Digite sua senha" required>
                </label>
                <button class="button" type="submit">Entrar na minha conta <span aria-hidden="true">→</span></button>
            </form>
            <p class="login-help">Para obter acesso ou recuperar sua senha, procure o administrador responsável.</p>
            <div class="login-signature"><span aria-hidden="true">◆</span> Segurança em cada detalhe.</div>
        </section>
    </main>
</body>
</html>