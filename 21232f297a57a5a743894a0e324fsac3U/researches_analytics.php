<?php
// --- Session context (adjust to real auth) ---
    session_start();
    ob_start();

    include '../assets/db/db.php';
    
    if($_SESSION['user_email_address']==''){
      header('location:../index.php');
    }



$chair_name = $_SESSION['user_first_name'].' '. $_SESSION['user_last_name'];
$role = $_SESSION['acc_type'];
$program = '';
$campus  = $_SESSION['campus'];
// if(!isset($_SESSION['chair_logged_in'])){ header('Location: ../login.php'); exit; }

$get_program = "SELECT * FROM `tblcourse` WHERE courseid='$_SESSION[programid]'";
$runget_program = mysqli_query($conn, $get_program);
$row_program = mysqli_fetch_assoc($runget_program);


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SKSU – <?php echo $_SESSION['systemname'] ?> Analytics</title>
  <link href="../images/logo.png" rel="icon">
  <link href="../images/logo.png" rel="apple-touch-icon">
<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet" />

<!-- DataTables CSS (Bootstrap 5 + Responsive + Buttons) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<!-- Select2 CSS (match version with JS) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Optional: prettier Bootstrap 5 theme for Select2 -->
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.4/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
  /* Make Select2 always fill the form-control width */
  .select2-container { width: 100% !important; }
