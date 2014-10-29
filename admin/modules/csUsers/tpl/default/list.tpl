<script type="text/javascript">
					var addUser = function () {
						location.href = './?nmod=csUsers&page=add';
					};

					var checkDelete = function () {
						if ($('select[name=action]').val() == 'delete') {
							if (confirm('Are you sure, this action can not be undone!')) {
								return(true);
							} else {
								return(false);
							}
						}
					};
				</script>
				<!-- table -->
				<div class="box">
					<!-- box / title -->
					<div class="title">
						<h5>Users</h5>
						<div class="search">
							<fh:form name="user-search" action="./?nmod=csUsers">
								<div class="input">
									<a href="./?nmod=csUsers&clearsearch=true" style="color: #FFFFFF; padding-right: 3px; text-decoration: underline">Clear</a>
									<fh:textbox for="keyword" id="search" />
								</div>
								<div class="button">
									<input type="submit" name="submit" value="Search Users" />
								</div>
							</fh:form>
						</div>
					</div>
					<!-- end box / title -->
					<div class="table">
						<cs:infoboxes />
						<div class="form">
							<div class="fields">
								<div class="buttons" style="text-align: right">
									<input type="submit" onclick="addUser(); return false;" value="Add User" />
								</div>
							</div>
						</div>
						<h5>Manage Current Users</h5>
						<fh:form name="user-lister" action="./?nmod=csUsers" onsubmit="return checkDelete();">
						<table id="users">
							<thead>
								<tr>
									<th class="left">Username</th>
									<th>Email</th>
									<th>Joined</th>
									<th>Active</th>
									<th class="selected last"><input type="checkbox" class="checkall" /></th>
								</tr>
							</thead>
							<tbody>
<% if (count($users) > 0): %><% foreach (array_values($users) as $user): %>								<tr>
									<td><a href="./?nmod=csUsers&page=edit&uid=<%$user['userId']%>"><%$user['username']%></a></td>
									<td class="author"><a href="mailto:<%$user['email']%>"><%$user['email']%></a></td>
									<td class="date"><%echo substr($user['dateJoined'], 0, 10);%></td>
									<td class="active"><% if ($user['status'] == 1): %>Yes<% else: %>No<% endif; %></td>
									<td class="selected last">
<% if ($user['userId'] == '1'): %>										<input type="checkbox" disabled="disabled" />
<% else: %>										<fh:checkbox for="changed[]" value="<%$user['userId']%>" />
<% endif; %>
									</td>
								</tr>
<% endforeach; %><% else: %>								<tr>
									<td class="title" colspan="5">No Users Found</td>
								</tr>
<% endif; %>
							</tbody>
						</table>
						<!-- pagination -->
						<div class="pagination pagination-left">
							<div class="results">
								<span>showing users <% if ($paginate->total_entries > 0) { echo(($paginate->offset + 1) . '-' . ($paginate->offset + count($users))); } else { echo('0'); } %> of <%$paginate->total_entries%></span>
							</div>
							<ul class="pager">
<% if ($paginate->last_page < 1): %>								<li class="disabled">&laquo; prev</li>
<% else: %>								<li><a href="./?nmod=csUsers&pagenum=<%$paginate->last_page%>">&laquo; prev</a></li>
<% endif; %>
<% if ($paginate->total_pages > 0): %><% foreach (array_values($paginate->listPages()) as $page): %>								<li<% if ($page == $paginate->curr_page): %> class="current"<% endif; %>><% if ($page != $paginate->curr_page): %><a href="./?nmod=csUsers&pagenum=<%$page%>"><%$page%></a><% else: %><%$page%><% endif; %></li>
<% endforeach; %><% endif; %>
<% if ($paginate->next_page < 1): %>								<li class="disabled">next &raquo;</li>
<% else: %>								<li><a href="./?nmod=csUsers&pagenum=<%$paginate->next_page%>">next &raquo;</a></li>
<% endif; %>
							</ul>
						</div>
						<!-- end pagination -->
						<!-- table action -->
						<div class="action">
							<select name="action">
								<option value="activate" class="unlocked">De/Activate</option>
								<option value="permissions" class="key">Permissions</option>
								<option value="delete" class="trash">Delete</option>
							</select>
							<div class="button">
								<input type="submit" name="submit" value="Apply to Selected" />
							</div>
						</div>
						<!-- end table action -->
						</fh:form>
					</div>
				</div>
				<!-- end table -->