jQuery(document).ready(function($){

	
	if(false != builder_post_types)
	{
		//Сама ф-я для загрузки пост тайпа
	var load_post_type = function(e){
        //Чистим все поля
        $("input:text,textarea").val("");
        $('input:checkbox').removeAttr('checked');
        //Разобраться с radio кнопками!
        $("[type='radio'][value='false']").prop("checked",true);
		var post_type = $("#types").find("option:selected").text();
		var it = builder_post_types[post_type];
        $("[name='post_type']").val(post_type);
		$("h1").text(it['label']);
		$("[type='checkbox']").prop("checked",false);
		$.each(it,function(i,val){
            console.log(typeof val);
			if((typeof val) === "object"){
				$.each(val,function(i,value){
					$("[name='"+i+"']:not([type='checkbox'],[type='radio'])").val(value);
					$("[type='checkbox'][name='"+value+"']").prop("checked",true);
				});
			}

			else if(typeof val === "boolean")
			{
                $("[name='"+i+"']").first().prop("checked",true);
			}
			else {
				$("[name='"+i+"']").val(val);
			}
		});
	};

	var types = builder_post_types;
	var select = $("#types");
	$.each(types,function(i,val){
		select.append("<option>"+i+"</option>");
	});
	load_post_type();
	$("form").submit(function(e){
		e.preventDefault();
		var post_type = select.find("option:selected").text();
		var data = $(this).serialize();
		$.post("http://wc.dev/wp-admin/admin-ajax.php?action=update_builder_post_types",data,function(response){
			document.location.href=response;
			console.log(response);
		});

	});
	//При изменении селекта, грузим нужный пост тайп
	$("#types").on("change",load_post_type);

}
else {
	$("form").hide();
	var msg = '<div id="message" class="error"><p>Добавьте сперва пост тайп для реадактирования!</p></div>';
	$(".wrap").append(msg);
}


// wp.media.view.Router = wp.media.view.Router.extend(
//   {
//     select : function(id){
//       var view = this.get( id );
//       this.deselect();
//       if(view && view.$el)
//         view.$el.addClass('active');
 
//      if(id == "myaction"){
//         alert(1);
//       }
//     }
// });

var formfield = null;

     var wireframe;

     $('body').on('click', '.add_menu_icon',function(e) {
         e.preventDefault();
         if (wireframe) {
             wireframe.open();
             return;
         }

         wireframe = wp.media.frames.wireframe = wp.media({
             title: 'Выбрать иконку',
             button: {
                 text: 'Вставить'
             },
             multiple: false
         });

         wireframe.on('select', function() {
             attachment = wireframe.state().get('selection').first().toJSON();
             console.log(attachment);
             $("input[name='menu_icon']").val(attachment.url);
         });


         wireframe.open();
     });
})

     