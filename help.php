<?php
//help.php
//Vikram Singh
//11/14/2017
//Basic html page with user instructions on how to accomplish certain tasks and use the website in general.



session_start();
?>
<html>
<head>
    <title>Bay Area Kirtan Programs</title>
<meta name="viewport" content="user-scalable=yes, width=device-width" />

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
 <link href="navbar.css" rel="stylesheet">

</head>
<body>

<?php include('header.html'); ?>  

<div class="container">
  <h3>About </h3>
  <p>
  Sikh Events is a platform which shows Sikhi-related events taking place all around the world in one convenient location. This will include kirtan programs, fundraisers, camps, and others types of events. Users can submit events through the website enabling anyone to see what is going on around them.
  </p>

  <h3> Help and How to Use</h3>

  <p>Basic instructions are given below. If you have any questions, concerns, or suggestions, 
    send an email to vsk@sikh.events.</p>

  <div class="panel panel-default">
    <div class="panel-heading panel-title">
      Create New User 
    </div>
    <div class="panel-body">
      One must have a user account in order to submit programs. 
      Click <a href="http://sikh.events/newuser.php">here</a> to register and create a new username and password. 
      Provide a valid email address so that you can reset your account info and be reached if there are any issues. 
      Make sure to write down or store your password in a safe location as you will need it to log in. 
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading panel-title"> 
      Log In 
    </div>
    <div class="panel-body">
      If you are not already logged in, you will be prompted to <a href="http://sikh.events/login.php">log in</a> before you can submit new programs or view/edit programs you have already submitted. 
      Once you are logged in you will be able to submit programs. You will also see the "Account" button in the menu
      where you can view programs you have alrady submitted. You can also Edit and Delete programs you have already submitted from here. 
      You can also log out or reset your password from this page. In addition, you can view and manage locations you have created. 
      Other features will be added here in the future, as they  become available. 
    </div>   
  </div>

  <div class="panel panel-default">
    <div class="panel-heading panel-title">
      Submit Program  
    </div>
    <div class="panel-body">
      In order to submit a new program, click "Submit New Program" in the menu. Fill in the details of your event. 
      Required fields are marked with a *. You must provide a Title, location name, zip, and start time/date. 
      Description and other fields are optional. 

      If your gurdwara or venue has already been added to the system, you will see it come up when typing in the location field. 
      Just select the right location and it will automatically fill out the address and phone number for you. 
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading panel-title">
      Repeating Events  
    </div>
    <div class="panel-body">
      Currently Sikh.Events supports basic repeating events.
      In order to create an event which will be the same for multiple days in a row, 
      follow these instructions: <br>
      <ol>
        <li>Go to "Submit New Program"</li>
        <li>Fill in the details</li>
        <li>Enter Start and End Date/Times for a single occurence</li>
        <li>Check the Repeat box</li>
        <li>Select the repeat mode. Available options are:
        	<ol>
        		<li>Daily - repeat this event each day from the Start Date</li>
        		<li>Weekly - repeat this event once a week on the same day of week as the Start Day</li>
        		<li>Monthly - repeat every [first,second,third, or fourth] [selected day of week]</li>
        	</ol>
        <li>Choose "Until" date - when the repeating events should stop (the last date of the event)</li>
      </ol>

      When you edit a repeating event, all occurences of the event will be affected. 
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading panel-title">
      Edit Program  
    </div>
    <div class="panel-body">
      In order to edit an existing program which has already been submitted, go to the "Account" page.
      Here you can view a list of programs you have submitted. Click "Edit" in the row you wish to select. 
      Now you can modify any of the fields such as the description or date and click submit again, and your changes will be saved. 
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading panel-title">
      Clone Program  
    </div>
    <div class="panel-body">
      If you need to submit a new program which is similar to one you have already submitted, you can follow the steps above to edit that program. 
      Instead of clicking the submit button, you can change the details like time or date and then click the "clone" button. 
      This will submit a new program with this information while leaving the original event unchanged. 
    </div>
  </div>

</div>

</body>
</html>