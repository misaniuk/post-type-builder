<div class="wrap">
    <?php
    if(!empty($_GET['error_msg'])){
        echo '<div id="message" class="error"><p>'.$_GET['error_msg'].'</p></div>';
    }

    elseif(!empty($_GET['success_msg'])){
        echo '<div id="message" class="updated"><p>'.$_GET['success_msg'].'</p></div>';
    }
    ?>
<h2>Создать новую таксономию</h2>
<p>Станица для создания таксономий</p>
<form method="post">
<table class="form-table">
<?php
Html::Input("Имя таксономии","имя","taxonomy");
Html::Category("object_type");

	//аргументы для label
	foreach(get_post_types() as $value) {
		Html::CheckBox($value,$value);
	}

Html::Category("Arguments");
Html::Input("label","label","label");
Html::Category("labels");
foreach(array("name","singular_name",'menu_name','all_items','edit_item','view_item','update_item','add_new_item','new_item_name','parent_item','parent_item_colon','search_items','popular_items','separate_items_with_commas','add_or_remove_items','choose_from_most_used','not_found') as $value) {
	Html::InputChild($value,$value,$value);
	}
Html::TextArea("Коллбэк","function() {}","meta_box_cb");
Html::Category("rewrite");

	//аргументы для rewrite
	foreach(array("slug","with_front","r_hierarchical",'ep_mask'
	) as $value) {
	Html::InputChild($value,$value,$value);
	}
Html::Category("capabilities");

	//аргументы для rewrite
	foreach(array("manage_terms","edit_terms","delete_terms",'assign_terms'
	) as $value) {
	Html::CheckBox($value,$value);
	}
Html::Input("description","description","description");
Html::Input("update_count_callback","update_count_callback","update_count_callback");
//bool'ы
foreach(array(
	array("public",true),
	array('show_ui',true),
	array('show_in_nav_menus' ,true),
	array('show_tagcloud',true),
	array('show_in_quick_edit',true),
	array('show_admin_column',false),
	array('hierarchical',true),
	array('query_var',true),
	array('_builtin' ,false),
	array('sort',true)
	 ) as $value) {
Html::Bool($value[0],$value[0],$value[1]);
    Html::Nonce();
}


?>

</table>

<p class="submit">
<input type="submit" class="button-primary" value="Создать" />
</p>
</form>

</div>