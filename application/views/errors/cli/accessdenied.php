<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$system_name = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description;
$system_title = $this->db->get_where('settings', array('type' => 'system_title'))->row()->description;
?>
<!DOCTYPE html>  
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Softvision India">
<title><?php echo $system_name; ?></title>
<!-- Bootstrap Core CSS -->
<link href="<?php echo base_url(); ?>svi/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>svi/plugins/bower_components/bootstrap-extension/css/bootstrap-extension.css" rel="stylesheet">
<!-- animation CSS -->
<!--<link href="<?php echo base_url(); ?>svi/css/animate.css" rel="stylesheet">-->
<!-- Custom CSS -->
<link href="<?php echo base_url(); ?>svi/css/style.css" rel="stylesheet">
<!-- color CSS -->
<!--<link href="<?php echo base_url(); ?>svi/css/colors/megna.css" id="theme"  rel="stylesheet">-->
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<!-- Preloader -->
<div class="preloader" >
  <div class="cssload-speeding-wheel" ></div>
</div>
<section id="wrapper" class="login-register" > 
  <div class="login-box login-sidebar" >
    <div class="white-box" style="margin-top:80px;border:1px solid red"> 

               <div align="center"> 
			   
					<h1><?php echo $heading; ?></h1>
					<div align="center">
                      <?php echo $message; ?> 
                    </div>
				<br><br>
						
        			
            </div>
        </div>
		
		
		
		

    </section>
	

<!-- jQuery -->
<script src="<?php echo base_url(); ?>svi/js/jquery-3.4.1.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<!--<script src="<?php echo base_url(); ?>svi/bootstrap/dist/js/tether.min.js"></script>-->
<script src="<?php echo base_url(); ?>svi/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>svi/plugins/bower_components/bootstrap-extension/js/bootstrap-extension.min.js"></script>

<!--Custom JavaScript -->
    <script src="<?php echo base_url() ?>svi/js/custom.min.js"></script>
    <script src="<?php echo base_url() ?>svi/js/custom.js"></script>
	

	

</body>

</html>
