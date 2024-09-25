<?php
include(__DIR__ . "/../../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['user'])) $api_response = $api->user_patch($_POST['user']);

    if (!empty($_POST["password"])) {
        if ($_POST["password"]["new_one"] != $_POST["password"]["new_two"]) {
            echo "Pas bon.";
        } else {
            $api_response = $api->user_password_set($_POST["password"]["current"], $_POST["password"]["new_one"]);
        }
    }

    if (!empty($api_response)) set_alert($api_response["message"]);
    $_SESSION["user"] = $api->user_get();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Sécurité") ?>

<body>
    <?= WebViewEngine::header("Sécurité", "/dashboard", "bi-arrow-left", "Retour") ?>
    <?= display_alert() ?>
    <section class="container">
        <h2><i class="bi bi-envelope"></i> Adresse de courriel secondaire</h2>
        <p>Vous recevrez les notifications liées à la sécurité de votre compte sur cette adresse. Elle est facultative.</p>
        <form class="mb-3" action="" method="POST">
            <div class="mb-3">
                <input type="email" class="form-control" id="secondaryEmailInput" name="user[recovery_email]" value="<?= htmlspecialchars($_SESSION["user"]["recovery_email"]) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary shadow-sm">Enregistrer</button>
        </form>
    </section>
    <div class="separator container"></div>
    <section class="container">
        <h2><i class="bi bi-key"></i> Mot de passe</h2>
        <p>Pour changer de mot de passe, saisissez votre mot de passe actuel en premier, puis votre nouveau mot de passe deux fois.</p>
        <form class="mb-3" action="" method="POST">
            <div class="mb-3">
                <input type="password" class="form-control" name="password[current]" autocomplete="current-password" placeholder="Actuel" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password[new_one]" autocomplete="new-password" placeholder="Nouveau" required>
                <input type="password" class="form-control" name="password[new_two]" autocomplete="new-password" placeholder="Nouveau" required>
            </div>
            <button type="submit" class="btn btn-primary shadow-sm">Enregistrer</button>
        </form>
    </section>
    <div class="separator container"></div>
    <section class="container">
        <h2><i class="bi bi-lock"></i> Authentification à deux facteurs</h2>
        <p>L'authentification à deux facteurs sert à renforcer la sécurité de votre compte en mettant en place une étape supplémentaire à l'ouverture de session.</p>

        <h4><i class="bi bi-123"></i> Code à usage unique</h4>
        <?php if ($_SESSION["user"]["totp_is_enabled"]) : ?>
            <div class="alert alert-success">
                <i class="bi bi-lock"></i> L'authentification renforcée par un code à usage unique est actuellement activée sur votre compte.
            </div>
        <?php else : ?>
            <div class="alert alert-danger">
                <i class="bi bi-unlock"></i> L'authentification renforcée par un code à usage unique est actuellement désactivée sur votre compte.
            </div>
        <?php endif; ?>
        <a href="totp.php" class="btn btn-primary shadow-sm">
            Gérer
        </a>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>