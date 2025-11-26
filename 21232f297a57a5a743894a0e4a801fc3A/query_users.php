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


	if(isset($_POST['updating_account'])){

		$acc_type = $_POST['acc_type'];
		$accountid = $_POST['accountid'];

		if($acc_type==0){
		$update = "UPDATE `tblaccount` SET acc_type=1 WHERE accountid='$accountid'";
		$runupdate = mysqli_query($conn, $update);
		}else{
			$update = "UPDATE `tblaccount` SET acc_type=0 WHERE accountid='$accountid'";
			$runupdate = mysqli_query($conn, $update);			
		}


	}

	if(isset($_POST['updating_program_assigned'])){

		$titleprogramidid = $_POST['titleprogramidid'];
		$accountid = $_POST['accountid'];

		$update = "UPDATE `tblaccount` SET programid='$titleprogramidid' WHERE accountid='$accountid'";
		$runupdate = mysqli_query($conn, $update);
	}


	if(isset($_POST['loading_accounts'])){
		echo
		''; ?>

        <div class="table-responsive"  style="pading:15px;">
          <table class="table table-hover align-middle m-0" id="tblResearch" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>ACCOUNT NAME</th>
                <th>EMAIL</th>
                <th>ACCOUNT TYPE</th>
                <th>PROGRAM</th>
                <th width="180">Actions</th>
              </tr>
            </thead>
            <tbody>
				<?php 
						$select = "SELECT * FROM `tblaccount`
						LEFT JOIN tblcourse on tblcourse.courseid = tblaccount.programid";
						$runselect = mysqli_query($conn, $select);
						$count = 0;
						while($rowselect = mysqli_fetch_assoc($runselect)){
							echo
							'
				              <tr>
				                <td width="1%">'.++$count.'.</td>
				                <td width="30%">'.strtoupper($rowselect['acc_name']).'</td>
				                <td>'.strtoupper($rowselect['email']).'</td>
				                <td>'.($rowselect['acc_type']==0?'<button onclick="change_account(\''.$rowselect['accountid'].'\', \''.$rowselect['acc_type'].'\')" class="btn btn-info btn-sm w-100">User</button>':'<button onclick="change_account(\''.$rowselect['accountid'].'\', \''.$rowselect['acc_type'].'\')" class="btn btn-primary btn-sm w-100">Administrator</button>').'</td>
				                <td>'.$rowselect['coursecode'].'</td>
				                <td width="1%" class="text-nowrap">
				                  <div class="btn-group btn-group-sm">
				                  	<button onclick="open_modalchangerogram(\''.$rowselect['accountid'].'\')" title="Change program assignment" class="btn btn-warning"><i class="bi bi-arrows-collapse-vertical"></i> Change Program</button>
				                    <button onclick="remove_account(\''.$rowselect['accountid'].'\')" title="Remove account" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalReview"><i class="bi bi-eye"></i></button>
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

