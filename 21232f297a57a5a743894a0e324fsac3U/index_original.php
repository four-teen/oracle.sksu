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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ORACLE • Program Chair Dashboard</title>
  <link href="../images/logo.png" rel="icon">
  <link href="../images/logo.png" rel="apple-touch-icon">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">

  <!-- DataTables CSS (Bootstrap 5 + Responsive + Buttons) -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

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
  </style>
</head>
<body onload="load_researches()">
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
      <a class="active" href="users.php"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
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
      <div class="small" style="opacity:.85"><?php echo htmlspecialchars($program.' • '.$campus); ?></div>
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
      <!-- KPIs -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="card-soft p-3 h-100">
            <div class="text-muted small">My Program • Pending</div>
            <div class="fs-3 fw-bold">8</div>
            <div class="progress mt-2"><div class="progress-bar bg-warning" style="width:40%"></div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-soft p-3 h-100">
            <div class="text-muted small">Approved (30d)</div>
            <div class="fs-3 fw-bold">27</div>
            <div class="progress mt-2"><div class="progress-bar bg-success" style="width:70%"></div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-soft p-3 h-100">
            <div class="text-muted small">Submissions this term</div>
            <div class="fs-3 fw-bold">44</div>
            <div class="progress mt-2"><div class="progress-bar" style="width:55%"></div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-soft p-3 h-100">
            <div class="text-muted small">Embargoed</div>
            <div class="fs-3 fw-bold">3</div>
            <div class="progress mt-2"><div class="progress-bar bg-danger" style="width:15%"></div></div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card-soft p-3 mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <div class="fw-semibold">Quick Actions</div>
          <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalNew"><i class="bi bi-plus-circle"></i> Encode New Research</button>
          <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport"><i class="bi bi-upload"></i> Import CSV</button>
          <button class="btn btn-outline-primary btn-sm" id="btnExport"><i class="bi bi-download"></i> Export CSV</button>
        </div>
      </div>

      <!-- Program Analytics (lite) -->
      <div class="row g-3 mb-3">
        <div class="col-lg-6">
          <div class="card-soft p-3 h-100">
            <div class="fw-semibold mb-2">By Type (<?php echo htmlspecialchars($program); ?>)</div>
            <div class="d-flex align-items-center justify-content-between py-1 border-bottom"><span>Capstone</span><span class="badge text-bg-light">24</span></div>
            <div class="d-flex align-items-center justify-content-between py-1 border-bottom"><span>Thesis</span><span class="badge text-bg-light">15</span></div>
            <div class="d-flex align-items-center justify-content-between py-1"><span>Publication</span><span class="badge text-bg-light">5</span></div>
            <div class="mt-3 small muted">Tip: We can swap this list for a chart later.</div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card-soft p-3 h-100">
            <div class="fw-semibold mb-2">By Program</div>
            <div class="mb-2">Isulan <div class="progress"><div class="progress-bar" style="width:55%"></div></div></div>
            <div class="mb-2">Tacurong <div class="progress"><div class="progress-bar bg-success" style="width:35%"></div></div></div>
            <div class="mb-2">ACCESS <div class="progress"><div class="progress-bar bg-warning" style="width:22%"></div></div></div>
            <div class="small muted">(Counts are sample data; wire to DB later.)</div>
          </div>
        </div>
      </div>

      <!-- My Program Researches Table -->
      <div class="card-soft p-3">
        <div class="d-flex justify-content-between align-items-center py-2">
          <div class="fw-semibold">My Program Researches</div>
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

