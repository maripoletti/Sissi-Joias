const API_URL = '/api';

const meses = [
  'Jan', 'Fev', 'Mar', 'Abr',
  'Mai', 'Jun', 'Jul', 'Ago',
  'Set', 'Out', 'Nov', 'Dez'
];

const anos = [2025, 2026, 2027, 2028];

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

function criarFiltros() {

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
   CAMPANHAS FILTRO
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

    btn.innerText = campanha.nome;

    if (campanhaSelecionada === campanha.nome) {
      btn.classList.add('active');
    }

    btn.onclick = () => {

      campanhaSelecionada = campanha.nome;

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
      `${API_URL}/campanhas.php`
    );

    const data = await response.json();

    campanhas = data;

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
      `${API_URL}/top-revendedoras.php?mes=${mesSelecionado}&ano=${anoSelecionado}&campanha=${campanhaSelecionada}`
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
    
      <div class="campanha-card">

        <div class="campanha-top">

          <input
            type="text"
            value="${campanha.nome}"
            placeholder="Nome da campanha"
            onchange="
              editarCampanha(
                ${campanha.id},
                'nome',
                this.value
              )
            "
          >

          <button
            onclick="removerCampanha(${campanha.id})"
          >
            <i class="fa-regular fa-trash-can"></i>
          </button>

        </div>

        <textarea
          placeholder="Descrição"
          onchange="
            editarCampanha(
              ${campanha.id},
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
              onchange="
                editarCampanha(
                  ${campanha.id},
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
              onchange="
                editarCampanha(
                  ${campanha.id},
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

    if (campanha.id === id) {

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
   NOVA CAMPANHA
========================= */

function adicionarCampanha() {

  campanhas.push({
    id: Date.now(),
    nome: '',
    descricao: '',
    inicio: '',
    fim: ''
  });

  renderizarModalCampanhas();
}

/* =========================
   REMOVER CAMPANHA
========================= */

function removerCampanha(id) {

  campanhas = campanhas.filter(
    (campanha) => campanha.id !== id
  );

  renderizarModalCampanhas();

  renderizarCampanhasFiltro();
}

/* =========================
   SALVAR CAMPANHAS
========================= */

async function salvarCampanhas() {

  try {

    const response = await fetch(
      `${API_URL}/salvar-campanhas.php`,
      {
        method: 'POST',

        headers: {
          'Content-Type': 'application/json'
        },

        body: JSON.stringify(campanhas)
      }
    );

    const data = await response.json();

    alert(data.message);

  } catch (error) {

    console.error(
      'Erro ao salvar campanhas:',
      error
    );

    alert('Erro ao salvar campanhas');
  }
}

/* =========================
   EVENTOS
========================= */

document
  .getElementById('abrirModal')
  .onclick = () => {

    modalOverlay.classList.add('active');
};

document
  .getElementById('fecharModal')
  .onclick = () => {

    modalOverlay.classList.remove('active');
};

document
  .getElementById('novaCampanha')
  .onclick = adicionarCampanha;

document
  .getElementById('salvarCampanhas')
  .onclick = salvarCampanhas;

criarFiltros();

buscarCampanhas();

buscarRanking();