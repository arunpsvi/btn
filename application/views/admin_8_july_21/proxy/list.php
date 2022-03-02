
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
							<table class="table-sm table-hover table-bordered" cellspacing="0" width='100%'>
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Password</th>
                                    <th>IP</th>                                    
                                    <th>Port</th>
                                   	<th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                   
                                </tr>
                            </tfoot>
                            
                            <tbody>
                            <?php  foreach ($proxies as $proxy): ?>
                                <?php 
									$user['created_at']='';

									
									
								?>
                                <tr>
                                    <td><?php echo $proxy['uname']; ?></td>
                                    <td><?php echo $proxy['password']; ?></td>
                                    <td><?php echo $proxy['ip']; ?></td>
                                    <td><?php echo $proxy['port']; ?></td>
                                    <td>
                                        <?php if ($proxy['status'] == 'Y'): ?>
                                            <div class="label label-table label-success">Active</div>
                                        <?php else: ?>
                                            <div class="label label-table label-danger">Inactive</div>
                                        <?php endif ?>
                                    </td>
									<td class="text-nowrap">
										<a href="<?php echo site_url('admin/proxy/update/'.$proxy['id']) ?>"><button type="button" title="Update" class="btn btn-success btn-circle btn-xs"><i class="fa fa-edit"></i></button></a>
										<a href="#"><button type="button" title="Update" class="btn btn-danger btn-circle btn-xs deleteProxy" id='<?php echo $proxy['id']; ?>'><i class="fa fa-trash"></i></button></a>
									</td>
								</tr>
							<?php endforeach ?>

                            </tbody>


                        </table>
                    </div>
					
					
            </div>
        </div>
    </div>

 </div>

<script>
$(document).ready(function() {	
$('.deleteProxy').on('click', function(e) {
		var apiUrl="<?php echo site_url('admin/proxy/delete/'); ?>";;
		apiUrl=apiUrl+this.id;

		$.confirm({
			title: 'Warning!',
			content: 'Proxy will be deleted',
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
							location.href="<?php echo site_url('admin/proxy'); ?>";
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
});
</script>

    <!-- End Page Content -->