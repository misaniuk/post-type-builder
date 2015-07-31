jQuery(document).ready(function($){
	$("form").submit(function(e){
		e.preventDefault();
		var data = $(this).serialize();
		console.log(data);
		$.post("http://wc.dev/wp-admin/admin-ajax.php?action=new_taxonomy",data,function(response){
			document.location.href=response;
			console.log(response);
		});
	});
})