<?php
/*
Plugin Name: Post Type Builder
Plugin URI:
Description: Плагин для создания кастомных пост тайпов
Version: 1.0
Author: Denis Misaniuk
*/

include "lib/HtmlRenderer.php";

class PostTypeBuilder {

	public $plugin_slug = "post_type_builder";
	public $plugin_name = "Post Type Builder";
	public $plugin_icon = "dashicons-edit";
	public $menu_pages = array("Новый Post Type","Настройки");
    public $defaults = array();
	public function __construct() {
        add_action("init",array($this, "init"));
	}

	public function init() {
        $this->defaults['nounce'] = wp_create_nonce('builder');
        $this->defaults['admin_url'] = admin_url();
		if (!get_option("builder_post_types")) {
			add_option( "builder_post_types");
		}
		else {
			$this->builder_regiser_post_type();
		}

			if (!get_option("builder_taxonomies")) {
			add_option( "builder_taxonomies");
		}
		else {
			$this->builder_regiser_taxonomies();
		}

		if(@$_GET['page'] == 'new_post_type') {
			wp_register_script( "create_post_type",plugins_url()."/post-type-builder/js/create_post_type.js",'jQuery',false, true );
            wp_localize_script( "create_post_type", "builder_defaults", $this->defaults);
            wp_enqueue_script("create_post_type");
            add_action('admin_enqueue_scripts', function() {
                wp_enqueue_media();
                $css = plugin_dir_url( __FILE__ ) . 'css/dashicons-picker.css';
                wp_enqueue_style( 'dashicons-picker', $css, array( 'dashicons' ), '1.0' );

                $js = plugin_dir_url( __FILE__ ) . 'js/dashicons-picker.js';
                wp_enqueue_script( 'dashicons-picker', $js, array( 'jquery' ), '1.0' );

            });

		}
		//editor
		if(@$_GET['page'] == 'edit_post_types') {
			wp_register_script( "edit_post_type",plugins_url()."/post-type-builder/js/edit_post_type.js",'jQuery',false, true );
			wp_localize_script( "edit_post_type", "builder_post_types", unserialize(get_option("builder_post_types")) );
            wp_localize_script( "edit_post_type", "builder_defaults", $this->defaults);
            wp_enqueue_script("edit_post_type");
			add_action('admin_enqueue_scripts', function() {
				wp_enqueue_media();
			    $css = plugin_dir_url( __FILE__ ) . 'css/dashicons-picker.css';
			    wp_enqueue_style( 'dashicons-picker', $css, array( 'dashicons' ), '1.0' );

			    $js = plugin_dir_url( __FILE__ ) . 'js/dashicons-picker.js';
			    wp_enqueue_script( 'dashicons-picker', $js, array( 'jquery' ), '1.0' );

			});
		}

		if(@$_GET['page'] == 'new_taxonomy') {
			wp_register_script( "new_taxonomy",plugins_url()."/post-type-builder/js/new_taxonomy.js",'jQuery',false, true );
			wp_localize_script( "new_taxonomy", "builder_taxonomies", unserialize(get_option("builder_taxonomies")) );
            wp_localize_script( "new_taxonomy", "builder_defaults", $this->defaults);
            wp_enqueue_script("new_taxonomy");
		}

		if(@$_GET['page'] == 'edit_taxonomies') {
			wp_register_script( "edit_taxonomies",plugins_url()."/post-type-builder/js/edit_taxonomies.js",'jQuery',false, true );
			wp_localize_script( "edit_taxonomies", "builder_taxonomies", unserialize(get_option("builder_taxonomies")) );
            wp_localize_script( "edit_taxonomies", "builder_defaults", $this->defaults);
            wp_enqueue_script("edit_taxonomies");
		}


		add_action("wp_ajax_update_builder_post_types",array($this,"update_builder_post_types"));
		add_action("wp_ajax_nopriv_update_builder_post_types",array($this,"update_builder_post_types"));


		add_action("wp_ajax_edit_builder_post_type",array($this,"edit_builder_post_type"));
		add_action("wp_ajax_nopriv_edit_builder_post_type",array($this,"edit_builder_post_type"));

		add_action("wp_ajax_new_taxonomy",array($this,"new_taxonomy"));
		add_action("wp_ajax_nopriv_new_taxonomy",array($this,"new_taxonomy"));
		add_action("admin_menu",array($this,"add_plugin_menu"));

        //trash
        add_action("wp_ajax_builder_trash",array($this,"builder_trash"));
        add_action("wp_ajax_nopriv_builder_trash",array($this,"builder_trash"));

	}



	public function builder_regiser_post_type() {
			foreach(unserialize(get_option("builder_post_types")) as $key=>$val) {
			register_post_type($key,$val);
		}
        flush_rewrite_rules();
	}	

