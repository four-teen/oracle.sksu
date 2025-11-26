<?php     session_start();
    ob_start();
	include '../db.php';


	if(isset($_POST['removingevents'])){
		$delete = "DELETE FROM `events` WHERE id = '$_POST[id]'";
		$rundelete = mysqli_query($conn, $delete);
	}

	if(isset($_POST['removing_docs'])){
		$delete = "DELETE FROM `tbluploads` WHERE upid = '$_POST[upid]'";
		$rundelete = mysqli_query($conn, $delete);
		$filloc = '../uploads/'.$_POST['filenamed'];
		unlink($filloc);
	}

	if(isset($_POST['uploading_docs'])){
		echo 
		'
			<table class="table table-sm table-borderless table-hover">
			  <thead>
			    <tr>
			      <th>#</th>
			      <th>Filename</th>
			      <th></th>
			    </tr>
			  </thead>
			  <tbody>
		'; ?>
		<?php 
			$getsupp = "SELECT * FROM `tbluploads`";
			$rungetsupp = mysqli_query($conn, $getsupp);
			$count = 0;
			while($rowsupp = mysqli_fetch_assoc($rungetsupp)){
				echo
				'
			    <tr>
			      <td width="1%">'.++$count.'.</td>
			      <td>'.$rowsupp['orig_filenamed'].'</td>
			      <td style="white-space:nowrap" width="1%">
							<div class="btn-group" role="group" aria-label="Basic example">
							  <button type="button" class="btn btn-info btn-sm">View</button>
							  <button onclick="remove_supp(\''.$rowsupp['upid'].'\',\''.$rowsupp['filenamed'].'\')" type="button" class="btn btn-danger btn-sm">Remove</button>
							</div>
			      </td>
			    </tr>
				';
			}

		?>
		<?php echo'

			  </tbody>
			</table>
		';
	}

	if(isset($_POST['logs'])){

              $title = addslashes($_POST['title']);
              $start_event = $_POST['start_event'];
              $end_event = $_POST['end_event'];
              $event_description = addslashes($_POST['event_description']);

              $datestart = date("Y-m-d", strtotime($start_event));
              $dateend = date("Y-m-d", strtotime($end_event));
              $email = $_POST['email'];
							$typeofevent = addslashes($_POST['typeofevent']);


		$insert = "INSERT INTO `events` (`title`, `start_event`, `end_event`, `event_description`, `start_date_only`, `end_date_only`, `use_email`,`event_type`) VALUES ('$title', '$start_event', '$end_event', '$event_description', '$datestart', '$dateend', '$email','$typeofevent')";
		$runinsert = mysqli_query($conn, $insert);

	}

//list view

	if(isset($_POST['loading_events_list'])){
		echo
		'
			 <table class="table table-sm">
			 	<thead class="bg-warning">
			 		<tr>
			 			<th width="1%"></th>
			 			<th>Event Title</th>
			 			<th>Event Description</th>
			 			<th>Event Start</th>
			 			<th>Event End</th>
			 			<th width="1%"></th>
			 		</tr>
			 	</thead>
			 	<tbody>
		'; ?>
		<?php 
			$loadevent = "SELECT * FROM `events` WHERE use_email='$_POST[email]' order by start_event asc";
			$runloadevent = mysqli_query($conn, $loadevent);
			$count = 0;
			while($rowevents = mysqli_fetch_assoc($runloadevent)){
				echo
				'
			 		<tr>
			 			<td>'.++$count.'.</td>
			 			<td>'.$rowevents['title'].'</td>
			 			<td>'.$rowevents['event_description'].'</td>
			 			<td>'.date("M d, y",strtotime($rowevents['start_event'])).'<br>'.date("h:i a",strtotime($rowevents['start_event'])).'</td>
			 			<td>'.date("m d, y",strtotime($rowevents['end_event'])).'<br>'.date("h:i a",strtotime($rowevents['end_event'])).'</td>
			 			<td><button class="btn btn-sm btn-danger">Remove</button></td>
			 		</tr>
				';
			}
		 ?>
		<?php echo'

			 	</tbody>
			 </table>
		';


	}	

	//card view		
	if(isset($_POST['loading_events'])){
	echo
	'<div class="row">';
			$loadevent = "SELECT * FROM `events` WHERE use_email='$_POST[email]' order by start_event asc";
			$runloadevent = mysqli_query($conn, $loadevent);
			$count = 0;
			while($rowevents = mysqli_fetch_assoc($runloadevent)){
				echo
				'
				  <div class="col-lg-4">
				      <div class="card h-100 text-black bg-default mb-3">
				        <div class="card-header bg-warning">'.$rowevents['title'].'</div>
				        <div class="card-body h-100 d-flex flex-column justify-content-between" style="padding:15px;">
				          <h5 class="card-title">'.$rowevents['event_description'].'</h5>
							<blockquote class="blockquote">
							  <footer class="blockquote-footer">'.date("F d, Y h:i", strtotime($rowevents['start_event'])).'-'.date("F d, Y h:i", strtotime($rowevents['end_event'])).'</footer>
							</blockquote>
				        </div>
				        <div class="card-footer">
				        	<button onclick="manage_uploading(\''.$rowevents['id'].'\')" class="btn btn-success btn-sm mt-auto">Add Files</button>
									<button  onclick="removeevent(\''.$rowevents['id'].'\')" class="btn btn-danger btn-sm mt-auto">Remove</button>
				        </div>
				      </div>     
				  </div>
				';
			}		
echo'</div>';

	}



 ?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
 	<meta charset="UTF-8">
 	<meta name="viewport" content="width=device-width, initial-scale=1.0">
 	<title>Document</title>
 </head>
 <body>
 	<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
 	</script>
 </body>
 </html>
