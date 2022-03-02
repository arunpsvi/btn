
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
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                </div>
            <?php endif ?>

            <?php $error_msg = $this->session->flashdata('error_msg'); ?>
            <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger delete_msg pull" style="width: 100%"> <i class="fa fa-times"></i> <?php echo $error_msg; ?> &nbsp;
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                </div>
            <?php endif ?>
			<?php if($flag==0){ ?>
				<form autocomplete="off" class="needs-validation" name='searchResult' id='searchResult' method='get' action="<?php echo $action; ?>">
					<h3 class="box-title">Download Records</h3>
					<hr>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label required">Select Website</label>
								<?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_id"'); ?>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Start scraped date</label>
								<input type="date" name="scraped_date_start" id="scraped_date_start" class="form-control my-datepicker" value="<?php if(!empty($scraped_date_start)) { echo $scraped_date_start; } ?>" autocomplete="<?php echo $autocomplete; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">End scraped date</label>
								<input type="date" name="scraped_date_end" id="scraped_date_end" class="form-control my-datepicker" value="<?php if(!empty($scraped_date_end)) { echo $scraped_date_end; } ?>" autocomplete="<?php echo $autocomplete; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group" id='qualifydropdown'>
								<label class="control-label">Qualify</label>
								<?php echo form_dropdown('qualify', $qualifySearchArr, $qualify,'class="form-control" id="qualify"'); ?>
							</div>
						</div>

						
										
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								<button type="submit" class="btn btn-rounded btn-sm btn-info"> <i class="fa fa-search"></i> Search</button>
<button type="button" id="download" class="btn btn-rounded btn-sm btn-info"> <i class="fa fa-download"></i> Download</button>
<button type="button" id="deleteResults" class="btn btn-rounded btn-sm btn-danger"> <i class="fa fa-trash"></i> Delete</button>
<?php if($userRole=='ADMIN'){ ?>
<button type="button" id="deleteAll" title='It will delete all matching records filld in search box.' class="btn btn-rounded btn-sm btn-danger"> <i class="fa fa-trash"></i> Delete All</button>
<?php } ?>
							</div>
						</div>
						
					</div>
					<?php if($userRole=='ADMIN'){ ?>
						<br><span class='text-danger'>* Delete All will delete all matching records filld in search box.</span>
					<?php } ?>
					<div class="row">
						<div class="col-md-12">
							<b>Total records - <?php echo $total_rows; ?></b>
						</div>
					</div>
					<input type='hidden' name='deleteAllSearch' id='deleteAllSearch' value=''>
				</form>
				<?php } ?>
				<div class="table-responsive">
					<table class="table-sm table-hover text-nowrap table-bordered" cellspacing="0" width='100%' style='font-size: 13px;'>
						<thead >
							<tr  class="bg-info">                                   
								<?php 
									$websiteId=$_GET['website_id'];
									$scraped_date=$_GET['scraped_date'];
									$per_page=$_GET['per_page'];
									$sort_order=$_GET['sort_order'];
									$sortIcon='';
									if($sort_order=='asc'){
										$sort_order='desc';
										$sortIcon='fa-sort-asc';
									}elseif($sort_order=='desc'){
										$sort_order='asc';
										$sortIcon='fa-sort-desc';
									}
									if(empty($sort_order)){
										$sort_order='asc';
									}
									$queryString="?website_id=$websiteId&scraped_date=$scraped_date&per_page=$per_page&sort_order=$sort_order"; 

								?>
								<?php //echo anchor('admin/download/'.$queryString, 'Qualify<i class="fa '.$sortIcon.' m-l-5" ></i>',array('class' => 'font-weight-bold text-white')); ?>
								<th style='min-width:40px !important;' class='font-weight-bold text-white'><i class="fa fa-trash m-r-5"></i><input type="checkbox" class="form-check-input" name="deleteAllResults" id="deleteAllResults"></th>
								<th class='font-weight-bold text-white'>Qualify</th>
								<th class='font-weight-bold text-white'>Name</th> 
								<th class='font-weight-bold text-white'>Phone</th> 
								<th class='font-weight-bold text-white'>Email</th> 
								<th class='font-weight-bold text-white'>Source</th> 
								<th style='min-width:80px !important;' class='font-weight-bold text-white'>Scraped Date</th> 
								<th style='min-width:80px !important;' class='font-weight-bold text-white'>Posted Date</th> 
								<th class='font-weight-bold text-white'>Location</th> 
								<th class='font-weight-bold text-white'>Category</th> 
								<th class='font-weight-bold text-white'>Sub Category</th> 
								<th class='font-weight-bold text-white'>Keyword</th>							
								<th class='font-weight-bold text-white'>Post Title</th>							
								<th class='font-weight-bold text-white'>Post Url</th>											
								<th class='font-weight-bold text-white'>Compensation</th>											
								<th class='font-weight-bold text-white'>Exact match</th>											
							</tr>
						</thead>
						<tbody>
						<form name='frmDeleteResults' id='frmDeleteResults' action='<?php echo site_url("admin/download/delete"); ?>' method='post'>
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
						<input type="hidden" name="website_id" value="<?php echo $websiteId; ?>">
						<input type="hidden" name="scraped_date" value="<?php echo $scraped_date; ?>">
						<input type="hidden" name="qualify" value="<?php echo $qualify; ?>">
						
						<?php foreach ($jobListings as $jobListing){

						if($jobListing['exact_match']=='Y'){
							$jobListing['exact_match']='Yes';
						}else{
							$jobListing['exact_match']='No';
						}

						$jobListing['keywords']=preg_replace('/,/is',', ',$jobListing['keywords']);
						$result_id =$jobListing['result_id'];
						?>

						<tr >                                   
							<td><input type="checkbox" class="class-name record_delete" name="record_delete[]" value='<?php echo $jobListing['result_id']; ?>'></td>
							<td ><?php $name="qualify_$result_id"; echo form_dropdown($name, $qualifyArr, $jobListing['qualify'],' style="height:22px;" id="'.$name.'" class="class-qualify"'); ?></td>
							<td><input type='text' class="class-name" name='<?php echo 'name_'.$result_id; ?>' id='<?php echo 'name_'.$result_id; ?>' value='<?php echo $jobListing['name']; ?>' /></td>
							<td><input type='text' class="class-phone" name='<?php echo 'phone_'.$result_id; ?>' id='<?php echo 'phone_'.$result_id; ?>' value='<?php echo $jobListing['phone']; ?>' /></td>
							<td><input type='text' class="class-email" name='<?php echo 'email_'.$result_id; ?>' id='<?php echo 'email_'.$result_id; ?>' value='<?php echo $jobListing['email']; ?>' /></td>
							<td><?php echo $jobListing['url']; ?></td>
							<td ><?php echo $jobListing['scraped_date']; ?></td>
							<td><?php echo $jobListing['posted_date']; ?></td>
							<td><?php echo $jobListing['location']; ?></td>
							<td><?php echo $jobListing['category_name']; ?></td>
							<td><?php echo $jobListing['sub_category_name']; ?></td>
							<td><?php echo $jobListing['keywords']; ?></td>
							<td><?php echo $jobListing['title']; ?></td>
							<td><a target='_blank' href="<?php echo $jobListing['job_url']; ?>"><?php echo $jobListing['job_url']; ?></a></td>
							<td><?php echo $jobListing['compensation']; ?></td>
							<td><?php echo $jobListing['exact_match']; ?></td>
									
						</tr>
						<?php } ?>
						</form>	
						</tbody>
					</table>
				</div>
				
                </div>
				<?php echo $pagination; ?>		
				
            </div>
        </div>
    </div>

 </div>
 
