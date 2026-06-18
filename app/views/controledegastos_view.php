<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios - Relatórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/controledegastos.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
  <div class="container">
    <div class="card controledegastos">

        <aside class="sidebar"></aside>

        <main class="main controledegastos-page">

            <header class="cg-titulo">
                <h1>Controle de Gastos</h1>
            </header>
    
            <section class="cg-itens">
                <div class="cg-item" id="item-insumo">
                    <p class="cg-texto">Gasto em insumos</p>
                    <h2 class="cg-dado">0</h2>
                    <div class="cg-rodape">
                        <p class="cg-data data">xx-xx-xxxx a xx-xx-xxxx</p>
                        <button type="button" class="trocar-intervalo">Trocar intervalo</button>
                    </div>
                </div>

                <div class="cg-item" id="item-banho">
                    <div class="cg-topo">
                        <p class="cg-texto">Gasto em banho</p>

                        <div class="cg-extra">
                            <button type="button" class="trocar-banhoMetal">Trocar metal do banho</button>
                            <p class="metal-atual">Metal atual:</p>
                            <p class="metal-valor">Todos</p>
                        </div>
                    </div>

                    <h2 class="cg-dado">0</h2>

                    <div class="cg-rodape">
                        <p class="cg-data data">xx-xx-xxxx a xx-xx-xxxx</p>
                        <button type="button" class="trocar-intervalo">Trocar intervalo</button>
                    </div>
                </div>

                <div class="cg-item" id="item-entrada">
                    <p class="cg-texto">Entrada de peças por intervalo</p>
                    <h2 class="cg-dado">0</h2>
                    <div class="cg-rodape">
                        <p class="cg-data data">xx-xx-xxxx a xx-xx-xxxx</p>
                        <button type="button" class="trocar-intervalo">Trocar intervalo</button>
                    </div>
                </div>

                <div class="cg-item" id="item-saida">
                    <p class="cg-texto">Saída de peças por intervalo</p>
                    <h2 class="cg-dado">0</h2>
                    <div class="cg-rodape">
                        <p class="cg-data data">xx-xx-xxxx a xx-xx-xxxx</p>
                        <button type="button" class="trocar-intervalo">Trocar intervalo</button>
                    </div>
                </div>
            </section>

            <section class="modals">
                <div class="modal" id="modal-intervalo" style="display:none;">
                    <div class="modal-real">
                        <div class="form-cabecalho">
                            <h2 id="modal-tituloIntervalo">?</h2>
                            <button class="modal-fechar">×</button>
                        </div>

                        <div class="form-grupoIntervalo">
                            <div class="form-datas">
                                <label>Data inicial:</label>
                                <input type="date" id="dataInicial">
                            </div>
                            
                            <div class="form-datas">
                                <label>Data final:</label>
                                <input type="date" id="dataFinal">
                            </div>
                        </div>

                        <button onclick="trocarIntervalo()">Salvar</button>
                    </div>
                </div>

                <div class="modal" id="modal-banho" style="display:none;">
                    <div class="modal-real">
                        <div class="form-cabecalho">
                            <h2 id="modal-tituloMetal">Trocar metal do banho</h2>
                            <button class="modal-fechar">×</button>
                        </div>

                        <div class="form-grupoMetal">
                            <label>Nome do metal</label>
                            <input type="text" id="metalBanho" placeholder="Ex: Ouro">
                            <div id="resultado-metalBanho" style="display: none;">

                            </div>
                        </div>

                        <button onclick="trocarMetal()">Salvar</button>
                    </div>
                </div>

                <div class="modal" id="modal-gastoAdicionar" style="display: none;">
                    <div class="modal-real">
                        <div class="form-cabecalho">
                            <h2 id="modal-tituloAdicionarGasto">Adicionar gasto</h2>
                            <button class="modal-fechar">×</button>
                        </div>

                        <div class="form-dados">
                            <div class="form-grupoGasto">
                                <label>Nome do gasto</label>
                                <input id="input-add-nome-gasto" type="text">
                            </div>

                            <div class="form-grupoGasto">
                                <label>Preço do gasto</label>
                                <input id="input-add-custo-gasto" type="number" step="0.01">
                            </div>

                            <div class="form-grupoGasto">
                                <label>Quando ocorreu</label>
                                <input id="input-add-data-gasto" type="date">
                            </div>
                        </div>

                        <button id="botaoGastoAdicionar">Adicionar</button>
                    </div>
                </div>

                <div class="modal" id="modal-gastoEditar" style="display: none;">
                    <div class="modal-real">
                        <div class="form-cabecalho">
                            <h2 id="modal-tituloAdicionarGasto">Editar gasto</h2>
                            <button class="modal-fechar">×</button>
                        </div>

                        <div class="form-dados">
                            <div class="form-grupoGasto">
                                <label>Nome do gasto</label>
                                <input id="input-nome-gasto" type="text">
                            </div>

                            <div class="form-grupoGasto">
                                <label>Preço do gasto</label>
                                <input id="input-custo-gasto" type="number" step="0.01">
                            </div>

                            <div class="form-grupoGasto">
                                <label>Quando ocorreu</label>
                                <input id="input-data-gasto" type="date">
                            </div>
                        </div>

                        <button id="botaoGastoEditar">Salvar</button>
                    </div>
                </div>
            </section>

            <section id="gastos-dinamicos">
                <div class="topo">
                    <div class="campo-custoTotal">
                        <label>Custo Total</label>
                        <input id="custoTotal" type="text" readonly>
                    </div>
                    <div class="realizar">
                        <div class="campo-sincronizar">
                            <label>Considerar os gastos acíma?</label>
                            <select id="sincronizarGastos">
                                <option value="sim">Sim</option>
                                <option value="nao" selected>Não</option>
                            </select>
                        </div>

                        <div class="intervalo">
                            <button type="button" id="gasto-intervalo" onclick="abrirModalIntervaloGerais()">Intervalo</button>
                            <p class="data">xx-xx-xxxx a xx-xx-xxxx</p>
                        </div>

                        <button type="button" id="gasto-add" onclick="abrirModalAdicionarGasto()">Adicionar gasto</button>
                    </div>
                </div>

                <div class="mainGastos">
                    <div class="gastos-header">
                        <p>Nome</p>
                        <p>Custo</p>
                        <p>Data</p>
                        <p>Ações</p>
                    </div>

                    <div id="lista-gastos">

                    </div>
                </div>
            </section>
        </main>

    </div>
  </div>
  <script src="js/controledegastos.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>
</html>