<?php

    session_start();
    ob_start();
    include '../db.php';
    
    $query = $conn->query("SELECT * FROM `events` ORDER by id");



    
?> 
<!DOCTYPE html>
<html>
 <head>
    <title>Admin - Sultan Kudarat State University Calendar</title>
    <link href="../images/logo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>  
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <style type="text/css">
    body{ 
overflow-x: hidden;
    }
  </style>
 </head>
 <body onload="loadingyourevents()">
<!-- Image and text -->
        <nav class="navbar navbar-light bg-success">
          <a class="navbar-brand" href="#">
            <img src="../images/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
            SKSU Calendar - Admin <span class="float-right">
              <?php 
                echo $_SESSION['user_last_name']. ', '.$_SESSION['user_first_name']  ;

               ?>
               <ion-icon onclick="logouts()" name="log-out-outline" style="position: relative;top: 3px;"></ion-icon>
            </span>
            
          </a>
        </nav>
  <br />
  <div class="row">
    <div class="col-lg-12">

    <div class="row">
      <div class="col-lg-12">
        <div  style="padding: 15px;">
          <button onclick="managing_events()" class="btn btn-succes"><ion-icon name="add-circle-outline"></ion-icon> Manage Activity</button>
        </div>
        
         <div id="calendar" style="padding: 15px;"></div>
      </div>
      
    </div>        
    </div>  
  </div>

  <br>

  <script>

    function managing_events(){
      window.location="manage_events.php";
    }

    function logouts(){
      window.location='../logout.php';
    }
   
  $(document).ready(function() {
   var calendar = $('#calendar').fullCalendar({
    editable:true,
    header:{
     left:'prev,next today',
     center:'title',
     right:'month,agendaWeek,agendaDay'
    },
    events: '../load.php',
    eventColor: 'orange',
    selectable:true,
    selectHelper:true,

   });
  });
   

      function loadlogs(){
        var title = $('#title').val();
        var start_event = $('#start_event').val();
        var end_event = $('#end_event').val();
        var event_description = $('#event_description').val();

        var email = '<?php echo $_SESSION['user_email_address'] ?>';
        $.ajax({
          type: "POST",
          url: "user_query.php",
          data: {
              "logs" : '1',
              "title" : title,
              "start_event" : start_event,
              "end_event" : end_event,
              "event_description" : event_description,
              "email" : email
                      },
          success: function(){
             loadingyourevents()
          }            
        });
      }


      function loadingyourevents(){
        var email = '<?php echo $_SESSION['user_email_address'] ?>';
        $.ajax({
          type: "POST",
          url: "user_query.php",
          data: {
              "loading_events" : '1',
              "email" : email
                      },
          success: function(x){
             $('#loadingevents').html(x);
          }            
        });
      }


  </script>

 </body>
</html>
