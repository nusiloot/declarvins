<?php

class DrmLieuRoute extends DrmCertificationRoute {

    public function getConfigLieu() {
        
        return $this->getDrmLieu()->getConfig();
    }

    public function getDrmLieu() {

        return $this->getObject();
    }

    public function getConfigCertification() {

        return $this->getConfigLieu()->getAppellation()->getGenre()->getCertification();
    }

    protected function getObjectForParameters($parameters) {
        $drm_certification = parent::getObjectForParameters($parameters);

        if (!array_key_exists('appellation', $parameters)) {

        	return $drm_certification->genres->getFirst()->appellations->getFirst()->lieux->getFirst();
        }

        if (isset($this->options['add_noeud']) && $this->options['add_noeud'] === true) {
            return $drm_certification->genres->add($parameters['genre'])
                                 ->appellations->add($parameters['appellation'])
                                 ->lieux->add($parameters['lieu']);
        } else {
            return $drm_certification->genres->get($parameters['genre'])
                                 ->appellations->get($parameters['appellation'])
                                 ->lieux->get($parameters['lieu']);
        }

        
    }

    protected function doConvertObjectToArray($object) {
        if ($object->getDefinition()->getHash() == "/declaration/certifications/*/genres/*/appellations/*/lieux/*") {
            $parameters = parent::doConvertObjectToArray($object->getCertification());
            $parameters['genre'] = $object->getAppellation()->getGenre()->getKey();
            $parameters['appellation'] = $object->getAppellation()->getKey();
            $parameters['lieu'] = $object->getKey();
        } elseif($object->getDefinition()->getHash() == "/declaration/certifications/*") {
            $parameters = parent::doConvertObjectToArray($object);
        }

        return $parameters;
    }

}