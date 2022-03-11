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
					<div class="form-body">
                        <h3 class="box-title">API details</h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label ">API Access</label>
                                    <input type="text" name="api_access" id="api_access" readonly class="form-control" value="
                                    <?php                                   
                                        if($api_access==0) {
                                            echo 'No';
                                        }else{
                                            echo 'Yes';
                                        }
                                    ?>" >
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label ">API Key</label>
                                    <input type="text" name="api_key" id="api_key" readonly value="
                                    <?php 
                                        if($api_access==1){
                                            if(!empty($api_key)) { 
                                                echo $api_key;
                                            }   
                                        }
                                       
                                    ?>" class="form-control" placeholder="">
                                </div>
                            </div>
                        <!--/row-->
                        </div>							
                    </div>					
					
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