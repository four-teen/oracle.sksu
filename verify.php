<?php
    session_start();
    include 'assets/db/db.php';
    include 'config.php';


if(isset($_GET["code"]))
{
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
    if(!isset($token['error']))
    {
        $google_client->setAccessToken($token['access_token']);
        $_SESSION['access_token'] = $token['access_token'];
        $google_service = new Google_Service_Oauth2($google_client);
        $data = $google_service->userinfo->get();

        if ($data === null) {
            die("Failed to retrieve user data from Google.");
        }

        if(!empty($data['given_name']))
        {
            $_SESSION['user_first_name'] = $data['given_name'];
        }

        if(!empty($data['family_name']))
        {
            $_SESSION['user_last_name'] = $data['family_name'];
        }

        if(!empty($data['email']))
        {
            $_SESSION['user_email_address'] = $data['email'];
        }

        if(!empty($data['gender']))
        {
            $_SESSION['user_gender'] = $data['gender'];
        }

        if(!empty($data['picture']))
        {
            $_SESSION['user_image'] = $data['picture'];
        }
    }
    else {
        // Handle error with token retrieval
        $error = $token['error'];
        error_log("Error fetching access token: " . $error);
        die("Failed to fetch access token from Google.");
    }
}
else {
    // Handle error with code parameter
    error_log("No code parameter in request.");
    die("Invalid request.");
}

// echo $_SESSION['user_last_name'].'-'.$_SESSION['user_email_address'];

?> 
<!DOCTYPE html>
<html>
 <head>
  <title>Admin - Sultan Kudarat State University</title>
  <link href="images/logo.png" rel="icon">


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
  <script>
  </script>
 </head>
 <body>

<?php 

    

// Ensure session variable exists
if (!isset($_SESSION['user_email_address']) || empty($_SESSION['user_email_address'])) {
    die("Error: User email address is not set in the session.");
}

$user_email = $_SESSION['user_email_address'];

// Query to check account
$check = "SELECT * FROM `tblaccount` 
          INNER JOIN `tblcampus` ON tblcampus.campusid = tblaccount.campus 
          WHERE email = '$user_email' 
          LIMIT 1";

$runcheck = mysqli_query($conn, $check);

if (!$runcheck) {
    die("Query Failed: " . mysqli_error($conn)); // Debugging purpose
}

// Check if any record is found
if (mysqli_num_rows($runcheck) == 1) {
    $rowcheck = mysqli_fetch_assoc($runcheck);

    // Store session variables
    $_SESSION['acc_id'] = $rowcheck['accountid'];
    $_SESSION['acc_type'] = $rowcheck['acc_type'];
    $_SESSION['campus'] = $rowcheck['campusname'];
    $_SESSION['campusid'] = $rowcheck['campus'];
    $_SESSION['programid'] = $rowcheck['programid'];

    // Redirect based on account type
    if ($rowcheck['acc_type'] == '1') { // Administrator
        header('Location: 21232f297a57a5a743894a0e4a801fc3A/');
        exit();
    } elseif ($rowcheck['acc_type'] == '0') { // Professor
        header('Location: 21232f297a57a5a743894a0e324fsac3U/');
        exit();
    }else{
        header('location:index.php');
    }
} else {
    // Invalid account message
    echo '
    <div class="row">
        <div class="col-lg-4 offset-lg-4 text-center">
            <div class="card">
                <img src="images/lockdown.jpg" class="card-img-top" alt="lockdown">
                <div class="card-body">
                    <h5 class="card-title">Invalid SKSU Account!</h5>
                    <p class="card-text">
                        Sorry, we were unable to log you in with the information provided. 
                        Please double-check your username and password and try again. 
                        If you\'ve forgotten your password, click the "Forgot Password" link below to reset it. 
                        If you continue to experience issues, please contact our support team for further assistance. 
                        Please contact the ICT.
                    </p>
                    <a href="index.php" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>';
}

 ?>


 </body>
</html>
