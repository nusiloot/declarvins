<?php include_partial('global/navTop', array('active' => 'vrac')); ?>
<section id="contenu" class="vracs">
	<?php include_component('vrac', 'etapes', array('vrac' => $form->getObject(), 'actif' => $etape)); ?>
	<form class="popup_form" method="post" action="<?php echo url_for('vrac_etape', array('sf_subject' => $form->getObject(), 'step' => $etape)) ?>">
		<?php echo $form->renderHiddenFields() ?>
		<?php echo $form->renderGlobalErrors() ?>

		<div>
			<div>
                <?php echo $form['type_transaction']->renderError() ?>
				<?php echo $form['type_transaction']->renderLabel() ?>
                <?php echo $form['type_transaction']->render() ?>
			</div>
			<div>
                <?php echo $form['produit']->renderError() ?>
				<?php echo $form['produit']->renderLabel() ?>
				<?php echo $form['produit']->render() ?>
			</div>
			<div>
                <?php echo $form['labels']->renderError() ?>
				<?php echo $form['labels']->renderLabel() ?>
				<?php echo $form['labels']->render() ?>
			</div>
			<div>
                <?php echo $form['mentions']->renderError() ?>
				<?php echo $form['mentions']->renderLabel() ?>
                <?php echo $form['mentions']->render() ?>
			</div>
			<div>
                <?php echo $form['volume_propose']->renderError() ?>
				<?php echo $form['volume_propose']->renderLabel() ?>
				<?php echo $form['volume_propose']->render() ?>
			</div>
			<div>
                <?php echo $form['annexe']->renderError() ?>
				<?php echo $form['annexe']->renderLabel() ?>
				<?php echo $form['annexe']->render() ?>
			</div>
			
		</div>
	
		<div class="ligne_form_btn">
			<button class="btn_valider" type="submit">Etape Suivante</button>
		</div>
	</form>
</section>