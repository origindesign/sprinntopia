<?php

/**
 * @file 
 * Contains \Drupal\includer\Controller\LiftTrailNodeController
 */
 
namespace Drupal\includer\Controller;



/**
 * The specific class to gather includes and pass to template
 *
 */
class LiftTrailNodeController extends ContentController {



    public function getIncludes () {

        // Get global settings
        $globalSettings = $this->globalHelper->getGlobalSettings();
        $this->result["settings"] = $globalSettings;

        if($this->result["settings"]['field_override_lifts_trails'] == "0"){

            // Define Helper (not using dependency so Includer doesn't necessarily require Lifts/Trails module)
            $lift_trails = \Drupal::service('lifts_trails.get_lifts_trails');

            // Setup machine name
            if($this->arguments["type"] == 'lift'){
                $machine_name = 'lift_';
            }else{
                $machine_name = 'trail_';
            }
            $machine_name .= $lift_trails->createMachineName($this->arguments["label"]);

            // Get status from feed
            $status = $lift_trails->getByName($machine_name);

            if($status != 'N/A'){
                $this->result['feed_status'] = 'status_'.$status;
            }

        }

        return $this->result;

    }


}
