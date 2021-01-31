
<!-- The server.php will  receive information submitted from the form and store it in the databse. -->




<?php
session_start();

// initializing variables
$name = "";
$email = "";
$phone = "";
$dob = "";
$gender = "";
$password = "";
$ratings = "";
$license= "";
$cost = "";
$errors = array(); 
$image = "";

$rows="";
$uid="";



$drivingLicenseNum = "";
$licenseExpiryYear = "";
$allowedYears = 18;
$alloweddrivingYears = 1;

// driver booking variables
$bookdate="";		
$description="";	
$duration="";
$total_cost="";


// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'personal_driver');

// This is registering the driver
if (isset($_POST['driver'])) {
  // receive all input values from the form
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);
  $dob = mysqli_real_escape_string($db, $_POST['dob']);
  $gender = mysqli_real_escape_string($db, $_POST['gender']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $cost = mysqli_real_escape_string($db, $_POST['cost']);
  $drivingLicenseNum = mysqli_real_escape_string($db, $_POST['licenseNumber']);
  $licenseExpiryYear = mysqli_real_escape_string($db, $_POST['licenseExpiryYear']);


  //Store the filename as it was on the client computer.
  $license = $_FILES['image']['name'];

  //Store the tempname as it is given by the host when uploaded.
  $imagetemp = $_FILES['image']['tmp_name'];

  //The path you wish to upload the image to
  $imagePath = "img/users/";

  if(is_uploaded_file($imagetemp)) {
    if(move_uploaded_file($imagetemp, $imagePath . $license)) {
      echo "<script>alert('Sussecfully uploaded  your image.')</script>";
      }
      else {
        echo "<script>alert('Failed to move your image.')</script>";
      }
    }
    else {
      echo "<script>alert('Failed to upload your image.')</script>";
    }   

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  // if (empty($fname)) { array_push($errors, "First Name is required"); }
  if (empty($license)) { array_push($errors, "license photo is required");
    echo "<script> alert(' license photo is required. ');
    window.location.replace('index.php') ; </script>";
  }
  if (empty($password)) { array_push($errors, "Password is required"); 
    echo "<script> alert(' Password is required. ');
    window.location.replace('index.php') ; </script>";
  }
  if (empty($name)) { array_push($errors, "name is required");
    echo "<script> alert(' name is required. ');
    window.location.replace('index.php') ; </script>";
  }
  if (empty($cost)) { array_push($errors, "cost per hour is required"); 
    echo "<script> alert(' cost per hour is required. ');
    window.location.replace('index.php') ; </script>";
  }

  if (empty($dob)) { array_push($errors, "Date of Birth is required");
    echo "<script> alert(' Date of Birth is required. ');
    window.location.replace('index.php') ; </script>";
  }

  $date_now = date("Y-m-d"); // this format is string comparable
  $dob = date('Y-m-d', strtotime(str_replace('-', '/', $dob)));

  $years_difference = round(differenceInHours($dob,$date_now));	

  if ($years_difference < $allowedYears) {
    echo "<script>
    alert('User must be 18 and above');
    window.location.replace('index.php') ;
    </script>";
    array_push($errors, "User must be 18 and above");
  }

  if (empty($drivingLicenseNum)) { array_push($errors, "Driving License Number is required");
    echo "<script> alert(' Driving License Number is required. ');
    window.location.replace('index.php') ; </script>";
  }

  if (empty($licenseExpiryYear)) { array_push($errors, "License Expiry Date is required");
    echo "<script> alert(' License Expiry Date is required. ');
    window.location.replace('index.php') ; </script>";
  }

  $years_difference = round(differenceInHours($date_now,$licenseExpiryYear));	

  if ($licenseExpiryYear < $date_now || $alloweddrivingYears > $years_difference ) {
    echo "<script>
    alert('Your driving linsence must be greater than a year for you to be an eligible driver.');
    window.location.replace('index.php') ;
    </script>";
    array_push($errors, "linsence must be greater than a year for you to be an eligible driver");
  }

  else{  // first check the database to make sure 
    // a user does not already exist with the same username and/or email
    $user_check_query = "SELECT * FROM personal_driver.user WHERE user.name='$name' OR email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) { 
      if ($user['name'] === $name) {
        array_push($errors, "User already exists");
        echo "<script> alert(' User already exists. ');
        window.location.replace('index.php') ; </script>";
      }

      if ($user['email'] === $email) {
        array_push($errors, "User already exists");
        echo "<script> alert(' User already exists. ');
        window.location.replace('index.php') ; </script>";
      }
    }

    // Finally, register driver if there are no errors in the form
    if (count($errors) == 0) {
      $password = md5($password);//encrypt the password before saving in the database

      // filling the user table
      $query = "INSERT INTO personal_driver.user (user.name, email, phone, dob, gender, user.password, user.isdelete) 
            VALUES('$name','$email','$phone', '$dob', '$gender', '$password', 'no')";

      mysqli_query($db, $query);

      $free="available";
      // Pulling the client's id from the client table
      $query_2 = "INSERT INTO personal_driver.driver (id, license, cost, driver.availability, driver.drivingLicenseNum, licenseExpiryYear) 
      VALUES((SELECT user.id FROM personal_driver.user WHERE email='$email'), '$license','$cost','$free','$drivingLicenseNum','$licenseExpiryYear')";

        mysqli_query($db, $query_2); 

      echo "<script>
            alert('Driver successfully registered.');
            window.location.href='index.php';
            </script>";
    }
  }
}

