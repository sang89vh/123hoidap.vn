<?php 
namespace  Web\Forms;
use Zend\Form\Form;
class UpdateEmailTwiterForm extends  Form{
    private $form_name;
    private $email;
    private $submit;

    public function __construct($form_name, $row_id, $user_id, $email, $name, $sex, $location_name, $submit){
        parent::__construct($form_name);
        $this->setAttribute("method", "post");
        //$this->setAttribute("class", "form-group");
        $this->add(array(
            'name'=>'id',
            'attributes'=>array(
                'type'=>'hidden',
                'value'=>$row_id,
        ) 
        ));
        $this->add(array(
            'name'=>'user_id',
            'attributes'=>array(
              'type'=>'hidden',
              'class'=>'form-control',
              'value'=>$user_id
        )
        ));
        $this->add(array(
            'name'=>'email',
            'attributes'=>array(
                'type'=>'text',
                'class'=>'form-control',
                'value'=>$email
            ),
            'options'=>array(
               'label'=>'Email'
             )
        ));
        $this->add(array(
            'name'=>'name',
            'attributes'=>array(
               'type'=>'text',
                'class'=>'form-control',
               'value'=>$name
            ),
            'options'=>array(
                'label'=>'FullName'
            )
        ));
        $this->add(array(
            'name'=>'sex',
            'attributes'=>array(
                'type'=>'text',
                'class'=>'form-control',
                'value'=>$sex
            ),
            'options'=>array(
                'label'=>'Sex'
             )
        ));
        $this->add(array(
            'name'=>'location',
            'attributes'=>array(
                'type'=>'text',
                'class'=>'form-control',
                'value'=>$location_name  
            ), 
            'options'=>array(
                'label'=>'Location'
            )
        ));
        $this->add(array(
            'name'=>'submit',
            'attributes'=>array(
                'type'=>'submit',
                'width'=>'200px',
                'class'=>'form-control',
                'value'=>$submit,
                'id'=>'submit'
            )
        ));
        
    }
}
?>