<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><%PageTitle%></title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<metainc tabsize="2" />

		<!-- stylesheets -->
		<cssinc baseurl="resources/css/" tabsize="2" cacheExpire="300" />
		<cs:location key="admin-headstyles" type="rawcss" />
		<link id="color" rel="stylesheet" type="text/css" href="resources/css/colors/<%admincolor%>.css" />
		<link rel="stylesheet" type="text/css" href="../resources/stylesheets/default/jquery-ui.css" />

		<!-- scripts (jquery) -->
		<script src="../resources/javascript/default/jquery.min.js" type="text/javascript"></script>
		<!--[if IE]><script language="javascript" type="text/javascript" src="resources/scripts/excanvas.min.js"></script><![endif]-->
		<script src="../resources/javascript/default/jquery-ui.min.js" type="text/javascript"></script>
		<jsinc baseurl="resources/scripts/" tabsize="2" cacheExpire="300" />
		<cs:location key="admin-headscripts" type="rawjs" />

		<!-- scripts (inline) -->
		<script type="text/javascript">
			$(document).ready(function () {
				style_path = "resources/css/colors";
				$("#date-picker").datepicker();
			});
		</script>
	</head>
	<body>
		<div id="colors-switcher" class="color">
			<a href="" class="blue" title="Blue"></a>
			<a href="" class="green" title="Green"></a>
			<a href="" class="brown" title="Brown"></a>
			<a href="" class="purple" title="Purple"></a>
			<a href="" class="red" title="Red"></a>
			<a href="" class="greyblue" title="GreyBlue"></a>
		</div>
		<!-- header -->
		<div id="header">
			<div id="header-outer">
				<!-- logo -->
				<div id="logo">
					<h1><a href="./" title="Chameleon Framework"><img src="resources/images/logo.png" alt="Chameleon Framework" /></a></h1>
				</div>
				<!-- end logo -->
				<cs:menu key="admin-user" />
				<div id="header-inner">
					<div id="home">
						<a href=""></a>
					</div>
					<cs:menu key="admin-quick" />
					<div class="corner tl"></div>
					<div class="corner tr"></div>
				</div>
			</div>
		</div>
		<!-- end header -->
		<!-- content -->
		<div id="content">
			<!-- end content / left -->
			<div id="left">
				<cs:menu key="admin-side" />
				<cs:location key="admin-side" />
			</div>
			<!-- end content / left -->
			<!-- content / right -->
			<div id="right">
				<cs:location key="admin-main" />
			</div>
			<!-- end content / right -->
		</div>
		<!-- end content -->
		<!-- footer -->
		<div id="footer">
			<p>Copyright &copy; 2011 Chameleon Sites. All Rights Reserved.</p>
		</div>
		<!-- end footert -->
	</body>
</html>