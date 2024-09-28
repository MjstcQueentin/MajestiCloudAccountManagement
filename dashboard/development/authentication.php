<?php
include(__DIR__ . "/../../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patch = $api->client_patch($_POST);
    set_alert($patch["message"], "success");
} elseif (empty($_GET["uuid"])) {
    header("Location: index.php");
    exit;
}

$client = $api->client_get($_REQUEST["uuid"]);

?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Gérer " . $client["name"]) ?>

<body>
    <?= WebViewEngine::header("Gérer " . $client["name"], "index.php", "bi-arrow-left") ?>
    <?= display_alert() ?>
    <section class="container">
        <div>
            <h2><i class="bi bi-key-fill"></i> Configuration du tunnel d'authentification</h2>
            <p>
                Votre application pourra ouvrir de nouvelles sessions grâce au protocole OAuth 2.0.
                Lorsque l'utilisateur souhaite se connecter à votre application avec MajestiCloud, il devra être redirigé
                vers le portail d'authentification interactif pour saisir ses identifiants. Votre application sera identifiée avec le client ID.
                Au retour de l'utilisateur après son authentification auprès de MajestiCloud, votre application recevra un code à usage unique
                qui devra être présenté à l'API, couplée de la clé secrète, en échange d'un jeton de session. Dans certains cas, la clé secrète est remplacée par
                un challenge PKCE (lorsque la clé secrète n'est pas utilisable, dans une application Web par exemple).
            </p>
            <div class="mb-3">
                <label for="uuidInput" class="form-label">ID unique du client</label>
                <input type="text" class="form-control" id="uuidInput" name="uuid" value="<?= htmlspecialchars($client["uuid"]) ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="secretKeyInput" class="form-label">Clé secrète du client</label>
                <input type="text" class="form-control" id="secretKeyInput" name="secret_key" value="<?= htmlspecialchars($client["secret_key"] ?? "") ?>" readonly>
            </div>
            <form action="authentication.php" method="POST">
                <input type="hidden" name="uuid" value="<?= htmlspecialchars($client["uuid"]) ?>">
                <div class="mb-3">
                    <label for="redirectUriInput" class="form-label">URL de redirection</label>
                    <input type="text" class="form-control" id="redirectUriInput" name="callback_url" value="<?= htmlspecialchars($client["callback_url"] ?? "") ?>" aria-describedby="redirectUriHelp">
                    <div id="redirectUriHelp" class="form-text">Saisissez ici une URI de votre client vers laquelle MajestiCloud redirigera l'utilisateur après qu'il se soit authentifié.</div>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>