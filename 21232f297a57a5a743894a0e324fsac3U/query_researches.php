<?php 

    session_start();
    ob_start();

    include '../assets/db/db.php';
    mysqli_set_charset($conn, 'utf8mb4');
    
    if($_SESSION['user_email_address']==''){
      header('location:../index.php');
    }

	$chair_name = $_SESSION['user_first_name'].' '. $_SESSION['user_last_name'];
	$role = $_SESSION['acc_type'];
	$program = '';
	$campus  = $_SESSION['campus'];




if (isset($_POST['saved_statistician'])) {

    $get_statistician = mysqli_real_escape_string($conn, $_POST['get_statistician']);
    $titleid_statistician = mysqli_real_escape_string($conn, $_POST['titleid_statistician']); // this is critic_paperid now

    $insert = "INSERT INTO `tblstatistician` (`stat_accid`, `stat_paperid`)
               VALUES ('$get_statistician', '$titleid_statistician')
               ON DUPLICATE KEY UPDATE `stat_accid` = '$get_statistician'";

    $runinsert = mysqli_query($conn, $insert);
}

if(isset($_POST['loaded_statistician'])){
	$titleid = $_POST['titleid'];
    $select_statistician = "SELECT * FROM `tblstatistician`
        INNER JOIN tblaccount ON tblaccount.accountid = tblstatistician.stat_accid
        WHERE stat_paperid = '$titleid'";
    $runselect_statistician = mysqli_query($conn, $select_statistician);

    if ($runselect_statistician && mysqli_num_rows($runselect_statistician) > 0) {
        while ($row = mysqli_fetch_assoc($runselect_statistician)) {
            echo 'STATISTICIAN : ' . $row['acc_name'];
        }
    } else {
        echo 'STATISTICIAN: No research statistician assigned yet.';
    }

}



	if(isset($_POST['deleting_title_panel'])){
		$delete = "DELETE FROM `tblpanelist` WHERE panelid='$_POST[panelid]'";
		$rundelete = mysqli_query($conn, $delete);
	}

if (isset($_POST['saved_critic'])) {
    $get_critic = mysqli_real_escape_string($conn, $_POST['get_critic']);
    $titleid_critic = mysqli_real_escape_string($conn, $_POST['titleid_critic']); // this is critic_paperid now

    $insert = "INSERT INTO `tblcritic` (`critic_accid`, `critic_paperid`)
               VALUES ('$get_critic', '$titleid_critic')
               ON DUPLICATE KEY UPDATE `critic_accid` = '$get_critic'";

    $runinsert = mysqli_query($conn, $insert);
}


if(isset($_POST['loaded_critic'])){
    $titleid = mysqli_real_escape_string($conn, $_POST['titleid']);

    $select_critic = "SELECT * FROM `tblcritic`
        INNER JOIN tblaccount ON tblaccount.accountid = tblcritic.critic_accid
        WHERE critic_paperid = '$titleid'";
    $runselect_critic = mysqli_query($conn, $select_critic);

    if ($runselect_critic && mysqli_num_rows($runselect_critic) > 0) {
        while ($row = mysqli_fetch_assoc($runselect_critic)) {
            echo 'RESEARCH CRITIC : ' . $row['acc_name'];
        }
    } else {
        echo 'CRITIC: No research critic assigned yet.';
    }


}

if(isset($_POST['saved_panelist'])){

	$get_panelist = $_POST['get_panelist'];
	$titleid_panelist = $_POST['titleid_panelist'];

	$insert = "INSERT INTO `tblpanelist` (`panel_accid`, `panel_paperid`) VALUES ('$get_panelist', '$titleid_panelist')";
	$runinsert = mysqli_query($conn, $insert);

}

if (isset($_POST['loaded_panelist'])) {
    $titleid = mysqli_real_escape_string($conn, $_POST['titleid']);

    $select_panelist = "SELECT * FROM `tblpanelist`
        INNER JOIN tblaccount ON tblaccount.accountid = tblpanelist.panel_accid
        WHERE panel_paperid = '$titleid'";
        
    $runselect_panelist = mysqli_query($conn, $select_panelist);

    if ($runselect_panelist && mysqli_num_rows($runselect_panelist) > 0) {
        $count = 0;
        while ($row = mysqli_fetch_assoc($runselect_panelist)) {
            echo 'PANEL ' . ++$count . ': ' . $row['acc_name'] . '<i onclick="remove_panel(\''.$row['panelid'].'\',\''.$titleid.'\')" class="bi bi-trash text-danger" style="cursor:pointer" title="Remove"></i> <br>';
        }
    } else {
        echo 'PANELs: No panelist assigned yet.';
    }
}

