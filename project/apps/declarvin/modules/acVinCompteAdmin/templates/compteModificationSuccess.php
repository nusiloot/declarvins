<?php include_component('global', 'navBack', array('active' => 'comptes', 'subactive' => 'comptes')); ?>

<section id="contenu">
<div class="clearfix" id="application_dr">
    <h1>Compte</h1>
    <div id="compteModification">
        <?php include_partial('acVinCompteAdmin/formCompteModification', array('form' => $form))?>
    </div>
	<strong class="champs_obligatoires">* Champs obligatoires</strong>
</div>
</section>

