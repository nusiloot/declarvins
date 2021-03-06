<?php include_component('global', 'navBack', array('active' => 'parametrage', 'subactive' => 'libelles')); ?>
<section id="contenu">
	<section id="principal">
		<div class="clearfix" id="application_dr">
    		<h1 id="messages">Messages</h1>
    		<?php include_partial('tableLibelles', array('object' => $messages, 'type' => 'messages')) ?>
    		<!-- <h1>Droits</h1> -->
    		<?php //include_partial('tableLibelles', array('object' => $droits, 'type' => 'droits')) ?>
    		<h1 id="labels">Labels</h1>
    		<?php include_partial('tableLibelles', array('object' => $labels, 'type' => 'labels')) ?>
    		<h1 id="controles">Controles</h1>
    		<?php include_partial('tableLibelles', array('object' => $controles, 'type' => 'controles')) ?>
    		<h1 id="contrat_vrac">Contrat Vrac</h1>
	    	<div class="tableau_ajouts_liquidations">
				<table class="tableau_recap">
					<thead>
						<tr>
							<th><strong>Code</strong></th>
							<th><strong>Libellé</strong></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<tr>
						<td rowspan="<?php echo count($configurationVrac->clauses); ?>">Clauses</td>
<?php foreach ($configurationVrac->clauses as $k => $c) : ?>
								<td><?php echo $c->nom." : ".$c->description; ?></td>
								<td class="actions">
									<a class="btn_modifier" href="<?php echo url_for('admin_libelles_edit', array('type' => 'vrac', 'key' => 'clauses@'.$k.'@nom')) ?>">Nom</a><br/>
									<a class="btn_modifier" href="<?php echo url_for('admin_libelles_edit', array('type' => 'vrac', 'key' => 'clauses@'.$k.'@description')) ?>">Description</a>
								</td>
</tr><tr>
<?php endforeach; ?>
					</tr>
<?php if (count($configurationVrac->clauses_complementaires)): ?>
					<tr>
						<td rowspan="<?php echo count($configurationVrac->clauses_complementaires); ?>">Clauses complémentaires</td>
<?php foreach ($configurationVrac->clauses_complementaires as $k => $c) : ?>
								<td><?php echo $c->nom." : ".$c->description; ?></td>
								<td class="actions">
									<a class="btn_modifier" href="<?php echo url_for('admin_libelles_edit', array('type' => 'vrac', 'key' => 'clauses_complementaires@'.$k.'@nom')) ?>">Nom</a><br/>
									<a class="btn_modifier" href="<?php echo url_for('admin_libelles_edit', array('type' => 'vrac', 'key' => 'clauses_complementaires@'.$k.'@description')) ?>">Description</a>
								</td>
</tr><tr>
<?php endforeach; ?>
					</tr>
<?php endif; ?>
					<tr class="alt">
						<td>Informations complémentaires</td>
						<td><?php echo $configurationVrac->informations_complementaires; ?></td>
						<td class="actions"><a class="btn_modifier"
							href="<?php echo url_for('admin_libelles_edit', array('type' => 'vrac', 'key' => 'informations_complementaires')) ?>">Edit</a>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</section>