<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>How to Create a QRCode using Google QRCode API</title>
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container">
	<h1 class="page-header text-center">QRCode using Google QRCode API</h1>
	<div class="row">
		<div class="col-sm-3 col-sm-offset-3">
			<form method="POST">
				<div class="form-group">
					<label for="">Text to Convert to QRCode</label>
					<input type="text" class="form-control" name="text_code">
				</div>
				<button type="submit" class="btn btn-primary" name="generate">Generate QRCode</button>
			</form>
		</div>
		<div class="col-sm-3">
			<?php
					//$code = "Welcoem to Shonu Galaxy. click on this link https://www.youtube.com/@shonusgalaxy";
					$code = "Shonu Galaxy stumble guys. Skins, emotes, maps and animations";
					echo "
						<img src='https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$code&choe=UTF-8'>
					";
				
			?>
		</div>
	</div>
</div>
</body>
</html>