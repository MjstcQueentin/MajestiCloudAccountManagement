<?php
include(__DIR__ . "/../../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);
if (empty($_REQUEST["uuid"])) {
    header("Location: index.php");
}

$me = $api->user_get();
$client = $api->client_get($_REQUEST["uuid"]);
$administrators = $api->client_administrator_get($_REQUEST["uuid"]);

?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Gérer " . $client["name"]) ?>

<body>
    <?= WebViewEngine::header("Gérer " . $client["name"], "index.php", "bi-arrow-left") ?>
    <?= display_alert() ?>
    <section class="container">
        <div>
            <h2><i class="bi bi-person-up"></i> Configuration des administrateurs</h2>
            <p>
                Les personnes listées ici peuvent administrer le client : changer ses informations, récupérer sa clé secrète, définir ses permissions et gérer les autres administrateurs.<br>
                N'ajoutez que des personnes de confiance ici. Chaque administrateur a le pouvoir d'ajouter d'autres administrateurs, et de révoquer les accès des autres administrateurs.
            </p>

            <?php foreach ($administrators as $administrator): ?>
                <div class="border rounded d-flex flex-row gap-3 p-3 mb-3">
                    <div>
                        <img class="rounded-circle" src="<?= htmlspecialchars($administrator["profile_picture_path"]) ?>" alt="Photo de profil de <?= htmlspecialchars($administrator["name"]) ?>" height="64" width="64">
                    </div>
                    <div class="flex-grow">
                        <p class="m-0">
                            <?= htmlspecialchars($administrator["name"]) ?>
                            <?php if ($me["uuid"] == $administrator["uuid"]): ?>
                                <span class="badge text-bg-primary">Vous</span>
                            <?php endif; ?>
                        </p>
                        <p class="m-0"><?= htmlspecialchars($administrator["primary_email"]) ?></p>
                        <?php if ($me["uuid"] != $administrator["uuid"]): ?>
                            <div class="mt-1">
                                <a href="#" class="btn btn-sm btn-danger"><i class="bi bi-person-slash"></i> Révoquer l'accès</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>