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
                            <th>Website</th>
                            <th>Bot Name</th>
                            <th>Scheduled At</th>                                    
                            <th>Started At</th>
                            <th>Last Activity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>                                   
                        </tr>
                    </tfoot>
                    
                    <tbody>
                        <?php  foreach ($schedules as $schedule): ?>
                        <tr>  
                            <td>
                                <?php echo $schedule['website_name']; ?>
                            </td>                                
                            <td>
                                <?php echo $schedule['bot_name']; ?>
                            </td>
                            
                            <td>
                                <?php echo $schedule['scheduled_time']; ?>
                            </td>
                            <td>
                                <?php echo $schedule['start_time']; ?>
                            </td>
                            <td>
                                <?php echo $schedule['last_updated']; ?>
                            </td>
                            
                            <?php if($schedule['status']=='C'){ ?>
								<td class="label label-table label-success" id='status_<?php echo $schedule['schedule_id']; ?>'>Completed <i><?php echo $schedule['completion_time']; ?></i></td>
							<?php }else if($schedule['status']=='S'){ ?>
								<td class="label label-table label-danger" id='status_<?php echo $schedule['schedule_id']; ?>'>Running <i><?php echo $schedule['start_time']; ?></i></td>
							<?php }else if($schedule['status']=='T'){ ?>
								<td class="label label-table label-warning" id='status_<?php echo $schedule['schedule_id']; ?>'>Reset at <i><?php echo $schedule['completion_time']; ?></i></td>
							<?php }else if($schedule['status']=='P'){ ?>
								<td style='font-size:10px;' class='font-weight-bold'  id='status_<?php echo $schedule['schedule_id']; ?>'>Scheduled at <i><?php echo $schedule['scheduled_time']; ?> </i></td>
							<?php }else if($schedule['status']=='F'){ ?>
								<td class="label label-table label-danger" id='status_<?php echo $schedule['schedule_id']; ?>'>Failed <i><?php echo $schedule['start_time']; ?></i></td>
							<?php }else{
								echo "<td id='status_".$schedule['schedule_id']."'>&nbsp;</td>";
							}?>
                            <?php
                                $minutes=0;
                                if($schedule['status']=='S'){
                                    $dateDiff=date_diff(date_create($schedule['last_updated']),date_create(date('Y-m-d H:i:s')));
                                    $minutes = $dateDiff->days * 24 * 60;
                                    $minutes += $dateDiff->h * 60;
                                    $minutes += $dateDiff->i;
                                }
                            ?>
                            <td class='text-nowrap'>
								<a class='view_log' href='#' id="view_<?php echo $schedule['schedule_id'];?>" title="View Details"><i class="fa fa-eye icon-blue m-r-5"></i></a>
                                <?php if($minutes>30){ ?>
								<a class='reset_bot' id="res_<?php echo $schedule['schedule_id'];?>" href="#" title="Reset Bot"><i class="fa fa-undo icon-blue m-r-5"></i></a>
                                <?php } ?>                                
							</td>                          
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
<script>
$(document).ready(function() {
    $('.view_log').on('click', function(e) {
        var id=$(this).attr('id');
        var sid=id.split('_');
        var apiUrl="<?php echo site_url('admin/diagnostictools/viewLog'); ?>";
        apiUrl=apiUrl+"/"+sid[1];
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
                $('#spinner').addClass('d-none');
                createJobViewHtml(response);
                /*
                    console.log(response.results);
                    $.each(response.results, function(key,value) {
                    alert(value.url);
                });
                */
            },
            fail: function(xhr, textStatus, errorThrown){
                //console.log('Failed');
            }
        });
    });

    $('.reset_bot').on('click', function(e) {
        var id=$(this).attr('id');
        var data=id.split('_');
        var apiUrl="<?php echo site_url('admin/diagnostictools/resetBot'); ?>";
        apiUrl=apiUrl+"/"+data[1];
        var message='';
        message="Are you sure to reset the bot ?";
    
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
                            if(response==1){
                                $('#spinner').addClass('d-none');
                                $('#res_'+data[1]).addClass('d-none');
                                $('#status_'+data[1]).addClass('label-warning');
                                $('#status_'+data[1]).removeClass('label-danger');
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

function createJobViewHtml(response){
    //console.log(response);
    var htmlContent='';
    htmlContent +='<div class="table"><table class="table-sm table-hover table-bordered" cellspacing="0" width="100%" style="font-size: 13px;">';

    

    htmlContent +='<tr>';
    htmlContent +='<td class="font-weight-bold" >Bot Name</td>';
    htmlContent +='<td class="font-weight-bold text-center">'+response.botName+'</td>';
    htmlContent +='</tr>';

    var i=0;
    $.each(response.results, function(key,value) {
        
        var message='';
        if(value.status==1){
            message +='<span class="text-success" >'+value.message+'</span>';
        }else{
            message +='<span class="text-danger" >'+value.message+'</span>';
        }
        htmlContent +='<tr>';
        htmlContent +='<td class="font-weight-bold" >Log Time</td>';
        htmlContent +='<td class="font-weight-bold">'+value.log_time+' | '+message+'</td>';
        htmlContent +='</tr>';

        htmlContent +='<tr>';
        if(value.message=='Started the process.'){
            htmlContent +='<td class="font-weight-bold" >&nbsp;</td>';
            htmlContent +='<td>&nbsp;</td>';
        }else{
            htmlContent +='<td class="font-weight-bold" >Url</td>';
            htmlContent +='<td><a href="'+value.url+'" target="_blank">'+value.url+'</a></td>';
        }
        htmlContent +='</tr>';
        /* htmlContent +='<tr>';
        htmlContent +='<td class="font-weight-bold" width="20%">Response</td>';
        if(value.status==1){
            //htmlContent +='<td class="text-success" >'+value.message+'</td>';
        }else{
            //htmlContent +='<td class="text-danger" >'+value.message+'</td>';
        }
        htmlContent +='</tr>';*/
        i++;
    }); 
    if(typeof value !== "undefined"){
        htmlContent +='<tr>';
        htmlContent +='<td class="font-weight-bold text-center" colspan="2">'+response.message+'</td>';
        htmlContent +='</tr>';
    }   
    htmlContent +='</table>';
    htmlContent +='</div>';

    $('#modal-body').html(htmlContent);
    $('#viewDetails').modal({
        //show: true
    });	
}
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
.modal-content{
    width:125% !important;
}
</style>