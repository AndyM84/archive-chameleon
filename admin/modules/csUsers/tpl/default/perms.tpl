<script type="text/javascript">
					var backToUsers = function () {
						location.href = "./?nmod=csUsers&rand=" + Math.random();
					};
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
						<h5>User Permissions</h5>
						<form name="user-perms" action="./?nmod=csUsers&page=perms" method="post">
						<table id="users">
							<thead>
								<tr>
									<th class="left">Permission</th>
									<th class="selected last"><input type="checkbox" class="checkall" /></th>
								</tr>
							</thead>
							<tbody>
<% foreach (array_values($permList) as $group): %>								<tr>
									<td colspan="2" align="left" style="font-weight: bold" class="last"><%$group['name']%></td>
								</tr>
<% foreach (array_values($group['perms']) as $perm): %>								<tr>
									<td><%$perm['label']%></td>
									<td class="selected last"><input type="checkbox" name="perms[<%$perm['permId']%>]" value="1"<% if ($perm['checked'] == 'true'): %> checked="checked"<% endif; %> /></td>
								</tr>
<% endforeach; %><% endforeach; %>
							</tbody>
						</table>
						<!-- table action -->
						<div class="action">
							<div class="button">
								<input type="submit" name="action" value="Save" />
							</div>
						</div>
						<!-- end table action -->
						</form>
					</div>
				</div>
				<!-- end table -->