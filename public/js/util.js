const BASE_URL = "http://localhost/gpmteste/"; // util para as requisições ajax

const DATATABLE_PTBR = {
    "sEmptyTable": "Nenhum registro encontrado",
    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
    "sInfoPostFix": "",
    "sInfoThousands": ".",
    "sLengthMenu": "_MENU_ resultados por página",
    "sLoadingRecords": "Carregando...",
    "sProcessing": "Processando...",
    "sZeroRecords": "Nenhum registro encontrado",
    "sSearch": "Pesquisar",
    "oPaginate": {
        "sNext": "Próximo",
        "sPrevious": "Anterior",
        "sFirst": "Primeiro",
        "sLast": "Último"
    },
    "oAria": {
        "sSortAscending": ": Ordenar colunas de forma ascendente",
        "sSortDescending": ": Ordenar colunas de forma descendente"
    }
}

function clearErrors() { //1  funcao para limpar erros da classe .has error do bootstrap
	$(".has-error").removeClass("has-error"); // jquery funcao para limpar erros do html
	$(".help-block").html(""); // limpar o html colocar vazio
}

function showErrors(error_list) {
	clearErrors(); // toda vez que apresentar novos erros é preciso limpar

	$.each(error_list, function(id, message) { // percorrer recebe dois parametros um #btn_login : Mensagem
		$(id).parent().parent().addClass("has-error");
		$(id).parent().siblings(".help-block").html(message)
	})
} 

function showErrorsModal(error_list) { // erros do modal
	clearErrors();

	$.each(error_list, function(id, message) {
		$(id).parent().parent().addClass("has-error");
		$(id).siblings(".help-block").html(message)
	})
} 

function loadingImg(message="") {
	return "<i class='fa fa-circle-o-notch fa-spin'></i>&nbsp;" + message // icone rodando spin .....ná hora do loading $(".help-block").html(LoadingImg)("Verificando")
}

function uploadImg(input_file, img, input_path) { // esssa função é responsavel de fazer o upload da imagem

	src_before = img.attr("src");
	img_file = input_file[0].files[0]; // objeto arquivo do input_file coloca na variavel
	form_data = new FormData(); // nativo formulario do javascript

	form_data.append("image_file", img_file);

	$.ajax({
		url: BASE_URL + "restrict/ajax_import_image", //requisição ajax
		dataType: "json",
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,
		type: "POST",
		beforeSend: function() {
			clearErrors();
			input_path.siblings(".help-block").html(loadingImg("Carregando imagem..."));
		},
		success: function(response) {
			clearErrors();
			if (response["status"]) {
				img.attr("src", response["img_path"]);
				input_path.val(response["img_path"]);
			} else {
				img.attr("src", src_before);
				input_path.siblings(".help-block").html(response["error"]);
			}
		},
		error: function() {
			img.attr("src", src_before);
		}
	})

}
