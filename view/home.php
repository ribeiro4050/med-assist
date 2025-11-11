<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedAssit - Agendamento Hospitalar Simplificado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="../img/logo.png" alt="logo MedAssit" style="width: 50px; height: auto">
      <span class="ms-2 fw-bold text-primary">MedAssit</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Início</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#sobre">Sobre Nós</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Serviços
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#servicos">Agendamento Online</a></li>
            <li><a class="dropdown-item" href="#servicos">Especialidades</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#servicos">Exames e Procedimentos</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#hospitais">Hospitais Parceiros</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#contato">Contato</a>
        </li>
      </ul>
      <div class="d-flex">
        <a href="index.php?page=login" class="btn btn-outline-primary me-2">Entrar</a>
        <a href="../view/usuario-create.php" class="btn btn-primary">Cadastrar</a>
      </div>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<div class="primeira-parte">
  <div class="container">
    <div class="hero-content text-center text-white py-5">
      <h1 class="display-4 fw-bold mb-4">Bem-Vindo a MedAssit</h1>
      <p class="lead mb-4">Buscamos transformar a eficiência da assistência médica com tecnologia de ponta</p>
      <p class="mb-5">Tenha acesso rápido a serviços médicos e atendimento especializado</p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="#" class="btn btn-primary btn-lg">Agendar Consulta</a>
        <a href="#" class="btn btn-outline-light btn-lg">Saiba Mais</a>
      </div>
    </div>
  </div>

  <!-- Cards de Serviços -->
 <div class="container mt-5">
    <div class="row g-4 justify-content-center"> 
      
      <div class="col-sm-12 col-md-6 col-lg-3">
        <div class="card h-100 border-primary shadow-sm">
          <div class="card-header bg-primary text-white text-center">
            <i class="fas fa-calendar-check fa-2x mb-2"></i>
            <h5 class="card-title">Agendamento Rápido</h5>
          </div>
          <div class="card-body text-primary">
            <p class="card-text">Agende consultas e exames em poucos cliques, com confirmação imediata.</p>
            <a href="#" class="btn btn-outline-primary btn-sm">Saiba mais</a>
          </div>
        </div>
      </div>
      
      <div class="col-sm-12 col-md-6 col-lg-3">
        <div class="card h-100 border-success shadow-sm">
          <div class="card-header bg-success text-white text-center">
            <i class="fas fa-user-md fa-2x mb-2"></i>
            <h5 class="card-title">Especialistas</h5>
          </div>
          <div class="card-body text-success">
            <p class="card-text">Acesso a mais de 500 especialistas em diversas áreas da medicina.</p>
            <a href="#" class="btn btn-outline-success btn-sm">Saiba mais</a>
          </div>
        </div>
      </div>
      
      <div class="col-sm-12 col-md-6 col-lg-3">
        <div class="card h-100 border-info shadow-sm">
          <div class="card-header bg-info text-white text-center">
            <i class="fas fa-hospital fa-2x mb-2"></i>
            <h5 class="card-title">Hospitais Parceiros</h5>
          </div>
          <div class="card-body text-info">
            <p class="card-text">Rede credenciada com os melhores hospitais e clínicas da região.</p>
            <a href="#" class="btn btn-outline-info btn-sm">Saiba mais</a>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-6 col-lg-3">
        <div class="card h-100 border-warning shadow-sm">
          <div class="card-header bg-warning text-white text-center">
            <i class="fas fa-clock fa-2x mb-2"></i>
            <h5 class="card-title">Atendimento 24h</h5>
          </div>
          <div class="card-body text-warning">
            <p class="card-text">Suporte completo a qualquer hora do dia para suas necessidades médicas.</p>
            <a href="#" class="btn btn-outline-warning btn-sm">Saiba mais</a>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>

<!-- Sobre Nós -->
<section id="sobre" class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h2 class="display-5 fw-bold text-primary mb-4">Sobre a MedAssit</h2>
        <p class="lead">Somos uma plataforma dedicada a simplificar o acesso aos serviços de saúde, conectando pacientes a profissionais e instituições de confiança.</p>
        <p>Nossa missão é tornar o agendamento de consultas e exames um processo simples, rápido e eficiente, eliminando burocracias e longas esperas.</p>
        <div class="d-flex mt-4">
          <div class="me-4 text-center">
            <h3 class="text-primary fw-bold">10K+</h3>
            <p class="text-muted">Pacientes Atendidos</p>
          </div>
          <div class="me-4 text-center">
            <h3 class="text-primary fw-bold">500+</h3>
            <p class="text-muted">Profissionais</p>
          </div>
          <div class="text-center">
            <h3 class="text-primary fw-bold">50+</h3>
            <p class="text-muted">Hospitais Parceiros</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <img src="../img/home-2.png" alt="Equipe MedAssit" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</section>

