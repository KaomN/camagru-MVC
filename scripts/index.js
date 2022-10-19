// Show error message in top
function setFormMessage(formElement, type, message) {
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
		alert("Oops, Something went wrong!")
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
		alert("Oops, Something went wrong!")
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
		alert("Oops, Something went wrong!")
	}
}

document.addEventListener("DOMContentLoaded", ()  => {
	const inputs = document.querySelectorAll(".form_input_group");
	for (const elem of inputs) {
		elem.children[0].addEventListener("input", function() {
			clearInputError(elem.children[0]);
		});
	}
	if (document.getElementById('backToLogin') != null) {
		document.getElementById('backToLogin').addEventListener("click", function() {
			window.location.replace(window.location.protocol + "//" + window.location.host + '/login');
		});
	}
	if (document.getElementById('close') != null) {
		document.getElementById('close').addEventListener("click", function() {
			document.querySelector('.popup').style.display = "none";
		});
	}
	if (document.getElementById('resendVerificationBtn') != null) {
		document.getElementById('resendVerificationBtn').addEventListener("click", async function() {
			const formData = new FormData();
			formData.append("request", "resendVerification")
			let response = await fetch('/login/request', {
				method: 'POST',
				body: formData
			});
			try {
				response = await response.json();
				console.log(response);
				if (response.status) {
					setFormMessage(document.getElementById('resendVerifyForm').querySelector('.form_message'), "success", response.message);
				}
			} catch(e) {
				alert("Oops, Something went wrong!")
			}
		});
		
	}
});