<!-- MODALS -->
<div class="modal fade" id="modalNew" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i><span id="newModalTitle">Encode New Research</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="frmResearch" class="row g-3">
          <input type="hidden" id="rId">
          <div class="col-12">
            <label class="form-label">Title</label>
            <textarea class="form-control" id="rTitle" rows="4" placeholder="Enter title…"></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label">Type</label>
            <select class="form-select" id="rType" required>
              <?php 
                $get_research_type = "SELECT * FROM `tblmanuscript_type` ORDER BY manus_type_desc ASC";
                $runget_research_type = mysqli_query($conn, $get_research_type);
                while($row_get_research_type = mysqli_fetch_assoc($runget_research_type)){
                  echo'<option value="'.$row_get_research_type['manus_typeid'].'">'.$row_get_research_type['manus_type_desc'].'</option>';
                }

              ?>
              <option>Capstone</option><option>Thesis</option><option>Dissertation</option><option>Publication</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Campus</label>
            <select class="form-select" id="rCampus" required>
              <?php 
                $get_campus = "SELECT * FROM `tblcampus` ORDER BY campusname ASC";
                $runget_campus = mysqli_query($conn, $get_campus);
                while($row_get_campus = mysqli_fetch_assoc($runget_campus)){
                  echo'<option value="'.$row_get_campus['campusid'].'">'.$row_get_campus['campusname'].'</option>';
                }

              ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Academic Year</label>
            <select class="form-select" id="rYear" required>
              <?php 
                $get_ay = "SELECT * FROM `tblacademic_year` ORDER BY ay_from ASC";
                $runget_ay = mysqli_query($conn, $get_ay);
                while($row_get_ay = mysqli_fetch_assoc($runget_ay)){
                  echo'<option value="'.$row_get_ay['ayid'].'">'.$row_get_ay['ay_from'].'-'.$row_get_ay['ay_to'].'</option>';
                }

              ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Authors</label>
            <input class="form-control" id="rAuthors" required placeholder="Surname, First; Surname, First"/>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button onclick="saving_reserches()" class="btn btn-success" id="btnSaveResearch"><i class="bi bi-save2 me-1"></i>Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-upload me-2"></i>Import Researches (CSV)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p class="mb-2">CSV columns: <code>title,type,campus,authors,submitted,status</code></p>
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
            <div class="fw-bold">IoT-based Smart Irrigation for Rice Fields</div>
            <div class="text-muted">Alonzo; Cruz • <?php echo htmlspecialchars($campus); ?> • Capstone • 2025/08/18</div>
          </div>
          <span class="status s-pending">Pending</span>
        </div>
        <p class="mb-0">Abstract preview goes here… Attachments: main.pdf</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-danger"><i class="bi bi-x"></i> Reject</button>
        <button class="btn btn-success"><i class="bi bi-check2"></i> Approve</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (required by DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS (Core + Bootstrap 5 + Responsive + Buttons) -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

function saving_reserches(){
  var rTitle   = ($('#rTitle').val()   || '').trim();
  var rType    = ($('#rType').val()    || '').trim();
  var rCampus  = ($('#rCampus').val()  || '').trim();
  var rYear    = ($('#rYear').val()    || '').trim();
  var rAuthors = ($('#rAuthors').val() || '').trim();

  // basic checks
  if(!rTitle){ $('#rTitle').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please enter the title.'}); return; }
  if(!rType){ $('#rType').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please select a type.'}); return; }
  if(!rCampus){ $('#rCampus').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please select a campus.'}); return; }
  if(!rYear){ $('#rYear').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please select an academic year.'}); return; }
  if(!rAuthors){ $('#rAuthors').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please enter author(s).'}); return; }

  var $btn = $('#btnSaveResearch');
  var old  = $btn.html();
  $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving…');

  $.ajax({
    type: 'POST',
    url: 'query_researches.php',
    dataType: 'json', // expect JSON
    data: {
      saving_researches: 1,
      rTitle:   rTitle,
      rType:    rType,
      rCampus:  rCampus,
      rYear:    rYear,
      rAuthors: rAuthors
    }
  })
  .done(function(res){
    // res should be: {status:'success'|'error', message:'...'}
    if(res.status === 'success'){
      Swal.fire({icon:'success', title:'Success', text: res.message || 'Saved.'});
      // close modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('modalNew'));
      if(modal) modal.hide();
      // reset form
      document.getElementById('frmResearch').reset();
      // refresh table
      if(typeof load_researches === 'function'){ load_researches(); }
    } else {
      Swal.fire({icon:'error', title:'Failed', text: res.message || 'Save failed.'});
    }
  })
  .fail(function(xhr){
    let msg = 'Network or server error.';
    try {
      const r = JSON.parse(xhr.responseText);
      if(r && r.message) msg = r.message;
    } catch(e){}
    Swal.fire({icon:'error', title:'Error', text: msg});
  })
  .always(function(){
    $btn.prop('disabled', false).html(old);
  });
}

  // Keep a reference so we can destroy/re-init on reloads
  let researchDT = null;

  function initResearchTable() {
    const $tbl = $('#tblResearch');
    if ($tbl.length === 0) return; // table not present (e.g., error)

    // Destroy previous instance if exists
    if ($.fn.DataTable.isDataTable($tbl)) {
      $tbl.DataTable().destroy();
    }

    researchDT = $tbl.DataTable({
      responsive: true,
      pageLength: 5,
      lengthMenu: [5, 10, 25, 50],
      order: [[0, 'desc']], // sort by ID
      dom:
        "<'row mb-2'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      buttons: [
        { extend: 'csv',   className: 'btn btn-info btn-sm',      text: '<i class="bi bi-download"></i> CSV' },
        { extend: 'excel', className: 'btn btn-success btn-sm',   text: '<i class="bi bi-file-earmark-excel"></i> Excel' },
        { extend: 'pdf',   className: 'btn btn-danger btn-sm',    text: '<i class="bi bi-file-earmark-pdf"></i> PDF' },
        { extend: 'print', className: 'btn btn-secondary btn-sm', text: '<i class="bi bi-printer"></i> Print' }
      ],
      language: { search: "", searchPlaceholder: "Search table…" }
    });

    // Wire topbar search to DT (rebind safely)
    $('#q').off('input.dtsearch').on('input.dtsearch', function () {
      researchDT.search(this.value).draw();
    });

    // Quick Actions Export CSV triggers DT CSV (rebind safely)
    $('#btnExport').off('click.dtcsv').on('click.dtcsv', function () {
      researchDT.button('.buttons-csv').trigger();
    });
  }

  function load_researches() {
    $('#loader').show();
    $('#content_area').hide();

    $.ajax({
      type: "POST",
      url: "query_researches.php",
      data: { "loading_researches": '1' },
      success: function (response) {
        // Inject the HTML first
        $('#content_area').html(response);

        // THEN init DataTables on the injected table
        initResearchTable();
      },
      error: function () {
        $('#content_area').html('<p class="text-danger">Error loading data.</p>');
      },
      complete: function () {
        setTimeout(() => {
          $('#loader').hide();
          $('#content_area').show();
        }, 300);
      }
    });
  }

  // Modal: Add/Edit prefill (delegated; buttons are in AJAX content)
  document.addEventListener('show.bs.modal', function (ev) {
    if (ev.target && ev.target.id === 'modalNew') {
      const btn = ev.relatedTarget;
      const f = document.getElementById('frmResearch');
      document.getElementById('newModalTitle').textContent =
        btn && btn.dataset.edit ? 'Edit Research' : 'Encode New Research';
      f && f.reset();
      if (btn && btn.dataset.edit) {
        try {
          const d = JSON.parse(btn.dataset.edit);
          document.getElementById('rId').value = d.id || '';
          document.getElementById('rTitle').value = d.title || '';
          document.getElementById('rType').value = d.type || 'Capstone';
          document.getElementById('rAuthors').value = d.authors || '';
          document.getElementById('rCampus').value = d.campus || '';
          document.getElementById('rYearTerm').value =
            (d.year ? d.year : '') + (d.term ? (' / ' + d.term) : '');
        } catch (err) {}
      }
    }
  });

  // Save (placeholder, same as before)
  document.addEventListener('DOMContentLoaded', function () {
    const modalNew = document.getElementById('modalNew');
    const btnSave = document.getElementById('btnSaveResearch');
    if (btnSave) {
      btnSave.addEventListener('click', () => {
        const title = document.getElementById('rTitle').value.trim();
        if (!title) { document.getElementById('rTitle').focus(); return; }
        btnSave.disabled = true; btnSave.textContent = 'Saving…';
        setTimeout(() => {
          btnSave.disabled = false; btnSave.textContent = 'Save';
          if (modalNew) bootstrap.Modal.getInstance(modalNew).hide();
          // After saving later: call load_researches() to refresh table
        }, 700);
      });
    }
  });
</script>

</body>
</html>
