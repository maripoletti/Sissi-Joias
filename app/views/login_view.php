<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Sissi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/login.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="container">
    <div class="card">

      <div class="left">
        <h2>LOGIN</h2>

        <form action="/login" method="post">

          <div class="input-box">
            <input type="email" name="email" id="email" required>
            <label for="email">E-mail</label>
          </div>

          <div class="input-box">
            <input type="password" name="pwd" id="senha" required>
            <label for="senha">Senha</label>
          </div>

          <button type="submit">ENTRAR</button>

        </form>

        <div class="links-acesso">
          <p>Não tem uma conta? <a href="/cadastro">Cadastrar-se</a></p>
          <p>Esqueceu sua senha? <a href="/esquecisenha">Clique aqui</a></p>
        </div>

        <?php
          if (isset($_SESSION['errors_login'])) {
            $errors = $_SESSION['errors_login'];
            foreach ($errors as $error) {
              echo '<p>' . $error . '</p><br>';
            }
            unset($_SESSION['errors_login']);
          }
        ?>
      </div>

      <div class="right">
        <h1>Bem-vinda, vendedora!</h1>
        <p>Organize. Controle. Evolua.</p>

        <div class="social-right">

          <!-- WhatsApp -->
          <a href="https://wa.me/5551996937109" target="_blank" class="icon-social" aria-label="WhatsApp">
            <svg viewBox="0 0 32 32">
              <path fill="currentColor" d="M16 .396C7.163.396 0 7.559 0 16.396c0 2.882.756 5.694 2.197 8.168L0 32l7.636-2.154a15.937 15.937 0 0 0 8.364 2.301c8.837 0 16-7.163 16-16S24.837.396 16 .396zm0 29.09a13.04 13.04 0 0 1-6.64-1.8l-.475-.28-4.532 1.278 1.208-4.416-.309-.453a13.042 13.042 0 1 1 10.748 5.671zm7.2-9.785c-.393-.196-2.327-1.15-2.688-1.282-.361-.131-.623-.196-.885.197s-1.016 1.282-1.246 1.544c-.229.262-.459.295-.852.098-.393-.197-1.659-.612-3.16-1.951-1.167-1.04-1.955-2.323-2.184-2.716-.229-.393-.024-.605.173-.802.178-.178.393-.459.59-.688.197-.229.262-.393.393-.655.131-.262.066-.492-.033-.688-.098-.197-.885-2.13-1.213-2.917-.32-.767-.646-.663-.885-.675l-.754-.013c-.262 0-.688.098-1.049.492-.361.393-1.377 1.344-1.377 3.278 0 1.934 1.41 3.802 1.607 4.065.197.262 2.776 4.24 6.73 5.946.941.406 1.676.649 2.249.831.945.3 1.806.258 2.487.156.758-.113 2.327-.951 2.655-1.868.328-.917.328-1.704.229-1.868-.098-.164-.361-.262-.754-.459z"/>
            </svg>
          </a>

          <!-- Instagram -->
          <a href="https://www.instagram.com/sissi_semijoiaseacessorios/" target="_blank" class="icon-social" aria-label="Instagram">
            <svg viewBox="0 0 24 24">
              <path fill="currentColor" d="M7.75 2C4.678 2 2 4.678 2 7.75v8.5C2 19.322 4.678 22 7.75 22h8.5c3.072 0 5.75-2.678 5.75-5.75v-8.5C22 4.678 19.322 2 16.25 2h-8.5zm0 2h8.5C18.216 4 20 5.784 20 7.75v8.5c0 1.966-1.784 3.75-3.75 3.75h-8.5C5.784 20 4 18.216 4 16.25v-8.5C4 5.784 5.784 4 7.75 4zm4.25 2.5A5.5 5.5 0 1 0 17.5 12 5.507 5.507 0 0 0 12 6.5zm0 2A3.5 3.5 0 1 1 8.5 12 3.504 3.504 0 0 1 12 8.5zm5.75-.75a1 1 0 1 0 1 1 1 1 0 0 0-1-1z"/>
            </svg>
          </a>

        </div>
      </div>

    </div>
  </div>

</body>
</html>