	public function builder_regiser_taxonomies() {
        foreach(unserialize(get_option("builder_taxonomies")) as $key=>$val) {
			register_taxonomy($val[0],$val[1],$val[2]);
		}
	}		

    public function builder_trash() {
        check_ajax_referer("builder");
        $data = $_POST;
        $option  = $data['option'];
        $key  = $data['key'];
        $array = unserialize(get_option($option));
        unset($array[$key]);
        update_option($option,serialize($array));
    }



	public function update_builder_post_types() {
        check_ajax_referer("builder");
        //Проверяем наш массив POST на неправильные символы (sanitize_text_field() )
        //Проверям на пустоту все значения в переданном массиве required
        $val = $this->check_post_values(array("post_type"));

		$params = (get_option("builder_post_types") == '') ? array() : unserialize(get_option("builder_post_types"));
		$post_type = $_POST['post_type'];
		$capability_type = $val['capability_type'] ;
		$supports = array();
		$taxonomies = array();
		foreach(get_taxonomies() as $key=>$value) {
			if(isset($val[$key])) {
				$taxonomies[] = $key;
			}
		}
		foreach( array('title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','page-formats') as $value ){
			if(isset($val[$value])) {
				$supports[] = $value;
			}
		};

			$params[$post_type]= array(
				"label" => $val['label'] ,
				"public" => filter_var($val['public'], FILTER_VALIDATE_BOOLEAN),
				"show_in_menu" => filter_var($val['show_in_menu'], FILTER_VALIDATE_BOOLEAN),
				"taxonomies" => $taxonomies,
				"labels" => array(
					'name' => isset($val['name']) ? $val['name'] : "" ,
					'singular_name' => isset($val['singular_name']) ? $val['singular_name'] : "" ,
					'menu_name'  => isset($val['menu_name']) ? $val['menu_name'] : "" ,
					'name_admin_bar'  => isset($val['name_admin_bar'] ) ? $val['name_admin_bar'] : "" ,
					'all_items'  => isset($val['all_items']) ? $val['all_items'] : "" ,
					'add_new'  => isset($val['add_new']) ? $val['add_new'] : "" ,
					'product' => isset($val['product']) ? $val['product'] : "" ,
					'add_new_item'  => isset($val['add_new_item']) ? $val['add_new_item'] : "" ,
					'edit_item'  => isset($val['edit_item']) ? $val['edit_item'] : "" ,
					'new_item'  => isset($val['new_item']) ? $val['new_item'] : "" ,
					'view_item'  => isset($val['view_item']) ? $val['view_item'] : "" ,
					'search_items'  => isset($val['search_items']) ? $val['search_items'] : "" ,
					'not_found'  => isset($val['not_found']) ? $val['not_found'] : "" ,
					'not_found_in_trash'  => isset($val['not_found_in_trash']) ? $val['not_found_in_trash'] : "" ,
					'parent_item_colon'  => isset($val['parent_item_colon']) ? $val['parent_item_colon'] : ""
                ),
					"description" => isset($val['description']) ?  $val['description'] : "",
					"show_ui" => filter_var($val['show_ui'], FILTER_VALIDATE_BOOLEAN),
					"exclude_from_search" => filter_var($val['exclude_from_search'], FILTER_VALIDATE_BOOLEAN),
					"publicly_queryable" => filter_var($val['publicly_queryable'], FILTER_VALIDATE_BOOLEAN) ,
					"show_in_nav_menus" => filter_var($val['show_in_nav_menus'], FILTER_VALIDATE_BOOLEAN) ,
					"show_in_admin_bar" =>filter_var($val['show_in_admin_bar'], FILTER_VALIDATE_BOOLEAN) ,
					"menu_position" => isset($val['menu_position']) ? $val['menu_position'] : "" ,
					"menu_icon" => isset($val['menu_icon']) ?  $val['menu_icon'] : "",
					"capabilities" => array(
						"edit_post"		 => "edit_{$capability_type}",
						"read_post"		 => "read_{$capability_type}",
						"delete_post"		 => "delete_{$capability_type}",
						"edit_posts"		 => "edit_{$capability_type}s",
						"edit_others_posts"	 => "edit_others_{$capability_type}s",
						"publish_posts"		 => "publish_{$capability_type}s",
						"read_private_posts"	 => "read_private_{$capability_type}s",
						"delete_posts"           => "delete_{$capability_type}s",
						"delete_private_posts"   => "delete_private_{$capability_type}s",
						"delete_published_posts" => "delete_published_{$capability_type}s",
						"delete_others_posts"    => "delete_others_{$capability_type}s",
						"edit_private_posts"     => "edit_private_{$capability_type}s",
						"edit_published_posts"   => "edit_published_{$capability_type}s",
						"create_posts"            => "edit_{$capability_type}s"

						),
				"capability_type" => isset($val['capability_type']) ? $val['capability_type'] : "",
				"map_meta_cap" => filter_var($val['map_meta_cap'], FILTER_VALIDATE_BOOLEAN) ,
				"hierarchical" => filter_var($val['hierarchical'], FILTER_VALIDATE_BOOLEAN) ,
				"supports" => $supports,
				"permalink_epmask" => isset($val['permalink_epmask']) ? $val['permalink_epmask'] : "" ,
				"rewrite" =>array(
						"slug" => isset($val['slug']) ? $val['slug'] : "",
						"with_front" => isset($val['with_front']) ? : "",
						"feeds" => isset($val['feeds']) ? $val['feeds'] : "",
						"pages" => isset($val['pages']) ? $val['pages'] : "",
						"ep_mask" => isset($val['ep_mask']) ? $val['ep_mask'] : ""
					),
				"query_var" => filter_var($val['query_var'], FILTER_VALIDATE_BOOLEAN) ,
				"can_export" => filter_var($val['can_export'], FILTER_VALIDATE_BOOLEAN) ,
				"has_archive" => filter_var($val['has_archive'], FILTER_VALIDATE_BOOLEAN),
				"_builtin" => filter_var($val['_builtin'] , FILTER_VALIDATE_BOOLEAN),
//				"_edit_link" =>filter_var($val['_edit_link'], FILTER_VALIDATE_BOOLEAN)
				);
        if(isset($_POST['register_meta_box_cb'])) {
            $params[$post_type]['register_meta_box_cb'] = $_POST['register_meta_box_cb'];
        };
        //Сравниваем чистый массив и шаблон, пустые ключи и пустые поля удаляем
        $this->clean_array($params[$post_type]);
		update_option( "builder_post_types",serialize($params));
        $success_msg = "Пост тайп успешно зарегистрирован!";
        echo  wp_send_json(admin_url("admin.php?page=new_post_type&success_msg={$success_msg}"));
	}

