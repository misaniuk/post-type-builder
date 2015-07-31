<?php

class Html{


	public static function Category($name){
		echo "<tr valign='top'><th scope='row'>{$name}:</th></tr>";

	}


	public static function SubCategory($name){
		echo "<h4>{$name}</h4>";
	}


	public static function Input($label,$placeholder,$name){
		echo "<tr valign='top'><th scope='row'>{$label}:</th><td><input class='category' style='width:50' type='text' placeholder='{$placeholder}' name='{$name}' /></td></tr>";

	}


	public static function InputChild($label,$placeholder,$name){
		echo "<tr valign='top'><th scope='row' style='padding-left:50px!important'><i>{$label}:</i></th><td><input class='category' style='width:50' type='text' placeholder='{$placeholder}' name='{$name}' /></td></tr>";

	}



	public static function TextArea($label,$placeholder,$name){
		echo "<tr valign='top'><th scope='row'>{$label}:</th><td><textarea class='category' style='width:50' placeholder='{$placeholder}' name='{$name}'></textarea></td></tr>";

	}



	public static function CheckBox($name,$value) {
		echo "<tr valign='top'><td style='padding-left:50px!important'><input type='checkbox' name='{$name}' value='{$value}'>{$value}</td></tr>";
	}



	public static function Select($name,$options) {
		$output = "<tr valign='top'>
		<th scope='row'>{$name}:</th>
		<td>
		<select name='{$name}'>";
		foreach($options as $option)
		{
			$output.="<option value='{$option}'>{$option}</option>";
		}
		
		$output.= "</select>
		</td></tr>";
		echo $output;
	}



	public static function Bool($label,$name,$default) {
		if(false === $default)
		{
			echo "<tr valign='top'><th scope='row'>{$label}:</th><td><input class='category' style='width:50' type='radio' value='true' name='{$name}'/>Да  <input class='category' style='width:50' type='radio' value='false' name='{$name}' checked/>Нет</td></tr>";

		}
		else 
		{
			echo "<tr valign='top'><th scope='row'>{$label}:</th><td><input class='category' style='width:50' type='radio' value='true' name='{$name}' checked/>Да  <input class='category' style='width:50' type='radio' value='false' name='{$name}' />Нет</td></tr>";

		}


	}

}

?>