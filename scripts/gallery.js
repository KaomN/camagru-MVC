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
	formData.append('request', 'getComments');
	formData.append('imageid', img.dataset.id);
	formData.append('imagename', img.dataset.filename);
	formData.append('imageuserid', img.dataset.userid);
	formData.append('imagesrc', img.src);
	let response = await fetch('/gallery/request', {
		method: 'POST',
		body: formData
	});
	response = await response.json();
	try {
		if(response.status) {
			messages.innerHTML = "";
			messages.append(htmlToElement(response.tag));
		}
	} catch(e) {
		alert("Oops, Something went wrong!")
	}
}
// Adds event listeners
function addListeners(counter, start) {
	const elements = document.querySelectorAll('.image-container');
	for (let x = Math.abs(counter.showingImageCount - start); x < counter.showingImageCount; x++) {
		// Comment button listener
		elements[x].querySelector('.comment').addEventListener('click', async function() {
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
			if(event.key === "Enter") {
				if(elements[x].querySelector('input[type=text]').value.length > 255) {
					alert("Length of comments needs to be less than 255 characters!")
				} else if(elements[x].querySelector('input[type=text]').value.trim() == "") {
					return;
				} else {
					const formData = new FormData();
					formData.append('request', "insertComment");
					formData.append('imageid', elements[x].children[1].firstChild.dataset.id);
					formData.append('imagename', elements[x].children[1].firstChild.dataset.filename);
					formData.append('imageuserid', elements[x].children[1].firstChild.dataset.userid);
					formData.append('imagesrc', elements[x].children[1].firstChild.src);
					formData.append('comment', String(elements[x].querySelector('input[type=text]').value));
					let response = await fetch('/gallery/request', {
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
						alert("Oops, Something went wrong!")
					}
				}
			}
		});
		// Like button
		if(elements[x].querySelector('.like') != null) {
			elements[x].querySelector('.like').addEventListener('click', async function() {
				const formData = new FormData();
				formData.append('request', 'likeImage');
				formData.append('imageid', elements[x].children[1].firstChild.dataset.id);
				formData.append('imagename', elements[x].children[1].firstChild.dataset.filename);
				formData.append('imageuserid', elements[x].children[1].firstChild.dataset.userid);
				formData.append('imagesrc', elements[x].children[1].firstChild.src);
				let response = await fetch('/gallery/request', {
					method: 'POST',
					body: formData
				});
				try {
					response = await response.json();
					if (response.status) {
						formData.set('request', 'getLikesData')
						let response = await fetch('/gallery/request', {
							method: 'POST',
							body: formData
						});
						response = await response.json();
						elements[x].querySelector('.like-amount').innerHTML = response.likecount + " like(s)";
						if (response.liked)
							toggleBtn(elements[x].querySelector('.like'), elements[x].querySelector('.unlike'));
						else
							toggleBtn(elements[x].querySelector('.unlike'), elements[x].querySelector('.like'));
					}
				} catch(e) {
					alert("Oops, Something went wrong!")
				}
			});
		}
		// Unlike button
		if(elements[x].querySelector('.unlike') != null) {
			elements[x].querySelector('.unlike').addEventListener('click', async function() {
				const formData = new FormData();
				formData.append('request', 'unlikeImage');
				formData.append('imageid', elements[x].children[1].firstChild.dataset.id);
				formData.append('imagename', elements[x].children[1].firstChild.dataset.filename);
				formData.append('imageuserid', elements[x].children[1].firstChild.dataset.userid);
				formData.append('imagesrc', elements[x].children[1].firstChild.src);
				let response = await fetch('/gallery/request', {
					method: 'POST',
					body: formData
				});
				try {
					response = await response.json();
					if (response.status) {
						formData.set('request', 'getLikesData')
						let response = await fetch('/gallery/request', {
							method: 'POST',
							body: formData
						});
						response = await response.json();
						elements[x].querySelector('.like-amount').innerHTML = response.likecount + " like(s)";
						if (response.liked)
							toggleBtn(elements[x].querySelector('.like'), elements[x].querySelector('.unlike'));
						else
							toggleBtn(elements[x].querySelector('.unlike'), elements[x].querySelector('.like'));
					}
				} catch(e) {
					alert("Oops, Something went wrong!")
				}
			});
		}
		// Delete button
		if(elements[x].querySelector('.delete') != null) {
			elements[x].querySelector('.delete').addEventListener('click', async function() {
				const formData = new FormData();
				formData.append('request', "deleteImage");
				formData.append('imageid', elements[x].children[1].firstChild.dataset.id);
				formData.append('imagename', elements[x].children[1].firstChild.dataset.filename);
				formData.append('imageuserid', elements[x].children[1].firstChild.dataset.userid);
				formData.append('imagesrc', elements[x].children[1].firstChild.src);
				let response = await fetch('/gallery/request', {
					method: 'POST',
					body: formData
				});
				try {
					response = await response.json();
					if (response.status) {
						elements[x].remove();
						counter.showingImageCount--;
						const loadingNewImages = {status: false};
						// Check if new images needs to be loaded
						if ((document.body.scrollTop + document.body.offsetHeight + 400) < document.body.scrollHeight && loadingNewImages.status)
							loadingNewImages.status = false;
						else if((document.body.scrollTop + document.body.offsetHeight + 400) > document.body.scrollHeight && !loadingNewImages.status) {
							loadingNewImages.status = true;
							formData.set('request', 'getGalleryImages');
							formData.set('start', counter.showingImageCount);
							await getImages(formData, counter);
						}
					}
				}
				catch(e) {
					alert("Oops, Something went wrong!")
				}
			});
		}
	}
}
// Fetch images from server
async function getImages(formData, counter) {
	formData.append('request', 'getGalleryImages');
	let response = await fetch('/gallery/request', {
		method: 'POST',
		body: formData
	});
	try {
		response = await response.json();
		if (response.status) {
			document.querySelector('main').appendChild(htmlToElement(response.tag));
			counter.showingImageCount += htmlToElement(response.tag).querySelectorAll('.image-container').length
			var start = htmlToElement(response.tag).querySelectorAll('.image-container').length
			addListeners(counter, start);
		}
	} catch(e) {
		alert("Oops, Something went wrong!1")
	}
}

document.addEventListener("DOMContentLoaded", async function() {
	const counter = {showingImageCount: 0};
	const loadingNewImages = {status: false};
	const formData = new FormData();
	formData.append('request', 'getGalleryImages');
	formData.append('start', 0);
	counter.showingImageCount = document.querySelectorAll('.image-container').length;
	addListeners(counter, counter.showingImageCount);
	// Infinite pagination
	document.body.addEventListener('scroll', async function() {
		let documentHeight = document.body.scrollHeight;
		let currentScroll = document.body.scrollTop + document.body.offsetHeight;
		let modifier = 400;
		if (currentScroll + modifier < documentHeight && loadingNewImages.status)
			loadingNewImages.status = false;
		else if(currentScroll + modifier > documentHeight && !loadingNewImages.status) {
			loadingNewImages.status = true;
			formData.set('start', counter.showingImageCount);
			await getImages(formData, counter);
		}
	})
});
