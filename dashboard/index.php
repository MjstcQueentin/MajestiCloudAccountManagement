<?php
include(__DIR__ . "/../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);
$sessions = $api->session_get();
usort($sessions, function ($a, $b) {
    return date_create_from_format("Y-m-d H:i:s", $b["last_activity_on"], timezone_open("UTC")) <=> date_create_from_format("Y-m-d H:i:s", $a["last_activity_on"], timezone_open("UTC"));
});

$client = $api->client_get($sessions[0]["client_uuid"]);

$user = $api->user_get();
if ($user["primary_email_is_validated"] == false) {
    set_alert("Veuillez valider votre adresse e-mail principale.", "warning");
} elseif (!empty($user["recovery_email"]) && $user["recovery_email_is_validated"] == false) {
    set_alert("Veuillez valider votre adresse e-mail secondaire.", "warning");
} elseif (!empty($user["to_be_deleted_after"])) {
    set_alert("Votre compte sera supprimé le " . date_create_from_format("Y-m-d H:i:s", $user["to_be_deleted_after"])->format("d/m/Y") . ".", "danger");
}

$my_clients = $api->client_get();
?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Gestion du compte MajestiCloud") ?>

<body>
    <?= WebViewEngine::header("Gestion du compte MajestiCloud", "/auth/logout.php", "bi-box-arrow-left", "Déconnexion") ?>
    <?= display_alert() ?>
    <section class="container">
        <div>
            <h2><i class="bi bi-person-lines-fill"></i> Profil</h2>
            <div class="d-flex align-items-center gap-3 mb-2">
                <div><img src="<?= $api->user_profile_picture_get() ?>" class="rounded-circle" alt="Photo de profil" height="80" width="80"></div>
                <div>
                    <p class="h3 mb-1"><?= htmlspecialchars($_SESSION["user"]["name"]) ?></p>
                    <p class="mb-0"><?= htmlspecialchars($_SESSION["user"]["primary_email"]) ?></p>
                </div>
            </div>
            <a href="profile.php" class="btn btn-primary shadow-sm"><i class="bi bi-pencil"></i> Modifier le profil</a>
            <?php if (!$user["primary_email_is_validated"]) : ?>
                <a href="triggeremail.php?for=primary_email_validation" class="btn btn-warning shadow-sm">Renvoyer l'e-mail de validation</a>
            <?php endif; ?>
        </div>
        <div class="separator"></div>
        <div>
            <h2><i class="bi bi-shield-lock"></i> Sécurité</h2>
            <div class="mb-3">
                <p class="m-0">
                    <?php if (!empty($_SESSION["user"]["recovery_email"])) : ?>
                        <i class="bi bi-check2"></i> Adresse de courriel de secours renseignée
                    <?php else : ?>
                        <i class="bi bi-x-lg"></i> Adresse de courriel de secours non renseignée
                    <?php endif; ?>
                </p>

                <p class="m-0">
                    <?php if ($_SESSION["user"]["totp_is_enabled"]) : ?>
                        <i class="bi bi-check2"></i> Authentification par OTP activée
                    <?php else : ?>
                        <i class="bi bi-x-lg"></i> Authentification par OTP désactivée
                    <?php endif; ?>
                </p>
            </div>
            <a href="security/index.php" class="btn btn-primary shadow-sm"><i class="bi bi-pencil"></i> Changer les paramètres de sécurité</a>
            <?php if (!empty($user["recovery_email"]) && !$user["recovery_email_is_validated"]) : ?>
                <a href="triggeremail.php?for=recovery_email_validation" class="btn btn-warning shadow-sm">Renvoyer l'e-mail de validation</a>
            <?php endif; ?>
        </div>
        <div class="separator"></div>
        <div>
            <h2><i class="bi bi-pc-display"></i> Sessions</h2>
            <p class="h4 mt-0">Dernière activité</p>
            <div class="d-flex align-items-center gap-3 mb-2">
                <div>
                    <img src="<?= htmlspecialchars($client["logo_url"]) ?>" title="Logo de l'application" height="55" width="55">
                </div>
                <div>
                    <p class="m-0"><?= htmlspecialchars($sessions[0]["client_name"]) ?> sur <?= htmlspecialchars($sessions[0]["device_name"]) ?></p>
                    <p class="m-0"><?= htmlspecialchars($sessions[0]["last_activity_on"]) ?> - <?= htmlspecialchars($sessions[0]["last_activity_ip"]) ?></p>
                </div>
            </div>
            <a href="sessions.php" class="btn btn-primary shadow-sm"><i class="bi bi-gear-wide-connected"></i> Gérer les sessions</a>
        </div>
        <div class="separator"></div>
        <div>
            <h2><i class="bi bi-person-circle"></i> État du compte</h2>

            <?php if (!empty($user["to_be_deleted_after"])) : ?>
                <p class="mt-0">Le compte est marqué comme À SUPPRIMER le <?= date_create_from_format("Y-m-d H:i:s", $user["to_be_deleted_after"])->format("d/m/Y") ?>.</p>
                <a href="accountdelete.php" class="btn btn-danger shadow-sm"><i class="bi bi-person-check-fill"></i> Annuler la suppression du compte</a>
            <?php else : ?>
                <p class="mt-0">Compte actif.</p>
                <a href="accountdelete.php" class="btn btn-danger shadow-sm"><i class="bi bi-person-dash-fill"></i> Demander la suppression du compte</a>
            <?php endif; ?>
        </div>
        <div class="separator"></div>
        <div>
            <h2><i class="bi bi-braces"></i> Options pour développeur⸱euse⸱s</h2>
            <?php if (empty($my_clients)): ?>
                <div class="alert alert-info">
                    Développeur-euse, utilisez MajestiCloud pour votre authentification centralisée !
                </div>
            <?php else: ?>
                <p class="mt-0">Vous administrez actuellement <?= count($my_clients) == 1 ? "un client" : count($my_clients) . " clients" ?>.</p>
            <?php endif; ?>
            <a href="development" class="btn btn-primary shadow-sm"><i class="bi bi-wrench-adjustable"></i> Administrer</a>
        </div>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>