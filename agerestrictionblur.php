<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

use PrestaShop\PrestaShop\Adapter\Entity\Tools;

if(!defined('_PS_VERSION_')) {
    exit;
}

class AgeRestrictionBlur extends Module
{
    private $errors = [];
    public function __construct()
    {
        $this->name ='agerestrictionblur';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'samar Al khalil';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];


        parent::__construct();

        $this->bootstrap = true;
        $this->displayName = $this->l("Age Restriction Blur");
        $this->description = $this->l('Module de vérification d\'âge pour restreindre l\'accès au site.');
        $this->confirmUninstall = $this->l('Êtes-vous sur de vouloir supprimer ce module');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('displayWrapperTop') ||
            !$this->registerHook('Header') ||
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_TITLE', 'Avez-vous 18 ans ou plus ?') ||
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_DESCRIPTION', 'This website contains products intended for adults only. Please confirm that you are 18 years or older.') ||
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_COOKIE_LIFETIME', 1) || // Durée de vie du cookie en jours
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_LOGO', Tools::getValue('AGE_RESTRICTION_BLUR_LOGO')) ||
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_PAGE_COLOR', '#f0f0f0')  ||// Couleur de la page
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_TITLE_COLOR', '#333333') || // Couleur du titre
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR', '#666666') ||// Couleur de la description
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_GOOGLE_FONT', '') || // Police GoogleFonts
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_BUTTON_COLOR', Configuration::get('AGE_RESTRICTION_BLUR_BUTTON_COLOR')) ||
            !Configuration::updateValue('AGE_RESTRICTION_BLUR_REDIRECTION', 'https://www.google.com/')


        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->unregisterHook('displayWrapperTop') ||
            !$this->unregisterHook('Header') ||
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_TITLE') ||
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_DESCRIPTION') ||
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_COOKIE_LIFETIME') || // Durée de vie du cookie en jours
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_LOGO') ||
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_PAGE_COLOR')  ||// Couleur de la page
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_TITLE_COLOR') || // Couleur du titre
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR') ||// Couleur de la description
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_GOOGLE_FONT') ||// Police GoogleFonts
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_BUTTON_COLOR') ||
            !Configuration::deleteByName('AGE_RESTRICTION_BLUR_REDIRECTION')
        ) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit_' . $this->name)) {
            // Form submitted, process the data
            $output .= $this->postProcess();

        }
        // Load the necessary JavaScript file for the configuration page
        //$this->context->controller->addJS($this->_path . 'views/js/admin.js');

        // Render the form

        $output .= $this->renderForm();
        return $output;
    }

    public function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $googleFonts = $this->getGoogleFonts();

        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Age Restriction Blur Configuration'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Titre'),
                    'name' => 'AGE_RESTRICTION_BLUR_TITLE',
                    'size' => 200,
                    'validate' => 'isGeneric',
                    'required' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'AGE_RESTRICTION_BLUR_DESCRIPTION',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Durée de vie du cookie (jours)'),
                    'name' => 'AGE_RESTRICTION_BLUR_COOKIE_LIFETIME',
                    'suffix' => $this->l('jours'),
                    'class' => 'fixed-width-xs',
                    'validate' => 'isInt',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Logo'),
                    'name' => 'AGE_RESTRICTION_BLUR_LOGO',
                    'display_image' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Couleur de la page'),
                    'name' => 'AGE_RESTRICTION_BLUR_PAGE_COLOR',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Couleur du titre'),
                    'name' => 'AGE_RESTRICTION_BLUR_TITLE_COLOR',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Couleur de la description'),
                    'name' => 'AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Google Font'),
                    'name' => 'AGE_RESTRICTION_BLUR_GOOGLE_FONT',
                    'options' => [
                        'query' => array_map(function ($font, $link) {
                            return [
                                'id' => $font,
                                'name' => $font,
                                'font_link' => $link, // Store the font link in the 'font_link' field
                            ];
                        }, array_keys($googleFonts), $googleFonts),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('Choisi un font google pour la page de verification de l\'âge.'),
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Couleur du bouton'),
                    'name' => 'AGE_RESTRICTION_BLUR_BUTTON_COLOR',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('URL de redirection'),
                    'name' => 'AGE_RESTRICTION_BLUR_REDIRECTION',
                    'size' => 200,
                    'validate' => 'isURL',

                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-primary',
                'name' => 'saving'
            ],
            'enctype' => 'multipart/form-data',
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit_' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
            ],
        ];

        // Chargez les valeurs actuelles de la configuration
        $helper->fields_value['AGE_RESTRICTION_BLUR_TITLE'] = Configuration::get('AGE_RESTRICTION_BLUR_TITLE', $default_lang);
        $helper->fields_value['AGE_RESTRICTION_BLUR_DESCRIPTION'] = Configuration::get('AGE_RESTRICTION_BLUR_DESCRIPTION', $default_lang);
        $helper->fields_value['AGE_RESTRICTION_BLUR_COOKIE_LIFETIME'] = Configuration::get('AGE_RESTRICTION_BLUR_COOKIE_LIFETIME', $default_lang);
        $helper->fields_value['AGE_RESTRICTION_BLUR_LOGO'] = Configuration::get('AGE_RESTRICTION_BLUR_LOGO');
        $helper->fields_value['AGE_RESTRICTION_BLUR_PAGE_COLOR'] = Configuration::get('AGE_RESTRICTION_BLUR_PAGE_COLOR', $default_lang);
        $helper->fields_value['AGE_RESTRICTION_BLUR_TITLE_COLOR'] = Configuration::get('AGE_RESTRICTION_BLUR_TITLE_COLOR', $default_lang);
        $helper->fields_value['AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR'] = Configuration::get('AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR', $default_lang);
        $helper->fields_value['AGE_RESTRICTION_BLUR_GOOGLE_FONT'] = Configuration::get('AGE_RESTRICTION_BLUR_GOOGLE_FONT', $default_lang);
        $helper->fields_value['AGE_RESTRICTION_BLUR_BUTTON_COLOR'] = Configuration::get('AGE_VERIFICATION_BUTTON_COLOR', $default_lang);
        $helper->fields_value['AGE_RESTRICTION_BLUR_REDIRECTION'] = Configuration::get('AGE_RESTRICTION_BLUR_REDIRECTION', $default_lang);
        return $helper->generateForm($fields_form);
    }


    public function postProcess()
    {
        if(Tools::isSubmit('saving')) {
            if(empty(Tools::getValue('AGE_RESTRICTION_BLUR_TITLE')) ||
               empty(Tools::getValue('AGE_RESTRICTION_BLUR_DESCRIPTION')) ||
               empty(Tools::getValue('AGE_RESTRICTION_BLUR_COOKIE_LIFETIME'))

            ) {
                return $this->displayError('Une valeur est vide');
            } else {
                $this->processImageUpload();
                /*  if ($this->processImageUpload()) {
                     $logoName = Tools::getValue('AGE_RESTRICTION_BLUR_LOGO');

                 }

                 if(empty($logoName)) {
                     $logoName = configuration::get('AGE_RESTRICTION_BLUR_LOGO');

                 } */

                Configuration::updateValue('AGE_RESTRICTION_BLUR_TITLE', Tools::getValue('AGE_RESTRICTION_BLUR_TITLE'));
                Configuration::updateValue('AGE_RESTRICTION_BLUR_DESCRIPTION', Tools::getValue('AGE_RESTRICTION_BLUR_DESCRIPTION'));
                Configuration::updateValue('AGE_RESTRICTION_BLUR_COOKIE_LIFETIME', Tools::getValue('AGE_RESTRICTION_BLUR_COOKIE_LIFETIME'));
                // Configuration::updateValue('AGE_RESTRICTION_BLUR_LOGO', $logoName);
                Configuration::updateValue('AGE_RESTRICTION_BLUR_PAGE_COLOR', Tools::getValue('AGE_RESTRICTION_BLUR_PAGE_COLOR'));
                Configuration::updateValue('AGE_RESTRICTION_BLUR_TITLE_COLOR', Tools::getValue('AGE_RESTRICTION_BLUR_TITLE_COLOR'));
                Configuration::updateValue('AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR', Tools::getValue('AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR'));
                Configuration::updateValue('AGE_RESTRICTION_BLUR_GOOGLE_FONT', Tools::getValue('AGE_RESTRICTION_BLUR_GOOGLE_FONT'));
                Configuration::updateValue('AGE_RESTRICTION_BLUR_BUTTON_COLOR', Tools::getValue('AGE_RESTRICTION_BLUR_BUTTON_COLOR'));
                Configuration::updateValue('AGE_RESTRICTION_BLUR_REDIRECTION', Tools::getValue('AGE_RESTRICTION_BLUR_REDIRECTION'));

                return $this->displayConfirmation('Sauvegarde réussie');
            }
        }
    }

    private function getGoogleFonts()
    {
        $url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyD7gXABk_mJq18GpNoMyhXXmRAgRZU2WJk&sort=popularity';

        try {
            $response = Tools::file_get_contents($url);
            $fonts = json_decode($response, true);


            if (isset($fonts['items'])) {
                $fontList = [];
                foreach ($fonts['items'] as $font) {
                    // Store the font name as the key and the font menu name as the value
                    $fontLink = 'https://fonts.googleapis.com/css2?family=' . urlencode($font['family']) . '&display=swap';
                    // Store the font name as the key and the formatted link as the value
                    $fontList[$font['family']] = $fontLink;
                }
                return $fontList;
            }
        } catch (Exception $e) {
            // Log the error for debugging purposes
            PrestaShopLogger::addLog($e->getMessage(), 3, null, 'AgeRestrictionBlur', 1, true);

            // Return an empty array to avoid errors in the configuration form
            return [];
        }

        return [];
    }

    protected function processImageUpload()
    {

        $uploadDir = _PS_MODULE_DIR_ . 'agerestrictionblur/views/img/images/';
        $fileName = 'ageLogo' . md5(uniqid()) . '.' . pathinfo(Tools::getValue('AGE_RESTRICTION_BLUR_LOGO'), PATHINFO_EXTENSION);

        $targetFile = $uploadDir . $fileName;

        $allowedExtensions = array('jpg', 'jpeg', 'png');
        if (!in_array(pathinfo(Tools::getValue('AGE_RESTRICTION_BLUR_LOGO'), PATHINFO_EXTENSION), $allowedExtensions)) {
            $this->errors[] = $this->l('Invalid file format. Allowed formats are jpg, jpeg, and png.');
            return false;
        }

        if (!move_uploaded_file($_FILES['AGE_RESTRICTION_BLUR_LOGO']['tmp_name'], $targetFile)) {
            return false;
        }
        if(empty($fileName)) {
            $fileName = configuration::get('AGE_RESTRICTION_BLUR_LOGO');

        }
        Configuration::updateValue('AGE_RESTRICTION_BLUR_LOGO', $fileName);
        return true;


    }

    public function hookDisplayWrapperTop($params)
    {

        $googleFonts = $this->getGoogleFonts();
        $selectedGoogleFont = Configuration::get('AGE_RESTRICTION_BLUR_GOOGLE_FONT');
        $selectedGoogleFontLink = isset($googleFonts[$selectedGoogleFont]) ? $googleFonts[$selectedGoogleFont] : null;
        $this->context->smarty->assign(array(
            'AGE_RESTRICTION_BLUR_TITLE' => Configuration::get('AGE_RESTRICTION_BLUR_TITLE', (int) $this->context->language->id),
            'AGE_RESTRICTION_BLUR_DESCRIPTION' => Configuration::get('AGE_RESTRICTION_BLUR_DESCRIPTION', (int) $this->context->language->id),
            'AGE_RESTRICTION_BLUR_LOGO' => Configuration::get('AGE_RESTRICTION_BLUR_LOGO'),
            'AGE_RESTRICTION_BLUR_PAGE_COLOR' => Configuration::get('AGE_RESTRICTION_BLUR_PAGE_COLOR'),
            'AGE_RESTRICTION_BLUR_TITLE_COLOR' => Configuration::get('AGE_RESTRICTION_BLUR_TITLE_COLOR'),
            'AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR' => Configuration::get('AGE_RESTRICTION_BLUR_DESCRIPTION_COLOR'),
            'AGE_RESTRICTION_BLUR_GOOGLE_FONT' => Configuration::get('AGE_RESTRICTION_BLUR_GOOGLE_FONT'),
            'AGE_RESTRICTION_BLUR_COOKIE_LIFETIME' => Configuration::get('AGE_RESTRICTION_BLUR_COOKIE_LIFETIME'),
            'AGE_RESTRICTION_BLUR_BUTTON_COLOR' => Configuration::get('AGE_RESTRICTION_BLUR_BUTTON_COLOR'),
            'AGE_RESTRICTION_BLUR_REDIRECTION' => Configuration::get('AGE_RESTRICTION_BLUR_REDIRECTION'),
            'selectedGoogleFontLink' => $selectedGoogleFontLink,
            'fontList' => $googleFonts,
            'confirm_button_text_18' => $this->l("OK"),
            'confirm_button_text_under_18' => $this->l("J'ai moins de 18 ans"),
            'module_dir' => $this->_path,
        ));

        return $this->display(__FILE__, 'views/templates/front/age_restriction_blur.tpl');

    }

    public function hookHeader()
    {

        $this->context->controller->addCSS($this->_path . 'views/css/age_restriction_blur.css');
        $this->context->controller->addJS($this->_path . 'views/js/age_restriction_blur.js');

    }

}
