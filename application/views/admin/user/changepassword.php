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
					<form autocomplete="off" class="needs-validation" name='change_password' id='change_password' method='post' action="<?php echo $action; ?>">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<input style="display:none" type="text" name="fakeusernameremembered" id='fakeuser'/>
					
						<div class="form-body">
							<h3 class="box-title"><?php echo $first_name.' '.$last_name;  ?> </h3>
							
							

							<?php 
								
								if(in_array($this->session->userdata('role'), $this->config->item('developerAccess'))){
									$editAllowed='';
								}
							?>
							<div class="row">
								<div class="col-md-6">						  
									<div class="form-group">
										<label class="control-label required">New Password</label>
										<input type="password" name="new_password" value="" id="new_password" class="form-control required" >								
									</div>
								</div>
								

								<div class="col-md-6">						  
									<div class="form-group">
										<label class="control-label">Retype New Password</label>
										<input type="password" name="retype_password" value="" id="retype_password" class="form-control">								
									</div>
								</div>								
							</div>						
						<div class="form-actions m-t-10">
							<button type="submit" class="btn btn-rounded btn-sm btn-success"> <i class="fa fa-check"></i>Reset</button>
							<button type="reset" class="btn btn-rounded btn-sm btn-danger" id='reset'><i class="fa fa-remove"></i>&nbsp;Cancel</button>
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
		

		$("#change_password").validate({			
			rules: {
				new_password: "required",
				retype_password: {
					required: true,
					equalTo: "#new_password"
				}
			}			
		});		 
			
	});

	

	
</script>

<style>
.selectBox {
  position: relative;
}
.selectBox select {
  width: 100%;
}
.overSelect {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
}
#checkboxes {
  display: none;
  border: 1px #dadada solid;
}
#checkboxes label {
  display: block;
}
#checkboxes label:hover {
  background-color: #1e90ff;
}
</style>

<script>
var expanded = false;




</script>