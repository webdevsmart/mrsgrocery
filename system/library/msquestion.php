<?php
class MsQuestion extends Model {
	const STATUS_DISABLED = 0;
	const STATUS_ENABLED = 1;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->language('multiseller/multiseller');
	}

	public function getQuestions($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS
					msq.*,
					pd.name as `product_name`,
					IFNULL(c.customer_name, '" . $this->language->get('ms_questions_customer_deleted') . "') as `author_name`
				FROM `" . DB_PREFIX . "ms_question` msq";

		if(isset($data['seller_id'])) {
			$sql .= "
				LEFT JOIN
					(SELECT product_id, seller_id FROM " . DB_PREFIX . "ms_product WHERE product_status = 1 AND product_approved = 1) msp
					ON (msp.product_id = msq.product_id)";
		}

		$sql .= " LEFT JOIN
					(SELECT customer_id, CONCAT(firstname, ' ', lastname) as customer_name FROM `" . DB_PREFIX . "customer`) c
					ON c.customer_id = msq.author_id
				 LEFT JOIN
					(SELECT product_id, name FROM " . DB_PREFIX . "product_description WHERE language_id = " . $this->config->get('config_language_id') . ") pd
					ON (pd.product_id = msq.product_id)
				WHERE 1 = 1"
			. (isset($data['question_id']) ? " AND msq.question_id =  " . (int)$data['question_id'] : '')
			. (isset($data['author_id']) ? " AND msq.author_id =  " . (int)$data['author_id'] : '')
			. (isset($data['seller_id']) ? " AND msp.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['product_id']) ? " AND msq.product_id =  " . (int)$data['product_id'] : '')
			. (isset($data['date']) ? " AND msq.date_created =  " . $this->db->escape($data['date']) : '')

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) {
			$res->rows[0]['total_rows'] = $total->row['total'];

			foreach ($res->rows as &$row) {
				$row['answers'] = $this->getAnswers(array('question_id' => $row['question_id']));
			}
		}

		return $res->num_rows && isset($data['single']) ? $res->rows[0] : $res->rows;
	}

	public function addQuestion($data = array()) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_question`
			 SET `author_id` = " . (int)$data['author_id'] . ",
				 `product_id` = " . (int)$data['product_id'] . ",
				 `text` = '" . $this->db->escape($data['question']) . "',
				 `date_created` = NOW()
		");
	}

	public function deleteQuestion($question_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_question` WHERE question_id = " . (int)$question_id);
	}

	public function getAnswers($data = array()) {
		// todo: CHECK INFO FOR DELETED CUSTOMER
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					msa.*,
					IFNULL(c.customer_name, '" . $this->language->get('ms_questions_customer_deleted') . "') as `author_name`
				FROM `" . DB_PREFIX . "ms_answer` msa
				LEFT JOIN (SELECT customer_id, CONCAT(firstname, ' ', lastname) as customer_name FROM `" . DB_PREFIX . "customer`) c
					ON c.customer_id = msa.author_id
				WHERE question_id = " . (int)$data['question_id'];

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) {
			$res->rows[0]['total_rows'] = $total->row['total'];
		}

		return $res->rows;
	}
	
	public function addAnswer($data = array()) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_answer`
			SET `author_id` = " . (int)$data['author_id'] . ",
				`question_id` = " . (int)$data['question_id'] . ",
				`text` = '" . $this->db->escape($data['text']) . "',
				`date_created` = NOW(),
				`rating` = 0"
		);
	}

	public function deleteAnswer($answer_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_answer` WHERE answer_id = " . (int)$answer_id);
	}
}
?>