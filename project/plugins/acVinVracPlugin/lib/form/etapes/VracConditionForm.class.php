<?php
class VracConditionForm extends VracForm 
{
   	public function configure()
    {
  		$this->setWidgets(array(
        	'has_transaction' => new WidgetFormInputCheckbox(),
  		    'contrat_pluriannuel' => new sfWidgetFormChoice(array('choices' => array('0' => 'Ponctuel', '1' => 'Adossé à un contrat pluriannel'),'expanded' => true)),
        	'reference_contrat_pluriannuel' => new sfWidgetFormInputText(),
  		    'cas_particulier' => new sfWidgetFormChoice(array('expanded' => true, 'choices' => $this->getCasParticulier(), 'renderer_options' => array('formatter' => array('VracSoussigneForm', 'casParticulierFormatter')))),
        	'export' => new sfWidgetFormChoice(array('choices' => $this->getChoixOuiNon(),'expanded' => true)),
    		'premiere_mise_en_marche' => new sfWidgetFormChoice(array('choices' => $this->getChoixOuiNon(),'expanded' => true)),
        	'bailleur_metayer' => new sfWidgetFormChoice(array('choices' => $this->getChoixOuiNon(),'expanded' => true)),
        	'annexe' => new sfWidgetFormChoice(array('choices' => $this->getChoixOuiNon(),'expanded' => true)),
        	'type_transaction' => new sfWidgetFormChoice(array('expanded' => true, 'choices' => $this->getTypesTransaction())),
    	));
        $this->widgetSchema->setLabels(array(
        	'has_transaction' => 'je souhaite faire ma déclaration de transaction en même tant que mon contrat',
        	'contrat_pluriannuel' => 'Type de contrat:',
        	'reference_contrat_pluriannuel' => 'Référence du contrat pluriannuel adossé à ce contrat*:',
            'cas_particulier' => 'Condition particulière*:',
        	'export' => 'Expédition export*:',
        	'premiere_mise_en_marche' => 'Première mise en marché:',
            'bailleur_metayer' => 'Entre bailleur et métayer:',
        	'annexe' => 'Présence d\'une annexe (cahier des charges techniques):',
        	'type_transaction' => 'Type de produit:',
        ));
        $this->setValidators(array(
        	'has_transaction' => new ValidatorBoolean(),
            'contrat_pluriannuel' => new sfValidatorChoice(array('required' => true, 'choices' => array('0','1'))),
        	'reference_contrat_pluriannuel' => new sfValidatorString(array('required' => false)),
            'cas_particulier' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getCasParticulier()))),
        	'export' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getChoixOuiNon()))),
        	'premiere_mise_en_marche' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getChoixOuiNon()))),
            'bailleur_metayer' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getChoixOuiNon()))),
        	'annexe' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getChoixOuiNon()))),
        	'type_transaction' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getTypesTransaction()))),
        ));


        if (count($this->getTypesTransaction()) < 2) {
            unset($this['type_transaction']);
        }
  		
  		$this->validatorSchema->setPostValidator(new VracConditionValidator());
  		$this->widgetSchema->setNameFormat('vrac_condition[%s]');
    }

    protected function doUpdateObject($values) {
      $this->getObject()->cas_particulier_libelle = $this->getConfiguration()->formatCasParticulierLibelle(array($this->getObject()->cas_particulier));
      if (!$values['contrat_pluriannuel']) {
          $values['reference_contrat_pluriannuel'] = null;
      }
      parent::doUpdateObject($values); 
      if (!$this->getObject()->annexe) {
          $this->getObject()->annexe = 0;
      }
        $types_transaction = $this->getTypesTransaction();
        if (count($types_transaction) == 1) {
            foreach($types_transaction as $key => $value) {
                $this->getObject()->type_transaction = $key;
            }
        }
        $this->getObject()->type_transaction_libelle = $this->getConfiguration()->formatTypesTransactionLibelle(array($this->getObject()->type_transaction));
    }

    protected function updateDefaultsFromObject() {
      parent::updateDefaultsFromObject();    
      $this->setDefault('cas_particulier', (($this->getObject()->cas_particulier) ? $this->getObject()->cas_particulier : null));
      if (is_null($this->getObject()->export)) {
          $this->setDefault('export', 0);
      }
      if (is_null($this->getObject()->annexe)) {
        $this->setDefault('annexe', 0);
      }   
      if (is_null($this->getObject()->bailleur_metayer)) {
        $this->setDefault('bailleur_metayer', 0);
      }    
      if (!$this->getObject()->contrat_pluriannuel) {
        $this->setDefault('contrat_pluriannuel', '0');
      } else {
          $this->setDefault('contrat_pluriannuel', '1');
      }
      
      if (is_null($this->getObject()->type_transaction)) {
        $this->setDefault('type_transaction', VracClient::TRANSACTION_DEFAUT);
      }      
    }

    public function conditionneIVSE() {
      return false;
    }
}