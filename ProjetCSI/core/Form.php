<?php
class Form {

    public $controller;

    public function __construct($controller){
        $this->controller = $controller;
    }

    public function input($name,$label, $options=array()){

        if(!isset($this->controller->request->data->$name)){
            $value = '';
        }else{
            $value = $this->controller->request->data->$name;
        }

        $html = '<div class="">
                    <label for="input"'.$name.'">'.$label.'</label>
                    <div class="input">';

        if(!isset($options['type'])){
            $html .= '<input type="text" id="input'.$name.'" name="'.$name.'" value="'.$value.'">';
        }elseif($options['type'] == 'textarea'){
            $html .= '<textarea id="input'.$name.'" name="'.$name.'" >'.$value.'</textarea>';
        }elseif($options['type'] == 'select'){
            $html .= '<select id="input'.$name.'" name="'.$name.'" required>';

            if(isset($options['options'])){
                foreach ($options['options'] as $o){
                    $html .= '<option ';
                    if($o == $value){
                        $html .= 'selected ';
                    }
                    $html .= 'value="'.$o.'">'.$o.'</option>';
                }
            }
            $html .= '</select>';

        }elseif($options['type'] == 'password') {
            $html .= '<input type="password" id="input'.$name.'" name="'.$name.'" value="'.$value.'">';
        }elseif($options['type'] == 'hidden'){
            $html .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
        }elseif ($options['type'] == 'checkbox'){
            $html .= '<input type="hidden" name="'.$name.'" value="0">
            <input type="checkbox" name="'.$name.'" value="1" '.(empty($value)?'':'checked').'>';
        }else{

        }
        
        $html .= '</div> </div>';    

        return $html;
    }
}