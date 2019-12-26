<?php
	session_start();
?>
<!doctype html>
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>SH4 Compatibility Tool</title>
		<link rel="canonical" href="http://wiki.planet-casio.com/tools/SH4compatibility"/>
		<link rel="stylesheet" type="text/css" href="system/style.css?100313" />
		<script type="text/javascript">//Google analytics
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-39078814-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	</head>
	<body>
		<div id="headerBar">
			<a class="Cell CellL" href="http://wiki.planet-casio.com/tools/SH4compatibility">
				<h1>SH4 Compatibility Tool</h1>
			</a>
			<a class="Cell CellR casiopeiaLink" href="http://www.casiopeia.net/forum/">
				<strong>Casiopeia</strong>
			</a>
			<a class="Cell CellR PClink" href="http://www.planet-casio.com/Fr/">
				<strong>Planète Casio</strong>
			</a>
			<a class="Cell CellR" href="http://wiki.planet-casio.com/">
				<strong>Casio Universal Wiki</strong>
			</a>
			<?php
				if(!empty($_SESSION['msg_text']) && !empty($_SESSION['msg_color']))
				{
					echo '<div style="color:'.$_SESSION['msg_color'].'">'.$_SESSION['msg_text'].'</div>';
					$_SESSION['msg_text']='';
				}
			?>
		</div>
		<div id="loadBox" style="display:block;font-size:14px;">
			<form method="post" action="system/converter.php" enctype="multipart/form-data">
				<h4 style="font-size:16px;">Make your g1a compatible for casio SH4 (Power Graphic 2)</h4>
				<input type="file" name="file" accept=".g1a"/><br/>
				<label><input type="checkbox" name="slow" checked="checked"/> Slow down modified function to simulate the old calculators (recommended)</label><br/>
				<input type="submit"/><br/>
				<span style="color:red;font-weight:bold;">Warning! This tool can cause damage on your calculator! Use it at your own risk. Planet-Casio, Casiopeia and me will not be responsible for any damage.</span>
			</form>
		</div>
		<div id="footer">
			<i>Le compatibidule, et vos add-ins pullulent !</i><br />
			Developed by Ziqumu for <a href="http://www.planet-casio.com/Fr/">Planete Casio</a> and <a href='http://www.casiopeia.net/'>Casiopeia</a>
		</div>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	</body>
</html>
