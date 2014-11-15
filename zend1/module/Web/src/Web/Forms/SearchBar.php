<?php
namespace Web\Forms;

use Zend\Form\Form;

class SearchBar extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('search-bar');
        $this->setAttribute("method", "get");

        $this->add(array(
            'name' => 'q',
            'type' => 'Text',
            'options' => array(
                'label' => ''
            ),
            'attributes' => array(
                'id' => 'faq_txt_key_search',
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => 'Tìm kiếm',
                'id' => 'faq_search'
            )
        ));
    }
}