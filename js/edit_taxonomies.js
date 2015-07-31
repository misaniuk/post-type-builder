jQuery(document).ready(function($){


    if(false != builder_taxonomies)
    {
        //Сама ф-я для загрузки пост тайпа
        var load_taxonomies = function(e){
            //Чистим все поля
            $("input:text,textarea").val("");
            $('input:checkbox').removeAttr('checked');
            //Разобраться с radio кнопками!
            $("[type='radio'][value='false']").prop("checked",true);
            var taxonomy = $("#types").find("option:selected").text();
            var it = builder_taxonomies[taxonomy];
            $("[name='taxonomy']").val(taxonomy);
            $("h1").text(it[2]['label']);
            $("[type='checkbox']").prop("checked",false);
            $.each(it[1], function(i, val) {
                $("[type='checkbox'][name='"+val+"']").prop("checked",true);
            })
            $.each(it[2],function(i,val){
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

        var types = builder_taxonomies;
        var select = $("#types");
        $.each(types,function(i,val){
            select.append("<option>"+i+"</option>");
        });
        load_taxonomies();
        $("form").submit(function(e){
            e.preventDefault();
            var post_type = select.find("option:selected").text();
            var data = $(this).serialize();
            $.post("http://wc.dev/wp-admin/admin-ajax.php?action=new_taxonomy",data,function(response){
                document.location.href='';
                console.log(response);
            });

        });
        //При изменении селекта, грузим нужный пост тайп
        $("#types").on("change",load_taxonomies);

    }
else {
	$("form").hide();
	var msg = '<div id="message" class="error"><p>Добавьте сперва таксономию для реадактирования!</p></div>';
	$(".wrap").append(msg);
}

});