<?php include('header.php') ?>
<main class="form-container" id="formLogin">
	<form class="form" action="" method="POST">
		<h1 class="title">Camagru</h1>
		<div class="form_message form_message_error"><?php echo (isset($res['error']) ? $res['error'] : "") ?></div>
		<div class="form_input_group">
			<input type="text" name="username" class="form_input" autofocus placeholder="Username" autocomplete="off" value="<?php echo (isset($username) ? $username : "") ?>">
			<div class="form_input_error_message"><?php echo (isset($res['username']) ? $res['username'] : "") ?></div>
		</div>
		<div class="form_input_group">
			<input type="password" name="password" class="form_input" placeholder="Password" autocomplete="off">
			<div class="form_input_error_message"><?php echo (isset($res['password']) ? $res['password'] : "") ?></div>
		</div>
		<button class="form_button" name="loginSubmit" type="submit">Login</button>
		<div class="seperator"><div></div><div>OR</div><div></div></div>
		<div>
			<a class="form__link" href="forgotpassword">Forgot password?</a>
		</div>
	</form>
	<div>
		<p>Don't have an account?</p><a class="form__link" href="signup" id="linkCreateAccount" draggable="false">Sign up!</a>
	</div>
</main>
<?php include('footer.php') ?>