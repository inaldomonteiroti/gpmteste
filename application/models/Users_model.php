<?php

class Users_model extends CI_Model { 

	public function __construct() { //função construtora
		parent::__construct(); // Especifica para o codigniter que esta fazendo o contrutor de User_model
		$this->load->database(); // funcao que vem de CI_model // carregar a base para fazer as operações
	}

	public function get_user_data($user_login) { //user_login é passado no controller
		//verifica se tem algum ususario e tras os dados
		$this->db 
			->select("user_id, password_hash, user_full_name, user_email") // pegar todos os campos
			->from("users") // da tabela users
			->where("user_login", $user_login); // onde userlogin igual a $userlogin

		$result = $this->db->get(); // a query roda agora e coloca no result no get

		if ($result->num_rows() > 0) { // se usuario encontrado retorna a linha retorna alguem
			return $result->row();
		} else {
			return NULL;  // senao retorna nulo
		}
	}

	public function get_data($id, $select = NULL) { //passa o id , select com os campos
		if (!empty($select)) {
			$this->db->select($select);
		}
		$this->db->from("users");  // buscando na tabela users
		$this->db->where("user_id", $id); // informações do usuario passado
		return $this->db->get(); // executa a query retorna o objeto que vai ter as propriedades
	}

	public function insert($data) { // precisa de $data onde data tem uma chave e o valor
		$this->db->insert("users", $data);
	}

	public function update($id, $data) { // atualização
		$this->db->where("user_id", $id);
		$this->db->update("users", $data);
	}

	public function delete($id) { // delete
		$this->db->where("user_id", $id);
		$this->db->delete("users");
	}

	public function is_duplicated($field, $value, $id = NULL) { // importante para verificar campos duplicados
		if (!empty($id)) {
			$this->db->where("user_id <>", $id);
		}
		$this->db->from("users");
		$this->db->where($field, $value);
		return $this->db->get()->num_rows() > 0;
	}

	var $column_search = array("user_login", "user_full_name", "user_email");
	var $column_order = array("user_login", "user_full_name", "user_email");

	private function _get_datatable() {

		$search = NULL;
		if ($this->input->post("search")) {
			$search = $this->input->post("search")["value"];
		}
		$order_column = NULL;
		$order_dir = NULL;
		$order = $this->input->post("order");
		if (isset($order)) {
			$order_column = $order[0]["column"];
			$order_dir = $order[0]["dir"];
		}

		$this->db->from("users");
		if (isset($search)) {
			$first = TRUE;
			foreach ($this->column_search as $field) {
				if ($first) {
					$this->db->group_start();
					$this->db->like($field, $search);
					$first = FALSE;
				} else {
					$this->db->or_like($field, $search);
				}
			}
			if (!$first) {
				$this->db->group_end();
			}
		}

		if (isset($order)) {
			$this->db->order_by($this->column_order[$order_column], $order_dir);
		}
	}

	public function get_datatable() {

		$length = $this->input->post("length");
		$start = $this->input->post("start");
		$this->_get_datatable();
		if (isset($length) && $length != -1) {
			$this->db->limit($length, $start);
		}
		return $this->db->get()->result();
	}

	public function records_filtered() {

		$this->_get_datatable();
		return $this->db->get()->num_rows();

	}

	public function records_total() {

		$this->db->from("users");
		return $this->db->count_all_results();

	}
}
