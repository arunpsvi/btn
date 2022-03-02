				<div class="table-responsive">
					<table class="table-sm table-hover text-nowrap table-bordered" cellspacing="0" width='100%' style='font-size: 13px;'>
						<thead >
							<tr  class="bg-info">                                   
								<?php 
									$websiteId=$_GET['website_id'];
									$scraped_date=$_GET['scraped_date'];
									$per_page=$_GET['per_page'];
									$sort_order=$_GET['sort_order'];
									$sortIcon='';
									if($sort_order=='asc'){
										$sort_order='desc';
										$sortIcon='fa-sort-asc';
									}elseif($sort_order=='desc'){
										$sort_order='asc';
										$sortIcon='fa-sort-desc';
									}
									if(empty($sort_order)){
										$sort_order='asc';
									}
									$queryString="?website_id=$websiteId&scraped_date=$scraped_date&per_page=$per_page&sort_order=$sort_order"; 

								?>
								<?php //echo anchor('admin/download/'.$queryString, 'Qualify<i class="fa '.$sortIcon.' m-l-5" ></i>',array('class' => 'font-weight-bold text-white')); ?>
								<th style='min-width:40px !important;' class='font-weight-bold text-white'><i class="fa fa-trash m-r-5"></i><input type="checkbox" class="form-check-input" name="deleteAllResults" id="deleteAllResults"></th>
								<th class='font-weight-bold text-white'>No.</th>
								<th class='font-weight-bold text-white shdiv_Qualify'>Qualify</th>
								<th class='font-weight-bold text-white shdiv_DecisionMaker' width='30%'>Decision Maker</th>
								<th class='font-weight-bold text-white shdiv_Post_Title' width='30%'>Post Title</th>
								<th class='font-weight-bold text-white'>Action</th> 
								
								


								<!--<th class='font-weight-bold text-white'>Name</th> 
								<th class='font-weight-bold text-white'>Phone</th> 
								<th class='font-weight-bold text-white'>Email</th> 
												
								<th class='font-weight-bold text-white'>Exact match</th>
								<th class='font-weight-bold text-white'>Emails</th>	-->	
							</tr>
						</thead>
						<tbody>
						<form name='frmDeleteResults' id='frmDeleteResults' action='<?php echo site_url("admin/downloadarchive/delete"); ?>' method='post'>
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
						<input type="hidden" name="website_id" value="<?php echo $websiteId; ?>">
						<input type="hidden" name="scraped_date" value="<?php echo $scraped_date; ?>">
						<input type="hidden" name="qualify" value="<?php echo $qualify; ?>">
						<?php foreach ($jobListings as $jobListing){
							
							$ci = get_instance();
							$keycontacts=$ci->common_model->get_key_persons($jobListing);
							$keycontactsArr=Array();
							foreach ($keycontacts as $keycontact){
								$keycontactsArr[]=$keycontact;
								#$keycontacts .=$keycontact['person_name']."(".$keycontact['designation'].")";
								#$keycontacts .=$keycontact['profile_url']."<br>";
							}

							if($jobListing['exact_match']=='Y'){
								$jobListing['exact_match']='Yes';
							}else{
								$jobListing['exact_match']='No';
							}

							$jobListing['keywords']=preg_replace('/,/is',', ',$jobListing['keywords']);
							$result_id =$jobListing['result_id'];

						?>
						<tr class='BOT_<?php echo $jobListing['search_id']; ?>'>     
							<td ><input type="checkbox" class="class-name record_delete" name="record_delete[]" value='<?php echo $jobListing['result_id']; ?>'></td>
							<td><?php echo $jobListing['result_id']; ?></td>
							<td class='shdiv_Qualify'><?php echo $jobListing['qualify']; ?></td>
							<td class='shdiv_DecisionMaker'>
								<?php if(strlen($jobListing['decision_maker'])>25){
									echo substr($jobListing['decision_maker'],0,25)." ...";
								}else{
									echo $jobListing['decision_maker'];
								} ?>
							</td>
							<td class='shdiv_Post_Title' >
								<?php if(strlen($jobListing['title'])>25){
									echo substr($jobListing['title'],0,25)." ...";
								}else{
									echo $jobListing['title'];
								} ?>
							</td>
						
							<td class="text-nowrap">
								<a href="#"><button type="button" title="View Details" class="btn btn-success btn-circle btn-xs viewJob" id='jid_<?php echo $jobListing['result_id']; ?>'><i class="fa fa-eye"></i></button></a>
								
							</td>							
						</tr>
						<?php } ?>
						</form>	
						</tbody>
					</table>
				</div>
				
                </div>
				<?php echo $pagination; ?>		
				
            </div>
        </div>
    </div>

 </div>

