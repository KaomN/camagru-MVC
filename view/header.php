<!DOCTYPE html>
<html lang="en">
<head>
	<base href="/"/>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/x-icon" href="src/favicon.ico">
	<title>Login / Sign Up Form</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@400;500;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="styles/header.css">
	<link rel="stylesheet" href="styles/footer.css">
	<?php echo $style; ?>
	<title>Camagru</title>
</head>
<body>
	<header>
		<p class="camagru">Camagru</p>
		<p id="userName"><?php echo (isset($_SESSION['username']) ? $_SESSION['username'] : ""); ?></p>
		<?php if(isset($_SESSION['id']) && $navbar) { ?>
		<nav>
			<ul>
				<li ><a href="logout" draggable="false"><i class="material-icons logout enabled" title="Logout">logout</i></a></li>
				<li><a href="settings" draggable="false"><i class="material-icons settings enabled" title="Settings">settings</i></a></li>
				<li><a href="profile" draggable="false"><i class="material-icons account_circle enabled" title="Profile">account_circle</i></a></li>
				<li><a href="upload" draggable="false"><i class="material-icons add_a_photo enabled" title="Add Picture">add_a_photo</i></a></li>
				<li><a href="gallery" draggable="false"><i class="material-icons gallery enabled" title="Gallery">image</i></a></li>
			</ul>
		</nav>
		<?php } else { ?>
			<nav>
				<ul style="justify-content: flex-start">
				<?php
					if (!isset($_SESSION['id']) && $_SERVER['REQUEST_URI'] === "/gallery") { ?>
						<li><a href="login" draggable="false"><i class="material-icons login enabled" title="Login">login</i></a></li>
				<?php } else { ?>
						<li><a href="gallery" draggable="false"><i class="material-icons gallery" title="Gallery">image</i></a></li>
				<?php }
				} ?>
				</ul>
			</nav>
	</header>
