<!--.row-->
<link href="<?php echo base_url();?>svi/css/bootstrap-tagsinput.css" rel="stylesheet" />
<script src="<?php echo base_url();?>svi/js/bootstrap-tagsinput.min.js"></script>
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
					<form autocomplete="off" class="needs-validation" name='addKeywords' id='addKeywords' method='post' action="">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<input style="display:none" type="text" name="fakeusernameremembered"/>

						<div class="form-body">
							<hr>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label required">Select Website</label>
										<?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_id"'); ?>
										
									</div>
								</div>
								<?php  $ci = get_instance(); ?>
								<div class="col-md-12">
									<label class="control-label required">Keywords</label>
									<div class="tags-default">
										<input type="text" name='keywords' id='keywords' value="<?php if(!empty($keywords)){ echo $keywords; } ?>" data-role="tagsinput" placeholder="add tags" />
									</div>
								</div>
								<div class="col-md-3">
									<div class="checkbox checkbox-info">
										<input name='load_keywords' id="load_keywords" type="checkbox" <?php if(!empty($loadDB)){ echo " checked "; } ?>>
										<label for="load_keywords"><b>Load keywords from database</b></label>								
									</div>
								</div>
															
						</div>				
					
						<div class="form-actions m-t-10">
							
							<button type="submit" class="btn btn-rounded btn-sm btn-success"> <i class="fa fa-check"></i>Save</button>
							
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
		$("#load_keywords").click(function(){	
			if($('#load_keywords').is(":checked")){
				var website_id=$("#website_id").val();
				var url ='<?php echo site_url('admin/keywords'); ?>';
				url=url+"?website_id="+website_id+"&loadDB=1";
				location.href=url;
			}else{
				var website_id=$("#website_id").val();
				var url ='<?php echo site_url('admin/keywords'); ?>';
				url=url+"?website_id="+website_id;
				location.href=url;
			}
		});

		$("#website_id").change(function(){		
			var website_id=$("#website_id").val();
			$("#keywords").html('');
			var apiUrl='<?php echo site_url('admin/common/getKeywords'); ?>';
			apiUrl=apiUrl+"?website_id="+website_id;

			$.ajax({
				url:apiUrl,
				method: 'get',				
				dataType: 'text',
				success: function(response){
					$("#keywords").val(response);
				},
			});			
		});		
	}); 
</script>