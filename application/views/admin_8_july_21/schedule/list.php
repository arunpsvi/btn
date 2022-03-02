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
			<div class="panel-body table-responsive ">	
			<h3 class="box-title">Scrape Data</h3>
				<hr>
			<div class="panel-wrapper collapse in" aria-expanded="true">
				<form method='post'>
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

				<input type='hidden' name='website_id' value='<?php echo $keywordData->website_id; ?>'>
				<input type='hidden' name='keyword_id' value='<?php echo $keywordData->keyword_id; ?>'>

				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label ">Website</label>
							<select name="website" class="form-control" id="website" disabled>
								<option><?php echo $keywordData->website_name; ?></option>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label required">Search Name</label>
							<select name="keyword" class="form-control" id="keyword" disabled>
								<option><?php echo $keywordData->search_name; ?></option>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label required">Start date</label>
							<input type="date" name="start_date" id="start_date" class="form-control my-datepicker" value="" autocomplete="">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label required">Number of days</label>
							<select name="numdays" class="form-control" id="numdays">
								<?php for($i=1; $i<100; $i++){ 
									if($i>60){
										$i=$i+14;
									}
									elseif($i>10){
										$i=$i+4;
									}
								?>
								<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label required">Time to start</label>
							<select name="start_time" class="form-control" id="start_time">
								<?php for($i=0; $i<24; $i++){ 
									$suffix='';
									if($i<12){
										$suffix=' AM';
									}else{
										$suffix=' PM';
									}
								?>
								<option value="<?php echo $i; ?>"><?php echo $i.$suffix; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">					
					<div class="col-md-1">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" name="monday" id="monday">
							<label class="form-check-label m-l-20" for="monday">Monday</label>
						</div>
					</div>
					<div class="col-md-1">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="tuesday" name="tuesday">
							<label class="form-check-label m-l-20" for="tuesday">Tuesday</label>
						</div>
					</div>
					<div class="col-md-1">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="wednesday" name="wednesday">
							<label class="form-check-label m-l-20" for="wednesday">Wednesday</label>
						</div>
					</div>
					<div class="col-md-1">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="thursday" name="thursday">
							<label class="form-check-label m-l-20" for="thursday">Thursday</label>
						</div>
					</div>
					<div class="col-md-1">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="friday" name="friday">
							<label class="form-check-label m-l-20" for="friday">Friday</label>
						</div>
					</div>
					<div class="col-md-1">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="saturday" name="saturday">
							<label class="form-check-label m-l-20" for="saturday">Saturday</label>
						</div>
					</div>
					<div class="col-md-1">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="sunday" name="sunday">
							<label class="form-check-label m-l-20" for="sunday">Sunday</label>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="alldays" name="alldays">
							<label class="form-check-label m-l-20" for="alldays">All days</label>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-check">
							<button type="submit" class="btn btn-rounded btn-sm btn-success" >Create schedule</button>
						</div>
					</div>
					
				</div>
				</form>
				<p id='scraping_message' class="text-center font-weight-bold"></p>
				<table class="table-sm table-hover table-bordered" cellspacing="0" width='100%'>
						<thead >
							<tr  class="bg-info">                                   
								<th width='9%' class='font-weight-bold text-white'>Start date</th> 
								<th  class='font-weight-bold text-white'>No. of days</th>
								<th class='font-weight-bold text-white'>Start time</th> 
								<th class='font-weight-bold text-white'>Days</th> 								
													
								<th width='10%' class='font-weight-bold text-white'>Action</th> 								
							</tr>
						</thead>
						<tbody>
						<?php
							$ci = get_instance();
							
						?>
						<?php foreach ($schedules as $schedule){

						if($search['exact_match']=='on'){
							$search['exact_match']='Yes';
						}else{
							$search['exact_match']='No';
						}

						$search['keywords']=preg_replace('/,/is',', ',$search['keywords']);
						$website=$ci->db->get_where('svi_websites',array('id'=>$search['website_id']))->row()->name;
						$subCategories=$search['subcategories'];
						$subCategories=preg_replace('/,/is',"','",$subCategories);
						$subCategories ="'$subCategories'";
						
						$categoryData=$ci->common_model->get_subcategories($subCategories);
						$subCategoriesTxt='';
						foreach ($categoryData as $category){
							if(empty($subCategoriesTxt)){
								$subCategoriesTxt .=$category['sub_category_name'];
							}else{
								$subCategoriesTxt .=','.$category['sub_category_name'];
							}
						}
						
						?>
						<tr >                                   
							<td><?php echo $schedule['start_date']; ?></td>
							<td><?php echo $schedule['scheduled_time']; ?></td>
							<td>
								<?php 
									$suffix='';
									if($schedule['time_to_start']<12){
										$suffix=' AM';
									}else{
										$suffix=' PM';
									}
									echo $schedule['time_to_start']." ".$suffix; 
								?>
							</td>
							<td>
								<?php 
									$daysStr='';
									if($schedule['monday']=='Y'){
										$daysStr.="Monday, ";
									}if($schedule['tuesday']=='Y'){
										$daysStr.= "Tuesday, ";
									}if($schedule['wednesday']=='Y'){
										$daysStr.= "Wednesday, ";
									}if($schedule['thursday']=='Y'){
										$daysStr.= "Thursday, ";
									}if($schedule['friday']=='Y'){
										$daysStr.= "Friday, ";
									}if($schedule['saturday']=='Y'){
										$daysStr.= "Saturday, ";
									}if($schedule['sunday']=='Y'){
										$daysStr.= "Sunday, ";
									}
									echo rtrim($daysStr, ', ');
								?>
							</td>
							
							<td class='hide_on_run'>
								<a class='update_search' href='#' id="<?php echo site_url('admin/schedule/view/'.$search['id']) ?>" title="Update Search"><i class="fa fa-eye icon-blue m-r-5"></i></a>
								<a class='delete_search' id="<?php echo site_url('admin/schedule/deleteSchedule/'.$schedule['id'].'/'.$schedule['keyword_id']) ?>" href="#" title="Delete Search"><i class="fa fa-trash icon-blue m-r-5"></i></a>
							</td>								
						</tr>
						<?php } ?>
							<input type='hidden' name='search_id' id='search_id' value=''>
						</tbody>
				</table>
				
                </div>
				<?php echo $pagination; ?>	
			</div>
		</div>
	</div>
</div>
<!--./row-->
<script>
    $(function () {
        $('select[multiple].active.3col').multiselect({
            columns: 2,
            placeholder: 'Select Categories',
            search: true,
            searchOptions: {
                'default': 'Search Categories'
            },
            selectAll: true
        });

    });
</script>	
<script>
	$(document).ready(function() {	

		var myVarTimer;
		var js_array =<?php echo json_encode($websiteListNameArr); ?>;
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
					$("#keywords").html(response);
				},
			});			
		});	
		$("#alldays").click(function(){
			if($(this).prop("checked")==true){
				$("#monday").prop("checked",true);	
				$("#tuesday").prop("checked",true);	
				$("#wednesday").prop("checked",true);	
				$("#thursday").prop("checked",true);	
				$("#friday").prop("checked",true);	
				$("#saturday").prop("checked",true);	
				$("#sunday").prop("checked",true);	
			}else{
				$("#monday").prop("checked",false);	
				$("#tuesday").prop("checked",false);	
				$("#wednesday").prop("checked",false);	
				$("#thursday").prop("checked",false);	
				$("#friday").prop("checked",false);	
				$("#saturday").prop("checked",false);	
				$("#sunday").prop("checked",false);	
			}
		});

		$(".run_scraper").click(function(){
			var statusVar='status_'+this.id;
			var apiUrl='<?php echo site_url(''); ?>';
			apiUrl =apiUrl+"/"+this.id;	
			//alert(apiUrl);
			var pieces = this.id.split("?sid=");
			var search_id=pieces[1];
			$("#search_id").val(search_id);
			$.confirm({
				title: 'Warning!',
				content: 'You are going to run the scraper. Once started you will not be able to open other window.',
				type: 'red',
				buttons: {					
					confirm: function () {						
						
						$.ajax({
							url:apiUrl,
							method: 'get',				
							dataType: 'text',				
							beforeSend: function(response){
								myVarTimer = setInterval("executeQuery()", 6000);
								$(".hide_on_run").hide();								
								$("#scraping_message").removeClass('text-success');	
								$("#scraping_message").addClass('text-warning');	
								$("#scraping_message").html('Scraping started...');	
							},
							success: function(response){
								$(".hide_on_run").show();
								$("#scraping_message").removeClass('text-warning');	
								$("#scraping_message").addClass('text-success');					
								clearTimeout(myVarTimer);
								executeQuery();
							},
						});	
						
					},
					cancel: function () {
						
					}					
				}
			});				
		});

		$('.delete_search').on('click', function(e) {
			var apiUrl=this.id;
			$.confirm({
				title: 'Warning!',
				content: 'Your search will be deleted',
				type: 'red',
				buttons: {					
					confirm: function () {						
						
						$.ajax({
							url:apiUrl,
							method: 'get',				
							dataType: 'text',
							beforeSend: function() {
								
							},
							success: function(response){
								location.href=response;
							},
							fail: function(xhr, textStatus, errorThrown){
							}
						});
						
					},
					cancel: function () {
					}					
				}
			});	
		});

		$('.update_search').on('click', function(e) {
			var accessUrl=this.id;
			$.confirm({
				title: 'Warning!',
				content: 'Your going to update search terms',
				type: 'red',
				buttons: {					
					confirm: function () {						
						location.href=accessUrl;						
					},
					cancel: function () {
					}					
				}
			});	
		});
		$('.schedule_cal').on('click', function(e) {
			var accessUrl=this.id;
			$.confirm({
				title: 'Warning!',
				content: 'Your going to update schedule',
				type: 'red',
				buttons: {					
					confirm: function () {						
						location.href=accessUrl;						
					},
					cancel: function () {
					}					
				}
			});	
		});		
		
	}); 

function executeQuery() {

	var search_id=$("#search_id").val();
	var apiUrl='<?php echo base_url(); ?>';
	apiUrl=apiUrl+"progress.php?search_id="+search_id;

	$.ajax({
		url: apiUrl,
		success: function(response) {
			$("#scraping_message").removeClass('text-success');	
			$("#scraping_message").addClass('text-warning');	
			$("#scraping_message").html(response);	
		}
	});
	
}

</script>