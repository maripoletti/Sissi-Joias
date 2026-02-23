<?php
require_once '../config/session.config.php';
require_once '../app/views/index_view.php';



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

        <form action="login.php" method="post">

          <div class="input-box">
            <input type="email" name="email" id="email" required>
            <label>E-mail</label>
          </div>

          <div class="input-box">
            <input type="password" name="pwd" id="senha" required>
            <label>Senha</label>
          </div>

          <button>ENTRAR</button>

        </form>

        <p>Não tem uma conta? <a href="cadastro.php">Cadastrar-se</a></p>
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

</body>
</html>