// register client
if (isset($_POST['customer'])) {
  // receive all input values from client form
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);
  $dob = mysqli_real_escape_string($db, $_POST['dob']);
  $gender = mysqli_real_escape_string($db, $_POST['gender']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($name)) { array_push($errors, "name is required"); 
    echo "<script> alert(' name is required. ');
    window.location.replace('index.php') ; </script>";
  }
  if (empty($password)) { array_push($errors, "Password is required");
    echo "<script> alert(' Password is required. ');
    window.location.replace('index.php') ; </script>";
  }

  if (empty($dob)) { array_push($errors, "Date of Birth is required");
    echo "<script> alert(' Date of Birth is required. ');
    window.location.replace('index.php') ; </script>";
  }

  $date_now = date("Y-m-d"); // this format is string comparable
  $dob = date('Y-m-d', strtotime(str_replace('-', '/', $dob)));

  $years_difference = round(differenceInHours($dob,$date_now));	

  if ($years_difference < $allowedYears) {
    echo "<script>
    alert('User must be 18 and above');
    window.location.replace('index.php') ;
    </script>";
    array_push($errors, "User must be 18 and above");
  }

  else{  // first check the database to make sure 
    // a user does not already exist with the same username and/or email
    $user_check_query = "SELECT * FROM personal_driver.user WHERE user.name='$name' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) { 
      if ($user['name'] === $name) {
        array_push($errors, "User already exists");
        echo "<script> alert(' User Entered already exist. ');
        window.location.replace('index.php') ; </script>";
      }
    }

    // Finally, register user if there are no errors in the form
    if (count($errors) == 0) {
      $password = md5($password);//encrypt the password before saving in the database

      $isdeleted = "no";

      // filling the user table
      $query = "INSERT INTO personal_driver.user (user.name, email, phone, dob, gender, user.password, user.isdelete) 
            VALUES('$name','$email','$phone', '$dob', '$gender', '$password', '$isdeleted')";

      mysqli_query($db, $query);

      echo "<script>
            alert('Client successfully registered.');
            window.location.href='index.php';
            </script>";
    }
  }
}

  // ...

