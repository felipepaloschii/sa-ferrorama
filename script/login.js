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

}