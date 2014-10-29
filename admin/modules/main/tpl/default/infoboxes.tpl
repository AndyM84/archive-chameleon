						<div class="messages">
<% if (isset($errors)): %>							<div id="message-error" class="message message-error">
								<div class="image">
									<img src="resources/images/icons/error.png" alt="Error" height="32" />
								</div>
								<div class="text">
									<h6>Error Message(s)</h6>
									<span><%$errors%></span>
								</div>
								<div class="dismiss">
									<a href="#message-error"></a>
								</div>
							</div>
<% endif; %>
<% if (isset($warnings)): %>							<div id="message-warning" class="message message-warning">
								<div class="image">
									<img src="resources/images/icons/warning.png" alt="Warning" height="32" />
								</div>
								<div class="text">
									<h6>Warning Message(s)</h6>
									<span><%$warnings%></span>
								</div>
								<div class="dismiss">
									<a href="#message-warning"></a>
								</div>
							</div>
<% endif; %>
<% if (isset($notices)): %>							<div id="message-notice" class="message message-notice">
								<div class="image">
									<img src="resources/images/icons/notice.png" alt="Notice" height="32" />
								</div>
								<div class="text">
									<h6>Notice Message(s)</h6>
									<span><%$notices%></span>
								</div>
								<div class="dismiss">
									<a href="#message-notice"></a>
								</div>
							</div>
<% endif; %>
<% if (isset($successes)): %>							<div id="message-success" class="message message-success">
								<div class="image">
									<img src="resources/images/icons/success.png" alt="Success" height="32" />
								</div>
								<div class="text">
									<h6>Success Message(s)</h6>
									<span><%$successes%></span>
								</div>
								<div class="dismiss">
									<a href="#message-success"></a>
								</div>
							</div>
<% endif; %>

						</div>