<!-- Serviços -->
<section id="servicos" class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold text-primary">Nossos Serviços</h2>
      <p class="lead">Oferecemos soluções completas para suas necessidades de saúde</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
              <i class="fas fa-stethoscope fa-2x text-primary"></i>
            </div>
            <h5 class="card-title">Consultas Médicas</h5>
            <p class="card-text">Agende consultas com especialistas de diversas áreas da medicina com facilidade e rapidez.</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
              <i class="fas fa-vial fa-2x text-success"></i>
            </div>
            <h5 class="card-title">Exames Laboratoriais</h5>
            <p class="card-text">Solicite e agende exames de sangue, imagem e outros procedimentos diagnósticos.</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
              <i class="fas fa-procedures fa-2x text-info"></i>
            </div>
            <h5 class="card-title">Procedimentos</h5>
            <p class="card-text">Agendamento para pequenos procedimentos, cirurgias ambulatoriais e tratamentos.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Hospitais Parceiros -->
<section id="hospitais" class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold text-primary">Hospitais Parceiros</h2>
      <p class="lead">Trabalhamos com as melhores instituições de saúde</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
          <img src="../img/sirio-libanes.png" class="card-img-top" alt="Hospital Sirio Libanes">
          <div class="card-body">
            <h5 class="card-title">Hospital SirioL ibanes</h5>
            <p class="card-text">Referência em cardiologia e oncologia com mais de 30 anos de atuação.</p>
            <div class="d-flex align-items-center text-warning mb-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star-half-alt"></i>
              <span class="ms-1 text-muted">4.5</span>
            </div>
            <a href="#" class="btn btn-outline-primary btn-sm">Ver especialidades</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
          <img src="../img/santa_casa.png" class="card-img-top" alt="Santa Casa">
          <div class="card-body">
            <h5 class="card-title">Santa Casa</h5>
            <p class="card-text">Atendimento humanizado em mais de 15 especialidades médicas.</p>
            <div class="d-flex align-items-center text-warning mb-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="far fa-star"></i>
              <span class="ms-1 text-muted">4.0</span>
            </div>
            <a href="#" class="btn btn-outline-primary btn-sm">Ver especialidades</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
          <img src="../img/são_luiz.png" class="card-img-top" alt="Hospital São Luiz">
          <div class="card-body">
            <h5 class="card-title">Hospital São Luiz</h5>
            <p class="card-text">Centro de excelência em ortopedia e medicina esportiva.</p>
            <div class="d-flex align-items-center text-warning mb-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <span class="ms-1 text-muted">5.0</span>
            </div>
            <a href="#" class="btn btn-outline-primary btn-sm">Ver especialidades</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
          <img src="../img/albert_eistein.png" class="card-img-top" alt="Hospital Albert Eistein">
          <div class="card-body">
            <h5 class="card-title">Albert Eistein</h5>
            <p class="card-text">Especializado em diagnósticos por imagem e medicina preventiva.</p>
            <div class="d-flex align-items-center text-warning mb-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star-half-alt"></i>
              <span class="ms-1 text-muted">4.5</span>
            </div>
            <a href="#" class="btn btn-outline-primary btn-sm">Ver especialidades</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Depoimentos -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold text-primary">O que nossos pacientes dizem</h2>
      <p class="lead">Avaliações de quem já utilizou nossos serviços</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
              <img src="../img/pessoa-1.png" alt="Maria Silva" class="rounded-circle me-3" style="width: 50px; height: 50px;">
              <div>
                <h5 class="mb-0">Maria Silva</h5>
                <div class="text-warning">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </div>
            </div>
            <p class="card-text">"Consegui marcar minha consulta com um cardiologista em menos de 5 minutos. O sistema é muito intuitivo e prático!"</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
              <img src="../img/pessoa-2.png" alt="João Santos" class="rounded-circle me-3" style="width: 50px; height: 50px;">
              <div>
                <h5 class="mb-0">João Santos</h5>
                <div class="text-warning">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
                </div>
              </div>
            </div>
            <p class="card-text">"Excelente plataforma! Agendei exames para minha família toda em um só lugar, sem precisar ligar para vários hospitais."</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
              <img src="../img/pessoa-3.png" alt="Ana Costa" class="rounded-circle me-3" style="width: 50px; height: 50px;">
              <div>
                <h5 class="mb-0">Vinicius Ferreiragi</h5>
                <div class="text-warning">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </div>
            </div>
            <p class="card-text">"Adorei a praticidade! Recebi lembretes no celular e por e-mail, não precisei me preocupar em esquecer minha consulta."</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-5 bg-primary text-white">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h2 class="display-6 fw-bold mb-3">Pronto para agendar sua consulta?</h2>
        <p class="lead mb-0">Cadastre-se agora e tenha acesso a todos os nossos serviços de forma rápida e segura.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="../view/login.php" class="btn btn-light btn-lg px-4">Começar Agora</a>
      </div>
    </div>
  </div>
