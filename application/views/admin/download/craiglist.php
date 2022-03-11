<?php
	$showHideArray=Array();

	$showHideArray['shdiv_Qualify']='Qualify';
	$showHideArray['shdiv_Name']='Name';
	$showHideArray['shdiv_Phone']='Phone';
	$showHideArray['shdiv_Email']='Email';
	$showHideArray['shdiv_Source']='Source';
	$showHideArray['shdiv_Scraped_Date']='Scraped Date';
	$showHideArray['shdiv_Posted_Date']='Posted Date';
	$showHideArray['shdiv_Location']='Location';
	$showHideArray['shdiv_Category']='Category';
	$showHideArray['shdiv_Sub_Category']='Sub Category';
	$showHideArray['shdiv_Keyword']='Keyword';
	$showHideArray['shdiv_Post_Title']='Post Title';
	//$showHideArray['shdiv_Post_Url']='Post Url';
	$showHideArray['shdiv_Compensation']='Compensation';
	$showHideArray['shdiv_Exact_match']='Exact match';
	ksort($showHideArray);
	if(empty($hidSelectedOptions)){
		foreach ($showHideArray as $key=>$value) {
			$hidSelectedOptions .=$key.",";
		}
	}
	$hidSelectedOptions=rtrim($hidSelectedOptions, ',');
?>
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
					<h3 class="box-title">Download Records</h3>
					<hr>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label required">Select Website</label>
								<?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_id"'); ?>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label ">Select Bot</label>
								<select name="showHideBots[]" multiple="multiple" class="3col active" id="showHideBots">
									<?php foreach ($botArray as $key=>$value) {
										$botData=explode('_',$key);
										$condition=array(
											"search_id"=>$botData[1]
										);
										if(!empty($scraped_date_start)){
											$condition["scraped_date"]=$scraped_date_start;
										}
										$totalRecords=$ci->common_model->countResults($condition)->total;
										?>
										<option value="<?php echo $key; ?>"><?php echo $value." (".$totalRecords.") "; ?></option>
									<?php } ?>
								</select>						
							</div>
						</div>
						<input type="hidden" name="hidSelectedBots" id="hidSelectedBots" value="<?php echo $hidSelectedBots; ?>">
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
						<?php if($userRole != 'ENDUSER'){?>
						<div class="col-md-2">
							<div class="form-group" id='qualifydropdown'>
								<label class="control-label">Qualify</label>
								<?php echo form_dropdown('qualify', $qualifySearchArr, $qualify,'class="form-control" id="qualify"'); ?>
							</div>
						</div>
						<?php } ?>
						<?php if($userRole != 'ENDUSER'){?>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label ">Show/Hide Columns</label>
								<select name="showHideCols[]" multiple="multiple" class="3col active" id="showHideCols">
									<?php foreach ($showHideArray as $key=>$value) {
										?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php } ?>
								</select>						
							</div>
						</div>
						<input type="hidden" name="hidSelectedOptions" id="hidSelectedOptions" value="<?php echo $hidSelectedOptions; ?>">									
						<?php } ?>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								
								<button type="submit" class="btn btn-rounded btn-sm btn-info"> <i class="fa fa-search"></i> Search</button>
<button type="button" id="download" class="btn btn-rounded btn-sm btn-info"> <i class="fa fa-download"></i> Download</button>

