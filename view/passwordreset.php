<?php include('header.php') ?>
<main class="form-container">
	<form class="form">
		<div class="lock-image-container">
			<i class="material-icons lock-reset">lock_reset</i>
		</div>
		<h3 style="text-align: center;">Camagru Password Reset</h3>
		<div class="form_message form_message_error"></div>
		<div class="form_input_group">
			<input type="password" id="passwordReset" class="form_input" placeholder="New Password" autocomplete="off">
			<div class="form_input_error_message"></div>
		</div>
		<div class="form_input_group">
			<input type="password" id="passwordResetConfirm" class="form_input" placeholder="Confirm Password" autocomplete="off">
			<div class="form_input_error_message"></div>
		</div>
		<div class="button_container">
			<button type="button" class="form_button cancel" id="cancelResetBtn">Cancel</button>
			<button type="button" class="form_button" id="confirmResetBtn">Confirm</button>
		</div>
	</form>
</main>
<?php include('footer.php') ?>