<div class="wrap">
<h2>Настройки</h2>
<p>Станица настроек плагина Post Type Builder</p>
<form method="post">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Категории:</th>
<td>
<select>
<?php 
$c = $this->categories;
foreach($c as $key=>$value):?>
<option><?php echo $value; ?></option>
<?php endforeach; ?>
</select>
</td>
</tr>
<h3>gegg</h3>
<tr valign="top">
<th scope="row">Новая категория:</th>
<td>
<input class="category" style='width:50%' type="text" placeholder="Название категории" name="category" />
<span id="add_category" class="dashicons dashicons-welcome-add-page" style="cursor:pointer;"></span>
</td>
</tr>

</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="shop_url,shop_key" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>

</div>