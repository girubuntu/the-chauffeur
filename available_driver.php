<?php 
include('config.php');
include('server.php');
//   session_start();

if (isset($_SESSION['bookstart'])) {
    $id=$_SESSION['driverid'];
    $bookdate = $_SESSION['bookstart'];
    $usrid = $_SESSION['userid'];
    $description = $_SESSION['description'];
    $duration =$_SESSION['bookend'];
    $total_cost =$_SESSION['totalcost'];
    $date_now = date("Y-m-d h:i:s"); // this format is string comparable

    $sql = "SELECT * FROM personal_driver.driver WHERE id = '$id' ";
    $results = mysqli_query($db, $sql);

    $query = "SELECT * FROM personal_driver.user WHERE id = '$id'";
    $result = mysqli_query($db, $query);
            if (mysqli_num_rows($results) == 1) {
              $rowresult=mysqli_fetch_assoc($result);

              $to = $rowresult['email']; // replace this mail with yours
              $who = $_SESSION['username'];
              $subject = "Client Booked Your Services";
              $msg = "booked your services from";
              $email= $_SESSION['email'];
              $phone= $_SESSION['phone'];
          		$headers = 'MIME-Version: 1.0' . "\r\n";
          		$headers .= "From: " . $_SESSION['email'] . "\r\n"; // Sender's E-mail
              $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
              $parameters = "";
              
              echo "<script>alert('code running trying to send email to $to');</script>";

              $message = "$who $msg $bookdate to $duration \n\n
              Kindly contact them on $phone or via their Email: $email";

          		if (mail($to, $subject, $message, $headers))
          		{
                echo "<script>alert('Driver is notified');
                </script>";
            
              // filling the user table
              $query="INSERT INTO personal_driver.transaction (bookdate, userid, driverid, descrption, duration, total_cost)VALUES ('$bookdate', '$usrid', '$id', '$description', '$duration', '$total_cost') ";
      
              // mysqli_query($db, $query);
              if(mysqli_query($db, $query)){
                echo "Records added successfully.";
            } else{
                echo "ERROR: Could not able to execute $query. " . mysqli_error($db);
            }

              if ($date_now == $bookdate){
                // update driver availabilty
                $sql = "UPDATE driver SET driver.availability='booked' WHERE id='$id'";

                if (mysqli_query($db, $sql)) {
                    echo "Record updated successfully";
                } else {
                    echo "Error updating record: " . mysqli_error($db);
                }
              }
            
            // header('location: available_driver.php');
            echo "<script>
            // alert('Transaction successfully recorded.');
            </script>";
          }else{
            echo "<script>alert('Email doesn't exist');
            window.location.href='available_driver.php';
                </script>";
          }
            }
}



  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: index.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: index.php");
  }

// set to current system time
date_default_timezone_set("Africa/Cairo");
$currentDateTime = date("Y-m-d h:i:s");
// check if no booking date or time arrived yet to change driver status.
// $sql = "SELECT bookdate, duration, driverid from personal_driver.transaction";
$sql = "SELECT driverid from personal_driver.transaction where bookdate <= '{$currentDateTime}' AND duration > '{$currentDateTime}' ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
// $results=$query->mysqli_stmt::fetch((PDO::FETCH_OBJ));
if($query->rowCount() > 0){
    foreach($results as $result){

        // $booking_start = ($result->bookdate);
        // $booking_end = ($result->duration);
        $b_driver = ($result->driverid);
        // echo "<script>alert('$b_driver date now')</script>"; // test

        // $booking_start = date('Y-m-d h:i:s', strtotime(str_replace('-', '/', $booking_start)));
        // $booking_end = date('Y-m-d h:i:s', strtotime(str_replace('-', '/', $booking_end)));

        // if($currentDateTime >= $booking_start && $currentDateTime < $booking_end ){
        $sql = "UPDATE driver SET driver.availability='booked' WHERE driver.id='$b_driver' ";

          if (mysqli_query($db, $sql)) {
            //   echo "Driver Booking Time arrived";
          } else { echo "Error updating record: " . mysqli_error($db);}

        // }else{
        //     $sql = "UPDATE driver SET driver.availability='available' WHERE id='$b_driver' ";
        //   if (mysqli_query($db, $sql)) {
        //     //   echo "Record updated successfully";

        //   } else { echo "Error updating record: " . mysqli_error($db);}
        // }
    }
}                        