if (isset($_POST['loading_adviser'])) {
    $titleid = $_POST['titleid'];

    $select = "SELECT * FROM `tbladviser`
        INNER JOIN tblaccount on tblaccount.accountid = tbladviser.advise_accid
        WHERE adviser_paperid = '$titleid' LIMIT 1";
    $runselect = mysqli_query($conn, $select);

    if ($runselect && mysqli_num_rows($runselect) > 0) {
        $rowselect = mysqli_fetch_assoc($runselect);
        echo 'ADVISER: ' . $rowselect['acc_name'];
    } else {
        echo 'ADVISER: <i>None assigned yet</i>'; // or just leave blank
    }
}

	if(isset($_POST['saving_adviser'])){
		$get_adviser = $_POST['get_adviser'];
		$titleid_adviser = $_POST['titleid_adviser'];		
	    $insert = "INSERT INTO `tbladviser` (`advise_accid`, `adviser_paperid`) 
               VALUES ('$get_adviser', '$titleid_adviser')
               ON DUPLICATE KEY UPDATE advise_accid = VALUES(advise_accid)";
		$runinsert = mysqli_query($conn, $insert);
	}

	if(isset($_POST['loading_preview'])){
		$select = "SELECT * FROM `tblresearches`
		INNER JOIN tblacademic_year on tblacademic_year.ayid=tblresearches.ayid
		INNER JOIN tblmanuscript_type on tblmanuscript_type.manus_typeid=tblresearches.typeid LIMIT 1";
		$runselect = mysqli_query($conn, $select);
		$rowselect = mysqli_fetch_assoc($runselect);

		echo
		'
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <div class="fw-bold">'.$rowselect['title'].'</div>
            <div class="text-muted">AUTHORs: '.strtoupper($rowselect['authors']).' • '.htmlspecialchars($campus).'</div>
          </div>
          <span class="status s-pending">'.$rowselect['status'].'</span>
        </div>
        <p class="mb-0">'.$rowselect['acronym'].' • '.$rowselect['ay_from'].'-'.$rowselect['ay_to'].'</p>        
		';
	}


	if(isset($_POST['update_title'])){
		$eTitle = addslashes($_POST['eTitle']);
		$eType = $_POST['eType'];
		$eYear = $_POST['eYear'];
		$eAuthors = $_POST['eAuthors'];
		$update = "UPDATE `tblresearches` SET `title`='$eTitle', `typeid`='$eType', `ayid`='$eYear', `authors`='$eAuthors' WHERE titleid='$_POST[eId]'";
		$runupdate = mysqli_query($conn, $update);
	}

	if(isset($_POST['deleting_title'])){

		$delete = "DELETE FROM `tblresearches` WHERE titleid='$_POST[titleid]'";
		$rundelete = mysqli_query($conn, $delete);

	}
	
	// if (isset($_POST['saving_researches'])) {
	//     header('Content-Type: application/json');

	//     $title    = trim($_POST['rTitle']   ?? '');
	//     $typeid   = intval($_POST['rType']  ?? 0);
	//     $campusid = intval($_SESSION['campusid']?? 0);
	//     $ayid     = intval($_POST['rYear']  ?? 0);
	//     $authors  = trim($_POST['rAuthors'] ?? '');

	//     if ($title === '' || $typeid <= 0 || $campusid <= 0 || $ayid <= 0 || $authors === '') {
	//         http_response_code(422);
	//         echo json_encode(['status'=>'error','message'=>'Please complete all required fields.']);
	//         exit;
	//     }


	//     // Insert (default status: Pending; change to 'Approved' if you really want auto-approve)
	//     $sql = "INSERT INTO tblresearches (title, typeid, campusid, ayid, authors, status)
	//             VALUES (?, ?, ?, ?, ?, 'On-going')";
	//     $stmt = mysqli_prepare($conn, $sql);
	//     if (!$stmt) {
	//         http_response_code(500);
	//         echo json_encode(['status'=>'error','message'=>'Prepare failed: '.mysqli_error($conn)]);
	//         exit;
	//     }
	//     mysqli_stmt_bind_param($stmt, 'siiis', $title, $typeid, $campusid, $ayid, $authors);

	//     if (!mysqli_stmt_execute($stmt)) {
	//         http_response_code(500);
	//         echo json_encode(['status'=>'error','message'=>'Insert failed: '.mysqli_stmt_error($stmt)]);
	//         mysqli_stmt_close($stmt);
	//         exit;
	//     }

	//     $id = mysqli_insert_id($conn);
	//     mysqli_stmt_close($stmt);

	//     echo json_encode([
	//         'status'  => 'success',
	//         'titleid' => $id,
	//         'message' => 'New research title added successfully.'
	//     ]);
	//     exit;
	// }

	if(isset($_POST['saving_researches'])){
		mysqli_set_charset($conn, "utf8mb4");
		
		$rTitle = mysqli_real_escape_string($conn, $_POST['rTitle']);
		$rType = $_POST['rType'];
		$rCampus = $_SESSION['campusid'];
		$rYear = $_POST['rYear'];
		$rAuthors = $_POST['rAuthors'];
		$rSDGs = $_POST['rSDGs']; // this is an array

	    // Convert array to comma-separated string
	    $sdgList = implode(",", $rSDGs);

		$insert = "INSERT INTO `tblresearches` (`title`, `typeid`, `campusid`, `ayid`, `authors`, `sdgs`, `status`, `submitted_at`, `updated_at`, `encoder`, `programid`) VALUES ('$rTitle', '$rType', '$rCampus', '$rYear', '$rAuthors', '$sdgList', 'On-going', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$_SESSION[acc_id]', '$_SESSION[programid]')";
		$runinsert = mysqli_query($conn, $insert);
		// echo $insert;
	}


	if(isset($_POST['loading_researches'])){
		echo
		''; ?>

        <div class="table-responsive"  style="pading:15px;">
          <table class="table table-hover align-middle m-0" id="tblResearch" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>TITLE</th>
                <th>SDG</th>
                <th>AUTHORs</th>
                <th>ADVISER</th>
                <th>TYPE</th>
                <th>AY</th>
                <th>STATUS</th>
                <th width="180">Actions</th>
              </tr>
            </thead>
            <tbody>
				<?php 
						$select = "SELECT * 
						FROM `tblresearches`
						INNER JOIN tblacademic_year ON tblacademic_year.ayid = tblresearches.ayid
						INNER JOIN tblmanuscript_type ON tblmanuscript_type.manus_typeid = tblresearches.typeid
						LEFT JOIN tbladviser ON tbladviser.adviser_paperid = tblresearches.titleid
						LEFT JOIN tblaccount ON tblaccount.accountid = tbladviser.advise_accid
						ORDER BY title ASC";
						// $select = "SELECT * 
						// FROM `tblresearches`
						// INNER JOIN tblacademic_year ON tblacademic_year.ayid = tblresearches.ayid
						// INNER JOIN tblmanuscript_type ON tblmanuscript_type.manus_typeid = tblresearches.typeid
						// LEFT JOIN tbladviser ON tbladviser.adviser_paperid = tblresearches.titleid
						// LEFT JOIN tblaccount ON tblaccount.accountid = tbladviser.advise_accid 
						// WHERE tblresearches.programid='$_SESSION[programid]'
						// ORDER BY title ASC";


						$runselect = mysqli_query($conn, $select);
						$count = 0;
						while($rowselect = mysqli_fetch_assoc($runselect)){
					    $rSDGs = $rowselect['sdgs'];

					    // If you want it as an array:
					    $sdgArray = explode(",", $rSDGs);

					    // Example: join back with comma + space
					    $sdgList = implode(", ", $sdgArray);

							echo
							'
				              <tr>
				                <td width="1%">'.++$count.'.</td>
				                <td width="30%">'.strtoupper($rowselect['title']).'</td>
				                <td>'.strtoupper($sdgList).'</td>
				                <td>'.strtoupper($rowselect['authors']).'</td>
				                <td>'.strtoupper($rowselect['acc_name']).'</td>
				                <td>'.$rowselect['acronym'].'</td>
				                <td>'.$rowselect['ay_from'].'-'.$rowselect['ay_to'].'</td>
				                <td><span class="status s-pending">Ongoing</span></td>
				                <td width="1%">
				                  <div class="btn-group btn-group-sm">
				                  	<button onclick="adding_adviser(\''.$rowselect['titleid'].'\')" title="Add Adviser" class="btn btn-primary"><i class="bi bi-person"></i></button>
				                    <button onclick="change_status(\''.$rowselect['titleid'].'\')" title="Change Status" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalReview"><i class="bi bi-eye"></i></button>
				                    <button onclick="edit_title(\''.$rowselect['titleid'].'\', \''.$rowselect['title'].'\', \''.$rowselect['authors'].'\', \''.$sdgList.'\')" class="btn btn-info"><i class="bi bi-pencil"></i></button>
				                    <button onclick="remove_title(\''.$rowselect['titleid'].'\')" class="btn btn-danger"><i class="bi bi-trash"></i></button>
				                  </div>
				                </td>
				              </tr>
							';
						}

				?>
            </tbody>
          </table>
        </div>
		<?php echo'';
		
	}
?>

