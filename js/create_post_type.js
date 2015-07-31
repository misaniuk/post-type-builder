jQuery(document).ready(function($){
	$("form").submit(function(e){
		e.preventDefault();
		var data = $(this).serialize();
		console.log(data);
		$.post("http://wc.dev/wp-admin/admin-ajax.php?action=update_builder_post_types",data,function(response){
			document.location.href=response;
			console.log(response);
		});
	});

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