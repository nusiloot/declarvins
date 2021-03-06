<?php
/**
 * Model for DRMCiel
 *
 */

class DRMCiel extends BaseDRMCiel 
{
	const VALIDATE_DAY = 27;
	
	public function setInformationsFromXml()
	{

		$this->transfere = 0;
		$this->valide = 0;
		$this->identifiant_declaration = null;
		$this->horodatage_depot = null;
		if ($reponseCiel = $this->getReponseCiel()) {
			$identifiantdeclaration = "".$reponseCiel->{'identifiant-declaration'};
			$horodatageDepot = "".$reponseCiel->{'horodatage-depot'};
			if ($identifiantdeclaration && $horodatageDepot) {
				$this->transfere = 1;
				$this->identifiant_declaration = $identifiantdeclaration;
				$this->horodatage_depot = $horodatageDepot;
			}
		}
	}
	
	public function getReponseCiel()
	{
		return ($this->xml)? new SimpleXMLElement(trim($this->xml)) : null;
	}
	
	public function isTransfere()
	{
		return ($this->transfere && $this->identifiant_declaration && $this->horodatage_depot)? true : false;
	}
	
	public function isValide()
	{
		return ($this->isTransfere() && $this->valide)? true : false;
	}
	
	public function hasErreurs()
	{
		return (count($this->getErreurs()) > 0);
	}
	
	public function getErreurs()
	{
		$erreurs = array();
		if ($this->xml && !$this->isTransfere()) {
			$reponseCiel = $this->getReponseCiel();
			$known = false;
			if (preg_match('/HTTP Error 0/', html_entity_decode($this->xml)) || preg_match('/permission to access .authtoken.oauth2/', html_entity_decode($this->xml))) {
			    $erreurs[] = "<strong>Le service de reception des DRM de la Douane est indisponible pour le moment</strong>";
				$known = true;
			}
			if (!$known && isset($reponseCiel->{'erreurs-fonctionnelles'})) {
				foreach ($reponseCiel->{'erreurs-fonctionnelles'}->{'erreur-fonctionnelle'} as $erreurFonctionnelle) {
					if (isset($erreurFonctionnelle->{'message-erreur'})) {
						$erreurs[] =  $erreurFonctionnelle->{'message-erreur'};
						$known = true;
					}
				}
			}
			if (!$known && isset($reponseCiel->{'erreur-technique'})) {
				if (isset($reponseCiel->{'erreur-technique'}->{'message-erreur'})) {
					$erreurs[] =  $reponseCiel->{'erreur-technique'}->{'message-erreur'};
					$known = true;
				}
			}
			if (!$known && isset($reponseCiel->{'erreur-interne'})) {
				if (isset($reponseCiel->{'erreur-interne'}->{'message-erreur'})) {
					$erreurs[] =  $reponseCiel->{'erreur-interne'}->{'message-erreur'};
					$known = true;
				}
			}
			if (!$known) {
				$erreurs[] =  $this->xml;
			}
		} elseif (!$this->isTransfere() && $this->xml === false) {
			$erreurs[] =  "Le service CIEL - Prodouane ne répond pas. Veuillez réessayer ultérieurement.";
		}
		return $erreurs;
	}

}