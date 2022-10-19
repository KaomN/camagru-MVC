<?php include('header.php') ?>
<main class="form-container" id="formLogin">
	<form class="form" action="/forgotpassword/request" method="POST">
		<div class="lock-image-container">
			<i class="material-icons lock">lock</i>
		</div>
		<h3 class="title">Trouble Logging in?</h3>
		<p>Enter your email and we'll send you a link to get back into your account.</p>
		<div class="form_message_error"></div>
		<div class="form_input_group">
			<input name="email" type="text" id="forgotPasswordEmail" class="form_input" placeholder="Email" autocomplete="off" value="<?php if (isset($email)) echo $email ?>">
			<div class="<?php if (isset($res['status'])) echo $res['status'] === false ? "form_input_error_message" : "form_message_success" ?>"><?php if (isset($res['message'])) echo $res['message'] ?></div>
		</div>
		<div class="button_container">
			<button type="submit" class="form_button" name="request" value="forgotPasswordAction">Send Link</button>
		</div>
		<div class="seperator"><div></div><div>OR</div><div></div></div>
		<div>
			<a class="form__link" href="signup" draggable="false">Create New Account</a>
		</div>
	</form>
	<a class="back__login" href="login" draggable="false"><span>Back To Login</span></a>
</main>
<?php 
	if(isset($resRP))
		var_dump($resRP) 
?>
<?php include('footer.php') ?>