<% if (!$isSubMenu): %><li>
							<a href="<%$item->href%>"<% if ($item->target != ''): %> target="<%$item->target%>"<% endif; %><%$item->other%>><% if (!isset($item->icon)): %><span class="normal"><%$item->text%></span><% else: %><span class="icon"><img src="<%$item->icon['src']%>" alt="<%$item->icon['alt']%>" /></span><span><%$item->text%></span><% endif; %></a><%$subMenu%>

						</li>
<% else: %>								<li<% if ($item->last === true): %> class="last"<% endif; %>><a href="<%$item->href%>"<% if ($item->target != ''): %> target="<%$item->target%>"<% endif; %><% if ($subMenu != ' '):%> class="childs"<% endif; %><%$item->other%>><%$item->text%></a><%$subMenu%></li>
<% endif; %>