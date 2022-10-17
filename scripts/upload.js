const mouseChecks = {dragging: false, resizing: false, resizingTL: false, resizingTR: false, resizingBL: false, resizingBR: false};
const mousePos = {x: 0, y: 0, dx: 0, dy: 0, sx: 0, sy: 0};
const filterPos = {x: 0, y: 0};
const filterData = {deltaX: 0, deltaY: 0, width: 0, height: 0, enabled: false};
const imageSize = {MAX_WIDTH: 0, MAX_HEIGHT: 0, width: 0, height: 0};
const isuploaded = {uploaded: false};
const camera = {enabled: false};
const stream = {videoStream: null};
const checkFile = {camera: false};
const constraints = window.constraints = {audio: false, video: true};
const btnIsDisabled = {captureButton: true, uploadButton: true};
// Shows elements
function showElem(elem, canvas, filter, videoElement, uploadedImage) {
	canvas.classList.add('hidden');
	filter.classList.add('hidden');
	videoElement.classList.add('hidden');
	uploadedImage.classList.add('hidden');
	elem.classList.remove('hidden');
}
// Sets max dimensions
function setMaxDimensions(width, height) {
	imageSize.MAX_WIDTH = width;
	imageSize.MAX_HEIGHT = height;
}
// sets dimensions of the image
function setDimensions(width, height) {
	imageSize.width = width;
	imageSize.height = height;
}
//disables a button and change class
function disableButton(button, btn) {
	button.classList.add('disabled');
	button.classList.remove('enabled')
	btnIsDisabled[btn] = true;
}
// Enables a button and change class
function enableButton(button, btn) {
	button.classList.add('enabled');
	button.classList.remove('disabled')
	btnIsDisabled[btn] = false;
}
// outputs string to HTML elements
function htmlToElement(html) {
	var template = document.createElement('template');
	html = html.trim();
	template.innerHTML = html;
	return template.content.firstChild;
}
// Add listener to delete button
function addListener($newImage) {
	function deleteListener(deleteBtn) {
		deleteBtn.addEventListener("click", async function() {
			const formData = new FormData();
			formData.append('request', "deleteThumbnail");
			formData.append('imageid', deleteBtn.previousSibling.dataset.id);
			formData.append('imagename', deleteBtn.previousSibling.dataset.filename);
			formData.append('imageuserid', deleteBtn.previousSibling.dataset.userid);
			formData.append('imagesrc', deleteBtn.previousSibling.src);
			let response = await fetch('/upload/request', {
				method: 'POST',
				body: formData
			});
			try {
				response = await response.text();
				console.log(response)
				if (response.status) {
					deleteBtn.parentElement.remove();
					if(document.querySelectorAll('.thumbnail').length === 0)
						document.querySelector('.thumbnails').remove();
				}
			} catch(e) {
				
			}
		});
	}
	if ($newImage) {
		deleteListener(document.querySelector('.delete'));
	} else {
		var deleteBtn = document.querySelectorAll('.delete');
		for (let x = 0; x < deleteBtn.length; x++) {
			deleteListener(deleteBtn[x]);
		}
	}
}