</section>

<!-- Contato -->
<section id="contato" class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold text-primary">Entre em Contato</h2>
      <p class="lead">Tire suas dúvidas ou solicite mais informações</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <h5 class="card-title mb-4">Informações de Contato</h5>
            <div class="d-flex align-items-start mb-3">
              <i class="fas fa-map-marker-alt text-primary me-3 mt-1"></i>
              <div>
                <h6>Endereço</h6>
                <p class="text-muted mb-0">Av.Salgado Filho, 3501 - Vila Rio, Guarulhos - SP</p>
              </div>
            </div>
            <div class="d-flex align-items-start mb-3">
              <i class="fas fa-phone text-primary me-3 mt-1"></i>
              <div>
                <h6>Telefone</h6>
                <p class="text-muted mb-0">(11) 3456-7890</p>
              </div>
            </div>
            <div class="d-flex align-items-start mb-3">
              <i class="fas fa-envelope text-primary me-3 mt-1"></i>
              <div>
                <h6>E-mail</h6>
                <p class="text-muted mb-0">contato@medassit.com.br</p>
              </div>
            </div>
            <div class="d-flex align-items-start">
              <i class="fas fa-clock text-primary me-3 mt-1"></i>
              <div>
                <h6>Horário de Atendimento</h6>
                <p class="text-muted mb-0">24h</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <h5 class="card-title mb-4">Envie uma Mensagem</h5>
            <form>
              <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" required>
              </div>
              <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="tel" class="form-control" id="telefone">
              </div>
              <div class="mb-3">
                <label for="mensagem" class="form-label">Mensagem</label>
                <textarea class="form-control" id="mensagem" rows="4" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-4 mb-md-0">
        <h5 class="mb-3">MedAssit</h5>
        <p class="text-muted">Simplificando o acesso à saúde através de tecnologia e inovação.</p>
        <div class="d-flex">
          <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      <div class="col-md-2 mb-4 mb-md-0">
        <h5 class="mb-3">Links Rápidos</h5>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Início</a></li>
          <li class="mb-2"><a href="#sobre" class="text-muted text-decoration-none">Sobre Nós</a></li>
          <li class="mb-2"><a href="#servicos" class="text-muted text-decoration-none">Serviços</a></li>
          <li class="mb-2"><a href="#hospitais" class="text-muted text-decoration-none">Hospitais</a></li>
        </ul>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <h5 class="mb-3">Serviços</h5>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Consultas</a></li>
          <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Exames</a></li>
          <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Procedimentos</a></li>
          <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Emergência</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h5 class="mb-3">Newsletter</h5>
        <p class="text-muted">Inscreva-se para receber novidades e promoções.</p>
        <div class="input-group">
          <input type="email" class="form-control" placeholder="Seu e-mail">
          <button class="btn btn-primary" type="button">Inscrever</button>
        </div>
      </div>
    </div>
    <hr class="my-4">
    <div class="row align-items-center">
      <div class="col-md-6 text-center text-md-start">
        <p class="mb-0 text-muted">&copy; 2023 MedAssit. Todos os direitos reservados.</p>
      </div>
      <div class="col-md-6 text-center text-md-end">
        <a href="#" class="text-muted text-decoration-none me-3">Política de Privacidade</a>
        <a href="#" class="text-muted text-decoration-none">Termos de Uso</a>
      </div>
    </div>
  </div>
</footer>

</body>
</html>