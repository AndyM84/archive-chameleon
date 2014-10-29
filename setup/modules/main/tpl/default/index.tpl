<%setupHeader%>
							<script type="text/javascript">
								var doSubmit = function () {
									$('#step1-action').attr('value', 'Continue');
									$('#step1-form').submit();

									return;
								};
							</script>
							<h1>Configuration</h1>
							<p>
								Enter the following configuration values for your installation of the Chameleon Site Framework.
							</p>
							<cs:infoboxes />
							<br /><br /><br />
							<fh:form name="step1" id="step1-form" action="./?page=step1">
							<div class="row">
								<div class="left">
									<h2>Admin Username</h2>
									<p>&nbsp;</p>
								</div>
								<div class="right">
									<fh:textbox for="admin_username" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Admin Password</h2>
									<p>Twice to confirm</p>
								</div>
								<div class="right">
									<fh:password for="admin_password" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>&nbsp;</h2>
									<p>&nbsp;</p>
								</div>
								<div class="right">
									<fh:password for="admin_password2" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Admin Email</h2>
									<p>Your email address</p>
								</div>
								<div class="right">
									<fh:textbox for="admin_email" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Database Host</h2>
									<p>&nbsp;</p>
								</div>
								<div class="right">
									<fh:textbox for="db_host" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Database Port</h2>
									<p>(optional)</p>
								</div>
								<div class="right">
									<fh:textbox for="db_port" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Database Name</h2>
									<p>&nbsp;</p>
								</div>
								<div class="right">
									<fh:textbox for="db_name" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Table Prefix</h2>
									<p>(optional)</p>
								</div>
								<div class="right">
									<fh:textbox for="db_prefix" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Database User</h2>
									<p>&nbsp;</p>
								</div>
								<div class="right">
									<fh:textbox for="db_user" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Database Password</h2>
									<p>&nbsp;</p>
								</div>
								<div class="right">
									<fh:password for="db_pass" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Cookie Domain</h2>
									<p>Used for cookies</p>
								</div>
								<div class="right">
									<fh:textbox for="site_cookie_domain" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Site Path</h2>
									<p>Used for links</p>
								</div>
								<div class="right">
									<fh:textbox for="site_path" />
								</div>
							</div>
							<div class="row">
								<div class="left">
									<h2>Enable Dev Mode</h2>
									<p>Enables extra debug info</p>
								</div>
								<div class="right">
									<fh:checkbox for="enable_dev_mode" value="true" />
								</div>
							</div>
							<br /><br /><br /><br />
							<input type="hidden" name="action" id="action" value="" />
							<a href="javascript: //;" onclick="doSubmit();" class="continue-button"></a>
							<br /><br/>
							</fh:form>
<%setupFooter%>