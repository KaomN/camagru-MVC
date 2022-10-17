<?php include('header.php') ?>
<main class="form-container" id="formLogin">
	<form class="form" action="" method="POST">
		<h1 class="title">Camagru</h1>
		<div class="form_message form_message_error"><?php echo (isset($res['error']) ? $res['error'] : "") ?></div>
		<div class="form_input_group">
			<input type="text" name="username" class="form_input" placeholder="Username" autocomplete="off" value="<?php if (isset($res['status']) && $res['status'] === false) echo (isset($username) ? $username : ""); ?>">
			<div class="form_input_error_message"><?php echo (isset($res['username']) ? $res['username'] : "") ?></div>
		</div>
		<div class="form_input_group">
			<input type="text" name="email" class="form_input" placeholder="Email" autocomplete="off" value="<?php if (isset($res['status']) && $res['status'] === false) echo (isset($email) ? $email : ""); ?>">
			<div class="form_input_error_message"><?php echo (isset($res['email']) ? $res['email'] : "") ?></div>
		</div>
		<div class="form_input_group">
			<input type="password" name="password" class="form_input" placeholder="Password" autocomplete="off" value="<?php if (isset($res['status']) && $res['status'] === false) echo (isset($password) ? $password : ""); ?>">
			<div class="form_input_error_message"><?php echo (isset($res['password']) ? $res['password'] : "") ?></div>
		</div>
		<div class="form_input_group">
			<input type="password" name="passwordConfirm" class="form_input" placeholder="Confirm Password" autocomplete="off" value="<?php if (isset($res['status']) && $res['status'] === false) echo (isset($passwordConfirm) ? $passwordConfirm : ""); ?>">
			<div class="form_input_error_message"><?php echo (isset($res['passwordConfirm']) ? $res['passwordConfirm'] : "") ?></div>
		</div>
		<button class="form_button" name="signupSubmit" type="submit">Sign up</button>
		<div class="seperator"><div></div><div>OR</div><div></div></div>
		<div>
			<a class="form__link" href="forgotpassword" draggable="false">Forgot password?</a>
		</div>
	</form>
	<div>
		<p>Already have an account?</p><a class="form__link" href="login" draggable="false">Log in!</a>
	</div>
	<div class="popup" style="<?php echo ($_SERVER['REQUEST_URI'] === "/signup/success" ? "display: block" : "display: none") ;?>">
		<div class="popup-content">
			<p>Thank you for signing up to Camagru!
				</br>An email has been sent to you for verification!
			</p>
			</br>
			<a href="login" draggable="false"><button class="form_button" type="button">Login!</button></a>
		</div>
	</div>
</main>
<?php include('footer.php') ?>