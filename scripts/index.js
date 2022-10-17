// const signupPopup = document.getElementById("signup");
// const queryString = window.location.search;
// const urlParams = new URLSearchParams(queryString);
// // Forms
// const loginForm = document.getElementById('formLogin');
// const signupForm = document.getElementById('formSignup');
// const forgotPasswordForm = document.getElementById('formForgotPassword');
// const passwordResetForm = document.getElementById('passwordResetForm');
// // Forgot Password Variables
// const forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
// const forgotPasswordLogin = document.getElementById("linkForgotPasswordL");
// const forgotPasswordSignup= document.getElementById("linkForgotPasswordS");
// const forgotPasswordBtnSend = document.getElementById("sendBtn");
// // Password reset Variables
// const passwordResetBtnCancel = document.getElementById('cancelResetBtn');
// const passwordResetBtnConfirm = document.getElementById('confirmResetBtn');
// const passwordResetPopup = document.getElementById('passwordResetPopup');
// // Veify popup
// const verifyPopup = document.getElementById('verifyAccountPopup');

// Show error message in top
function setFormMessage(formElement, type, message) {
	//const messageElement = formElement.querySelector(".form_message");
	formElement.textContent = message;
	formElement.classList.remove("form_message_success", "form_message_error");
	formElement.classList.add(`form_message_${type}`);
}
// Show error message under input
function setInputError(inputElement, message) {
	inputElement.classList.add("form_input_error");
	inputElement.parentElement.querySelector(".form_input_error_message").textContent = message;
}
// Clear error message
function clearInputError(inputElement) {
	inputElement.classList.remove("form_input_error");
	inputElement.parentElement.querySelector(".form_input_error_message").textContent = "";
	setFormMessage(inputElement.closest('form').querySelector('.form_message_error'), "error", "")
}
// Check Login form inputs
// function loginCheckInput() {
// 	if (document.getElementById("login_username").value == "")
// 		setInputError(document.getElementById("login_username"), "Username required!");
// 	if (document.getElementById("login_passwd").value == "")
// 		setInputError(document.getElementById("login_passwd"), "Password required!");
// 	for (let x = 0; x < loginForm.length; x++)
// 		if (loginForm[x].classList.contains('form_input_error'))
// 			return true;
// 	return false;
// }
// Checks Login fetch response
// function loginCheckData(response) {
// 	if (response.status) {
// 		window.location.href = window.location.protocol + "//" + window.location.host + "/camagru" + "/gallery.php";
// 	// Form error checks
// 	} else {
// 		if (response.username)
// 			setInputError(document.getElementById("login_username"), response.username);
// 		if (response.password)
// 			setInputError(document.getElementById("login_passwd"), response.password);
// 		if (!response.status && response.error === "verify") {
// 			verifyPopup.style.display = "block";
// 			document.getElementById('verifyMessage').innerHTML = response.message;
// 			window.onclick = function(event) {
// 				if (event.target == verifyPopup) {
// 					verifyPopup.style.display = "none";
// 				}
// 			}
// 		}
// 		else if (!response.status)
// 			setFormMessage(loginForm.querySelector('.form_message_error'), "error", response.message);
// 	}
// }
// // Fetch login response
// async function fetchLogin() {
// 	const formData = new FormData(loginForm.querySelector('form'));
// 	let response = await fetch('./scripts/php/login.php', {
// 		method: 'POST',
// 		body: formData
// 	});
// 	response = await response.json();
// 	loginCheckData(response);
// }
// Check signup form inputs
// function signupCheckInput() {
// 	const rePassword = /\d|[A-Z]/;
// 	const reUsername = /^[a-zA-Z0-9\-\_]+$/;
// 	if (document.getElementById("signup_username").value.trim() == "")
// 		setInputError(document.getElementById("signup_username"), "Username required!");
// 	else if(!reUsername.test(document.getElementById("signup_username").value))
// 		setInputError(document.getElementById("signup_username"), "Username only accepts 'a-z', '0-9', '-' and '_'");
// 	else if (document.getElementById("signup_username").value.length < 4)
// 		setInputError(document.getElementById("signup_username"), "Username minumim length of 4!");
// 	else if (document.getElementById("signup_username").value.length > 10)
// 		setInputError(document.getElementById("signup_username"), "Username maximum length 20!");
// 	if (document.getElementById("signup_email").value.trim() == "")
// 		setInputError(document.getElementById("signup_email"), "Email required!");
// 	if (document.getElementById("signup_passwd").value.trim() == "")
// 		setInputError(document.getElementById("signup_passwd"), "Password required!");
// 	// else if (document.getElementById("signup_passwd").value.length < 8)
// 	// 	setInputError(document.getElementById("signup_passwd"), "Password minimum length of 8!");
// 	// else if (document.getElementById("signup_passwd").value.length > 255)
// 	// 	setInputError(document.getElementById("signup_passwd"), "Password needs to be shorter than 255 characters!");
// 	// else if (!rePassword.test(document.getElementById("signup_passwd").value))
// 	// 	setInputError(document.getElementById("signup_passwd"), "Password needs to include atleast an uppercase letter or number!");
// 	else if (document.getElementById("signup_passwd").value != document.getElementById("signup_confirm_passwd").value) {
// 		setInputError(document.getElementById("signup_passwd"), "Password did not match!");
// 		setInputError(document.getElementById("signup_confirm_passwd"), "Password did not match!");
// 	}
// 	if (document.getElementById("signup_confirm_passwd").value == "")
// 		setInputError(document.getElementById("signup_confirm_passwd"), "Password confirmation required!");
// 	// Checks if there was any errors
// 	for (let x = 0; x < signupForm.querySelector('.form').length; x++)
// 		if (signupForm.querySelector('.form')[x].classList.contains('form_input_error'))
// 			return true;
// 	return false;
// }
// // Checks signup fetch response error checks
// function signupCheckData(response) {
// 	const signupForm = document.getElementById('createAccount');
// 	if (response.status) {
// 		signupPopup.style.display = "block";
// 	} else {
// 		if (response.firstname)
// 			setInputError(document.getElementById("signup_firstname"), response.firstname);
// 		if (response.surname)
// 			setInputError(document.getElementById("signup_surname"), response.surname);
// 		if (response.username)
// 			setInputError(document.getElementById("signup_username"), response.username);
// 		if (response.email)
// 			setInputError(document.getElementById("signup_email"), response.email);
// 		if (response.password)
// 			setInputError(document.getElementById("signup_passwd"), response.password);
// 		if (response.confirmPassword)
// 			setInputError(document.getElementById("signup_confirm_passwd"), response.confirmPassword);
// 		if (!response.status && response.error === "sql")
// 			setFormMessage(signupForm.querySelector('.form_message_error'), "error", response.message);
// 	}
// }
// // Fetch signup response
// async function fetchSignup() {
// 	const formData = new FormData(signupForm.querySelector('form'));
// 	let response = await fetch('./scripts/php/signup.php', {
// 		method: 'POST',
// 		body: formData
// 	});
// 	response = await response.json();
// 	signupCheckData(response);
// }
// Fetch request to send email
async function sendEmail(formData) {
	let response = await fetch('./scripts/php/mail.php', {
		method: 'POST',
		body: formData
	});
	try {
		response = await response.json();
		if (response.status) {
			document.getElementById('forgotPasswordSent').innerHTML = response.message
			forgotPasswordPopup.style.display= "block";
		} else {
			setFormMessage(forgotPasswordForm.querySelector('.form_message_error'), "error", response.message);
			setInputError(document.getElementById('forgotPasswordEmail'), "");
		}
	} catch(e) {

	}
}
// Shows forgot password form
// function showForgotPassword() {
// 	if (loginForm.classList.contains('hidden'))
// 		var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?signup=true';
// 	else
// 		var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?login=true';
// 	window.history.pushState({ path: newurl }, '', newurl);
// 	window.onclick = function(event) {
// 		if (event.target == forgotPasswordPopup) {
// 			forgotPasswordPopup.style.display = "none";
// 		}
// 	}
// 	forgotPasswordForm.classList.remove("hidden");
// 	loginForm.classList.add("hidden");
// 	signupForm.classList.add("hidden");
// 	forgotPasswordBtnSend.onclick = function() {
// 		if (document.getElementById('forgotPasswordEmail').value.trim() =="") {
// 			setFormMessage(forgotPasswordForm.querySelector('.form_message_error'), "error", "Email required!");
// 			setInputError(document.getElementById('forgotPasswordEmail'), "");
// 		} else {
// 			const formData = new FormData();
// 			formData.append('function', 'resetPasswordEmail');
// 			formData.append('email', document.getElementById('forgotPasswordEmail').value)
// 			sendEmail(formData);
// 		}
// 	}
// 	document.getElementById("forgotPasswordEmail").addEventListener("input", e => {
// 		clearInputError(document.getElementById("forgotPasswordEmail"));
// 		setFormMessage(forgotPasswordForm.querySelector('.form_message_error'), "error", "");
// 	});
// 	forgotPasswordForm.onkeydown = async function(event){
// 		if(event.key === "Enter") {
// 			event.preventDefault();
// 			const formData = new FormData();
// 			formData.append('function', 'resetPasswordEmail');
// 			formData.append('email', document.getElementById('forgotPasswordEmail').value)
// 			sendEmail(formData);
// 		}
// 	}
// }
// // Password reset Form Checks
// function passwordResetCheckInput() {
// 	const rePassword = /\d|[A-Z]/;
// 	if (document.getElementById("passwordReset").value.trim() == "")
// 		setInputError(document.getElementById("passwordReset"), "Password required!");
// 	else if (document.getElementById("passwordReset").value.length < 8)
// 		setInputError(document.getElementById("passwordReset"), "Password minimum length of 8!");
// 	else if (document.getElementById("passwordReset").value.length > 255)
// 		setInputError(document.getElementById("passwordReset"), "Password needs to be shorter than 255 characters!");
// 	else if (!rePassword.test(document.getElementById("passwordReset").value))
// 		setInputError(document.getElementById("passwordReset"), "Password needs to include atleast an uppercase letter or number!");
// 	else if (document.getElementById("passwordReset").value != document.getElementById("passwordResetConfirm").value) {
// 		setInputError(document.getElementById("passwordReset"), "Password did not match!");
// 		setInputError(document.getElementById("passwordResetConfirm"), "Password did not match!");
// 	}
// 	if (document.getElementById("passwordResetConfirm").value.trim() == "")
// 		setInputError(document.getElementById("passwordResetConfirm"), "Password confirmation required!");
// 	for (let x = 0; x < passwordResetForm.length; x++)
// 		if (passwordResetForm[x].classList.contains('form_input_error'))
// 			return true;
// 	return false;
// }
// // Check Password reset Fetch response error checks
// function passwordResetCheckData(response) {
// 	if (response.status) {
// 		passwordResetPopup.style.display = "block"
// 		setTimeout(function(){
// 			var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?login=true';
// 			window.location.replace(newurl);
// 		}, 5000);
// 	} else {
// 		if (response.password)
// 			setInputError(document.getElementById("signup_passwd"), response.password);
// 		if (response.confirmPassword)
// 			setInputError(document.getElementById("signup_confirm_passwd"), response.confirmPassword);
// 		if (!response.status)
// 			setFormMessage(passwordResetForm.querySelector('.form_message_error'), "error", response.message);
// 	}
// }
// // Fetch API password reset
// async function fetchPasswordReset() {
// 	let token = urlParams.get('t');
// 	const formData = new FormData();
// 	formData.append('function', 'resetPassword');
// 	formData.append('token', token);
// 	formData.append('password', document.getElementById('passwordReset').value)
// 	formData.append('confirmPassword', document.getElementById('passwordResetConfirm').value)
// 	let response = await fetch('./scripts/php/mail.php', {
// 		method: 'POST',
// 		body: formData
// 	});
// 	response = await response.json();
// 	passwordResetCheckData(response);
// }
// // Password reset confirm button
// passwordResetBtnConfirm.onclick = async function() {
// 	if (!passwordResetCheckInput())
// 			fetchPasswordReset();
// }
// Password reset cancel button
// passwordResetBtnCancel.onclick = function() {
// 	var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?login=true';
// 	window.location.replace(newurl);
// }
// Link to login page after successful password reset
// document.getElementById('passwordResetLinkLogin').onclick = function() {
// 	var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?login=true';
// 	window.location.replace(newurl);
// }
// Verify Account
async function emailVerification() {
	let token = urlParams.get('t');
	const formData = new FormData()
	formData.append('function', 'verifyAccount');
	formData.append('token', token);
	let response = await fetch('./scripts/php/mail.php', {
		method: 'POST',
		body: formData
	});
	try {
		response = await response.json();
		if (response.status)
			if (confirm(response.message))
				window.location.replace(window.location.protocol + "//" + window.location.host + window.location.pathname + '?login=true');
	} catch(e) {

	}
}
// Change notification settings through link
async function emailNotification() {
	let token = urlParams.get('t');
	const formData = new FormData()
	formData.append('function', 'emailNotification');
	formData.append('token', token);
	let response = await fetch('./scripts/php/mail.php', {
		method: 'POST',
		body: formData
	});
	try {
		response = await response.json();
		if (response.status)
			if (confirm(response.message))
				window.location.replace(window.location.protocol + "//" + window.location.host + window.location.pathname + '?login=true');
	} catch(e) {

	}
}
// Modify email
async function modifyEmail() {
	let token = urlParams.get('t');
	let newToken = urlParams.get('nt');
	const formData = new FormData()
	formData.append('function', 'modifyEmail');
	formData.append('token', token);
	formData.append('newToken', newToken);
	let response = await fetch('./scripts/php/mail.php', {
		method: 'POST',
		body: formData
	});
	try {
		response = await response.json();
		if (response.status)
			if (confirm(response.message))
				window.location.replace(window.location.protocol + "//" + window.location.host + window.location.pathname + '?login=true');
	} catch(e) {

	}
}
// Resend verification email;
async function resendVerification(formData) {
	let response = await fetch('./scripts/php/mail.php', {
		method: 'POST',
		body: formData
	});
	try {
		response = await response.json();
		if (response.status)
			setFormMessage(document.getElementById('resendVerifyForm').querySelector('.form_message_error'), "success", response.message);
	} catch(e) {

	}
}
// Check signup form inputs in JS
// signupForm.addEventListener('submit', function (e) {
// 	e.preventDefault();
// 	if (!signupCheckInput())
// 		fetchSignup();
// });

