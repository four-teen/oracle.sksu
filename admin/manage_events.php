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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
  <style type="text/css">
    body{ 
      overflow-x: hidden;
    }
    label{
      position: relative;
      top:8px;
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
      <div class="col-lg-12" style="padding-left:40px;padding-right:40px;">
      <p>
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
          <i class="fa fa-plus"></i> Add Activity
        </button>

<div class="btn-group" role="group" aria-label="Basic example" style="position: relative;">
  <button onclick="loadingyourevents()" type="button" class="btn btn-secondary">Card View</button>
  <button onclick="loadingyourevents_list()" type="button" class="btn btn-secondary">List View</button>
</div>

      </p>
      <div class="collapse" id="collapseExample">
        <div class="card card-body" style="padding: 15px;">
          <div class="row">

            <div class="col-lg-12">
              <label for="typeofevent">Type of Event</label>
              <select id="typeofevent" class="form-control">
                <option value="Meeting">Meeting</option>
                <option value="Seminar">Seminar</option>
                <option value="Exam">Exam</option>
              </select>
            </div>            
            <div class="col-lg-12">
              <label for="title">Event Title</label>
              <input type="text" class="form-control" id="title">
            </div>
            <div class="col-lg-6">
              <label for="start_event">Start Date/Time</label>
              <input type="datetime-local" class="form-control" id="start_event">
            </div>
            <div class="col-lg-6">
              <label for="end_event">End Date/Time</label>
              <input type="datetime-local" class="form-control" id="end_event">
            </div>
            <div class="col-lg-12">
              <label for="event_description">Event Description</label>
              <textarea id="event_description" cols="30" rows="8" class="form-control"></textarea>
            </div>
            <div class="col-lg-12">
              <br>
                <button onclick="loadlogs()" class="btn btn-primary">Save Event</button>
            </div>
          </div>
        </div>
      </div>
    <div class="row">
      <div class="col-lg-12">
        <div id="loadingevents">Loading Events..</div>
      </div>
<!--       <div class="col-lg-12">
        <div id="test">test</div>
      </div> -->
    </div>
      </div>        
    </div>        
    </div>  
  </div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script>
    function removeevent(id){
        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this imaginary file!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
              $.ajax({
                type: "POST",
                url: "user_query.php",
                data: {
                    "removingevents" : '1',
                    "id" : id
                            },
                success: function(){
                   loadingyourevents()
                }            
              });
          } else {
            
          }
        });
    }

    function manage_uploading(eventid){
      window.location='manage_uploads.php?id='+eventid;
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
        var typeofevent = $('#typeofevent').val();

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
              "email" : email,
              "typeofevent": typeofevent
                      },
          success: function(){
             loadingyourevents();
    swal("Success! New event successfuly created!", {
      icon: "success",
    });
    $('#event_description').val('');
document.getElementById("collapseExample").classList.remove("show");
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

      function loadingyourevents_list(){
        var email = '<?php echo $_SESSION['user_email_address'] ?>';
        $.ajax({
          type: "POST",
          url: "user_query.php",
          data: {
              "loading_events_list" : '1',
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
