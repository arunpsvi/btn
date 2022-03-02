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
					<form autocomplete="off" class="needs-validation" name='addKeywords' id='addKeywords' method='post' action="<?php if(!empty($action)){ echo $action; } ?>">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<input style="display:none" type="text" name="fakeusernameremembered"/>

						<div class="form-body">
							<h3 class="box-title">Manage Search</h3>
							<hr>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label class="control-label required"><b>Select website</b></label>
										<?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_id"'); ?>										
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label class="control-label required"><b>Search name</b></label>
										<input type="text" name="search_name" id="search_name" class="form-control" value="<?php if(!empty($search_name)) { echo $search_name; } ?>" autocomplete="<?php echo $autocomplete; ?>">									
									</div>
								</div>								
								
								<div class="col-md-4" id='div_subcategories'>
									<div class="form-group">
										<label class="control-label ">Scrape by category</label>
										<?php echo form_dropdown('subcategories[]', $subcategoriesArr, $subcategoriesArr,'multiple="multiple" class="3col active" id="subcategories"'); ?>    								
									</div>
								</div>
								<div class="col-md-2 " id='div_exact_match'><br>
									<div class="checkbox checkbox-info">
										<input name='exact_match' id="exact_match" type="checkbox" <?php if(!empty($exact_match)) { echo "checked"; } ?>>
										<label for="exact_match"><b>Exact match</b></label>								
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label class="control-label ">Posted start date</label>
										<input type="date" name="posted_date_start" id="posted_date_start" class="form-control my-datepicker" value="<?php if(!empty($posted_date_start)) { echo $posted_date_start; } ?>" autocomplete="<?php echo $autocomplete; ?>">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label class="control-label ">Posted end date</label>
										<input type="date" name="posted_date_end" id="posted_date_end" class="form-control my-datepicker" value="<?php if(!empty($posted_date_end)) { echo $posted_date_end; } ?>" autocomplete="<?php echo $autocomplete; ?>">
									</div>
								</div>
								<div class="col-md-4" id='div_useraccess'>
									<div class="form-group">
										<label class="control-label ">User Access</label>
										<?php echo form_dropdown('useraccess[]', $activeUsersArr, $activeUsersArr,'multiple="multiple" class="3col active" id="user_access"'); ?>    								
									</div>
								</div>
						
								<div class="col-md-6">
									<label class="control-label required">Keywords</label>
									<div class="tags-default">
										<input type="text" name='keywords' id='keywords' value="<?php if(!empty($keywords)){ echo $keywords; } ?>" data-role="tagsinput" placeholder="add tags" />
									</div>
								</div>
								<div class="col-md-4" id='div_useraccess'>
									<div class="form-group">
										<label class="control-label ">Choose Locations</label>
										<?php echo form_dropdown('locations[]', $locationsArr, $locationsArr,'multiple="multiple" class="3col active" id="location"'); ?>    								
									</div>
								</div>
								<div class="col-md-3">
									<div class="checkbox checkbox-info">
										<input name='load_keywords' id="load_keywords" type="checkbox" <?php if(!empty($loadDB)){ echo " checked "; } ?>>
										<label for="load_keywords"><b>Load keywords from database</b></label>								
									</div>
								</div>
																			
						</div>		
						
						<input type='hidden' name='search_id' id='search_id' value="<?php if(!empty($search_id)){ echo $search_id; } ?>" >
						<input type='hidden' name='hidSelectedOptions' id='hidSelectedOptions' value="<?php if(!empty($hidSelectedOptions)){ echo $hidSelectedOptions; } ?>" >
						<input type='hidden' name='hidSelectedUserAccess' id='hidSelectedUserAccess' value="<?php if(!empty($hidSelectedUserAccess)){ echo $hidSelectedUserAccess; } ?>" >
						<input type='hidden' name='hidSelectedLocations' id='hidSelectedLocations' value="<?php if(!empty($hidSelectedLocations)){echo $hidSelectedLocations; } ?>">
											
						<div class="form-actions m-t-10">
							
							<button type="submit" class="btn btn-rounded btn-sm btn-success" > <i class="fa fa-cog"></i>
							<?php if(!empty($search_id)){ 
								 echo '&nbsp;Update search'; 
								}else{ echo '&nbsp;Add search'; } 
							?>
							</button>
							<span>&nbsp;</span>
							<span id='scraping_message' class='text-warning font-weight-bold'></span>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!--./row-->
<script>
    $(function () {
        $('#subcategories').multiselect({
            columns: 2,
            placeholder: 'Select Categories',
            search: true,
            searchOptions: {
                'default': 'Search Categories'
            },
            selectAll: true
        });
		$('#user_access').multiselect({
            columns: 2,
            placeholder: 'User Access',
            search: true,
            searchOptions: {
                'default': 'Search Users'
            },
            selectAll: true
        });
		$('#location').multiselect({
            columns: 1,
            placeholder: 'location',
            search: true,
            searchOptions: {
                'default': 'Search Location'
            },
            selectAll: true
        });


    });