<!-- Modal -->
<div class="modal " id="addnewqualify" tabindex="-1" role="dialog" aria-labelledby="addnewqualifyLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addnewqualifyLabel">Add new qualify</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label required">Qualify name</label>
							<input type="text" name="qualify_name" id="qualify_name" class="form-control" placeholder="Qualify" autocomplete="off">
						</div>
					</div>
					<div class="col-md-12" id='ajaxloader'>
						
					</div>
				</div>
			</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info" id='savequalify'>Save</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
	
	$(".class-qualify").on('change', function() {		
		var id=this.id.split('_')[1];
		var qualify=$('#qualify_'+id).val()
		var apiUrl='<?php echo site_url('admin/download/updateRecords'); ?>';
		apiUrl=apiUrl+"?updateQualify=true&qualify="+qualify+"&id="+id;
		$.ajax({
			url:apiUrl,
			method: 'get',				
			dataType: 'text',
			beforeSend: function() {
				//$("#ajaxloader").html('<?php echo $this->config->item("spinner_code") ?>');
			},
			success: function(response){
				//alert(response);
				//$("#postCodeHtml").html(response);	
				//location.href='<?php echo site_url('admin/quotation/quotation_list'); ?>';
			},
			fail: function(xhr, textStatus, errorThrown){
				//$("#postCodeHtml").html('Some error occured! Please try again.');
			}
		}); 
    }); 
	$("#savequalify").on('click', function() {
		var qualifyName=$('#qualify_name').val();
		var apiUrl='<?php echo site_url('admin/common/addQualify'); ?>';
		apiUrl=apiUrl+"?qualifyName="+qualifyName;
		$.ajax({
			url:apiUrl,
			method: 'get',				
			dataType: 'json',
			beforeSend: function() {
				$("#ajaxloader").html('<?php echo $this->config->item("spinner_code") ?>');
			},
			success: function(response){
				
				if(response.status==1){
					$("#qualify").html(response.option);
					setTimeout(function(){
						$('#addnewqualify').modal('hide');
					}, 2000);
				}
				$("#ajaxloader").html(response.message);
									
			},
			fail: function(xhr, textStatus, errorThrown){
				//$("#postCodeHtml").html('Some error occured! Please try again.');
			}
		}); 
    }); 
	$("#qualify").on('change', function() {		
		if($('#qualify').val()=='ADD#NEW'){
			$('#qualify_name').val('');
			$("#ajaxloader").html('');
			$('#addnewqualify').modal({
				show: true
			});		
			$('#qualify').val('');
		}
    }); 
	$(".class-name,.class-email,.class-phone").blur(function() {		
		var id=this.id.split('_')[1];
		var name=$('#name_'+id).val();
		var phone=$('#phone_'+id).val();
		var email=$('#email_'+id).val();
		var apiUrl='<?php echo site_url('admin/download/updateRecords'); ?>';
		apiUrl=apiUrl+"?id="+id+"&name="+name+"&phone="+phone+"&email="+email;
		$.ajax({
			url:apiUrl,
			method: 'get',				
			dataType: 'text',
			beforeSend: function() {
				//$("#postCodeHtml").html('<?php echo $this->config->item("spinner_code") ?>');
			},
			success: function(response){
				//alert(response);
				//$("#postCodeHtml").html(response);	
				//location.href='<?php echo site_url('admin/quotation/quotation_list'); ?>';
			},
			fail: function(xhr, textStatus, errorThrown){
				//$("#postCodeHtml").html('Some error occured! Please try again.');
			}
		}); 
    });
	$('#download').on('click', function() {
		var url='<?php echo site_url("admin/download/downloadCSV"); ?>';

		url=url+"?website_id="+$('#website_id').val();
		url=url+"&scraped_date_start="+$('#scraped_date_start').val();
		url=url+"&scraped_date_end="+$('#scraped_date_end').val();
		url=url+"&qualify="+$('#qualify').val();
		location.href=url;
	});
	$('#deleteResults').on('click', function() {
		$.confirm({
			title: 'Warning!',
			content: 'Once deleted, records will not be recovered',
			type: 'red',
			buttons: {					
				confirm: function () {						
					$( "#frmDeleteResults" ).submit();						
				},
				cancel: function () {
				}					
			}
		});	
		
	});
	$('#deleteAllResults').on('click', function() {
		if($(this).prop("checked")==true){
			$(".record_delete").prop("checked",true);	
		}else{
			$(".record_delete").prop("checked",false);
		}		
	});
	$('#deleteAll').on('click', function() {
		$.confirm({
			title: 'Warning!',
			content: 'Once deleted, records will not be recovered',
			type: 'red',
			buttons: {					
				confirm: function () {						
					$('#deleteAllSearch').val('true');
					$('#searchResult').submit();					
				},
				cancel: function () {
				}					
			}
		});			
	});
});
</script>