$sql = "SELECT driverid from personal_driver.transaction where duration < '{$currentDateTime}' ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0){
    foreach($results as $result){

        $b_driver = ($result->driverid);
        
            $sql = "UPDATE driver SET driver.availability='available' WHERE id='$b_driver' ";
          if (mysqli_query($db, $sql)) {
            //   echo "Record updated successfully";
          } 
          else { echo "Error updating record: " . mysqli_error($db);}
    }
}


//   echo "<script>alert('$currentDateTime testing today')</script>";
$test34 = '2020-03-23 07:01:00';

$hours_difference = round(differenceInHours($currentDateTime,$test34));	
$message = "The Difference is " . $hours_difference . " hours";


// echo "<script>alert('$message testing today')</script>";

//   if ($currentDateTime > '2020-03-23 07:01:00' ) {
//     echo "<script>alert('testing today')</script>";
// }
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <!-- <meta http-equiv="Refresh" content="60"> -->
    <title>Available Drivers</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/gijgo.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/slicknav.css">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css">

    <link rel="stylesheet" href="css/style.css">
    <!-- <link rel="stylesheet" href="css/responsive.css"> -->
</head>

<!-- reload page every 1 min -->
<script>
function pageloadEvery(t) {
setTimeout('location.reload(true)', t);
}
</script>

