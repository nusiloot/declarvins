<?php

/**
 * daids actions.
 *
 * @package    declarvin
 * @subpackage daids
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class daidsActions extends sfActions
{

  /**
   *
   * @param sfWebRequest $request 
   */
  public function executeNouvelle(sfWebRequest $request) 
  {
      $daids = $this->getRoute()->getDAIDS();
      if ($daids->getHistorique()->hasDAIDSInProcess()) {
        throw new sfException('Une DAI/DS est déjà en cours de saisie.');
      }
      if(DAIDSClient::getInstance()->formatToCompare($daids->periode) > DAIDSClient::getInstance()->formatToCompare(DAIDSClient::getInstance()->getCurrentPeriode())) {
        throw new sfException('Impossible de faire une DAI/DS future');
      }
      $daids->save();
      $this->redirect('daids_informations', $daids);
  }
  
  /**
   *
   * @param sfWebRequest $request 
   */
  public function executeInit(sfWebRequest $request) 
  {
      $daids = $this->getRoute()->getDAIDS();
      $reinit_etape = $request->getParameter('reinit_etape', 0);
      if ($reinit_etape) {
		$daids->setCurrentEtapeRouting('recapitulatif');
      	$this->redirect($daids->getCurrentEtapeRouting(), $daids);
      } elseif ($etape = $daids->etape) {
      	$this->redirect($daids->getCurrentEtapeRouting(), $daids);
      } else {
		$daids->setCurrentEtapeRouting('ajouts_liquidations');
      	$this->redirect('daids_informations', $daids);
      }
  }
  
 /**
  * Executes mon espace action
  *
  * @param sfRequest $request A request object
  */
  public function executeMonEspace(sfWebRequest $request)
  {
      $this->etablissement = $this->getRoute()->getEtablissement();
      $this->historique = DAIDSClient::getInstance()->getDAIDSHistorique($this->etablissement->identifiant);
      $this->formCampagne = new DAIDSCampagneForm($this->etablissement->identifiant);
      $this->hasDaidsEnCours = $this->historique->hasDAIDSInProcess();
      if ($request->isMethod(sfWebRequest::POST)) {
	  	if ($this->hasDaidsEnCours) {
	  		throw new sfException('Une DAIDS est déjà en cours de saisie.');
	  	}
    	$this->formCampagne->bind($request->getParameter($this->formCampagne->getName()));
  	  	if ($this->formCampagne->isValid()) {
  	  		$values = $this->formCampagne->getValues();
  	  		$daids = DAIDSClient::getInstance()->createDoc($this->etablissement->identifiant, $values['campagne']);
  	  		$daids->mode_de_saisie = DAIDSClient::MODE_DE_SAISIE_PAPIER;
      		$daids->save();
      		$this->redirect('daids_informations', $daids);
  	  	}
      }
  }
  
  public function executeDelete(sfWebRequest $request) {
      $etablissement = $this->getRoute()->getEtablissement();
      $daids = $this->getRoute()->getDAIDS();
      if (!$daids->isNew() && ($daids->isSupprimable() || ($this->getUser()->hasCredential(myUser::CREDENTIAL_OPERATEUR) && $daids->isSupprimableOperateur()))) {
        $daidsList = DAIDSClient::getInstance()->findByIdentifiantAndPeriodeAndRectificative($daids->identifiant, $daids->periode, $daids->getRectificative());
        foreach($daidsList as $d) {
          $d->delete();
        }
      	$this->redirect('daids_mon_espace', $etablissement);
      }
      throw new sfException('Vous ne pouvez pas supprimer cette DAIDS');
  }
  
  public function executeDeleteOne(sfWebRequest $request) {
      $etablissement = $this->getRoute()->getEtablissement();
      $daids = $this->getRoute()->getDAIDS();
      if (!$daids->isNew() && ($daids->isSupprimable() || ($this->getUser()->hasCredential(myUser::CREDENTIAL_OPERATEUR) && $daids->isSupprimableOperateur()))) {
        $daids->delete();
      	$this->redirect('daids_mon_espace', $etablissement);
      }
      throw new sfException('Vous ne pouvez pas supprimer cette DAIDS');
  }
  
  public function executeInformations(sfWebRequest $request)
  {
    $this->daids = $this->getRoute()->getDAIDS();
    $this->etablissement = $this->getRoute()->getEtablissement();
    $isAdmin = $this->getUser()->hasCredential(myUser::CREDENTIAL_OPERATEUR);
    $this->form = new DAIDSInformationsForm(array(), array('is_admin' => $isAdmin));
    if ($request->isMethod(sfWebRequest::POST)) {
    	$this->form->bind($request->getParameter($this->form->getName()));
  	  if ($this->form->isValid()) {
	  		$values = $this->form->getValues();
            if ($values['confirmation'] == "modification") {
            	$this->redirect('daids_modif_infos', $this->daids);
            } elseif ($values['confirmation']) {
            	$this->daids->setDeclarantInformations($this->etablissement);		
  				$this->daids->save();
	  		}
	        $this->daids->setCurrentEtapeRouting('recapitulatif');
	        return $this->redirect('daids_recap', $this->daids->declaration->certifications->getFirst());
    	}
    }
  }
  
  public function executeModificationInfos(sfWebRequest $request)
  {
      $this->daids = $this->getRoute()->getDAIDS();
      $this->etablissement = $this->getRoute()->getEtablissement();
  }
    
	public function executeValidee() 
	{
        $this->etablissement = $this->getRoute()->getEtablissement();
    }
    
	public function executeNonValidee() 
	{
		$this->etablissement = $this->getRoute()->getEtablissement();
	}
    
	public function executeHamza() 
	{
		
	}
    
    public function executeDetail(sfWebRequest $request) 
    {
        $this->init();
        $this->detail = $this->getRoute()->getDAIDSDetail();
        $this->setTemplate('index');
    }

    public function executeValidation(sfWebRequest $request)
    {
	    $this->etablissement = $this->getRoute()->getEtablissement();
	    $this->daids = $this->getRoute()->getDAIDS();
	    $this->daidsValidation = $this->daids->validation();
	    $this->form = new DAIDSValidationForm(array(), array('engagements' => $this->daidsValidation->getEngagements()));
	    if (!$request->isMethod(sfWebRequest::POST)) {
	      return sfView::SUCCESS;
	    }
	    $this->form->bind($request->getParameter($this->form->getName()));
		if (!$this->form->isValid() || !$this->daidsValidation->isValide()) {
	      return sfView::SUCCESS;
	    }
		$this->daids->validate();
		$this->daids->save();
	    if ($this->daids->needNextVersion()) {
	      $daids_version_suivante = $this->daids->generateNextVersion();
	      $daids_version_suivante->save();
	    }
	    $this->redirect('daids_visualisation', array('sf_subject' => $this->daids, 'hide_rectificative' => 1));
    }
    
  public function executeVisualisation(sfWebRequest $request)
  {
    $this->daids = $this->getRoute()->getDAIDS();
    $this->etablissement = $this->getRoute()->getEtablissement();
    $this->hide_rectificative = $request->getParameter('hide_rectificative');
    $this->daids_suivante = $this->daids->getSuivante();
  }

  public function executeShowError(sfWebRequest $request) 
  {
    $this->daids = $this->getRoute()->getDAIDS();
    $daidsValidation = $this->daids->validation();
    $controle = $daidsValidation->find($request->getParameter('type'), $request->getParameter('identifiant_controle'));
    $this->forward404Unless($controle);
    $this->getUser()->setFlash('control_message', $controle->getMessage());
    $this->getUser()->setFlash('control_css', "flash_".$controle->getType());
    $this->redirect($controle->getLien());
  }

  public function executeRectificative(sfWebRequest $request)
  {
    $this->etablissement = $this->getRoute()->getEtablissement();
    $daids = $this->getRoute()->getDAIDS();
    if ($daids->getHistorique()->hasDAIDSInProcess()) {
      throw new sfException('Une DAI/DS est déjà en cours de saisie.');
    }
    $daids_rectificative = $daids->generateRectificative();
    $daids_rectificative->save();
    return $this->redirect('daids_init', $daids_rectificative);
  }

  public function executeModificative(sfWebRequest $request)
  {
    $this->etablissement = $this->getRoute()->getEtablissement();
    $daids = $this->getRoute()->getDAIDS();
    if ($daids->getHistorique()->hasDAIDSInProcess()) {
      throw new sfException('Une DAI/DS est déjà en cours de saisie.');
    }
    $daids_modificative = $daids->generateModificative();
    $daids_modificative->save();
    return $this->redirect('daids_init', $daids_modificative);
  }
  
  public function executePdf(sfWebRequest $request)
  {
    ini_set('memory_limit', '512M');
    $this->daids = $this->getRoute()->getDAIDS();
  	$pdf = new ExportDAIDSPdf($this->daids);

    return $this->renderText($pdf->render($this->getResponse(), false, $request->getParameter('format')));
  }
  
}