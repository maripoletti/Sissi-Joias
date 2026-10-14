const API='/api'; // ajuste a base da API se necessário
const ADMIN_ROLE=2; // valor de $_SESSION['role'] que representa o admin (ajuste)

let revendedoras=[];
let arquivos=[];
let pastaAberta=null;

const $=id=>document.getElementById(id);
const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const kb=n=>n>1e6?(n/1e6).toFixed(1)+' MB':Math.max(1,Math.round(n/1e3))+' KB';
const dataBR=d=>d.split('-').reverse().join('/');

/* ===== Sessão (vem do PHP via window.userData) ===== */
const user=window.userData||{};
const ehAdmin=()=>Number(user.role)===ADMIN_ROLE;
const meuRevId=()=>Number(user.revendedora_id);

/* ===== API ===== */
async function req(url,opts){
  const r=await fetch(url,opts);
  if(!r.ok) throw new Error('Erro '+r.status+' ao comunicar com o servidor.');
  return r.status===204?null:r.json();
}
// Envia JSON via POST (IDs vão no corpo, não na URL)
const postJSON=(url,body)=>req(url,{
  method:'POST',
  headers:{'Content-Type':'application/json'},
  body:JSON.stringify(body)
});
const erro=e=>alert(e.message||'Falha na requisição.');
const norm=a=>({id:a.id,revId:a.revendedora_id??a.revId,nome:a.nome,tamanho:a.tamanho,
  data:String(a.criado_em??a.data).slice(0,10),url:a.url});

// GET /api/pastasrevendedoras -> lista de pastas (só admin)
async function carregarPastas(){
  revendedoras=await req(`${API}/pastasrevendedoras`);
}
// POST /api/arquivos/listar  body: { revendedora_id }
async function carregarArquivos(revId){
  const lista=await postJSON(`${API}/arquivos/listar`,{revendedora_id:revId});
  arquivos=arquivos.filter(a=>a.revId!==revId).concat(lista.map(norm));
}

async function atualizar(){
  try{
    if(ehAdmin()){
      if(pastaAberta===null) await Promise.all(revendedoras.map(r=>carregarArquivos(r.id))); // contagem por pasta
      else await carregarArquivos(pastaAberta);
    }else{
      await carregarArquivos(meuRevId());
    }
  }catch(e){erro(e)}
  render();
}

/* ===== Render ===== */
function linhaArquivo(a,admin){
  return `<div class="arq">
    <span style="font-size:22px">📄</span>
    <div class="info"><div class="nome">${esc(a.nome)}</div>
    <small>${kb(a.tamanho)} · enviado em ${dataBR(a.data)}</small></div>
    <a class="btn sec" href="${esc(a.url)}" download="${esc(a.nome)}" style="text-decoration:none">Baixar</a>
    ${admin?`<button class="btn del" data-del="${a.id}">Excluir</button>`:''}
  </div>`;
}

function render(){
  ['adminLista','adminPasta','revView'].forEach(i=>$(i).classList.add('hidden'));

  if(ehAdmin()){
    if(pastaAberta===null){
      $('titulo').textContent='Pasta de Revendedoras';
      $('pastas').innerHTML=revendedoras.map(r=>{
        const n=arquivos.filter(a=>a.revId===r.id).length;
        return `<button class="pasta" data-pasta="${r.id}"><span class="ico">📁</span>
          <span><b>${esc(r.nome)}</b><small>${n} arquivo${n===1?'':'s'}</small></span></button>`;
      }).join('');
      $('adminLista').classList.remove('hidden');
    }else{
      const r=revendedoras.find(x=>x.id===pastaAberta);
      const lista=arquivos.filter(a=>a.revId===r.id);
      $('nomePasta').textContent='📁 '+r.nome;
      $('arqsAdmin').innerHTML=lista.length
        ?lista.map(a=>linhaArquivo(a,true)).join('')
        :'<p class="vazio">Nenhum arquivo nesta pasta. Use “Adicionar arquivo”.</p>';
      $('adminPasta').classList.remove('hidden');
    }
  }else{
    // Revendedora: só os arquivos dela
    $('titulo').textContent='Meus Arquivos';
    const meus=arquivos.filter(a=>a.revId===meuRevId());
    $('arqsRev').innerHTML=meus.length
      ?meus.map(a=>linhaArquivo(a,false)).join('')
      :'<p class="vazio">Você ainda não tem arquivos disponíveis.</p>';
    $('revView').classList.remove('hidden');
  }
}

/* ===== Ações do admin ===== */
$('pastas').onclick=e=>{
  const b=e.target.closest('[data-pasta]'); if(!b) return;
  pastaAberta=Number(b.dataset.pasta); atualizar();
};
$('voltar').onclick=()=>{pastaAberta=null;atualizar()};
$('addBtn').onclick=()=>$('fileInput').click();
$('fileInput').onchange=async e=>{
  if(!ehAdmin()||pastaAberta===null) return;
  const revId=pastaAberta, files=[...e.target.files];
  e.target.value='';
  // POST /api/arquivos/upload (multipart/form-data: "revendedora_id" e "arquivo")
  for(const f of files){
    try{
      const fd=new FormData();
      fd.append('revendedora_id',revId); // antes do arquivo
      fd.append('arquivo',f);
      await req(`${API}/arquivos/upload`,{method:'POST',body:fd});
    }catch(err){erro(err)}
  }
  atualizar();
};
$('arqsAdmin').onclick=async e=>{
  const b=e.target.closest('[data-del]'); if(!b||!ehAdmin()) return;
  const a=arquivos.find(x=>x.id===Number(b.dataset.del));
  if(confirm(`Remover “${a.nome}” desta revendedora?`)){
    try{
      // POST /api/arquivos/excluir  body: { id, revendedora_id }
      await postJSON(`${API}/arquivos/excluir`,{id:a.id,revendedora_id:a.revId});
      atualizar();
    }catch(err){erro(err)}
  }
};

/* ===== Início ===== */
(async()=>{
  const hoje=$('data-atual');
  if(hoje) hoje.textContent=new Date().toLocaleDateString('pt-BR',
    {weekday:'long',day:'2-digit',month:'long',year:'numeric'});
  if(ehAdmin()){
    try{await carregarPastas()}catch(e){erro(e)}
  }
  atualizar();
})();