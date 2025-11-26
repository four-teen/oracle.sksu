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
  <title>SKSU – <?php echo $_SESSION['systemname'] ?> Portal</title>
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
        .select2-container--default .select2-selection--single {
            height: 37px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 37px; /* Apply line-height specifically to the text element */
        }
  </style>
</head>
<body onload="load_researches_extensions()">
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
      <!-- Quick Actions -->
      <div class="card-soft p-3 mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <div class="fw-semibold">Quick Actions</div>
          <button class="btn btn-info btn-warning" onclick="load_new_research()"><i class="bi bi-plus-circle"></i> Encode New Extension</button>
        </div>
            <div id="test">test</div>
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

<!-- MODALS Encode New Research -->
<div class="modal fade" id="modalNew" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i><span id="newModalTitle">Encode New Extension</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="frmResearch" class="row g-3">
          <input type="hidden" id="rId">

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
            <label class="form-label">Extension Title</label>
            <textarea class="form-control" id="rTitle" rows="4" placeholder="Enter extension title…"></textarea>
          </div>
    <div class="col-12">
      <label class="form-label">Sustainable Development Goals (SDGs)</label>
      <select id="rSDG" class="js-example-basic-multiple" multiple="multiple">
        <option value="1">SDG 1 – No Poverty</option>
        <option value="2">SDG 2 – Zero Hunger</option>
        <option value="3">SDG 3 – Good Health and Well-being</option>
        <option value="4">SDG 4 – Quality Education</option>
        <option value="5">SDG 5 – Gender Equality</option>
        <option value="6">SDG 6 – Clean Water and Sanitation</option>
        <option value="7">SDG 7 – Affordable and Clean Energy</option>
        <option value="8">SDG 8 – Decent Work and Economic Growth</option>
        <option value="9">SDG 9 – Industry, Innovation and Infrastructure</option>
        <option value="10">SDG 10 – Reduced Inequalities</option>
        <option value="11">SDG 11 – Sustainable Cities and Communities</option>
        <option value="12">SDG 12 – Responsible Consumption and Production</option>
        <option value="13">SDG 13 – Climate Action</option>
        <option value="14">SDG 14 – Life Below Water</option>
        <option value="15">SDG 15 – Life on Land</option>
        <option value="16">SDG 16 – Peace, Justice and Strong Institutions</option>
        <option value="17">SDG 17 – Partnerships for the Goals</option>
      </select>
    </div>


<!--           <div class="col-12">
            <label for="advisersID">Select Authors</label>
            <select id="advisersID" class="js-example-basic-single" name="state">
              <?php 
                $get_advisers = "SELECT * FROM `tblaccount` ORDER BY `accountid` ASC";
                $runget_advisers = mysqli_query($conn, $get_advisers);
                while($row_advisers = mysqli_fetch_assoc($runget_advisers)){
                  echo '<option value="'.$row_advisers['accountid'].'">'.$row_advisers['acc_name'].'</option>';
                }
              ?>
              
            </select>

          </div> -->
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


