<?php include_component('global', 'navBack', array('active' => 'statistiques', 'subactive' => 'bilan_drm')); ?>
<section id="contenu">
	<section id="principal">
		<div class="clearfix" id="application_dr">
    		<h1>Bilan des DRM saisies</h1>
    		<p>
    		<font color="red"><strong>0</strong> : DRM manquante</font><br />
    		<strong>0</strong> : DRM saisie non validée<br />
    		<font color="green"><strong>1</strong> : DRM saisie validée</font>
    		</p>
    		<br />
    		<div class="contenu clearfix">
	        	<?php include_partial('formCampagne', array('form' => $formCampagne)) ?>
	        </div>
	        <br />
    		<div class="tableau_ajouts_liquidations">
	    		<table class="tableau_recap">
	    			<thead>
		    			<tr>
		    				<th style="padding: 0 5px;"><strong>Etablissements</strong></th>
				    		<?php foreach ($bilan->getPeriodes() as $periode): ?>
				    		<th style="padding: 0;"><strong><?php echo $periode ?></strong></th>
				    		<?php endforeach; ?>
		    			</tr>
	    			</thead>
	    			<tbody>
			    		<?php 
			    			$etablissementsInformations = $bilan->getEtablissementsInformations();
			    			$drmsInformations = $bilan->getDRMsInformations();
			    			foreach ($bilan->getEtablissementsInformations() as $identifiant => $etablissement): 
			    			$informations = $etablissementsInformations[$identifiant];
			    		?>
			    			<tr>
			    				<td style="padding: 0 5px;">
			    					<?php echo $informations[StatistiquesBilanView::VALUE_ETABLISSEMENT_RAISON_SOCIALE] ?> <?php echo $informations[StatistiquesBilanView::VALUE_ETABLISSEMENT_NOM] ?> (<?php echo $identifiant ?>)<br />
			    					<?php echo $informations[StatistiquesBilanView::VALUE_ETABLISSEMENT_ADRESSE] ?><br />
			    					<?php echo $informations[StatistiquesBilanView::VALUE_ETABLISSEMENT_CODE_POSTAL] ?> <?php echo $informations[StatistiquesBilanView::VALUE_ETABLISSEMENT_COMMUNE] ?><br />
			    					<?php echo $informations[StatistiquesBilanView::VALUE_ETABLISSEMENT_PAYS] ?>
			    				</td>
					    		<?php 
					    			$drms = $drmsInformations[$identifiant];
					    			$precedente = null;
					    			foreach ($bilan->getPeriodes() as $periode): 
					    		?>
					    		<td style="padding: 0;">
					    			<strong>
					    			<?php if (!isset($drms[$periode]) && !$precedente): ?>
					    			<font color="red">0</font>
					    			<?php elseif (!isset($drms[$periode]) && $precedente && $precedente[StatistiquesBilanView::VALUE_DRM_TOTAL_FIN_DE_MOIS] > 0): ?>
					    			<font color="red">0</font>
					    			<?php elseif (isset($drms[$periode]) && !$drms[$periode][StatistiquesBilanView::VALUE_DRM_DATE_SAISIE]): ?>
					    			0
					    			<?php else: ?>
					    			<font color="green">1</font>
					    			<?php endif; ?>
					    			</strong>
					    		</td>
					    		<?php 
					    			if (isset($drms[$periode])) {
					    				$precedente = $drms[$periode];
					    			}
					    			endforeach; 
					    		?>
			    			</tr>
			    		<?php endforeach; ?>
	    			</tbody>
	    		</table>
    		</div>
    	</div>
	</section>
</section>