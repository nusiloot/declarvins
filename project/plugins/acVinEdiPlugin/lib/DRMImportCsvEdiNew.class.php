<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DRMImportCsvEdi
 *
 */
class DRMImportCsvEdiNew extends DRMCsvEdi {

    protected $configuration = null;
    protected $mouvements = array();
    protected $csvDoc = null;

    public function __construct($file, DRM $drm = null) 
    {
        $this->configuration = ConfigurationClient::getCurrent();
        if(is_null($this->csvDoc)) {
            $this->csvDoc = CSVClient::getInstance()->createOrFindDocFromDRM($file, $drm);
        }
        parent::__construct($file, $drm);
    }

    public function getCsvDoc() 
    {
        return $this->csvDoc;
    }

    public function getDocRows() 
    {
        return $this->getCsv($this->csvDoc->getFileContent());
    }

    /**
     * CHECK DU CSV
     */
    public function checkCSV() {
        $this->csvDoc->clearErreurs();
        $this->checkCSVIntegrity();
        
        if ($this->csvDoc->hasErreurs()) {
            $this->csvDoc->setStatut(self::STATUT_ERREUR);
            $this->csvDoc->save();
            return;
        }
        

        if ($this->csvDoc->hasErreurs()) {
            $this->csvDoc->setStatut(self::STATUT_WARNING);
            $this->csvDoc->save();
            return;
        }
        $this->csvDoc->setStatut(self::STATUT_VALIDE);
        $this->csvDoc->save();
    }

    /**
     * IMPORT DEPUIS LE CSV CHECK
     */
    public function importCSV1($withSave = true) {
        $this->importAnnexesFromCSV();

        $this->importMouvementsFromCSV();
        $this->importCrdsFromCSV();
        //$this->drm->teledeclare = true;
        $this->drm->etape = DRMClient::ETAPE_VALIDATION;
        $this->drm->type_creation = DRMClient::DRM_CREATION_EDI;
        $this->drm->buildFavoris();
        $this->drm->storeDeclarant();
        $this->drm->initSociete();
        $this->updateAndControlCoheranceStocks();

        if($withSave) {
            $this->drm->save();
        }
    }

    public function updateAndControlCoheranceStocks() {

        $this->drm->update();

        if ($this->csvDoc->hasErreurs()) {
            $this->csvDoc->setStatut(self::STATUT_WARNING);
            $this->csvDoc->save();
        }
    }
    /*
     * FIN CHECK
     */
    
    public function importCsv()
    {
        $numLigne = 0;
    	foreach ($this->getDocRows() as $csvRow) {
            $numLigne++;
            if ($numLigne == 1 && KeyInflector::slugify($csvRow[self::CSV_TYPE]) == 'TYPE') {
                continue;
            }
    		switch($csvRow[self::CSV_TYPE]) {
    			case self::TYPE_CAVE:
    				$this->importCave($numLigne, $csvRow);
    				break;
    			case self::TYPE_CONTRAT:
    				$this->importRetiraison($numLigne, $csvRow);
    				break;
    			case self::TYPE_CRD:
    				$this->importCrd($numLigne, $csvRow);
    				break;
    			case self::TYPE_ANNEXE:
    				$this->importAnnexe($numLigne, $csvRow);
    				break;
    			default:
    				break;
    		}
    	}
    	if ($this->csvDoc->hasErreurs()) {
    		$this->csvDoc->setStatut(self::STATUT_ERREUR);
    		$this->csvDoc->save();
    		return;
    	}
    }
    