<!-- MODALS Encode Edit Research -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i><span id="newModalTitle">Edit Extension</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="frmResearchEdit" class="row g-3">
          <input type="hidden" id="eId">
          <div class="col-12">
            <label class="form-label">Title</label>
            <textarea class="form-control" id="eTitle" rows="4" placeholder="Enter extension title…"></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label">Type</label>
            <select class="form-select" id="eType" required>
              <?php 
                $get_research_type = "SELECT * FROM `tblmanuscript_type` ORDER BY manus_type_desc ASC";
                $runget_research_type = mysqli_query($conn, $get_research_type);
                while($row_get_research_type = mysqli_fetch_assoc($runget_research_type)){
                  echo'<option value="'.$row_get_research_type['manus_typeid'].'">'.$row_get_research_type['manus_type_desc'].'</option>';
                }

              ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Academic Year</label>
            <select class="form-select" id="eYear" required>
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
            <input class="form-control" id="eAuthors" required placeholder="Surname, First; Surname, First"/>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button onclick="updating_researches()" class="btn btn-success" id="btnSaveResearch" data-bs-dismiss="modal"><i class="bi bi-save2 me-1"></i>Save</button>
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
        <div id="research_status">Loading research details</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-danger"><i class="bi bi-x"></i> Done</button>
        <button class="btn btn-success"><i class="bi bi-check2"></i> Approved</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modaladviser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-person-badge text-primary me-2"></i> Select Adviser
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-body">
                <input type="hidden" id="titleid_adviser">
                <label class="form-label fw-semibold">Select name here...</label>
                <select id="get_adviser" class="js-example-basic-single form-control" name="state">
                  <?php 
                    $get_adviser = "SELECT * FROM `tblaccount` ORDER BY acc_name ASC";
                    $runget_adviser = mysqli_query($conn, $get_adviser);
                    while($row_get_adviser = mysqli_fetch_assoc($runget_adviser)){
                      echo '<option value="'.$row_get_adviser['accountid'].'">'.
                            htmlspecialchars($row_get_adviser['acc_name']).'</option>';
                    }
                  ?>
                </select>
                <div class="py-2">
                  <button onclick="saving_adviser()" class="btn btn-success btn-sm">Set Adviser</button>
                  <button onclick="saving_panelist()" class="btn btn-info btn-sm">Set Panel</button>
                  <button onclick="saving_critic()" class="btn btn-primary btn-sm">Set Critic</button>
                  <button onclick="saving_statistician()" class="btn btn-warning btn-sm">Set Statistician</button>
                </div>
              </div>
            </div>
          </div>


          <div class="col-12">
            <h5>Roles within the Committee</h5>
            <div id="research_adviser" class="border rounded p-3 bg-light border-success"></div>
            <br>
            <div id="research_panelist" class="border rounded p-3 bg-light border-info"></div>
            <br>
            <div id="research_critic" class="border rounded p-3 bg-light border-primary"></div>
            <br>
            <div id="research_statistician" class="border rounded p-3 bg-light border-warning"></div>            
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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





    function remove_panel(panelid,titleid){
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

          $.ajax({
              type: "POST",
              url: "query_researches.php",
              data: {
                  "deleting_title_panel": "1",
                  "panelid" : panelid
              },
              success: function (response) {
                loading_panelists(titleid);
                // $('#test').html(response);
                Swal.fire({
                  title: "Deleted!",
                  text: "Your file has been deleted.",
                  icon: "success"
                });
              }
          });  


        }
      });
    } 

    function saving_statistician(){
      var get_statistician = $('#get_adviser').val();
      var titleid_statistician = $('#titleid_adviser').val();

        $.ajax({
            type: "POST",
            url: "query_researches.php",
            data: {
                "saved_statistician": "1",
                "get_statistician" : get_statistician,
                "titleid_statistician" : titleid_statistician
            },
            success: function () {
               loading_statistician(titleid_statistician);
            }
        });   
    }

function loading_statistician(titleid){
    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "loaded_statistician": "1",
            "titleid" : titleid
        },
        success: function (response) {
          $('#research_statistician').html(response);
        }
    });   
}


function saving_critic(){
  var get_critic = $('#get_adviser').val();
  var titleid_critic = $('#titleid_adviser').val();
    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "saved_critic": "1",
            "get_critic" : get_critic,
            "titleid_critic" : titleid_critic
        },
        success: function (response) {
           loading_critic(titleid_critic);
           // $('#test').html(response);
        }
    });   
}

function loading_critic(titleid){
    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "loaded_critic": "1",
            "titleid" : titleid
        },
        success: function (response) {
          $('#research_critic').html(response);
        }
    });   
} 

function loading_panelists(titleid){
    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "loaded_panelist": "1",
            "titleid" : titleid
        },
        success: function (response) {
          $('#research_panelist').html(response);
        }
    });   
}  

function saving_panelist(){

  var get_panelist = $('#get_adviser').val();
  var titleid_panelist = $('#titleid_adviser').val();
    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "saved_panelist": "1",
            "get_panelist" : get_panelist,
            "titleid_panelist" : titleid_panelist
        },
        success: function (response) {
           loading_panelists(titleid_panelist);
           // $('#test').html(response);
        }
    });   
}

function loading_advisers(titleid){
    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "loading_adviser": "1",
            "titleid" : titleid
        },
        success: function (response) {
          $('#research_adviser').html(response);
        }
    });   
}

function saving_adviser(){
  var get_adviser = $('#get_adviser').val();
  var titleid_adviser = $('#titleid_adviser').val();
    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "saving_adviser": "1",
            "get_adviser" : get_adviser,
            "titleid_adviser" : titleid_adviser
        },
        success: function () {
           loading_advisers(titleid_adviser);
          // $('#research_adviser').html(response);
        }
    }); 
}