	public function new_taxonomy() {
        check_ajax_referer("builder");
        $t = $this->check_post_values(array("taxonomy",array("object_type" => get_post_types())),"new_taxonomy");
		$taxonomies = (get_option("builder_taxonomies") == '') ? array() : unserialize(get_option("builder_taxonomies"));

		$object_type = array();
		foreach(get_post_types() as $key=>$val) {
			if(isset($t[$key])) {
				$object_type[] = $key;
			}
		}

		$capabilities = array();
		foreach(array('manage_terms','assign_terms','edit_terms','delete_terms') as $val) {
			if(isset($t[$val])) {
				$capabilities[] = $val;
			}
		}
		$taxonomies[$t['taxonomy']] = array($t['taxonomy'],$object_type,array(
				"label" => isset($t['label']) ? $t['label'] : "",
				"labels" => array(
					'name'              => isset($t['name']) ? $t['name'] : "",
					'singular_name'     => isset($t['singular_name']) ? $t['singular_name'] : "",
					'search_items'      => isset($t['search_items']) ? $t['search_items'] : "",
					'all_items'         => isset($t['all_items']) ? $t['all_items'] : "",
					'parent_item'       => isset($t['parent_item']) ? $t['parent_item'] : "",
					'parent_item_colon' => isset($t['parent_item_colon']) ? $t['parent_item_colon'] : "",
					'edit_item'         => isset($t['edit_item']) ? $t['edit_item'] : "",
					'update_item'       => isset($t['update_item']) ? $t['update_item'] : "",
					'add_new_item'      => isset($t['add_new_item']) ? $t['add_new_item'] : "",
					'new_item_name'     => isset($t['new_item_name']) ? $t['new_item_name'] : "",
					'menu_name'         => isset($t['menu_name']) ? $t['menu_name'] : ""
					),
				// "meta_box_cb" => $t['meta_box_cb'],
				"rewrite" => array(
					"slug" => isset($t['slug']) ? $t['slug'] : "",
					"with_front" => isset($t['with_front']) ? $t['with_front'] : "",
					"hierarchical" => isset($t['r_hierarchical']) ? $t['r_hierarchical'] : "",
					"ep_mask" => isset($t['ep_mask']) ? $t['ep_mask'] : ""
					),
				"capabilities" => $capabilities,
				"description" => isset($t['description']) ? $t['description'] : "",
				"update_count_callback" => isset($t['update_count_callback']) ? $t['update_count_callback'] : "",
				"public" => filter_var($t['public'], FILTER_VALIDATE_BOOLEAN) ,
				"show_ui" => filter_var($t['show_ui'], FILTER_VALIDATE_BOOLEAN) ,
				"show_in_nav_menus" => filter_var($t['show_in_nav_menus'], FILTER_VALIDATE_BOOLEAN) ,
				"show_tagcloud" => filter_var($t['show_tagcloud'], FILTER_VALIDATE_BOOLEAN) ,
				"show_in_quick_edit" => filter_var($t['show_in_quick_edit'], FILTER_VALIDATE_BOOLEAN) ,
				"show_admin_column" => filter_var($t['show_admin_column'], FILTER_VALIDATE_BOOLEAN) ,
				"hierarchical" => filter_var($t['hierarchical'], FILTER_VALIDATE_BOOLEAN) ,
				"query_var" => filter_var($t['query_var'], FILTER_VALIDATE_BOOLEAN) ,
				"_builtin" => filter_var($t['_builtin'], FILTER_VALIDATE_BOOLEAN) ,
				"sort" => filter_var($t['sort'], FILTER_VALIDATE_BOOLEAN) ,

			));
        if(isset($_POST['register_meta_box_cb'])) {
            $params[$taxonomies[$t['taxonomy']]['meta_box_cb']]= $_POST['meta_box_cb'];
        };
        //Сравниваем чистый массив и шаблон, пустые ключи и пустые поля удаляем
        $this->clean_array($taxonomies[$t['taxonomy']][1]);
        $this->clean_array($taxonomies[$t['taxonomy']][2]);
        update_option( "builder_taxonomies",serialize($taxonomies));
        $success_msg = "Таксономия успешно зарегистрирована!";
        echo  wp_send_json(admin_url("admin.php?page=new_taxonomy&success_msg={$success_msg}"));
			// print_r($taxonomies);

	}










