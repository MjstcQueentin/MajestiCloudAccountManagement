<?php
include(__DIR__ . "/../../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $delete = $api->client_delete($_REQUEST["uuid"]);
    set_alert($delete["message"], "success");
    http_response_code(303);
    header("Location: index.php");
    exit;
} elseif (empty($_GET["uuid"])) {
    http_response_code(303);
    header("Location: index.php");
    exit;
}

$client = $api->client_get($_REQUEST["uuid"]);

?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Supprimer " . $client["name"]) ?>

<body>
    <?= WebViewEngine::header("Supprimer " . $client["name"], "index.php", "bi-arrow-left") ?>
    <?= display_alert() ?>
    <section class="container">
        <div>
            <h2><i class="bi bi-exclamation-diamond"></i> Veuillez lire attentivement</h2>
            <p>
                La suppression d'un client sur cette instance de MajestiCloud est immédiate et définitive.<br>
                Elle entraînera l'interruption immédiate de toute action en cours via votre client sur MajestiCloud. Tous vos utilisateurs seront immédiatement déconnectés.<br>
                Vous ne pourrez pas restaurer les données de ce client une fois la suppression ordonnée.
            </p>
            <form action="delete.php" method="POST">
                <input type="hidden" name="uuid" value="<?= htmlspecialchars($client["uuid"]) ?>">

                <button type="submit" class="btn btn-danger">Supprimer</button>
                <a href="index.php" class="btn btn-primary">Annuler</a>
            </form>
        </div>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>