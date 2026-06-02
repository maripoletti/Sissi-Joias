const API_URL = '/api';

const meses = [
  'Jan', 'Fev', 'Mar', 'Abr',
  'Mai', 'Jun', 'Jul', 'Ago',
  'Set', 'Out', 'Nov', 'Dez'
];

const anoAtual = new Date().getFullYear();

const anos = [];

for (let i = 2026; i <= anoAtual; i++) {
  anos.push(i);
}

let campanhas = [];

let mesSelecionado = 'Dez';
let anoSelecionado = 2026;
let campanhaSelecionada = 'Todas';

const mesesContainer = document.getElementById('mesesContainer');
const anosContainer = document.getElementById('anosContainer');

const campanhasFiltroContainer = document.getElementById(
  'campanhasFiltroContainer'
);

const rankingContainer = document.getElementById(
  'rankingContainer'
);

const campanhasLista = document.getElementById(
  'campanhasLista'
);

const modalOverlay = document.getElementById(
  'modalOverlay'
);

/* =========================
   ABRIR / FECHAR MODAL
========================= */

function abrirModalCampanhas() {
  modalOverlay.classList.add('active');
}

function fecharModalCampanhas() {
  modalOverlay.classList.remove('active');
}

/* =========================
   FILTROS
========================= */

function criarFiltros() {

  if (!mesesContainer || !anosContainer) {
    return;
  }

  mesesContainer.innerHTML = '';
  anosContainer.innerHTML = '';

  meses.forEach((mes) => {

    const btn = document.createElement('button');

    btn.classList.add('filter-btn');

    btn.innerText = mes;

    if (mes === mesSelecionado) {
      btn.classList.add('active');
    }

    btn.onclick = () => {

      mesSelecionado = mes;

      criarFiltros();

      buscarRanking();
    };

    mesesContainer.appendChild(btn);
  });

  anos.forEach((ano) => {

    const btn = document.createElement('button');

    btn.classList.add('filter-btn');

    btn.innerText = ano;

    if (ano === anoSelecionado) {
      btn.classList.add('active');
    }

    btn.onclick = () => {

      anoSelecionado = ano;

      criarFiltros();

      buscarRanking();
    };

    anosContainer.appendChild(btn);
  });
}

/* =========================
   FILTRO DE CAMPANHAS
========================= */

function renderizarCampanhasFiltro() {

  campanhasFiltroContainer.innerHTML = '';

  const todasBtn = document.createElement('button');

  todasBtn.classList.add('filter-btn');

  todasBtn.innerText = 'Todas';

  if (campanhaSelecionada === 'Todas') {
    todasBtn.classList.add('active');
  }

  todasBtn.onclick = () => {

    campanhaSelecionada = 'Todas';

    renderizarCampanhasFiltro();

    buscarRanking();
  };

  campanhasFiltroContainer.appendChild(todasBtn);

  campanhas.forEach((campanha) => {

    const btn = document.createElement('button');

    btn.classList.add('filter-btn');

    btn.innerText = campanha.nome || 'Sem nome';

    if (campanhaSelecionada === campanha.id) {
      btn.classList.add('active');
    }

    btn.onclick = () => {

      campanhaSelecionada = campanha.id;

      renderizarCampanhasFiltro();

      buscarRanking();
    };

    campanhasFiltroContainer.appendChild(btn);
  });
}

/* =========================
   BUSCAR CAMPANHAS
========================= */

async function buscarCampanhas() {

  try {

    const response = await fetch(
      `${API_URL}/campanhas/get`
    );

    const data = await response.json();

    campanhas = Array.isArray(data) ? data : [];

    renderizarCampanhasFiltro();

    renderizarModalCampanhas();

  } catch (error) {

    console.error(
      'Erro ao buscar campanhas:',
      error
    );
  }
}

/* =========================
   BUSCAR RANKING
========================= */

async function buscarRanking() {

  rankingContainer.innerHTML = `
    <p class="empty-text">
      Carregando...
    </p>
  `;

  try {

    const response = await fetch(
      `${API_URL}/toprevendedoras?campanha=${campanhaSelecionada}`
    );

    const data = await response.json();

    if (!data.length) {

      rankingContainer.innerHTML = `
        <p class="empty-text">
          Nenhuma revendedora encontrada.
        </p>
      `;

      return;
    }

    rankingContainer.innerHTML = '';

    data.forEach((item, index) => {

      rankingContainer.innerHTML += `
      
        <div class="card-revendedora">

          <div>

            <span class="ranking-posicao">
              #${index + 1}
            </span>

            <h3 class="nome-revendedora">
              ${item.nome}
            </h3>

          </div>

          <div class="valor-revendedora">

            R$ ${item.total}

          </div>

        </div>
      `;
    });

  } catch (error) {

    console.error(
      'Erro ao buscar ranking:',
      error
    );

    rankingContainer.innerHTML = `
      <p class="empty-text">
        Erro ao carregar ranking.
      </p>
    `;
  }
}

/* =========================
   MODAL CAMPANHAS
========================= */