<body onload="javascript:pageloadEvery(60000);">
    <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

    <!-- header-start -->
    <header>
        <div class="header-area ">
            <div class="header-top_area d-none d-lg-block">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-4 col-lg-4">
                            <div class="logo">
                                <a href="index.php">
                                    <img src="img/logo.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-8 col-md-8">
                            <div class="header_right d-flex align-items-center">
                                <div class="short_contact_list">
                                    <ul>
                                        <li><a href="userProfile.php"> <i class="fa fa-envelope"></i> Profile </a></li>
                                        <li><a href="#"> <i class="fa fa-phone"></i> (+250) 78-096-780</a></li>
                                    </ul>
                                </div>

                                <div class="book_btn d-none d-lg-block">
                                    <form method="post" action="available_driver.php">
                                        <a class="boxed-btn3-line" href="available_driver.php?logout='1'"> Log Out <?php $arr = explode(' ',trim($_SESSION['username'])); echo $arr[0];; ?></a>
                                    </form> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header-end -->

    <?php $uid = $_SESSION['userid'];
    $sql="SELECT distinct bookdate, driverid, duration, total_cost from personal_driver.transaction, user where userid='$uid'";
                $query = $dbh -> prepare($sql);
                $query->execute();
                $results=$query->fetchAll(PDO::FETCH_OBJ);
                $cnt=1;
                if($query->rowCount() > 0){?>
                

                    <div class="booking_details">
                        <div class="container">
                                    <!-- <div class="col-xl-5 col-lg-5 col-md-6"> -->
                                        <!-- <div class="features_info"> -->
                                            <h3>Your Booking Details </h3>
                                            <div class="table table-borderless table-hover table-dark">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Driver Name</th>
                                                            <th scope="col">Booking Start</th>
                                                            <th scope="col">Booking End</th>
                                                            <th scope="col">Cost/Hr</th>
                                                            <th scope="col">Booked Hours</th>
                                                            <th scope="col">Total Cost</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                            foreach($results as $result)
                                                            {              
                                                                $tempid = $result->driverid;
                                                                $sql = "SELECT user.name, cost FROM driver, user WHERE driver.id='$tempid' and user.id='$tempid'";
                                                                $res = mysqli_query($db, $sql);
                                                                $row=mysqli_fetch_assoc($res);
                                                                $drivername = $row['name'];
                                                                $hourcost = $row['cost'];
                                                                ?>                                      
                                                                    <tr>
                                                                        <th scope="row"><?php echo htmlentities($cnt);?></td>
                                                                        <td class="difsize"><?php echo htmlentities($drivername);?></td>
                                                                        <td class="difsize"><?php echo htmlentities($result->bookdate);?></td>
                                                                        <td class="difsize"><?php echo htmlentities($result->duration);?></td>
                                                                        <td class="difsize"><?php echo $hourcost;?></td>
                                                                        <td class="difsize"><?php echo htmlentities($result->total_cost)/$hourcost;?></td>
                                                                        <td class="difsize"><?php echo htmlentities($result->total_cost);?></td>                            
                                                                    </tr>
                                                            <?php $cnt=$cnt+1;} ?>                                      
                                                        </tbody>
                                                </table>
                                            <!-- </div> -->
                                        <!-- </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                        
                                

                    <?php $sql = "SELECT user.name, user.phone, driver.license, driver.cost, driver.availability, driver.id 
                    from personal_driver.driver INNER JOIN personal_driver.user On driver.id = user.id WHERE driver.availability ='available' AND user.isdelete='no' ORDER BY id ASC";
                                    $query = $dbh -> prepare($sql);
                                    $query->execute();
                                    $results=$query->fetchAll(PDO::FETCH_OBJ);
                                    // $results=$query->mysqli_stmt::fetch((PDO::FETCH_OBJ));
                                    $cnt=1;
                                    if($query->rowCount() > 0)
                                    {
                                    foreach($results as $result)
                                    {               ?>
                                    <!-- chose_area  -->
                                        <div class="chose_area ">
                                            <div class="container">
                                                <div class="features_main_wrap">
                                                    <div class="row  align-items-center">    
                                                        <div class="col-xl-5 col-lg-5 col-md-6">
                                                            <div class="about_image">
                                                                <img src="img/users/<?php echo htmlentities($result->license);?>" alt="">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6 col-md-6">
                                                            <div class="features_info">
                                                                <h3><?php echo htmlentities($result->name);?> </h3>
                                                                <ul>
                                                                    <li>Contact me at <b> <?php echo htmlentities($result->phone);?> </b></li>
                                                                    <li>charge <b><?php echo htmlentities($result->cost);?>$</b> per hour</li>
                                                                    <li>Currently <b><?php echo htmlentities($result->availability);?></b></li>
                                                                </ul>
                                                                <div class="about_btn">
                                                                    <a href="book_driver.php?id=<?php echo htmlentities($result->id); ?>" class="boxed-btn3-line">
                                                                        Book Driver
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
    <!--/ chose_area  -->

                                    <?php }}else{  ?>
                                        <div class="chose_area ">
                                            <div class="container">
                                                <div class="features_main_wrap">
                                                    <div class="row  align-items-center">
                                                        <h3> No Driver is in the Personal Driver Sytem. </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

    <!-- Drivers Fetch from Database -->

    <!-- contact_location  -->
    <div class="contact_location">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <div class="location_left">
                        <div class="logo">
                            <a href="index.html">
                                <img src="logo.png" alt="">
                            </a>
                        </div>
                        <ul>
                            <li><a href="#"> <i class="fa fa-facebook"></i> </a></li>
                            <li><a href="#"> <i class="fa fa-google-plus"></i> </a></li>
                            <li><a href="#"> <i class="fa fa-twitter"></i> </a></li>
                            <li><a href="#"> <i class="fa fa-youtube"></i> </a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="single_location">
                        <h3> <img src="img/icon/address.svg" alt=""> Location</h3>
                        <p>3rd Floor, East Wing, Kigali Heights, 
                            Kigali-2563</p>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="single_location">
                        <h3> <img src="img/icon/support.svg" alt=""> Drop Us A Line</h3>
                        <p>+25 078 096 780 <br>
                            support@mychauffeur.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--/ End of contact_location  -->


    <!-- footer start -->
    <footer class="footer">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                <div class="col-xl-3 col-md-6 col-lg-3">
                        <div class="footer_widget">
                            <h3 class="footer_title">
                                Driver
                            </h3>
                            <ul>
                            <li><a href="userProfile.php">Profile</a></li>
                                <li><a data-toggle="modal" data-target="#driverForm" href="#" href="#">Become A Driver</a></li>
                                <li><a href="#">Log In</a></li>
                            </ul>

                        </div>
                    </div>
                    <div class="col-xl-2 col-md-6 col-lg-2">
                        <div class="footer_widget">
                            <h3 class="footer_title">
                                Company
                            </h3>
                            <ul>    
                                <li><a href="workwithus.php">Our Story</a></li>
                                <li><a href="workwithus.php">Careers</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-lg-3">
                        <div class="footer_widget">
                            <h3 class="footer_title">
                                Customers
                            </h3>
                            <ul>
                                <li><a data-toggle="modal" data-target="#clientForm" href="#">Become A Customer</a></li>
                                <li><a href="#">Log In</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-lg-4">
                        <div class="footer_widget">
                            <h3 class="footer_title">
                                Subscribe
                            </h3>
                            <form action="#" class="newsletter_form">
                                <input type="text" placeholder="Enter your mail">
                                <button type="submit">Subscribe</button>
                            </form>
                            <p class="newsletter_text">Subscribe to our monthly newsletter to get the latest updates, deals and discounts first.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copy-right_text">
            <div class="container">
                <div class="footer_border"></div>
                <div class="row">
                    <div class="col-xl-12">
                        <p class="copy_right text-center">
                            <!-- coopyright for the mychauffeur website-->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | MyChauffeur work is a private work <i class="fa fa-heart-o" aria-hidden="true"></i> by Jordan Sagisengo
<!-- coopyright for the mychauffeur website -->
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--/ footer end  -->

 <!-- Be a client Modal -->
 <form method="post" action="index.php">
    <?php include('errors.php'); ?>

    <div class="modal fade custom_search_pop" id="clientForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="serch_form">
            <table>
                <tbody>
                    <tr><td><label for="name">Name:</label></td><td><input type="text" name="name" placeholder="name" value="<?php echo $name; ?>" ></td></tr>
                    <tr><td><label for="email">Email:</label></td><td><input type="text" name="email" placeholder="email" value="<?php echo $email; ?>" ></td></tr>
                    <tr><td><label>Birth Date: </label></td><td><input id="myTextField" type="date" name="dob" value="<?php echo $dob; ?>"></td></tr>
                    <tr><td><label>Gender: </label></td><td><select name="gender"> <option>Female</option> <option>Male</option> </select></td></tr>
                    <tr><td><label>Password: </label></td><td><input type="password" name="password" placeholder="password"></td></tr>
                </tbody>
            </table>
                <button type="submit" name="customer">Register</button>
            </div>
        </div>
        </div>
    </div>
    </form>

    <!-- End of be a client Modal -->

    <!-- Be a driver Modal -->
    <form method="POST" action="index.php" enctype="multipart/form-data">
    <?php include('errors.php'); ?>

        <div class="modal fade custom_search_pop" id="driverForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="serch_form">
                    <div class="driver">
                    <table>
                        <tbody>
                            <tr><td><label for="name">Name:</label></td><td><input type="text" name="name" placeholder="name" value="<?php echo $name; ?>" ></td></tr>
                            <tr><td><label for="email">Email:</label></td><td><input type="text" name="email" placeholder="email" value="<?php echo $email; ?>" ></td></tr>
                            <tr><td><label>Tel: </label></td><td><input type="text" name="phone" placeholder="phone (i.e.: 0781234567)" value="<?php echo $phone; ?>" ></td></tr>
                            <tr><td><label>Birth Date: </label></td><td><input id="myTextField" type="date" name="dob" value="<?php echo $dob; ?>"></td></tr>
                            <tr><td><label>Gender: </label></td><td><select name="gender"> <option>Female</option> <option>Male</option> </select></td></tr>
                            <tr><td><label>Password: </label></td><td><input type="password" name="password" placeholder="password"></td></tr>
                            <tr><td><label>License Number: </label></td><td><input type="text" name="licenseNumber" placeholder="126156"></td></tr>
                            <tr><td><label>License Expiry Date: </label></td><td><input id="myTextField" type="date" name="licenseExpiryYear"></td></tr>
                            <tr><td><label>Cost($): </label></td><td><input type="text" name="cost" placeholder="Cost per hour (i.e.: 100)" ></td></tr>
                            <tr><td><label for="driver_lics">Upload Photo</label></td><td><input id="driver_lics" name="image" style="display:none;" type="file"></td></tr>
                        </tbody>
                    </table>
                        <button type="submit" name="driver">Register</button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </form> -->
    <!-- End of driver Modal -->
    <!-- JS here -->
    <script src="js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/ajax-form.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/imagesloaded.pkgd.min.js"></script>
    <script src="js/scrollIt.js"></script>
    <script src="js/jquery.scrollUp.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/nice-select.min.js"></script>
    <script src="js/jquery.slicknav.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/plugins.js"></script>
    <!-- <script src="js/gijgo.min.js"></script> -->
    <script src="js/slick.min.js"></script>



    <!--contact js-->
    <script src="js/contact.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="js/jquery.form.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/mail-script.js"></script>


    <script src="js/main.js"></script>




</body>

</html>