
    <!-- Start Page Content -->

    <div class="row">
        <div class="col-lg-12">

            
           <div class="panel panel-info">
              <div class="panel-body table-responsive">		
			  <div class="alert alert-success delete_msg pull d-none" id='close_checkbox' style="width: 100%"> 
			  </div>
				 <?php $msg = $this->session->flashdata('msg'); ?>
            <?php if (isset($msg)): ?>
                <div class="alert alert-success delete_msg pull" style="width: 100%"> <i class="fa fa-check-circle"></i> <?php echo $msg; ?> &nbsp;
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">X</span> </button>
                </div>
            <?php endif ?>

            <?php $error_msg = $this->session->flashdata('error_msg'); ?>
            <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger delete_msg pull" style="width: 100%"> <i class="fa fa-times"></i> <?php echo $error_msg; ?> &nbsp;
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">X</span> </button>
                </div>
            <?php endif ?>
			<?php if($flag==0){
				$ci = get_instance(); ?>
				<form autocomplete="off" class="needs-validation" name='searchResult' id='searchResult' method='get' action="<?php echo $action; ?>">
					<h3 class="box-title">My Bots</h3>
					<hr>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label required">Select Website</label>
								<?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_id"'); ?>
							</div>
						</div>
						
					
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<?php if($userRole == 'ENDUSER'){ ?>
                                <button type="button" id="mybots" class="btn btn-rounded btn-sm btn-info"> <i class="fa fa-eye"></i> My Bots</button>
                                <?php } ?>
							</div>
						</div>
						
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<b>Total records - <?php echo $total_rows; ?></b>
						</div>
					</div>
					<input type='hidden' name='deleteAllSearch' id='deleteAllSearch' value=''>
				</form>
				<?php } ?>
				<div class="table-responsive">
					<table class="table-sm table-hover table-bordered" cellspacing="0" width='100%'>
                            <thead>
                                <tr>
                                    <th>Bot ID</th>
                                    <th>Website</th>
                                    <th>Bot Name</th>                                    
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                   
                                </tr>
                            </tfoot>
                            
                            <tbody>
                            <?php  foreach ($bots as $bot): ?>
                                <?php 
									$ci = get_instance();	
                                    
								?>
                                <tr>
                                    <td><?php echo $bot['keyword_id']; ?></td>
                                    <td><?php echo $websiteListArr[$bot['website_id']]; ?></td>
                                    <td><?php echo $bot['search_name']; ?></td>                                   
								
		                        </tr>

                            <?php endforeach ?>

                            </tbody>
                        </table>
				</div>
				
                </div>
				<?php echo $pagination; ?>		
				
            </div>
        </div>
    </div>

 </div>    
<script>
$(document).ready(function(){
	
	$('#mybots').on('click', function() {
		var website_id=$('#website_id').val();
		var url='<?php echo site_url("admin/download/mybots"); ?>';
		//url=url+'?wid='+website_id;
		location.href=url;
	});
    $('#website_id').on('change', function() {
		var url='<?php echo site_url("admin/download/mybots"); ?>';
		url=url+"?wid="+$('#website_id').val();		
		location.href=url;
	});
	
	
});



</script>