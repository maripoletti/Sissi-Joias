<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pasta de Revendedoras | Sissi Semi Joias e Acessórios</title>

    <!-- Estilos globais -->
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/estilo2.css">

    <!-- Estilo específico desta tela -->
    <link rel="stylesheet" href="styles/pastasrevend.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
</head>

<body>

    <div class="container">

        <div class="card pastas-layout">

            <!-- SIDEBAR GLOBAL -->
            <aside class="sidebar"></aside>

            <!-- CONTEÚDO PRINCIPAL -->
            <main class="main">

                <!-- CABEÇALHO -->
                <header class="top">

                    <div>
                        <h1 id="titulo">Pasta de Revendedoras</h1>
                        <span class="subtitle" id="data-atual"></span>
                    </div>

                    <div class="top-actions">

                        <!-- SOMENTE PARA TESTES -->
                        <!-- No sistema real, o perfil vem do login -->
                        <div class="demo">
                            <label for="perfil">Testando como:</label>

                            <select
                                id="perfil"
                                aria-label="Perfil de teste"
                            ></select>
                        </div>

                        <!-- SAIR -->
                        <a href="/logout" class="btn-sair">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Sair
                        </a>

                    </div>

                </header>

                <!-- CONTEÚDO DA PÁGINA -->
                <section class="pastas-conteudo">

                    <!-- ADMIN: LISTA DE PASTAS -->
                    <section id="adminLista" class="hidden">

                        <div class="pastas" id="pastas"></div>

                    </section>

                    <!-- ADMIN: CONTEÚDO DE UMA PASTA -->
                    <section id="adminPasta" class="hidden">

                        <div class="bar">

                            <button
                                type="button"
                                class="btn sec"
                                id="voltar"
                            >
                                <i class="fa-solid fa-arrow-left"></i>
                                Voltar
                            </button>

                            <h3 id="nomePasta"></h3>

                            <button
                                type="button"
                                class="btn"
                                id="addBtn"
                            >
                                <i class="fa-solid fa-plus"></i>
                                Adicionar arquivo
                            </button>

                            <input
                                type="file"
                                id="fileInput"
                                multiple
                                class="hidden"
                            >

                        </div>

                        <div id="arqsAdmin"></div>

                    </section>

                    <!-- REVENDEDORA: VISUALIZAÇÃO DOS ARQUIVOS -->
                    <section id="revView" class="hidden">

                        <div id="arqsRev"></div>

                    </section>

                </section>

            </main>

        </div>

    </div>

    <!-- DADOS DA SESSÃO -->
    <script>
        window.userData = {
            nome: <?= json_encode(
                $_SESSION['usuario_nome'] ?? 'Usuário'
            ) ?>,

            role: <?= json_encode(
                $_SESSION['role'] ?? 0
            ) ?>
        };
    </script>

    <!-- JAVASCRIPT GLOBAL -->
    <script src="js/global.js"></script>

    <!-- JAVASCRIPT ESPECÍFICO DA PÁGINA -->
    <script src="js/pastasrevend.js"></script>

</body>
</html>