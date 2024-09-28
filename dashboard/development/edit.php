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
            <h2><i class="bi bi-info-square"></i> Informations générales</h2>
            <p>Ces informations seront présentées à l'utilisateur se connectant via le portail d'authentification interactif.</p>
            <form action="edit.php" method="POST">
                <input type="hidden" name="uuid" value="<?= htmlspecialchars($client["uuid"]) ?>">
                <div class="mb-3">
                    <label for="nameInput" class="form-label">Nom de l'application</label>
                    <input type="text" class="form-control" id="nameInput" name="name" aria-describedby="nameHelp" value="<?= htmlspecialchars($client["name"]) ?>" required>
                    <div id="nameHelp" class="form-text">Saisissez ici le nom de votre client tel qu'il est connu par vos utilisateurs.</div>
                </div>
                <div class="mb-3">
                    <label for="authorNameInput" class="form-label">Nom de l'auteur</label>
                    <input type="text" class="form-control" id="authorNameInput" name="author_name" aria-describedby="authorNameHelp" value="<?= htmlspecialchars($client["author_name"]) ?>" required>
                    <div id="authorNameHelp" class="form-text">Saisissez ici le nom de l'auteur du client tel qu'il est connu par vos utilisateurs.</div>
                </div>
                <div class="mb-3">
                    <label for="descriptionInput" class="form-label">Description de l'application</label>
                    <textarea class="form-control" id="descriptionInput" name="description" aria-describedby="descriptionHelp" required><?= htmlspecialchars($client["description"]) ?></textarea>
                    <div id="descriptionHelp" class="form-text">Saisissez ici une courte description, en une phrase maximum, qui aide à reconnaître votre application.</div>
                </div>
                <div class="mb-3">
                    <label for="webpageInput" class="form-label">Adresse de la page Web</label>
                    <input type="url" class="form-control" id="webpageInput" name="webpage" aria-describedby="webpageHelp" value="<?= htmlspecialchars($client["webpage"]) ?>" required>
                    <div id="webpageHelp" class="form-text">Saisissez ici une URL qui dirigera vos utilisateurs vers le site vitrine de votre client.</div>
                </div>
                <div class="mb-3">
                    <label for="logoInput" class="form-label">Logo ou icône</label>
                    <div class="d-flex flex-row gap-1">
                        <div class="border rounded bg-body-tertiary p-2">
                            <img src="<?= htmlspecialchars($client["logo_url"]) ?>" alt="Logo actuel" height="64" width="64">
                        </div>
                        <div class="flex-grow-1">
                            <input type="url" class="form-control" id="logoInput" name="logo_url" aria-describedby="logoHelp" value="<?= htmlspecialchars($client["logo_url"]) ?>" required>
                            <div id="logoHelp" class="form-text">Saisissez l'URL menant à une icône carrée d'au moins 64 pixels de côté.</div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>