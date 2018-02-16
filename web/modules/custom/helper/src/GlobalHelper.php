<?php
/**
 * @file Contains \Drupal\helper\GlobalHelper
 */
 
namespace Drupal\helper;

 
/**
 * A Class for storing global variables
 *
 */
class GlobalHelper {


    protected $globalSettings;


    /**
     * @return mixed
     */
    public function getGlobalSettings()
    {
        return $this->globalSettings;
    }


    /**
     * @param mixed $globalSettings
     */
    public function setGlobalSettings($globalSettings)
    {
        $this->globalSettings = $globalSettings;
    }


}
