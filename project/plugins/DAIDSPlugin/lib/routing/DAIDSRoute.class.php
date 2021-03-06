<?php

class DAIDSRoute extends sfObjectRoute implements InterfaceEtablissementRoute 
{
    protected $daids = null;
    
    protected function getObjectForParameters($parameters) 
    {
        if (!preg_match('/^[0-9]{4}-[0-9]{2}/', $parameters['periode_version'])) {
            throw new InvalidArgumentException(sprintf('The "%s" route has an invalid parameter "%s" value "%s".', $this->pattern, 'periode_version', $parameters['periode_version']));
        }
        $this->daids = DAIDSClient::getInstance()->find('DAIDS-'.$parameters['identifiant'].'-'.$parameters['periode_version']);
        if(!$this->daids && isset($this->options['creation'])) {
            $this->daids = DAIDSClient::getInstance()->createDocByPeriode($parameters['identifiant'], $parameters['periode_version']);
        }
        if (!$this->daids) {
            throw new sfError404Exception(sprintf('No DAIDS found for this periode/version "%s".',  $parameters['periode_version']));
        }
		if (isset($this->options['no_archive']) && $this->options['no_archive'] === true && ($this->getEtablissement()->statut == Etablissement::STATUT_ARCHIVE) && !sfContext::getInstance()->getUser()->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {
			$this->redirect('daids_mon_espace', array('identifiant' => $this->getEtablissement()->identifiant));
		}
		if (isset($this->options['must_be_valid']) && $this->options['must_be_valid'] === true && !$this->daids->isValidee()) {
			$this->redirect('daids_not_validated', array('identifiant' => $this->getEtablissement()->identifiant, 'periode_version' => $this->getDAIDS()->getPeriodeAndVersion()));
		}
		if (isset($this->options['must_be_not_valid']) && $this->options['must_be_not_valid'] === true && $this->daids->isValidee()) {
			$this->redirect('daids_validated', array('identifiant' => $this->getEtablissement()->identifiant, 'periode_version' => $this->getDAIDS()->getPeriodeAndVersion()));
		}
		$this->checkSecurity($this->getEtablissement());
        return $this->daids;
    }

    protected function doConvertObjectToArray($object) 
    {  
        $parameters = array("identifiant" => $object->getIdentifiant(), "periode_version" => $object->getPeriodeAndVersion());
        return $parameters;
    }

    public function getDAIDS() {
        if (!$this->daids) {
            $this->daids = $this->getObject()->getDocument();
        }
        $object = $this->daids;
    	$user = sfContext::getInstance()->getUser();
    	if ($user->hasCredential(myUser::CREDENTIAL_OPERATEUR) && $object->type != DAIDSFictive::TYPE) {
    		$interpro = $user->getCompte()->getGerantInterpro();
    		$newobject = new DAIDSFictive($object, $interpro);
    		return $newobject;
    	} else {
    		return $object;
    	}
    }

    public function getEtablissement() {
        return $this->getDAIDS()->getEtablissement();
    }
    

    
    public function checkSecurity($etablissement = null) {
    	if (!$etablissement) {
    		return;
    	}
    	$user = sfContext::getInstance()->getUser();
    	$compte = $user->getCompte();
    	if (!$user->hasCredential(myUser::CREDENTIAL_OPERATEUR) && $compte->type == 'CompteTiers') {
    		if (!$compte->hasEtablissement($etablissement->get('_id'))) {
    			return $this->redirect('@acces_interdit');
    		}
    	}
    }
    
	public function redirect($url, $statusCode = 302)
	{
		if (is_object($statusCode) || is_array($statusCode))
		{
			$url = array_merge(array('sf_route' => $url), is_object($statusCode) ? array('sf_subject' => $statusCode) : $statusCode);
			$statusCode = func_num_args() >= 3 ? func_get_arg(2) : 302;
		}
		sfContext::getInstance()->getController()->redirect($url, 0, $statusCode);
		throw new sfStopException();
	}
    
    public function getDAIDSConfiguration() {
        return ConfigurationClient::getCurrent();
    }
}