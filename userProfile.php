<?php
    include('config.php');
    include('server.php');

    if (!isset($_SESSION['username'])) {
        header('location: index.php');
    }

    if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION['username']);
        header("location: index.php");
    }
    
    // Updating Customer's record
    if (isset($_POST['updateUser'])) {
        $id=intval($_SESSION['userid']);
        $name= mysqli_real_escape_string($db, $_POST['name']);
        $phone= mysqli_real_escape_string($db, $_POST['phone']);
        if (empty($name)) { array_push($errors, "Name is required"); 
            echo "<script> alert('Name is required. ');
            window.location.replace('userProfile.php') ; </script>";
        }
        
        if (empty($phone)) { array_push($errors, "Phone Number is required");
            echo "<script> alert('Phone Number is required. ');
            window.location.replace('userProfile.php') ; </script>";
        }
        else{
            $sql="update  personal_driver.user a set name=:name,phone=:phone where a.id=$id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':name',$name,PDO::PARAM_STR);
            $query->bindParam(':phone',$phone,PDO::PARAM_STR);
            $query->execute();

            if (isset($_SESSION['isdriver'])){
                $licenseNumber= mysqli_real_escape_string($db, $_POST['licenseNumber']);
                $expiryYear= mysqli_real_escape_string($db, $_POST['expiryYear']);
                
                if (empty($licenseNumber)) { array_push($errors, "Driver lincese Number is required"); 
                    echo "<script> alert('Driver lincese Number is required. ');
                    window.location.replace('userProfile.php') ; </script>";
                }
                
                if (empty($expiryYear)) { array_push($errors, "Expiry year is required");
                    echo "<script> alert('Expiry year is required. ');
                    window.location.replace('userProfile.php') ; </script>";
                }
                else{
                
                $sql="update  personal_driver.driver a set drivingLicenseNum=:licenseNumber,licenseExpiryYear=:expiryYear where a.id=$id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':licenseNumber',$licenseNumber,PDO::PARAM_STR);
                $query->bindParam(':expiryYear',$expiryYear,PDO::PARAM_STR);
                $query->execute();
            }

            }

            $query = "SELECT * FROM personal_driver.user WHERE user.id=$id";
            $results = mysqli_query($db, $query);
            if (mysqli_num_rows($results) == 1) {
                $row=mysqli_fetch_assoc($results);

                $_SESSION['username'] = $row['name'];;
                $_SESSION['phone'] = $row['phone'];

                $ifdriver = "SELECT * FROM personal_driver.driver WHERE id=$id";
                $isdriver = mysqli_query($db, $ifdriver);

                if (mysqli_num_rows($isdriver) == 1){
                $driverRow=mysqli_fetch_assoc($isdriver);

                $_SESSION['licenseNum'] = $driverRow['drivingLicenseNum'];
                $_SESSION['expiryYear'] = $driverRow['licenseExpiryYear'];
                $_SESSION['cost'] = $driverRow['cost'];

            }
        }

        echo "<script>alert('Successfully updated User');
        
        </script>";
    }  
}
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Driver</title>
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

