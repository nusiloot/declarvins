<?php use_helper('Float'); ?>
<?php use_helper('Unit'); ?>
<tr>
    <td>
    	<?php if (!$detail->total_debut_mois && !$detail->hasStockFinDeMoisDRMPrecedente()): ?>
    	<a href="<?php echo url_for('drm_mouvements_generaux_produit_delete', $detail) ?>" class="supprimer">Supprimer</a>
    	<?php endif; ?>
    	<?php echo $detail->getFormattedLibelle(ESC_RAW); ?>
        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR) || $sf_user->isUsurpationMode()): ?>
    	<a href="<?php echo url_for('drm_mouvements_generaux_product_edit', $detail) ?>" class="btn_popup" data-popup="#popup_edit_produit_<?php echo $detail->getIdentifiantHTML() ?>" data-popup-config="configFormEdit"><img src="/images/pictos/pi_edit.png" alt="edit" /></a>
        <?php endif; ?>
    </td>
	<td>
        <?php echo echoLongFloat($detail->total_debut_mois) ?> <span class="unite"><?php echoHl($detail) ?> </span>
	</td>
	<td class="acqTd <?php if ($detail->getDocument()->droits_acquittes): ?>showTd<?php else: ?>noTd<?php endif; ?>">
        <?php echo echoLongFloat($detail->acq_total_debut_mois) ?> <span class="unite"><?php echoHl($detail) ?>  </span>
	</td>
	<td>
		<?php echo $form['pas_de_mouvement_check']->render(array("class" => "pas_de_mouvement")) ?>
	</td>
</tr>