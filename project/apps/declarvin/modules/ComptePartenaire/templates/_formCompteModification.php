<form method="post" action="<?php echo url_for(array('sf_route' => 'compte_partenaire_modification', 'login' => $form->getObject()->login)); ?>">
    
    <div class="ligne_form">
        <label>Login</label>
        <span><?php echo $form->getObject()->login ?></span>
    </div>
    <?php 
    include_partial('ComptePartenaire/formCompteRenderer', array('form' => $form));
    ?>

    <div class="ligne_form reinit_mdp">
        <a href="<?php echo url_for('partenaire_compte_password', array('login' => $form->getObject()->login)) ?>" class="btn_orange">Redéfinir mon mot de passe</a>
    </div>
    
    <div class="btnValidation">
        <input class="btn_valider" type="submit" value="Modifier"/>
    </div>
</form>