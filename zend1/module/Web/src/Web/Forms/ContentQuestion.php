<?php
namespace Web\Forms;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ContentQuestion extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('content-question');
        $this->setAttributes(array(
            "role" => "form",
//             "class"=>"form-horizontal",
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Tiêu đề câu hỏi'
            ),
            'attributes' => array(
                'id' => 'faq_txt_title',
                'class' => 'form-control',
                'placeholder' => 'Tiêu đề câu hỏi',
//                 'minlength' => '50',
//                 'data-validation-minlength-message' => 'Chiều dài tối thiểu 50 ký tự',
                'maxlength' => '120',
                'data-validation-maxlength-message' => 'Tiêu đề câu hỏi dài quá 120 ký tự',

            ),

        ));
        $this->add(array(
            'name' => 'note_edit',
            'type' => 'Text',
            'options' => array(
                'label' => 'Miêu tả thay đổi'
            ),
            'attributes' => array(
                'id' => 'faq_txt_note_edit',
                'class' => 'form-control',
                'placeholder' => 'Tiêu đề câu hỏi',
//                 'minlength' => '50',
//                 'data-validation-minlength-message' => 'Chiều dài tối thiểu 50 ký tự',
                'maxlength' => '500',
                'data-validation-maxlength-message' => 'Miêu tả những thay đổi',

            ),

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
        $this->add(array(
            'name' => 'content_question',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Nội dung chi tiết câu hỏi'
            ),
            'attributes' => array(
                'id' => 'faq_txt_content_question',
                'class' => 'form-control faq_txt_content',
//                 'cols'=>'',
                'rows'=>'100',
            )
        ));
        $this->add(array(
            'name' => 'key_word',
            'type' => 'Text',
            'options' => array(
                'label' => 'Từ khoá'
            ),
            'attributes' => array(
                'id' => 'faq_txt_key_word',
                'class' => 'form-control',
                'placeholder' => 'Từ khoá',
                'data-role'=>'tagsinput',

            )
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Tiếp',
                'id' => 'faq_btn_submit_next'
            )
        ));
    }
}