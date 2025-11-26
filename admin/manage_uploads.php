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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <style type="text/css">
    body{ 
overflow-x: hidden;
    }
  </style>
 </head>
 <body onload="loadingyourevents();loadinguploaded()">
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
    <div class="col-lg-12 text-center">

      <input type="file" name="file" id="file" style="border: thin solid green;padding-left: 110px;padding-right: 0px;padding-top: 15px;padding-bottom: 15px;border-radius: 40px;"/>  

<div id="loader-icon" style="display: none; position: relative;" class="text-center"><img src="loader.gif" width="30%" />

      <footer class="blockquote-footer">Please upload related files here! It is reccomended that the filename is related to the content. 
      <cite title="profile" style="color:blue"></cite>      
    </div> 
  </div>
  <br>
  <br>
  <br>
  <hr>

  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <!-- <div id="test">test</div> -->
        <div id="supportfiles">Loading files</div>
      </div>
    </div>
  </div>

  <script>

    function remove_supp(upid,filenamed){
      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this selected file!",
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
                "removing_docs" : '1',
                "upid" : upid,
                "filenamed" : filenamed
                        },
            success: function(x){
               loadinguploaded();
               // $('#test').html(x);
            }            
          });

        } else {
          swal("Delete operation cancelled!");
        }
      });      
    }

    function loadinguploaded(){
        $.ajax({
          type: "POST",
          url: "user_query.php",
          data: {
              "uploading_docs" : '1'
                      },
          success: function(x){
             $('#supportfiles').html(x);
          }            
        });
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

$(document).ready(function(){
 $(document).on('change', '#file', function(){
  var name = document.getElementById("file").files[0].name;
  var form_data = new FormData();
  var ext = name.split('.').pop().toLowerCase();

  if(jQuery.inArray(ext, ['gif','png','jpg','jpeg','JPG','JPEG','PNG', 'avi', 'pdf', 'PDF', 'mp4', 'avi', 'mkv', 'doc', 'DOC', 'docx', 'DOCX', 'xls', 'XLS', 'xlsx', 'XLSX', 'ppt', 'PPT', 'pptx', 'PPTX']) == -1) 
    {
     alert("Invalid Image File");
     return false;
    }
      var oFReader = new FileReader();
      oFReader.readAsDataURL(document.getElementById("file").files[0]);
      var f = document.getElementById("file").files[0];
      var fsize = f.size||f.fileSize;
     form_data.append("file", document.getElementById('file').files[0]);
      $('#loader-icon').show();
      $('#targetLayer').hide();
     $.ajax({
      url:"upload_query.php",
      method:"POST",
      data: form_data,

      contentType: false,
      cache: false,
      processData: false,
      beforeSend:function(){
        $('.progress-bar').width('50%');
      },   
        uploadProgress: function(event, position, total, percentageComplete)
        {
          $('.progress-bar').animate({
            width: percentageComplete + '%'
          }, {
            duration: 1000
          });
        },
      success:function(data)
      {
       $('#uploaded_image').html(data);
          $('#loader-icon').hide();
          $('#targetLayer').show();
loadinguploaded();
      }
     });

   });
});

  </script>

 </body>
</html>
