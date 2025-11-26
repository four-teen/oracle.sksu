<?php
    session_start();
    ob_start();

    include '../assets/db/db.php';
    
    if($_SESSION['user_email_address']==''){
      header('location:../index.php');
    }
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
<body onload="load_accounts()">
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

      <!-- Table -->
      <div class="card-soft px-3 py-3">
        <div class="d-flex justify-content-between align-items-center p-3">
          <div class="fw-semibold">User management</div>

        </div>
        <div class="row">
          <div class="col-lg-12 intro3">
            <!-- <div id="test">test</div> -->
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
<?php 
  include 'offcanvas.php';
?>

<!-- MODALS -->
<div class="modal fade" id="modalchangerogram" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>New Program Assigned</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <form class="row g-3">
              <div class="row">
                <div class="col-lg-12">
                  <input type="hidden" id="theaccountid">
                  <label for="programid">Select Program</label>
                  <select class="js-example-basic-single" name="state" id="programid">
                    <?php 
                       $select = "SELECT * FROM `tblcourse`";
                       $runselect = mysqli_query($conn, $select);
                       while($rowselect = mysqli_fetch_assoc($runselect)){
                          echo'<option value="'.$rowselect['courseid'].'">'.$rowselect['coursecode'].' - '.$rowselect['coursedescription'].'</option>';
                       }
                    ?>
                    
                  </select>
                </div>
              </div>

            </form>
          </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button onclick="update_program_assigned()" class="btn btn-success" data-bs-dismiss="modal">Update</button>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Bootstrap bundle (load ONCE only) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

  function change_account(accountid, acc_type){
    var at = '';
    if(acc_type==0){
      at = 'User';
    }else{
      at = 'Administrator';
    }

    Swal.fire({
      title: "Are you sure?",
      text: "You are going to change the account into "+at+'!',
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, change it!"
    }).then((result) => {
      if (result.isConfirmed) {

        $.ajax({
            type: "POST",
            url: "query_users.php",
            data: {
                "updating_account": "1",
                "acc_type" : acc_type,
                "accountid" : accountid
            },
            success: function (response) {
              load_accounts();
            }
        });

      }
    });      
  }

  function  remove_account(){
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: "Deleted!",
          text: "Your file has been deleted.",
          icon: "success"
        });
      }
    });    
  }

  function update_program_assigned(){
    var programid = $('#programid').val();
    var accountid = $('#theaccountid').val();

    $.ajax({
        type: "POST",
        url: "query_users.php",
        data: {
            "updating_program_assigned": "1",
            "titleprogramidid" : programid,
            "accountid" : accountid
        },
        success: function (response) {
          load_accounts();
          Swal.fire({
            title: "Assigned!",
            text: "Your new program assignement is set.",
            icon: "success"
          });
        }
    }); 
  }
  


function open_modalchangerogram(accountid){
  $('#theaccountid').val(accountid);
  $('#modalchangerogram').modal('show');

  // Reinitialize Select2 inside modal on every open
  setTimeout(() => {
    $('#programid').select2({
      dropdownParent: $('#modalchangerogram'),
      width: '100%' // Optional: helps keep layout consistent
    });
  }, 100); // Slight delay ensures modal is fully rendered
}


  // Keep a reference so we can destroy/re-init on reloads
  let researchDT = null;

function initResearchTable() {
  const $tbl = $('#tblResearch');
  if ($tbl.length === 0) return;

  if ($.fn.DataTable.isDataTable($tbl)) {
    $tbl.DataTable().destroy();
  }

  researchDT = $tbl.DataTable({
    responsive: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    order: [[1, 'asc']], // Now we sort by TITLE (column index 1), not the count column
    columnDefs: [
      {
        targets: 0,           // First column (row number)
        searchable: false,
        orderable: false
      }
    ],
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

  // 🧠 Add this block to re-number the first column after sort/filter/draw
  researchDT.on('order.dt search.dt draw.dt', function () {
    let i = 1;
    researchDT.column(0, { search: 'applied', order: 'applied' })
      .nodes()
      .each(function (cell) {
        cell.innerHTML = i++;
      });
  }).draw();

  // Rebind topbar search
  $('#q').off('input.dtsearch').on('input.dtsearch', function () {
    researchDT.search(this.value).draw();
  });

  $('#btnExport').off('click.dtcsv').on('click.dtcsv', function () {
    researchDT.button('.buttons-csv').trigger();
  });
}

  function load_accounts() {
    $('#loader').show();
    $('#content_area').hide();


    $.ajax({
      type: "POST",
      url: "query_users.php",
      data: { "loading_accounts": '1' },
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

$(document).ready(function() {
    // Correct initialization of Select2 with the dropdownParent option
    $('.js-example-basic-single').select2({
        dropdownParent: $('#modalchangerogram')
    });
});
</script>
</body>
</html>