// Booking a driver
if (isset($_POST['bookdriver'])) {
    $id=intval($_GET['id']);
    $usrid = $_SESSION['userid'];

    // receive all input values from the form
    $bookdate = mysqli_real_escape_string($db, $_POST['date']);
    $duration = mysqli_real_escape_string($db, $_POST['hours']);
    $description = mysqli_real_escape_string($db, $_POST['message']);
    
    if (empty($bookdate)) { 
      echo "<script> alert('Booking not successfully specified');
      var data = $id; 
      window.location.replace('book_driver.php?id=' + data) ;
      </script>";
      array_push($errors, "bookdate is required"); }
    if (empty($duration)) { 
      echo "<script> alert('Booking not successfully specified');
      var data = $id; 
      window.location.replace('book_driver.php?id=' + data) ;
      </script>";
      array_push($errors, "duration is required"); }
     
    else{  // first check the database to make sure 
      $date_now = date("Y-m-d h:i:s"); // this format is string comparable
      $bookdate = date('Y-m-d h:i:s', strtotime(str_replace('-', '/', $bookdate)));
      $duration = date('Y-m-d h:i:s', strtotime(str_replace('-', '/', $duration)));
  
      if ($date_now > $bookdate || $bookdate > $duration) {
          echo "<script>
          alert('Please provide correct booking date and time details the current datetime is $date_now');
          var data = $id; 
          window.location.replace('book_driver.php?id=' + data) ;
          </script>";
          array_push($errors, "provide correct booking date and time");
      }else{

        //  check if there is no collapsing booking time for driver.
        $sql = "SELECT * from personal_driver.transaction where (driverid = '$id') AND ((bookdate <= '{$bookdate}' AND duration > '{$bookdate}') OR (bookdate <= '{$duration}' AND duration > '{$duration}')) ";
        $query = mysqli_query($db, $sql);
        
        if(mysqli_num_rows($query) > 0){
          $result=mysqli_fetch_assoc($query);
          $booking_start = ($result['bookdate']);
          $booking_end = ($result['duration']);
          echo "<script>alert('Driver was booked first from $booking_start to $booking_end, which is in your booking interval. Please try a differnt time');
          var data = $id; 
          window.location.replace('book_driver.php?id=' + data) ;
          </script>";
        }
        
        //  check if driver is not booked in the middle of your booking time.
        $sql = "SELECT * from personal_driver.transaction where (driverid = '$id') AND (bookdate >='{$bookdate}'AND duration <='{$duration}') ";
        $query = mysqli_query($db, $sql);
        if (mysqli_num_rows($query) > 0) {
          $result=mysqli_fetch_assoc($query);
          $booking_start = ($result['bookdate']);
          $booking_end = ($result['duration']);
          echo "<script>alert('Driver is booked in the middle of your booking interval fromt the $booking_start to $booking_end');
          var data = $id; 
          window.location.replace('book_driver.php?id=' + data) ;
          </script>";
        }
        else{  
          $sql = "SELECT * FROM personal_driver.driver WHERE id = '$id' ";
          $results = mysqli_query($db, $sql);
          $row=mysqli_fetch_assoc($results);
          $hourcost = $row['cost'];

          $hours_difference = round(differenceInHours($bookdate,$duration));	
          // $message = "The Difference is " . $hours_difference . " hours";
          
          // echo "<script> alert('hour cost $hourcost and try again on date')</script>";
          $total_cost = $hourcost*$hours_difference;
          // echo "<script> alert('total cost is $total_cost and try again on date')</script>";
          
          // Finally, register driver if there are no errors in the form
          if (count($errors) == 0) { 

            $_SESSION['bookstart'] = $bookdate;
            $_SESSION['bookend'] = $duration;
            $_SESSION['description'] = $description;
            $_SESSION['driverid'] = $id;
            $_SESSION['totalcost'] = $total_cost;

            header('location: https://flutterwave.com/pay/eoqpftvyjnnz');
            

          //   $query = "SELECT * FROM personal_driver.user WHERE id = '$id'";
          //   $result = mysqli_query($db, $query);
          //   if (mysqli_num_rows($results) == 1) {
          //     $rowresult=mysqli_fetch_assoc($result);

          //     $to = $rowresult['email']; // replace this mail with yours
          //     $who = $_SESSION['username'];
          //     $subject = "Client Booked Your Services";
          //     $msg = "booked your services from";
          //     $email= $_SESSION['email'];
          //     $phone= $_SESSION['phone'];
          // 		$headers = 'MIME-Version: 1.0' . "\r\n";
          // 		$headers .= "From: " . $_SESSION['email'] . "\r\n"; // Sender's E-mail
          //     $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
          //     $parameters = "";
              
          //     echo "<script>alert('code running trying to send email to $to');</script>";

          //     $message = "$who $msg $bookdate to $duration \n\n
          //     Kindly contact them on $phone or via their Email: $email";

          // 		if (mail($to, $subject, $message, $headers))
          // 		{
          //       echo "<script>alert('Driver is notified');
          //       </script>";
            
          //     // filling the user table
          //     $query="INSERT INTO personal_driver.transaction (bookdate, userid, driverid, descrption, duration, total_cost)VALUES ('$bookdate', '$usrid', '$id', '$description', '$duration', '$total_cost') ";
      
          //     // mysqli_query($db, $query);
          //     if(mysqli_query($db, $query)){
          //       echo "Records added successfully.";
          //   } else{
          //       echo "ERROR: Could not able to execute $query. " . mysqli_error($db);
          //   }

          //     if ($date_now == $bookdate){
          //       // update driver availabilty
          //       $sql = "UPDATE driver SET driver.availability='booked' WHERE id='$id'";

          //       if (mysqli_query($db, $sql)) {
          //           echo "Record updated successfully";
          //       } else {
          //           echo "Error updating record: " . mysqli_error($db);
          //       }
          //     }
            
          //   // header('location: available_driver.php');
          //   echo "<script>
          //   // alert('Transaction successfully recorded.');
          //   window.location.href='available_driver.php';
          //   </script>";
          // }else{
          //   echo "<script>alert('Email doesn't exist');
          //   window.location.href='available_driver.php';
          //       </script>";
          // }
          //   }
          } 
          
        }
      }
    }
  }

  function differenceInHours($startdate,$enddate){
    $starttimestamp = strtotime($startdate);
    $endtimestamp = strtotime($enddate);
    $difference = abs($endtimestamp - $starttimestamp)/3600;
    return $difference;
  }
