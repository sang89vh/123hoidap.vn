<?php

namespace Web\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class TagForm extends Form {
	public function __construct($name = null, $options = array()) {
		parent::__construct ( $name, $options );
		$this->addElements ();
		$this->setAttribute ( "encrypt", "multipart/form-data" );
	}
	public function addElements() {
		$this->add ( array (
				'name' => 'tag_name',
				'type' => 'Text',
				'options' => array (
						'label' => 'Tiêu đề bài viết'
				),
				'attributes' => array (
						'id' => 'faq_tag_name',
						'class' => 'form-control',
						'placeholder' => 'Tên của tag nhỏ hơn 25 ký tự, nếu có 2 từ nên sử dụng dấu gạch ngang '-' nối lại. VD: may-tinh',
						// 'minlength' => '50',
						// 'data-validation-minlength-message' => 'Chiều dài tối thiểu 50 ký tự',
						'maxlength' => '120',
						'data-validation-maxlength-message' => 'Tiêu đề bài viết dài quá 120 ký tự'
				)

		)
		 );
		$this->add ( array (
				'name' => 'type',
				'type' => 'Text',
				'options' => array (
						'label' => 'phân loại'
				),
				'attributes' => array (
						'id' => 'faq_tag_type',
						'class' => 'form-control'
				)

		)
		 );

		$this->add ( array (
				'name' => 'tag_desc',
				'type' => 'TextArea',
				'options' => array (
						'label' => 'Nội dung chi tiết bài viết'
				),
				'attributes' => array (
						'id' => 'faq_txt_tag_desc',
						'class' => 'form-control faq_txt_content',
						// 'cols'=>'',
						'rows' => '100'
				)
		) );
		$this->add ( array (
				'name' => 'tag_relationship',
				'type' => 'Text',
				'options' => array (
						'label' => 'Từ khoá'
				),
				'attributes' => array (
						'id' => 'faq_txt_tag_relationship',
						'class' => 'form-control',
						'placeholder' => 'Từ khoá',
						'data-role' => 'tagsinput'
				)

		) );
		// File Input
		$file = new Element\File ('tag_avatar' );
		$file->setLabel ( 'Avatar Image Upload' )->setAttribute ( 'id', 'tag_avatar' );
		$this->add ( $file );
	}
}
?>