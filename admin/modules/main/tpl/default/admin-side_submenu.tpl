<% if (!$isNested): %>
<%$menuItems%>
<% else: %>

							<ul class="<% if ($activeParent): %>expanded<% else: %>collapsed<% endif; %>">
<%$menuItems%>
							</ul>
<% endif; %>