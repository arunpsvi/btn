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
							<h3 class="box-title">Person Info</h3>
							<hr>
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label required">First Name</label>
										<input type="text" name="first_name" id="first_name" class="form-control" value="<?php if(!empty($first_name)) { echo $first_name; } ?>" autocomplete="off">
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label required">Last Name</label>
										<input type="text" name="last_name" id="last_name" value="<?php if(!empty($last_name)) { echo $last_name; } ?>" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label required">E-mail Address</label>
										<input type="text" name="email" id="email" value="<?php if(!empty($email)) { echo $email; } ?>" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Mobile</label>
										<input type="text" name="mobile" id="mobile" value="<?php if(!empty($mobile)) { echo $mobile; } ?>" class="form-control" placeholder="">
									</div>
								</div>	
							
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label required">User Role</label>
										<select class="form-control required" name='user_role' id='user_role'>
											<option value="">Select</option>
											<?php  foreach ($user_roles as $key=>$value){  ?>
											<option value="<?php echo $key; ?>" <?php if($category==$key){ echo "selected"; } ?>><?php echo $value; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>													
														
							
							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label required">Email Notification</label>
									<select class="form-control required" name='email_notification' id='email_notification'>
										<option value='0' <?php if($email_notification=='0') { echo " selected ";} ?>>No</option>
										<option value='1' <?php if($email_notification=='1') { echo " selected ";} ?>>Yes</option>
									</select>
								</div>
							</div>
							
							<?php if($this->session->userdata('role')=='ADMIN'){ ?>
							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label required">Login status</label>
									<select class="form-control required" name='status' id='status'>
										<option value='1' <?php if($status=='1') { echo " selected ";} ?>>Active</option>
										<option value='0' <?php if($status=='0') { echo " selected ";} ?>>Inactive</option>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label required">API Access</label>
									<select class="form-control required" name='api_access' id='api_access_status'>										
										<option value='0' <?php if($api_access=='0') { echo " selected ";} ?>>No</option>
										<option value='1' <?php if($api_access=='1') { echo " selected ";} ?>>Yes</option>
									</select>
								</div>
							</div>
							<div class="col-md-4" id='div_useraccess'>
								<div class="form-group">
									<label class="control-label ">User Access</label>
									<?php echo form_dropdown('useraccess[]', $allSearchesArr, $allSearchesArr,'multiple="multiple" class="3col active" id="useraccess"'); ?>    								
								</div>
							</div>
							
							

							<input type='hidden' name='hidSearchOptions' id='hidSearchOptions' value="<?php if(!empty($selectedSearchesToStr)){ echo $selectedSearchesToStr; } ?>" >
							<?php } ?>
							<?php if(empty($user_id)){ ?>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label required">Password</label>
									<input type="password" name="password" id="password" class="form-control" placeholder="">
								</div>
							</div>
							<?php }else{ ?>
								<input type="hidden" name="password" id="password" value="<?php if(!empty($password)) { echo $password; } ?>">
							<? } ?>

							<!--/row-->
							</div>
							
						</div>
						<?php
							if($api_access=='1'){ ?>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label ">API Key</label>
										<input type="text" readonly name="apikey" id="apikey" class="form-control" placeholder="" value="<?php echo $api_key;?>">
									</div>
								</div>
							</div>

							
							<?php }	?>
						<div class="row">
							<div class="col-md-2">
								<div class="form-actions">
									<button type="submit" class="btn btn-rounded btn-sm btn-success"> <i class="fa fa-check"></i>Save</button>
								</div>
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

		
			$('#useraccess').multiselect({
				columns: 1,
				placeholder: 'Select Search Bot',
				search: true,
				searchOptions: {
					'default': 'Search Bot'
				},
				selectAll: true
			});

			var hidValue = $("#hidSearchOptions").val();
			if(hidValue!=''){
				var selectedOptions = hidValue.split(",");
				for(var i in selectedOptions) {
					var optionVal = selectedOptions[i];
					$("#useraccess").find("option[value="+optionVal+"]").prop("selected", "selected");
				}
				$("#useraccess").multiselect('reload');
			}
		
	});  

</script>