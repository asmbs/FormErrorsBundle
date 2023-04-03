<?php

/**
 * FormErrorsExtension - extension to list all errors from form.
 *
 * @author Maciej Szkamruk <ex3v@ex3v.com>
 */

namespace Ex3v\FormErrorsBundle\Twig;

use Symfony\Component\Form\Form;
use Ex3v\FormErrorsBundle\Services\FormErrorsParser;
use Twig\Extension\AbstractExtension as TwigAbstractExtension;
use Twig\TwigFunction;

class FormErrorsExtension extends TwigAbstractExtension
{

    /**
     *
     * @var FormErrorsParser
     */
    private $parser;

    public function __construct(FormErrorsParser $parser)
    {
        $this->parser = $parser;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('all_form_errors', array($this, 'allFormErrors'), array("is_safe" => array("html")))
        );
    }

    /**
     * Main Twig extension. Call this in Twig to get formatted output of your form errors.
     * Note that you have to provide form as Form object, not FormView.
     * 
     * @param Form $form
     * @param string $tag html tag, in which all errors will be packed. If you will provide 'li', 'ul' wrapper will be added
     * @param class-string $class class of each error. Default is none
     * @return string
     */
    public function allFormErrors(Form $form, $tag = 'li', $class = '')
    {
        $errorsList = $this->parser->parseErrors($form);

        $return = '';
        if (count($errorsList)) {
            if ($tag == 'li') {
                $return.='<ul>';
            }
            /** @var array $item */
	        foreach ($errorsList as $item) {
                $return.=$this->handleErrors($item, $tag, $class);
            }

            if ($tag == 'li') {
                $return.='</ul>';
            }
        }

        return $return;
    }

    /**
     * Handle single error creation
     * @param array $item
     * @param string $tag
     * @param class-string $class
     * @return string
     */
    private function handleErrors($item, $tag, $class)
    {

        $return = '';

        $errors = $item['errors'];

        if (count($errors)) {
            /* @var $error \Symfony\Component\Form\FormError */
            foreach ($errors as $error) {
                $return.='<' . $tag . ' class="'.$class.'">';
                $return .= $item['label'];
                $return.=': ';
                $return .= $error->getMessage();  // The translator has already translated any validation error.
                $return.="</" . $tag . '>';
            }
        }

        return $return;
    }

    public function getName()
    {
        return 'all_form_errors_extension';
    }

}
