<?php include 'layout/css.php'; ?>
<style>
.class-logo{
	height:50px;
}
.navbar-header{
background:black !important;
}
</style>
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <div id="wrapper"> 
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header"> <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="icon-grid"></i></a>
                <div class="top-left-part"><a class="logo" href="<?php echo site_url('admin/dashboard/') ?>"><b><img src="<?php echo base_url();?>svi/logo.jpeg" alt="Botnum" class='class-logo' /></b><span class="hidden-xs"></span></a></div>
                <ul class="nav navbar-top-links navbar-left hidden-xs">
                    <li><a href="javascript:void(0)" class="open-close hidden-xs"><i class="icon-grid"></i></a></li>
                </ul>
				
					<?php
						$ci = get_instance();
						$ci->load->model('common_model');
						$notifications=$ci->common_model->get_notifications_by_user();
						$totalNotification=count($notifications);
					?>
				
					
					 <ul class="nav navbar-top-links navbar-right pull-right">
					
						<li class="dropdown"> 
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id='notification-bell'><i class="icon-bell"></i><strong id='notification_total'><?php if($totalNotification>0){ echo $totalNotification; } ?></strong>
								<div class="notify"><span class=""></span><span class=""></span></div>
							</a>
							<ul class="dropdown-menu dropdown-tasks animated bounceInRight">
								<?php foreach($notifications as $notification){ ?>
									<li>
										<a href="#">
											<p> <strong><?php echo $notification['search_name']; ?></strong> <br><span class="pull-right text-muted"><i>Completed at <?php echo $notification['end_time']; ?></i></span> </p>
										</a>
									</li>
									<li class="divider"></li>
								<?php } ?>							
							</ul>
                        <!-- /.dropdown-tasks -->
                    </li>
                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
						<b class="hidden-xs1"><?php echo $this->session->userdata('name'); ?></b></a>
                        <ul class="dropdown-menu dropdown-user animated flipInY">
                            <li><a href="<?php echo site_url('admin/user/changepassword/'.$this->session->userdata('id')); ?>"><i class="ti-settings"></i> Change Password</a></li>
                            <li><a href="<?php echo site_url('auth/logout'); ?>"><i class="fa fa-power-off"></i>  Logout</a></li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    	
                    <!-- /.dropdown -->
                </ul>
            </div>
            <!-- /.navbar-header -->
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <!-- Left navbar-header -->
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse slimscrollsidebar">
                <ul class="nav" id="side-menu">
                    
                   
                    <li> <a href="<?php echo site_url('admin/dashboard') ?>" class="waves-effect"><i class="ti-dashboard p-r-10"></i> <span class="hide-menu">Dashboard</span></a> </li>
                    <li><a href="javascript:void(0);" class="waves-effect"><i class="fa fa-cogs fa-fw"></i> <span class="hide-menu">Scraper Management<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<!--<li> <a href="<?php echo site_url('admin/keywords') ?>">Manage Keywords</a></li>-->
							<?php if($this->session->userdata('role')=='ADMIN'){ ?>
								<li> <a href="<?php echo site_url('admin/searches') ?>">Manage Searches</a></li>
								<li> <a href="<?php echo site_url('admin/scrape') ?>">Scrape Data</a></li>
							<?php } ?>
							<li> <a href="<?php echo site_url('admin/download') ?>">Download Records</a></li>
						</ul>
					</li>

					<?php if($this->session->userdata('role')=='ADMIN'){ ?>
						<li><a href="<?php echo site_url('admin/user/all_user_list') ?>" class="waves-effect"><i class="icon-user fa-fw"></i> <span class="hide-menu">User Management</span></a></li>
						<li><a href="<?php echo site_url('admin/proxy') ?>" class="waves-effect"><i class="fa fa-cog fa-fw"></i> <span class="hide-menu">Proxy Management</span></a></li>
					<?php } ?>
                    <li><a href="<?php echo site_url('auth/logout') ?>" class="waves-effect"><i class="icon-logout fa-fw"></i> <span class="hide-menu">Log out</span></a></li>
                </ul>
            </div>
        </div>
        <!-- Left navbar-header end -->
       
	   
	    <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                
			<div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title"><?php echo $page_title; ?></h4>
                    </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <ol class="breadcrumb">
                            <li><a href="#" id='backlink'>Back</a></li>
							<?php 
								if(!empty($breadcrumbs)){ 
									foreach ($breadcrumbs as $breadcrumb){ 
										echo $breadcrumb;
									}
								}
							 ?>
                        </ol>
                    </div>
                    <!-- /.col-lg-12 -->
                </div> 	
				
				
				
				
				<!--  row    -->
               <?php echo $main_content; ?>
                <!-- /.row -->
			
            </div>
            <!-- /.container-fluid -->
           <?php include 'layout/footer.php'; ?>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
    <!-- jQuery -->
	<script>
	$(document).ready(function(){
		$('#notification-bell').click(function(){
			$('#notification_total').html('');
			var apiUrl="<?php echo site_url('admin/common/updateNotificationReadStatus'); ?>";
			$.ajax({
				url:apiUrl,
				method: 'get',				
				dataType: 'text',
				beforeSend: function() {
					
				},
				success: function(response){
					
				},
				fail: function(xhr, textStatus, errorThrown){
				}
			});
		});
		
		$('.input-class-price').keyup(function(e){
			this.value = this.value.replace(/[^0-9.]/g, '');
		});	
		$(document).on("keyup", ".input-class-price" , function() {
			this.value = this.value.replace(/[^0-9.]/g, '');
		});
		$(document).on("keyup", ".input-class-int" , function() {
			this.value = this.value.replace(/[^0-9]/g, '');
		});
		$('#backlink').click(function(){
			parent.history.back();
			return false;
		});
	});

	</script>
   <?php include 'layout/js.php'; ?>

