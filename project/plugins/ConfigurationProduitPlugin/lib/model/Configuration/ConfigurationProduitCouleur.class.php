<?php
class ConfigurationProduitCouleur extends BaseConfigurationProduitCouleur
{
	const TYPE_NOEUD = 'couleur';
	const CODE_APPLICATIF_NOEUD = 'CO';

	public function getChildrenNode()
	{
      return $this->cepages;
    }

    public function getCertification()
    {
        return $this->getAppellation()->getCertification();
    }

    public function getGenre()
    {
        return $this->getAppellation()->getGenre();
    }

	public function getAppellation()
	{
        return $this->getCouleur()->getLieu()->getAppellation();
    }

    public function getMention()
    {
        return $this->getLieu()->getMention();
    }

    public function getLieu()
    {
    	return $this->getParentNode();
    }
	public function getCouleur()
    {
    	return $this;
    }

	public function hasCepage()
	{
    	return (count($this->cepages) > 1 || (count($this->cepages) == 1 && $this->cepages->getFirst()->getKey() != ConfigurationProduit::DEFAULT_KEY));
    }

	public function getTotalLieux()
	{
		return array();
	}

    public function getTotalCouleurs($onlyForDrmVrac = false, $cvoNeg = false, $date = null, $exception = null)
    {

    	if ($onlyForDrmVrac) {
    		if (!$this->getCurrentDrmVrac(true)) {
    			return array();
    		}
    	}

    	if ($exception) {
    		//echo '/'.str_replace('/', '\/', $exception).'/';exit;
    		if (preg_match('/'.str_replace('/', '\/', $exception).'/', $this->getHash())) {
    			return array();
    		}
    	}

    	if($cvoNeg){
    		return array($this->getHash() => $this);
    	}

    	return $this->getProduitWithTaux($date);

    }

    protected function getProduitWithTaux($date = null) {
        $date_cvo = (!$date)? date('Y-m-d') : $date;
        $droit = $this->getCurrentDroit(ConfigurationProduit::NOEUD_DROIT_CVO, $date_cvo, true);
        if($droit && $droit->taux >= 0 && $droit->taux !== null){
             return array($this->getHash() => $this);
        }
        return array();
    }
    
    public function getIdentifiantDouane() {
        $defautCep = null;
        if ($this->cepages->exist(ConfigurationProduit::DEFAULT_KEY)) {
            $defautCep = $this->cepages->get(ConfigurationProduit::DEFAULT_KEY);
        }
        return ($defautCep)? $defautCep->getIdentifiantDouane() : null;
    }

	/*
     * Les fonctions ci-dessous sont relatives à la gestion de la configuration du catalogue produit
     */

  	public function hasLabels() { return false; }

  	public function hasDepartements() { return false; }

	public function hasPrestations() { return false; }

  	public function hasCvo() { return true; }

  	public function hasDouane() { return false; }

  	public function hasDRMVrac() { return false; }

  	public function hasCiel() { return false; }

  	public function hasOIOC() { return false; }

  	public function hasDefinitionDrm() { return false; }

  	public function getTypeNoeud() { return self::TYPE_NOEUD; }

  	public function getCodeApplicatif() { return self::CODE_APPLICATIF_NOEUD; }

  	public function getCsvLibelle() { return ConfigurationProduitCsvFile::CSV_PRODUIT_COULEUR_LIBELLE; }

  	public function getCsvCode() { return ConfigurationProduitCsvFile::CSV_PRODUIT_COULEUR_CODE; }
}