function renderizarModalCampanhas() {

  campanhasLista.innerHTML = '';

  campanhas.forEach((campanha) => {

    campanhasLista.innerHTML += `
    
      <div class="campanha-card" data-id="${campanha.id}">

        <div class="campanha-top">

          <input
            type="text"
            value="${campanha.nome || ''}"
            placeholder="Nome da campanha"
            data-campo="nome"
            oninput="
              editarCampanha(
                '${campanha.id}',
                'nome',
                this.value
              )
            "
          >

          <button
            onclick="removerCampanha('${campanha.id}')"
          >
            <i class="fa-regular fa-trash-can"></i>
          </button>

        </div>

        <textarea
          placeholder="Descrição"
          data-campo="descricao"
          oninput="
            editarCampanha(
              '${campanha.id}',
              'descricao',
              this.value
            )
          "
        >${campanha.descricao || ''}</textarea>

        <div class="datas">

          <div>

            <label>Início</label>

            <input
              type="date"
              value="${campanha.inicio || ''}"
              data-campo="inicio"
              oninput="
                editarCampanha(
                  '${campanha.id}',
                  'inicio',
                  this.value
                )
              "
            >

          </div>

          <div>

            <label>Fim</label>

            <input
              type="date"
              value="${campanha.fim || ''}"
              data-campo="fim"
              oninput="
                editarCampanha(
                  '${campanha.id}',
                  'fim',
                  this.value
                )
              "
            >

          </div>

        </div>

      </div>
    `;
  });
}

/* =========================
   EDITAR CAMPANHA
========================= */

function editarCampanha(id, campo, valor) {

  campanhas = campanhas.map((campanha) => {

    if (String(campanha.id) === String(id)) {

      return {
        ...campanha,
        [campo]: valor
      };
    }

    return campanha;
  });

  renderizarCampanhasFiltro();
}

/* =========================
   SINCRONIZAR DADOS DA TELA
========================= */

function sincronizarCampanhasDaTela() {

  const cards = document.querySelectorAll('.campanha-card');

  cards.forEach((card) => {

    const id = card.getAttribute('data-id');

    const campanha = campanhas.find(
      (item) => String(item.id) === String(id)
    );

    if (!campanha) {
      return;
    }

    const nome = card.querySelector('[data-campo="nome"]');
    const descricao = card.querySelector('[data-campo="descricao"]');
    const inicio = card.querySelector('[data-campo="inicio"]');
    const fim = card.querySelector('[data-campo="fim"]');

    campanha.nome = nome ? nome.value : '';
    campanha.descricao = descricao ? descricao.value : '';
    campanha.inicio = inicio ? inicio.value : '';
    campanha.fim = fim ? fim.value : '';
  });
}

/* =========================
   NOVA CAMPANHA
========================= */

function adicionarCampanha() {

  campanhas.push({
    id: Date.now(),
    nome: '',
    descricao: '',
    inicio: '',
    fim: '',
    nova: true
  });

  renderizarModalCampanhas();
}

/* =========================
   REMOVER CAMPANHA
========================= */

async function removerCampanha(id) {

  const campanhaEncontrada = campanhas.find(
    (campanha) => String(campanha.id) === String(id)
  );

  if (campanhaEncontrada && campanhaEncontrada.nova) {

    campanhas = campanhas.filter(
      (campanha) => String(campanha.id) !== String(id)
    );

    renderizarModalCampanhas();
    renderizarCampanhasFiltro();

    return;
  }

  try {

    const response = await fetch(`${API_URL}/campanhas/del`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id })
    });

    const data = await response.json();

    if (data.success) {

      campanhas = campanhas.filter(
        (campanha) => String(campanha.id) !== String(id)
      );

      renderizarModalCampanhas();
      renderizarCampanhasFiltro();

    } else {

      alert(data.message || 'Erro ao remover campanha');
    }

  } catch (error) {

    console.error('Erro ao remover campanha:', error);

    alert('Erro ao remover campanha');
  }
}

/* =========================
   SALVAR CAMPANHAS
========================= */

async function salvarCampanhas() {

  sincronizarCampanhasDaTela();

  const botaoSalvar = document.getElementById('salvarCampanhas');

  try {

    botaoSalvar.disabled = true;
    botaoSalvar.innerHTML = `
      <i class="fa-solid fa-spinner fa-spin"></i>
      Salvando...
    `;

    const campanhasParaSalvar = campanhas.map((campanha) => {
      return {
        id: campanha.id,
        nome: campanha.nome,
        descricao: campanha.descricao,
        inicio: campanha.inicio,
        fim: campanha.fim
      };
    });

    const response = await fetch(
      `${API_URL}/campanhas/salvar`,
      {
        method: 'POST',

        headers: {
          'Content-Type': 'application/json'
        },

        body: JSON.stringify(campanhasParaSalvar)
      }
    );

    const texto = await response.text();

    const data = texto ? JSON.parse(texto) : {};

    if (!response.ok || data.success === false) {

      alert(data.message || 'Erro ao salvar campanhas');

      return;
    }

    fecharModalCampanhas();

    await buscarCampanhas();

    await buscarRanking();

  } catch (error) {

    console.error(
      'Erro ao salvar campanhas:',
      error
    );

    alert('Erro ao salvar campanhas');

  } finally {

    botaoSalvar.disabled = false;

    botaoSalvar.innerHTML = `
      <i class="fa-regular fa-floppy-disk"></i>
      Salvar
    `;
  }
}

/* =========================
   EVENTOS
========================= */

document
  .getElementById('abrirModal')
  .onclick = abrirModalCampanhas;

document
  .getElementById('fecharModal')
  .onclick = fecharModalCampanhas;

document
  .getElementById('novaCampanha')
  .onclick = adicionarCampanha;

document
  .getElementById('salvarCampanhas')
  .onclick = salvarCampanhas;

/* =========================
   INICIAR PÁGINA
========================= */

criarFiltros();

buscarCampanhas();

buscarRanking();