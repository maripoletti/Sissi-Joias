<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>

    <link rel="stylesheet" href="styles/cadastro.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

<div class="container">

    <div class="top-icon" id="previewContainer">
        <span id="iconPadrao">👤</span>
        <img id="previewImage" src="" alt="Foto de perfil">
    </div>

    <h1>Criar conta</h1>
    <p class="subtitle">Preencha os dados abaixo para se cadastrar</p>

    <div class="card">
        <?php
        if (isset($_SESSION["errors_signup"])) {
            $errors = $_SESSION["errors_signup"] ?? [];
            unset($_SESSION["errors_signup"]);
        }
        ?>

        <form id="cadastroForm" action="/cadastro" method="POST" enctype="multipart/form-data">

            <label for="fotoPerfil">Foto de perfil</label>
            <div class="upload-box">
                <input type="file" name="foto_perfil" id="fotoPerfil" accept="image/*">
            </div>

            <?php if (isset($errors["foto_wrong"])): ?>
                <p class="errors"><?= $errors["foto_wrong"] ?></p>
            <?php endif; ?>

            <label>Nome completo</label>
            <input type="text" name="name" placeholder="Seu nome completo" required>
            <?php if (isset($errors["name_wrong"])): ?>
                <p class="errors"><?= $errors["name_wrong"] ?></p>
            <?php endif; ?>

            <label>E-mail</label>
            <input type="email" name="email" placeholder="seu@email.com" required>
            <?php if (isset($errors["email_wrong"])): ?>
                <p class="errors"><?= $errors["email_wrong"] ?></p>
            <?php endif; ?>

            <label>Senha</label>
            <input type="password" name="pwd" placeholder="Crie uma senha forte" required>
            <?php if (isset($errors["pwd_wrong"])): ?>
                <p class="errors"><?= $errors["pwd_wrong"] ?></p>
            <?php endif; ?>

            <label>Confirmar Senha</label>
            <input type="password" name="pwdRepeat" placeholder="Confirme sua senha" required>
            <?php if (isset($errors["pwd_match"])): ?>
                <p class="errors"><?= $errors["pwd_match"] ?></p>
            <?php endif; ?>

            <label>Telefone</label>
            <input type="text" name="phone" id="telefone" placeholder="(XX) XXXXX-XXXX" inputmode="numeric">
            <input type="hidden" name="phone_raw" id="telefone_raw">

            <?php if (isset($errors["phone_wrong"])): ?>
                <p class="errors"><?= $errors["phone_wrong"] ?></p>
            <?php endif; ?>

            <?php if (isset($errors["empty_field"])): ?>
                <p class="errors"><?= $errors["empty_field"] ?></p>
            <?php endif; ?>

            <?php if (isset($errors["is_bigger"])): ?>
                <p class="errors"><?= $errors["is_bigger"] ?></p>
            <?php endif; ?>

            <button type="submit">Criar minha conta →</button>

        </form>

        <div class="login-link">
            Já possui uma conta? <a href="/">Entrar</a>
        </div>
    </div>

</div>

<script>
const Telefone = document.querySelector("#telefone");
const TelefoneRaw = document.querySelector("#telefone_raw");
const fotoPerfil = document.querySelector("#fotoPerfil");
const previewImage = document.querySelector("#previewImage");
const iconPadrao = document.querySelector("#iconPadrao");

Telefone.addEventListener("input", () => {
    let v = Telefone.value.replace(/\D/g, "");
    if (v.length > 11) v = v.slice(0, 11);

    let vFormat = v;
    if (v.length > 10) {
        vFormat = v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
    } else if (v.length > 5) {
        vFormat = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, "($1) $2-$3");
    } else if (v.length > 2) {
        vFormat = v.replace(/^(\d{2})(\d{0,5})$/, "($1) $2");
    }

    Telefone.value = vFormat;
    TelefoneRaw.value = v;
});

fotoPerfil.addEventListener("change", function () {
    const arquivo = this.files[0];

    if (arquivo) {
        const leitor = new FileReader();

        leitor.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = "block";
            iconPadrao.style.display = "none";
        };

        leitor.readAsDataURL(arquivo);
    } else {
        previewImage.src = "";
        previewImage.style.display = "none";
        iconPadrao.style.display = "block";
    }
});
</script>
</body>
</html>