<?php

defined('BASEPATH') OR exit('No direct script access allowed'); // Segurança ..... impede acesso direto ao arquivo

class Restrict extends CI_Controller{ //Extender a classe principal CI_Controller

	public function __construct() {
		parent::__construct();
		$this->load->library("session"); // Carregar a biblioteca
	}

	public function index(){

		if ($this->session->userdata("user_id")) { // se tiver seção vai para restrict e não para login
			$data = array(    // variaveis customizadas
				"styles" => array(
					"dataTables.bootstrap.min.css",
					"datatables.min.css"
				),
				"scripts" => array(  // variaveis customizadas
					"sweetalert2.all.min.js",
					"dataTables.bootstrap.min.js",
					"datatables.min.js",
					"util.js",
					"restrict.js" //passando a abertura dos modais estão aqui
				),
				"user_id" => $this->session->userdata("user_id")
			);
			$this->template->show("restrict.php", $data);
		} else { // se ele nao está logado
			$data = array( // variaveis customizadas para a view login
				"scripts" => array(  
					"util.js",
					"login.js" 
				)
			);
			$this->template->show("login.php", $data); // Essa é a view do formulário de LOGIN
		}

	}

	public function logoff() { // destruir todas as variaveis de seção 
		$this->session->sess_destroy();
		header("Location: " . base_url() . "restrict"); // força atualização para voltar para o index
	}
	
	public function ajax_login() { // função chamada quando o form for submetido

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array(); // vazio
		$json["status"] = 1; // status 1 ocorreu tudo bem
		$json["error_list"] = array();

		$username = $this->input->post("username"); // recebe via post username e password que vem do form
		$password = $this->input->post("password");

		if (empty($username)) {
			$json["status"] = 0; // erro statos 0
			$json["error_list"]["#username"] = "Usuário não pode ser vazio!";
		} else {
			$this->load->model("users_model"); // 1 Carregando o model users printr - aqui foi passado um ususario
			$result = $this->users_model->get_user_data($username);
			if ($result) { // se achou
				$user_id = $result->user_id;
				$password_hash = $result->password_hash;
				if (password_verify($password, $password_hash)) { // se a senha passada ta batendo com o hash
					$this->session->set_userdata("user_id", $user_id);  // criar uma seção - login feito tem que carregar lá em cima no construtor
				} else {
					$json["status"] = 0; //deu erro
				}
			} else {
				$json["status"] = 0;
			}
			if ($json["status"] == 0) {
				$json["error_list"]["#btn_login"] = "Usuário e/ou senha incorretos!"; // se der erro ...essa é a mensagem de erro
			}
		}

		echo json_encode($json); //transformar o array json no formato json para ser lido com javascript

	}

	public function ajax_import_image() {

		if (!$this->input->is_ajax_request()) { // se a requisição verifica se é do tipo ajax / não permite acesso direto
			exit("Nenhum acesso de script direto permitido!");
		}

		//utilizar uma biblioteca do codgniter para fazer o upload de imagem....obs definir permissão da pasta

		$config["upload_path"] = "./tmp/";
		$config["allowed_types"] = "gif|png|jpg";
		$config["overwrite"] = TRUE;

		$this->load->library("upload", $config); // carregando a biblioteca

		$json = array();
		$json["status"] = 1;

		if (!$this->upload->do_upload("image_file")) { // passado no javascript nome do campo que contem o arquivo
			$json["status"] = 0;
			$json["error"] = $this->upload->display_errors("","");
		} else {
			if ($this->upload->data()["file_size"] <= 1024) {
				$file_name = $this->upload->data()["file_name"];
				$json["img_path"] = base_url() . "tmp/" . $file_name; // formação do caminho
			} else {
				$json["status"] = 0;
				$json["error"] = "Arquivo não deve ser maior que 1 MB!";
			}

		}

		echo json_encode($json);
	}

	public function ajax_save_course() { // salvando o curso

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;
		$json["error_list"] = array();

		$this->load->model("courses_model"); //

		$data = $this->input->post(); // metodo

		if (empty($data["course_name"])) { // se o curso name for vazio
			$json["error_list"]["#course_name"] = "Nome do curso é obrigatório!";
		} else {
			if ($this->courses_model->is_duplicated("course_name", $data["course_name"], $data["course_id"])) { // field , value e id
				$json["error_list"]["#course_name"] = "Nome de curso já existente!";
			}
		}

		$data["course_duration"] = floatval($data["course_duration"]);
		if (empty($data["course_duration"])) {
			$json["error_list"]["#course_duration"] = "Duração do curso é obrigatório!";
		} else {
			if (!($data["course_duration"] > 0 && $data["course_duration"] < 100)) {
				$json["error_list"]["#course_duration"] = "Duração do curso deve ser maior que 0 (h) e menor que 100 (h)!";
			}
		}

		// 0 a 99.9 // precisamos alterar isso no banco

		if (!empty($json["error_list"])) {
			$json["status"] = 0;
		} else {

			if (!empty($data["course_img"])) { // se o image nao estiver vazio , que vem do post no formulario

				$file_name = basename($data["course_img"]); // url completa
				$old_path = getcwd() . "/tmp/" . $file_name; // caminhos fisicos no sistema da pasta tmp / patch
				$new_path = getcwd() . "/public/images/courses/" . $file_name; //  caminhos fisicos no sistema da pasta courses
				rename($old_path, $new_path); 

				$data["course_img"] = "/public/images/courses/" . $file_name; // esse é o path da imagem no banco

			} else {
				unset($data["course_img"]);
			}

			if (empty($data["course_id"])) { // se o curso é novo não teem nada lá
				$this->courses_model->insert($data); // insere o curso
			} else {
				$course_id = $data["course_id"];
				unset($data["course_id"]);
				$this->courses_model->update($course_id, $data);
			}
		}

		echo json_encode($json);
	}

