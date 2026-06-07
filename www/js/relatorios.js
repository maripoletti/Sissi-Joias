async function carregarRelatorios(){

    const res = await fetch("/api/relatorios");
    const data = await res.json();


    const total = Number(data.total);
    const qtd = Number(data.qtd_vendas);
    const medio = qtd ? total / qtd : 0;

    document.querySelectorAll(".rel-card .rel-value")[0].textContent = "R$ " + total.toFixed(2);
    document.querySelectorAll(".rel-card .rel-value")[1].textContent = qtd;
    document.querySelectorAll(".rel-card .rel-value")[2].textContent = "R$ " + medio.toFixed(2);
    document.querySelectorAll(".rel-card .rel-value")[3].textContent = data.vendedoras_ativas ?? 0;


    if(data.estoque){

        document.getElementById("totalPecas").textContent = data.estoque.total_pecas ?? 0;
        document.getElementById("totalUnidades").textContent = data.estoque.total_unidades ?? 0;
        document.getElementById("totalAlertas").textContent = data.estoque.alertas ?? 0;

        const valorEstoque = Number(data.estoque.valor ?? 0);
        document.getElementById("valorEstoque").textContent = "R$ " + valorEstoque.toFixed(2);

    }


    const sellers = document.querySelector(".rel-sellers");
    sellers.innerHTML = "";

    if(data.vendedoras && data.vendedoras.length){

        const maiorVenda = Math.max(...data.vendedoras.map(v => Number(v.valor)));

        data.vendedoras
          .sort((a,b)=> Number(b.valor) - Number(a.valor))
          .forEach((v,i)=>{

              const valor = Number(v.valor);
              const percentual = maiorVenda ? (valor / maiorVenda) * 100 : 0;

              let rankClass = "";

              if(i === 0) rankClass = "gold";
              else if(i === 1) rankClass = "purple";
              else if(i === 2) rankClass = "purple2";

              const el = document.createElement("div");
              el.className = "seller";

              el.innerHTML = `
                  <div class="rank ${rankClass}">${i + 1}</div>
                  <div class="info">
                      <strong>${v.nome}</strong>
                      <div class="seller-track">
                          <div class="seller-fill ${rankClass}" style="width:${percentual}%"></div>
                      </div>
                  </div>
                  <div class="money">R$ ${valor.toFixed(2)}</div>
              `;

              sellers.appendChild(el);

          });

    }


    const bars = document.querySelector(".rel-bars");
    bars.innerHTML = "";

    if(data.produtos_mais_vendidos && data.produtos_mais_vendidos.length){

        const maior = Math.max(...data.produtos_mais_vendidos.map(p => Number(p.vendidos)));

        data.produtos_mais_vendidos.forEach(p => {

            const vendidos = Number(p.vendidos);
            const percent = maior ? (vendidos / maior) * 100 : 0;

            const el = document.createElement("div");
            el.className = "rel-bar";

            el.innerHTML = `
                <span>${p.nome}</span>
                <div class="rel-track">
                    <div class="rel-fill roxo" style="width:${percent}%"></div>
                </div>
            `;

            bars.appendChild(el);

        });

    }

    const pay = document.querySelector(".rel-pay-grid");
    pay.innerHTML = "";

    if(data.pagamentos && data.pagamentos.length){

        data.pagamentos.forEach(p => {

            const valor = Number(p.valor);
            const percentual = total ? (valor / total) * 100 : 0;

            const el = document.createElement("div");
            el.className = "pay-card";

            el.innerHTML = `
                <span class="pay-title">${p.tipo}</span>
                <h4>R$ ${valor.toFixed(2)}</h4>
                <p>${percentual.toFixed(0)}% do total</p>
            `;

            pay.appendChild(el);

        });

    }

}

document.addEventListener("DOMContentLoaded", carregarRelatorios);
