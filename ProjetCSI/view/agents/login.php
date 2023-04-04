<div>
	<h1>Zone réservé</h1>
	<form action="<?php echo BASE_URL.'/agents/login';?>" method="post">
		<div id="input">
			<label for="inputLogin">Identifiant </label>
			<input type="text" id="inputLogin" name="Login" value><br>
			<label for="inputPassword">Mot de passe</label>
			<input type="password" id="inputPassword" name="Password" value><br> <br>
		</div>

		<div class="actions">
			<input type="submit" class="btn primary" value="Se connecter">
		</div>
	</form>


</div>




<!-- <h2>Connexion</h2><br>
			<form action=".php" method="post">

				<?php if (isset($messageErreurConnexion)): ?>
					<p id="MsgErreurConnexion"><?php echo $messageErreurConnexion; ?></p>
				<?php endif; ?>
			</form> -->