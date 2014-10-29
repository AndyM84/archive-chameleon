<!-- table -->
				<div class="box">
					<!-- box / title -->
					<div class="title">
						<h5>Skins/Modules</h5>
					</div>
					<!-- end box / title -->
					<div class="table">
						<cs:infoboxes />
						<h5>Front Default Modules</h5>
						<fh:form name="module-selector" action="./?nmod=csSkins">
						<div class="form">
							<div class="fields">
								<div class="field">
									<div class="label">
										<label for="file">Default Start:</label>
									</div>
									<div class="input input-file">
										<fh:dropdown for="startmod" data="module_model::getStartMods" />
									</div>
								</div>
								<div class="field">
									<div class="label">
										<label for="url">Default Error:</label>
									</div>
									<div class="input">
										<fh:dropdown for="errormod" data="module_model::getErrorMods" />
									</div>
								</div>
								<div class="buttons">
									<input type="submit" name="action" value="Set Modules" />
								</div>
							</div>
						</div>
						</fh:form>
						<h5>Select Active Skin</h5>
						<fh:form name="skin-lister" action="./?nmod=csSkins">
						<table id="skins">
							<thead>
								<tr>
									<th class="left">Title</th>
									<th>Author</th>
									<th>Version</th>
									<th>Active</th>
								</tr>
							</thead>
							<tbody>
<% if (count($packages) > 0): %><% foreach (array_values($packages) as $package): %>								<tr>
									<td class="title" style="vertical-align: middle"><% if (file_exists("resources/skin-images/{$package['skin']}-thumb.png")): %><a href="resources/skin-images/<%$package['skin']%>-preview.png" target="_blank" style="margin-right: 10px"><img src="resources/skin-images/<%$package['skin']%>-thumb.png" height="80" border="0" style="vertical-align: middle" /></a><% endif; %><%$package['name']%></td>
									<td class="author" style="vertical-align: middle"><a href="<%$package['url']%>" target="_blank"><%echo shortenAuthor($package['author']);%></a></td>
									<td class="date" style="vertical-align: middle"><%$package['version']%></td>
									<td class="active" style="text-align: center; vertical-align: middle"><input type="radio" name="active" value="<%$package['skinId']%>"<% if ($_POST['active'] == $package['skinId']): %> checked="checked"<% endif; %> /></td>
								</tr>
<% endforeach; %><% else: %>								<tr>
									<td class="title" colspan="5">No Skins Installed</td>
								</tr>
<% endif; %>
							</tbody>
						</table>
						<!-- table action -->
						<div class="action">
							<div class="button">
								<input type="submit" name="submit" value="Activate Selected" />
							</div>
						</div>
						<!-- end table action -->
						</fh:form>
					</div>
				</div>
				<!-- end table -->