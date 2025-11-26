<?php
// --- Session context (adjust to real auth) ---
    session_start();
    ob_start();

    include '../assets/db/db.php';
    
    if($_SESSION['user_email_address']==''){
      header('location:../index.php');
    }

$query = "SELECT 
            CONCAT(ay_from, '-', ay_to) AS acad_year,
            COUNT(*) AS total_researches
          FROM tblresearches
          INNER JOIN tblacademic_year ON tblacademic_year.ayid = tblresearches.ayid
          GROUP BY acad_year
          ORDER BY ay_from ASC";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'year' => $row['acad_year'],
        'total' => (int)$row['total_researches']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