    private function importCave($numLigne, $datas)
  	{
		$hash = $this->getHashProduit($datas);
    	if (!$this->configuration->getConfigurationProduit($hash)) {
    		$this->csvDoc->addErreur($this->productNotFoundError($numLigne, $datas));
    		return;
  		}
  		$produit = $this->drm->addProduit($hash, array());
  		
  		$categorieMvt = $datas[self::CSV_CAVE_CATEGORIE_MOUVEMENT];
  		$typeMvt = $datas[self::CSV_CAVE_TYPE_MOUVEMENT];
  		$valeur = $datas[self::CSV_CAVE_VOLUME];
  		
  		if ($this->mouvements) {
	  		if ($categorieMvt && !array_key_exists($categorieMvt, $this->mouvements)) {
	  			$this->csvDoc->addErreur($this->categorieMouvementNotFoundError($numLigne, $datas));
	  			return;
	  		}
	  		if (!array_key_exists($typeMvt, $this->mouvements[$categorieMvt])) {
	  			$this->csvDoc->addErreur($this->typeMouvementNotFoundError($numLigne, $datas));
	  			return;
	  		}
  		} else {
  			if ($categorieMvt && !$produit->exist($categorieMvt)) {
	  			$this->csvDoc->addErreur($this->categorieMouvementNotFoundError($numLigne, $datas));
	  			return;
	  		}
  			if ($categorieMvt && !$produit->get($categorieMvt)->exist($typeMvt)) {
	  			$this->csvDoc->addErreur($this->typeMouvementNotFoundError($numLigne, $datas));
	  			return;
	  		} elseif(!$categorieMvt && !$produit->exist($typeMvt)) {
	  			$this->csvDoc->addErreur($this->typeMouvementNotFoundError($numLigne, $datas));
	  			return;
	  		}
  		}
  		
  		if (!is_numeric($valeur) || $valeur < 0) {
	  		$this->csvDoc->addErreur($this->valeurMouvementNotValidError($numLigne, $datas));
	  		return;  			
  		}
  		
  		$mvt = ($categorieMvt)? $produit->getOrAdd($categorieMvt) : $produit;
  		$mvt->add($typeMvt, round($this->floatize($valeur), 2));
    }
    
    private function importRetiraison($numLigne, $datas)
  	{
  		$hash = $this->getHashProduit($datas);
  		if (!$this->configuration->getConfigurationProduit($hash)) {
  			$this->csvDoc->addErreur($this->productNotFoundError($numLigne, $datas));
  			return;
  		}
  		
  		$produit = $this->drm->addProduit($hash, array());
  		
  		$numContrat = $datas[self::CSV_CONTRAT_CONTRATID];
  		$valeur = $datas[self::CSV_CONTRAT_VOLUME];
  		
		if (!$produit->hasSortieVrac()) {
	  		$this->csvDoc->addErreur($this->retiraisonNotAllowedError($numLigne, $datas));
	  		return;
		}
		
		$contrats = $produit->getContratsVrac();
		$exist = false;
		foreach ($contrats as $contrat) {
			if ($numContrat == $contrat->getNumeroContrat()) {
				$exist = true;
				break;
			}
		}
		
		if (!$exist) {
	  		$this->csvDoc->addErreur($this->contratNotFoundError($numLigne, $datas));
	  		return;
		}

  		if (!is_numeric($valeur) || $valeur < 0) {
  			$this->csvDoc->addErreur($this->valeurMouvementNotValidError($numLigne, $datas));
  			return;
  		}
  		
  		
  		$produit->addVrac($numContrat, round($this->floatize($valeur), 2));
    }
    
    private function importCrd($numLigne, $datas)
  	{
  		$categorie = $datas[self::CSV_CRD_COULEUR];
  		$type = $datas[self::CSV_CRD_GENRE];
  		$centilisation = $datas[self::CSV_CRD_CENTILITRAGE];
  		
  		$categorieCrd = $datas[self::CSV_CRD_CATEGORIE_KEY];
  		$typeCrd = $datas[self::CSV_CRD_TYPE_KEY];
  		$valeur = $datas[self::CSV_CRD_QUANTITE];
  		
  		if (!$this->configuration->isCentilisationCrdAccepted($centilisation)) {
  			$this->csvDoc->addErreur($this->centilisationCrdNotFoundError($numLigne, $datas));	
  			return;
  		}
  		if (!$this->configuration->isCategorieCrdAccepted($categorie)) {
  			$this->csvDoc->addErreur($this->categorieCrdNotFoundError($numLigne, $datas));  	
  			return;			
  		}
  		if (!$this->configuration->isTypeCrdAccepted($type)) {
  			$this->csvDoc->addErreur($this->typeCrdNotFoundError($numLigne, $datas));  	
  			return;
  		}
  		
  		$crd = $this->drm->addCrd($categorie, $type, $centilisation);
  		
  		if ($categorieCrd && !$crd->exist($categorieCrd)) {
  			$this->csvDoc->addErreur($this->categorieCrdMvtNotFoundError($numLigne, $datas));
  			return;
  		}
  		if ($categorieCrd && !$crd->get($categorieCrd)->exist($typeCrd)) {
  			$this->csvDoc->addErreur($this->typeCrdMvtNotFoundError($numLigne, $datas));
  			return;
  		} elseif(!$categorieCrd && !$crd->exist($typeCrd)) {
  			$this->csvDoc->addErreur($this->typeCrdMvtNotFoundError($numLigne, $datas));
  			return;
  		}
  		
  		if (!is_numeric($valeur) || $valeur < 0 || intval($valeur) != $valeur) {
	  		$this->csvDoc->addErreur($this->valeurCrdMouvementNotValidError($numLigne, $datas));
	  		return;  			
  		}
  		
  		$mvt = ($categorieCrd)? $crd->getOrAdd($categorieCrd) : $crd;
  		$mvt->add($typeCrd, round($this->floatize($valeur), 2));
  		
    }
    
