<?php
namespace Admin\Forms;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class CreateNews extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('create-news');
        $this->setAttributes(array(
            "role" => "form",
//             "class"=>"form-horizontal",
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Tiêu đề'
            ),
            'attributes' => array(
                'id' => 'faq_txt_title',
                'class' => 'form-control',
                'placeholder' => 'Tiêu đề bài viết',
//                 'minlength' => '50',
//                 'data-validation-minlength-message' => 'Chiều dài tối thiểu 50 ký tự',
                'maxlength' => '120',
                'data-validation-maxlength-message' => 'Tiêu đề bài viết dài quá 120 ký tự',

            ),

        ));

        $this->add(array(
            'name' => 'content_news',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Nội dung chi tiết bài viết'
            ),
            'attributes' => array(
                'id' => 'faq_txt_content_news',
                'class' => 'form-control faq_txt_content',
//                 'cols'=>'',
                'rows'=>'100',
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'lưu',
                'class'=>'btn btn-primary',
                'id' => 'faq_btn_submit'
            )
        ));
    }
}