<?php require_once(__DIR__ . "/engine/webviewengine/WebViewEngine.class.php"); ?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Une erreur est survenue") ?>

<style>
    h2 {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    body {
        background-image: linear-gradient(to top right, #f5fcff, #fff);
    }

    @media (prefers-color-scheme: dark) {
        body {
            background-image: linear-gradient(to top right, #111, #000);
        }
    }
</style>

<body style="min-height: 100vh;" class="p-3 d-flex flex-column justify-content-center align-items-center">
    <div class="mb-3">
        <img src="/assets/images/logos/logo.png" alt="MajestiCloud logo" height="96">
    </div>
    <div class="border rounded-3 shadow p-4 bg-body-tertiary" style="width:100%; max-width: 700px;">
        <h2>Une erreur interne est survenue.</h2>
        <?php if (!empty($_GET["error"])) : ?>
            <p><?= nl2br(htmlspecialchars($_GET["error"])); ?></p>
        <?php endif; ?>
        <a class="btn btn-sm btn-secondary" href="/">Retour vers l'interface</a>
    </div>
    <footer class="m-5">
        &copy; 2024 Les Majesticiels
    </footer>
    <script src="https://assets.lesmajesticiels.org/libraries/bootstrap/bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://assets.lesmajesticiels.org/libraries/bootstrap/bootstrap-5.x-custom/color-modes-toggler.js"></script>
</body>

</html>