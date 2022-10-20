<?php include('header.php') ?>
<main class="form-container" id="formLogin">
	<form class="form" action="/email/request" method="POST">
		<div class="lock-image-container">
			<i class="material-icons lock">alternate_email</i>
		</div>
		<h3 class="title">Email change request</h3>
		<center><p>Enter the pin code that was sent to you</p></center>
		<div class="form_message_error"></div>
		<div class="form_input_group">
			<input name="pin" type="text" class="form_input" placeholder="Pin" autocomplete="off">
			<div class="<?php if (isset($res['status'])) echo $res['status'] === false ? "form_input_error_message" : "form_message_success"; else echo "form_input_error_message"; ?>"><?php if (isset($res['message'])) echo $res['message'] ?></div>
		</div>
		<div class="button_container2">
			<button type="submit" class="form_button2" name="request" value="emailChangeAction">Submit</button>
		</div>
	</form>
	<a class="back__login" href="login" draggable="false"><span>Back To Login</span></a>
</main>
<?php
if (isset($res))
	var_dump($res)
?>
<?php include('footer.php') ?>