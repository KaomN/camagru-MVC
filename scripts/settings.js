const userDetails = {email: "",  id: "", username: "", verified: "", notification: ""};
const formOpen = {name: false, username: false, email: false, password: false, notification: false};
const isEnabled = {nameSubmit: false, usernameSubmit: false, emailSubmit: false, passwordSubmit: false};
// Show error message
function showInputError(inputElement, message) {
	inputElement.classList.remove('form_input_success_message');
	inputElement.classList.add('form_input_error_message');
	inputElement.textContent = message;
}
// Show success message
function showInputSuccess(inputElement, message) {
	inputElement.classList.add('form_input_success_message');
	inputElement.classList.remove('form_input_error_message');
	inputElement.textContent = message;
}
// Clear error message
function clearInputMessage(inputElement) {
	inputElement.textContent = "";
}
// Toggles form
function toggleForm(form, name) {
	if (!formOpen[name]) {
		form.classList.remove('hidden');
		formOpen[name] = true;
	} else {
		form.classList.add('hidden');
		formOpen[name] = false;
	}
}
// Toggles notification button
function toggleNotification(toggleHide, toggleShow) {
	toggleHide.classList.add('hidden');
	toggleShow.classList.remove('hidden');
}
// enables check button
function enableButton(button, key) {
	button.classList.add('enabled');
	button.classList.remove('disabled');
	isEnabled[key] = true;
}
// diables check button
function disableButton(button, key) {
	button.classList.remove('enabled');
	button.classList.add('disabled');
	isEnabled[key] = false;
}

