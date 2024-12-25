<?php
session_start();
require '../../config/connection.php';

$id_usuario = $_SESSION['id_usuarios'];
$db = new Database();
$con = $db->conectar();


$sql = "SELECT firstname, lastname, role, photo, active FROM usuarios WHERE id_usuarios = :id_usuario";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $firstname = $row['firstname'];
  $lastname = $row['lastname'];
  $role = $row['role'];
  $photo = $row['photo'];
  $active = $row['active'];

  if ((int) $active === 0) {
    $_SESSION['error_message'] = 'Tu cuenta ha sido desactivada. Por favor, contacta con el administrador.';
    header('Location: ../../index.php');
    exit();
  }

  if ($role !== 'Proveedor') {
    $_SESSION['error_message'] = 'No tienes permiso para acceder a esta página.';
    header('Location: ../../index.php');
    exit();
  }

  if ($photo !== null) {
    $base64Image = base64_encode($photo);
    $imageSrc = 'data:image/jpeg;base64,' . $base64Image;
  } else {
    $imageSrc = '../../assets/img/avatars/1.png';
  }
} else {
  $_SESSION['error_message'] = 'Usuario no encontrado o cuenta no válida.';
  header('Location: ../../index.php');
  exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = $_POST['data'];
  $allZero = true;

  try {
    foreach ($data as $row) {

      $a = isset($row['a']) && $row['a'] !== '' ? (int) $row['a'] : 0;
      $b = isset($row['b']) && $row['b'] !== '' ? (int) $row['b'] : 0;
      $c = isset($row['c']) && $row['c'] !== '' ? (int) $row['c'] : 0;
      $a_and_b = isset($row['a_and_b']) && $row['a_and_b'] !== '' ? (int) $row['a_and_b'] : 0;
      $goods = isset($row['goods']) && $row['goods'] !== '' ? (int) $row['goods'] : 0;
      $total = $a + $b + $c + $a_and_b + $goods;


      if ($a == 0 && $b == 0 && $c == 0 && $a_and_b == 0 && $goods == 0) {
        continue;
      } else {
        $allZero = false;
      }

      $sql = "INSERT INTO wire_failures (id_usuario, box, a, b, c, a_and_b, goods, total) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $con->prepare($sql);
      $stmt->execute([$id_usuario, $row['box'], $a, $b, $c, $a_and_b, $goods, $total]);
    }


    if ($allZero) {
      $_SESSION['error_message'] = 'Debe ingresar al menos un valor diferente a cero para almacenar la información.';
    } else {
      $_SESSION['success_message'] = "Datos guardados correctamente.";
    }

  } catch (Exception $e) {
    $_SESSION['error_message'] = "Hubo un error al guardar los datos: " . $e->getMessage();
  }

  header('Location: ' . $_SERVER['PHP_SELF']);
  exit();
}


