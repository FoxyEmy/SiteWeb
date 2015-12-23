<?php 
	include_once 'inc/header.php'; 
	$bdd = new PDO('mysql:host=localhost;dbname=blackcat;charset=utf8', 'root', '');
?>

	<main>
		<section class="container">
				<h1>Les utilisateurs du<span> Chat noir</span></h1>
				<form>
					<input type="search" name="cherche" placeholder="nom...">
					<button type="submit">Rechercher un chat</button>
				</form>

				<?php
					if(isset($_GET['cherche']) && !empty($_GET['cherche'])){

						$rep2 = $bdd->prepare('SELECT * FROM users WHERE nickname LIKE :trouve');
						$rep2->bindValue(':trouve', '%'.$_GET['cherche'].'%', PDO::PARAM_STR);
						$rep2->execute();
						$users2 = $rep2->fetchAll(PDO::FETCH_ASSOC);

						foreach ($users2 as $key => $value) {
							echo '<h2>'.$value['nickname'].'</h2>'.'<p>'.' enregistré le '.$value['date_registered'].'</p><br><hr>';
						}
					} else {
							$rep = $bdd->prepare('SELECT * FROM users ORDER BY date_registered');
							$rep->execute(array());
							$users = $rep->fetchAll(PDO::FETCH_ASSOC);

							foreach ($users as $key => $value) {
								echo'<br><br><p> . '.$value['nickname'].' enregistré le '.$value['date_registered'].'</p>';
							} 
					}
				?>
		</section>
	</main>
<?php include_once 'inc/footer.php'; ?>