// Check login form inputs in JS
// loginForm.addEventListener('submit', function (e) {
// 	e.preventDefault();
// 	if (!loginCheckInput())
// 		fetchLogin();
// });
// Shows Form
// function showForm(form) {
// 	loginForm.classList.add("hidden");
// 	signupForm.classList.add("hidden");
// 	forgotPasswordForm.classList.add("hidden");
// 	passwordResetForm.classList.add("hidden");
// 	form.classList.remove("hidden");
// }

// document.getElementById('CreateNewAccount').addEventListener("click", function(event) {
// 	event.preventDefault();
// 	showForm(signupForm);
// })

// document.getElementById('backToLogin').addEventListener("click", function(event) {
// 	event.preventDefault();
// 	showForm(loginForm);
// })

document.addEventListener("DOMContentLoaded", ()  => {
	const inputs = document.querySelectorAll(".form_input_group");
	for (const elem of inputs) {
		elem.children[0].addEventListener("input", function() {
			clearInputError(elem.children[0]);
		});
	}
	// Check Url Params
	// if (urlParams.has('signup'))
	// 	showForm(signupForm);
	// else if (urlParams.has('t') && urlParams.has('v'))
	// 	emailVerification();
	// else if (urlParams.has('t') && urlParams.has('n'))
	// 	emailNotification();
	// else if (urlParams.has('t') && urlParams.has('nt'))
	// 	modifyEmail();
	// else if (urlParams.has('t') && urlParams.has('pr'))
	// 	showForm(passwordResetForm);
	// Resend verification email
	// document.getElementById('resendVerificationBtn').addEventListener("click", function() {
	// 	const formData = new FormData();
	// 	formData.append('function', 'resendVerification');
	// 	formData.append('username', document.getElementById('login_username').value.trim());
	// 	resendVerification(formData);
	// });
	// Show Forgot Password Form when clicking link
	// forgotPasswordLogin.onclick = function() {
	// 	showForgotPassword();
	// } 
	// forgotPasswordSignup.onclick = function() {
	// 	showForgotPassword();
	// }
	// Show signupform when clicking link
	// document.getElementById('linkCreateAccount').addEventListener("click", event => {
	// 	event.preventDefault();
	// 	showForm(signupForm);
	// 	// Add signup parameter to URL
	// 	var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?signup=true';
	// 	window.history.pushState({ path: newurl }, '', newurl);
	// });
	// // Show loginform when clicking link
	// document.getElementById('linkLogin').addEventListener("click", event => {
	// 	event.preventDefault();
	// 	showForm(loginForm);
	// 	// Add login parameter to URL
	// 	var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?login=true';
	// 	window.history.pushState({ path: newurl }, '', newurl);
	// });
	// // Close signupPopup when clicked outside of the popup
	// window.onclick = function(event) {
	// 	if (event.target == signupPopup) {
	// 		signupPopup.style.display = "none";
	// 		window.location.href = window.location.protocol + "//" + window.location.host + "/camagru" + "/?login=true";
	// 	}
	// }
	// Clears input errors and Form Messages when typing on inputs
});