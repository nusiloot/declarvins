<form method="post" action="<?php echo url_for(array('sf_route' => 'compte_partenaire_ajout')); ?>">
    <div class="ligne_form ligne_form_label">
        <?php echo $form['login']->renderLabel() ?>
        <?php echo $form['login']->render() ?>
        <?php echo $form['login']->renderError() ?>
    </div>
    <?php 
      include_partial('ComptePartenaire/formCompteRenderer', array('form' => $form));  
    ?>
	
	<strong class="champs_obligatoires">* Champs obligatoires</strong>
    
    <div class="btnValidation">
        <input class="btn_valider" type="submit" value="Ajouter"/>
    </div>
</form>