<!-- <button type="button" id="savemyfilter" class="btn btn-rounded btn-sm btn-info"> <i class="fa fa-save"></i> Save My Filter</button> -->
<?php if($userRole != 'ENDUSER'){ ?>
<button type="button" id="deleteResults" class="btn btn-rounded btn-sm btn-danger"> <i class="fa fa-trash"></i> Delete</button>
<?php if($userRole=='ADMIN'){ ?>
<button type="button" id="deleteAll" title='It will delete all matching records filld in search box.' class="btn btn-rounded btn-sm btn-danger"> <i class="fa fa-trash"></i> Delete All</button>
<?php }} ?>
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
								<?php if($userRole != 'ENDUSER'){?>
								<th style='min-width:40px !important;' class='font-weight-bold text-white'><i class="fa fa-trash m-r-5"></i><input type="checkbox" class="form-check-input" name="deleteAllResults" id="deleteAllResults"></th>
								<?php } ?>
								<th class='font-weight-bold text-white'>Action</th>
								<th class='font-weight-bold text-white'>No.</th>
								<?php if($userRole != 'ENDUSER'){?>
								<th class='font-weight-bold text-white shdiv_Qualify'>Qualify</th>
								<th class='font-weight-bold text-white shdiv_Name'>Name</th> 
								<th class='font-weight-bold text-white shdiv_Phone'>Phone</th> 
								<th class='font-weight-bold text-white shdiv_Email'>Email</th>
								<?php } ?> 
								<th class='font-weight-bold text-white shdiv_Source'>Source</th> 
								<th style='min-width:80px !important;' class='font-weight-bold text-white shdiv_Scraped_Date'>Scraped Date</th> 
								<th style='min-width:80px !important;' class='font-weight-bold text-white shdiv_Posted_Date'>Posted Date</th> 
								<th class='font-weight-bold text-white shdiv_Location'>Location</th> 
								<th class='font-weight-bold text-white shdiv_Category'>Category</th> 
								<th class='font-weight-bold text-white shdiv_Sub_Category'>Sub Category</th> 
								<th class='font-weight-bold text-white shdiv_Keyword'>Keyword</th>							
								<th class='font-weight-bold text-white shdiv_Post_Title'>Post Title</th>							
								<!--<th class='font-weight-bold text-white shdiv_Post_Url'>Post Url</th>											-->
								<th class='font-weight-bold text-white shdiv_Compensation'>Compensation</th>											
								<th class='font-weight-bold text-white shdiv_Exact_match'>Exact match</th>											
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

						<tr class='BOT_<?php echo $jobListing['search_id']; ?>'>  
						<?php if($userRole != 'ENDUSER'){?>                              
							<td><input type="checkbox" class="class-name record_delete" name="record_delete[]" value='<?php echo $jobListing['result_id']; ?>'></td>
						<?php } ?>
							<td>
								<a href="#" title="View Details" class="viewJob" id='jid_<?php echo $jobListing['result_id']; ?>' ><i class="fa fa-eye icon-blue m-r-5"></i></a>
								<a href="<?php echo $jobListing['job_url']; ?>" target='_blank' title="View Post"><i class="fa fa-anchor icon-blue m-r-5"></i></a>
							</td>
							<td><?php echo $jobListing['result_id']; ?></td>
							<?php if($userRole != 'ENDUSER'){?>
								<td class='shdiv_Qualify'><?php $name="qualify_$result_id"; echo form_dropdown($name, $qualifyArr, $jobListing['qualify'],' style="height:22px;" id="'.$name.'" class="class-qualify "'); ?></td>
								<td class='shdiv_Name'><input type='text' class="class-name" name='<?php echo 'name_'.$result_id; ?>' id='<?php echo 'name_'.$result_id; ?>' value='<?php echo $jobListing['name']; ?>' /></td>
								<td class='shdiv_Phone'><input type='text' class="class-phone" name='<?php echo 'phone_'.$result_id; ?>' id='<?php echo 'phone_'.$result_id; ?>' value='<?php echo $jobListing['phone']; ?>' /></td>
								<td class='shdiv_Email'><input type='text' class="class-email" name='<?php echo 'email_'.$result_id; ?>' id='<?php echo 'email_'.$result_id; ?>' value='<?php echo $jobListing['email']; ?>' /></td>
							<?php } ?>
							<td class='shdiv_Source'><?php echo $jobListing['url']; ?></td>
							<td class='shdiv_Scraped_Date'><?php echo $jobListing['scraped_date']; ?></td>
							<td class='shdiv_Posted_Date'><?php echo $jobListing['posted_date']; ?></td>
							<td class='shdiv_Location'><?php echo $jobListing['location']; ?></td>
							<td class='shdiv_Category'><?php echo $jobListing['category_name']; ?></td>
							<td class='shdiv_Sub_Category'><?php echo $jobListing['sub_category_name']; ?></td>
							<td class='shdiv_Keyword'><?php echo $jobListing['keywords']; ?></td>
							<td class='shdiv_Post_Title'><?php echo $jobListing['title']; ?></td>
							<!--<td class='shdiv_Post_Url'><a target='_blank' href="<?php echo $jobListing['job_url']; ?>"><?php echo $jobListing['job_url']; ?></a></td>-->
							<td class='shdiv_Compensation'><?php echo $jobListing['compensation']; ?></td>
							<td class='shdiv_Exact_match'><?php echo $jobListing['exact_match']; ?></td>
									
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
<style>
	.wrapTd{
		word-wrap: break-word;min-width: 160px;max-width: 160px;
	}
