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
        <form id="cadastroForm" action="" method="POST">

            <label>Nome completo</label>
            <input type="text" name="nome" placeholder="Seu nome completo">

            <label>E-mail</label>
            <input type="email" name="email" placeholder="seu@email.com">

            <label>Senha</label>
            <input type="password" name="senha" placeholder="Crie uma senha forte">
 
            <label>Confirmar Senha</label>
            <input type="password" name="confirmar_senha" placeholder="Confirme sua senha">

            <label>Telefone</label>
            <input  type= "text" name="telefone" placeholder="(XX) XXXXX-XXXX" maxlength="11" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">

            <button type="submit">Criar minha conta →</button>

        </form>

        <div class="login-link">
            Já possui uma conta? <a href="login.php">Entrar</a>
        </div>
    </div>

</div>

<script src="script.js"></script>
</body>
</html>