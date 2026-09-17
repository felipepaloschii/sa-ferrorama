const max_tentativas = 5;
const tempo_bloqueio = 60000;


document.getElementById("form-login").onsubmit = (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const senha = document.getElementById("senha").value;
    const mensagem = document.getElementById("mensagem");

    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    mensagem.textContent = "";
    mensagem.className = "";


    const bloqueadoate = Number(localStorage.getItem("bloqueadoate")) || 0;
    if (Date.now() < bloqueadoate) {
        const tempoRestante = Math.ceil((bloqueadoate - Date.now()) / 1000);
        mensagem.textContent = `Você está temporariamente bloqueado. Tente novamente em ${tempoRestante} segundos.`;
        mensagem.className = "erro";
        return;
    }
}