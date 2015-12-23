<?php include_once 'inc/header.php'; ?>

<?php
$post = array();
$err = array();
$erreursForm = false;
$formValid = false;

$maxSize = 1024 * 1000; // 1Ko * 1000 = 1Mo
$dirUpload = 'img/';
$mimeTypeAllowed = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif');

if(!empty($_POST)){
	$finfo = new finfo();


	foreach($_POST as $key => $value){
		$post[$key] = trim(strip_tags($value));
	}

	if(empty($post['titre'])){
		$error[] = 'Le titre ne peut être vide';
	}
	if(empty($_FILES['image']['size'])){ // On vérifie qu'il n'y a pas d'image 
		/*
		 * Par défaut, sans avoir envoyé de fichier
		 * $_FILES['img'] = array(
		 *		 				'name' => '', 
		 *		 				'type' => '', 
		 *		 				'tmp_name' => '', 
		 *		 				'error' => '', 
		 *		 				'size' => ''
		 *	 				);
	 	 */
		$error[] = 'L\'image ne peut être vide';
	}
	elseif($_FILES['image']['size'] > $maxSize) { // Je vérifie que l'image ne soit pas trop grosse
		$error[] = 'L\'image excède le poids autorisé';
	}
	// in array(valeur, tableau) : cherche valeur dans tableau :-)
	// Vérifiera que le mime type de l'image correspond a ceux qu'on a autorisé
	$fileMimeType = $finfo->file($_FILES['image']['tmp_name'], FILEINFO_MIME_TYPE);
	if(!in_array($fileMimeType, $mimeTypeAllowed)){ // 
		$error[] = 'Le fichier n\'est pas une image';
	}
	if (empty($post['contenu'])) {
		$err[] = 'Vous devez saisir un article !';
	}
	
	if (count($err) > 0) {
		$erreursForm = true;
	}
	else {
			// $monImgUpload = upload/monimage.jpg
		$search = array(' ', 'é', 'è', 'à');
		$replace = array('-', 'e', 'e', 'a', 'u');
		$newFileName = str_replace($search, $replace, time().'-'.$_FILES['image']['name']);
		$monImgUpload = $dirUpload.$newFileName;
		if(move_uploaded_file($_FILES['image']['tmp_name'], $monImgUpload)){
			// insertion des données de l'article seulement si le fichier a été uploadé
			$bdd = new PDO('mysql:host=localhost;dbname=blackcat;charset=utf8', 'root', '');
			$insertArt = $bdd->prepare('INSERT INTO articles (titre, image, contenu, date) VALUES (:titre, :image, :contenu, NOW())');
			$insertArt->bindValue(':titre', $post['titre'], PDO::PARAM_STR);
			$insertArt->bindValue(':image', $monImgUpload, PDO::PARAM_STR);
			$insertArt->bindValue(':contenu', $post['contenu'], PDO::PARAM_STR);

			if($insertArt->execute()){
				$formSuccess = true;
				$id_article = $bdd->lastInsertId();
			}
			else {
				$errorShow = true;
				$error[] = 'Une erreur est survenue lors de l\'insertion de votre article';
			}
		}
		else { // S'il y a une erreur lors de l'upload de l'image
			$errorShow = true;
			$error[] = 'Une erreur est survenue lors de l\'envoi de votre image';
		}
	}
}

?>





	<main>
		<section class="container">				
			<h1 class="arti2">Poster un article</h1>
				<?php
					if($erreursForm){
						echo '<p style="color:red">'.implode('<br>', $err).'</p>';
					}
					if ($formValid) {
						echo'<p style = "color:green"> Youhou ! Le formulaire est valide, l\'article est envoyé !!</p>';
					}
				?>
				<form method="POST" enctype="multipart/form-data">
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxSize; ?>">

					<label>Titre</label>
					<input type="text" id="titre" name="titre">

					<label>Article</label>
					<textarea id="contenu" name="contenu"></textarea>

					<label>Lien image</label>
					<input type="file" id="image" name="image">

					<br>
					<button type="submit">Envoyer</button>

				</form>


		<div id="up">
        	<a href=""><img src="img/chatnoir.png" id="logo"></a>
    	</div>     

	</main>


<?php include_once 'inc/footer.php'; ?>