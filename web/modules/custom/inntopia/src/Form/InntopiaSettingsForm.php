<?php

/**
 * @file
 * Contains \Drupal\inntopia\Form\InntopiaSettingsForm.
 */

namespace Drupal\inntopia\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Configure file system settings for this site.
 */
class InntopiaSettingsForm extends ConfigFormBase {


    /**
     * Class constructor.
     * @param ConfigFactoryInterface $config_factory
     */
    public function __construct(ConfigFactoryInterface $config_factory) {
        parent::__construct($config_factory);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('config.factory')
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'inntopia_settings_form';
    }


    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return ['inntopia.settings'];
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $config = $this->config('inntopia.settings');

        $form['inntopia_sales_id'] = array(
            '#type' => 'textfield',
            '#title' => t('Sales ID'),
            '#default_value' => $config->get('inntopia_sales_id')
        );

        $form['inntopia_server'] = array(
            '#type' => 'textfield',
            '#title' => t('Inntopia Server'),
            '#default_value' => $config->get('inntopia_server')
        );

        return parent::buildForm($form, $form_state);
    }


    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {

    }


    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

        $config = $this->config('inntopia.settings');

        foreach ($form_state->getValues() as $key => $value) {
            $config->set($key, $value);
        }

        $config->save();

        parent::submitForm($form, $form_state);

    }

}