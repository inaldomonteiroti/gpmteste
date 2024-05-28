<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="favicon.ico">
		<title>GPMTESTE - Solução em Produtos e Serviços</title>
		<!-- Bootstrap core CSS -->
		<link href="<?php echo base_url(); ?>public/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<!-- Custom styles for this template -->
		<link href="<?php echo base_url(); ?>public/css/owl.carousel.css" rel="stylesheet">
		<link href="<?php echo base_url(); ?>public/css/owl.theme.default.min.css"  rel="stylesheet">
		<link href="<?php echo base_url(); ?>public/css/style.css" rel="stylesheet">
		<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
		<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
		<script src="<?php echo base_url(); ?>public/js/ie-emulation-modes-warning.js"></script>
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- Estilos customizados passados como parametro pelo controller -->
		<!-- onde style_name é o nome do aquivo passado no controller -->
		<?php if (isset($styles)) {
			foreach ($styles as $style_name) {
				$href = base_url() . "public/css/" . $style_name; ?>
				<link href="<?=$href // echo?>" rel="stylesheet"> 
			<?php }
		} ?>
		
	</head>
	<body id="page-top">
		<!-- Navigation -->
		<nav class="navbar navbar-default navbar-shrink navbar-fixed-top">
			<div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header page-scroll">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand page-scroll" href="#page-top" style="margin-top: 10px;"><img src="<?php echo base_url(); ?>public/images/logo.png" alt="GPMTESTE"></a>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				
				<!-- /.navbar-collapse -->
			</div>
			<!-- /.container-fluid -->
		</nav>
