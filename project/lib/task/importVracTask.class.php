<?php

class importVracTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'declarvin'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      new sfCommandOption('file', null, sfCommandOption::PARAMETER_REQUIRED, null),
      new sfCommandOption('interpro', null, sfCommandOption::PARAMETER_REQUIRED, null),
      // add your own options here
    ));

    $this->namespace        = 'import';
    $this->name             = 'vrac';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF

EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

  	$csv = new CsvFile($options['file']);
    $lignes = $csv->getCsv();
    $vracClient = VracClient::getInstance();
    $vracConfiguration = ConfigurationClient::getCurrent()->getConfigurationVracByInterpro($options['interpro']);
    
    $numLigne = 0;
    foreach ($lignes as $ligne) {
    	$numLigne++;
    	$import = new VracDetailImport($ligne, $vracClient, $vracConfiguration);
    	$vrac = $import->getVrac();
    	if ($import->hasErrors()) {
    		$this->logSection('vrac', "echec de l'import du contrat ligne $numLigne", null, 'ERROR');
    		$this->logBlock($import->getLogs(), 'ERROR');
    	} else {
    		$vrac->save();
    		$this->logSection('vrac', $vrac->get('_id')." : succès de l'import du contrat ligne $numLigne.");
    	}
    }
  }
}