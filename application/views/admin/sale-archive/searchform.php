<style>
.ms-options{
	scroll-behavior:auto !important;
}
</style>
<div class="row">
        <div class="col-lg-12">

            
           <div class="panel panel-info">
              <div class="panel-body table-responsive">		
			  <div class="alert alert-success delete_msg pull d-none" id='close_checkbox' style="width: 100%"> 
			  </div>
				 <?php $msg = $this->session->flashdata('msg'); ?>
            <?php if (isset($msg)): ?>
                <div class="alert alert-success delete_msg pull" style="width: 100%"> <i class="fa fa-check-circle"></i> <?php echo $msg; ?> &nbsp;
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">X</span> </button>
                </div>
            <?php endif ?>

            <?php $error_msg = $this->session->flashdata('error_msg'); ?>
            <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger delete_msg pull" style="width: 100%"> <i class="fa fa-times"></i> <?php echo $error_msg; ?> &nbsp;
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">X</span> </button>
                </div>
            <?php endif ?>
			<?php if($flag==0){ ?>
				<form autocomplete="off" class="needs-validation" name='searchResult' id='searchResult' method='get' action="<?php echo $action; ?>">
					<h3 class="box-title">Archived Records</h3>
					<hr>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label required">Select Website</label>
								<?php echo form_dropdown('website_id', $websiteListArr, $website_id,'class="form-control" id="website_id"'); ?>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label ">Select Bot</label>
								<select name="showHideBots[]" multiple="multiple" class="3col active" id="showHideBots">
									<?php foreach ($botArray as $key=>$value) {
										?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php } ?>
								</select>						
							</div>
						</div>
						<input type="hidden" name="hidSelectedBots" id="hidSelectedBots" value="<?php echo $hidSelectedBots; ?>">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Start scraped date</label>
								<input type="date" name="scraped_date_start" id="scraped_date_start" class="form-control my-datepicker" value="<?php if(!empty($scraped_date_start)) { echo $scraped_date_start; } ?>" autocomplete="<?php echo $autocomplete; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">End scraped date</label>
								<input type="date" name="scraped_date_end" id="scraped_date_end" class="form-control my-datepicker" value="<?php if(!empty($scraped_date_end)) { echo $scraped_date_end; } ?>" autocomplete="<?php echo $autocomplete; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group" id='qualifydropdown'>
								<label class="control-label">Qualify</label>
								<?php echo form_dropdown('qualify', $qualifySearchArr, $qualify,'class="form-control" id="qualify"'); ?>
							</div>
						</div>

						<!--<div class="col-md-4">
							<div class="form-group">
								<label class="control-label ">Show/Hide Columns</label>
								<select name="showHideCols[]" multiple="multiple" class="3col active" id="showHideCols">
									<?php foreach ($showHideArray as $key=>$value) {
										?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php } ?>
								</select>						
							</div>
						</div>
						<input type="hidden" name="hidSelectedOptions" id="hidSelectedOptions" value="<?php echo $hidSelectedOptions; ?>"> -->									
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								
								<button type="submit" class="btn btn-rounded btn-sm btn-info"> <i class="fa fa-search"></i> Search</button>
<button type="button" id="download" class="btn btn-rounded btn-sm btn-info"> <i class="fa fa-download"></i> Download</button>

<button type="button" id="deleteResults" class="btn btn-rounded btn-sm btn-danger"> <i class="fa fa-trash"></i> Delete</button>
<!--<?php if($userRole=='ADMIN'){ ?>
<button type="button" id="deleteAll" title='It will delete all matching records filld in search box.' class="btn btn-rounded btn-sm btn-danger"> <i class="fa fa-trash"></i> Delete All</button>
<?php } ?> -->
							</div>
						</div>
						
					</div>
					<?php if($userRole=='ADMIN'){ ?>
						<!--<br><span class='text-danger'>* Delete All will delete all matching records filld in search box.</span>-->
					<?php } ?>
					<div class="row">
						<div class="col-md-12">
							<b>Total records - <?php echo $total_rows; ?></b>
						</div>
					</div>
					<input type='hidden' name='deleteAllSearch' id='deleteAllSearch' value=''>
				</form>
				<?php } ?>