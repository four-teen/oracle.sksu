<?php
// --- Minimal session/role guard (adjust to your auth flow) ---
session_start();
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$role = $_SESSION['role'] ?? 'admin';
// if(!isset($_SESSION['admin_logged_in'])){ header('Location: ../login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ORACLE Admin • Dashboard</title>
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
    :root{
      --text:#1f2428; --muted:#6b7280; --line:#e5e7eb;
      --brand:#0a58ff; --brand2:#00c6ff; --accent:#198754; --gold:#ffb400;
      --bg:#f5f7fb; --card:#ffffff;
      --sidebar:#0a58ff; --sidebar-text:#eaf1ff; --sidebar-hover:#ffde8a;
    }
    *{font-family:'Outfit',system-ui,Segoe UI,Roboto,Arial,sans-serif}
    body{background:var(--bg); color:var(--text)}

    /* Layout */
    .app{display:grid; grid-template-columns: 260px 1fr; min-height:100vh}
    @media (max-width: 991.98px){ .app{grid-template-columns:1fr} }

    /* Sidebar */
    .sidebar{background:var(--sidebar); color:var(--sidebar-text); border-right:1px solid rgba(255,255,255,.08)}
    .sidebar .brand{padding:1rem 1.25rem; border-bottom:1px solid rgba(255,255,255,.12)}
    .sidebar .brand .logo{width:30px;height:30px;border-radius:50%;background:radial-gradient(circle at 30% 30%, #fff 0 8%, var(--brand) 8% 48%, transparent 48%), radial-gradient(circle at 65% 60%, var(--brand2), #001430 70%); box-shadow:0 0 15px rgba(0,209,255,.35)}
    .sidebar a{color:var(--sidebar-text); text-decoration:none; display:flex; align-items:center; gap:.6rem; padding:.75rem 1.25rem; border-left:3px solid transparent}
    .sidebar a:hover{background:rgba(255,255,255,.06); color:#fff}
    .sidebar a.active{background:rgba(255,255,255,.12); border-left-color:var(--gold); color:#fff}
    .sidebar .section{padding:.75rem 1.25rem; color:rgba(255,255,255,.7); text-transform:uppercase; font-size:.78rem}

    /* Topbar */
    .topbar{background:#fff; border-bottom:1px solid var(--line); position:sticky; top:0; z-index:10}
    .search-wrap{position:relative}
    .search-wrap i{position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9aa4b2}
    .search-wrap input{padding-left:38px}

    /* Cards */
    .card-soft{background:var(--card); border:1px solid var(--line); border-radius:14px}
    .kpi-number{font-weight:800}

    /* Table */
    .table thead th{background:#f1f4f9; font-weight:700}
    .status{padding:.25rem .5rem; border-radius:999px; font-size:.8rem}
    .s-pending{background:#fff3cd; color:#8a6d3b}
    .s-approved{background:#d1fae5; color:#065f46}
    .s-embargo{background:#fee2e2; color:#991b1b}

    /* Utilities */
    .btn-pill{border-radius:999px}
    .badge-glass{background:rgba(10,88,255,.08); color:#0a58ff; border:1px solid rgba(10,88,255,.18)}
  </style>
</head>
<body onload="load_researches()">
<div class="app">
  <!-- SIDEBAR -->
<?php 
  include 'sidebar.php';
 ?>

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
            <input class="form-control" placeholder="Search researches, authors, keywords…" />
          </div>
          <span class="badge badge-glass d-none d-md-inline">Role: <?php echo strtoupper($role); ?></span>
          <div class="dropdown">
            <button class="btn btn-outline-secondary btn-pill dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($admin_name); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
              <li><a class="dropdown-item" href="preferences.php"><i class="bi bi-sliders"></i> Preferences</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Sign out</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="container-fluid py-4">
      <!-- KPIs -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="card-soft p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-muted small">DISSERTATION</div>
                <div class="kpi-number fs-3">0</div>
              </div>
              <div class="text-success"><i class="bi bi-check2-circle fs-3"></i></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-soft p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-muted small">THESIS</div>
                <div class="kpi-number fs-3">0</div>
              </div>
              <div class="text-success"><i class="bi bi-check2-circle fs-3"></i></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-soft p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-muted small">CAPSTONE</div>
                <div class="kpi-number fs-3">0</div>
              </div>
              <div class="text-success"><i class="bi bi-check2-circle fs-3"></i></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-soft p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-muted small">Avg. Approval Time</div>
                <div class="kpi-number fs-3">0.0d</div>
              </div>
              <div class="text-primary"><i class="bi bi-stopwatch fs-3"></i></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters & Actions -->
      <div class="card-soft p-3 mb-3">
        <form class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Campus</label>
            <select class="form-select" id="fCampus">
              <option value="">All Campuses</option>
              <option>ACCESS</option><option>Bagumbayan</option><option>Isulan</option>
              <option>Tacurong</option><option>Kalamansig</option><option>Palimbang</option><option>Lutayan</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Type</label>
            <select class="form-select" id="fType">
              <option value="">Any</option>
              <option>Capstone</option><option>Thesis</option><option>Dissertation</option>
              <option>IPO</option><option>Copyright</option><option>Publication</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="fStatus">
              <option value="">Any</option>
              <option>Pending</option><option>Approved</option><option>Embargoed</option>
            </select>
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Apply Filters</button>
          </div>
        </form>
      </div>

      <!-- Table -->
      <div class="card-soft px-3 py-3">
        <div class="d-flex justify-content-between align-items-center p-3">
          <div class="fw-semibold">Recent Research Submissions</div>

        </div>
        <div class="row">
          <div class="col-lg-12 intro3">
            <div id="main_data">
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
    <h5 class="offcanvas-title">ORACLE Admin</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <a class="d-block mb-2" href="index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a class="d-block mb-2" href="researches.php"><i class="bi bi-journal-text me-2"></i>Researches</a>
    <a class="d-block mb-2" href="reviews.php"><i class="bi bi-check2-square me-2"></i>Reviews & Approvals</a>
    <a class="d-block mb-2" href="authors.php"><i class="bi bi-people me-2"></i>Authors & Advisers</a>
    <a class="d-block mb-2" href="campuses.php"><i class="bi bi-building me-2"></i>Campuses</a>
    <a class="d-block mb-2" href="analytics.php"><i class="bi bi-graph-up me-2"></i>Analytics</a>
    <hr>
    <a class="d-block mb-2" href="users.php"><i class="bi bi-person-gear me-2"></i>Users & Roles</a>
    <a class="d-block mb-2" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a>
    <a class="d-block mb-2" href="logs.php"><i class="bi bi-clipboard-data me-2"></i>Activity Logs</a>
  </div>
</div>

<!-- MODALS -->
<div class="modal fade" id="modalNew" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>New Research</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form class="row g-3">
          <div class="col-12">
            <label class="form-label">Title</label>
            <input class="form-control" placeholder="Enter title"/>
          </div>
          <div class="col-md-6">
            <label class="form-label">Type</label>
            <select class="form-select"><option>Capstone</option><option>Thesis</option><option>Dissertation</option><option>IPO</option><option>Copyright</option><option>Publication</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Campus</label>
            <select class="form-select"><option>ACCESS</option><option>Bagumbayan</option><option>Isulan</option><option>Tacurong</option><option>Kalamansig</option><option>Palimbang</option><option>Lutayan</option></select>
          </div>
          <div class="col-12">
            <label class="form-label">Authors</label>
            <input class="form-control" placeholder="Surname, First; Surname, First"/>
          </div>
          <div class="col-12">
            <label class="form-label">Abstract</label>
            <textarea class="form-control" rows="4" placeholder="Paste abstract…"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Adviser</label>
            <input class="form-control" placeholder="Adviser full name"/>
          </div>
          <div class="col-md-6">
            <label class="form-label">Year/Term</label>
            <input class="form-control" placeholder="2025 / 1st Sem"/>
          </div>
          <div class="col-12">
            <label class="form-label">Upload PDF</label>
            <input type="file" class="form-control" accept="application/pdf" />
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-success">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-upload me-2"></i>Import CSV</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p class="mb-2">Upload a CSV with columns: <code>title,type,campus,authors,submitted,status</code></p>
        <input type="file" class="form-control" accept=".csv" />
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary">Upload</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalReview" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-eye me-2"></i>Submission Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <div class="fw-bold">Automated Pattern Recognition of Inaul Fabrics</div>
            <div class="text-muted">Franco; Caparas • Isulan • Thesis • 2025/08/21</div>
          </div>
          <span class="status s-pending">Pending</span>
        </div>
        <p class="mb-0">Abstract preview goes here… (truncate or lazy-load full text). Attachments: main.pdf (1.2 MB), appendix.zip</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-danger"><i class="bi bi-x"></i> Reject</button>
        <button class="btn btn-success"><i class="bi bi-check2"></i> Approve</button>
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

<script>

</script>
</body>
</html>
