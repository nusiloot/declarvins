<table class="statistiques">
	<tr>
    	<th>Nombre de document :</th>
    	<td><?php echo $nbDoc ?></td>
    </tr>
<?php 
	foreach ($configFacets as $configFacet):
		$stats = $facets[$configFacet['nom']];
		$stat = $stats['total'];
		if ($n = $configFacet['divise']) {
			$stat = ($facets[$n]['total'] > 0)? $stats['total'] / $facets[$n]['total'] : 0;
		}
?>
	<tr>
    	<th><?php echo $configFacet['nom'] ?> : </th>
    	<td><?php echo number_format($stat, 2, ',', ' '); ?><strong><?php echo $configFacet['unite'] ?></strong>
    	</td>
    </tr>
<?php endforeach; ?>
</table>