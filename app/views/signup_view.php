<?php

declare(strict_types= 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>

    <link rel="stylesheet" href="styles/cadastro.css">
</head>
<body>

<div class="container">

    <div class="top-icon">👤</div>

    <h1>Criar conta</h1>
    <p class="subtitle">Preencha os dados abaixo para se cadastrar</p>

    <div class="card">
        <?php
        if(isset($_SESSION["errors_signup"])) {
            $errors = $_SESSION["errors_signup"] ?? [];
            unset($_SESSION["errors_signup"]);
        }
        ?>

        <form action="" method="POST">

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


            <label>Telefone</label>
            <input type="text" name="phone" inputmode="numeric" placeholder="(00) 00000-0000" required>

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

</body>
</html>