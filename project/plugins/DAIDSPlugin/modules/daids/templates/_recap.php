<?php use_helper('Version'); ?>
<?php use_helper('Float'); ?>
<?php foreach($daids->declaration->certifications as $certification): ?>
	<div class="tableau_ajouts_liquidations">
		<h2><?php echo $certification->getConfig()->libelle ?></h2>
		<table class="tableau_recap">
			<thead>
				<tr>
					<td style="border: none;">&nbsp;</td>
					<th style="font-weight: bold; border: none;">Stock théorique au 31 Juillet</th>
					<th style="font-weight: bold; border: none;">Stock physique au 31 Juillet</th>
					<th style="font-weight: bold; border: none;">Total Manquants ou Excédents</th>
					<th style="font-weight: bold; border: none;">Total Pertes Autorisée</th>
					<th style="font-weight: bold; border: none;">Manquants taxables éventuels</th>
					<th style="font-weight: bold; border: none;">Volumes manquants cotisables DAI/DS</th>
					<!-- <th style="font-weight: bold; border: none;">Total droits de circulation à payer</th>  -->
					<?php if ($sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR)): ?>
					<th style="font-weight: bold; border: none;">Total cotisations interprofessionnelles à payer</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php $details = $certification->getProduits(); 
					  $i = 1;?>

				<?php foreach($details as $detail): 
                        $i++; ?>
						<tr <?php if($i%2!=0) echo ' class="alt"'; ?>>
							<td><?php echo $detail->getLibelle(ESC_RAW) ?></td>
                            <td class="<?php echo isVersionnerCssClass($detail, 'stock_theorique') ?>"><strong><?php if ($detail->stock_theorique) echoLongFloat($detail->stock_theorique); else echoLongFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'stock_chais') ?>"><strong><?php if ($detail->stock_chais) echoLongFloat($detail->stock_chais); else echoLongFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_manquants_excedents') ?>"><strong><?php if ($detail->total_manquants_excedents) echoLongFloat($detail->total_manquants_excedents); else echoLongFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_pertes_autorisees') ?>"><strong><?php if ($detail->total_pertes_autorisees) echoLongFloat($detail->total_pertes_autorisees); else echoLongFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_manquants_taxables') ?>"><strong><?php if ($detail->total_manquants_taxables) echoLongFloat($detail->total_manquants_taxables); else echoLongFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_manquants_taxables_cvo') ?>">
								<?php if ($sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR)): ?>
									<?php if ($daids->isValidee()): ?>
										<a class="btn_edit btn_popup" data-popup-config="configForm" data-popup="#popup_update_cvo_<?php echo $detail->renderId() ?>" href="<?php echo url_for('daids_visualisation_update_cvo', $detail) ?>" title="Modifier"><strong><?php if ($detail->total_manquants_taxables_cvo) echoLongFloat($detail->total_manquants_taxables_cvo); else echoFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></a>
									<?php else: ?>
										<strong><?php if ($detail->total_manquants_taxables_cvo) echoLongFloat($detail->total_manquants_taxables_cvo); else echoLongFloat(0); ?></strong>&nbsp;<span class="unite">hl</span>
									<?php endif; ?>
								<?php else: ?>
									<strong><?php if ($detail->total_manquants_taxables_cvo) echoLongFloat($detail->total_manquants_taxables_cvo); else echoLongFloat(0); ?></strong>&nbsp;<span class="unite">hl</span>
								<?php endif; ?>
							</td>
							<!-- <td class="<?php //echo isVersionnerCssClass($detail, 'total_douane') ?>"><strong><?php //if ($detail->total_douane) echoFloat($detail->total_douane); else echoFloat(0); ?></strong>&nbsp;<span class="unite">€</span></td>  -->
							<?php if ($sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR)): ?>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_cvo') ?>">
									<strong><?php if ($detail->total_cvo) echoFloat($detail->total_cvo); else echoFloat(0); ?></strong>&nbsp;<span class="unite">€</span>
							</td>
							<?php endif; ?>
						</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php endforeach; ?>