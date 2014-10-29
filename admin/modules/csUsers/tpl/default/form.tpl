<script type="text/javascript">
					var backToUsers = function () {
						location.href = "./?nmod=csUsers&rand=" + Math.random();
					};
					$(function() {
						$( ".date-picker" ).datepicker({ dateFormat: 'yy-mm-dd' });
					});
				</script>
				<!-- table -->
				<div class="box">
					<!-- box / title -->
					<div class="title">
						<h5>Users</h5>
					</div>
					<!-- end box / title -->
					<div class="table">
						<cs:infoboxes />
						<div class="form">
							<div class="fields">
								<div class="buttons" style="text-align: right">
									<input type="submit" onclick="backToUsers(); return false;" value="Back to User List" />
								</div>
							</div>
						</div>
						<h5><% if ($_REQUEST['page'] == 'add'): %>Add<% else: %>Edit<% endif; %> User</h5>
						<fh:form name="user-modify" action="./?nmod=csUsers&page=<%$_REQUEST['page']%>" enctype="multipart/form-data">
						<fh:hidden for="userId" id="userId" />
						<div class="form">
							<div class="fields">
								<div class="field">
									<div class="label">
										<label for="username">Username:</label>
									</div>
									<div class="input">
										<fh:textbox for="username" id="username" size="20" />
									</div>
								</div>
								<div class="field">
									<div class="label">
										<label for="email">Email:</label>
									</div>
									<div class="input">
										<fh:textbox for="email" id="email" size="40" />
									</div>
								</div>
								<div class="field">
									<div class="label">
										<label for="password">Password:</label>
									</div>
									<div class="input">
										<fh:password for="password" id="password" size="25" />
									</div>
								</div>
								<div class="field">
									<div class="label">
										<label for="confirmPassword">Confirm Password:</label>
									</div>
									<div class="input">
										<fh:password for="confirmPassword" id="confirmPassword" size="25" />
									</div>
								</div>
								<div class="field">
									<div class="label">
										<label for="dateJoined">Date Joined:</label>
									</div>
									<div class="input">
										<fh:textbox for="dateJoined" id="dateJoined" size="25" class="date-picker"/>
									</div>
								</div>
								<div class="field">
									<div class="label">
										<label for="status">Active:</label>
									</div>
									<div class="input">
										<fh:dropdown for="status" data="add_user_model::getStatuses" />
									</div>
								</div>
								<cs:location key="admin-userform" />
								<div class="buttons">
									<input type="submit" name="action" value="<% if ($_REQUEST['page'] == 'add'): %>Add<% else: %>Edit<% endif; %> User" />
								</div>
							</div>
						</div>
						</fh:form>
					</div>
				</div>
				<!-- end table -->