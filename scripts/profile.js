function logout() {
	fetch('./scripts/php/logout.php');
}
// outputs string to HTML elements
function htmlToElement(html) {
	var template = document.createElement('template');
	html = html.trim();
	template.innerHTML = html;
	return template.content.firstChild;
}
// Toggles Like/unlike button
function toggleBtn(btn1, btn2) {
	btn1.classList.add('disabled', 'hidden');
	btn1.classList.remove('enabled');
	btn2.classList.add('enabled');
	btn2.classList.remove('hidden', 'disabled');
}
// Gets the image comments
async function getComments(img, messages, userInputMessage) {
	userInputMessage.value = "";
	const formData = new FormData();
	formData.append('function', 'getComments');
	formData.append('imageid', img.dataset.id);
	formData.append('imagename', img.dataset.filename);
	formData.append('imageuserid', img.dataset.userid);
	formData.append('imagesrc', img.src);
	let response = await fetch('./scripts/php/gallery.php', {
		method: 'POST',
		body: formData
	});
	try {
		response = await response.json();
		if(response.status) {
			messages.innerHTML = "";
			messages.prepend(htmlToElement(response.html));
		}
	} catch(e) {

	}
}
// Adds event listeners
function addListeners() {

	const elements = document.querySelectorAll('.image-container');
	for(let x = 0; x < elements.length; x++) {
		// Comment Btn listener
		elements[x].querySelector('.comment ').addEventListener('click', async function() {
			if (elements[x].querySelector('.message-container').classList.contains('off')) {
				await getComments(elements[x].children[1].firstChild, elements[x].querySelector('.messages'), elements[x].querySelector('input[type=text]'));
				elements[x].querySelector('.message-container').classList.remove('off')
				elements[x].querySelector('.messages').scrollTop = elements[x].querySelector('.messages').scrollHeight
			} else {
				elements[x].querySelector('.message-container').classList.add('off')
			}
		});
		// Comment input listener
		elements[x].querySelector('input[type=text]').addEventListener('keydown', async function(event) {
			if(event.key === "Enter"){
				if(elements[x].querySelector('input[type=text]').value.length > 255) {
					alert("Length of comments needs to be less than 255 characters!")
				} else if(elements[x].querySelector('input[type=text]').value.trim() == "") {
					return;
				} else {
					const formData = new FormData();
					formData.append('imageid', elements[x].children[1].firstChild.dataset.id);
					formData.append('imagename', elements[x].children[1].firstChild.dataset.filename);
					formData.append('imageuserid', elements[x].children[1].firstChild.dataset.userid);
					formData.append('imagesrc', elements[x].children[1].firstChild.src);
					formData.append('comment', String(elements[x].querySelector('input[type=text]').value));
					let response = await fetch('./scripts/php/insertComment.php', {
						method: 'POST',
						body: formData
					});
					try {
						response = await response.json();
						if (response.status) {
							await getComments(elements[x].children[1].firstChild, elements[x].querySelector('.messages'), elements[x].querySelector('input[type=text]'));
							elements[x].querySelector('.messages').scrollTop = elements[x].querySelector('.messages').scrollHeight
						}
					} catch(e) {

					}
				}
			}
		});
		// Delete btn listener
		elements[x].querySelector('.delete').addEventListener('click', async function () {
			const formData = new FormData();
			formData.append('imageid', elements[x].children[1].firstChild.dataset.id);
			formData.append('imagename', elements[x].children[1].firstChild.dataset.filename);
			formData.append('imageuserid', elements[x].children[1].firstChild.dataset.userid);
			formData.append('imagesrc', elements[x].children[1].firstChild.src);
			let response = await fetch('./scripts/php/deleteImage.php', {
				method: 'POST',
				body: formData
			});
			try {
				response = await response.json();
				if (response.status) {
					elements[x].remove();
				}
			} catch(e) {

			}
		});
	}
}
// Fetch images from server
async function getImages() {
	let response = await fetch('./scripts/php/profile.php');
	try {
		response = await response.json();
		if (response.status) {
			document.body.insertBefore(htmlToElement(response.html), document.querySelector('footer'));
			addListeners();
		}
	} catch(e) {

	}
}

document.addEventListener("DOMContentLoaded", async function() {
	await getImages();
	document.getElementById("logout").addEventListener("click", function() {
		logout();
		window.location.href = window.location.protocol + "//" + window.location.host + "/camagru";
	});
});
