<style>
	.wrapTd{
		word-wrap: break-word;min-width: 160px;max-width: 160px;
	}
</style>

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
    $(function () {
        $('#showHideCols.active.3col').multiselect({
            columns: 2,
            placeholder: 'Show/Hide Columns',
            search: true,
            searchOptions: {
                'default': 'Show/Hide Columns'
            },
            selectAll: false
        });

		$('#showHideBots.active.3col').multiselect({
            columns: 1,
            placeholder: 'Select Bot',
            search: true,
            searchOptions: {
                'default': 'Select Bot'
            },
            selectAll: false
        });
    });
</script>
<script>
$(document).ready(function(){
	var hidValue = $("#hidSelectedBots").val();
	if(hidValue!=''){
		var selectedOptions = hidValue.split(",");
		for(var i in selectedOptions) {
			var optionVal = selectedOptions[i];

			$("#showHideBots").find("option[value="+optionVal+"]").prop("selected", "selected");
		}
		$("#showHideBots").multiselect('reload');
	}
	

	removeColumns();
	$("#showHideBots").on('change', function() {
		removeColumns();
	});

	
	$('#website_id').on('change', function() {
		var url='<?php echo site_url("admin/downloadarchive"); ?>';
		url=url+"?website_id="+$('#website_id').val();		
		location.href=url;
	});
	$('#download').on('click', function() {
		var url='<?php echo site_url("admin/downloadarchive/downloadCSV"); ?>';
		url=url+"?website_id="+$('#website_id').val();
		url=url+"&bot_name="+$('#showHideBots').val();
		url=url+"&scraped_date_start="+$('#scraped_date_start').val();
		url=url+"&scraped_date_end="+$('#scraped_date_end').val();
		url=url+"&qualify="+$('#qualify').val();
		location.href=url;
	});
	$('#deleteResults').on('click', function() {
		$.confirm({
			title: 'Warning!',
			content: 'Once deleted records will not be recovered',
			type: 'red',
			buttons: {					
				confirm: function () {						
					$( "#frmDeleteResults" ).submit();						
				},
				cancel: function () {
				}					
			}
		});	
		
	});
	$('#deleteAllResults').on('click', function() {
		if($(this).prop("checked")==true){
			$(".record_delete").prop("checked",true);	
		}else{
			$(".record_delete").prop("checked",false);
		}		
	});
	$('#deleteAll').on('click', function() {
		$.confirm({
			title: 'Warning!',
			content: 'Once deleted, records will not be recovered',
			type: 'red',
			buttons: {					
				confirm: function () {						
					$('#deleteAllSearch').val('true');
					$('#searchResult').submit();					
				},
				cancel: function () {
				}					
			}
		});			
	});
	$('.viewJob').on('click', function() {
		var jobIdArr=this.id.split('_');
		var jid=jobIdArr[1];
		var apiUrl='<?php echo site_url('admin/downloadarchive/fetchDetails/') ?>';
		apiUrl=apiUrl+jid;
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

	if(response.bot_name !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Bot Name</td>';
		htmlContent +='<td>'+response.bot_name+'</td>';
		htmlContent +='</tr>';
	}
	if(response.qualify !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Qualify</td>';
		htmlContent +='<td>'+response.qualify+'</td>';
		htmlContent +='</tr>';
	}
	if(response.name !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Name</td>';
		htmlContent +='<td>'+response.name+'</td>';
		htmlContent +='</tr>';
	}
	if(response.phone !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Phone</td>';
		htmlContent +='<td>'+response.phone+'</td>';
		htmlContent +='</tr>';
	}
	if(response.email !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Email</td>';
		htmlContent +='<td>'+response.email+'</td>';
		htmlContent +='</tr>';
	}
	if(response.job_url !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Post Url</td>';
		htmlContent +='<td><a target="_blank" href="'+response.job_url+'">'+response.job_url+'</a></td>';
		htmlContent +='</tr>';
	}

	if(response.title !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Post Title</td>';
		htmlContent +='<td class="wrapTd">'+response.title+'</td>';
		htmlContent +='</tr>';
	}
	if(response.keywords !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Keywords</td>';
		htmlContent +='<td class="wrapTd"> '+response.keywords+'</td>';
		htmlContent +='</tr>';
	}
	if(response.compensation !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Compensation</td>';
		htmlContent +='<td class="wrapTd"> '+response.compensation+'</td>';
		htmlContent +='</tr>';
	}
	if(response.applications !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Applications</td>';
		htmlContent +='<td class="wrapTd"> '+response.applications+'</td>';
		htmlContent +='</tr>';
	}
	if(response.posted_date !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Posted Date</td>';
		htmlContent +='<td class="wrapTd"> '+response.posted_date+'</td>';
		htmlContent +='</tr>';
	}
	if(response.scraped_date !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Scraped Date</td>';
		htmlContent +='<td class="wrapTd"> '+response.scraped_date+'</td>';
		htmlContent +='</tr>';
	}
	
	if(response.location !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Location</td>';
		htmlContent +='<td class="wrapTd"> '+response.location+'</td>';
		htmlContent +='</tr>';
	}
	if(response.organization !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Organization</td>';
		htmlContent +='<td class="wrapTd"> '+response.organization+'</td>';
		htmlContent +='</tr>';
	}
	if(response.employementType !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">EmployementType</td>';
		htmlContent +='<td class="wrapTd"> '+response.employementType+'</td>';
		htmlContent +='</tr>';
	}
	if(response.industries !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Industries</td>';
		htmlContent +='<td class="wrapTd"> '+response.industries+'</td>';
		htmlContent +='</tr>';
	}
	if(response.companyUrl !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Company Url</td>';
		htmlContent +='<td class="wrapTd"><a target="_blank" href="'+response.companyUrl+'">'+response.companyUrl+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.profile_url !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Profile Url</td>';
		htmlContent +='<td class="wrapTd"><a target="_blank" href="'+response.profile_url+'"> '+response.profile_url+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.twitterURL !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Twitter URL</td>';
		htmlContent +='<td class="wrapTd"> <a target="_blank" href="'+response.twitterURL+'">'+response.twitterURL+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.facebookURL !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Facebook URL</td>';
		htmlContent +='<td class="wrapTd"> <a target="_blank" href="'+response.facebookURL+'">'+response.facebookURL+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.jobFunction !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Job Function</td>';
		htmlContent +='<td class="wrapTd"> '+response.jobFunction+'</td>';
		htmlContent +='</tr>';
	}
	if(response.companyWebsite !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Company Website</td>';
		htmlContent +='<td class="wrapTd"> <a target="_blank" href="'+response.companyWebsite+'">'+response.companyWebsite+'</a></td>';
		htmlContent +='</tr>';
	}
	if(response.employees !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Employees</td>';
		htmlContent +='<td class="wrapTd"> '+response.employees+'</td>';
		htmlContent +='</tr>';
	}
	if(response.employeesOnLinkedin !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Employees On Linkedin</td>';
		htmlContent +='<td class="wrapTd"> '+response.employeesOnLinkedin+'</td>';
		htmlContent +='</tr>';
	}
	if(response.emails !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Emails</td>';
		htmlContent +='<td class="wrapTd"> '+response.emails+'</td>';
		htmlContent +='</tr>';
	}

	if(response.decision_maker !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Decision Maker</td>';
		htmlContent +='<td class="wrapTd"> '+response.decision_maker+'</td>';
		htmlContent +='</tr>';
	}

	if(response.description !=''){	
		htmlContent +='<tr>';	
		htmlContent +='<td class="font-weight-bold" width="20%">Description</td>';
		htmlContent +='<td class="wrapTd"> '+response.description+'</td>';
		htmlContent +='</tr>';
	}

	
	htmlContent +='</table>';
	htmlContent +='</div>';

	$('#modal-body').html(htmlContent);
	$('#viewDetails').modal({
		//show: true
	});	
}
function removeColumns(){
	
	$('li input[type="checkbox"]').each(function(){
		var shDiv=$(this).val();
		if($(this).is(":checked")){
			$("."+shDiv).show();
		}else{
			$("."+shDiv).hide();
		}
	});
	
}
</script>