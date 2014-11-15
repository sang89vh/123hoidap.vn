<?php
namespace Web\Forms;

use Zend\Form\Form;
use Web\Forms\ContentQuestion;
use Zend\InputFilter\InputFilter;

class FinishQuestion extends ContentQuestion
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('finish-question');
        $this->setAttributes(array(
            "role" => "form",
            "class"=>"form-horizontal",
                ));

        $this->add(array(
            'name' => 'bonus_point',
            'type' => 'Text',
            'options' => array(
                'label' => 'Điểm thưởng cho câu trả lời hay nhất'
            ),
            'attributes' => array(
                'id' => 'faq_txt_bonus_point',
                'class' => 'form-control input-mini spinner-input',
            ),

        ));
    }
}