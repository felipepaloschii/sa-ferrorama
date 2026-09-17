<?php 
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: public/usuario/tela-login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - PIA Enterprise</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>

<body id="home-corpo">

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

        <div class="acoes-superiores">
            <span class="ponto-online"></span>
            <span class="texto-online">Online</span>
            <span class="icone-sino"></span>
           <div class="menu-usuario">

        <span class="icone-usuario"></span>

        <div class="aba-usuario">
        <a href="public/usuario/logout.php">Encerrar sessão</a>
        </div>

</div>
        </div>
    </header>

    <aside class="menu-lateral">
        <nav>
            <a class="ativo" href="#">Dashboard</a>
            <a href="public/sensor/tela-home-sensor.php">Sensores</a>
            <a href="#">Alertas</a>
            <a href="#">Relat&oacute;rios</a>
            <a href="#">Configura&ccedil;&otilde;es</a>
        </nav>
    </aside>

    <main class="conteudo">

        <section class="painel-principal">

            <h2>Dashboard</h2>

            <div class="metricas">

                <article class="cartao-metrica">
                    <h3>Status do trem</h3>

                    <div class="linha-metrica">
                        <strong class="verde">ATIVO</strong>
                        <span class="icone-check"></span>
                    </div>
                </article>

                <article class="cartao-metrica">
                    <h3>Velocidade</h3>

                    <div class="linha-metrica">
                        <strong class="ciano">
                            80<span>Km/h</span>
                        </strong>

                        <span class="icone-velocidade"></span>
                    </div>
                </article>

                <article class="cartao-metrica">
                    <h3>Temperatura</h3>

                    <div class="linha-metrica">
                        <strong class="vermelho">75&deg;C</strong>
                        <span class="termometro"></span>
                    </div>
                </article>

                <article class="cartao-metrica">
                    <h3>Vibração</h3>

                    <div class="linha-metrica">
                        <strong class="verde">Normal</strong>
                        <span class="linha-vibracao"></span>
                    </div>
                </article>

            </div>

            <div class="grade-dashboard">

                <section class="cartao-grafico">
                    <canvas id="grafico-sensores"></canvas>
                </section>

                <section class="cartao-eventos">

                    <h3>Eventos Recentes</h3>

                    <ul>

                        <li>
                            <span class="icone-evento alet"></span>
                            <strong></strong>
                            <time></time>
                        </li>

                        <li>
                            <span class="icone-evento alert"></span>
                            <strong></strong>
                            <time></time>
                        </li>

                        <li>
                            <span class="icone-evento atenco"></span>
                            <strong></strong>
                            <time></time>
                        </li>

                        <li>
                            <span class="icone-evento informaco"></span>
                            <strong></strong>
                            <time></time>
                        </li>

                        <li>
                            <span class="icone-evento informaca"></span>
                            <strong></strong>
                            <time></time>
                        </li>

                    </ul>

                </section>

                <section class="cartao-mapa">

                    <iframe
                        src="https://www.google.com/maps?q=S%C3%A3o%20Paulo&output=embed"
                        width="100%"
                        height="100%"
                        style="border: 0;"
                        loading="lazy">
                    </iframe>

                </section>

            </div>

        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script/grafico.js"></script>

</body>

</html>