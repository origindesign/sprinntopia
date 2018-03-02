<?php

/**
 * @file
 * Contains \Drupal\inntopia\Twig\ImageFormat.
 */

namespace Drupal\inntopia\Twig;


/**
 * Provides the ImageFormat function within Twig templates.
 */
class ImageFormat extends \Twig_Extension {

    
    
    /**
     * {@inheritdoc}
     */
    public function getName() {      
        return 'image_format';
    }

    
    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction(
                    'drupalizeImage', array($this, 'drupalizeImage')
                )
        );
    }



	/**
	 * Get image from external server and store it in Drupal system using
	 * imagecache_external module
	 *
	 * @param $img_path
	 * @param $img_caption
	 * @param $style_name
	 *
	 * @return array
	 *
	 */
    public function drupalizeImage ( $img_path, $img_caption, $style_name ){

		$result = array(
			'#theme' => 'imagecache_external',
			'#uri' => $img_path,
			'#style_name' => $style_name,
			'#alt' => $img_caption,
		);


		return $result;
       
    }
    
    

}
