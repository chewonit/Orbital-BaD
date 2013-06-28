<?php

class QuestionGroup
{
	var $id;
	var $name;
	var $url;
	var $page_id;
	
	var $post_title;   // Generated
	
	function QuestionGroup ($details = '')
	{
		if (is_array ($details))
		{
			foreach ($details AS $key => $value)
				$this->$key = $value;
		}
		
		$this->url = rtrim ($this->url, '/');
		if ($this->page_id > 0 && strlen ($this->post_title) == 0)
		{
			$item = get_post ($this->page_id);
			$this->post_title = $item->post_title;
		}
	}
	
	function get_groups (&$pager)
	{
		global $wpdb;
		
		$sql  = "SELECT SQL_CALC_FOUND_ROWS @groups.*,{$wpdb->posts}.post_title,COUNT(@questions.id) AS questions FROM @groups ";
		$sql .= "LEFT JOIN {$wpdb->posts} ON @groups.page_id={$wpdb->posts}.ID ";
		$sql .= "LEFT JOIN @questions ON @groups.id=@questions.group_id AND @questions.status='approved'";
		$sql .= $pager->to_limits ('', array ('name'), '', 'GROUP BY @groups.id');

		$sql = str_replace ('@', $wpdb->prefix.'faqtastic_', $sql);
		
		$rows = $wpdb->get_results ($sql, ARRAY_A);
		$pager->set_total ($wpdb->get_var ("SELECT FOUND_ROWS()"));
		
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[] = new QuestionGroup ($row);
		}
		return $data;
	}

	function get_all ()
	{
		global $wpdb;
		
		$rows = $wpdb->get_results ("SELECT * FROM {$wpdb->prefix}faqtastic_groups ORDER BY name", ARRAY_A);
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[] = new QuestionGroup ($row);
		}
		return $data;
	}
	
	function get ($id)
	{
		global $wpdb;
		
		$sql  = "SELECT @groups.*,{$wpdb->posts}.post_title FROM @groups ";
		$sql .= "LEFT JOIN {$wpdb->posts} ON @groups.page_id={$wpdb->posts}.ID ";
		$sql .= "WHERE @groups.id=$id ";
		$sql .= "GROUP BY @groups.id";

		$sql = str_replace ('@', $wpdb->prefix.'faqtastic_', $sql);
		$row = $wpdb->get_row ($sql, ARRAY_A);
		if ($row)
			return new QuestionGroup ($row);
		return false;
	}
	
	function get_by_name ($name)
	{
		global $wpdb;
		$name = $wpdb->escape ($name);
		$row = $wpdb->get_row ("SELECT * FROM {$wpdb->prefix}faqtastic_groups WHERE name LIKE '$name'", ARRAY_A);
		if ($row)
			return new QuestionGroup ($row);
		return false;
	}
	
	function get_by_url ($name)
	{
		global $wpdb;
		$name = $wpdb->escape ($name);
		$row = $wpdb->get_row ("SELECT * FROM {$wpdb->prefix}faqtastic_groups WHERE url LIKE '$name'", ARRAY_A);
		if ($row)
			return new QuestionGroup ($row);
		return false;
	}
	
	function delete ()
	{
		global $wpdb;
		
		$questions = Question::get_all_by_group ($this->id);
		if (count ($questions) > 0)
		{
			foreach ($questions AS $question)
				$question->delete ();
		}

		$wpdb->query ("DELETE FROM {$wpdb->prefix}faqtastic_groups WHERE id='{$this->id}'");
	}
	
	function create ($data)
	{
		global $wpdb;

		$data = stripslashes_deep ($data);
		
		if (strlen ($data['group_name']) > 0)
		{
			$name = $wpdb->escape (QuestionGroup::sanitize ($data['group_name']));
			
			$exists = $wpdb->get_var ("SELECT COUNT(id) FROM {$wpdb->prefix}faqtastic_groups WHERE name='$name'");
			if ($exists == 0)
			{
				$this->page_id = intval ($data['page_id']);
			
				$wpdb->query ("INSERT INTO {$wpdb->prefix}faqtastic_groups (name,page_id) VALUES ('$name', '{$this->page_id}')");
				return true;
			}
		}
		
		return false;
	}
	
	function update ($data)
	{
		global $wpdb;

		if (strlen ($data['group_name']) > 0)
		{
			// Meta data
			if (class_exists ('MetaData') && $this->page_id > 0)
			{
				MetaData::add_tags        ($this->page_id, $data['meta_keywords']);
				MetaData::add_description ($this->page_id, $data['meta_description']);
				MetaData::add_page_title  ($this->page_id, $data['page_title']);
				MetaData::add_nofollow    ($this->page_id, isset ($data['nofollow']) ? true : false);
				MetaData::add_noindex     ($this->page_id, isset ($data['noindex']) ? true : false);
			}
			
			// Normal data
			$this->name    = $data['group_name'];
			$this->ratings = isset ($data['group_ratings']) ? true : false;
			$this->ask     = $data['ask'];
			if (isset ($data['page_id']))
			{
				$this->reset_hierarchy (intval ($data['page_id']));
				$this->page_id = intval ($data['page_id']);
			}
			
			$name = $wpdb->escape ($data['group_name']);
			$ask  = $wpdb->escape ($data['ask']);
			
			$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_groups SET name='$name', ratings='{$this->ratings}', page_id='{$this->page_id}', ask='$ask' WHERE id='{$this->id}'");
			return true;
		}
		
		return false;
	}
	
	// Go through a page hierarchy and create/delete any pages that should/shouldnt be there
	function reset_hierarchy ($target_page)
	{
		global $wpdb;
		
		$questions = Question::get_all_by_group ($this->id);
		if (count ($questions) > 0)
		{
			// Delete pages if we were paged and now we're not
			if ($this->page_id > 0 && $target_page == 0)
			{
				// First we remove any original hierarchy - get all questions in this group
				foreach ($questions AS $question)
				{
					wp_delete_post ($question->page_id);   // For each one delete the attached page
					$question->page_id = 0;
				}
					
				$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET page_id=0 WHERE group_id={$this->id}");
			}

			//wp_delete_post ($target_page);   // For each one delete the attached page

			// Now we recreate the new hierarchy
			if ($target_page > 0)
			{
				foreach ($questions AS $question)
				{
					if ($question->page_id == 0)
						$question->create_page ($target_page, $question->question, $question->question);
					else
						$wpdb->query ("UPDATE {$wpdb->posts} SET post_parent=$target_page WHERE id={$question->page_id}");
				}
			}
		}
	}
	
	function sanitize ($url)
	{
		$url = trim ($url);
		$url = preg_replace ('@[/\\<>\[\]]@', '', $url);
		return $url;
	}
	
	function has_pages ()
	{
		if ($this->page_id > 0)
			return true;
		return false;
	}
	
	function ask_button ()
	{
		if ($this->ask)
			return $this->ask;
		return __ ('Ask question', 'faqtastic');
	}
	
	function reorder ($offset, $order)
	{
		if ($this->page_id > 0)
		{
			global $wpdb;
			
			if ($offset === '')
			{
				$ids = $wpdb->get_results ("SELECT id FROM {$wpdb->prefix}faqtastic_questions WHERE group_id=$group ORDER BY position");
				if (count ($ids) > 0)
				{
					foreach ($ids AS $pos => $id)
					{
						$wpdb->query ("UPDATE {$wpdb->prefix}posts SET menu_order=$pos WHERE id='{$id->id}'");
					}
				}
			}
			else
			{
				foreach ($order AS $pos => $id)
				{
					$wpdb->query ("UPDATE {$wpdb->prefix}posts SET menu_order='".($offset + $pos)."' WHERE id='$id'");
				}
			}
		}
	}
}
?>