// Fetches thumbnails for newly uploaded image
async function createUploadedThumbnail() {
	const formData = new FormData();
	formData.append('request', "createThumbnail");
	let response = await fetch('/upload/request', {
		method: 'POST',
		body: formData
	});
	response = await response.json();
	console.log(response);
	try {
		if (response.status) {
			document.querySelector('.thumbnails').prepend(htmlToElement(response.tag));
			//addListener(true);
		}
	} catch(e) {

	}
}
// Using Fetch API to send image to server
async function upload(formData, captureImg, uploadedImage) {
	let response = await fetch('/upload/request', {
		method: 'POST',
		body: formData
	});
	response = await response.json();
	console.log(response)
	try {
		if (response.status) {
			await createUploadedThumbnail();
			captureImg.src = response.src
			uploadedImage.src = response.src;
		}
	} catch(e) {

	}
}
// Handle onSuccess stream
function handleSuccess(stream, sidebar) {
	const video = document.querySelector('video');
	video.srcObject = stream.videoStream;
	sidebar.classList.remove('hidden');
}
// Handle error message
function handleError(error, captureBtn, uploadBtn, sidebar) {
	disableButton(captureBtn, "captureButton");
	disableButton(uploadBtn, "uploadButton");
	sidebar.classList.add('hidden');
	if (error.name === 'NotAllowedError') {
		errorMsg('Permissions have not been granted to use your camera, you need to allow the page access to your device');
	} else if (error.name === 'NotFoundError') {
		errorMsg('Could not find a camera device on the system!');
	}
}
// Show error message
function errorMsg(msg) {
	const errorElement = document.querySelector('#errorMsg');
	errorElement.innerHTML = msg;
}
// Initiate getUserMedia
async function init(camera, captureBtn, uploadBtn, sidebar, videoElement) {
	try {
		stream.videoStream = await navigator.mediaDevices.getUserMedia(constraints);
		camera.enabled = true;
		handleSuccess(stream, sidebar);
	} catch(error) {
		videoElement.classList.add("hidden")
		handleError(error, captureBtn, uploadBtn, sidebar);
	}
}
// Stop video stream
function stopStream(camera, captureBtn, uploadBtn) {
	if (camera.enabled) {
		camera.enabled = false;
		stream.videoStream.getTracks().forEach(function(track) {
			track.stop();
		});
		disableButton(captureBtn, "captureButton");
		disableButton(uploadBtn, "uploadButton");
	}
}
// Calculates smallest area to fit entire picture in the canvas and retain aspect ratio
function calcDimensions(canvas, image, imageSize, canvasSize) {
	if ((canvasSize.offsetWidth/image.width) < (canvasSize.offsetHeight/image.height)) {
		setDimensions(imageSize.MAX_WIDTH, (image.height * (imageSize.MAX_WIDTH / image.width)));
	} else {
		setDimensions((image.width * (imageSize.MAX_HEIGHT / image.height)), imageSize.MAX_HEIGHT);
	}
	canvas.width = imageSize.width = imageSize.width - 50;
	canvas.height = imageSize.height = imageSize.height - 50;
}
// Calculates smallest area to fit entire camera picture in the canvas and retain aspect ratio
function calcDimensionsCamera(canvas, imageSize, canvasSize) {
	if ((canvasSize.offsetWidth/video.videoWidth) < (canvasSize.offsetHeight/video.videoHeight)) {
		setDimensions(canvasSize.offsetWidth, (video.videoHeight * (canvasSize.offsetWidth / video.videoWidth)));
	} else {
		setDimensions((video.videoWidth * (canvasSize.offsetHeight / video.videoHeight)), canvasSize.offsetHeight);
	}
	canvas.width = imageSize.width = imageSize.width - 60;
	canvas.height = imageSize.height = imageSize.height  - 45;
}
// Redraws canvas image if window is resized
function redraw(image, canvas, imageSize, canvasSize, context) {
	context.clearRect(0, 0, canvas.width, canvas.height);
	setMaxDimensions(canvas.width, canvas.height)
	setDimensions(image.width, image.height);
	calcDimensions(canvas, image, imageSize, canvasSize);
	context.drawImage(image, 0, 0, imageSize.width, imageSize.height);
}
// Draws filter on the canvas
function drawFilter(filter, filterContext, filterImg, filterPos, imageSize) {
	filterContext.clearRect(0, 0, filter.width, filter.height);
	if ((filter.width/filterImg.width) < (filter.height/filterImg.height)) {
		filterImg.height = filterImg.height * (imageSize.MAX_WIDTH / filterImg.width);
		filterImg.width = imageSize.MAX_WIDTH;
	} else {
		filterImg.width = filterImg.width * (imageSize.MAX_HEIGHT / filterImg.height);
		filterImg.height = imageSize.MAX_HEIGHT;
	}
	filterPos.x = Math.abs(filter.width - filterImg.width)/2;
	filterPos.y = Math.abs(filter.height - filterImg.height)/2;
	filterContext.drawImage(filterImg, filterPos.x, filterPos.y, filterImg.width, filterImg.height);
}
// Updates the filter size on resize
function updateFilterSize(filter, filterContext, filterImg, filterPos) {
	filterContext.clearRect(0, 0, filter.width, filter.height);
	filterContext.drawImage(filterImg, filterPos.x, filterPos.y, filterImg.width, filterImg.height);
}
// remove filter
function removeFilter(filter, filterContext) {
	filterContext.clearRect(0, 0, filter.width, filter.height);
	filterData.enabled = false;
}
// Check if mouse is on top left of filter
function mouseTopLeft(event, filterPos, left, top) {
	if (event.clientX > (filterPos.x + left)
		&& event.clientX < (filterPos.x + 40 + left)
		&& event.clientY > (filterPos.y + top)
		&& event.clientY < (filterPos.y + 40 + top))
		return true;
	return false;
}
// Check if mouse is on top right of filter
function mouseTopRight(event, filterPos, filterImg, left, top) {
	if (event.clientX > (filterPos.x + filterImg.width - 40 + left)
		&& event.clientX < (filterPos.x + filterImg.width + left)
		&& event.clientY > (filterPos.y + top)
		&& event.clientY < (filterPos.y + 40 + top))
		return true;
	return false;
}
// Check if mouse is on bottom left of filter
function mouseBottomLeft(event, filterPos, filterImg, left, top) {
	if (event.clientX > (filterPos.x + left)
		&& event.clientX < (filterPos.x + 40 + left)
		&& event.clientY > (filterPos.y + top + filterImg.height - 40)
		&& event.clientY < (filterPos.y + filterImg.height + top))
		return true;
	return false;
}
// Check if mouse is on bottom right of filter
function mouseBottomRight(event, filterPos, filterImg, left, top) {
	if (event.clientX > (filterPos.x + filterImg.width - 40 + left)
		&& event.clientX < (filterPos.x + filterImg.width + left)
		&& event.clientY > (filterPos.y + top + filterImg.height - 40)
		&& event.clientY < (filterPos.y + filterImg.height + top))
		return true;
	return false;
}
// check if mouse positions is inside the filter image
function mouseInFilter(event, filterPos, filterImg, left, top) {
	if (event.clientX > (filterPos.x + left)
		&& event.clientX < (filterPos.x + filterImg.width + left)
		&& event.clientY > (filterPos.y + top)
		&& event.clientY < (filterPos.y + filterImg.height + top))
		return true;
	return false;
}
// Calculates mouse delta
function calcMouseDelta(event, mousePos) {
	mousePos.x = parseInt(event.clientX);
	mousePos.y = parseInt(event.clientY);
	mousePos.dx = mousePos.x - mousePos.sx;
	mousePos.dy = mousePos.y - mousePos.sy;
}
// Update mouse position
function updateMousePosition(mousePos) {
	mousePos.sx = mousePos.x;
	mousePos.sy = mousePos.y;
}
// Updates filter Data
function updateFilterData(filterImg) {
	filterData.deltaX = (filterPos.x / imageSize.width);
	filterData.deltaY = (filterPos.y / imageSize.height);
	filterData.width = (filterImg.width / imageSize.width);
	filterData.height = (filterImg.height / imageSize.height);
}
// Draws to hidden canvas
function drawToCanvasEdit(img, contextEdit, canvasEdit, videoElement) {
	if (camera.enabled) {
		contextEdit.clearRect(0, 0, canvasEdit.width, canvasEdit.height);
		canvasEdit.width = video.videoWidth;
		canvasEdit.height = video.videoHeight;
		contextEdit.drawImage(videoElement, 0, 0);
	} else {
		contextEdit.clearRect(0, 0, canvasEdit.width, canvasEdit.height);
		canvasEdit.width = img.width;
		canvasEdit.height = img.height;
		contextEdit.drawImage(img, 0, 0);
	}
}

