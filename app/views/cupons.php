<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o painel já inclui a sidebar, não a inclua novamente.
// require_once __DIR__ . '/sidebar.php';
?>

<link rel="stylesheet" href="styles/cupons.css">

<main class="cupons-page">
    <header class="cupons-header">
        <h1>Cupons</h1>
        <p>Gerencie os descontos das revendedoras da Sissi Semi Joias.</p>
    </header>

    <nav class="cupons-tabs" aria-label="Seções de cupons">
        <button class="cupons-tab active" type="button"
                data-tab="gerenciar" aria-selected="true">
            Gerenciar cupons
        </button>

        <button class="cupons-tab" type="button"
                data-tab="revendedora" aria-selected="false">
            Visão da revendedora
        </button>
    </nav>

    <section class="cupons-card" id="tab-gerenciar">
        <div class="cupons-section-title">
            <h2>Cupons por revendedora</h2>
            <p>Escolha uma revendedora e gerencie seus cupons.</p>
        </div>

        <div class="cupons-field">
            <label for="revendedora-admin">ID da revendedora</label>

            <select id="revendedora-admin">
                <!-- Substitua pelos IDs reais do banco de dados. -->
                <option value="alice">Alice (exemplo)</option>
                <option value="bruna">Bruna (exemplo)</option>
                <option value="carolina">Carolina (exemplo)</option>
            </select>
        </div>

        <div class="cupons-table-wrapper">
            <table class="cupons-table">
                <thead>
                    <tr>
                        <th>Cupom</th>
                        <th>Desconto</th>
                        <th>Disponíveis</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody id="lista-cupons-admin">
                    <tr>
                        <td colspan="4">Carregando cupons...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="cupons-novo">
            <h3>Novo cupom</h3>

            <form id="form-novo-cupom">
                <div class="cupons-field">
                    <label for="desconto-cupom">Desconto (%)</label>
                    <input type="number" id="desconto-cupom"
                           min="1" max="100" step="1"
                           value="10" required>
                </div>

                <div class="cupons-field">
                    <label for="quantidade-cupom">Quantidade</label>
                    <input type="number" id="quantidade-cupom"
                           min="1" max="10000" step="1"
                           value="1" required>
                </div>

                <button type="submit"
                        class="cupons-btn cupons-btn-primary">
                    Adicionar cupom
                </button>
            </form>
        </div>

        <p class="cupons-feedback" id="feedback-admin"
           role="status" aria-live="polite"></p>
    </section>

    <section class="cupons-card" id="tab-revendedora" hidden>
        <div class="cupons-section-title">
            <h2>Meus cupons</h2>
            <p>Consulte os cupons disponíveis para sua conta.</p>
        </div>

        <div class="cupons-aviso">
            Os cupons são carregados pela sessão da revendedora autenticada.
        </div>

        <div class="cupons-field">
            <label for="revendedora-cliente">Conta</label>
            <select id="revendedora-cliente" disabled>
                <option value="">Conta autenticada</option>
            </select>
        </div>

        <div class="cupons-resumo" id="resumo-cupons"></div>

        <div class="cupons-usados">
            <h3>Cupons que já usei</h3>

            <div class="cupons-table-wrapper">
                <table class="cupons-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Cupom</th>
                            <th>Original</th>
                            <th>Final</th>
                        </tr>
                    </thead>

                    <tbody id="lista-cupons-usados"></tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<script src="js/cupons.js" defer></script>