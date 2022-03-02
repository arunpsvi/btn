<?php 
$system_name = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description;
$system_title = $this->db->get_where('settings', array('type' => 'system_title'))->row()->description;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
   
    <title><?php echo $system_name; ?></title>
    
	
	<!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url();?>svi/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>svi/plugins/bower_components/bootstrap-extension/css/bootstrap-extension.css" rel="stylesheet">
    
	<!-- Menu CSS -->
    <link href="<?php echo base_url();?>svi/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>svi/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    
    
	<!-- Custom CSS -->
    <link href="<?php echo base_url();?>svi/css/style.css" rel="stylesheet">
      
	<!-- color CSS -->
    <link href="<?php echo base_url();?>svi/css/colors/blue.css" id="theme" rel="stylesheet">
    
	
	<!--<link href="<?php echo base_url(); ?>svi/plugins/bower_components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />-->
	
</head>
<body>
<!-- jQuery -->
	<link href = "<?php echo base_url(); ?>jquery/jquery-ui.css" rel = "stylesheet">
	<link href = "<?php echo base_url(); ?>svi/jquery.multiselect.css" rel = "stylesheet">
	<link href = "<?php echo base_url(); ?>svi/css/bootstrap-toggle.min.css" rel = "stylesheet">
	
	<!--jquery-confirm css  -->
	<link href = "<?php echo base_url(); ?>svi/css/jquery-confirm.min.css" rel = "stylesheet">
	<script src="<?php echo base_url(); ?>svi/js/jquery-3.4.1.min.js"></script>	

	<script src = "<?php echo base_url(); ?>jquery/jquery-ui.js"></script>
	<script src="<?php echo base_url(); ?>svi/js/jquery.validate.min.js"></script>	
	<script src="<?php echo base_url(); ?>svi/jquery.multiselect.js"></script>	

		
        