function adding_adviser(titleid) {
  // Show modal
  $('#titleid_adviser').val(titleid);
  loading_advisers(titleid);
  loading_panelists(titleid);
  loading_critic(titleid);  
  loading_statistician(titleid);
  $('#modaladviser').modal('show');

  // Run only once when modal is shown
  $('#modaladviser').off('shown.bs.modal').on('shown.bs.modal', function () {
    const $sel = $(this).find('.js-example-basic-single');

    // Destroy previous instance if exists
    if ($sel.hasClass("select2-hidden-accessible")) {
      $sel.select2('destroy');
    }

    // Initialize select2
    $sel.select2({
      theme: 'bootstrap4',               // match Bootstrap look
      width: '100%',                     // full width
      placeholder: 'Select an adviser',
      dropdownParent: $('#modaladviser') // render dropdown inside modal
    });
  });
}



// Recommended: use Bootstrap 5 API to show the modal
function load_new_research() {
  const modalEl = document.getElementById('modalNew');
  const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);
  
  // Re-bind a one-time handler each time you open
  $('#modalNew').one('shown.bs.modal', function () {
    const $sdg = $('.js-example-basic-multiple');   // your class
    // Avoid double init if user leaves modal open and re-triggers
    if (!$sdg.data('select2')) {
      $sdg.select2({
        placeholder: 'Select related SDGs',
        allowClear: true,
        closeOnSelect: false,
        dropdownParent: $('#modalNew') // CRITICAL for modals
      });
    } else {
      // If already initialized from a previous open, make sure dropdown is attached to the modal
      $sdg.select2('destroy');
      $sdg.select2({
        placeholder: 'Select related SDGs',
        allowClear: true,
        closeOnSelect: false,
        dropdownParent: $('#modalNew')
      });
    }
  });

  modal.show();
}




  function get_details_review(titleid){
    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "loading_preview": "1",
            "titleid" : titleid
        },
        success: function (response) {
          $('#research_status').html(response);
        }
    }); 
  }


  function change_status(titleid){
    get_details_review(titleid);
    $('#modalReview').modal('show');
  }

  function updating_researches(){
    var eTitle   = ($('#eTitle').val()   || '').trim();
    var eType    = ($('#eType').val()    || '').trim();
    var eYear    = ($('#eYear').val()    || '').trim();
    var eAuthors = ($('#eAuthors').val() || '').trim();
    var eId = $('#eId').val();

    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
            "update_title": "1",
            "eTitle" : eTitle,
            "eType" : eType,
            "eYear" : eYear,
            "eAuthors" : eAuthors,
            "eId" : eId
        },
        success: function (response) {
          load_researches_extensions();
          Swal.fire({
            title: "Updated!",
            text: "Record has been updated.",
            icon: "success"
          });
        }
    }); 
  }

  function edit_title(titleid,title,authors){
    $('#eId').val(titleid);
    $('#eTitle').val(title);
    $('#eAuthors').val(authors);
    $('#modalEdit').modal('show');
  }


    function remove_title(titleid){
 
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

          $.ajax({
              type: "POST",
              url: "query_researches.php",
              data: {
                  "deleting_title": "1",
                  "titleid" : titleid
              },
              success: function (response) {
                load_researches_extensions();
                $('#test').html(response);
                Swal.fire({
                  title: "Deleted!",
                  text: "Your file has been deleted.",
                  icon: "success"
                });
              }
          });  


        }
      });
    } 

  function saving_reserches(){
    var rTitle   = ($('#rTitle').val()   || '').trim();
    var rType    = 90001;
    var rYear    = ($('#rYear').val()    || '').trim();
    var rAuthors = '';
    var rSDGs    = $('#rSDG').val() || []; // array of selected SDGs

    // basic checks
    if(!rTitle){ $('#rTitle').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please enter the title.'}); return; }
    if(!rType){ $('#rType').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please select a type.'}); return; }
    if(!rYear){ $('#rYear').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please select an academic year.'}); return; }
    if(rSDGs.length === 0){ $('#rSDG').focus(); Swal.fire({icon:'warning', title:'Missing', text:'Please select at least one SDG.'}); return; }

    $.ajax({
        type: "POST",
        url: "query_researches.php",
        data: {
          saving_researches: 1,
          rTitle:   rTitle,
          rType:    rType,
          rYear:    rYear,
          rAuthors: rAuthors,
          rSDGs:    rSDGs // send the array
        },
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        success: function (response) {
          // $('#test').html(response);
          load_researches_extensions();
          Swal.fire({
            title: "Saved!",
            text: "Record has been inserted!",
            icon: "success"
          });
        }
    }); 


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

  function load_researches_extensions() {
    $('#loader').show();
    $('#content_area').hide();

    $.ajax({
      type: "POST",
      url: "query_researches.php",
      data: { "loading_extension": '1' },
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
 
    // For multi-select (SDGs)
    $('#rSDG').select2({
        dropdownParent: $('#modalNew')
    });
});


</script>

</body>
</html>
