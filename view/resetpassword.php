<?php include('header.php') ?>
<main class="form-container">
	<form class="form" action="/resetpassword/request" method="POST">
		<div class="lock-image-container">
			<i class="material-icons lock-reset">lock_reset</i>
		</div>
		<h3 style="text-align: center;">Camagru Password Reset</h3>
		<div class="form_message form_message_error"><?php if (isset($res['message'])) echo $res['message'] ?></div>
		<div class="form_input_group">
			<input type="password" name="password" class="form_input" placeholder="New Password" autocomplete="off">
			<div class="form_input_error_message"><?php if (isset($res['messagePassword'])) echo $res['messagePassword'] ?></div>
		</div>
		<div class="form_input_group">
			<input type="password" name="passwordConfirm" class="form_input" placeholder="Confirm Password" autocomplete="off">
			<div class="form_input_error_message"><?php if (isset($res['messagePasswordConfirm'])) echo $res['messagePasswordConfirm'] ?></div>
		</div>
		<div class="button_container">
			<button type="button" class="form_button cancel" id="backToLogin">Cancel</button>
			<button type="submit" class="form_button" name="request" value="resetPasswordAction">Submit</button>
		</div>
	</form>
</main>
<?php
	if (isset($popup))
		echo $popup;
?>
<?php include('footer.php') ?>