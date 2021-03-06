<?php echo include_partial('Email/headerMail') ?>
 	
Bonjour,<br /><br />
Vous souhaitez créer un nouveau mot de passe pour accéder à la plateforme Declarvins.net.<br /><br />
Le ou les comptes suivants sont liés à votre adresse email :<br />
<ul>
<?php foreach ($logins as $login): ?>
	<li>Login : <strong><?php echo $login ?></strong>, merci de suivre la procédure suivante en cliquant sur ce lien : <a href="<?php echo ProjectConfiguration::getAppRouting()->generate('compte_password', array('login' => $login, 'rev' => $compte->_rev), true); ?>">Redéfinition de mon mot de passe</a></li>
<?php endforeach; ?>
</ul>
Vous pourrez ainsi accéder à la plateforme en vous connectant avec vos identifiants (login et mot de passe nouvellement créé).
Pour toute question, n'hésitez pas à <a href="<?php echo ProjectConfiguration::getAppRouting()->generate('contact', array(), true); ?>">contacter votre interprofession</a><br /><br />
L'équipe Declarvins.net

<?php echo include_partial('Email/footerMail') ?>