    private function importAnnexe($numLigne, $datas)
  	{
    	switch ($datas[self::CSV_ANNEXE_TYPEANNEXE]) {
    		case self::TYPE_ANNEXE_NONAPUREMENT:
    			$this->importNonApurement($numLigne, $datas);
    			break;
    		
    		case self::TYPE_ANNEXE_DOCUMENT:
    			$this->importDocument($numLigne, $datas);
    			break;
    			
    		case self::TYPE_ANNEXE_OBSERVATIONS:
    			$this->importObservations($numLigne, $datas);
    			break;
    			
    		case self::TYPE_ANNEXE_STATISTIQUES:
    			$this->importStatistiques($numLigne, $datas);
    			break;
    			
    		case self::TYPE_ANNEXE_SUCRE:
    			$this->importSucre($numLigne, $datas);
    			break;
    			
    		default:
    			break;
    	}
    }
    
    private function importNonApurement($numLigne, $datas)
    {
    	
    }
    
    private function importDocument($numLigne, $datas)
    {
    	$declaratif = $this->drm->getImportableDeclaratif();

    	$categorie = $datas[self::CSV_ANNEXE_CATMVT];
    	$type = $datas[self::CSV_ANNEXE_TYPEMVT];
    	$valeur = $datas[self::CSV_ANNEXE_QUANTITE];
    	
    	if (!$categorie || !$declaratif->exist($categorie)) {
    		$this->csvDoc->addErreur($this->categorieAnnexeNotFoundError($numLigne, $datas));
    		return;
    	}
    	if (!$declaratif->get($categorie)->exist($type)) {
    		$this->csvDoc->addErreur($this->typeAnnexeNotFoundError($numLigne, $datas));
    		return;
    	}
    	
    	if (!is_numeric($valeur) || $valeur < 0 || intval($valeur) != $valeur) {
    		$this->csvDoc->addErreur($this->valeurAnnexeNotValidError($numLigne, $datas));
    		return;
    	}
  		
  		$mvt = $declaratif->getOrAdd($categorie);
  		$mvt->add($type, round($this->floatize($valeur), 2));
    }
    
    private function importObservations($numLigne, $datas)
    {
    	$hash = $this->getHashProduit($datas);
    	if (!$this->configuration->getConfigurationProduit($hash)) {
    		$this->csvDoc->addErreur($this->productNotFoundError($numLigne, $datas));
    		return;
    	}
    	
    	$produit = $this->drm->addProduit($hash, array());
    	
    	if (!$datas[self::CSV_ANNEXE_OBSERVATION]) {
    		$this->csvDoc->addErreur($this->observationsEmptyError($numLigne, $datas));
    		return;    		
    	}
    	
    	$produit->setObservations($datas[self::CSV_ANNEXE_OBSERVATION]);
    }
    
    private function importStatistiques($numLigne, $datas)
    {

    	$declaratif = $this->drm->getImportableDeclaratif();
    	
    	$categorie = $datas[self::CSV_ANNEXE_CATMVT];
    	$type = $datas[self::CSV_ANNEXE_TYPEMVT];
    	$valeur = $datas[self::CSV_ANNEXE_QUANTITE];
    	 
    	if (!$categorie || !$declaratif->exist($categorie)) {
    		$this->csvDoc->addErreur($this->categorieAnnexeNotFoundError($numLigne, $datas));
    		return;
    	}
    	if (!$declaratif->get($categorie)->exist($type)) {
    		$this->csvDoc->addErreur($this->typeAnnexeNotFoundError($numLigne, $datas));
    		return;
    	}
    	 
    	if (!is_numeric($valeur) || $valeur < 0) {
    		$this->csvDoc->addErreur($this->valeurStatistiqueNotValidError($numLigne, $datas));
    		return;
    	}
    	
    	$mvt = $declaratif->getOrAdd($categorie);
    	$mvt->add($type, round($this->floatize($valeur), 2));
    }
    