    public function add_plugin_menu() {
		add_menu_page( $this->plugin_name, $this->plugin_name, "manage_options", $this->plugin_slug, array($this,"get_new_html_for_settings"), $this->plugin_icon, 50);
		add_submenu_page( $this->plugin_slug, "Новый Post Type", "Добавить post type ", "manage_options", "new_post_type", array($this,"get_new_html_for_post_type_builer"));
		add_submenu_page( $this->plugin_slug, "Редактор", "Редактор post types ", "manage_options", "edit_post_types", array($this,"get_new_html_for_edit_post_type_builer"));
		add_submenu_page( $this->plugin_slug, "Новая таксономия", "Добавить таксономию", "manage_options", "new_taxonomy", array($this,"get_new_html_for_new_taxonomy"));
		add_submenu_page( $this->plugin_slug, "Редактор таксономий", "Редактор таксономий", "manage_options", "edit_taxonomies", array($this,"get_new_html_for_edit_taxonomies"));
		remove_submenu_page($this->plugin_slug,$this->plugin_slug);

	}



	public function get_new_html_for_post_type_builer() {
		include("view/new_post_type.php");
	}
	public function get_new_html_for_settings() {
		include("view/settings.php");
	}
	public function get_new_html_for_edit_post_type_builer() {
		include("view/edit_post_types.php");
	}
	public function get_new_html_for_new_taxonomy() {
		include("view/new_taxonomy.php");
	}
	public function get_new_html_for_edit_taxonomies() {
		include("view/edit_taxonomies.php");
	}




    public function check_post_values($required,$redirect = "new_post_type") {

        $data = $_POST;
        //Чекаем все
        foreach($data as $key=>$val) {
            if ( !empty($val) ) {
                    $data[ $key ] = sanitize_text_field( $val);
            }
            else {
                unset($data[$key]);
            }
        }
        //Чекаем только required
        foreach($required as $val) {
            $error_msg = "Поле {$val} обязательно к заполнению";

            if(is_array($val)) {
                //Кол-во выбраных полей в обязательном разделе
                $n = 0;
                foreach($val as $key=>$value) {
                    foreach ($value as $k=>$v) {
                        if(isset($data[$k])) {
                            $n++;
                        }
                    }
                    if(empty($n)) {
                        $error_msg = "Категория {$key} обязательна к заполнению";
                        wp_send_json(admin_url("admin.php?page={$redirect}&error_msg={$error_msg}"));
                    }
                }
            }
            else {
                if(empty($data[$val])) {
                    wp_send_json(admin_url("admin.php?page={$redirect}&error_msg={$error_msg}"));
                }
            }

        }

    return $data;

    }



    public function clean_array(&$array) {
        foreach ($array as $key=>$value) {
            if (empty($value)) {
                unset($array[$key]);
            }
            elseif (is_array($value)) {
                foreach ($value as $k=>$v) {
                    if(empty($v)) {
                        unset($array[$key][$k]);
                    }
                }

            }
        }
        foreach ($array as $key=>$value) {
            if (empty($value)) {
                unset($array[$key]);
            }
        }
            return $array;

    }
}

new PostTypeBuilder();
?>