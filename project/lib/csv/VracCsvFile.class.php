<?php

class VracCsvFile extends CsvFile 
{
  const CSV_VRAC_DECLARANT_IDENTIFIANT = 0;
  const CSV_VRAC_DECLARANT_NOM = 1;
  const CSV_VRAC_CONTRAT_NUMERO = 2;
  const CSV_VRAC_CERTIFICATION = 3;
  const CSV_VRAC_CERTIFICATION_CODE = 4; 
  const CSV_VRAC_GENRE = 5;
  const CSV_VRAC_GENRE_CODE = 6;
  const CSV_VRAC_APPELLATION = 7;
  const CSV_VRAC_APPELLATION_CODE = 8;
  const CSV_VRAC_LIEU = 9;
  const CSV_VRAC_LIEU_CODE = 10;
  const CSV_VRAC_COULEUR = 11;
  const CSV_VRAC_COULEUR_CODE = 12;
  const CSV_VRAC_CEPAGE = 13;
  const CSV_VRAC_CEPAGE_CODE = 14;
  const CSV_VRAC_MILLESIME = 15;
  const CSV_VRAC_MILLESIME_CODE = 16;
  const CSV_VRAC_LABELS = 17;
  const CSV_VRAC_LABELS_CODE = 18;
  const CSV_VRAC_MENTION = 19;
  const CSV_VRAC_MENTION_CODE = 20;
  const CSV_VRAC_ACHETEUR_IDENTIFIANT = 21;
  const CSV_VRAC_ACHETEUR_NOM = 22;
  const CSV_VRAC_COURTIER_IDENTIFIANT = 23;
  const CSV_VRAC_COURTIER_NOM = 24;
  const CSV_VRAC_CONTRAT_DATE = 25;
  const CSV_VRAC_CONTRAT_VOLUME_PROMIS = 26;
  const CSV_VRAC_CONTRAT_VOLUME_REALISE = 27;

  private function verifyCsvLine($line) {
    if (!preg_match('/[^ ]+/', $line[self::CSV_VRAC_CONTRAT_NUMERO]))
      throw new Exception('Numero de contrat nécessaire : '.$line[self::CSV_VRAC_CONTRAT_NUMERO]);
    if (! $line[self::CSV_VRAC_CONTRAT_VOLUME_PROMIS]*1)
      throw new Exception('Volume promis nécessaire : '.$line[self::CSV_VRAC_CONTRAT_VOLUME_PROMIS]);
    $declarant = EtablissementClient::getInstance()->retrieveById($line[self::CSV_VRAC_DECLARANT_IDENTIFIANT]);
    if (!$declarant) {
      throw new Exception('Impossible de trouver un etablissement correspondant à l\'identifiant '.$line[self::CSV_VRAC_DECLARANT_IDENTIFIANT]);
    }
  }

  private function getProduit($line) {
    return $this->config->identifyNodeProduct($line[self::CSV_VRAC_CERTIFICATION], 
					  $line[self::CSV_VRAC_APPELLATION], 
					  $line[self::CSV_VRAC_LIEU], 
					  $line[self::CSV_VRAC_COULEUR], 
					  $line[self::CSV_VRAC_CEPAGE], 
					  $line[self::CSV_VRAC_MILLESIME]);
  }

  public function importContrats() {
    $this->config = ConfigurationClient::getCurrent();
    $this->errors = array();
    $this->numline = 0;
    $contrats = array();
    $csvs = $this->getCsv();
    try {
      foreach ($csvs as $line) {
	$this->verifyCsvLine($line);
	$hash = $this->getProduit($line);
	$c = VracClient::getInstance()->retrieveByNumeroAndEtablissementAndHashOrCreateIt($line[self::CSV_VRAC_CONTRAT_NUMERO], 
											     $line[self::CSV_VRAC_DECLARANT_IDENTIFIANT],
											     $hash);
	$c->add('acheteur')->add('nom', $line[self::CSV_VRAC_ACHETEUR_NOM]);
	$c->add('volume_promis', $line[self::CSV_VRAC_CONTRAT_VOLUME_PROMIS]*1);
	if (!$c->volume_realise)
	  $c->add('volume_realise', $line[self::CSV_VRAC_CONTRAT_VOLUME_REALISE]*1);
	$contrats[] = $c;
      }
    }catch(Execption $e) {
      $this->error[] = $e->getMessage();
    }
    return $contrats;
  }

  public function getErrors() {
    return $this->errors;
  }
}