<?php

declare(strict_types= 1);

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

        <form action="login" method="post">

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

        <p>Não tem uma conta? <a href="/cadastro">Cadastrar-se</a></p>
        

        <?php
          if(isset($_SESSION['errors_login'])) {
            $errors = $_SESSION['errors_login'];
            foreach($errors as $error) {
              echo '<p>'. $error .'</p><br>';
            }
            unset($_SESSION['errors_login']);
          }
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