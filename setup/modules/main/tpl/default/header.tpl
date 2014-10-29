<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Chameleon Setup</title>

		<link href="resources/Styles/Site.css" rel="Stylesheet" type="text/css" />

		<script type="text/javascript" src="../resources/javascript/default/jquery.min.js"></script>
	</head>
	<body>
		<div id="page">
			<div id="header"></div>
			<div id="content">
				<div id="top"></div>
				<div id="body">
					<div id="left">
						<ul>
							<li <% if (!isset($_REQUEST['page']) || $_REQUEST['page'] == 'step1'): %>class="active"<% endif; %>>
								<h2>Configuration</h2>
								<p>Configure your installation</p>
							</li>
							<li <% if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'step2'): %>class="active"<% endif; %>>
								<h2>Install</h2>
								<p>Perform installation</p>
							</li>
							<li <% if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'step3'): %>class="active"<% endif; %>>
								<h2>Finished</h2>
								<p>Installation report and use instructions</p>
							</li>
						</ul>
					</div>
					<div id="right">
						<div class="pad">