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
                <div class="spinner spinner-border d-none" id='spinner' role="status" >
                    <span class="sr-only">Loading...</span>
                </div>
                <table class="table-sm table-hover table-bordered" cellspacing="0" width='100%'>
                    <thead>
                        <tr>
                            <th width="10%">Proxy</th>
                            <th>Craiglist</th>
                            <th>Linkedin</th>                                    
                            <th>Indeed</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>                                   
                        </tr>
                    </tfoot>
                    
                    <tbody>
                        <?php  foreach ($proxies as $proxy): ?>
                        <tr>                                  
                            <td>
                                <br>
                                <?php echo $proxy['ip'];?>                                
                                <br>
                                <br>
                                <?php if($proxy['status'] =='Y'){ ?>
                                    <a class="proxy_block" id="<?php echo $proxy['id'].'_N'; ?>" href="#" title="Block Proxy for all websites"><i class="fa fa-ban text-danger m-r-5"></i></a>
                                <?php }else{ ?>
                                    <a class="proxy_open" id="<?php echo $proxy['id'].'_Y'; ?>" href="#" title="Open Proxy for all websites"><i class="fa fa-check text-success m-r-5"></i></a>
                                <?php } ?>

                            </td>
                            
                            <td style='font-size:12px;'>
                                <?php $controller->getHtml($proxy,$proxyStatusData,1); ?>
                            </td>
                            <td style='font-size:12px;'>
                                <?php $controller->getHtml($proxy,$proxyStatusData,2); ?>
                            </td>
                            <td style='font-size:12px;'>
                                <?php $controller->getHtml($proxy,$proxyStatusData,3); ?>
                            </td>
                                              
                           <!-- <td class="text-nowrap">
                                <a href="<?php echo site_url('admin/proxy/update/'.$proxy['id']) ?>"><button type="button" title="Update" class="btn btn-success btn-circle btn-xs"><i class="fa fa-edit"></i></button></a>
                                <a href="#"><button type="button" title="Update" class="btn btn-danger btn-circle btn-xs deleteProxy" id='<?php echo $proxy['id']; ?>'><i class="fa fa-trash"></i></button></a>
                            </td>-->
                        </tr>
                    <?php endforeach ?>
                        <tr>
                            <td colspan='5'>
                                <div class="form-actions">
									<button type="button" id='chk_proxy' class="btn btn-rounded btn-sm btn-success"> <i class="fa fa-check"></i>&nbsp;Check Proxies</button>
                                    <!--<button type="button" class="btn btn-rounded btn-sm btn-success"> <i class="fa fa-download"></i>&nbsp;Download</button>-->
								</div>
                            </td>                            
                        </tr>
                    </tbody>
                </table>
            </div>					
        </div>
    </div>
</div>

</div>

<script>
$(document).ready(function() {
    $('.proxy_open,.proxy_block').on('click', function(e) {
        var id=$(this).attr('id');
        var pid=id.split('_');
        var apiUrl="<?php echo site_url('admin/proxy/blockUnblock'); ?>";
        apiUrl=apiUrl+"/"+pid[0]+'/'+pid[1];
        var message='';
        if(pid[1]=='N'){
            message="Are you sure to Block this proxy for all the websites?"
        }else{
            message="Are you sure to Open this proxy for all the websites?"
        }
		$.confirm({
			title: 'Warning!',
			content: message,
			type: 'red',
			buttons: {					
				confirm: function () {				
					$.ajax({
						url:apiUrl,
						method: 'get',				
						dataType: 'text',
						beforeSend: function() {
							$('#spinner').removeClass('d-none');
						},
						success: function(response){
                            $('#spinner').addClass('d-none');
                            if(response==1){
                                location.href="<?php echo site_url('admin/diagnostictools/proxy'); ?>";
                            }
						},
						fail: function(xhr, textStatus, errorThrown){
                            //console.log('Failed');
						}
					});
					
				},
				cancel: function () {
				}					
			}
		});
    });

    $('.proxy_open_website,.proxy_block_website').on('click', function(e) {
        var id=$(this).attr('id');
        var data=id.split('_');
        var apiUrl="<?php echo site_url('admin/diagnostictools/blockUnblock'); ?>";
        apiUrl=apiUrl+"/"+data[0]+'/'+data[1]+'/'+data[2];
        var message='';
        if(data[2]=='B'){
            message="Are you sure to Block this proxy for this website?";
        }else{
            message="Are you sure to Open this proxy for this website?";
        }

		$.confirm({
			title: 'Warning!',
			content: message,
			type: 'red',
			buttons: {					
				confirm: function () {				
					$.ajax({
						url:apiUrl,
						method: 'get',				
						dataType: 'text',
						beforeSend: function() {
							$('#spinner').removeClass('d-none');
						},
						success: function(response){
                            $('#spinner').addClass('d-none');
                            if(response==1){
                                location.href="<?php echo site_url('admin/diagnostictools/proxy'); ?>";
                            }
						},
						fail: function(xhr, textStatus, errorThrown){
                            //console.log('Failed');
						}
					});
					
				},
				cancel: function () {
				}					
			}
		});
    });

    $('#chk_proxy').on('click', function(e) {
		var apiUrl="<?php echo site_url('admin/diagnostictools/checkproxy'); ?>";;
		$.confirm({
			title: 'Warning!',
			content: 'And it take some times, so please wait.',
			type: 'red',
			buttons: {					
				confirm: function () {				
					$.ajax({
						url:apiUrl,
						method: 'get',				
						dataType: 'text',
						beforeSend: function() {
							$('#spinner').removeClass('d-none');
						},
						success: function(response){
							//location.href="<?php echo site_url('admin/proxy'); ?>";
                            $('#spinner').addClass('d-none');
						},
						fail: function(xhr, textStatus, errorThrown){
                            //console.log('Failed');
						}
					});
					
				},
				cancel: function () {
				}					
			}
		});	
	});
});
</script>

<style>
    .spinner {
  display: block;
  position: fixed;
  z-index: 1031; /* High z-index so it is on top of the page */
  top: 50%;
  right: 50%; /* or: left: 50%; */
  margin-top: -..px; /* half of the elements height */
  margin-right: -..px; /* half of the elements widht */
}
</style>