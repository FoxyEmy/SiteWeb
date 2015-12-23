<?php include_once 'inc/header.php'; 
	$bdd = new PDO('mysql:host=localhost;dbname=blackcat;charset=utf8', 'root', '');
	$rep = $bdd->prepare('SELECT * FROM articles ORDER BY date DESC LIMIT 6');
	$rep->execute(array());
	$art = $rep->fetchAll(PDO::FETCH_ASSOC);

?>


	<main>
		<section class="container">
				<h1>Les articles du<span> Chat noir</span></h1>
				
				<?php 
					foreach ($art as $key => $value) {
					
				?>
				<article>
					<a href="article.php?id=<?php echo $value['id']?>"><img src="<?php echo $value['image'];?>"></a>
					<a href="article.php?id=<?php echo $value['id']?>"><h2><?php echo $value['titre'];?></h2></a>
					<p><?php echo mb_substr($value['contenu'], 0, 200); ?><a href="article.php?id=<?php echo $value['id']?>"> Lire la suite...</a></p>
				</article>
				<?php } ?>
		</section>



	<div id="up">
        <a href=""><img src="img/chatnoir.png" id="logo"></a>
    </div>     
 


	</main>




<?php include_once 'inc/footer.php'; ?>