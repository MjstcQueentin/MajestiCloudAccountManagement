<?php
include(__DIR__ . "/../engine/core.include.php");
require_token();

$api = new MajestiCloudAPI($_SESSION["token"]);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['user'])) {
        $api_response = $api->user_patch($_POST['user']);
    }

    if (!empty($_POST['profile_picture'])) {
        switch ($_POST['profile_picture']['action']) {
            case 'delete':
                $api_response = $api->user_profile_picture_delete();
                break;
            case 'add':
                $local_path = UPLOAD_DIR . "/" . $_FILES['profile_picture']['name']['file'];
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name']['file'], $local_path)) {
                    $api_response = $api->user_profile_picture_set($local_path);
                    unlink($local_path);
                }
                break;
        }
    }

    if (!empty($api_response)) set_alert($api_response["message"]);

    $_SESSION["user"] = $api->user_get();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?= WebViewEngine::head("Mon profil") ?>

<body>
    <?= WebViewEngine::header("Mon profil", "index.php", "bi-arrow-left", "Retour") ?>
    <?= display_alert() ?>
    <section class="container">
        <h2><i class="bi bi-person-circle"></i> Photo de profil</h2>
        <div class="mb-3">
            <img src="<?= $api->user_profile_picture_get() ?>" class="rounded-circle" alt="Photo de profil" height="120" width="120">
        </div>
        <div>
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#profilePictureChangeModal">Nouvelle photo</button>
            <form class="d-inline" action="" method="POST">
                <input type="hidden" name="profile_picture[action]" value="delete">
                <button type="submit" class="btn btn-danger shadow-sm">Supprimer la photo</button>
            </form>
        </div>
    </section>
    <div class="separator container"></div>
    <section class="container">
        <h2><i class="bi bi-person-vcard"></i> Nom d'affichage</h2>
        <form class="mb-3" action="" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" id="displayNameInput" name="user[name]" value="<?= htmlspecialchars($_SESSION["user"]["name"]) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary shadow-sm">Enregistrer</button>
        </form>
    </section>
    <div class="separator container"></div>
    <section class="container">
        <h2><i class="bi bi-envelope"></i> Adresse e-mail principale</h2>
        <form class="mb-3" action="" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" id="primaryEmailInput" name="user[primary_email]" value="<?= htmlspecialchars($_SESSION["user"]["primary_email"]) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary shadow-sm">Enregistrer</button>
        </form>
    </section>
    <?= WebViewEngine::footer() ?>

    <!-- Profile picture change modal -->
    <div class="modal fade" id="profilePictureChangeModal" tabindex="-1" aria-labelledby="profilePictureChangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="profilePictureChangeModalLabel">Changer de photo de profil</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="profile_picture[action]" value="add">
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Nouvelle photo</label>
                            <input class="form-control" type="file" name="profile_picture[file]" id="formFile" required accept="image/*" aria-describedby="profilPictureHelp">
                            <div id="profilPictureHelp" class="form-text">Tout format d'image accepté, 2 Mo maximum.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>