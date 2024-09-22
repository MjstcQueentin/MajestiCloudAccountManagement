<?php
include(__DIR__ . "/../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);
$current = $api->sessions_current_get();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["action"] == "delete" && !empty($_POST["session"]["uuid"])) {
        $api_response = $api->session_delete($_POST["session"]["uuid"]);
    }

    if (!empty($api_response)) set_alert($api_response["message"]);
}

$sessions = $api->sessions_get();
usort($sessions, function ($a, $b) {
    return date_create_from_format("Y-m-d H:i:s", $b["last_activity_on"], timezone_open("UTC")) <=> date_create_from_format("Y-m-d H:i:s", $a["last_activity_on"], timezone_open("UTC"));
});
?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Sessions") ?>

<body>
    <?= WebViewEngine::header("Sessions", "index.php", "bi-arrow-left", "Retour") ?>
    <?= display_alert() ?>
    <section class="container">
        <?php foreach ($sessions as $session) : ?>
            <div class="d-flex flex-row gap-3 border rounded p-3 mb-3">
                <div>
                    <i class="fs-1 bi bi-window-fullscreen"></i>
                </div>
                <div class="flex-grow">
                    <p class="m-0">
                        <?= htmlentities($session["client_name"]) ?>
                        <?php if ($session["uuid"] == $current['uuid']) : ?>
                            <span class="badge bg-primary fw-normal">Actuelle</span>
                        <?php endif; ?>
                    </p>
                    <p class="m-0 text-muted"><?= htmlentities($session["device_name"]) ?> (<?= htmlentities($session["last_activity_ip"]) ?>)</p>
                    <p class="m-0 text-muted"><?= htmlentities($session["last_activity_on"]) ?></p>
                    <?php if ($session["uuid"] != $current['uuid']) : ?>
                        <form class="d-inline" action="" method="POST">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="session[uuid]" value="<?= $session["uuid"] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-power"></i> End session
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>