document.addEventListener("DOMContentLoaded", async () => {
	const uploadedImage = document.getElementById('image');
	const canvas = document.getElementById('canvas');
	const canvasEdit = document.getElementById('canvasEdit');
	const canvasSize = document.getElementById('cSize');
	const context = canvas.getContext('2d');
	const contextEdit = canvasEdit.getContext('2d');
	const captureBtn = document.getElementById('capture');
	const uploadBtn = document.getElementById('upload');
	const sidebar = document.getElementById('sidebar');
	const filter = document.getElementById('filter');
	const filterContext = filter.getContext('2d');
	const videoElement = document.getElementById('video');
	var videoImg = new Image();
	var img = new Image();
	var captureImg = new Image();
	var filterImg = new Image();
	var lastOpenedFile;
	filterImg.src = '/src/filters/filter1.png';
	var rect = canvas.getBoundingClientRect();
	addListener(false);
	// Webcam button
	document.getElementById('webcam').addEventListener('click', e => {
		showElem(videoElement, canvas, filter, videoElement, uploadedImage)
		removeFilter(filter, filterContext);
		if (!camera.enabled) {
			init(camera, captureBtn, uploadBtn, sidebar, videoElement);
			//videoElement.classList.remove("hidden");
		} else {
			stopStream(camera, captureBtn, uploadBtn);
			displayDefault(canvas, img, imageSize, context);
			showElem(canvas, canvas, filter, videoElement, uploadedImage)
		}
	});
	// Camera capture button
	captureBtn.addEventListener('click', e => {
		if (btnIsDisabled.captureButton)
			return;
		drawToCanvasEdit(img, contextEdit, canvasEdit, videoElement);
		checkFile.camera = true
		context.clearRect(0, 0, canvasSize.offsetWidth, canvasSize.offsetHeight);
		calcDimensionsCamera(canvas, imageSize, canvasSize);
		context.drawImage(videoElement, 0, 0, imageSize.width, imageSize.height);
		rect = canvas.getBoundingClientRect();
		stopStream(camera, captureBtn, uploadBtn);
		context.drawImage(filter, 0, 0);
		//removeFilter(filter, filterContext);
		enableButton(uploadBtn, "uploadButton");
		captureImg.src = canvas.toDataURL();
		img.src = captureImg.src;
		showElem(canvas, canvas, filter, videoElement, uploadedImage)
	})
	// Choose file button
	document.getElementById('fileToUpload').addEventListener('change', event => {
		checkFile.camera = false;
		stopStream(camera, captureBtn, uploadBtn);
		videoImg.src == ""
		showElem(canvas, canvas, filter, videoElement, uploadedImage)
		sidebar.classList.remove('hidden');
		document.querySelector('#errorMsg').textContent = "";
		context.clearRect(0, 0, canvas.width, canvas.height);
		removeFilter(filter, filterContext);
		setMaxDimensions(canvasSize.offsetWidth, canvasSize.offsetHeight)
		imageFile = event.target.files[0];
		console.log(imageFile)
		try {
			img.src = URL.createObjectURL(event.target.files[0]);
			lastOpenedFile = event.target.files[0];
			img.onload = function(){
				drawToCanvasEdit(img, contextEdit, canvasEdit);
				calcDimensions(canvas, img, imageSize, canvasSize)
				rect = canvas.getBoundingClientRect();
				context.drawImage(img, 0, 0, imageSize.width, imageSize.height);
			}
			enableButton(uploadBtn, "uploadButton");
		} catch(e) {
			disableButton(uploadBtn, "uploadButton")
			showElem(uploadedImage, canvas, filter, videoElement, uploadedImage);
			sidebar.classList.add('hidden');
		}
	});
	// Uploads current showing picture to the server
	document.getElementById('upload').addEventListener('click', async function() {
		if (btnIsDisabled.uploadButton)
			return;
		disableButton(uploadBtn, "uploadButton");
		canvasEdit.toBlob(async function(blob) {
			updateFilterData(filterImg);
			const formData = new FormData();
			formData.append('request', "uploadImage")
			formData.append('filterData', JSON.stringify(filterData))
			if (checkFile.camera) {
				const file = new File([blob], "image.jpg");
				formData.append('file', file);
			} else {
				if(!document.getElementById('fileToUpload').files[0])
					var file = lastOpenedFile;
				else
					var file = document.getElementById('fileToUpload').files[0]
				formData.append('file', file);
			}
			await upload(formData, captureImg, uploadedImage);
			showElem(uploadedImage, canvas, filter, videoElement, uploadedImage)
			sidebar.classList.add('hidden');
			removeFilter(filter, filterContext);
		}, "image/jpeg", 1);
		isuploaded.uploaded = true;
	});
	// Button to add filter to canvas
	document.getElementById('btnFilter').addEventListener('click', e => {
		if (!filterData.enabled) {
			filterData.enabled = true;
			rect = canvas.getBoundingClientRect();
			filter.classList.remove('hidden');
			if (camera.enabled) {
				calcDimensionsCamera(canvas, imageSize, canvasSize);
				filter.width = canvas.width;
				filter.height = canvas.height;
				setMaxDimensions(filter.width, filter.height);
				setDimensions(filterImg.width, filterImg.height);
				drawFilter(filter, filterContext, filterImg, filterPos, imageSize);
				rect = filter.getBoundingClientRect();
				enableButton(captureBtn, "captureButton");
			} else {
				filter.width = canvas.width;
				filter.height = canvas.height;
				setMaxDimensions(canvas.width, canvas.height);
				drawFilter(filter, filterContext, filterImg, filterPos, imageSize);
				enableButton(uploadBtn, "uploadButton");
			}
			updateFilterData(filterImg);
		} else {
			removeFilter(filter, filterContext);
			disableButton(captureBtn, "captureButton");
			if (camera.enabled)
				disableButton(uploadBtn, "uploadButton");
		}
	});
	//Update canvas size on window resize
	addEventListener('resize', function() {
		if (camera.enabled) {
			calcDimensionsCamera(canvas, imageSize, canvasSize);
			filter.width = imageSize.width;
			filter.height = imageSize.height;
			setMaxDimensions(filter.width, filter.height);

			setDimensions(filterImg.width, filterImg.height);
			rect = filter.getBoundingClientRect();
			if (filterData.enabled)
				drawFilter(filter, filterContext, filterImg, filterPos, imageSize);
			updateFilterData(filterImg);
		}
		// } else if (isuploaded.uploaded) {
		// 	redraw(captureImg, canvas, imageSize, canvasSize, context);
		// }
		else {
			canvas.width = canvasSize.offsetWidth;
			canvas.height = canvasSize.offsetHeight;
			rect = canvas.getBoundingClientRect();
			if (videoImg.src == "")
				redraw(img, canvas, imageSize, canvasSize, context);
			else
				redraw(videoImg, canvas, imageSize, canvasSize, context);
			filter.width = canvas.width;
			filter.height = canvas.height;
			if (filterData.enabled) {
				setMaxDimensions(canvas.width, canvas.height);
				drawFilter(filter, filterContext, filterImg, filterPos, imageSize);
				rect = canvas.getBoundingClientRect();
			}
			updateFilterData(filterImg);
		}
	});
	// filter canvas event listeners.
	filter.onmousedown = (event) => {
		rect = filter.getBoundingClientRect();
		event.preventDefault(event);
		if (!filterData.enabled)
			return;
		mousePos.sx = parseInt(event.clientX);
		mousePos.sy = parseInt(event.clientY);
		if (mouseTopLeft(event, filterPos, rect.left, rect.top) ||
			mouseTopRight(event, filterPos, filterImg, rect.left, rect.top) ||
			mouseBottomLeft(event, filterPos, filterImg, rect.left, rect.top) ||
			mouseBottomRight(event, filterPos, filterImg, rect.left, rect.top) ||
			mouseInFilter(event, filterPos, filterImg, rect.left, rect.top))
				mouseChecks.resizing = true;
		if (mouseTopLeft(event, filterPos, rect.left, rect.top))
			mouseChecks.resizingTL = true;
		else if (mouseTopRight(event, filterPos, filterImg, rect.left, rect.top))
			mouseChecks.resizingTR = true;
		else if (mouseBottomLeft(event, filterPos, filterImg, rect.left, rect.top))
			mouseChecks.resizingBL = true;
		else if (mouseBottomRight(event, filterPos, filterImg, rect.left, rect.top))
			mouseChecks.resizingBR = true;
		else if (mouseInFilter(event, filterPos, filterImg, rect.left, rect.top))
			mouseChecks.dragging = true
	};
	filter.onmouseup = (event) => {
		if (!mouseChecks.dragging && !mouseChecks.resizing)
		return;
		event.preventDefault();
		for (let key in mouseChecks)
			mouseChecks[key] = false;
	};
	filter.onmouseout = (event) => {
		document.body.style.cursor = "default";
		if (!mouseChecks.dragging && !mouseChecks.resizing)
			return;
		event.preventDefault();
		for (let key in mouseChecks)
			mouseChecks[key] = false;
	};
	// Checks mouse position on filter to resize and move the filter
	filter.onmousemove = (event) => {
		if (!filterData.enabled)
			return;
		calcMouseDelta(event, mousePos);
		if (mouseChecks.dragging) {
			event.preventDefault();
			filterPos.x += mousePos.dx;
			filterPos.y += mousePos.dy;
		}
		if (mouseChecks.resizing) {
			if (mouseChecks.resizingTL) {
				filterImg.width -= mousePos.dx;
				filterImg.height -= mousePos.dy;
				filterPos.x += mousePos.dx;
				filterPos.y += mousePos.dy;
			} else if (mouseChecks.resizingTR) {
				filterImg.width += mousePos.dx;
				filterImg.height -= mousePos.dy;
				filterPos.y += mousePos.dy;
			} else if (mouseChecks.resizingBL) {
				filterImg.width -= mousePos.dx;
				filterImg.height += mousePos.dy;
				filterPos.x += mousePos.dx;
			} else if (mouseChecks.resizingBR) {
				filterImg.width += mousePos.dx;
				filterImg.height += mousePos.dy;
			}
		}
		updateFilterSize(filter, filterContext, filterImg, filterPos);
		updateMousePosition(mousePos);
		updateFilterData(filterImg);
	};
	// Check mouse position on filter image to change cursor style
	filter.addEventListener('mousemove', (event) => {
		if (!filterData.enabled)
			return;
		if (mouseTopLeft(event, filterPos, rect.left, rect.top))
			document.body.style.cursor = "nw-resize";
		else if (mouseTopRight(event, filterPos, filterImg, rect.left, rect.top))
			document.body.style.cursor = "ne-resize";
		else if (mouseBottomLeft(event, filterPos, filterImg, rect.left, rect.top))
			document.body.style.cursor = "sw-resize";
		else if (mouseBottomRight(event, filterPos, filterImg, rect.left, rect.top))
			document.body.style.cursor = "se-resize";
		else if (mouseInFilter(event, filterPos, filterImg, rect.left, rect.top))
			document.body.style.cursor = "pointer";
		else
			document.body.style.cursor = "default";
	});
});
