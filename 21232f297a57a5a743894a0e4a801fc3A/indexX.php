<?php 
    session_start();
    ob_start();

    include '../assets/db/db.php';
    
    if($_SESSION['user_email_address']==''){
      header('location:../index.php');
    }


 ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $_SESSION['systemname']; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../images/logo.png" rel="icon">
  <link href="../images/logo.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/@jarstone/dselect/dist/css/dselect.css">
  <link href="../assets/css/style.css" rel="stylesheet">

<style>
.dropdown-divider {
  border: 1px solid #ddd;
  margin: 8px 0;
}  

</style>

</head>

<body onload="loading_subjects();">


  <!-- ======= Sidebar ======= -->
  <?php 
    include 'header.php';  
    include 'sidebar.php';
  ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">
            <!-- Reports -->
            <div class="col-12">
              <div class="card">



                <div class="card-body">
                  <h5 class="card-title"></h5>

                  <div class="row">
                    <div class="col-lg-4">
                      <div class="card">
                        <div class="card-header bg-info">
                          Faculty Management
                        </div>
                        <div class="card-body">
                          <h5 class="card-title">WORKLOAD</h5>
                          <p class="card-text">Managing faculty subjects</p>
                          <a href="manage_subjects.php" class="btn btn-primary">Manage</a>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="card">
                        <div class="card-header bg-warning">
                          Syllabus Collection
                        </div>
                        <div class="card-body">
                          <h5 class="card-title">COLLECTIONS</h5>
                          <p class="card-text">Review syllabus submission</p>
                          <a href="manage_subjects_syllabus.php" class="btn btn-primary">Show Collections</a>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="card">
                        <div class="card-header bg-primary">
                          Faculty Evaluation
                        </div>
                        <div class="card-body">
                          <h5 class="card-title">EVALUATIONS</h5>
                          <p class="card-text">Review faculty evaluations</p>
                          <a href="faculty_evaluation.php" class="btn btn-primary">Evaluate</a>
                        </div>
                      </div>
                    </div>                    


                    </div>
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="card">
                          <div class="card-header bg-info">
                            SUMMARY
                          </div>
                          <div class="card-body">
                            <h5 class="card-title">EVALUATION SUMMARY</h5>
                            <p class="card-text">Review evaluations summary</p>
                            <a href="evaluations_summary.php" class="btn btn-primary">Show</a>
                          </div>
                        </div>
                      </div>                       
                    </div>

                    </div>

                  </div>
                         




<!--                   <div class="row">
                    <div class="col-lg-12">
                      <div id="main_data">Loading details</div>
                      <div id="loader" style="display: none;" class="text-center">
                        <img src="../loader.gif" alt="Loading..." width="10%">
                      </div>                      
                    </div>
                  </div> -->

                  </div>

              </div>
            </div><!-- End Reports -->

          </div>
        </div><!-- End Left side columns -->

 
      </div>
    </section>

  </main><!-- End #main -->

 
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="https://unpkg.com/@jarstone/dselect/dist/js/dselect.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.1.6/js/dataTables.bootstrap5.js"></script>
  <script src="../assets/js/main.js"></script>

  <script>

    function mission(){
      loaded_mission();
      $('#modal_mission').modal('show');
    }

    function vision(){
      loaded_vision();
      $('#modal_vision').modal('show');
    }    

    function sgoals(){
      loaded_sg();
      $('#modal_sg').modal('show');
    }    

    function ioutcomes(){
      loading_io();
      $('#modal_io').modal('show');
    }     

    function programs_po(){
      loading_po();
      $('#modal_po').modal('show');
    }

   function prospectus(){
    window.location = 'prospectus.php';
   }
   function index(){
    window.location = 'index.php';
   }
  </script>

</body>

</html>