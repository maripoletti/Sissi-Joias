const Telefone = document.querySelector("#telefone");
const TelefoneRaw = document.querySelector("#telefone_raw");
const fotoPerfil = document.querySelector("#fotoPerfil");
const previewImage = document.querySelector("#previewImage");
const iconPadrao = document.querySelector("#iconPadrao");

Telefone.addEventListener("input", () => {
    let v = Telefone.value.replace(/\D/g, "");
    if (v.length > 11) v = v.slice(0, 11);

    let vFormat = v;
    if (v.length > 10) {
        vFormat = v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
    } else if (v.length > 5) {
        vFormat = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, "($1) $2-$3");
    } else if (v.length > 2) {
        vFormat = v.replace(/^(\d{2})(\d{0,5})$/, "($1) $2");
    }

    Telefone.value = vFormat;
    TelefoneRaw.value = v;
});

fotoPerfil.addEventListener("change", function () {
    const arquivo = this.files[0];

    if (arquivo) {
        const leitor = new FileReader();

        leitor.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = "block";
            iconPadrao.style.display = "none";
        };

        leitor.readAsDataURL(arquivo);
    } else {
        previewImage.src = "";
        previewImage.style.display = "none";
        iconPadrao.style.display = "block";
    }
});