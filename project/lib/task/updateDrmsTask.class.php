<?php

class generateDrmsTask extends sfBaseTask {

    protected function configure() {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'update';
        $this->name = 'drms';
        $this->briefDescription = 'Génère les drms suivantes de drm au stock null';
        $this->detailedDescription = <<<EOF
The [alertes|INFO] task does things.
Call it with:
  Génère les drms suivantes de drm au stock null
  [php symfony alertes|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        set_time_limit('3600');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        $campagne = null;
        
        $drms = DRMClient::getInstance()->findAll();
        $i = 1;
        foreach ($drms->rows as $d) {
        	$drm = DRMClient::getInstance()->find($d->id);
			$drm->update();
        	$drm->save();
			$this->logSection('drm', $d->id.' OK '.$i);
			$i++;
        }
    }

}