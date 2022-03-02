<script>
	function showPluginDetails() {
		var id = $('#pluginslist').val();
		$('.plugin-details').hide();
		$('#' + id).show();
		return;
	}
</script>							
							
    <!-- /#wrapper -->
    <!-- jQuery -->
	<?php if (($this->session->flashdata('flash_message')) != ""): ?>
	<script type="text/javascript">
    $(document).ready(function() {
        $.toast({
           
            text: '<?php echo $this->session->flashdata('flash_message'); ?>',
            position: 'top-right',
            loaderBg: '#5475ed',
            icon: 'info',
            hideAfter: 3500,
            stack: 6
        })
    });
    </script>
	<?php endif; ?>	
	
	<?php if (($this->session->flashdata('error_message')) != ""): ?>
	<script type="text/javascript">
    $(document).ready(function() {
        $.toast({
           
            text: '<?php echo $this->session->flashdata('error_message'); ?>',
            position: 'top-right',
            loaderBg: '#f56954',
            icon: 'warning',
            hideAfter: 3500,
            stack: 6
        })
    });
    </script>
	<?php endif; ?>
	
	
	
	


	<!-- Bootstrap Core JavaScript -->
    <script src="<?php echo base_url(); ?>svi/bootstrap/dist/js/bootstrap.min.js"></script>   
    <!-- Menu Plugin JavaScript -->
    <script src="<?php echo base_url(); ?>svi/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
	 <!-- icheck -->   
    <script src="<?php echo base_url(); ?>svi/js/jquery.slimscroll.js"></script>    
    <!-- Custom Theme JavaScript -->
    <script src="<?php echo base_url(); ?>svi/js/custom.min.js"></script>
    <script src="<?php echo base_url(); ?>svi/js/jquery-confirm.min.js"></script>
    <!--<script src="<?php echo base_url(); ?>svi/js/jquery.dataTables.min.js"></script>-->
</body>

</html>
