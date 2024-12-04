document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript carregado com sucesso!"); // Verifique se isso aparece no console.

    const form = document.querySelector("form");

    if (form) {
        form.addEventListener("submit", function (event) {
            const radios = form.querySelectorAll('input[name="score"]');
            let isScoreSelected = Array.from(radios).some(radio => radio.checked);

            if (!isScoreSelected) {
                event.preventDefault();
                alert("Por favor, selecione uma pontuação.");
            }
        });
    } else {
        console.error("Formulário não encontrado!");
    }
});