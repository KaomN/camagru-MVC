<?php include('header.php') ?>
<main>
	<div class="form-container">
		<div class="form-show-container">
			<h4 class="h4-username">Username: </h4>
			<span id="spanUsername"><?php echo $_SESSION['username']; ?></span>
			<i class="material-icons edit-username enabled" title="edit">edit</i>
		</div>
		<div class="edit-container hidden">
				<div class="edit-name-form">
					<div>
						<input type="text" placeholder="Username" name="username" id="username" autocomplete="off">
					</div>
					<div class="form_input_error_message" id="errorUsername"></div>
					<div class="message-container">
						<div class="form_input_error_message" id="usernameMessage"><?php echo (isset($res['message']) ? $res['message'] : ""); ?></div>
					</div>
				</div>
				<div class="btn-container">
					<i class="material-icons check-username disabled" title="Submit">check</i>
				</div>
		</div>
	</div>
	<div class="form-container">
		<div class="form-show-container">
			<h4 class="h4-email">Email:</h4>
			<span id="spanEmail"><?php echo $_SESSION['email']; ?></span>
			<i class="material-icons edit-email enabled" title="edit">edit</i>
		</div>
		<div class="edit-container hidden">
			<div class="edit-name-form">
				<div>
					<input type="text" placeholder="Email" id="email" autocomplete="off">
				</div>
				<div class="message-container">
					<div class="form_input_error_message" id="emailMessage"></div>
				</div>
			</div>
			<div class="btn-container">
				<i class="material-icons check-email disabled" title="Submit">check</i>
			</div>
		</div>
	</div>
	<div class="form-container">
		<div class="form-show-container">
			<h4 class="h4-password">Password</h4>
			<i class="material-icons edit-password enabled" title="edit">edit</i>
		</div>
		<div class="edit-container hidden">
			<div class="edit-name-form">
				<div>
					<input type="password" placeholder="Current Password" id="passwordCurrent">
				</div>
				<div class="form_input_error_message" id="errorCurrentPassword"></div>
				<div>
					<input type="password" placeholder="New Password" id="passwordNew">
				</div>
				<div class="form_input_error_message" id="errorNewPassword"></div>
				<div>
					<input type="password" placeholder="Confirm password" id="passwordConfirm">
				</div>
				<div class="form_input_error_message" id="errorConfirmPassword"></div>
				<div class="message-container">
					<div class="form_input_error_message" id="passwordMessage"></div>
				</div>
			</div>
			<div class="btn-container">
				<i class="material-icons check-password disabled" title="Submit">check</i>
			</div>
		</div>
	</div>
	<div class="form-container">
		<div class="form-show-container">
			<h4 class="h4-notification">Notifications</h4>
			<i class="material-icons edit-notification enabled" title="edit">edit</i>
		</div>
		<div class="edit-container hidden">
			<div class="edit-name-form">
				<div class="email">
					<div class>
						<h4>Email Notification</h4>
					</div>
					<i class="material-icons check-toggle_on enabled <?php echo ($_SESSION['notification'] ? '' : 'hidden'); ?>" title="Enable">toggle_on</i>
					<i class="material-icons check-toggle_off enabled <?php echo ($_SESSION['notification'] ? 'hidden' : ''); ?>" title="Disable">toggle_off</i>
				</div>
				<div class="message-container">
					<div class="form_input_error_message" id="notificationMessage"></div>
				</div>
			</div>
		</div>
	</div>

</main>
<?php include('footer.php') ?>