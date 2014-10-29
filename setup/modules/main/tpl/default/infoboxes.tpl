<link rel="stylesheet" href="../resources/stylesheets/default/infoboxes.css" type="text/css" />

<% if (isset($notices)): %><div class="infoboxes_info"><%$notices%></div><% endif; %>
<% if (isset($successes)): %><div class="infoboxes_success"><%$successes%></div><% endif; %>
<% if (isset($warnings)): %><div class="infoboxes_warning"><%$warnings%></div><% endif; %>
<% if (isset($errors)): %><div class="infoboxes_error"><%$errors%></div><% endif; %>