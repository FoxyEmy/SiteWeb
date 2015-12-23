<?php include_once 'inc/header.php'; 
	$bdd = new PDO('mysql:host=localhost;dbname=blackcat;charset=utf8', 'root', '');
	$rep = $bdd->prepare('SELECT * FROM articles WHERE id = :idArticle');
	$rep->bindValue(':idArticle', $_GET['id'], PDO::PARAM_INT);
	$rep->execute();
	$art = $rep->fetchAll(PDO::FETCH_ASSOC);


	$prep =$bdd->prepare('SELECT * FROM commentaires WHERE article_id = :idArticle');
	$prep->bindValue(':idArticle', $_GET['id'], PDO::PARAM_INT);
	$prep->execute();
	$blabla = $prep->fetchAll(PDO::FETCH_ASSOC);



	$post = array();
	$err = array();
	$erreursForm = false;
	$formValid = false;

if (!empty($_POST)) {
	foreach ($_POST as $key => $value) {
		$post[$key] = trim(strip_tags($value));
	}
	if(empty($post['nickname'])){
		$err[] = 'le pseudo est obligatoire ';
	}
	if (empty($post['comm'])) {
		$err[] = 'Vous devez saisir un commentaire !';
	}
	if (count($err) > 0) {
		$erreursForm = true;
	}
	else {
		$checkuser = $bdd->prepare('SELECT id FROM users WHERE nickname = :pseudo');
		$checkuser->bindValue(':pseudo', $post['nickname'], PDO::PARAM_STR);
		$checkuser->execute();

		$user = $checkuser->fetch(PDO::FETCH_ASSOC);

		if(isset($user['id']) && !empty($user['id'])){
			$utilisateurId = $user['id'];
		}
		else{

			$res = $bdd->prepare('INSERT INTO users(nickname, date_registered)VALUES(:pseudoComm, NOW())');
			$res->bindValue(':pseudoComm', $post['nickname'], PDO::PARAM_STR);
			$res->execute();
			$utilisateurId = $bdd->lastInsertId();
		}
			if(!empty($utilisateurId)){
				$reponse = $bdd->prepare('INSERT INTO commentaires(id_user, comm, article_id, date) VALUES(:userId, :comment, :idArticle, NOW())');
				$reponse->bindValue(':userId', $utilisateurId, PDO::PARAM_INT);
				$reponse->bindValue(':comment', $post['comm'], PDO::PARAM_STR);
				$reponse->bindValue(':idArticle', $_GET['id'], PDO::PARAM_INT);
				
				if($reponse->execute()){
					$nbInsertion = $reponse->rowCount();
					$formValid = true;
				};

			}

			else {
				$err[]='une erreur est survenue';
				$erreursForm = true;
			}
		
	}
}




?>


	<main>
		<section class="container">				
				<?php 
					foreach ($art as $key => $value) {
					
				?>
				<article class="arti1">
					<span><?php echo date('j F Y', strtotime($value['date'])); ?></span>
					<h1><?php echo $value['titre'];?></h2>
					<img src="<?php echo $value['image'];?>">
					<p><?php echo $value['contenu']; ?></p>
				</article>
				<div class="formulair">
					<form method="POST">
						<h3>Poster un commentaire</h2>
						<?php
							if($erreursForm){
								echo '<p style="color:red">'.implode('<br>', $err).'</p>';
							}
							if ($formValid) {
								echo'<p style = "color:green"> le commentaire est envoyé !!</p>';
							}
						?>
						<label>Pseudo</label>
						<input type="texte" id="nickname" name="nickname"></input>

						<label>Commentaire</label>
						<textarea id="comm" name="comm"></textarea>
						<br>
						<button type="submit">Envoyer</button>

					</form>
				</div>
				<?php 
					foreach ($blabla as $key => $value) {
					$use =$bdd->prepare('SELECT nickname FROM users WHERE id = :userId ');
					$use->bindValue(':userId', $value['id_user'], PDO::PARAM_STR);
					$use->execute();
					$userName=$use->fetch(PDO::FETCH_ASSOC);


				?>
				<div class="commentair">
				
					<h2><?php echo $userName['nickname'];?><span><?php echo "   -   ".date('j F Y', strtotime($value['date']));?></span></h2>
					<p><?php  echo $value['comm'];?></p>
				</div>
				<?php } ?>

				<?php } ?>

		</section>


		<div id="up">
       		<a href=""><img src="img/chatnoir.png" id="logo"></a>
   		</div>     




	</main>


<?php include_once 'inc/footer.php'; ?>