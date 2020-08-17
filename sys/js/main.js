//MARCA COMO COMPRADO
$(document).ready(function clicktoHide() {
	$("#comprados").css("display", "none")
	$(".to_hide").click(function hide(){
		var line = $(this)
		line.hide()
		$("#comprados").css({"display": "block", "width": "100%"})
		$("#comprados table").css("width", "100%")
		$("#comprados table").append("<tr>"+line.html()+"</tr>")
	})
})

//AUTO COMPLETAR
$(document).ready(function() {
	//$("#auto_value").attr("size", $("#auto_value option").length)
	var display = $("#auto_value option").css("display")

	$("#auto_search").keyup(function(){
		//$("#auto_value").attr("size", $("#auto_value option").length)
		var texto = replaceSpecialChars($(this).val().toLowerCase())
		
		$("#auto_value option").css("display", display)
		$("#auto_value option").each(function(){
			var string = replaceSpecialChars($(this).text().toLowerCase())
			var position = string.indexOf(texto)
			if(position < 0)
				$(this).css("display", "none")
			
		})
	})
   $("#auto_value").click(function(){
       $(".autocomplete").val($(this).val())
   })
})

//FILTROS
$(document).ready(function(){
	$(".filtro").change(function(){
		var filtro_key = $(this).attr("id")
		var filtro_val = $(this).val()
		var url_atual = $(location).attr("search")
		var url_new = ""
			
		var find = "&"+filtro_key+"="
		
		if(filtro_val != ''){
			if(url_atual.search(filtro_key)==-1){
				url_new = url_atual+"&"+filtro_key+"="+filtro_val
			}else{
				var id_num = url_atual.search(find)+find.length
				var id = url_atual.slice(id_num)
				
				url_new = url_atual.replace(find+id, find+filtro_val)
			}
		}else{
			if(url_atual.search(filtro_key)==-1){
				url_new = url_atual
			}else{
				var id_num = url_atual.search(find)+find.length
				var id = url_atual.slice(id_num)
				
				url_new = url_atual.replace(find+id, '')
			}
		}
		
		$(location).attr("search", url_new)
	})
})

//JANELA MODAL
$(document).ready(function(){
	$(".modal").click(function( e ){
		
		//cancela o comportamento padrão do link
		e.preventDefault();
		//armazena o atributo href do link
		var page = "#dialog";
		var link = $(this).attr('href');
		var title = $(this).attr("value")
		
		//define o título da página modal
		$("#dialog h2").html(title)
		
		//armazena a largura e a altura da tela
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		//Define largura e altura do div#mask iguais ás dimensões da tela
		$('#mask').css({'width':'100%','height':'100%'});
		//efeito de transição
		$('#mask').fadeIn(500);
		$('#mask').fadeTo("slow",0.8);
		
		$("#dialog").height("auto");
		$("#dialog").width("60%");
		
		//armazena a largura e a altura da janela
		var winH = $(window).height();
		var winW = $(window).width();
		//centraliza na tela a janela popup
		$(page).css('top',  25);
		$(page).css({'left': '10%', 'margin-right': '10%'});
		
		//efeito de transição
		$(page).fadeIn(1000);
		$(page+" article").load(link);
	});
		
	//se o botãoo fechar for clicado
	$('.barra .close').click(function (e) {
		//cancela o comportamento padrão do link
		e.preventDefault();
		$('#mask, .window').hide();
	});
	
	//se div#mask for clicado
	$('#mask').click(function () {
		$(this).hide();
		$('.window').hide();
	});
});

//MENU RESPONSIVO
$(document).ready(function(){
	$("#open_menu").click(function(){
		$("header nav").attr("class", "oppened")		
	})
	$("main").click(function(){
		$(".oppened").attr("class", "closed")
	})
})

//BUSCA
$(document).ready(function(){
	$("#busca input").keyup(function(){
		var texto = replaceSpecialChars($(this).val().toLowerCase())

		$(".cardmini, .card, .card_banca, tr, main li").css("display", "flex")
		$("tr").css("display", "table-row")
		$(".cardmini, .card, .card_banca, tr, main li").each(function(){
			var string = replaceSpecialChars($(this).text().toLowerCase())
			var position = string.indexOf(texto)
			if(position < 0)
				$(this).css("display", "none")
			
		})
	})
})

function replaceSpecialChars(str){
  str = str.replace(/[ÀÁÂÃÄÅ]/,"A");
  str = str.replace(/[àáâãäå]/,"a");
  str = str.replace(/[ÈÉÊË]/,"E");
  str = str.replace(/[èéêë]/,"e");
  str = str.replace(/[ÌÍÎÏ]/,"I");
  str = str.replace(/[ìíîï]/,"i");
  str = str.replace(/[ÒÓÔÖ]/,"O");
  str = str.replace(/[òóôö]/,"o");
  str = str.replace(/[ÙÚÛÜ]/,"U");
  str = str.replace(/[ùúûü]/,"u");
  str = str.replace(/[Ç]/,"C");
  str = str.replace(/[ç]/,"c");

  return str.replace(/[^a-z0-9]/gi,''); 
}