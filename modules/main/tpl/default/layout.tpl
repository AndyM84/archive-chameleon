<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><%PageTitle%></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<metainc tabsize="2" />

		<link rel="stylesheet" href="resources/stylesheets/default/styles.css" type="text/css" />
		<link rel="stylesheet" href="resources/stylesheets/default/jquery-ui.css" type="text/css" />
		<cssinc baseurl="resources/stylesheets/default/" tabsize="2" compress="true" combine="true" cacheExpire="300" />

		<script type="text/javascript" src="resources/javascript/default/jquery.min.js"></script>
		<script type="text/javascript" src="resources/javascript/default/jquery-ui.min.js"></script>
		<jsinc baseurl="resources/javascript/default/" tabsize="2" compress="true" combine="true" minify="true" cacheExpire="300" />
	</head>
	<body>
		<div class="wrapper">
			<div id="container">
				<div id="header">
					<h1><a href="./">Your Website</a></h1>
					<h2>powered by chameleon & n2framework</h2>
				</div>
				<cs:menu key="front-nav" />
				<div id="page-intro">
					<h2>A Simple Website</h2>
					<p>Simple, yet powerful.  Your site is now running on top of one of the fastest and most capable site frameworks available today.</p>
				</div>
				<div id="body">
					<div id="content">
						<cs:location key="front-main" />
					</div>

					<div class="sidebar">
						<ul>
							<cs:location key="front-sidebar" />
						</ul>
						<div class="sidebar-end"></div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div id="footer">
				<div class="footer-content">
					<cs:location key="front-footer" />
					<div class="clear"></div>
				</div>
				<div id="footer-links">
					<p>
						&copy; YourSite <%CopyrightYear%>. Design by <a href="http://www.spyka.net">spyka.net</a> |
						Built on <a href="http://chameleon-sites.com" target="_blank">Chameleon</a> &
						<a href="http://n2framework.com/" target="_blank">N2 Framework</a>
					</p>
				</div>
			</div>
		</div>
	</body>
</html>