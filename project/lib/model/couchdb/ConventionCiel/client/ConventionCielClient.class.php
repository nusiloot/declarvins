<?php

class ConventionCielClient extends acCouchdbClient 
{
    public static function getInstance()
    {
      return acCouchdbManager::getClient("ConventionCiel");
    }
    
    /**
     *
     * @param string $login
     * @param integer $hydrate
     * @return Contrat
     */
    public function retrieveById($id, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
    	return parent::find('CONVENTIONCIEL-'.$id, $hydrate);
    }
    
    public function createObject($compte)
    {
    	$convention = new ConventionCiel();
    	$convention->set('_id', 'CONVENTIONCIEL-'.$compte->login);
    	$convention->no_convention = $compte->login;
    	$convention->compte = $compte->_id;
    	$convention->date_saisie = date('c');
    	$convention->nom = $compte->nom;
    	$convention->prenom = $compte->prenom;
    	$convention->fonction = $compte->fonction;
    	$convention->email = $compte->email;
    	$convention->telephone = $compte->telephone;
    	$convention->email_beneficiaire = $compte->email;
    	$convention->telephone_beneficiaire = $compte->telephone;
    	$convention->date_ciel = date('Y-m-d');
    	$convention->representant_legal = 0;
    	$convention->valide = 0;
    	$interpro = array();
    	$nbEtablissement = 0;
    	$currentEtab = null;
    	for ($i=0; $i<3; $i++) {
    		$convention->habilitations->add();
    	}
    	foreach ($compte->getTiersCollection() as $etablissement) {
    		if (!$etablissement->hasDroit(EtablissementDroit::DROIT_DRM_DTI)) {
    			continue;
    		}
    		if ($etablissement->statut != Etablissement::STATUT_ACTIF) {
    			continue;
    		}
    		if ($nbEtablissement >= 2) {
    			break;
    		}
    		$nbEtablissement++;
    		$currentEtab = $etablissement;
    		$etab = $convention->etablissements->getOrAdd($etablissement->_id);
    		$etab->nom = $etablissement->nom;
    		$etab->raison_sociale = $etablissement->raison_sociale;
    		$etab->siret = $etablissement->siret;
    		$etab->cni = $etablissement->cni;
    		$etab->cvi = $etablissement->cvi;
    		$etab->siege->adresse = $etablissement->siege->adresse;
    		$etab->siege->code_postal = $etablissement->siege->code_postal;
    		$etab->siege->commune = $etablissement->siege->commune;
    		$etab->siege->pays = $etablissement->siege->pays;
    		$etab->comptabilite->adresse = $etablissement->comptabilite->adresse;
    		$etab->comptabilite->code_postal = $etablissement->comptabilite->code_postal;
    		$etab->comptabilite->commune = $etablissement->comptabilite->commune;
    		$etab->comptabilite->pays = $etablissement->comptabilite->pays;
    		$etab->no_accises = $etablissement->no_accises;
    		$etab->no_tva_intracommunautaire = $etablissement->no_tva_intracommunautaire;
    		$etab->email = $etablissement->email;
    		$etab->telephone = $etablissement->telephone;
    		$etab->fax = $etablissement->fax;
    		$etab->famille = $etablissement->famille;
    		$etab->sous_famille = $etablissement->sous_famille;
    		$etab->service_douane = $etablissement->service_douane;
    		$interpro[$etablissement->interpro] = $etablissement->interpro; 
    	}
    	if (count($interpro) == 1) {
    		$convention->interpro = current($interpro);
    	}
    	if ($nbEtablissement == 1) {
    		$convention->raison_sociale = ($currentEtab->raison_sociale)? $currentEtab->raison_sociale : $currentEtab->nom ;
    		$convention->no_operateur = $currentEtab->siret;
    		$convention->no_siret_payeur = $currentEtab->siret;
    		$convention->adresse = $currentEtab->siege->adresse;
    		$convention->code_postal = $currentEtab->siege->code_postal;
    		$convention->commune = $currentEtab->siege->commune;
    		$convention->pays = $currentEtab->siege->pays;
    		$convention->email_beneficiaire = $currentEtab->email;
    		$convention->telephone_beneficiaire = $currentEtab->telephone;
    	}
    	return $convention;
    }
}
