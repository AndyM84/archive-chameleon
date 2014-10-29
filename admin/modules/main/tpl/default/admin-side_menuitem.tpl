<% if (!$isSubMenu && !$isNested): %>
					<h6 id="h-menu-<%$item->key%>"<% if ($item->active): %> class="selected"<% endif; %>><a href="<%$item->href%>"<% if ($item->target != ''): %> target="<%$item->target%>"<% endif; %><%$item->other%>><span><%$item->text%></span></a></h6>
					<ul id="menu-<%$item->key%>" class="<% if ($item->active): %>opened<% else: %>closed<% endif; %>">
<%$subMenu%>
					</ul>
<% elseif ($isSubMenu && !$isNested): %>
						<li<% if ($subMenu != ' ' || $item->last): %> class="<% if ($subMenu != ' '): %>collapsible <% endif; %><% if ($item->last): %>last<% endif; %>"<% endif; %>>
							<a href="<% if ($subMenu == ' '): %><%$item->href%><% else: %>#<% endif; %>"<% if ($subMenu != ' '): %> class="<% if ($item->active): %>minus<% else: %>plus<% endif; %>"<% endif; %><% if ($item->target != ''): %> target="<%$item->target%>"<% endif; %><%$item->other%>><%$item->text%></a><%$subMenu%>

						</li>
<% else: %>
								<li<% if ($item->last): %> class="last"<% endif; %>><a href="<%$item->href%>"<% if ($item->target != ''): %> target="<%$item->target%>"<% endif; %><%$item->other%>><%$item->text%></a></li>
<% endif; %>