<body>
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
                                        <li><a href="#"> <i class="fa fa-envelope"></i> info@mychauffeur.com</a></li>
                                        <li><a href="#"> <i class="fa fa-phone"></i> (+250) 78-096-780</a></li>
                                    </ul>
                                </div>

                                <div class="book_btn d-none d-lg-block">
                                    <a class="boxed-btn3-line" href="available_driver.php?logout='1'">Log Out <?php $arr = explode(' ',trim($_SESSION['username'])); echo $arr[0]; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sticky-header" class="main-header-area">
                <div class="container">
                    <div class="header_bottom_border">
                        <div class="row align-items-center">
                            <div class="col-12 d-block d-lg-none">
                                <div class="logo">
                                    <a href="index.php">
                                        <img src="img/logo.png" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9">
                                <div class="main-menu  d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a  href="index.php">home</a></li>
                                            <li><a href="#">Driver <i class="ti-angle-down"></i></a>
                                                <ul class="submenu">
                                                    <li><a data-toggle="modal" data-target="#signin" href="#">Profile</a></li>
                                                    <li><a data-toggle="modal" data-target="#driverForm" href="#">Become A Driver</a></li>
                                                    <li><a data-toggle="modal" data-target="#signin" href="#">Log In</a></li>
                                                </ul>
                                            </li>
                                            <li><a href="#">Customer <i class="ti-angle-down"></i></a>
                                                <ul class="submenu">
                                                    <li><a data-toggle="modal" data-target="#clientForm" href="#">Be A Customer</a></li>
                                                    <li><a data-toggle="modal" data-target="#signin" href="#">Log In</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                                <div class="Appointment justify-content-end">
                                    <div class="search_btn">
                                        <a data-toggle="modal" data-target="#clientForm" href="#"> 
                                            <!-- <i class="ti-search"></i> -->
                                            Be A Client
                                        </a>
                                    </div>
                                    &nbsp; &nbsp;
                                        <div class="search_btn">
                                            <a data-toggle="modal" data-target="#driverForm" href="#">
                                                Be A Driver 
                                                <!-- <i class="ti-search"></i> -->
                                            </a>
                                        </div>

                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mobile_menu d-block d-lg-none"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>
    <!-- header-end -->

    <div class="chose_area ">
        <div class="container">
            <div class="features_main_wrap">
                <div class="row  align-items-center">
                    <div class="col-xl-5 col-lg-5 col-md-6">
                        
                        <div class="about_image">
                            <img src="img/users/<?php $photoName = "profileIcon.jpg"; echo(!isset($_SESSION['userphoto']) ?  $photoName : $_SESSION['userphoto'] );?>" alt="">

                            <!-- <img src="img/profileIcon.jpg" alt="profile picture"> -->
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6">
                        <div class="features_info">
                            <h3><?php echo $_SESSION['username']; ?></h3>
                            <p> Ut enim ad minim veniam, quis laboris nisi ut
                             aliquip ex ea commodo consequat.
                            </p>
                            <ul>
                                <li> User Name : <?php echo $_SESSION['username'];?> </li>
                                <li> Phone Number : <?php echo $_SESSION['phone'];?> </li>
                                <?php if(isset($_SESSION['licenseNum'])){ ?>
                                    <li> License Number: <?php echo $_SESSION['licenseNum'];?></li>
                                    <li> License Expiry Date: <?php echo $_SESSION['expiryYear'];?></li>
                                <?php } ?>
                            </ul>

                            <div class="about_btn">
                                <a class="boxed-btn3-line" data-toggle="modal" data-target="#updateForm" href="#">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




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
                                <li><a href="#">Profile</a></li>
                                <li><a data-toggle="modal" data-target="#driverForm" href="#" href="#">Become A Driver</a></li>
                                <li><a data-toggle="modal" data-target="#signin" href="#">Log In</a></li>
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
                                <li><a data-toggle="modal" data-target="#signin" href="#">Log In</a></li>
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
<!-- Button trigger modal -->


  <!-- Update client Modal -->
  <form method="post" action="userProfile.php">
    <?php include('errors.php'); ?>

    <div class="modal fade custom_search_pop" id="updateForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="serch_form">
            <table>
                <tbody>
                    <tr><td><label for="name">User Name:</label></td><td><input type="text" name="name" placeholder="name" value="<?php echo $_SESSION['username']; ?>" ></td></tr>
                    <tr><td><label>Phone Number:</label></td><td><input type="number" name="phone" placeholder="phone" value="<?php echo $_SESSION['phone']; ?>" ></td></tr>
                    <?php if(isset($_SESSION['licenseNum'])){ ?>
                    <tr><td><label>License Number: </label></td><td><input type="text" name="licenseNumber" value="<?php echo $_SESSION['licenseNum']; ?>"></td></tr>
                    <tr><td><label>License Expiry Year: </label></td><td><input id="expiryYear" type="date" name="expiryYear"></td></tr>
                    <?php } ?>
                </tbody>
            </table>
                <button type="submit" name="updateUser">Update</button>
            </div>
        </div>
        </div>
    </div>
    </form>
  
  <!-- Be a client Modal -->
  <form method="post" action="userProfile.php">
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
    <form method="POST" action="userProfile.php" enctype="multipart/form-data">
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
    </form>
    <!-- End of driver Modal -->

    <!-- Sign In  Modal -->
    <form method="post" action="userProfile.php">
    <?php include('errors.php'); 
        
        if (!isset($_SESSION['username'])) { ?>
        <div class="modal fade custom_search_pop" id="signin" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="form-group">
                    <h2 class="moveme"> Your Account</h2>

                    <ul class="formlist">
                        <li><label class="foolabel">User Name:</label><input type="text" name="name" placeholder="name" value="<?php echo $name; ?>"></li>
                        <li><label class="foolabel">Password:</label><input type="password" name="password" placeholder="password"></li>
                    </ul>

                    <button  type="submit" name="login_user">Log In</button>
                </div>
            </div>
            </div>
        </div>
        
        <?php }?>
    </form>

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