// ... 

// user logging in 
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['name']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

//   check if the login text box is not empty
  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $query = "SELECT * FROM personal_driver.admin WHERE adminName='$username' AND adminPassword='$password'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
      $row=mysqli_fetch_assoc($results);

      $_SESSION['username'] = $username;
      
      $uid=$row['id'];
      $_SESSION['userid'] = $uid;
      
      header('location: admin/index.php');

    } else{
      $password = md5($password);
      $query = "SELECT * FROM personal_driver.user WHERE user.name='$username' AND user.password='$password'";
      $results = mysqli_query($db, $query);
      if (mysqli_num_rows($results) == 1) {
        $row=mysqli_fetch_assoc($results);

        $_SESSION['username'] = $username;
        $_SESSION['email'] = $row['email'];;
        $_SESSION['phone'] = $row['phone'];;
        
        $uid=$row['id'];
        $_SESSION['userid'] = $uid;

        $ifdriver = "SELECT * FROM personal_driver.driver WHERE id='$uid'";
        $isdriver = mysqli_query($db, $ifdriver);

        echo "<script>alert('hi'); 
          </script>";

        if (mysqli_num_rows($isdriver) == 1){
          $driverRow=mysqli_fetch_assoc($isdriver);

          // echo "<script>
          //   alert("in the shit. $driverRow['drivingLicenseNum'] myid $uid");
          //   </script>";

          echo "<script>alert('Driver is $driverRow to $uid'); 
          </script>";

          $_SESSION['licenseNum'] = $driverRow['drivingLicenseNum'];
          $_SESSION['expiryYear'] = $driverRow['licenseExpiryYear'];
          $_SESSION['userphoto'] = $driverRow['license'];
          $_SESSION['cost'] = $driverRow['cost'];
          $_SESSION['isdriver'] = $driverRow['id'];

        }
        // $_SESSION['success'] = "You are now logged in";

        header('location: available_driver.php');
      }else {
        array_push($errors, "Wrong username/password combination");
        echo "<script>
            alert('Wrong username/password combination.');
            window.location.href='index.php';
            </script>";
      }
    }
  }
}
?>