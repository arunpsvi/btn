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
			<h3 class="box-title">Manage Bots</h3>
				<hr>
			<div class="panel-wrapper collapse in" aria-expanded="true">
				
				<p id='scraping_message' class="text-center font-weight-bold"></p>
				<table class="table-sm table-hover table-bordered text-nowrap" cellspacing="0" width='100%'>
						<thead >
							<tr  class="bg-info">                                   
								<th width='9%' class='font-weight-bold text-white'>Created date</th> 
								<th width='14%' class='font-weight-bold text-white'>Next scheduled date</th> 
								<th width='10%' class='font-weight-bold text-white'>Status / Time</th> 
								<th class='font-weight-bold text-white'>Website</th>

								<th  class='font-weight-bold text-white'>Bot Name</th>
								<!--<th class='font-weight-bold text-white'>Search keywords</th> 
								<th class='font-weight-bold text-white'>Categories</th> 								
								 
								<th class='font-weight-bold text-white'>Exact match</th> -->
								<th class='font-weight-bold text-white'>Post start date</th> 
								<th class='font-weight-bold text-white'>Post end date</th> 
								<th class='font-weight-bold text-white'>Total records</th> 								
								<th width='10%' class='font-weight-bold text-white'>Action</th> 								
							</tr>
						</thead>
						<tbody>
						<?php
							$ci = get_instance();
							
						?>
						<?php foreach ($searches as $search){
							
							$ci = get_instance();
							$searchData['keyword_id']=$search['keyword_id'];
							$resultStatus=$ci->common_model->getLastScrapeStatus($searchData);
							
							if($search['exact_match']=='on'){
								$search['exact_match']='Yes';
							}else{
								$search['exact_match']='No';
							}

							$search['keywords']=preg_replace('/,/is',', ',$search['keywords']);
							$website=$ci->db->get_where('svi_websites',array('id'=>$search['website_id']))->row()->name;
							$websiteUrl=$ci->db->get_where('svi_websites',array('id'=>$search['website_id']))->row()->url;
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
							
							$condition=Array(
								'keyword_id'=>$search['keyword_id']
								);
							$scheduleRecord=$ci->common_model->get_latest_schedule($condition);
						
						?>
						<tr >                                   
							<td><?php echo $search['created_date']; ?></td>
							<td><?php echo $scheduleRecord->scheduled_time; ?></td>
							<?php if($resultStatus->status=='C'){ ?>
								<td class="label label-table label-success" id="status_<?php echo $search['keyword_id']; ?>">Completed <?php echo $resultStatus->completion_time; ?></td>
							<?php }else if($resultStatus->status=='S'){ ?>
								<td class="label label-table label-danger" id="status_<?php echo $search['keyword_id']; ?>">Running <?php echo $resultStatus->start_time; ?></td>
							<?php }else{
								echo "<td id='status_".$search['keyword_id']."'>&nbsp;</td>";
							}?>
							<td><?php echo $websiteUrl; ?></td>
							
							<td><?php echo $search['search_name']; ?></td>
							<!--<td><?php echo $search['keywords']; ?></td>
							<td><?php echo $subCategoriesTxt; ?></td>
														
							<td><?php echo $search['exact_match']; ?></td>-->
							<td><?php if($search['posted_date_start'] != '0000-00-00'){ echo $search['posted_date_start']; } ?></td>
							<td><?php if($search['posted_date_end'] != '0000-00-00'){ echo $search['posted_date_end']; } ?></td>
							<td>
							<?php 
							$condition=array(
								"search_id"=>$search['keyword_id']
							);
							echo $ci->bot_model->countResults($condition)->total; ?></td>
							<td class='hide_on_run'>
								<a class='<?php if ($resultStatus->status !='S'){ echo "run_scraper";} ?>' id='<?php echo $website.'?sid='.$search['keyword_id']; ?>' href="#" title="<?php if ($resultStatus->status!='S'){ echo "Run Scraper"; } else{ echo "Scraping is already in progress" ;} ?>"><i class="fa fa-gear <?php if ($resultStatus->status =='S'){echo " text-danger "; }else{ echo " icon-blue ";} ?> m-r-5"></i></a>
								<?php if(in_array($this->session->userdata('role'), $this->config->item('salesAccess'))){ ?>
									<a class='update_search' href='#' id="<?php echo site_url('admin/bots/update/'.$search['keyword_id']) ?>" title="Update Bot"><i class="fa fa-pencil icon-blue m-r-5"></i></a>
									<a class='delete_search' id="<?php echo site_url('admin/searches/deleteSearch/'.$search['keyword_id']) ?>" href="#" title="Delete Search"><i class="fa fa-trash icon-blue m-r-5"></i></a>								
									<a class='schedule_cal' id='<?php echo site_url('admin/schedule/addSchedule/'.$search['keyword_id']) ?>' href="#" title="Schedule"><i class="fa fa-calendar icon-blue m-r-5"></i></a>
									<a class='view_details' id='<?php echo site_url('admin/bots/fetchDetails/'.$search['keyword_id']) ?>' href="#" title="View Details"><i class="fa fa-eye icon-blue m-r-5"></i></a>
									<?php if(!empty($search['favorite_id'])){
										?>
										<a class='unfavourite' id='<?php echo "fav##".$search['keyword_id']; ?>' href="#" title="Remove from favourite"><i class="fa fa-star text-warning m-r-5"></i></a>									
										<?php
									}else{ ?>
										<a class='favourite' id='<?php echo "fav##".$search['keyword_id']; ?>' href="#" title="Add to favourite"><i class="fa fa-star icon-blue m-r-5"></i></a>
									<?php } ?>
								<?php } ?>
								
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
		$(".run_scraper").click(function(){
			var statusVar='status_'+this.id;
			var apiUrl='<?php echo site_url(''); ?>';
			apiUrl =apiUrl+"/"+this.id;	
			/* alert(apiUrl);
			return false; */
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
								$("#status_"+search_id).addClass('label label-table label-danger');	
								$("#status_"+search_id).removeClass('label-success');	
								$("#status_"+search_id).html('Running');
							},
							success: function(response){
								$(".hide_on_run").show();
								$("#scraping_message").removeClass('text-warning');	
								$("#scraping_message").addClass('text-success');	
								$("#status_"+search_id).addClass('label label-table label-danger');	
								$("#status_"+search_id).removeClass('label-danger');
								$("#status_"+search_id).addClass('label-success');	
								$("#status_"+search_id).html('Completed');
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

		$(".favourite").click(function(){
			var data = this.id.split("##");
			var apiUrl='<?php echo site_url('admin/scrape'); ?>';
			apiUrl =apiUrl+"/favourite/"+data[1];	
			$.confirm({
				title: 'Warning!',
				content: 'You are going to add this bot to favourite',
				type: 'red',
				buttons: {					
					confirm: function () {					
						$.ajax({
							url:apiUrl,
							method: 'get',				
							dataType: 'text',				
							beforeSend: function(response){
							},
							success: function(response){
								location.href="<?php echo site_url('admin/bots'); ?>";
							},
						});	
						
					},
					cancel: function () {
						
					}					
				}
			});	
		});
		$(".unfavourite").click(function(){
			var data = this.id.split("##");
			var apiUrl='<?php echo site_url('admin/scrape'); ?>';
			apiUrl =apiUrl+"/unfavourite/"+data[1];	
			$.confirm({
				title: 'Warning!',
				content: 'You are going to remove this bot from favourite',
				type: 'red',
				buttons: {					
					confirm: function () {					
						$.ajax({
							url:apiUrl,
							method: 'get',				
							dataType: 'text',				
							beforeSend: function(response){
							},
							success: function(response){
								location.href="<?php echo site_url('admin/bots'); ?>";
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
								location.href="<?php echo site_url('admin/bots'); ?>";
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

		$('.view_details').on('click', function() {
			var jid=this.id;
			var apiUrl=jid;
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

	if(response.subcategories !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Sub Categories</td>';
		htmlContent +='<td>'+response.subcategories+'</td>';
		htmlContent +='</tr>';
	}
	if(response.keywords !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Keywords</td>';
		htmlContent +='<td>'+response.keywords+'</td>';
		htmlContent +='</tr>';
	}
	if(response.bot_name !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Bot Name</td>';
		htmlContent +='<td>'+response.bot_name+'</td>';
		htmlContent +='</tr>';
	}
	if(response.locations !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Locations</td>';
		htmlContent +='<td>'+response.locations+'</td>';
		htmlContent +='</tr>';
	}
	if(response.posted_date_start !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Post Date Start</td>';
		htmlContent +='<td>'+response.posted_date_start+'</td>';
		htmlContent +='</tr>';
	}
	if(response.posted_date_end !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Post Date End</td>';
		htmlContent +='<td>'+response.posted_date_end+'</td>';
		htmlContent +='</tr>';
	}
	if(response.exact_match !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Exact Match</td>';
		htmlContent +='<td>'+response.exact_match+'</td>';
		htmlContent +='</tr>';
	}

	if(response.next_schedule_time !='' && response.next_schedule_time !=null){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Next Scheduled Time</td>';
		htmlContent +='<td class="wrapTd">'+response.next_schedule_time+'</td>';
		htmlContent +='</tr>';
	}
	if(response.created_date !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Created On</td>';
		htmlContent +='<td class="wrapTd"> '+response.created_date+'</td>';
		htmlContent +='</tr>';
	}

	
	htmlContent +='</table>';
	htmlContent +='</div>';

	$('#modal-body').html(htmlContent);
	$('#viewDetails').modal({
		//show: true
	});	
}

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