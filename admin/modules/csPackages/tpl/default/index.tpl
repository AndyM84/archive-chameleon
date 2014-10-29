<!-- table -->
				<div class="box">
					<!-- box / title -->
					<div class="title">
						<h5>Packages</h5>
						<div class="search">
							<fh:form name="package-search" action="./?nmod=csPackages<% if (isset($_REQUEST['safemode'])): %>&safemode=<%$_REQUEST['safemode']%><% endif; %>">
								<div class="input">
									<a href="./?nmod=csPackages&clearsearch=true" style="color: #FFFFFF; padding-right: 3px; text-decoration: underline">Clear</a>
									<fh:textbox for="keyword" id="search" />
								</div>
								<div class="button">
									<input type="submit" name="submit" value="Search Packages" />
								</div>
							</fh:form>
						</div>
					</div>
					<!-- end box / title -->
					<div class="table">
						<cs:infoboxes />
						<h5>Add New Package</h5>
						<fh:form name="package-upload" action="./?nmod=csPackages&safemode=true" enctype="multipart/form-data">
						<div class="form">
							<div class="fields">
								<div class="field">
									<div class="label">
										<label for="file">File:</label>
									</div>
									<div class="input input-file">
										<fh:file for="file" id="file" size="40" />
									</div>
								</div>
								<div class="field" style="font-weight: bold; text-align: center">
									<p>- OR -</p>
								</div>
								<div class="field">
									<div class="label">
										<label for="url">Url:</label>
									</div>
									<div class="input">
										<fh:textbox for="url" id="url" size="40" />
									</div>
								</div>
								<div class="buttons">
									<input type="submit" name="action" value="Add Package" />
								</div>
							</div>
						</div>
						</fh:form>
						<p>&nbsp;</p>
						<h5>Manage Current Packages</h5>
						<fh:form name="package-lister" action="./?nmod=csPackages&safemode=true">
						<table id="packages">
							<thead>
								<tr>
									<th class="left">Title</th>
									<th>Author</th>
									<th>Version</th>
									<th>Active</th>
									<th class="selected last"><input type="checkbox" class="checkall" /></th>
								</tr>
							</thead>
							<tbody>
<% if (count($packages) > 0): %><% foreach (array_values($packages) as $package): %>								<tr>
									<td class="title"><%$package['name']%></td>
									<td class="author"><a href="<%$package['url']%>" target="_blank"><%echo shortenAuthor($package['author']);%></a></td>
									<td class="date"><%$package['version']%></td>
									<td class="active"><% if ($package['active'] == 1): %>Yes<% else: %>No<% endif; %></td>
									<td class="selected last">
<% if ($package['key'] == 'cs_packages' || $package['key'] == 'cs_skins' || $package['key'] == 'cs_users'): %>										<input type="checkbox" disabled="disabled" />
<% else: %>										<fh:checkbox for="changed[]" value="<%$package['packageId']%>" skipdefault="true" />
<% endif; %>
									</td>
								</tr>
<% endforeach; %><% else: %>								<tr>
									<td class="title" colspan="5">No Packages Installed</td>
								</tr>
<% endif; %>
							</tbody>
						</table>
						<!-- pagination -->
						<div class="pagination pagination-left">
							<div class="results">
								<span>showing results <% if ($paginate->total_entries > 0) { echo(($paginate->offset + 1) . '-' . ($paginate->offset + count($packages))); } else { echo('0'); } %> of <%$paginate->total_entries%></span>
							</div>
							<ul class="pager">
<% if ($paginate->last_page < 1): %>								<li class="disabled">&laquo; prev</li>
<% else: %>								<li><a href="./?nmod=csPackages&pagenum=<%$paginate->last_page%>">&laquo; prev</a></li>
<% endif; %>
<% if ($paginate->total_pages > 0): %><% foreach (array_values($paginate->listPages()) as $page): %>								<li<% if ($page == $paginate->curr_page): %> class="current"<% endif; %>><% if ($page != $paginate->curr_page): %><a href="./?nmod=csPackages&pagenum=<%$page%>"><%$page%></a><% else: %><%$page%><% endif; %></li>
<% endforeach; %><% endif; %>
<% if ($paginate->next_page < 1): %>								<li class="disabled">next &raquo;</li>
<% else: %>								<li><a href="./?nmod=csPackages&pagenum=<%$paginate->next_page%>">next &raquo;</a></li>
<% endif; %>
							</ul>
						</div>
						<!-- end pagination -->
						<!-- table action -->
						<div class="action">
							<select name="action">
								<option value="activate" class="unlocked">De/Activate</option>
								<option value="delete" class="locked">Delete</option>
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