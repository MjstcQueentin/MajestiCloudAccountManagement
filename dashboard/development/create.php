<?php
include(__DIR__ . "/../../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post = $api->client_post($_POST);
    set_alert($post["message"], "success");
    http_response_code(303);
    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Créer un nouveau client") ?>

<body>
    <?= WebViewEngine::header("Créer un nouveau client", "index.php", "bi-arrow-left") ?>
    <?= display_alert() ?>
    <section class="container">
        <div>
            <h2><i class="bi bi-info-square"></i> Informations de création</h2>
            <p>Ces informations sont nécessaires pour la création d'un nouveau client. Vous pourrez les modifier plus tard.</p>
            <form action="create.php" method="POST">
                <div class="mb-3">
                    <label for="nameInput" class="form-label">Nom de l'application</label>
                    <input type="text" class="form-control" id="nameInput" name="name" aria-describedby="nameHelp" required>
                    <div id="nameHelp" class="form-text">Saisissez ici le nom de votre client tel qu'il est connu par vos utilisateurs.</div>
                </div>
                <div class="mb-3">
                    <label for="authorNameInput" class="form-label">Nom de l'auteur</label>
                    <input type="text" class="form-control" id="authorNameInput" name="author_name" aria-describedby="authorNameHelp" required>
                    <div id="authorNameHelp" class="form-text">Saisissez ici le nom de l'auteur du client tel qu'il est connu par vos utilisateurs.</div>
                </div>
                <div class="mb-3">
                    <label for="descriptionInput" class="form-label">Description de l'application</label>
                    <textarea class="form-control" id="descriptionInput" name="description" aria-describedby="descriptionHelp" required></textarea>
                    <div id="descriptionHelp" class="form-text">Saisissez ici une courte description, en une phrase maximum, qui aide à reconnaître votre application.</div>
                </div>
                <div class="mb-3">
                    <label for="webpageInput" class="form-label">Adresse de la page Web</label>
                    <input type="url" class="form-control" id="webpageInput" name="webpage" aria-describedby="webpageHelp" required>
                    <div id="webpageHelp" class="form-text">Saisissez ici une URL qui dirigera vos utilisateurs vers le site vitrine de votre client.</div>
                </div>
                <div class="mb-3">
                    <label for="logoInput" class="form-label">Logo ou icône</label>
                    <input type="url" class="form-control" id="logoInput" name="logo_url" aria-describedby="logoHelp" required>
                    <div id="logoHelp" class="form-text">Saisissez l'URL menant à une icône carrée d'au moins 64 pixels de côté.</div>
                </div>
                <div class="mb-3">
                    <label for="redirectUriInput" class="form-label">URL de redirection</label>
                    <input type="text" class="form-control" id="redirectUriInput" name="callback_url" aria-describedby="redirectUriHelp">
                    <div id="redirectUriHelp" class="form-text">Saisissez ici une URI de votre client vers laquelle MajestiCloud redirigera l'utilisateur après qu'il se soit authentifié.</div>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </section>
    <?= WebViewEngine::footer() ?>
</body>

</html>