<?php
class MsReview extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;

	public function getReviews($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT 
				SQL_CALC_FOUND_ROWS
				msr.*,
				mss.nickname,
				pd.name as `product_name`
				FROM `" . DB_PREFIX . "ms_review` msr";

		$sql .= "
				LEFT JOIN " . DB_PREFIX . "ms_seller mss USING(seller_id)";

		$sql .= " LEFT JOIN
					(SELECT product_id, name FROM " . DB_PREFIX . "product_description WHERE language_id = " . $this->config->get('config_language_id') . ") pd
					ON (pd.product_id = msr.product_id)
				WHERE 1 = 1 "
			. (isset($data['review_id']) ? " AND msr.review_id =  " .  (int)$data['review_id'] : '')
			. (isset($data['seller_id']) ? " AND msr.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['product_id']) ? " AND msr.product_id =  " .  (int)$data['product_id'] : '')
			. (isset($data['order_product_id']) ? " AND msr.order_product_id =  " .  (int)$data['order_product_id'] : '')
			. (isset($data['order_id']) ? " AND msr.order_id =  " .  (int)$data['order_id'] : '')
			. (isset($data['author_id']) ? " AND msr.author_id =  " .  (int)$data['author_id'] : '')
			. (isset($data['rating']) ? " AND msr.rating =  " .  (int)$data['rating'] : '')
			. (isset($data['date']) ? " AND msr.date_created =  " .  $this->db->escape($data['date']) : '')

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) {
			$res->rows[0]['total_rows'] = $total->row['total'];

			foreach ($res->rows as &$row) {
				$row['attachments'] = $this->getReviewAttachments(array('review_id' => $row['review_id']));
			}
		}

		return $res->num_rows && isset($data['single']) ? $res->rows[0] : $res->rows;
	}

	public function createOrUpdateReview($data = array()) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_review
			SET "
				. (isset($data['review_id']) ? " review_id = " . (int)$data['review_id'] . "," : "")
				. (isset($data['seller_id']) ? " seller_id = " . (int)$data['seller_id'] . "," : "")
				. " author_id = " . (int)$data['author_id'] . ",
				product_id = " . (int)$data['product_id'] . ",
				order_product_id = " . ($data['order_product_id'] ? (int)$data['order_product_id'] : NULL) . ",
				order_id = " . ($data['order_id'] ? (int)$data['order_id'] : NULL) . ",
				rating = " . (int)$data['rating'] . ",
				title = '" . $this->db->escape($data['title']) . "',
				comment = '" . $this->db->escape($data['comment']) . "',
				date_created = NOW(),
				status = " . self::STATUS_ACTIVE . "
			ON DUPLICATE KEY UPDATE
				review_id = review_id,
				rating = " . (int)$data['rating'] . ",
				title = '" . $this->db->escape($data['title']) . "',
				comment = '" . $this->db->escape($data['comment']) . "',
				date_updated = NOW()
		");

		$review_id = isset($data['review_id']) && $data['review_id'] ? $data['review_id'] : $this->db->getLastId();

		if(isset($data['attachments'])) $this->createReviewAttachment($review_id, $data['attachments']);
	}

	public function deleteReview($review_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_review WHERE review_id=" . (int)$review_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_review_attachment WHERE review_id=" . (int)$review_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_review_comment WHERE review_id=" . (int)$review_id);
	}

	public function getFeedbackHistory($data = array()) {
		$stats = array();

		$feedbacks = array(
			'positive',
			'neutral',
			'negative'
		);

		$time_periods = array(
			'one_month',
			'three_months',
			'six_months',
			'twelve_months'
		);

		foreach ($feedbacks as $feedback) {
			foreach ($time_periods as $time_period) {
				$sql = "
					SELECT
						SQL_CALC_FOUND_ROWS
						1
					FROM `" . DB_PREFIX . "ms_review` msr
					LEFT JOIN (SELECT product_id, seller_id FROM `" . DB_PREFIX . "ms_product`) as msp USING(product_id)
					WHERE msp.seller_id = '" . (int)$data['seller_id'] . "'
						";

				switch ($feedback) {
					case 'positive':
						$sql .= "AND msr.rating IN (4,5)";
						break;

					case 'neutral':
						$sql .= "AND msr.rating = 3";
						break;

					case 'negative':
						$sql .= "AND msr.rating IN (1,2)";
						break;

					default:
						break;
				}

				switch ($time_period) {
					case 'one_month':
						$sql .= " AND DATEDIFF(DATE(date_created), DATE(NOW())) <= 0
								  AND DATEDIFF(DATE(NOW() - INTERVAL 1 MONTH), DATE(date_created)) < 0";
						break;

					case 'three_months':
						$sql .= " AND DATEDIFF(DATE(date_created), DATE(NOW() - INTERVAL 1 MONTH)) <= 0
								  AND DATEDIFF(DATE(NOW() - INTERVAL 3 MONTH), DATE(date_created)) < 0";
						break;

					case 'six_months':
						$sql .= " AND DATEDIFF(DATE(date_created), DATE(NOW() - INTERVAL 3 MONTH)) <= 0
								  AND DATEDIFF(DATE(NOW() - INTERVAL 6 MONTH), DATE(date_created)) < 0";
						break;

					case 'twelve_months':
						$sql .= " AND DATEDIFF(DATE(date_created), DATE(NOW() - INTERVAL 6 MONTH)) <= 0
								  AND DATEDIFF(DATE(NOW() - INTERVAL 12 MONTH), DATE(date_created)) < 0";
						break;

					default:
						break;
				}

				$sql .=	" ORDER BY msr.rating";

				$this->db->query($sql);

				$total_query = $this->db->query("SELECT FOUND_ROWS() as total");
				$stats[$feedback][$time_period] = $total_query->row['total'];
			}
		}

		return $stats;
	}

	public function getReviewAttachments($data = array()) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_review_attachment
				WHERE 1 = 1"
				. (isset($data['review_attachment_id']) ? " AND review_attachment_id = '" . (int)$data['review_attachment_id'] . "'" : "")
				. (isset($data['review_id']) ? " AND review_id = '" . (int)$data['review_id'] . "'" : "");

		$res = $this->db->query($sql);

		$images = array();
		foreach ($res->rows as $row) {
			$images[$row['review_attachment_id']] = $row;
		}

		return $images;
	}

	public function getReviewComments($review_id, $data = array()) {
		$res = $this->db->query("SELECT
				SQL_CALC_FOUND_ROWS
				*
			FROM `" . DB_PREFIX . "ms_review_comment` msrc
			WHERE review_id = '" . (int)$review_id . "'"
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) {
			$res->rows[0]['total_rows'] = $total->row['total'];
		}

		return $res->rows;
	}

	public function addReviewComment($data = array()) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_review_comment`
			SET review_id = '" . (int)$data['review_id'] . "',
				author_id = '" . (int)$data['author_id'] . "',
				text = '" . $this->db->escape($data['text']) . "',
				rating = 0,
				date_created = NOW()"
		);

		return $this->db->getLastId();
	}

	public function createReviewAttachment($review_id, $images = array()) {
		foreach ($images as $key => $img) {
			$newImagePath = $this->MsLoader->MsFile->moveImage($img);
			$this->db->query("INSERT INTO " . DB_PREFIX . "ms_review_attachment SET review_id = '" . (int)$review_id . "', attachment = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "'");
		}
	}

	public function deleteReviewAttachment($attachment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_review_attachment WHERE review_attachment_id = " . (int)$attachment_id);
	}

	public function deleteReviewComment($comment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_review_comment WHERE comment_id = " . (int)$comment_id);
	}

	public function setHelpful($review_id, $isHelpful) {
		if($isHelpful) {
			$this->db->query("UPDATE " . DB_PREFIX . "ms_review SET helpful=helpful+1 WHERE review_id = " . (int)$review_id);
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "ms_review SET unhelpful=unhelpful+1 WHERE review_id = " . (int)$review_id);
		}
	}
}
?>