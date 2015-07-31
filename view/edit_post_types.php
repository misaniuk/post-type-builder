<div class="wrap">
<h2>Редактор Post Type</h2>
<p>Станица для редактирования custom post types</p>
<form method="post">
<table class="form-table">
<select id="types">
</select>
</table>

<p class="submit">
<input type="submit" class="button-primary update" value="Обновить" />
    <span class="dashicons dashicons-trash trash" style="font-size:28px;color:rosybrown;cursor:pointer;"></span>

</p>
<table class="form-table">
<h1>Post Type</h1>
<?php
Html::Input("Post type","post type","post_type");
Html::Input("label","label","label");
Html::Category("labels");
echo "<tr valign='top'><th scope='row' style='padding-left:50px!important'><i>Иконка:</i></th><td style='width:189px'><input class='category menu_icon' style='width:50' type='text' placeholder='Класс иконки' name='menu_icon' /></td><td style='width:1px!important;'><span style='  color: #0073aa;
  cursor: pointer;' class='dashicons dashicons-admin-appearance add_menu_icon'></span></td><td><span style='  color: #0073aa;
  cursor: pointer;' class='dashicons dashicons-format-image add_menu_image dashicons-picker'  data-target='.menu_icon'></span></td>
</tr>";

	//аргументы для label
	foreach(array("menu_position","name","singular_name",'menu_name','name_admin_bar','all_items' ,'add_new','product','add_new_item','edit_item','new_item','view_item' ,'search_items' ,'not_found','not_found_in_trash' ,'parent_item_colon' 
	) as $value) {
	Html::InputChild($value,$value,$value);
	}

Html::Category("supports");

	//аргументы для label
	foreach(array("title","editor","author","thumbnail",'excerpt','trackbacks','custom-fields' ,'comments','revisions','page-attributes','page-formats') as $value) {
	Html::CheckBox($value,$value);
	}

Html::Category("rewrite");

	//аргументы для rewrite
	foreach(array("slug","with_front","feeds","pages",'ep_mask'
	) as $value) {
	Html::InputChild($value,$value,$value);
	}



//strings
Html::TextArea("Описание","Описание","description");
Html::TextArea("Коллбэк","function() {}","register_meta_box_cb");

Html::Category("Таксономии");
$taxonomies = get_taxonomies();
unset($taxonomies['nav_menu']);
unset($taxonomies['link_category']);
unset($taxonomies['post_format']);
foreach($taxonomies as $value) {
	Html::CheckBox($value,$value);
	}


Html::Select("capability_type",array("post","page"));
Html::Input("permalink_epmask","permalink_epmask","permalink_epmask");




//bool'ы
foreach(array(
	array("public",false),
	array("exclude_from_search",false),
	array('publicly_queryable',false),
	array('show_ui',false),
	array('show_in_menu',false),
	array('show_in_nav_menus' ,false),
	array('show_in_admin_bar',false),
	array('map_meta_cap',false),
	array('hierarchical',false),
	array('has_archive',false),
	array('query_var',false),
	array('can_export',false),
	array('_builtin' ,false)
	 ) as $value) {
Html::Bool($value[0],$value[0],$value[1]);
}
Html::Nonce();
?>
</table>
</form>

</div>