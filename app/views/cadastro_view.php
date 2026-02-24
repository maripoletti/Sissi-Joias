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
        <form action="" method="POST">

            <label>Nome completo</label>
            <input type="text" name="nome" placeholder="Seu nome completo" required>

            <label>E-mail</label>
            <input type="email" name="email" placeholder="seu@email.com" required>

            <label>Senha</label>
            <input type="password" name="senha" placeholder="Crie uma senha forte" required>

            <label>Telefone</label>
            <input type="text" name="telefone" inputmode="numeric" placeholder="(00) 00000-0000" required>

            <button type="submit">Criar minha conta →</button>

        </form>

        <div class="login-link">
            Já possui uma conta? <a href="/">Entrar</a>
        </div>
    </div>

</div>

</body>
</html>