    private function importSucre($numLigne, $datas)
    {
    	$quantite = str_replace(',', '.', $datas[self::CSV_ANNEXE_QUANTITE]);
    	if (!is_numeric($quantite) || $quantite < 0) {
    		$this->csvDoc->addErreur($this->sucreWrongFormatError($num_ligne, $csvRow));
    		return;
    	}
    	$this->drm->setImportableSucre($quantite);
    }

    private function checkCSVIntegrity() {
        $ligne_num = 1;
        $periodes = array();
        $accises = array();
        foreach ($this->getDocRows() as $csvRow) {
            if ($ligne_num == 1 && KeyInflector::slugify($csvRow[self::CSV_TYPE]) == 'TYPE') {
                $ligne_num++;
                continue;
            }
            if (!in_array($csvRow[self::CSV_TYPE], self::$permitted_types)) {
                $this->csvDoc->addErreur($this->createWrongFormatTypeError($ligne_num, $csvRow));
            }
            if (!preg_match('/^[0-9]{4}-[0-9]{2}$/', $csvRow[self::CSV_PERIODE])) {
                $this->csvDoc->addErreur($this->createWrongFormatPeriodeError($ligne_num, $csvRow));
            } else {
            	$periodes[$csvRow[self::CSV_PERIODE]] = 1;
            }
            if (!preg_match('/^FR0[0-9]{10}$/', $csvRow[self::CSV_NUMACCISE])) {
                $this->csvDoc->addErreur($this->createWrongFormatNumAcciseError($ligne_num, $csvRow));
            } else {
            	$accises[$csvRow[self::CSV_NUMACCISE]] = 1;
            }
            $ligne_num++;
        }
        if (count($periodes) > 1) {
        	$this->csvDoc->addErreur($this->createMultiPeriodeError());
        }
        if (count($accises) > 1) {
        	$this->csvDoc->addErreur($this->createMultiAcciseError());
        }
    }
    
    private function createMultiPeriodeError() {
        return $this->createError(0, 'DRM', "Import limité à une seule période");
    }
    private function createMultiAcciseError() {
        return $this->createError(0, 'DRM', "Import limité à un seul numéro d'EA");
    }
    
