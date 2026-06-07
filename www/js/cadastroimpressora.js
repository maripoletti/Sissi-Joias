const tabs = document.querySelectorAll(".ni-tab");
let tipo = "nf";

const form = document.getElementById("printerForm");

const hiddenType = document.createElement("input");
hiddenType.type = "hidden";
hiddenType.name = "tipo";
hiddenType.value = tipo;
form.appendChild(hiddenType);

tabs.forEach(btn => {
  btn.addEventListener("click", () => {
    tabs.forEach(b => {
      b.classList.remove("active");
      b.setAttribute("aria-selected","false");
    });

    btn.classList.add("active");
    btn.setAttribute("aria-selected","true");

    tipo = btn.dataset.type;
    hiddenType.value = tipo;
  });
});

function validarIP(ip) {
  if (!ip) return true;

  const partes = ip.split(".");
  if (partes.length !== 4) return false;

  return partes.every(p => {
    const n = Number(p);
    return p !== "" && n >= 0 && n <= 255;
  });
}

function validarPorta(porta) {
  if (!porta) return true;

  const n = Number(porta);
  return Number.isInteger(n) && n >= 0 && n <= 65535;
}

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const fd = new FormData(form);
  const data = Object.fromEntries(fd.entries());

  if (!validarIP(data.ip)) {
    alert("IP inválido");
    return;
  }

  if (!validarPorta(data.porta)) {
    alert("Porta inválida");
    return;
  }

  try {

    const res = await fetch("/api/cadastroimpressora", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    });

    const json = await res.json();

    form.reset();
    hiddenType.value = tipo;

  } catch (err) {
    console.error(err);
  }
});