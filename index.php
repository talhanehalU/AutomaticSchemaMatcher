<?php
session_start();
?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>

    <!-- Meta-Information -->
    <title>Automatic Schema Matcher</title>
    <meta charset="utf-8">
    <meta name="description" content="upload source schemas">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap/bootstrap-theme.min.css" />
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" />

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,700,600,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,800,700,900' rel='stylesheet' type='text/css'>

    <link href="css/minislide/flexslider.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/main.css" />
    <link href="css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" type="image/jpg" href="img/favicon.jpg">
    <script src="https://kit.fontawesome.com/587c50eb1a.js" crossorigin="anonymous"></script>

   <!-- <script type="text/javascript">
    $(document).ready(function (){
        $('[data-toggle="popover"]').popover({
            placement: 'bottom'
        });
    });    
    </script>-->
</head>

<body>
    <br><br>
    <div class="breadcrumb-color breadcrumb-contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb">
                        <li>
                            <a href="index.html">Home</a>
                        </li>
                        <li class="active">Upload Source Schemas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

                <a class="navbar-brand" href="index.html"><img src="images/logo3.png" alt="logo" /></a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse dropdown-prod-cart-scroll scroll-menu">

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown">Home <b class="caret"></b></a>
                        <ul class="dropdown-menu vertical-li">
                            <li><a href="index.html">Home 1</a></li>
                            <li><a href="home2.html">Home 2</a></li>
                            <li><a href="home3.html">Home 3</a></li>
                            <li><a href="home4.html">Home 4</a></li>
                            <li><a href="home5.html">Home 5</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown">About <b class="caret"></b></a>
                        <ul class="dropdown-menu vertical-li">
                            <li><a href="about.html">About Us Page 1</a></li>
                            <li><a href="about2.html">About Us Page 2</a></li>
                            <li><a href="about3.html">About Us Page 3</a></li>
                            <li><a href="about4.html">About Us Page 4</a></li>
                        </ul>
                    </li>

                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown">Shop <b class="caret"></b></a>
                        <ul class="dropdown-menu vertical-li">
                            <li><a href="shop/store.html">Shop Page</a></li>
                            <li><a href="shop/store_dresses.html">Shop Dresses</a></li>
                            <li><a href="shop/product.html">Single Prod.</a></li>
                            <li><a href="shop/product_dresses.html">Single Dresses</a></li>
                            <li><a href="shop/store_dresses_rc.html">Shop Dresses Right Col.</a></li>
                            <li><a href="shop/store_dresses_lc.html">Shop Dresses Left Col.</a></li>
                            <li><a href="shop/shoppingCart.html">Shopping Cart</a></li>
                        </ul>
                    </li>

                    <li class="dropdown menu-large"><a class="dropdown-toggle" data-toggle="dropdown">Pages <b class="caret"></b></a>
                        <ul class="menu-items megamenu dropdown-menu">
                            <li class="menu-item col-sm-3">
                                <h3 class="angula-megamenu-title">Page Samples 1</h3>
                                <ul class="mega-sub-menu">
                                    <li><i class="fa fa-angle-right"></i><a href="services.html">Services</a></li>
                                    <li><i class="fa fa-angle-right"></i><a href="meet_team.html">Meet the Team</a></li>
                                    <li><i class="fa fa-angle-right"></i><a href="faq.html">FAQ</a></li>
                                    <li><i class="fa fa-angle-right"></i><a href="404.html">404</a></li>
                                    <li><i class="fa fa-angle-right"></i><a href="pricing.html">Pricing Table</a></li>
                                </ul>
                            </li>
                            <li class="menu-item col-sm-3">
                                <h3 class="angula-megamenu-title">Page Samples 2</h3>
                                <ul class="mega-sub-menu">
                                    <li><i class="fa fa-angle-right"></i><a href="templates/footer.html">Footer 1 </a></li>
                                    <li><i class="fa fa-angle-right"></i><a href="templates/footer_2.html">Footer 2</a></li>
                                    <li><i class="fa fa-angle-right"></i><a href="templates/footer_3.html">Footer 3</a></li>
                                    <li><i class="fa fa-angle-right"></i><a href="templates/footer_sponsor.html">Footer 4</a></li>
                                    <li><i class="fa fa-angle-right"></i><a href="templates/header_dark.html">Header dark</a></li>
                                </ul>
                            </li>
                            <li class="menu-item col-sm-6 menu-adwertising">
                                <div class="col-md-6">
                                    <figure class="hero-image hero-imacretina-1"></figure>
                                    <figure class="hero-image hero-imacretina-2"></figure>
                                    <figure class="hero-image hero-imacretina-3"></figure>
                                    <img class="discount" src="images/discount.png" alt="" />
                                </div>
                                <div class="col-md-6 menu-text-monitor">
                                    <p> Start something new.</p>
                                    <p><i class="fa fa-angle-right"></i> TV Monitor LED 19" HD</p>
                                    <p><i class="fa fa-angle-right"></i> Resolution: 1366x768</p>
                                    <p><i class="fa fa-angle-right"></i> Color: 16.7 milion</p>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown">Blog <b class="caret"></b></a>
                        <ul class="dropdown-menu vertical-li">
                            <li><a href="blog.html">Blog Prod</a></li>
                            <li><a href="blog_right.html">Blog Right Sidebar</a></li>
                            <li><a href="blog_left.html">Blog Left Sidebar</a></li>
                        </ul>
                    </li>
                    <li><a href="contact.html">Contact</a>
                    </li>

                </ul>

            </div>
        </div>
    </nav>
/*
   <?php
/*
   $_SESSION['uploadStatus']="";
   
$us = $_SESSION['uploadStatus'];

    if(empty($_POST["upload"]))
    {
        $_SESSION['uploadStatus']="";
    }

    if(isset($_SESSION["uploadStatus"]) && $_SESSION["uploadStatus"] != "")
    {
   echo '<script type="text/javascript">alert("' . $us .'")</script>';
   unset($_SESSION["uploadStatus"]);
    }
*/

   ?>
   */

    <div class="container">
        <div class="row last-inf-dt">
        <br>
            <div class="col-sm-12">
                <h4 class="contact-title">Upload Source Schemas</h4>

                <p class="contact-sub-txt">Upload source schemas in XML, CSV, JSON or SQL formats</p>
                <br><br>

                <form id="schemaUpload-form" enctype="multipart/form-data" role="form" method="POST" action="submitSchemas.php">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="schemaInput">Source Schema Files: </label>
                            <input type="file" name="sourceSchemas[]" multiple>
                        </div>
                        <br><br>
                        <div class="form-group col-lg-12">
                            <button type="submit" name="upload" class="btn btn-primary bt-contact-submit">Upload</button>
                            <!--<p><span class="error"></p>-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
    <footer>
        <div class="info-footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="col-xs-12"> <i class="fa fa-database"></i><span>&nbsp; AUTOMATIC&nbsp;&nbsp;SCHEMA&nbsp;&nbsp;MATCHER</span></div>
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/owl_carousel/owl.carousel.min.js" type="text/javascript"></script>
    <script src="js/script.js" type="text/javascript"></script>

</body>

</html>