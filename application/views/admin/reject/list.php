
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

                    <form autocomplete="off" class="needs-validation" name='searchResult' id='searchResult' method='get' action="<?php echo $action; ?>">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label required">Select Website</label>
                                        <?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_id"'); ?>
                                    </div>
                                </div>
                            </div>
                                
                        </form>
							<table class="table-sm table-hover table-bordered" cellspacing="0" width='100%'>
                            <thead>
                                <tr>
                                    <th>Website</th>
                                    <th>Company Name</th>
                                    <th>Profile Url</th>                                    
                                    <th>Website Url</th>                                    
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                   
                                </tr>
                            </tfoot>
                            
                            <tbody>
                            <?php  foreach ($companies as $company): ?>
                               
                                <tr>
                                    <td><?php echo $company['website_name']; ?></td>
                                    <td><?php echo $company['company_name']; ?></td>
                                    <td> <a href='<?php echo $company['companyUrl']; ?>' target='_blank'><?php echo $company['companyUrl']; ?></a></td>
                                    <td> <a href='<?php echo $company['website_url']; ?>' target='_blank'><?php echo $company['website_url']; ?></a></td>
                                    <td class="text-nowrap">
										<a href="<?php echo site_url('admin/reject/update/'.$company['reject_id']) ?>"><button type="button" title="Update" class="btn btn-success btn-circle btn-xs"><i class="fa fa-edit"></i></button></a>
										<a href="#"><button type="button" title="Delete" class="btn btn-danger btn-circle btn-xs deleteCompany" id='<?php echo $company['reject_id']; ?>'><i class="fa fa-trash"></i></button></a>
									</td>
								</tr>
							<?php endforeach ?>

                            </tbody>


                        </table>
                    </div>
                    <?php echo $pagination; ?>		
					
            </div>
        </div>
    </div>

 </div>

<script>
$(document).ready(function() {	
    $('.deleteCompany').on('click', function(e) {
        var apiUrl="<?php echo site_url('admin/reject/delete/'); ?>";;
        apiUrl=apiUrl+this.id;

        $.confirm({
            title: 'Warning!',
            content: 'Company will be deleted from reject list',
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
                            location.href="<?php echo site_url('admin/reject'); ?>";
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
    $('#website_id').on('change', function(e) {
        $('#searchResult').submit();
    });
});
</script>
<!-- End Page Content -->