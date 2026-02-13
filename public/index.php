<?php
require_once __DIR__ . '/../config/session.config.php';
require_once __DIR__ . '/../app/views/index_view.php';


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Sissi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/login.css">
</head>
<body>

  <div class="container">
    <div class="card">

      <div class="left">
        <h2>LOGIN</h2>

        <form id="loginForm">

          <div class="input-box">
            <input type="text" id="email" required>
            <label>E-mail</label>
          </div>

          <div class="input-box">
            <input type="password" id="senha" required>
            <label>Senha</label>
          </div>

          <button type="submit">ENTRAR</button>

        </form>

        <p>Não tem uma conta?<a href="cadastro.php"><span> Cadastrar-se</span></a></p>
        <?php 
        check_login_errors(); 
        ?>
      </div>

      <div class="right">
        <h1>Bem-vinda, vendedora!</h1>
        <p>Organize. Controle. Evolua.</p>
      </div>

    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
