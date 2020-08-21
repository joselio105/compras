// s/ JQuery
//Selecionar linha da tabela

/*

+ 01) Estado inicial (tabela1 preenchida, tabela2 vazia)
+ 02) Verificar itens no localStorage, remover da tabela1 e enviar para a tabela2
03) Ordenar tabelas
04) Em caso de clique na tabela1:
+	04.1) Acrecesntar a linha ao localStorage
+	04.2) Remover a linha da tabela1
+	04.3) Adicionar a linha à tabela2
	04.4) Ordenar tabelas
05) Em caso de clique na tabela2:
	05.1) Remover linha do localStorage
	05.2) Remover a linha da tabela2
	05.3) Adicionar a linha à tabela1
	05.4) Ordenar tabelas
06) Botão que ao clicado limpa tabela2:
+	06.1) Limpa localStorage
+	06.2) Limpa tabela2
+	06.3) Recoloca itens removidos para tabela1
	06.4) Ordena tabela1
*/

window.addEventListener("load", () => {
	const tabela1 = document.querySelector("#comprar-table")
	const tabela2 = document.querySelector("#comprados-table")
	const selecteds = []
	const noCarrinho = readListOnServer()	
	const paraComprar = getRowsFromTable(tabela1)
	const paraComprarAinda = avoidDoubleItens(noCarrinho, paraComprar)

	console.log(noCarrinho)

	renderTable(tabela1, paraComprarAinda)	
	renderTable(tabela2, getRows(noCarrinho, paraComprar))
	
	paraComprarAinda.forEach(linha=>{
		let clickNum = 0
		linha.addEventListener("click", ()=>{
			
			const actions = {
				1: ()=>{
					console.log(`Clique ${clickNum} - Selecionar linha#${linha.id}`)
					linha.className = "selected"
					addToList(linha, selecteds)
					clickNum = 2
				},
				2: ()=>{
					console.log(`Clique ${clickNum} - Deselecionar linha#${linha.id}`)
					linha.className = ""	
					removeFromList(linha, selecteds)
					clickNum = 0
				},
				0: ()=>{
					console.log(`Clique ${clickNum} - Enviar para o carrinho a linha#${linha.id}`)
					addToList(linha.id, noCarrinho)
					saveListOnServer(noCarrinho)
					renderTable(tabela2, getRows(noCarrinho, paraComprar))
					clickNum = 1
				}
			}
			
			actions[clickNum]()
		})
	})
	
	document.querySelector("#cleanBasket").addEventListener("click", ()=>{
		saveListOnServer([])
		const noCarrinho = readListOnServer()
		renderTable(tabela1, paraComprarAinda)	
		renderTable(tabela2, getRows(noCarrinho, paraComprar))
		
	})
})

function renderTable(tabela, listaLinhas){
	const titles = getTitlesFromTable(tabela)
	const row = document.createElement("tr")
	
	//limpa tabela
	while (tabela.firstChild) {
	  tabela.removeChild(tabela.firstChild);
	}
	
	//acrescenta títulos
	row.setAttribute("class", "title")
	titles.forEach(th=>{
		row.appendChild(th)
	})
	tabela.appendChild(row)
	
	//acrescenta linhas
	listaLinhas.forEach(linha=>{
		tabela.appendChild(linha)
	})
}

function avoidDoubleItens(toExcludeList, mainList){
	let response = []
	
	mainList.forEach(item=>{
		toExcludeList.forEach(toExclude=>{
			if(toExclude !== item.id){
				response.push(item)
			}
		})
		if(toExcludeList.length === 0){
			response = mainList
		}
	})
	
	return response
}

function getRows(idList, rows){
	const response = []
	
	rows.forEach(row=>{
		idList.forEach(id=>{
			if(id === row.id){
				response.push(row)
			}
		})
	})
	return response
}

function getTitlesFromTable(table){
	return table.querySelectorAll("th")
}

function getRowsFromTable(table){	
	const rows = table.querySelectorAll("tr")
	const response = []
	
	rows.forEach(row=>{
		if(row.className !== "titles"){
			response.push(row)
		}
	
	})
	
	return response
}

function saveListOnServer(noCarrinho){
	localStorage.setItem("noCarrinho", JSON.stringify(noCarrinho))
}

function readListOnServer(){
	return JSON.parse(localStorage.getItem("noCarrinho"))
}

function addToList(element, list){
	if(list.indexOf(element) === -1){
		list.push(element)
	}
}

function removeFromList(element, list){
	const busca = list.indexOf(element)
	if(busca !== -1){
		list.splice(busca, 1)
	}	
}

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