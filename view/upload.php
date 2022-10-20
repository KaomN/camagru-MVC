<?php include('header.php') ?>
<main>
	<div class="canvas-container">
		<div class="canvas-show">
			<div class="filter-container hidden" id="sidebar">
				<input type="image" src="src/filters/filter1.png" class="btn-filter" id="btnFilter"></input>
			</div>
			<div class="canvas-layers" id="cSize">
				<video class="hidden" id="video" autoplay="" playsinline=""></video>
				<img id="image" src="src/alt-img.png">
				<canvas class="canvas hidden" id="canvas"></canvas>
				<canvas class="hidden" id="canvasEdit"></canvas>
				<canvas class="hidden" id="filter"></canvas>
				<div id="errorMsg"></div>
			</div>
		</div>
		<div class="img-buttons" id="img-buttons">
			<i class="material-icons camera enabled" id="webcam">photo_camera</i>
			<i class="material-icons capture disabled" id="capture">camera</i>
			<input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" onclick="this.value=null;">
			<i class="material-icons file enabled" onclick="document.getElementById('fileToUpload').click();">add_photo_alternate</i>
			<i class="material-icons upload disabled" id="upload">file_upload</i>
		</div>
	</div>
</main>
<div class="thumbnail-container">
	<div class="thumbnails">
		<?php
			$thumbnails = "";
			$imageDir = "src/uploads/" . $_SESSION['username'] . "/";
			if($res['status'] === false){?>
				<div id="noImage" style="display: flex; align-items: center; justify-content: center; width:100%; height:100%;"><p>Error fetching images</p></div>
		<?php }
			else if(empty($imageData)) { ?>
				<div id="noImage" style="display: flex; align-items: center; justify-content: center; width:100%; height:100%;"><p>No images</p></div>
		<?php }
			else {
				foreach($imageData as $thumbnail) {
					$thumbnails .=	'<div class="thumbnail">' .
										'<img src="' . $imageDir . $thumbnail['filename'] .'" alt="Thumbnail" data-userid="' . $thumbnail['userid'] . '" data-id="' . $thumbnail['id'] . '" data-filename="' . $thumbnail['filename'] . '">' .
										'<i class="material-icons delete" title="Delete">delete</i>'.
									'</div>';
				}
				echo $thumbnails;
			}
		?>
	</div>
</div>
<?php include('footer.php') ?>