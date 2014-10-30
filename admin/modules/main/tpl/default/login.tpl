<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><%PageTitle%></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<metainc tabsize="2" />

	<!-- stylesheets -->
	<link rel="stylesheet" type="text/css" href="resources/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="resources/css/style.css" media="screen" />
	<link id="color" rel="stylesheet" type="text/css" href="resources/css/colors/<%admincolor%>.css" />
	<link rel="stylesheet" type="text/css" href="../resources/stylesheets/default/jquery-ui.css" />

	<!-- scripts (jquery) -->
	<script src="../resources/javascript/default/jquery.min.js" type="text/javascript"></script>
	<script src="../resources/javascript/default/jquery-ui.min.js" type="text/javascript"></script>
	<script src="resources/scripts/smooth.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			style_path = "resources/css/colors";

			$("input.focus").focus(function () {
				if (this.value == this.defaultValue) {
					this.value = "";
				}
				else {
					this.select();
				}
			});

			$("input.focus").blur(function () {
				if ($.trim(this.value) == "") {
					this.value = (this.defaultValue ? this.defaultValue : "");
				}
			});

			$("input:submit, input:reset").button();
		});
	</script>
</head>
<body>
<div id="login">
	<!-- login -->
	<div class="title">
		<h5>Sign In</h5>
		<div class="corner tl"></div>
		<div class="corner tr"></div>
	</div>
	<cs:infoboxes />
	<div class="inner">
		<fh:form name="login-form" action="./">
			<div class="form">
				<!-- fields -->
				<div class="fields">
					<div class="field">
						<div class="label">
							<label for="username">Username:</label>
						</div>
						<div class="input">
							<fh:textbox for="username" id="username" size="40" class="focus" />
						</div>
					</div>
					<div class="field">
						<div class="label">
							<label for="password">Password:</label>
						</div>
						<div class="input">
							<fh:password for="password" id="password" size="40" class="focus" />
						</div>
					</div>
					<div class="field">
						<div class="checkbox">
							<fh:checkbox for="remember" id="remember" value="true" />
							<label for="remember">Remember me</label>
						</div>
					</div>
					<div class="buttons">
						<input type="submit" value="Sign In" />
					</div>
				</div>
				<!-- end fields -->
				<!-- links -->
				<div class="links">
					<a href="./?nmod=main&page=forgot">Forgot your password?</a>
				</div>
				<!-- end links -->
			</div>
		</fh:form>
	</div>
	<!-- end login -->
	<div id="colors-switcher" class="color">
		<a href="" class="blue" title="Blue"></a>
		<a href="" class="green" title="Green"></a>
		<a href="" class="brown" title="Brown"></a>
		<a href="" class="purple" title="Purple"></a>
		<a href="" class="red" title="Red"></a>
		<a href="" class="greyblue" title="GreyBlue"></a>
	</div>
</div>
</body>
</html>