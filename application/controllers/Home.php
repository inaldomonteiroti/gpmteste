<?php
defined('BASEPATH') OR exit('No direct script access allowed'); //impedir  o  acesso ao arquivo direto

class Home extends CI_Controller {  // o controle é uma class que extende essa outra class CI_Controller é o primeiro controller chamado

	public function index()
	{

		$this->load->model("courses_model");
		$courses = $this->courses_model->show_courses();

		$this->load->model("team_model");
		$team = $this->team_model->show_team();

		$data = array(    // está passando dentro do array dois arquivos js para a view home.php
			"scripts" => array(  
				"owl.carousel.min.js", 
				"theme-scripts.js" 
			),
			"courses" => $courses,
			"team" => $team
		);
		$this->template->show("home.php", $data); // primeira view chamada
	}

}
