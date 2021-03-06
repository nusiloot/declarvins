<?php
class DRMCampagneForm extends sfForm
{
    protected $etablissement = null;

    public function __construct($etablissement, $defaults = array(), $options = array(), $CSRFSecret = null)
    {
        $this->etablissement = $etablissement;
        parent::__construct($defaults, $options, $CSRFSecret);
    }

	public function configure() {    
        $this->setWidgets(array(
            'months'   => new sfWidgetFormChoice(array('choices' => $this->getMonths())),
        	'years'   => new sfWidgetFormChoice(array('choices' => $this->getYears()))
        ));

        $this->setValidators(array(
            'months'   => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getMonths()))),
        	'years'   => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getYears())))
        ));
        
        $this->widgetSchema->setNameFormat('campagne[%s]');
        $this->validatorSchema->setPostValidator(new DRMCampagneValidator(array('etablissement' => $this->etablissement)));
    }
    
    public function getMonths() {
        
            $dateFormat = new sfDateFormat('fr_FR');
	    $results = array('' => '');
	    for ($i = 1; $i <= 12; $i++)
	    { 
              
              $month = $dateFormat->format(date('Y').'-'.$i.'-01', 'MMMM');
	      $results[$i] = $month;//strftime("%A %d %B",time()); //sprintf('%02d', $i);
	    }
	    return $results;
    }
    public function getYears() {
    	$years = range(date('Y') + 1, date('Y') - 10);
    	array_unshift($years, '');
    	$years = array_combine($years, $years);
    	return $years;
    }
}