    private function createWrongFormatTypeError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, KeyInflector::slugify($csvRow[self::CSV_TYPE]), "Choix possible type : " . implode(', ', self::$permitted_types));
    }

    private function createWrongFormatPeriodeError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, KeyInflector::slugify($csvRow[self::CSV_PERIODE]), "Format période : AAAA-MM");
    }

    private function createWrongFormatNumAcciseError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, KeyInflector::slugify($csvRow[self::CSV_NUMACCISE]), "Format numéro d'accise : FR0XXXXXXXXXX");
    }
    
  	private function centilisationCrdNotFoundError($num_ligne, $csvRow) {
  		return $this->createError($num_ligne, $csvRow[self::CSV_CRD_CENTILITRAGE], "La centilisation CRD n'a pas été reconnue");
  	}
  	
  	private function categorieCrdNotFoundError($num_ligne, $csvRow) {
  		return $this->createError($num_ligne, $csvRow[self::CSV_CRD_COULEUR], "La catégorie fiscale CRD n'a pas été reconnue");
  	}
  	
  	private function typeCrdNotFoundError($num_ligne, $csvRow) {
  		return $this->createError($num_ligne, $csvRow[self::CSV_CRD_GENRE], "Le type CRD n'a pas été reconnu");
  	}
  	
  	private function categorieAnnexeNotFoundError($num_ligne, $csvRow) {
  		return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_CATMVT], "La catégorie d'annexe n'a pas été reconnue");
  	}
  	
  	private function typeAnnexeNotFoundError($num_ligne, $csvRow) {
  		return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_TYPEMVT], "Le type d'annexe n'a pas été reconnu");
  	}

    private function retiraisonNotAllowedError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CONTRAT_CONTRATID], "Aucune sortie cave ne permet la retiraison du contrat");
    }

    private function contratNotFoundError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CONTRAT_CONTRATID], "Le contrat n'a pas été trouvé");
    }

    private function productNotFoundError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CAVE_PRODUIT], "Le produit n'a pas été trouvé");
    }

    private function categorieMouvementNotFoundError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CAVE_CATEGORIE_MOUVEMENT], "La catégorie de mouvement n'a pas été trouvée");
    }

    private function typeMouvementNotFoundError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CAVE_TYPE_MOUVEMENT], "Le type de mouvement n'a pas été trouvé");
    }

    private function categorieCrdMvtNotFoundError($num_ligne, $csvRow) {
    	return $this->createError($num_ligne, $csvRow[self::CSV_CRD_CATEGORIE_KEY], "La catégorie de mouvement de CRD n'a pas été trouvée");
    }
    
    private function typeCrdMvtNotFoundError($num_ligne, $csvRow) {
    	return $this->createError($num_ligne, $csvRow[self::CSV_CRD_TYPE_KEY], "Le type de mouvement de CRD n'a pas été trouvé");
    }
    
    private function valeurMouvementNotValidError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CAVE_VOLUME], "La valeur doit être un nombre positif");
    }
    
    private function valeurCrdMouvementNotValidError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CRD_QUANTITE], "La valeur doit être un nombre entier positif");
    }
    
    private function valeurAnnexeNotValidError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_QUANTITE], "La valeur doit être un nombre entier positif");
    }
    
    private function valeurStatistiqueNotValidError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_QUANTITE], "La valeur doit être un nombre positif");
    }

    private function exportPaysNotFoundError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CAVE_EXPORTPAYS], "Le pays d'export n'a pas été trouvé");
    }

    private function contratIDEmptyError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CAVE_CONTRATID], "L'id du contrat ne peut pas être vide");
    }

    private function contratIDNotFoundError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_CAVE_CONTRATID], "Le contrat n'a pas été trouvé");
    }

    private function observationsEmptyError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_OBSERVATION], "Les observations sont vides.");
    }

    private function sucreWrongFormatError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_QUANTITE], "La quantité de sucre est nulle ou possède un mauvais format.");
    }

    private function typeDocumentWrongFormatError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_TYPEANNEXE], "Le type de document d'annexe n'est pas connu.");
    }

    private function annexesTypeMvtWrongFormatError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_TYPEMVT], "Le type d'enregistrement des " . $csvRow[self::CSV_ANNEXE_TYPEANNEXE] . " doit être 'début' ou 'fin' .");
    }

    private function annexesNumeroDocumentError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_TYPEANNEXE], "Le numéro de document ne peut pas être vide.");
    }

    private function annexesNonApurementWrongDateError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_NONAPUREMENTDATEEMISSION], "La date est vide ou mal formattée.");
    }

    private function annexesNonApurementWrongNumAcciseError($num_ligne, $csvRow) {
        return $this->createError($num_ligne, $csvRow[self::CSV_ANNEXE_NONAPUREMENTACCISEDEST], "La numéro d'accise du destinataire est vide ou mal formatté.");
    }

    private function createError($num_ligne, $erreur_csv, $raison) {
        $error = new stdClass();
        $error->num_ligne = $num_ligne;
        $error->erreur_csv = $erreur_csv;
        $error->raison = $raison;
        return $error;
    }

    private function getHashProduit($datas)
    {
    	$hash = 'declaration/certifications/'.$this->getKey($datas[self::CSV_CAVE_CERTIFICATION]).
    	'/genres/'.$this->getKey($datas[self::CSV_CAVE_GENRE], true).
    	'/appellations/'.$this->getKey($datas[self::CSV_CAVE_APPELLATION], true).
    	'/mentions/'.$this->getKey($datas[self::CSV_CAVE_MENTION], true).
    	'/lieux/'.$this->getKey($datas[self::CSV_CAVE_LIEU], true).
    	'/couleurs/'.strtolower($this->couleurKeyToCode($datas[self::CSV_CAVE_COULEUR])).
    	'/cepages/'.$this->getKey($datas[self::CSV_CAVE_CEPAGE], true);
    	return $hash;
    }
     
    private function getKey($key, $withDefault = false)
    {
    	if ($key == " " || !$key) {
    		$key = null;
    	}
    	if ($withDefault) {
    		return ($key)? $key : ConfigurationProduit::DEFAULT_KEY;
    	} else {
    		return $key;
    	}
    }
    
    private function couleurKeyToCode($key)
    {
    	$correspondances = array(1 => "rouge",
    			2 => "rose",
    			3 => "blanc");
    	if (!in_array($key, array_keys($correspondances))) {
    		return $key;
    	}
    	return $correspondances[$key];
    }	


  	private function floatize($value)
  	{
  		if ($value === null) {
  			return null;
  		}
  		return floatval(str_replace(',', '.', $value));
  	}

}