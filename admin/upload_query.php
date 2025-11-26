<?php
//upload.php

    session_start();
    ob_start();
    include '../db.php';

    // if($_SESSION['acc_name']==''){
    //     header('location:../index.php');
    // }

if($_FILES["file"]["name"] != '')
    {

     $test = explode('.', $_FILES["file"]["name"]);
     $filePath = realpath($_FILES["file"]["tmp_name"]);

     $ext = end($test);
     $name = $_FILES["file"]["name"];

    $d1 = new Datetime();
    $thename = $d1->format('U');
    $viewname = $thename.'_'.$name; 
     
     $location = '../uploads/' . $viewname;  
     move_uploaded_file($_FILES["file"]["tmp_name"], $location);

     //insert to database
     $insert="INSERT INTO `tbluploads` (`acc_id`, `filenamed`, `orig_filenamed`, `dateuploaded`) VALUES ('$_SESSION[acc_id]', '$viewname', '$name', CURRENT_TIMESTAMP)";
     $runinsert=mysqli_query($conn, $insert);

    }


?>

