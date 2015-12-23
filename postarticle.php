<?php include_once 'inc/header.php'; ?>

<?php
$post = array();
$err = array();
$erreursForm = false;
$formValid = false;

if(isset($_FILES['image'])){
		$fsize = $_FILES['image']['size'];

		if($fsize == 0 || $fsize > 1048576){
			$err[]= 'On peut se tutoyer ? t\'es lourd !';
		} 
		else {
			$dest = 'img';
			$name = $_FILES['image']['name'];
			$imgup = $_FILES['image']['tmp_name'];

			// move_uploaded_file($imgup, $dest.'/'.$name); OU :

			move_uploaded_file($imgup, $dest.'/'.$name);
			$lien = $dest.'/'.$name;
		}
}
else{
	$err[] = 'Choisir une image';
}





if (!empty($_POST)) {
	foreach ($_POST as $key => $value) {
		$post[$key] = trim(strip_tags($value));
	}
	if(empty($post['titre'])){
		$err[] = 'le titre ne peut être vide';
	}
	if (empty($post['contenu'])) {
		$err[] = 'Vous devez saisir un article !';
	}
	
	if (count($err) > 0) {
		$erreursForm = true;
	}
	else {
		
		$bdd = new PDO('mysql:host=localhost;dbname=blackcat;charset=utf8', 'root', '');
		$res = $bdd->prepare('INSERT INTO articles(date, titre, image, contenu)VALUES(NOW(),:titreArticle, :imageArticle, :contenuArticle)');
		$res->bindValue(':titreArticle', $post['titre'], PDO::PARAM_STR);
		$res->bindValue(':imageArticle', $lien, PDO::PARAM_STR);
		$res->bindValue(':contenuArticle', $post['contenu'], PDO::PARAM_INT);
		
		if($res->execute()){
			$formValid = true;
		}
		else {
			$err[]='une erreur est survenue';
			print_r($res->errorInfo());
			$erreursForm = true;
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