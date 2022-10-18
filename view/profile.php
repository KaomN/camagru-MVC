<?php include('header.php') ?>
<main>
	<?php
	if ($images['status'])
		echo $images['tag'];
	else
		echo '<div styles="display:flex;align-items:center;justify-content:center;">No images uploaded!</div>';
	?>
</main>
<?php include('footer.php') ?>