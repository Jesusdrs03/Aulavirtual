<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Estudiante</title>

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Enlace del css -->
    <link rel="stylesheet" href="../css/style.css">

  </head>
  <body>
    <div class="grid-container">

      <!-- Header -->
      <header class="header">
        <div class="menu-icon" onclick="openSidebar()">
          <span class="material-icons-outlined">menu</span>
        </div>
        <div class="header-left">
          <span class="material-icons-outlined">search</span>
        </div>
        <div class="header-right">
          <span class="material-icons-outlined">notifications</span>
          <span class="material-icons-outlined">account_circle</span>
        </div>
      </header>
      <!-- End Header -->

      <!-- Sidebar -->
      <aside id="sidebar">
        <div class="sidebar-title">
          <div class="sidebar-brand">
            
          </div>
          
          <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
        </div>

        <ul class="sidebar-list">
          <li class="sidebar-list-item">
            <a href="#" target="_blank">
              <span class="material-icons-outlined">home</span> Inicio
            </a>
          </li>

          <li class="sidebar-list-item">
            <a href="#" target="_blank">
              <span class="material-icons-outlined">account_circle</span> Perfil
            </a>
          </li>
          
          <li class="sidebar-list-item">
            <a href="#" target="_blank">
              <span class="material-icons-outlined">backpack</span>Materias
            </a>

          <li class="sidebar-list-item">
            <a href="#" target="_blank">
              <span class="material-icons-outlined">local_library</span> Cursos
            </a>

          <li class="sidebar-list-item">
            <a href="#" target="_blank">
              <span class="material-icons-outlined">list_alt</span> Calificaciones
            </a>
          </li>

          <li class="sidebar-list-item">
            <a href="#" target="_blank">
              <span class="material-icons-outlined">assignment</span> record academico
            </a>
          </li>

          <li class="sidebar-list-item">
            <a href="#" target="_blank">
              <span class="material-icons-outlined">logout</span> Cerrar sesión
            </a>
          </li>
        </ul>




      </aside>
      <!-- End Sidebar -->

      <!-- Main -->
      <main class="main-container">
        <div class="main-title">
          <p class="font-weight-bold">Bienvenido(a)</p>
        </div>

        <div class="main-cards">

          <div class="card">
            <div class="card-inner">
              <p class="text-primary">DOCENTES</p>
              <span class="material-icons-outlined text-green">person</span>
            </div>
            <span class="text-primary font-weight-bold">14</span>
          </div>

          <div class="card">
            <div class="card-inner">
              <p class="text-primary">ESTUDIANTES</p>
              <span class="material-icons-outlined text-blue">school</span>
            </div>
            <span class="text-primary font-weight-bold">100</span>
          </div>

          <div class="card">
            <div class="card-inner">
              <p class="text-primary">MATERIAS</p>
              <span class="material-icons-outlined text-orange">backpack</span>
            </div>
            <span class="text-primary font-weight-bold">18</span>
          </div>

          <div class="card">
            <div class="card-inner">
              <p class="text-primary">CURSOS</p>
              <span class="material-icons-outlined text-red">local_library</span>
            </div>
            <span class="text-primary font-weight-bold">16</span>
          </div>
        </div>

    <!-- Scripts -->
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
    <!-- Custom JS -->
    <script src="../javascript/script2.js"></script>
  </body>
</html>