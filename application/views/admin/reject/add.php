<!--.row-->

<div class="row">
	<div class="col-md-12">
	<?php if (validation_errors()) : ?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
				<?php echo validation_errors(); ?>
			</div>
    
		<?php elseif (!empty($errorMsg)) : ?>    
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
				<?php echo $errorMsg; ?>
			</div>    
		<?php endif; ?>
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
							<h3 class="box-title">Company Information</h3>
							<hr>
							<div class="row">
							 	<div class="col-md-3">
									<div class="form-group">
										<label class="control-label required">Website</label>
										<?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_id"'); ?>	
									</div>
								</div>	
								<div class="col-md-3">
									<div class="form-group">
										<label class="control-label required">Company Name</label>
										<input type="text" name="company_name" id="company_name" class="form-control" value="<?php if(!empty($company_name)) { echo $company_name; } ?>" autocomplete="off">
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Profile Url</label>
										<input type="text" name="companyUrl" id="companyUrl" class="form-control" value="<?php if(!empty($companyUrl)) { echo $companyUrl; } ?>" autocomplete="off">
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Website Url</label>
										<input type="text" name="website_url" id="website_url" class="form-control" value="<?php if(!empty($website_url)) { echo $website_url; } ?>" autocomplete="off">
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
					<form  class="needs-validation" autocomplete="off" name='bulkUpload' id='bulkUpload' method='post' action="<?php echo site_url('admin/reject/uploadCSV'); ?>" enctype="multipart/form-data">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

						<div class="form-body">
							<h3 class="box-title">Bulk Upload</h3>
							Please upload file in csv format, <a target="_blank" href='<?php echo base_url('uploads/sample_reject_list.csv'); ?>' class='font-weight-bold'>View</a> sample file
							<hr>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_idupload"'); ?>	
									</div>
								</div>
								<div class="col-md-3">						  
									<div class="form-group">
										<input type='file' name='rejectlist_file' > 
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
	});  

</script>