</style>
<div class="modal " id="viewDetails" tabindex="-1" role="dialog" aria-labelledby="addnewqualifyLabel" aria-hidden="true">

	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addnewqualifyLabel">View Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id='modal-body'> 
				
			</div>

    </div>
  </div>
</div>

<script>
    $(function () {
        $('#showHideCols.active.3col').multiselect({
            columns: 2,
            placeholder: 'Show/Hide Columns',
            search: true,
            searchOptions: {
                'default': 'Show/Hide Columns'
            },
            selectAll: false
        });

		$('#showHideBots.active.3col').multiselect({
            columns: 1,
            placeholder: 'Select Bot',
            search: true,
            searchOptions: {
                'default': 'Select Bot'
            },
            selectAll: true
        });
    });
</script>
<script>
$(document).ready(function(){
	
	
	var hidValue = $("#hidSelectedOptions").val();
	//alert(typeof(hidValue));
	if(hidValue!='' && typeof(hidValue)!='undefined'){
		var selectedOptions = hidValue.split(",");
		for(var i in selectedOptions) {
			var optionVal = selectedOptions[i];

			$("#showHideCols").find("option[value="+optionVal+"]").prop("selected", "selected");
		}
		$("#showHideCols").multiselect('reload');
	}

	var hidValue = $("#hidSelectedBots").val();
	if(hidValue!=''){
		var selectedOptions = hidValue.split(",");
		for(var i in selectedOptions) {
			var optionVal = selectedOptions[i];

			$("#showHideBots").find("option[value="+optionVal+"]").prop("selected", "selected");
		}
		$("#showHideBots").multiselect('reload');
	}
	

	removeColumns();
	$("#showHideCols").on('change', function() {
		removeColumns();
		var showHideCols=$('#showHideCols').val();
		var website_id=$('#website_id').val();
		var search_id=$('#bot_name').val();
		var apiUrl='<?php echo site_url('admin/download/saveMyFilter'); ?>';
		apiUrl=apiUrl+"?showHideCols="+showHideCols+"&website_id="+website_id+"&search_id=0";
		$.ajax({
			url:apiUrl,
			method: 'get',				
			dataType: 'json',
			beforeSend: function() {
				$("#ajaxloader").html('<?php echo $this->config->item("spinner_code") ?>');
			},
			success: function(response){
				
				$("#ajaxloader").html(response.message);
									
			},
			fail: function(xhr, textStatus, errorThrown){
				//$("#postCodeHtml").html('Some error occured! Please try again.');
			}
		});
	});

	$("#showHideBots").on('change', function() {
		removeColumns();
	});

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
	$('#website_id').on('change', function() {
		var url='<?php echo site_url("admin/download"); ?>';
		url=url+"?website_id="+$('#website_id').val();		
		location.href=url;
	});
	$('#download').on('click', function() {
		var url='<?php echo site_url("admin/download/downloadCSV"); ?>';

		url=url+"?website_id="+$('#website_id').val();
		url=url+"&bot_name="+$('#showHideBots').val();
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
	$('#mybots').on('click', function() {
		var website_id=$('#website_id').val();
		var url='<?php echo site_url("admin/download/mybots"); ?>';
		url=url+'?wid='+website_id;
		location.href=url;
	});
	
	$("#savemyfilter").on('click', function() {
		return false;
		var showHideCols=$('#showHideCols').val();
		var website_id=$('#website_id').val();
		var search_id=$('#bot_name').val();
		var apiUrl='<?php echo site_url('admin/download/saveMyFilter'); ?>';
		apiUrl=apiUrl+"?showHideCols="+showHideCols+"&website_id="+website_id+"&search_id="+search_id;
		
		$.confirm({
			title: 'Warning!',
			content: 'You are going to save this filter',
			type: 'red',
			buttons: {					
				confirm: function () {						
					$.ajax({
						url:apiUrl,
						method: 'get',				
						dataType: 'json',
						beforeSend: function() {
							$("#ajaxloader").html('<?php echo $this->config->item("spinner_code") ?>');
						},
						success: function(response){
							
							$("#ajaxloader").html(response.message);
												
						},
						fail: function(xhr, textStatus, errorThrown){
							//$("#postCodeHtml").html('Some error occured! Please try again.');
						}
					}); 					
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
	$('.viewJob').on('click', function() {
		var jobIdArr=this.id.split('_');
		var jid=jobIdArr[1];
		var apiUrl='<?php echo site_url('admin/download/fetchDetails/') ?>';
		apiUrl=apiUrl+jid;
		$.ajax({
			url:apiUrl,
			method: 'get',				
			dataType: 'json',
			beforeSend: function() {
				$('#modal-body').html('<div class="spinner-border text-center" role="status">   <span class="sr-only text-center">Loading...</span></div>');
				$('#viewDetails').modal({
					show: true
				});	
			},
			success: function(response){
				createJobViewHtml(response);
			},
			fail: function(xhr, textStatus, errorThrown){
			}
		});
	});
});

function createJobViewHtml(response){
	//console.log(response);
	var htmlContent='';
	htmlContent +='<div class="table"><table class="table-sm table-hover table-bordered" cellspacing="0" width="100%" style="font-size: 13px;">';
	
	if(response.message !=''){		
		htmlContent +='<tr>';
		htmlContent +='<td colspan="2" class="font-weight-bold text-center">'+response.message+'</td>';
		htmlContent +='</tr>';
	}
	if(response.website_name !=''){		
		htmlContent +='<tr>';
		htmlContent +='<td class="font-weight-bold" width="20%">Website Name</td>';
		htmlContent +='<td>'+response.website_name+'</td>';
		htmlContent +='</tr>';
	}

	if(response.bot_name !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Bot Name</td>';
		htmlContent +='<td>'+response.bot_name+'</td>';
		htmlContent +='</tr>';
	}
	if(response.qualify !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Qualify</td>';
		htmlContent +='<td>'+response.qualify+'</td>';
		htmlContent +='</tr>';
	}
	if(response.name !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Name</td>';
		htmlContent +='<td>'+response.name+'</td>';
		htmlContent +='</tr>';
	}
	if(response.phone !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Phone</td>';
		htmlContent +='<td>'+response.phone+'</td>';
		htmlContent +='</tr>';
	}
	if(response.email !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Email</td>';
		htmlContent +='<td>'+response.email+'</td>';
		htmlContent +='</tr>';
	}
	if(response.job_url !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Post Url</td>';
		htmlContent +='<td><a target="_blank" href="'+response.job_url+'">'+response.job_url+'</a></td>';
		htmlContent +='</tr>';
	}

	if(response.title !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Post Title</td>';
		htmlContent +='<td class="wrapTd">'+response.title+'</td>';
		htmlContent +='</tr>';
	}
	if(response.keywords !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Keywords</td>';
		htmlContent +='<td class="wrapTd"> '+response.keywords+'</td>';
		htmlContent +='</tr>';
	}
	if(response.compensation !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Compensation</td>';
		htmlContent +='<td class="wrapTd"> '+response.compensation+'</td>';
		htmlContent +='</tr>';
	}
	if(response.applications !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Applications</td>';
		htmlContent +='<td class="wrapTd"> '+response.applications+'</td>';
		htmlContent +='</tr>';
	}
	if(response.posted_date !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Posted Date</td>';
		htmlContent +='<td class="wrapTd"> '+response.posted_date+'</td>';
		htmlContent +='</tr>';
	}
	if(response.scraped_date !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Scraped Date</td>';
		htmlContent +='<td class="wrapTd"> '+response.scraped_date+'</td>';
		htmlContent +='</tr>';
	}
	
	if(response.location !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Location</td>';
		htmlContent +='<td class="wrapTd"> '+response.location+'</td>';
		htmlContent +='</tr>';
	}
	if(response.organization !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Organization</td>';
		htmlContent +='<td class="wrapTd"> '+response.organization+'</td>';
		htmlContent +='</tr>';
	}
	if(response.employementType !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">EmployementType</td>';
		htmlContent +='<td class="wrapTd"> '+response.employementType+'</td>';
		htmlContent +='</tr>';
	}
	if(response.industries !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Industries</td>';
		htmlContent +='<td class="wrapTd"> '+response.industries+'</td>';
		htmlContent +='</tr>';
	}
	if(response.companyUrl !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Company Url</td>';
		htmlContent +='<td class="wrapTd"><a target="_blank" href="'+response.companyUrl+'">'+response.companyUrl+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.profile_url !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Profile Url</td>';
		htmlContent +='<td class="wrapTd"><a target="_blank" href="'+response.profile_url+'"> '+response.profile_url+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.twitterURL !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Twitter URL</td>';
		htmlContent +='<td class="wrapTd"> <a target="_blank" href="'+response.twitterURL+'">'+response.twitterURL+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.facebookURL !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Facebook URL</td>';
		htmlContent +='<td class="wrapTd"> <a target="_blank" href="'+response.facebookURL+'">'+response.facebookURL+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.jobFunction !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Job Function</td>';
		htmlContent +='<td class="wrapTd"> '+response.jobFunction+'</td>';
		htmlContent +='</tr>';
	}
	if(response.companyWebsite !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Company Website</td>';
		htmlContent +='<td class="wrapTd"> <a target="_blank" href="'+response.companyWebsite+'">'+response.companyWebsite+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.employees !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Employees</td>';
		htmlContent +='<td class="wrapTd"> '+response.employees+'</td>';
		htmlContent +='</tr>';
	}
	if(response.employeesOnLinkedin !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Employees On Linkedin</td>';
		htmlContent +='<td class="wrapTd"> '+response.employeesOnLinkedin+'</td>';
		htmlContent +='</tr>';
	}
	if(response.emails !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Emails</td>';
		htmlContent +='<td class="wrapTd"> '+response.emails+'</td>';
		htmlContent +='</tr>';
	}

	if(response.decision_maker !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Decision Maker</td>';
		htmlContent +='<td class="wrapTd"> '+response.decision_maker+'</td>';
		htmlContent +='</tr>';
	}

	if(response.description !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Description</td>';
		htmlContent +='<td class="wrapTd"> '+response.description+'</td>';
		htmlContent +='</tr>';
	}

	
	htmlContent +='</table>';
	htmlContent +='</div>';

	$('#modal-body').html(htmlContent);
	$('#viewDetails').modal({
		//show: true
	});	
}

function removeColumns(){
	
	$('li input[type="checkbox"]').each(function(){
		var shDiv=$(this).val();
		//alert(shDiv);
		if($(this).is(":checked")){
			$("."+shDiv).show();
		}else{
			$("."+shDiv).hide();
		}
	});
	
}
</script>