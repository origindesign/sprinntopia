<?php

/**
 * @file
 * Contains \Drupal\helper\Twig\BackgroundHelper.
 */

namespace Drupal\helper\Twig;


use Drupal\Core\Datetime\DrupalDateTime;


/**
 * Provides the BackgroundHelper function within Twig templates.
 */
class StringHelper extends \Twig_Extension {


    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'string_helper';
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction(
                'displayFromToDates',
                array($this, 'displayFromToDates'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'displayFromToTime',
                array($this, 'displayFromToTime'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'displayMonthDate',
                array($this, 'displayMonthDate'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'uniqueString',
                array($this, 'uniqueString'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
              'windDirection',
              array($this, 'windDirection'),
              array('is_safe' => array('html') )
            )
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('machine', array($this, 'machineFilter')),
        );
    }



    public function machineFilter($string)
    {
        $value = str_replace('&amp; ','',$string);
        $value = str_replace(' ','-', strtolower($value));
		$value = preg_replace('@[^a-z0-9-]+@','', $value);
        return $value;
    }


    /**
     * @param $from
     * @param $to
     * @param bool $year
     * @return string
     */
    public function displayFromToDates ($from, $to, $year = false ){

        $fromDate = DrupalDateTime::createFromTimestamp( (int)$from );

        // If there is a to date
        if ($to){

            $toDate = DrupalDateTime::createFromTimestamp( (int)$to );

            // Return from date only if days are the same
            if ( $fromDate->format("Y-m-d") == $toDate->format("Y-m-d") ){
                return '<span class="date">
                            <span class="month">'.$fromDate->format('M').'</span>
                            <span class="day">'.$fromDate->format('j').'</span>
                            '.($year ? '<span class="year">'. $fromDate->format('Y').'</span>' : '' ).'
                        </span>';
            }else{

                // If days are different
                return '<span class="date">
                            <span class="month">'.$fromDate->format('M').'</span>
                            <span class="day">'.$fromDate->format('j').'</span>
                            '.($year ? '<span class="year">'. $fromDate->format('Y').'</span>' : '' ).'
                        </span>
                        <span class="to">to</span>
                        <span class="date">
                            <span class="month">'.$toDate->format('M').'</span>
                            <span class="day">'.$toDate->format('j').'</span>
                            '.($year ? '<span class="year">'. $toDate->format('Y').'</span>' : '' ).'
                        </span>';
            }

        }

        return '<span class="date">
                    <span class="month">'.$fromDate->format('M').'</span>
                    <span class="day">'.$fromDate->format('j').'</span>
                    '.($year ? '<span class="year">'. $fromDate->format('Y').'</span>' : '' ).'
                </span>';

    }



    /**
     * @param $from int datetime
     * @param $to int datetime
     * @return string return time in a from to format
     */
    public function displayFromToTime ($from, $to ){

        $fromDate = DrupalDateTime::createFromTimestamp( (int)$from );

        // If there is a to date
        if ($to){

            $toDate = DrupalDateTime::createFromTimestamp( (int)$to );

            return $fromDate->format("g:ia")." to ".$toDate->format("g:ia");

        }

        return $fromDate->format("g:ia");

    }



    /**
     * @param $date int datetime
     * @return string return time in a from to format
     */
    public function displayMonthDate ($date){

        $date = DrupalDateTime::createFromTimestamp( (int)$date );

        return '<span class="date">
                  <span class="month">'.$date->format('F').'</span>
                  <span class="day">'.$date->format('d').'</span>
                </span>';

    }




    /**
     * @param string $string
     * @return string return a unique ID based on string
     */
    public function uniqueString ($string ){

        $cleanStr = str_replace(" ", "", $string);
        //$arrStr = str_split($cleanStr, );

        return $cleanStr;

    }




    /**
     * @param string $string
     * @return string return a unique ID based on string
     */
    public function windDirection ( $angle ){

      $val = ($angle / 22.5) + 0.5;
      $directions = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
      return $directions[($val % 16)];

    }






}
