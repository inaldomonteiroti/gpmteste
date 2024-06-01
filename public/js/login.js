$(function() {

	$("#login_form").submit(function() {

		$.ajax({
			type: "post", // metodo
			url: BASE_URL + "restrict/ajax_login", // url que vai fazer o post
			dataType: "json", //
			data: $(this).serialize(), // dados do form e vai enviar
			beforeSend: function() { // antes de enviar
				clearErrors(); // limpar erros
				$("#btn_login").parent().siblings(".help-block").html(loadingImg("Verificando..."));
			},
			success: function(json) {
				if (json["status"] == 1) {
					clearErrors();
					$("#btn_login").parent().siblings(".help-block").html(loadingImg("Logando..."));
					window.location = BASE_URL + "restrict";
				} else {
					showErrors(json["error_list"]);
				}
			},
			error: function(response) {
				console.log(response);
			}
		})

		return false;
	})

})
