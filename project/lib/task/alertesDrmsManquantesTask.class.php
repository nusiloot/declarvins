<?php

class alertesDrmsManquantesTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagne au format AAAA-AAAA'),
        ));
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'alertes';
        $this->name = 'drms-manquantes';
        $this->briefDescription = 'Retourne les drms considérées comme manquantes';
        $this->detailedDescription = <<<EOF
The [alertes|INFO] task does things.
Call it with:
  Retourne les drms considérées comme manquantes
  [php symfony alertes|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        set_time_limit('3600');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        
        /* Mise a jour des messages si un csv est passé en argument */
        if (isset($arguments['campagne']) && !empty($arguments['campagne'])) {
			$drmsManquantes = new DRMsManquantes($arguments['campagne']);
			$drmsManquantes->showDrms();
    	}
    }

}