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

      <h1>Trocar Senha</h1>

      <form action="/trocarsenha" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
        
        <label>Senha</label>
        <input type="password" name="pwd" placeholder="Crie uma senha forte" required>

        <label>Confirmar Senha</label>
        <input type="password" name="pwdRepeat" placeholder="Confirme sua senha" required>

        <button type="submit">Trocar senha</button>
      </form>

      <a href="/login" class="back-link">← Voltar para o login</a>
    </div>
  </div>

</body>
</html>