	public function ajax_save_member() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;
		$json["error_list"] = array();

		$this->load->model("team_model");

		$data = $this->input->post();

		if (empty($data["member_name"])) {
			$json["error_list"]["#member_name"] = "Nome do vendedor é obrigatório!";
		} 

		if (!empty($json["error_list"])) {
			$json["status"] = 0;
		} else {

			if (!empty($data["member_photo"])) {

				$file_name = basename($data["member_photo"]);
				$old_path = getcwd() . "/tmp/" . $file_name;
				$new_path = getcwd() . "/public/images/team/" . $file_name;
				rename($old_path, $new_path);

				$data["member_photo"] = "/public/images/team/" . $file_name;

			} else {
				unset($data["member_photo"]);
			}

			if (empty($data["member_id"])) {
				$this->team_model->insert($data);
			} else {
				$member_id = $data["member_id"];
				unset($data["member_id"]);
				$this->team_model->update($member_id, $data);
			}
		}

		echo json_encode($json);
	}

	public function ajax_save_user() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;
		$json["error_list"] = array();

		$this->load->model("users_model");

		$data = $this->input->post();

		if (empty($data["user_login"])) {
			$json["error_list"]["#user_login"] = "Login é obrigatório!";
		} else {
			if ($this->users_model->is_duplicated("user_login", $data["user_login"], $data["user_id"])) {
				$json["error_list"]["#user_login"] = "Login já existente!";
			}
		}

		if (empty($data["user_full_name"])) {
			$json["error_list"]["#user_full_name"] = "Nome Completo é obrigatório!";
		} 

		if (empty($data["user_email"])) {
			$json["error_list"]["#user_email"] = "E-mail é obrigatório!";
		} else {
			if ($this->users_model->is_duplicated("user_email", $data["user_email"], $data["user_id"])) {
				$json["error_list"]["#user_email"] = "E-mail já existente!";
			} else {
				if ($data["user_email"] != $data["user_email_confirm"]) {
					$json["error_list"]["#user_email"] = "";
					$json["error_list"]["#user_email_confirm"] = "E-mails não conferem!";
				}
			}
		}

		if (empty($data["user_password"])) {
			$json["error_list"]["#user_password"] = "Senha é obrigatório!";
		} else {
			if ($data["user_password"] != $data["user_password_confirm"]) {
				$json["error_list"]["#user_password"] = "";
				$json["error_list"]["#user_password_confirm"] = "Senha não conferem!";
			}
		}

		if (!empty($json["error_list"])) {
			$json["status"] = 0;
		} else {

			$data["password_hash"] = password_hash($data["user_password"], PASSWORD_DEFAULT);

			unset($data["user_password"]);
			unset($data["user_password_confirm"]);
			unset($data["user_email_confirm"]);

			if (empty($data["user_id"])) {
				$this->users_model->insert($data);
			} else {
				$user_id = $data["user_id"];
				unset($data["user_id"]);
				$this->users_model->update($user_id, $data);
			}
		}

		echo json_encode($json);
	}

	public function ajax_get_course_data() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;
		$json["input"] = array();

		$this->load->model("courses_model");

		$course_id = $this->input->post("course_id");
		$data = $this->courses_model->get_data($course_id)->result_array()[0];
		$json["input"]["course_id"] = $data["course_id"];
		$json["input"]["course_name"] = $data["course_name"];
		$json["input"]["course_duration"] = $data["course_duration"];
		$json["input"]["course_description"] = $data["course_description"];

		$json["img"]["course_img_path"] = base_url() . $data["course_img"];

		echo json_encode($json);
	}

	public function ajax_get_member_data() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;
		$json["input"] = array();

		$this->load->model("team_model");

		$member_id = $this->input->post("member_id");
		$data = $this->team_model->get_data($member_id)->result_array()[0];
		$json["input"]["member_id"] = $data["member_id"];
		$json["input"]["member_name"] = $data["member_name"];
		$json["input"]["member_description"] = $data["member_description"];

		$json["img"]["member_photo_path"] = base_url() . $data["member_photo"];

		echo json_encode($json);
	}

	public function ajax_get_user_data() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;
		$json["input"] = array();

		$this->load->model("users_model");

		$user_id = $this->input->post("user_id");
		$data = $this->users_model->get_data($user_id)->result_array()[0];
		$json["input"]["user_id"] = $data["user_id"];
		$json["input"]["user_login"] = $data["user_login"];
		$json["input"]["user_full_name"] = $data["user_full_name"];
		$json["input"]["user_email"] = $data["user_email"];
		$json["input"]["user_email_confirm"] = $data["user_email"];
		$json["input"]["user_password"] = $data["password_hash"];
		$json["input"]["user_password_confirm"] = $data["password_hash"];

		echo json_encode($json);
	}

	public function ajax_delete_course_data() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;

		$this->load->model("courses_model");
		$course_id = $this->input->post("course_id");
		$this->courses_model->delete($course_id);

		echo json_encode($json);
	}

	public function ajax_delete_member_data() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;

		$this->load->model("team_model");
		$member_id = $this->input->post("member_id");
		$this->team_model->delete($member_id);

		echo json_encode($json);
	}

	public function ajax_delete_user_data() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$json = array();
		$json["status"] = 1;

		$this->load->model("users_model");
		$user_id = $this->input->post("user_id");
		$this->users_model->delete($user_id);

		echo json_encode($json);
	}

	public function ajax_list_course() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$this->load->model("courses_model");
		$courses = $this->courses_model->get_datatable();

		$data = array();
		foreach ($courses as $course) {

			$row = array();
			$row[] = $course->course_name;

			if ($course->course_img) {
				$row[] = '<img src="'.base_url().$course->course_img.'" style="max-height: 100px; max-width: 100px;">';
			} else {
				$row[] = "";
			}

			$row[] = $course->course_duration;
			$row[] = '<div class="description">'.$course->course_description.'</div>';

			$row[] = '<div style="display: inline-block;">
						<button class="btn btn-primary btn-edit-course" 
							course_id="'.$course->course_id.'">
							<i class="fa fa-edit"></i>
						</button>
						<button class="btn btn-danger btn-del-course" 
							course_id="'.$course->course_id.'">
							<i class="fa fa-times"></i>
						</button>
					</div>';

			$data[] = $row;

		}

		$json = array(
			"draw" => $this->input->post("draw"),
			"recordsTotal" => $this->courses_model->records_total(),
			"recordsFiltered" => $this->courses_model->records_filtered(),
			"data" => $data,
		);

		echo json_encode($json);
	}

	public function ajax_list_member() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$this->load->model("team_model");
		$team = $this->team_model->get_datatable();

		$data = array();
		foreach ($team as $member) {

			$row = array();
			$row[] = $member->member_name;

			if ($member->member_photo) {
				$row[] = '<img src="'.base_url().$member->member_photo.'" style="max-height: 100px; max-width: 100px;">';
			} else {
				$row[] = "";
			}

			$row[] = '<div class="description">'.$member->member_description.'</div>';

			$row[] = '<div style="display: inline-block;">
						<button class="btn btn-primary btn-edit-member" 
							member_id="'.$member->member_id.'">
							<i class="fa fa-edit"></i>
						</button>
						<button class="btn btn-danger btn-del-member" 
							member_id="'.$member->member_id.'">
							<i class="fa fa-times"></i>
						</button>
					</div>';

			$data[] = $row;

		}

		$json = array(
			"draw" => $this->input->post("draw"),
			"recordsTotal" => $this->team_model->records_total(),
			"recordsFiltered" => $this->team_model->records_filtered(),
			"data" => $data,
		);

		echo json_encode($json);
	}

	public function ajax_list_user() {

		if (!$this->input->is_ajax_request()) {
			exit("Nenhum acesso de script direto permitido!");
		}

		$this->load->model("users_model");
		$users = $this->users_model->get_datatable();

		$data = array();
		foreach ($users as $user) {

			$row = array();
			$row[] = $user->user_login;
			$row[] = $user->user_full_name;
			$row[] = $user->user_email;

			$row[] = '<div style="display: inline-block;">
						<button class="btn btn-primary btn-edit-user" 
							user_id="'.$user->user_id.'">
							<i class="fa fa-edit"></i>
						</button>
						<button class="btn btn-danger btn-del-user" 
							user_id="'.$user->user_id.'">
							<i class="fa fa-times"></i>
						</button>
					</div>';

			$data[] = $row;

		}

		$json = array(
			"draw" => $this->input->post("draw"),
			"recordsTotal" => $this->users_model->records_total(),
			"recordsFiltered" => $this->users_model->records_filtered(),
			"data" => $data,
		);

		echo json_encode($json);
	}


}
