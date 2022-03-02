<!--.row-->

<div class="row">
	<div class="col-md-12">
		<?php $msg = $this->session->flashdata('msg'); ?>
		<?php if (isset($msg)): ?>
			<div class="alert alert-success delete_msg pull" style="width: 100%"> <i class="fa fa-check-circle"></i> <?php echo $msg; ?> &nbsp;
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
			</div>
		<?php endif ?>

		<?php $error_msg = $this->session->flashdata('error_msg'); ?>
		<?php if (isset($error_msg)): ?>
			<div class="alert alert-danger delete_msg pull" style="width: 100%"> <i class="fa fa-times"></i> <?php echo $error_msg; ?> &nbsp;
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
			</div>
		<?php endif ?>
	</div>
	<div class="col-md-12">
		<div class="panel panel-info">
			
			<div class="panel-wrapper collapse in" aria-expanded="true">
				<div class="panel-body">
					<form  class="needs-validation" autocomplete="off" name='addUser' id='addUser' method='post' action="<?php echo $action; ?>">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

						<div class="form-body">
							<h3 class="box-title">Proxy Info</h3>
							<hr>
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label required">User Name</label>
										<input type="text" name="uname" id="uname" class="form-control" value="<?php if(!empty($uname)) { echo $uname; } ?>" autocomplete="off">
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label required">Password</label>
										<input type="text" name="password" id="password" value="<?php if(!empty($password)) { echo $password; } ?>" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label required">IP</label>
										<input type="text" name="ip" id="ip" value="<?php if(!empty($ip)) { echo $ip; } ?>" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label required" >Port</label>
										<input type="text" name="port" id="port" value="<?php if(!empty($port)) { echo $port; } ?>" class="form-control required" placeholder="">
									</div>
								</div>	
							
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label required">Status</label>
										<select class="form-control required" name='status' id='status'>
											<option value="Y" <?php if( $status=='Y') { echo " selected "; } ?> >Active</option>				
											<option value="N" <?php if( $status=='N') { echo " selected "; } ?> >Inactive</option>		
										</select>
									</div>
								</div>													
														
							

							<!--/row-->
							</div>
							
						</div>
						<div class="row">
							<div class="col-md-2">
								<div class="form-actions">
									<button type="submit" class="btn btn-rounded btn-sm btn-success"> <i class="fa fa-check"></i>Save</button>
								</div>
							</div>
						</div>
					</form>
					
					<br>
					<hr>
					<form  class="needs-validation" autocomplete="off" name='addUser' id='addUser' method='post' action="<?php echo site_url('admin/proxy/uploadCSV'); ?>" enctype="multipart/form-data">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

						<div class="form-body">
							<h3 class="box-title">Bulk Proxy Upload</h3>
							Please upload file in csv format, <a target="_blank" href='<?php echo base_url('uploads/sample_Proxylist.csv'); ?>' class='font-weight-bold'>View</a> sample file
							<hr>
							<div class="row">
							<div class="col-md-3">						  
								<div class="form-group">
									<input type='file' name='proxylist_file' > 
								</div>
							</div>
							<div class="col-md-3">						  
								<div class="form-group">
								<button type="submit" class="btn btn-rounded btn-sm btn-success"> <i class="fa fa-check"></i>Upload</button> 
								</div>
							</div>
						  </div>						
							<!--/row-->
							</div>
							
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<!--./row-->
<script>
	$(document).ready(function() {						
		$("#addUser").validate({
			rules: {
				first_name: "required",					
				last_name: "required",					
				email: {
				  required: true,
				  email: true
				},
				username: "required",
				password: "required",
				user_role: "required",
				//tpc: "required",
			},
			messages: {
				first_name: "Please input first name",		
				last_name: "Please input last name",		
				email: "Please input correct email",
				username: "Please input username",
				password: "Please input password",
				user_role: "Please select user role",
			},				
		});
		
		/*$('#saveChallan').click(function() {
			$("#saveChallan").hide();
			$(".processing").show();
			setTimeout(function(){
				$(".processing").hide();
				$("#saveChallan").show();		
			}, 10000);
			$("#saveChallanForm").valid();
		});*/
	});  

</script>