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
					<th style="font-weight: bold; border: none;">Total vins logés dans votre chais</th>
					<th style="font-weight: bold; border: none;">Total Stock de votre propriété</th>
					<th style="font-weight: bold; border: none;">Total Manquants ou Excédents</th>
					<th style="font-weight: bold; border: none;">Total Pertes Autorisée</th>
					<th style="font-weight: bold; border: none;">Manquants taxables éventuels</th>
					<th style="font-weight: bold; border: none;">Régulation, correction ou avoir</th>
					<th style="font-weight: bold; border: none;">Total droits à payer</th>
				</tr>
			</thead>
			<tbody>
				<?php $details = $certification->getProduits(); 
					  $i = 1;?>

				<?php foreach($details as $detail): 
                        $i++; ?>
						<tr <?php if($i%2!=0) echo ' class="alt"'; ?>>
							<td><?php echo $detail->getLibelle(ESC_RAW) ?></td>
                            <td class="<?php echo isVersionnerCssClass($detail, 'stock_theorique') ?>"><strong><?php if ($detail->stock_theorique) echoFloat($detail->stock_theorique); else echoFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'stock_chais') ?>"><strong><?php if ($detail->stock_chais) echoFloat($detail->stock_chais); else echoFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'stock_propriete') ?>"><strong><?php if ($detail->stock_propriete) echoFloat($detail->stock_propriete); else echoFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_manquants_excedents') ?>"><strong><?php if ($detail->total_manquants_excedents) echoFloat($detail->total_manquants_excedents); else echoFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_pertes_autorisees') ?>"><strong><?php if ($detail->total_pertes_autorisees) echoFloat($detail->total_pertes_autorisees); else echoFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_manquants_taxables') ?>"><strong><?php if ($detail->total_manquants_taxables) echoFloat($detail->total_manquants_taxables); else echoFloat(0); ?></strong>&nbsp;<span class="unite">hl</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_regulation') ?>"><strong><?php if ($detail->total_regulation) echoFloat($detail->total_regulation); else echoFloat(0); ?></strong>&nbsp;<span class="unite">€</span></td>
							<td class="<?php echo isVersionnerCssClass($detail, 'total_droits_regulation') ?>"><strong><?php if ($detail->total_droits_regulation) echoFloat($detail->total_droits_regulation); else echoFloat(0); ?></strong>&nbsp;<span class="unite">€</span></td>
						</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php endforeach; ?>