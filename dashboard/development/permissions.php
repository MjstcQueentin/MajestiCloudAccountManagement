<?php
include(__DIR__ . "/../../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);
if (empty($_REQUEST["uuid"])) {
    header("Location: index.php");
}

$client = $api->client_get($_REQUEST["uuid"]);
$permissions = $api->client_permission_get($_REQUEST["uuid"]);

?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Gérer " . $client["name"]) ?>

<body>
    <?= WebViewEngine::header("Gérer " . $client["name"], "index.php", "bi-arrow-left") ?>
    <?= display_alert() ?>
    <section class="container">
        <div>
            <h2><i class="bi bi-toggles"></i> Configuration des permissions</h2>
            <p>
                Les permissions définissent ce que votre client aura le droit de faire sur MajestiCloud ou non.<br>
                Chaque permission octroie l'accès à une certaine partie des données. Elle définit aussi si vous avez le droit d'écrire ou non.
            </p>

            <?php if (empty($permissions)): ?>
                <div class="alert alert-info">
                    Aucune permission n'est octroyée pour le moment.
                </div>
            <?php else: ?>
                <?php foreach ($permissions as $permission): ?>
                    <div class="border rounded p-3 mb-3">
                        <p class="m-0"><?= htmlspecialchars($permission["user_friendly_description"]) ?></p>
                        <div class="fs-5">
                            <?php if ($permission["can_read"] == 1): ?>
                                <span class="badge rounded-pill text-bg-success">Lecture autorisée</span>
                            <?php else: ?>
                                <span class="badge rounded-pill text-bg-danger">Lecture interdite</span>
                            <?php endif; ?>

                            <?php if ($permission["can_write"] == 1): ?>
                                <span class="badge rounded-pill text-bg-success">Écriture autorisée</span>
                            <?php else: ?>
                                <span class="badge rounded-pill text-bg-danger">Écriture interdite</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="alert alert-primary">
                L'édition des permissions des clients OAuth sur MajestiCloud est soumise à l'approbation d'un administrateur, afin d'empêcher les vols de données.<br>
                Veuillez contacter le support technique en cas de besoin.
            </div>
        </div>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>