</style>




  <style>
    :root{
      --text:#1f2428; --muted:#6b7280; --line:#e5e7eb;
      --brand:#0a58ff; --brand2:#00c6ff; --accent:#198754; --gold:#ffb400;
      --bg:#f5f7fb; --card:#ffffff; --sidebar:#0a58ff; --sidebar-text:#eaf1ff;
    }
    *{font-family:'Outfit',system-ui,Segoe UI,Roboto,Arial,sans-serif}
    body{background:var(--bg); color:var(--text)}
    .app{display:grid; grid-template-columns: 260px 1fr; min-height:100vh}
    @media (max-width: 991.98px){ .app{grid-template-columns:1fr} }

    /* Sidebar */
    .sidebar{background:var(--sidebar); color:var(--sidebar-text); border-right:1px solid rgba(255,255,255,.08)}
    .sidebar .brand{padding:1rem 1.25rem; border-bottom:1px solid rgba(255,255,255,.12)}
    .sidebar .brand .logo{width:30px;height:30px;border-radius:50%;background:radial-gradient(circle at 30% 30%, #fff 0 8%, var(--brand) 8% 48%, transparent 48%), radial-gradient(circle at 65% 60%, var(--brand2), #001430 70%); box-shadow:0 0 15px rgba(0,209,255,.35)}
    .sidebar a{color:var(--sidebar-text); text-decoration:none; display:flex; align-items:center; gap:.6rem; padding:.75rem 1.25rem; border-left:3px solid transparent}
    .sidebar a:hover{background:rgba(255,255,255,.06)}
    .sidebar a.active{background:rgba(255,255,255,.12); border-left-color:var(--gold)}
    .sidebar .section{padding:.75rem 1.25rem; color:rgba(255,255,255,.7); text-transform:uppercase; font-size:.78rem}

    /* Topbar */
    .topbar{background:#fff; border-bottom:1px solid var(--line); position:sticky; top:0; z-index:10}
    .search-wrap{position:relative}
    .search-wrap i{position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9aa4b2}
    .search-wrap input{padding-left:38px}

    /* Cards/Table */
    .card-soft{background:var(--card); border:1px solid var(--line); border-radius:14px}
    .table thead th{background:#f1f4f9; font-weight:700}
    .status{padding:.25rem .5rem; border-radius:999px; font-size:.8rem}
    .s-draft{background:#e0e7ff; color:#3730a3}
    .s-pending{background:#fff3cd; color:#8a6d3b}
    .s-approved{background:#d1fae5; color:#065f46}
    .s-embargo{background:#fee2e2; color:#991b1b}
    .badge-role{background:rgba(10,88,255,.08); color:#0a58ff; border:1px solid rgba(10,88,255,.18)}
    .btn-pill{border-radius:999px}
    .muted{color:var(--muted)}
    .progress{height:.6rem}

    /* DataTables toolbar spacing */
    .dt-buttons .btn{margin-right:.25rem}

.select2-results__options {
  max-height: 200px !important; /* limit height */
  overflow-y: auto !important;  /* scrollbar */
}

  </style>
</head>
<body>
<div class="app">
  <!-- SIDEBAR -->
  <aside class="sidebar d-none d-lg-flex flex-column">
    <div class="brand d-flex align-items-center gap-2">
      <span class="logo"></span>
      <div>
        <div class="fw-bold">ORACLE</div>
        <div class="small" style="opacity:.8">Program Chair</div>
      </div>
    </div>
    <div class="section">My Area</div>

    <nav class="flex-grow-1">
      <a class="active" href="index.php"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
      <a href="#" onclick="return false;"><i class="bi bi-journal-text"></i><span>My Program Researches</span></a>
      <a href="#" onclick="return false;"><i class="bi bi-clipboard-check"></i><span>Reviews Assigned</span></a>
      <a href="#" onclick="return false;"><i class="bi bi-graph-up"></i><span>Program Analytics</span></a>
      <div class="section">Account</div>
      <a href="#" onclick="return false;"><i class="bi bi-person"></i><span>Profile</span></a>
      <a href="#" onclick="return false;"><i class="bi bi-sliders"></i><span>Preferences</span></a>
    </nav>
    <div class="p-3 border-top border-light-subtle mt-auto">
      <div class="small mb-1">Signed in as</div>
      <div class="fw-semibold text-white"><?php echo htmlspecialchars($chair_name); ?></div>
      <div class="small" style="opacity:.85"><?php echo htmlspecialchars($row_program['coursecode'].' • '.$campus); ?></div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="d-flex flex-column">
    <!-- TOPBAR -->
    <div class="topbar py-2">
      <div class="container-fluid">
        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav">
            <i class="bi bi-list"></i>
          </button>
          <div class="search-wrap flex-grow-1">
            <i class="bi bi-search"></i>
            <input id="q" class="form-control" placeholder="Search my program’s researches…" />
          </div>
          <!-- <span class="badge badge-role d-none d-md-inline"><?php echo strtoupper($program).' • '.strtoupper($campus); ?></span> -->
          <div class="dropdown">
            <button class="btn btn-outline-secondary btn-pill dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($chair_name); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-sliders"></i> Preferences</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Sign out</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="container-fluid py-4">


      <!-- My Program Researches Table -->
      <div class="card-soft p-3">
        <div class="d-flex justify-content-between align-items-center py-2">
          <div class="fw-semibold">My Program Researches</div>
        </div>

        <div class="row">
          <div class="col-lg-12 intro3">
            <div id="main_data">
<div id="researchChart" style="max-width: 100%; height: 350px;"></div>

              <div id="loader" class="text-center" style="display: none;">
                <img src="../loader.gif" alt="Loading..." width="10%">
              </div>
              <div id="content_area"></div>
            </div>                
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- OFFCANVAS (mobile sidebar) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNav">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">ORACLE • Program Chair</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <a class="d-block mb-2" href="#"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a class="d-block mb-2" href="#"><i class="bi bi-journal-text me-2"></i>My Program Researches</a>
    <a class="d-block mb-2" href="#"><i class="bi bi-clipboard-check me-2"></i>Reviews Assigned</a>
    <a class="d-block mb-2" href="#"><i class="bi bi-graph-up me-2"></i>Program Analytics</a>
    <hr>
    <a class="d-block mb-2" href="#"><i class="bi bi-person me-2"></i>Profile</a>
    <a class="d-block mb-2" href="#"><i class="bi bi-sliders me-2"></i>Preferences</a>
  </div>
</div>




<div class="modal fade" id="modalReview" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-eye me-2"></i>Submission Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div id="research_status">Loading research details</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-danger"><i class="bi bi-x"></i> Done</button>
        <button class="btn btn-success"><i class="bi bi-check2"></i> Approved</button>
      </div>
    </div>
  </div>
</div>


<!-- jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Select2 (match CSS version) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Bootstrap bundle (load ONCE only) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
fetch('getResearchStats.php')
  .then(response => response.json())
  .then(data => {
    const years = data.map(item => item.year);
    const totals = data.map(item => item.total);

    const options = {
      chart: {
        type: 'line',
        height: 350,
        toolbar: { show: false }
      },
      series: [{
        name: "Total Researches",
        data: totals
      }],
      xaxis: {
        categories: years,
        title: { text: 'Academic Year' }
      },
      yaxis: {
        title: { text: 'No. of Researches' },
        min: 0
      },
      stroke: {
        curve: 'smooth',
        width: 3
      },
      markers: {
        size: 6,
        colors: ['#28a745'],
        strokeColor: '#fff',
        strokeWidth: 2,
        shape: 'circle',
        hover: {
          size: 9
        }
      },
      colors: ['#28a745'],
      title: {
        text: 'Total Researches Per Academic Year',
        align: 'left'
      },
      tooltip: {
      theme: false,
      style: {
        fontSize: '14px',
        fontFamily: 'Arial, sans-serif'
      },
      custom: function({ series, seriesIndex, dataPointIndex, w }) {
        return `
          <div style="
            background-color: #fd7e14;
            color: #fff;
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
          ">
            ${series[seriesIndex][dataPointIndex]}
          </div>`;
      }
    }
    };

    const chart = new ApexCharts(document.querySelector("#researchChart"), options);
    chart.render();
  });
</script>


</body>
</html>
