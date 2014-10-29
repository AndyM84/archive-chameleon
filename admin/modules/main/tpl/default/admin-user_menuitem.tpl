

					<li<% if ($item->first || $item->last): %> class="<% if ($item->first && !$item->last): %>first<% else: %>last highlight<% endif; %>"<% endif; %>><a href="<%$item->href%>"<% if ($item->target != ''): %> target="<%$item->target%>"<% endif; %><%$item->other%>><%$item->text%></a></li>