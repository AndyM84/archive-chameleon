

						<li<% if ($item->active): %> class="selected"<% endif; %>><a href="<%$item->href%>"<% if ($item->target != ''): %> target="<%$item->target%>"<% endif; %><%$item->other%>><%$item->text%></a></li>