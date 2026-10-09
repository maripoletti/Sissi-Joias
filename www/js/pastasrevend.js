/* ===== DADOS (em memória; troque por chamadas à sua API/banco) =====
   Tabela sugerida: arquivos_revendedora(id, revendedora_id, nome, url, tamanho, criado_em) */
const revendedoras=[
  {id:1,nome:'Ana Souza'},{id:2,nome:'Beatriz Silva'},
  {id:3,nome:'Camila Oliveira'},{id:4,nome:'Daniela Santos'}
];
let arquivos=[
  {id:1,revId:1,nome:'Catálogo Outubro.pdf',tamanho:2400000,data:'2026-10-02',url:'#'},
  {id:2,revId:1,nome:'Tabela de preços.xlsx',tamanho:88000,data:'2026-10-05',url:'#'},
  {id:3,revId:3,nome:'Contrato assinado.pdf',tamanho:310000,data:'2026-09-28',url:'#'}
];
let prox=10, pastaAberta=null;
 
const $=id=>document.getElementById(id);
const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const kb=n=>n>1e6?(n/1e6).toFixed(1)+' MB':Math.max(1,Math.round(n/1e3))+' KB';
const dataBR=d=>d.split('-').reverse().join('/');
const perfil=()=>$('perfil').value; // 'admin' ou id da revendedora
 
/* ===== Perfil de teste ===== */
$('perfil').innerHTML='<option value="admin">Admin</option>'+
  revendedoras.map(r=>`<option value="${r.id}">${esc(r.nome)} (revendedora)</option>`).join('');
$('perfil').onchange=()=>{pastaAberta=null;render()};
 
/* ===== Render ===== */
function linhaArquivo(a,admin){
  return `<div class="arq">
    <span style="font-size:22px">📄</span>
    <div class="info"><div class="nome">${esc(a.nome)}</div>
    <small>${kb(a.tamanho)} · enviado em ${dataBR(a.data)}</small></div>
    <a class="btn sec" href="${a.url}" download="${esc(a.nome)}" style="text-decoration:none">Baixar</a>
    ${admin?`<button class="btn del" data-del="${a.id}">Excluir</button>`:''}
  </div>`;
}
 
function render(){
  ['adminLista','adminPasta','revView'].forEach(i=>$(i).classList.add('hidden'));
  const p=perfil();
 
  if(p==='admin'){
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
        :'<p class="vazio">Nenhum arquivo nesta pasta. Use “+ Adicionar arquivo”.</p>';
      $('adminPasta').classList.remove('hidden');
    }
  }else{
    // Revendedora: só os arquivos dela, como página
    $('titulo').textContent='Meus Arquivos';
    const meus=arquivos.filter(a=>a.revId===Number(p));
    $('arqsRev').innerHTML=meus.length
      ?meus.map(a=>linhaArquivo(a,false)).join('')
      :'<p class="vazio">Você ainda não tem arquivos disponíveis.</p>';
    $('revView').classList.remove('hidden');
  }
}
 
/* ===== Ações do admin ===== */
$('pastas').onclick=e=>{
  const b=e.target.closest('[data-pasta]'); if(!b) return;
  pastaAberta=Number(b.dataset.pasta); render();
};
$('voltar').onclick=()=>{pastaAberta=null;render()};
$('addBtn').onclick=()=>$('fileInput').click();
$('fileInput').onchange=e=>{
  if(perfil()!=='admin'||pastaAberta===null) return;
  [...e.target.files].forEach(f=>{
    // Real: enviar f via FormData ao servidor e salvar o registro com revendedora_id
    arquivos.push({id:prox++,revId:pastaAberta,nome:f.name,tamanho:f.size,
      data:new Date().toISOString().slice(0,10),url:URL.createObjectURL(f)});
  });
  e.target.value=''; render();
};
$('arqsAdmin').onclick=e=>{
  const b=e.target.closest('[data-del]'); if(!b||perfil()!=='admin') return;
  const a=arquivos.find(x=>x.id===Number(b.dataset.del));
  if(confirm(`Remover “${a.nome}” desta revendedora?`)){
    // Real: DELETE /api/revendedoras/:revId/arquivos/:id
    arquivos=arquivos.filter(x=>x.id!==a.id); render();
  }
};
 
render();