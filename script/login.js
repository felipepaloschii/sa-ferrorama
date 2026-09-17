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

    if (!email || !senha) {
        mensagem.textContent = "Preencha todos os campos.";
        mensagem.className = "erro";
        return;
    }

    if (!regexEmail.test(email)) {
        mensagem.textContent = "Digite um e-mail válido.";
        mensagem.className = "erro";
        return;
    }

    const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];
    const usuario = usuarios.find(u => u.email === email && u.senha === senha);


    if (!usuario) {
        const tentativas = (Number(localStorage.getItem("tentativas")) || 0) + 1;

        if (tentativas >= max_tentativas) {
            localStorage.setItem("bloqueadoate", Date.now() + tempo_bloqueio);
            localStorage.removeItem("tentativas");
            mensagem.textContent = "Você excedeu o número máximo de tentativas. Tente novamente em 1 minuto.";
            mensagem.className = "erro";
        } else {
            localStorage.setItem("tentativas", tentativas);
            mensagem.textContent = `E-mail ou senha incorretos. Tentativa ${tentativas} de ${max_tentativas}.`;
            mensagem.className = "erro";
        }

        mensagem.className = "erro";
        return;
    }
     
    localStorage.removeItem("tentativas");
    localStorage.removeItem("bloqueadoate");
    localStorage.setItem("usuarioLogado", JSON.stringify(usuario));
    window.location.href = "index.php";
};

const toggle = document.getElementById("toggle");
if (toggle) {
    toggle.style.cursor = "pointer";
    toggle.onclick = () => {
        window.location.href = "public/usuario/tela-cadastro-usuario.php";
    };
}