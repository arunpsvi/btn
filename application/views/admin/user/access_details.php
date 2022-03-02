
    <!-- Start Page Content -->

    <div class="row">
        <div class="col-lg-12">

            
           <div class="panel panel-info">
              <div class="panel-body table-responsive">				
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
                <form autocomplete="off" class="needs-validation" name='searchform' id='searchform' method='get' action="<?php echo $action; ?>">
					
					<div class="row">
					    <div class="col-md-2">
							<div class="form-group" id='qualifydropdown'>
								<label class="control-label">User</label>
								<?php echo form_dropdown('user_id', $usersArr, $user_id,'class="form-control" id="user_id"'); ?>
							</div>
						</div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-md-12" for="example-text">IP Address</label>
                                <input type="text" name="ip_address" class="form-control" value="<?php echo $ip_address; ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <br>
                            <button type="submit" class="btn btn-info btn-rounded btn-sm"> <i class="fa fa-search"></i>&nbsp;&nbsp;Search</button>
                            <button type="reset" class="btn btn-danger btn-rounded btn-sm" id='reset'> <i class="fa"></i>Reset</button>
                        </div>					
                            			
                    </div>			
				</form>
							<table class="table-sm table-hover table-bordered" cellspacing="0" width='100%'>
                            <thead>
                                <tr>
                                    <th width="30%">Name</th>
                                    <th>Login Date/Time</th>
                                    <th>IP Address</th>  
                                    <th>Action</th>  
                                 
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                   
                                </tr>
                            </tfoot>
                            
                            <tbody>
                            <?php  foreach ($userAccessDetails as $access_detail): ?>
                                <?php 
                                    $ci = get_instance();	
                                    $condition=Array();
                                    $condition['ip_address']=$access_detail['ip_address'];
                                    $ipID=$ci->db->get_where('ipblocklist',$condition)->row()->ip_id;
                                    $checked=' checked ';
                                    if(!empty($ipID)){
                                        $checked='';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $access_detail['first_name']." ".$access_detail['last_name']; ?></td>
                                    <td><?php echo $access_detail['login_datetime']; ?></td>
                                    <td><?php echo $access_detail['ip_address']; ?></td>
                                    <td>
                                        <input type="checkbox" <?php echo $checked; ?> class='ip_address' data-toggle="toggle" data-on="Open" data-off="Block" data-onstyle="success" data-offstyle="danger" data-size="mini" value="<?php echo $access_detail['ip_address']; ?>" ></td>
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

    <!-- End Page Content -->

    

<script>
$(document).ready(function(){

    $('#user_id').on('change', function() {
        $('#searchform').submit();
    });
    $('#reset').on('click', function() {
        location.href='<?php echo site_url('admin/user/userlog'); ?>';
    });
    
    var flag=1;
    $('.ip_address').on('change', function(e) {
        if(flag==1){
            var ipaddress=this.value;
            var thisObj=this;
            var block='';
            var message='';
            var apiUrl='<?php echo site_url('admin/user/blockUnblock'); ?>';
            var reLoadUrl='<?php echo $reLoadUrl; ?>';
            if($(this).prop('checked')==false){
                block='true';  
                message= 'You are going to block this IP ?';
            }else{
                block='false';  
                message= 'You are going to Unblock this IP ?';
            }
            $.confirm({
                title: 'Please Confirm!',
                content: message,
                type: 'red',
                buttons: {					
                    confirm: function () {
                        apiUrl=apiUrl+"?block="+block+"&ip_address="+ipaddress;
                        $.ajax({
                            url:apiUrl,
                            method: 'get',				
                            dataType: 'text',					
                            success: function(response){
                                location.href=reLoadUrl;
                            }
                        });
                    },
                    cancel: function () {
                        flag=0;	
                        if(block=='true'){
                            $(thisObj).bootstrapToggle('on');			
                        }else{
                            $(thisObj).bootstrapToggle('off');
                        }				
                        e.preventDefault();	   
                    }					
                }
            });
            
        }else{
            flag=1;	
        }
    });
});
</script>