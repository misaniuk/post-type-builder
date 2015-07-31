jQuery(document).ready(function($){
	$("form").submit(function(e){
		e.preventDefault();
		var data = $(this).serialize();
		console.log(data);
        var admin_url = builder_defaults['admin_url'];
		$.post(admin_url+"admin-ajax.php?action=new_taxonomy",data,function(response){
			document.location.href=response;
			console.log(response);
		});
	});
})