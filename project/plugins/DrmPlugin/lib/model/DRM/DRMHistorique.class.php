<?php
class DRMHistorique
{
	const VIEW_INDEX_ETABLISSEMENT = 0;
	const VIEW_INDEX_ANNEE = 1;
	const VIEW_INDEX_MOIS = 2;
	const VIEW_INDEX_RECTIFICATIVE = 3;
	const VIEW_INDEX_STATUS = 4;
	const VIEW_INDEX_STATUS_DOUANE_ENVOI = 5;
	const VIEW_INDEX_STATUS_DOUANE_ACCUSE = 6;
	const DERNIERE = 'derniere';

	private $etablissement;
	private $anneeCourante;
	private $drms;
	private $annees;
	
	public function __construct($etablissement, $anneeCourante = null)
	{
		$this->etablissement = $etablissement;
		$this->anneeCourante = $anneeCourante;
	}
	
	public function getSliceDrms($limit = 0) {
		return array_slice($this->getDrms(), 0, $limit);
	}
	
	public function getDrms($limite = 0)
	{
		if (!$this->drms) {
			$this->loadDrms();
		}
		return $this->drms;
	}
	
	private function loadDrms()
	{
		$drms = acCouchdbManager::getClient()
						->startkey(array($this->etablissement, null))
    					->endkey(array($this->etablissement, array()))
    					->reduce(false)
    					->getView("drm", "all")
    					->rows;

		$result = array();
		foreach ($drms as $drm) {
		  $result[$drm->id] = $drm->key;
		}
		krsort($result);

		$campagne = null;
		foreach($result as $key => $item) {
			if ($item[self::VIEW_INDEX_ANNEE].'-'.$item[self::VIEW_INDEX_MOIS] != $campagne) {
				$result[$key][self::DERNIERE] = true;
				$campagne = $item[self::VIEW_INDEX_ANNEE].'-'.$item[self::VIEW_INDEX_MOIS];
			} else {
				$result[$key][self::DERNIERE] = false;
			}
		}
		$this->drms = $result;
	}
	
	public function getAnnees()
	{
		if (!$this->annees) {
			$annees = array();
			$drms = $this->getDrms();
	    	foreach ($drms as $drm) {
		  	if (!in_array($drm[self::VIEW_INDEX_ANNEE], $annees)) {
	  				$annees[] = $drm[self::VIEW_INDEX_ANNEE];
	    		}
	  		}
	  		rsort($annees);
	  		$this->annees = $annees;
		}
		return $this->annees;
	}
	
	public function getDrmsParAnneeCourante()
	{
		$drmsAnnee = array();
		$anneeCourante = $this->getAnneeCourante();
		$drms = $this->getDrms();
		foreach ($drms as $id => $drm) {
			if ($drm[1] == $anneeCourante) {
				$drmsAnnee[$id] = $drm;
			}
		}
		return $drmsAnnee;
	}
	
	public function getAnneeCourante()
	{
		if (!$this->anneeCourante) {
			if($annees = $this->getAnnees()) {
				$this->anneeCourante = $annees[0];
			}
		}
		return $this->anneeCourante;
	}
	
	public function getFutureDrm()
	{
		$lastDrm = current($this->getLastDrm());
		if (!$lastDrm) {
		  return array('DRM-'.$this->etablissement.'-'.date('Y').'-'.date('m') => array($this->etablissement, date('Y'), sprintf("%02d", date('m')), 0, null, null));
		}
		$nextMonth = $lastDrm[self::VIEW_INDEX_MOIS] + 1;
		$nextYear = $lastDrm[self::VIEW_INDEX_ANNEE];
		if ($nextMonth > 12) {
			$nextMonth = '01';
			$nextYear++;
		}
	        $nextMonth = sprintf("%02d", $nextMonth);
		return array('DRM-'.$this->etablissement.'-'.$nextYear.'-'.$nextMonth => array($this->etablissement, $nextYear, $nextMonth, 0, null, null));
	}

	public function getLastDrm()
	{
		return $this->getSliceDrms(1);
	}
	
	public function hasDrmInProcess()
	{
		$result = false;
		$drms = $this->getDrms();
		foreach ($drms as $drm) {
			if (!$drm[self::VIEW_INDEX_STATUS]) {
				$result = true;
				break;
			}
		}
		return $result;
	}
}