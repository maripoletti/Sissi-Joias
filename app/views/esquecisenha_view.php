<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Esqueci Minha Senha</title>
  <link rel="stylesheet" href="styles/esquecisenha.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="forgot-container">
    <div class="forgot-card">
      <div class="icon-box">🔐</div>

      <h1>Esqueceu sua senha?</h1>
      <p>
        Fica tranquila(o), acontece! <br>
        Digite seu e-mail abaixo para receber o link de redefinição.
      </p>

      <form>
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

        <button type="submit">Enviar link de recuperação</button>
      </form>

      <a href="/login" class="back-link">← Voltar para o login</a>
    </div>
  </div>

</body>
</html>