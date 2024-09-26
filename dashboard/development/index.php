<?php
include(__DIR__ . "/../../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);
$my_clients = $api->client_get();

?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Options pour les développeur⸱euse⸱s") ?>

<body>
    <?= WebViewEngine::header("Options pour les développeur⸱euse⸱s", "/dashboard/index.php", "bi-arrow-left") ?>
    <?= display_alert() ?>
    <section class="container">
        <div>
            <h2><i class="bi bi-window"></i> Clients que vous administrez</h2>

            <?php if (empty($my_clients)): ?>
                <div class="alert alert-info">Vous n'administrez actuellement aucun client.</div>
            <?php else: ?>
                <?php foreach ($my_clients as $client): ?>
                    <div class="d-flex p-3 gap-3 border rounded">
                        <div>
                            <img src="<?= htmlspecialchars($client["logo_url"]) ?>" alt="Logo de <?= htmlspecialchars($client["name"]) ?>" height="64" width="64">
                        </div>
                        <div class="flex-grow">
                            <p><?= htmlentities($client["name"]) ?></p>
                            <div>
                                <a href="edit.php?uuid=<?= htmlspecialchars($client["uuid"]) ?>" class="btn btn-primary shadow-sm">Modifier</a>
                                <a href="authentication.php?uuid=<?= htmlspecialchars($client["uuid"]) ?>" class="btn btn-primary shadow-sm">Authentification</a>
                                <a href="permissions.php?uuid=<?= htmlspecialchars($client["uuid"]) ?>" class="btn btn-primary shadow-sm">Permissions</a>
                                <a href="administrators.php?uuid=<?= htmlspecialchars($client["uuid"]) ?>" class="btn btn-primary shadow-sm">Administrateurs</a>
                                <a href="delete.php?uuid=<?= htmlspecialchars($client["uuid"]) ?>" class="btn btn-danger shadow-sm">Supprimer</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <a href="new.php" class="btn btn-success shadow-sm mt-3">Nouveau</a>
        </div>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>