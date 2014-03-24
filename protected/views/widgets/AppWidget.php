<?php 

class AppWidget extends CWidget{
	
	protected function icon($type){
        return '<span class="glyphicon glyphicon-'.$type.'"></span> ';
    }
}