$sql = "SELECT * FROM wire_failures";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../../assets/css/demo.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />
  <script src="../../assets/vendor/js/helpers.js"></script>
  <script src="../../assets/js/config.js"></script>
  <link rel="shortcut icon" href="../ico/comter.png" type="image/x-icon">
  <style>
    ::-webkit-scrollbar {
      width: 12px;
      height: 12px;
      transition: all 0.3s ease;
    }

    ::-webkit-scrollbar-thumb {
      background-color: rgb(27, 114, 155);
      border-radius: 10px;
      transition: background-color 0.3s ease, transform 0.3s ease;
      margin: 4px;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }


    ::-webkit-scrollbar-thumb:hover {
      background-color: rgb(27, 114, 155);
      transform: scale(1.2);
    }


    ::-webkit-scrollbar-track {
      background-color: #f1f1f1;
      transition: background-color 0.3s ease;
      border-radius: 10px;
      box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
    }


    ::-webkit-scrollbar-track:hover {
      background-color: #e0e0e0;
    }


    html {
      scrollbar-width: thin;
      scrollbar-color: rgb(27, 114, 155)B #f1f1f1;
      transition: scrollbar-color 0.3s ease;
    }


    html:hover {
      scrollbar-color: rgb(27, 114, 155) #e0e0e0;
    }
  </style>
  <title>Proveedor</title>
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">


      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="../admin_panel.php" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="../ico/comter.png" alt="" width="50">
              <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                  <g id="Icon" transform="translate(27.000000, 15.000000)">
                    <g id="Mask" transform="translate(0.000000, 8.000000)">
                      <mask id="mask-2" fill="white">
                        <use xlink:href="#path-1"></use>
                      </mask>
                      <use fill="#696cff" xlink:href="#path-1"></use>
                      <g id="Path-3" mask="url(#mask-2)">
                        <use fill="#696cff" xlink:href="#path-3"></use>
                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                      </g>
                      <g id="Path-4" mask="url(#mask-2)">
                        <use fill="#696cff" xlink:href="#path-4"></use>
                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                      </g>
                    </g>
                    <g id="Triangle"
                      transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                      <use fill="#696cff" xlink:href="#path-5"></use>
                      <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                    </g>
                  </g>
                </g>
              </g>
              </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">COMTER</span>
          </a>

          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">

          <li class="menu-item active">
            <a href="../admin_panel.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Analytics">Dashboard</div>
            </a>
          </li>


          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-layout"></i>
              <div data-i18n="Layouts">Reportes</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item">
                <a href="infinex.php" class="menu-link">
                  <div data-i18n="Without menu">Infinex</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molex.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM46</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="reporte.php" class="menu-link">
                  <div data-i18n="Container">Reporte</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="scc.php" class="menu-link">
                  <div data-i18n="Fluid">SCC</div>
                </a>
              </li>
              <!--<li class="menu-item">
                <a href="layouts-blank.html" class="menu-link">
                  <div data-i18n="Blank">Blank</div>
                </a>
              </li>-->
            </ul>
          </li>


      </aside>

      <div class="layout-page">

        <nav
          class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
          id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="bx bx-menu bx-sm"></i>
            </a>
          </div>

          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            <div class="navbar-nav align-items-center">
              <div class="nav-item d-flex align-items-center">
                <form method="GET" action="search_molex.php" class="d-flex align-items-center">
                  <input type="text" name="search" class="form-control border-0 shadow-none"
                    placeholder="Buscar por Operador..." aria-label="Search..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                  <button type="submit" class="btn border-0 bg-transparent p-0 ms-2">
                    <i class="bx bx-search fs-4 lh-0"></i>
                  </button>
                </form>
              </div>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">




              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="<?php echo $imageSrc; ?>" alt="" class="w-px-40 h-auto rounded-circle" />
                  </div>



                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img src="<?php echo $imageSrc; ?>" alt="" class="w-px-40 h-auto rounded-circle" />
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-semibold d-block"><?php echo $firstname . ' ' . $lastname; ?></span>
                          <small class="text-muted"><?php echo $role; ?></small>
                        </div>


                      </div>
                    </a>
                  </li>
                  <!--<li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">
                      <i class="bx bx-user me-2"></i>
                      <span class="align-middle">My Profile</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">
                      <i class="bx bx-cog me-2"></i>
                      <span class="align-middle">Settings</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">
                      <span class="d-flex align-items-center align-middle">
                        <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                        <span class="flex-grow-1 align-middle">Billing</span>
                        <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                      </span>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>-->
                  <li>
                    <a class="dropdown-item" href="#" onclick="confirmLogout()">
                      <i class="bx bx-power-off me-2"></i>
                      <span class="align-middle">Cerrar sesión</span>
                    </a>
                  </li>
                </ul>
              </li>

            </ul>
          </div>
        </nav>
        <style>
          .logo-style {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
          }
        </style>

        <div class="content-wrapper">
          <div class="container my-5">
            <div class="text-center">
              <img src="../../img/infinex.png" alt="Infinex Logo" class="img-fluid w-100 mb-4 logo-style">
            </div>


            <?php if (isset($_SESSION['success_message'])): ?>
              <div class="alert alert-success"><?= $_SESSION['success_message'];
              unset($_SESSION['success_message']); ?>
              </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
              <div class="alert alert-danger"><?= $_SESSION['error_message'];
              unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <form method="POST">
              <div class="table-responsive">
                <table class="table table-bordered text-center">
                  <thead style="background-color: black;">
                    <tr>
                      <th style="color:#0f7ecb">BOX/BAG</th>
                      <th style="color:#0f7ecb">A</th>
                      <th style="color:#0f7ecb">B</th>
                      <th style="color:#0f7ecb">C</th>
                      <th style="color:#0f7ecb">A & B</th>
                      <th style="color:#0fcb59">GOODS</th>
                      <th class="text-white">TOTAL</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $rows = ['BOX 1', 'BOX 2', 'BOX 3', 'BAG 1'];
                    foreach ($rows as $row): ?>
                      <tr>
                        <td><input type="hidden" name="data[<?= $row ?>][box]" value="<?= $row ?>"><?= $row ?></td>
                        <td><input type="number" class="form-control" name="data[<?= $row ?>][a]" value="0" min="0"></td>
                        <td><input type="number" class="form-control" name="data[<?= $row ?>][b]" value="0" min="0"></td>
                        <td><input type="number" class="form-control" name="data[<?= $row ?>][c]" value="0" min="0"></td>
                        <td><input type="number" class="form-control" name="data[<?= $row ?>][a_and_b]" value="0" min="0">
                        </td>
                        <td><input type="number" class="form-control" name="data[<?= $row ?>][goods]" value="0" min="0">
                        </td>
                        <td class="fw-bold text-primary">0</td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="6" class="text-end fw-bold text-white" style="background-color: black;">TOTAL
                        GENERAL:</td>
                      <td id="total-general" class="fw-bold text-success" style="background-color: black;">0</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Guardar</button>
              </div>
            </form>
          </div>
          <div class="container">
            <div class="mt-5">
              <div>
                <form action="../backend/exportar_excel_infinex.php" method="post">
                  <button type="submit" class="btn btn-success" id="exportButton" disabled>
                    <i class="bx bxs-file"></i> Exportar a Excel
                  </button>
                </form>
              </div>
              <h3 class="text-center">Registros</h3>
              <div class="table-responsive">
                <table class="table table-bordered text-center" id="inspectionTable">
                  <thead style="background-color: black;">
                    <tr>
                      <td colspan="7" class="fw-bold text-white">THE WIRE PRESENT THE FAILURE AT POSITION</td>
                    </tr>
                    <tr>
                      <th style="color:#0f7ecb">BOX/BAG</th>
                      <th style="color:#0f7ecb">A</th>
                      <th style="color:#0f7ecb">B</th>
                      <th style="color:#0f7ecb">C</th>
                      <th style="color:#0f7ecb">A & B</th>
                      <th style="color:#0fcb59">GOODS</th>
                      <th class="text-white">TOTAL</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($records)): ?>
                      <tr>
                        <td colspan="7" class="fw-bold text-danger">No hay registros disponibles.</td>
                      </tr>
                    <?php else: ?>
                      <?php
                      $totalGeneral = 0;
                      foreach ($records as $record):
                        $totalGeneral += $record['total'];
                        ?>
                        <tr>
                          <td><?= htmlspecialchars($record['box']) ?></td>
                          <td><?= htmlspecialchars($record['a'] ?? 0) ?></td>
                          <td><?= htmlspecialchars($record['b'] ?? 0) ?></td>
                          <td><?= htmlspecialchars($record['c'] ?? 0) ?></td>
                          <td><?= htmlspecialchars($record['a_and_b'] ?? 0) ?></td>
                          <td><?= htmlspecialchars($record['goods'] ?? 0) ?></td>
                          <td><?= htmlspecialchars($record['total'] ?? 0) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="6" class="text-end fw-bold text-white" style="background-color: black;">TOTAL
                        GENERAL:</td>
                      <td class="fw-bold text-success" style="background-color: black;">
                        <?= htmlspecialchars($totalGeneral) ?>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <script>
            document.addEventListener('DOMContentLoaded', function () {
              var table = document.getElementById('inspectionTable');
              var exportButton = document.getElementById('exportButton');

              var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
              var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;

              exportButton.disabled = !hasRecords;
            });
          </script>




          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-column flex-md-row justify-content-center py-2">
              <div class="mb-2 mb-md-0 text-center text-md-start">
                ©
                <script>
                  document.write(new Date().getFullYear());
                </script>
                , Comter
                <a href="#" class="footer-link fw-bolder">Osdems Digital Group</a>
              </div>
            </div>
          </footer>

          <div class="content-backdrop fade"></div>
        </div>

      </div>

    </div>


    <div class="layout-overlay layout-menu-toggle"></div>
  </div>





  <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../../assets/vendor/libs/popper/popper.js"></script>
  <script src="../../assets/vendor/js/bootstrap.js"></script>
  <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../../assets/vendor/js/menu.js"></script>
  <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>
  <script src="../../assets/js/main.js"></script>
  <script src="../../assets/js/dashboards-analytics.js"></script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script>
    const inputs = document.querySelectorAll('input[type="number"]');
    const totalGeneral = document.getElementById('total-general');

    inputs.forEach(input => {
      input.addEventListener('input', () => {
        let total = 0;
        document.querySelectorAll('tbody tr').forEach(row => {
          const values = row.querySelectorAll('input[type="number"]');
          const rowTotal = Array.from(values).reduce((acc, input) => acc + parseInt(input.value || 0), 0);
          row.querySelector('.fw-bold.text-primary').textContent = rowTotal;
          total += rowTotal;
        });
        totalGeneral.textContent = total;
      });
    });





  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

  <script>
    function confirmLogout() {
      Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres cerrar la sesión?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '../backend/home/logout.php';
        }
      });
    }
  </script>

</body>

</html>