document.addEventListener("DOMContentLoaded", function() {
	// Textboxes
	const textboxUsername = document.getElementById('username');
	const textboxEmail = document.getElementById('email');
	const textboxPasswordCurrent = document.getElementById('passwordCurrent');
	const textboxPasswordNew = document.getElementById('passwordNew');
	const textboxPasswordConfirm = document.getElementById('passwordConfirm');
	// Form Toggle buttons
	const usernameFormButton = document.getElementsByClassName('edit-username')[0]
	const emailFormButton = document.getElementsByClassName('edit-email')[0]
	const passwordFormButton = document.getElementsByClassName('edit-password')[0]
	const notificationFormButton = document.getElementsByClassName('edit-notification')[0]
	// Forms
	const usernameForm= document.getElementsByClassName('edit-container')[0];
	const emailForm = document.getElementsByClassName('edit-container')[1];
	const passwordForm= document.getElementsByClassName('edit-container')[2];
	const notificationForm = document.getElementsByClassName('edit-container')[3];
	// Form submit buttons
	const notificationOff = document.getElementsByClassName('check-toggle_off')[0];
	const notificationOn = document.getElementsByClassName('check-toggle_on')[0];
	const usernameSubmit = document.getElementsByClassName('check-username')[0];
	const emailSubmit = document.getElementsByClassName('check-email')[0];
	const passwordSubmit = document.getElementsByClassName('check-password')[0];
	// Show error text
	const errorUsername = document.getElementById('errorUsername');
	const errorCurrentPassword = document.getElementById('errorCurrentPassword');
	const errorNewPassword = document.getElementById('errorNewPassword');
	const errorConfirmPassword = document.getElementById('errorConfirmPassword');
	// Show Fetch message
	const usernameMessage = document.getElementById('usernameMessage');
	const emailMessage = document.getElementById('emailMessage');
	const passwordMessage = document.getElementById('passwordMessage');
	const notificationMessage = document.getElementById('notificationMessage');
	// toggles forms
	usernameFormButton.onclick = function() {
		toggleForm(usernameForm, "username");
	}
	emailFormButton.onclick = function() {
		toggleForm(emailForm, "email");
	}
	passwordFormButton.onclick = function() {
		toggleForm(passwordForm, "password");
	}
	notificationFormButton.onclick = function() {
		toggleForm(notificationForm, "notification");
	}

	// Clears messages and activates the check button
	textboxUsername.oninput = function() {
		clearInputMessage(usernameMessage);
		clearInputMessage(errorUsername);
		const re = /^[a-zA-Z0-9\-\_]+$/;
		if (textboxUsername.value.trim() != "" && re.test(textboxUsername.value) && textboxUsername.value.length >= 4 && textboxUsername.value.length <= 20) {
			enableButton(usernameSubmit, 'usernameSubmit');
		} else {
			if (!re.test(textboxUsername.value) && textboxUsername.value != "") {
				showInputError(errorUsername, "Username can only contain 'a-z', '0-9', '-' and '_'");
			}
			disableButton(usernameSubmit, 'usernameSubmit');
		}
	}
	//Change username
	usernameSubmit.onclick = async function() {
		if (!isEnabled.usernameSubmit)
			return ;
		const formData = new FormData();
		formData.append('request', "updateUsername");
		formData.append('username', textboxUsername.value);
		let response = await fetch('/settings/request', {
			method: 'POST',
			body: formData
		});
		try {
			response = await response.json();
			console.log(response)
			if (response.status) {
				disableButton(usernameSubmit, 'usernameSubmit');
				document.getElementById('spanUsername').innerHTML = textboxUsername.value;
				textboxUsername.value = "";
				showInputSuccess(usernameMessage, response.message);
			} else {
				showInputError(errorUsername, response.message);
			}
			setTimeout(function(){
				clearInputMessage(usernameMessage);
				clearInputMessage(errorUsername);
			}, 3000);
		} catch(e) {
			alert("Oops, Something went wrong!")
		}
	}
	// Clears messages and activates the check button
	textboxEmail.oninput = function() {
		clearInputMessage(emailMessage);
		if (textboxEmail.value != "")
			enableButton(emailSubmit);
		else
			disableButton(emailSubmit);
	}
	//Sends email to user to modify email address
	emailSubmit.onclick = async function() {
		const formData = new FormData();
		formData.append('request', 'updateEmail');
		formData.append('email', document.getElementById('email').value);
		let response = await fetch('/settings/request', {
			method: 'POST',
			body: formData
		});
		try {
			response = await response.json();
			if (response.status) {
				disableButton(emailSubmit, 'emailSubmit');
				showInputSuccess(emailMessage, response.message);
			} else {
				showInputError(emailMessage, response.message);
			}
			setTimeout(function(){
				clearInputMessage(emailMessage);
			}, 15000);
		} catch(e) {
			alert("Oops, Something went wrong!")
		}
	}
	// check password meets minimum security
	function checkPassword() {
		// if (textboxPasswordCurrent.value.length >= 8 && textboxPasswordNew.value.length >= 8 && textboxPasswordConfirm.value.length >= 8
		// 	&& textboxPasswordCurrent.value != "" && textboxPasswordNew.value != "" && textboxPasswordConfirm.value != "")
		// 	return true;
		// return false;

		//TESTING purposes
		return true;
	}
	// Clears messages and activates the check button
	textboxPasswordCurrent.oninput = function() {
		clearInputMessage(passwordMessage);
		clearInputMessage(errorCurrentPassword);
		if (checkPassword())
			enableButton(passwordSubmit, "passwordSubmit");
		else
			disableButton(passwordSubmit, "passwordSubmit");
	}
	// Clears messages and activates the check button
	textboxPasswordNew.oninput = function() {
		clearInputMessage(passwordMessage);
		clearInputMessage(errorNewPassword);
		if (checkPassword())
			enableButton(passwordSubmit, "passwordSubmit");
		else
			disableButton(passwordSubmit, "passwordSubmit");
	}
	// Clears messages and activates the check button
	textboxPasswordConfirm.oninput = function() {
		clearInputMessage(passwordMessage);
		clearInputMessage(errorConfirmPassword);
		if (checkPassword())
			enableButton(passwordSubmit, "passwordSubmit");
		else
			disableButton(passwordSubmit, "passwordSubmit");
	}
	// Change Password
	passwordSubmit.onclick = async function() {
		if (!isEnabled.passwordSubmit)
			return;
		// const re = /\d|[A-Z]/;
		// if (textboxPasswordNew.value != textboxPasswordConfirm.value || textboxPasswordNew.value.length > 255 || textboxPasswordConfirm.value.length > 255 || textboxPasswordNew.value.length < 8 || !re.test(textboxPasswordNew.value)) {
		// 	if (textboxPasswordNew.value.length < 8)
		// 		showInputError(errorNewPassword, "Password minimum length of 8!");
		// 	else if (!re.test(textboxPasswordNew.value))
		// 		showInputError(errorNewPassword, "Password needs to include atleast an uppercase letter or number!");
		// 	else if (textboxPasswordNew.value.length > 255)
		// 		showInputError(errorNewPassword, "Password needs to be shorter than 255 characters!");
		// 	else if (textboxPasswordNew.value != textboxPasswordConfirm.value) {
		// 		showInputError(errorConfirmPassword, "Password did not match with Confirmation!");
		// 		showInputError(errorNewPassword, "Password did not match with Confirmation!");
		// 	}
		// } 
		else {
			if (!isEnabled.passwordSubmit)
				return ;
			const formData = new FormData();
			formData.append('request', 'updatePassword');
			formData.append('currentPassword', textboxPasswordCurrent.value);
			formData.append('newPassword', textboxPasswordNew.value);
			formData.append('confirmPassword', textboxPasswordConfirm.value);
			let response = await fetch('/settings/request', {
				method: 'POST',
				body: formData
			});
			try {
				response = await response.json();
				if (response.status) {
					textboxPasswordCurrent.value = "";
					textboxPasswordNew.value = "";
					textboxPasswordConfirm.value = "";
					showInputSuccess(passwordMessage, response.message);
				} else {
					if (response.error === "wrong")
						showInputError(errorCurrentPassword, response.message);
					else if (response.error === "match")
						showInputError(passwordMessage, response.message);
					else if (response.error === "short")
						showInputError(passwordMessage, response.message);
					else if (response.error === "long")
						showInputError(passwordMessage, response.message);
					else if (response.error === "complex")
						showInputError(passwordMessage, response.message);
					else if (response.error === "empty")
						showInputError(errorCurrentPassword, response.message);
					else if (response.error === "emptyNp")
						showInputError(errorNewPassword, response.message);
					else if (response.error === "emptyCp")
						showInputError(errorConfirmPassword, response.message);
					else
						showInputError(passwordMessage, response.message);
				}
				setTimeout(function(){
					clearInputMessage(passwordMessage);
				}, 3000);
			} catch(e) {
				alert("Oops, Something went wrong!")
			}
		}
	}
	// Notification on button
	notificationOn.onclick = async function() {
		const formData = new FormData();
		formData.append('request', 'notificationOff');
		let response = await fetch('/settings/request', {
			method: 'POST',
			body: formData
		});
		try {
			response = await response.json();
			if (response.status) {
				toggleNotification(notificationOn, notificationOff);
				showInputSuccess(notificationMessage, response.message);
				setTimeout(function(){
					clearInputMessage(notificationMessage);
			}, 3000);
			} else {
				showInputError(notificationMessage, response.message);
			}
		} catch(e) {
			alert("Oops, Something went wrong!")
		}

	}
	// Notification off button
	notificationOff.onclick = async function() {
		const formData = new FormData();
		formData.append('request', 'notificationOn');
		let response = await fetch('/settings/request', {
			method: 'POST',
			body: formData
		});
		try {
			response = await response.json();
			if (response.status) {
				toggleNotification(notificationOff, notificationOn);
				showInputSuccess(notificationMessage, response.message);
				setTimeout(function(){
					clearInputMessage(notificationMessage);
				}, 3000);
			} else {
				showInputError(notificationMessage, response.message);
			}
		} catch(e) {
			alert("Oops, Something went wrong!")
		}
	}
});