<?php
    // Keep your original PHP block
    session_start();
    ob_start();

    include 'assets/db/db.php';
    include 'config.php';
    // This creates the working Google OAuth redirect link (server-side OAuth flow)
    $google_login_btn = '<a class="btn btn-google" href="'.$google_client->createAuthUrl().'"><i class="bi bi-google"></i> Sign in with SKSU Google</a>';

    $get_config = "SELECT * FROM `tblconfig`";
    $runget_config = mysqli_query($conn, $get_config);
    $row_config = mysqli_fetch_assoc($runget_config);
    $_SESSION['systemname'] = $row_config['systemname'];
    $_SESSION['systemcopyright'] = $row_config['systemcopyright'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SKSU – <?php echo $_SESSION['systemname'] ?> Portal</title>
  <link href="images/logo.png" rel="icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --text:#1f2428; --muted:#6b7280; --line:#e5e7eb; --brand:#0a58ff; --brand2:#00c6ff; --bg:#f9fafb; --card:#ffffff;
    }
    *{font-family:'Outfit',system-ui,Segoe UI,Roboto,Arial,sans-serif}
    body{background:var(--bg); color:var(--text)}
    .navbar{background:var(--card); border-bottom:1px solid var(--line)}
    .brand-title{font-weight:800}
    .hero{padding:6rem 0; background:linear-gradient(180deg,#ffffff 0%, #f6f8ff 100%); border-bottom:1px solid var(--line)}
    .hero h1{font-weight:800; font-size:clamp(2.2rem,4vw,3rem); margin-bottom:.5rem}
    .hero .lead{color:var(--muted)}
    .btn-google{display:inline-flex; align-items:center; gap:.6rem; border:1px solid #d0d7de; background:#fff; color:#0b1b3a; padding:.85rem 1.2rem; border-radius:12px; font-weight:600; text-decoration:none}
    .btn-google:hover{border-color:#b6c2cf; background:#f8fafc}
    .btn-google .bi-google{font-size:1.1rem; color:#ea4335}
    .pill{display:inline-block; border:1px solid var(--line); background:#fff; padding:.45rem .75rem; border-radius:999px; color:var(--muted); font-size:.9rem}
    .feature{background:var(--card); border:1px solid var(--line); border-radius:16px; padding:1.6rem; height:100%; transition:transform .2s ease, box-shadow .2s ease}
    .feature:hover{transform:translateY(-3px); box-shadow:0 10px 24px rgba(2,6,23,.06)}
    .feature i{font-size:1.5rem; background:linear-gradient(135deg,var(--brand),var(--brand2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent}
    .kpi{border-top:1px dashed var(--line)}
    footer{border-top:1px solid var(--line); background:var(--card)}
    .footer a{color:var(--muted); text-decoration:none}
    .footer a:hover{color:var(--brand)}
footer {
  background: url('images/sksu-seal.png') no-repeat center;
  background-size: 150px;
  background-color: #f8f9fa;
}

/* Transparent modal background */
.modal-backdrop.show {
  background-color: rgba(0, 0, 0, 0.5); /* semi-transparent dark */
}

/* Semi-transparent modal content */
#searchModal .modal-content {
  background-color: rgba(255, 255, 255, 0.5); /* white w/ slight transparency */
  /*backdrop-filter: blur(8px); */
  filter: blur(0.5px) grayscale(10%);
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}.result-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  cursor: pointer;
}

.result-card:hover {
  transform: scale(1.02);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
}
.text-gradient {
  background: linear-gradient(90deg, #0a58ff, #00c6ff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 800;
  text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
}
#features {
  position: relative;
  overflow: hidden;
  padding: 80px 0;
  background: #f4f4f4;
}

.falling-object {
  position: absolute;
  width: 40px;
  height: 40px;
  background-size: contain;
  background-repeat: no-repeat;
  animation: fall linear infinite;
  opacity: 0.8;
  z-index: 0;
}

@keyframes fall {
  0% {
    transform: translateY(-100px) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(100vh) rotate(360deg);
    opacity: 0;
  }
}

  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <a class="navbar-brand brand-title" href="#">ORACLE</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
          <li class="nav-item"><a class="nav-link" href="#impact">Impact</a></li>
        </ul>
        <div class="ms-lg-3">
          <!-- USE YOUR ORIGINAL WORKING GOOGLE OAUTH LINK -->
          <?php echo $google_login_btn; ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <header class="hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <span class="pill mb-3"><i class="bi bi-building me-2"></i>Managed by Research Development & Innovation Office</span>
          <h1>The <span class="text-gradient">Oracle</span></h1>
          <p class="lead">Centralized Research Repository for SKSU’s 7 Campuses for Capstones, theses, IPOs, copyrights, and international publications — curated and searchable.</p>
          <div class="d-flex gap-2 flex-wrap mt-3">
            <span class="pill"><i class="bi bi-check-circle me-1"></i> ACCESS</span>
            <span class="pill"><i class="bi bi-check-circle me-1"></i> Bagumbayan</span>
            <span class="pill"><i class="bi bi-check-circle me-1"></i> Isulan</span>
            <span class="pill"><i class="bi bi-check-circle me-1"></i> Tacurong</span>
            <span class="pill"><i class="bi bi-check-circle me-1"></i> Kalamansig</span>
            <span class="pill"><i class="bi bi-check-circle me-1"></i> Palimbang</span>
            <span class="pill"><i class="bi bi-check-circle me-1"></i> Lutayan</span>
          </div>
          <div class="mt-4">
            <!-- USE YOUR ORIGINAL WORKING GOOGLE OAUTH LINK -->
            <?php echo $google_login_btn; ?>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="feature">
            <div class="mb-2 fw-semibold">Quick Search</div>
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control border-start-0" placeholder="Search title, author, campus…"/>
            </div>
            <div class="row text-center mt-4 g-3">
              <div class="col-6">
                <div class="text-muted small">RESEARCHEs</div>
                <?php 
                  $get_research = "SELECT count(titleid) as research_count FROM `tblresearches`";
                  $runget_research = mysqli_query($conn, $get_research);
                  if($runget_research){
                    $row_research = mysqli_fetch_assoc($runget_research);
                    echo'<div class="fs-3 fw-bold">'.$row_research['research_count'].'</div>';
                  }

                ?>
               
              </div>
              <div class="col-6">
                <div class="text-muted small">ADVISERs</div>
                <?php 
                  $get_authors = "SELECT count(titleid) as authors_count FROM `tblresearches`
            INNER JOIN tbladviser ON tbladviser.adviser_paperid = tblresearches.titleid
            INNER JOIN tblaccount ON tblaccount.accountid = tbladviser.advise_accid";
                  $runget_authors = mysqli_query($conn, $get_authors);
                  if($runget_authors){
                    $row_authors = mysqli_fetch_assoc($runget_authors);
                    echo'<div class="fs-3 fw-bold">'.$row_authors['authors_count'].'</div>';
                  }

                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- FEATURES -->
  <section id="features" class="py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature text-center">
            <i class="bi bi-shield-lock-fill"></i>
            <h5 class="mt-2">Secure SSO</h5>
            <p class="text-muted">Server‑side Google OAuth (redirect) keeps accounts safe and integrates with your existing backend.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature text-center">
            <i class="bi bi-collection"></i>
            <h5 class="mt-2">Structured Repository</h5>
            <p class="text-muted">Faceted search, campus filters, and exportable citations for academic use.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature text-center">
            <i class="bi bi-graph-up"></i>
            <h5 class="mt-2">Impact & Analytics</h5>
            <p class="text-muted">Dashboards for submissions, approvals, downloads, and campus contributions.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- IMPACT STRIP -->
  <section id="impact" class="kpi py-5">
    <div class="container">
      <div class="row g-4 text-center">
        <div class="col-6 col-md-3">
          <div class="feature">
            <div class="text-muted small">Active Contributors</div>
            <div class="display-6 fw-bold">0</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="feature">
            <div class="text-muted small">Peer‑reviewers</div>
            <div class="display-6 fw-bold">0</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="feature">
            <div class="text-muted small">Indexed Papers</div>
            <div class="display-6 fw-bold">0</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="feature">
            <div class="text-muted small">Avg. Approval Time</div>
            <div class="display-6 fw-bold">0</div>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- Add this modal somewhere before </body> -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body">
        <div class="bg-white rounded shadow-lg p-4 position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
          <h5 class="mb-3">Search Results</h5>
          <div id="searchResults">
            <p class="text-muted">No results yet. Start typing to see matches…</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


  <!-- FOOTER -->
  <footer class="py-4">
    <div class="container footer text-center">
      <div class="mb-1 fw-bold">ORACLE</div>
      <div class="text-muted mb-1">© <span id="yr"></span> Sultan Kudarat State University • Research Development & Innovation Office</div>
      <div><a href="#">Privacy Policy</a> · <a href="#">Terms</a></div>
    </div>
  </footer>

  <script>
    document.getElementById('yr').textContent = new Date().getFullYear();
  </script>
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



<!-- Trigger it via search input keypress -->
<script>

  // When any modal is hidden, remove the backdrop and restore body scroll
  document.addEventListener('hidden.bs.modal', function () {
    const modals = document.querySelectorAll('.modal-backdrop');
    modals.forEach(m => m.remove());
    document.body.classList.remove('modal-open');
    document.body.style = ''; // Reset styles
  });

  const searchInput = document.querySelector('.input-group-lg input');
  searchInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      const keyword = searchInput.value.trim();
      if (keyword !== '') {
        fetch(`search_query.php?keyword=${encodeURIComponent(keyword)}`)
          .then(res => res.text())
          .then(html => {
            document.getElementById('searchResults').innerHTML = html;
            new bootstrap.Modal(document.getElementById('searchModal')).show();
          })
          .catch(err => {
            document.getElementById('searchResults').innerHTML = '<p class="text-danger">An error occurred.</p>';
          });
      }
    }
  });

document.querySelector('input[type="text"]').addEventListener("keydown", function(e){
  if(e.key === "Enter"){
    const keyword = this.value;
    fetch("search_query.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "keyword=" + encodeURIComponent(keyword)
    })
    .then(res => res.text())
    .then(html => {
      document.getElementById("searchResults").innerHTML = html;
      new bootstrap.Modal(document.getElementById('searchModal')).show();
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const icons = [
    // 'images/paper-icon.png',
    // 'images/folder-icon.png',
    // 'images/logo.png'
  ];

  const features = document.getElementById('features');

  for (let i = 0; i < 25; i++) {
    const icon = document.createElement('div');
    icon.className = 'falling-object';
    icon.style.left = Math.random() * 100 + 'vw';
    icon.style.animationDuration = (5 + Math.random() * 5) + 's';
    icon.style.animationDelay = (Math.random() * 5) + 's';

    // random icon
    const randomIcon = icons[Math.floor(Math.random() * icons.length)];
    icon.style.backgroundImage = `url(${randomIcon})`;

    features.appendChild(icon);
  }
});

</script>


</body>
</html>
