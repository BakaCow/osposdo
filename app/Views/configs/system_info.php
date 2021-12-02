<style type="text/css">
	 a:hover {
	  cursor:pointer;
}
	 hidden {
  visibility: hidden;
}
</style>
</style>
<script type="text/javascript" src="js/clipboard.min.js"></script>
<div id="config_wrapper" class="col-sm-12">
	<?php echo lang('Config.server_notice') ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-2" align="left"><br><?php //TODO: the align attribute is not supported in HTML5 and needs to be replaced with CSS ?>
			<p style="min-height:14.7em;"><strong>General Info </p> 
			<p style="min-height:9.9em;">User Setup</p> 
			<p>Permissions</p></strong>
			</div> 
			<div class="col-sm-8" id="issuetemplate" align="left"><br>
				<?php echo lang('Config.ospos_info') . ':' ?>
				<?php echo esc($this->appconfig->get('application_version')) ?> - <?php echo esc(substr($this->appconfig->get('commit_sha1')), 0, 6) ?><br>
				Language Code: <?php echo current_language_code() ?><br><br>
				<div id="TimeError"></div>
				Extensions & Modules:<br>
					<?php 
						echo "&#187; GD: ", extension_loaded('gd') ? '<font color="green">Enabled &#x2713</font>' : '<font color="red">Disabled &#x2717</font>', '<br>';	//TODO: <font> is not an HTML5 supported tag.  <p style=...> needs to be used instead.
						echo "&#187; BC Math: ", extension_loaded('bcmath') ? '<font color="green">Enabled &#x2713</font>' : '<font color="red">Disabled &#x2717</font>', '<br>';
						echo "&#187; INTL: ", extension_loaded('intl') ? '<font color="green">Enabled &#x2713</font>' : '<font color="red">Disabled &#x2717</font>', '<br>';
						echo "&#187; OpenSSL: ", extension_loaded('openssl') ? '<font color="green">Enabled &#x2713</font>' : '<font color="red">Disabled &#x2717</font>', '<br>';
						echo "&#187; MBString: ", extension_loaded('mbstring') ? '<font color="green">Enabled &#x2713</font>' : '<font color="red">Disabled &#x2717</font>', '<br>';
						echo "&#187; Curl: ", extension_loaded('curl') ? '<font color="green">Enabled &#x2713</font>' : '<font color="red">Disabled &#x2717</font>', '<br> <br>';
					?>
				User Configuration:<br>
				.Browser:
					<?php
						function get_browser_name($user_agent)
						{
							if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
							elseif (strpos($user_agent, 'Edge')) return 'Edge';
							elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
							elseif (strpos($user_agent, 'Safari')) return 'Safari';
							elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
							elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
							return 'Other';
						}
						 echo esc(get_browser_name($_SERVER['HTTP_USER_AGENT']));
					?><br>
				.Server Software: <?php echo esc($_SERVER['SERVER_SOFTWARE']) ?><br>
				.PHP Version: <?php echo PHP_VERSION ?><br>
				.DB Version: <?php echo esc(mysqli_get_server_info($this->db->conn_id)) ?><br>
				.Server Port: <?php echo esc($_SERVER['SERVER_PORT']) ?><br>
				.OS: <?php echo php_uname('s') .' '. php_uname('r') ?><br><br>
				File Permissions:<br>
						&#187; [application/logs:]
						<?php $logs = '../application/logs/'; 
							$uploads = '../public/uploads/'; 
							$images = '../public/uploads/item_pics/'; 
							$import = '../import_items.csv';
							$importcustomers = '../import_customers.csv';	//TODO: This variable does not follow naming conventions for the project.
							
							if (is_writable($logs)) {
								echo ' -  ' . substr(sprintf("%o",fileperms($logs)),-4) . ' |  ' . '<font color="green">  Writable &#x2713 </font>';
							} else {
								echo ' -  ' . substr(sprintf("%o",fileperms($logs)),-4) . ' |  ' . '<font color="red">	Not Writable &#x2717 </font>';
							}
							clearstatcache();
							if (is_writable($logs) && substr(decoct(fileperms($logs)), -4) != 750  ) {
								echo ' | <font color="red">Vulnerable or Incorrect Permissions &#x2717</font>';
							} else {
								echo ' | <font color="green">Security Check Passed &#x2713 </font>';
							}	
							clearstatcache();
						?>
						<br>
						&#187; [public/uploads:]
						<?php
							if (is_writable($uploads)) {
								echo ' -  ' . substr(sprintf("%o",fileperms($uploads)),-4) . ' |  ' . '<font color="green">	 Writable &#x2713 </font>';
							} else {
								echo ' -  ' . substr(sprintf("%o",fileperms($uploads)),-4) . ' |  ' . '<font color="red"> Not Writable &#x2717 </font>';
							}
							clearstatcache();
							if (is_writable($uploads) && substr(decoct(fileperms($uploads)), -4) != 750  ) {
								echo ' | <font color="red">Vulnerable or Incorrect Permissions &#x2717</font>';
							} else {
								echo ' |  <font color="green">Security Check Passed &#x2713 </font>';
							}	
							clearstatcache();
						?>
						<br>
						&#187; [public/uploads/item_pics:]	
						<?php 
							if (is_writable($images)) {
								echo ' -  ' . substr(sprintf("%o",fileperms($images)),-4) . ' |	 ' . '<font color="green"> Writable &#x2713 </font>';
							} else {
								echo ' -  ' . substr(sprintf("%o",fileperms($images)),-4) . ' |	 ' . '<font color="red"> Not Writable &#x2717 </font>';
							} 
							clearstatcache();
							if (substr(decoct(fileperms($images)), -4) != 750  ) {
								echo ' | <font color="red">Vulnerable or Incorrect Permissions &#x2717</font>';
							} else {
								echo ' | <font color="green">Security Check Passed &#x2713 </font>';
							}	
							clearstatcache();
						?>
						<br>
						&#187; [import_customers.csv:]
						<?php 
							if (is_readable($importcustomers)) {
								echo ' -  ' . substr(sprintf("%o",fileperms($importcustomers)),-4) . ' |  ' . '<font color="green">	 Readable &#x2713 </font>';
							} else {
								echo ' -  ' . substr(sprintf("%o",fileperms($importcustomers)),-4) . ' |  ' . '<font color="red"> Not Readable &#x2717 </font>';
							}
							clearstatcache(); 
							if (!((substr(decoct(fileperms($importcustomers)), -4) == 640) || (substr(decoct(fileperms($importcustomers)), -4) == 660) )) {
								echo ' | <font color="red">Vulnerable or Incorrect Permissions &#x2717</font>';
							} else {
								echo ' | <font color="green">Security Check Passed &#x2713 </font>';
							}	
							clearstatcache();
						?>
						<br>
						<?php
					
						if(!((substr(decoct(fileperms($logs)), -4) == 750) && (substr(decoct(fileperms($uploads)), -4) == 750) && (substr(decoct(fileperms($images)), -4) == 750) 
                             && ((substr(decoct(fileperms($importcustomers)), -4) == 640) || (substr(decoct(fileperms($importcustomers)), -4) == 660)))) {
							echo '<br><font color="red"><strong>' . lang('Config.security_issue') . '</strong> <br>' . lang('Config.perm_risk') . '</font><br>';
						} 
						else { 
							echo '<br><font color="green">' . lang('Config.no_risk') . '</strong> <br> </font>';
						}
						if(substr(decoct(fileperms($logs)), -4) != 750) {
							echo '<br><font color="red"> &#187; [application/logs:] ' . lang('Config.is_writable') . '</font>';
						}
						if(substr(decoct(fileperms($uploads)), -4) != 750) {
							echo '<br><font color="red"> &#187; [public/uploads:] ' . lang('Config.is_writable') . '</font>';
						}
						if(substr(decoct(fileperms($images)), -4) != 750) {
							echo '<br><font color="red"> &#187; [public/uploads/item_pics:] ' . lang('Config.is_writable') . '</font>';
						}
						if(!((substr(decoct(fileperms($importcustomers)), -4) == 640) || (substr(decoct(fileperms($importcustomers)), -4) == 660))) {
							echo '<br><font color="red"> &#187; [import_customers.csv:] ' . lang('Config.is_readable') . '</font>';
						}
						?>
						<br>
				<div id="timezone" style="font-weight:600;"></div><br><br>
				<div id="ostimezone" style="display:none;" ><?php echo esc($this->appconfig->get('timezone')) ?></div><br>
				<br>	
			</div>
		</div>
	</div>
</div>
<div align="center">
		<a class="copy" data-clipboard-action="copy" data-clipboard-target="#issuetemplate">Copy Info</a> | <a href="https://github.com/opensourcepos/opensourcepos/issues/new" target="_blank"> <?php echo lang('Config.report_an_issue') ?></a>
		<script>
			var clipboard = new ClipboardJS('.copy');

			clipboard.on('success', function(e) {
				document.getSelection().removeAllRanges();
			});
			
			document.getElementById("timezone").innerText = Intl.DateTimeFormat().resolvedOptions().timeZone;
					
			$(function() {
				$('#timezone').clone().appendTo('#timezoneE');
			});
							
			if($('#timezone').html() !== $('#ostimezone').html())
			document.getElementById("TimeError").innerHTML = '<font color="red"><?php echo lang('Config.timezone_error') ?></font><br><br><?php echo lang('Config.user_timezone') ?><div id="timezoneE" style="font-weight:600;"></div><br><?php echo lang('Config.os_timezone') ?><div id="ostimezoneE" style="font-weight:600;"><?php echo esc($this->appconfig->get('timezone')) ?></div><br>';
		</script>
</div>
