<?php
include(__DIR__ . "/../../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION["user"]["totp_is_enabled"]) {
        $api->user_totp_delete();
    } else {
        $new_totp = $api->user_totp_post();
    }

    $_SESSION["user"] = $api->user_get();
}

?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Gestion de l'authentification par OTP") ?>

<body>
    <?= WebViewEngine::header("Authentification par OTP", "index.php", "bi-arrow-left", "Retour") ?>
    <?= display_alert() ?>

    <section class="container">
        <p>
            L'authentification par OTP (One Time Password en anglais) est une méthode d'authentification renforcée qui consiste à vous demander un
            code à usage unique renouvelé régulièrement.
        </p>
        <p>
            Ce code à usage unique vous sera demandé <b>en plus de votre mot de passe habituel</b> à chaque nouvelle ouverture de session. Il permet de conserver
            votre compte sous contrôle même en cas de vol de mot de passe.
        </p>
        <p>
            Cette méthode requiert l'usage d'une application de génération de mots de passe à usage unique, comme Google Authenticator,
            Microsoft Authenticator, ou Authy par exemple. Ce protocole étant standardisé, le choix de l'application est à votre discrétion.
        </p>
    </section>

    <section class="container">
        <?php if ($_SESSION["user"]["totp_is_enabled"]) : ?>
            <div class="alert alert-success">
                <i class="bi bi-lock"></i> L'authentification renforcée par un code à usage unique est actuellement activée sur votre compte.
            </div>
        <?php else : ?>
            <div class="alert alert-danger">
                <i class="bi bi-unlock"></i> L'authentification renforcée par un code à usage unique est actuellement désactivée sur votre compte.
            </div>
        <?php endif; ?>

        <?php if (isset($new_totp)) : ?>
            <div class="border rounded p-2 mb-3">
                <p class="m-0">Scannez ce QR Code avec votre application d'OTP pour finaliser la configuration.</p>
                <img src="<?= qr_code($new_totp["provisioning_uri"]) ?>" alt="QR Code à scanner pour obtenir les codes à usage unique" height="250">

                <p class="mb-1">Ou, si vous ne pouvez pas scanner le QR Code, alors saisissez la clé de configuration qui suit :</p>
                <pre class="border rounded fs-5 p-1"><?= htmlspecialchars($new_totp["secret"]) ?></pre>
            </div>
        <?php endif; ?>

        <form action="totp.php" method="POST">
            <input type="hidden" action="toggle">

            <?php if ($_SESSION["user"]["totp_is_enabled"]) : ?>
                <button type="submit" class="btn btn-danger">Désactiver</button>
            <?php else : ?>
                <button type="submit" class="btn btn-success">Activer</button>
            <?php endif; ?>
        </form>
    </section>

    <?= WebViewEngine::footer() ?>
</body>

</html>