</script>	
<script>
	$(document).ready(function() {
		var hidValue = $("#hidSelectedOptions").val();
		if(hidValue!=''){
			var selectedOptions = hidValue.split(",");
			for(var i in selectedOptions) {
				var optionVal = selectedOptions[i];
				$("#subcategories").find("option[value="+optionVal+"]").prop("selected", "selected");
			}
			$("#subcategories").multiselect('reload');
		}
		var hidValue = $("#hidSelectedLocations").val();
		if(hidValue!=''){
			var selectedOptions = hidValue.split(",");
			for(var i in selectedOptions) {
				var optionVal = selectedOptions[i];
				$("#location").find("option[value="+optionVal+"]").prop("selected", "selected");
			}
			$("#location").multiselect('reload');
		}
		var hidValue = $("#hidSelectedUserAccess").val();
		if(hidValue!=''){
			var selectedOptions = hidValue.split(",");
			for(var i in selectedOptions) {
				var optionVal = selectedOptions[i];
				$("#user_access").find("option[value="+optionVal+"]").prop("selected", "selected");
			}
			$("#user_access").multiselect('reload');
		}
		
		var website_id=$("#website_id").val();
		if(website_id==1){
			//$("#div_exact_match").show();
			//$("#div_subcategories").show();
			//$("#div_posted_date").show();
		}else{
			//$("#div_exact_match").hide();
			//$("#div_subcategories").hide();
			//$("#div_posted_date").hide();
		}

		var uniqueCheckUrl="<?php echo site_url('admin/searches/checkunique'); ?>";
		uniqueCheckUrl = uniqueCheckUrl+"?search_id="+ $("#search_id").val();
		$("#addKeywords").validate({	
			rules: {
				search_name: {
					"required":true,
					"remote":uniqueCheckUrl
				}
			},
			messages: {
				search_name: {
					"required":"Please fill the Search name",
					"remote":"Search name already exists"
				},		
			}
		});

		$("#load_keywords").click(function(){	
			if($('#load_keywords').is(":checked")){
				var website_id=$("#website_id").val();
				var url ='<?php echo $action; ?>';
				url=url+"?website_id="+website_id+"&loadDB=1";
				if($('#search_name').val()!=''){
					url=url+"&sn="+$('#search_name').val();
				}
				location.href=url;
			}else{
				var website_id=$("#website_id").val();
				var url ='<?php echo $action; ?>';
				url=url+"?website_id="+website_id;
				if($('#search_name').val()!=''){
					url=url+"&sn="+$('#search_name').val();
				}
				location.href=url;
			}
		});
		var myVarTimer;
		var js_array =<?php echo json_encode($websiteListNameArr); ?>;
		$("#website_id").change(function(){		
			var website_id=$("#website_id").val();
			/*if(website_id==1){
				$("#div_exact_match").show();
				$("#div_subcategories").show();
				$("#div_posted_date").show();
			}else{
				$("#div_exact_match").hide();
				$("#div_subcategories").hide();
				$("#div_posted_date").hide();
			}*/
			var url='<?php echo site_url('admin/searches'); ?>';
			url =url+"?website_id="+website_id;
			location.href=url;


			/*$("#keywords").html('');
			var apiUrl='<?php echo site_url('admin/common/getKeywords'); ?>';
			apiUrl=apiUrl+"?website_id="+website_id;

			$.ajax({
				url:apiUrl,
				method: 'get',				
				dataType: 'text',
				success: function(response){
					$("#keywords").html(response);
				},
			});	*/		
		});	
		/*$("#startScraping").click(function(){
			var exact_match='N';
			if($('#exact_match').is(":checked")){
				exact_match='Y';
			}
			var website_id=$("#website_id").val();
			var posted_date=$("#posted_date").val();
			var controller_name=js_array[website_id];
			var subcategories = $('#subcategories option:selected').toArray().map(item => item.value).join();
			var apiUrl='<?php echo site_url(''); ?>';
			apiUrl =apiUrl+"/"+controller_name+"?website_id="+website_id+'&mak='+exact_match+'&subcategories='+subcategories+'&posted_date='+posted_date;	
			

			
			$.ajax({
				url:apiUrl,
				method: 'get',				
				dataType: 'text',				
				beforeSend: function(response){
					myVarTimer = setInterval("executeQuery()", 6000);
					$("#startScraping").prop('disabled', true);	
					$("#scraping_message").removeClass('text-success');	
					$("#scraping_message").addClass('text-warning');	
					$("#scraping_message").html('Scraping started...');	
				},
				success: function(response){
					$("#startScraping").prop('disabled', false);
					$("#scraping_message").addClass('text-success');	
					$("#scraping_message").removeClass('text-warning');	
					$("#scraping_message").html('Scraping Completed...');	
					clearTimeout(myVarTimer);
					//$("#keywords").html(response);

				},
			});	
		});*/
		
	}); 

function executeQuery() {

	var website_id=$("#website_id").val();
	var apiUrl='<?php echo base_url(); ?>';
	apiUrl=apiUrl+"progress.php?website_id="+website_id;

	$.ajax({
		url: apiUrl,
		success: function(response) {
			$("#scraping_message").removeClass('text-success');	
			$("#scraping_message").addClass('text-warning');	
			$("#scraping_message").html(response);	
		}
	});
	//myVarTimer=setTimeout(executeQuery, 6000);
}

</script>