<section id="contenu">
	<?php if($sf_user->hasFlash('notice')) : ?>
	<p class="flash_message"><?php echo $sf_user->getFlash('notice'); ?></p>
	<?php endif; ?>
	<form id="creation_compte" method="post" action="<?php echo url_for('compte_password', array('login' => $compte->login)) ?>">
		<?php echo $form->renderHiddenFields(); ?>
		<?php echo $form->renderGlobalErrors(); ?>
		<h1>Redéfinition de votre mot de passe</h1>
		<div class="col">
			<div class="ligne_form">
				<?php echo $form['mdp1']->renderError() ?>
				<?php echo $form['mdp1']->renderLabel() ?>
				<?php echo $form['mdp1']->render() ?>
			</div>
			<div class="ligne_form">
				<?php echo $form['mdp2']->renderError() ?>
				<?php echo $form['mdp2']->renderLabel() ?>
				<?php echo $form['mdp2']->render() ?>
			</div>
		</div>
		<div class="col">
			<div class="ligne_btn">
				<button type="submit" class="btn_valider">Valider</button>
			</div>
		</div>
	</form>
</section>