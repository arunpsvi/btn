
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
                                    <th>Bot ID</th>
                                    <th>Website</th>
                                    <th>Bot Name</th>                                    
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                   
                                </tr>
                            </tfoot>
                            
                            <tbody>
                            <?php  foreach ($bots as $bot): ?>
                                <?php 
									$ci = get_instance();	
                                    
								?>
                                <tr>
                                    <td><?php echo $bot['keyword_id']; ?></td>
                                    <td><?php echo $websiteListArr[$bot['website_id']]; ?></td>
                                    <td><?php echo $bot['search_name']; ?></td>                                   
								
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