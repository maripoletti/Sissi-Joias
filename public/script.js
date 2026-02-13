const form = document.querySelector("#loginForm");

if (form) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.querySelector("#email").value.trim();
    const senha = document.querySelector("#senha").value.trim();

    if (!email || !senha) {
      alert("Preencha todos os campos!");
      return;
    }

    try {
      const response = await fetch("https://www.sissisemijoiaseacessorios.com.br/", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ email, senha })
      });

      const data = await response.json();

      if (response.ok) {
        window.location.href = "dashboard.php";
      } else {
        alert(data.message || "Email ou senha inválidos!");
      }

    } catch (error) {
      console.error("Erro ao conectar com o servidor:", error);
      alert("Erro no servidor. Tente novamente.");
    }
  });
}

const dataElemento = document.getElementById("data-atual");

if (dataElemento) {
  const hoje = new Date();

  const opcoes = {
    weekday: "long",
    day: "2-digit",
    month: "long"
  };

  let dataFormatada = hoje.toLocaleDateString("pt-BR", opcoes);

  dataFormatada =
    dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);

  dataElemento.textContent = dataFormatada;
}
