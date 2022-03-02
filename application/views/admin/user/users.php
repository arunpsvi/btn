
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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>                                    
                                    <th>Role</th>
                                   	<th>Login Status</th>
                                   	<th>Email Notification</th>
                                   	<th width="25%">Assigned Bots</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                   
                                </tr>
                            </tfoot>
                            
                            <tbody>
                            <?php  foreach ($users as $user): ?>
                                <?php 
									$user['created_at']='';
                                    $ci = get_instance();	
                                    $searchList=$ci->search_model->get_assigned_searches($user['user_id']);							
                                    $botNames='';
                                    foreach ($searchList as $searchData){
                                        $url=site_url('/admin/searches/update/').$searchData['search_id'];
                                        $botNames .='<a target="_blank" href="'.$url.'">'.$searchData['search_name']."</a>, ";
                                    }
                                    $botNames=rtrim($botNames, ', ');
								?>
                                <tr>
                                    <td><?php echo $user['first_name']." ".$user['last_name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['mobile']; ?></td>
									<td width="10%">
                                        <?php if ($user_roles[$user['role']] == 'Admin'): ?>
                                            <div class="label label-table label-info"><i class="fa fa-user"></i><?php echo $user_roles[$user['category']]; ?></div>
                                        <?php else: ?>
                                            <div class="label label-table label-success"><?php echo $user_roles[$user['category']]; ?></div>
                                        <?php endif ?>
                                    </td>
									
                                    <td>
                                        <?php if ($user['status'] == 0): ?>
                                            <div class="label label-table label-danger">Inactive</div>
                                        <?php else: ?>
                                            <div class="label label-table label-success">Active</div>
                                        <?php endif ?>
                                    </td>

									<td>
                                        <?php if ($user['email_notification'] == 1): ?>
                                            <div class="label label-table label-danger">Yes</div>
                                        <?php else: ?>
                                            <div class="label label-table label-success">No</div>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php echo  $botNames;?>
                                    </td>
									
                                    

		<td class="text-nowrap">                                 

			<a href="<?php echo site_url('admin/user/update/'.$user['user_id']) ?>"><button type="button" title="Update" class="btn btn-success btn-circle btn-xs"><i class="fa fa-edit"></i></button></a>
			<a href="<?php echo site_url('admin/user/changepassword/'.$user['user_id']) ?>"><button type="button" title="Update" class="btn btn-success btn-circle btn-xs"><i class="fa fa-key"></i></button></a>

			
                                        
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

    <!-- End Page Content -->