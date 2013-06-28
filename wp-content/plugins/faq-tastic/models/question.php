<?php

class Question
{
	var $id;
	var $question;
	var $answer;
	var $url;
	var $position;
	var $status;
	var $created;
	var $group_id;
	var $notified;
	
	function Question ($details = '')
	{
		if (is_array ($details))
		{
			foreach ($details AS $key => $value)
				$this->$key = $value;
		}
	}
	
	function get_count ($group)
	{
		global $wpdb;
		return $wpdb->get_var ("SELECT COUNT(id) FROM {$wpdb->prefix}faqtastic_questions WHERE group_id='$group' AND status='approved'");
	}
	
	function get_pending_count ($group)
	{
		global $wpdb;
		return $wpdb->get_var ("SELECT COUNT(id) FROM {$wpdb->prefix}faqtastic_questions WHERE group_id='$group' AND status='pending'");
	}
	
	function get ($id)
	{
		global $wpdb;
		
		$row = $wpdb->get_row ("SELECT * FROM {$wpdb->prefix}faqtastic_questions WHERE id='$id'", ARRAY_A);
		if ($row)
			return new Question ($row);
		return false;
	}
	
	function get_by_page ($id)
	{
		global $wpdb;
		
		$row = $wpdb->get_row ("SELECT * FROM {$wpdb->prefix}faqtastic_questions WHERE page_id='$id'", ARRAY_A);
		if ($row)
				return new Question ($row);
		return false;
	}
	
	function get_all_by_group ($id)
	{
		global $wpdb;
		
		$rows = $wpdb->get_results ("SELECT * FROM {$wpdb->prefix}faqtastic_questions WHERE group_id='$id'", ARRAY_A);
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[] = new Question ($row);
		}
		return $data;	
	}
	
	function get_sources (&$pager)
	{
		global $wpdb;
		
		$pager->set_total ($wpdb->get_var ("SELECT COUNT(*) FROM {$wpdb->prefix}faqtastic_questions".$pager->to_conditions ("author_email IS NOT NULL AND status='approved'")));
		$rows = $wpdb->get_results ("SELECT * FROM {$wpdb->prefix}faqtastic_questions".$pager->to_limits ("author_email IS NOT NULL AND status='approved'", array ('question', 'answer')), ARRAY_A);
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[] = new Question ($row);
		}
		return $data;
	}
	
	function get_by_group ($id, &$pager)
	{
		global $wpdb;
		
		$pager->set_total ($wpdb->get_var ("SELECT COUNT(*) FROM {$wpdb->prefix}faqtastic_questions".$pager->to_conditions ("group_id='$id' AND status='approved'")));
		$rows = $wpdb->get_results ("SELECT * FROM {$wpdb->prefix}faqtastic_questions".$pager->to_limits ("group_id='$id' AND status='approved'", array ('question', 'answer')), ARRAY_A);
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[] = new Question ($row);
		}
		return $data;
	}

	function get_approved ($id, $order)
	{
		global $wpdb;
		
		if ($order == 'default')
			$order = 'position ASC';
		else
			$order = '(positive + negative) DESC';
			
		$rows = $wpdb->get_results ("SELECT * FROM {$wpdb->prefix}faqtastic_questions WHERE group_id='$id' AND status='approved' ORDER BY $order", ARRAY_A);
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[] = new Question ($row);
		}
		return $data;
	}
	
	function get_pending ($id, &$pager)
	{
		global $wpdb;
		
		$group = '';
		if ($group > 0)
			$group = "group_id=$id AND";
		
		$pager->set_total ($wpdb->get_var ("SELECT COUNT(*) FROM {$wpdb->prefix}faqtastic_questions WHERE $group status='pending'"));
		$rows = $wpdb->get_results ("SELECT {$wpdb->prefix}faqtastic_questions.*,{$wpdb->prefix}faqtastic_groups.name FROM {$wpdb->prefix}faqtastic_questions,{$wpdb->prefix}faqtastic_groups WHERE {$wpdb->prefix}faqtastic_questions.group_id={$wpdb->prefix}faqtastic_groups.id AND $group status='pending'".$pager->to_limits (''), ARRAY_A);
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[$row['group_id']][] = new Question ($row);
		}
		return $data;
	}
	
	function get_all ()
	{
		global $wpdb;
		
		$rows = $wpdb->get_results ("SELECT * FROM {$wpdb->prefix}faqtastic_questions ORDER BY question", ARRAY_A);
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[] = new Question ($row);
		}
		return $data;
	}
	
	function create ($group, $data, $status = 'approved', $sendadmin = false)
	{
		global $wpdb;

		$data = stripslashes_deep ($data);
		
		if (strlen ($data['faq_question']) > 0)
		{
			$pageid   = 0;
			$question = $wpdb->escape ($data['faq_question']);
			$answer   = $wpdb->escape ($data['faq_answer']);
			
			$author_name = $author_website = $author_email = 'NULL';

			if (strlen ($data['faq_email']) > 0)
				$author_email = "'".$wpdb->escape ($data['faq_email'])."'";

			if (strlen ($data['faq_website']) > 0)
				$author_website = "'".$wpdb->escape ($data['faq_website'])."'";

			if (strlen ($data['faq_name']) > 0)
				$author_name = "'".$wpdb->escape ($data['faq_name'])."'";
			
			$follow = 0;
			if (strlen ($data['faq_follow']) > 0)
				$follow = intval ($data['faq_follow']);
						
			$pos = $wpdb->get_var ("SELECT COUNT(id) FROM {$wpdb->prefix}faqtastic_questions WHERE group_id={$group->id}");
			$success = $wpdb->query ("INSERT INTO {$wpdb->prefix}faqtastic_questions (group_id,created,author_email,author_name,author_website,author_website_follow,position,question,answer) VALUES ({$group->id},NOW(),$author_email,$author_name,$author_website,$follow,$pos,'$question','$answer')");
			$id = $wpdb->insert_id;
			if ($id > 0)
			{
				$question = Question::get ($id);
				$question->approve ($status, $data);

				if ($sendadmin)
				{
					global $faqtastic;
					$faqtastic->send_email (get_option ('admin_email'), __ ('FAQ-Tastic Question'), FAQDefaults::admin_email ($question));
				}
				
				return $id;
			}
		}
		
		return false;
	}
	
	function create_page ($parent, $title, $name)
	{
		// Create a WP page too
		global $user_ID, $wpdb;

		$page['post_type']    = 'page';
		$page['post_content'] = '[faq answer]';
		$page['post_parent']  = $parent;
		$page['post_author']  = $user_ID;
		$page['post_status']  = 'publish';
		$page['post_title']   = $wpdb->escape ($title);
		$page['post_name']    = $wpdb->escape ($name);

		$this->page_id = wp_insert_post ($page);
		$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET page_id='{$this->page_id}' WHERE id='{$this->id}'");
	}
	
	function approve ($status, $data)
	{
		$this->status = $status;
		
		global $wpdb;
		$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET status='$status' WHERE id='{$this->id}'");
		
		$group = QuestionGroup::get ($this->group_id);
		if ($group->page_id > 0 && $this->page_id == 0 && $this->status == 'approved')
			$this->create_page ($group->page_id, $data['page_title'] == '' ? $this->question : $data['page_title'], $data['page_slug'] == '' ? $this->question  : $data['page_slug']);
		
		if ($this->status == 'approved' && strlen ($this->author_email) > 0 && $this->notified == 0)
		{
			if ($group->page_id > 0)
				$mail = FAQDefaults::approved_paged ($this, $data['faq_message']);
			else
				$mail = FAQDefaults::approved_simple ($this, $data['faq_message']);

			global $faqtastic;
			$faqtastic->send_email ($this->author_email, __ ('FAQ Answer: ', 'faqtastic').substr ($this->question, 0, 40), $mail);
			$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET notified=1 WHERE id='{$this->id}'");
		}
		else if ($this->status == 'pending' && strlen ($this->author_email) > 0 && $this->notified == 0)
		{
			$mail = FAQDefaults::thanks ($this);

			global $faqtastic;
			$faqtastic->send_email ($this->author_email, __ ('Question submission: ', 'faqtastic').substr ($this->question, 0, 40), $mail, true);
		}
	}
	
	function update ($data)
	{
		global $wpdb;

		$data = stripslashes_deep ($data);
		if (strlen ($data['faq_question']) > 0)
		{
			$update_post = false;
			if ($this->page_id > 0)
				$update_post = true;
				
			$this->question       = $data['faq_question'];
			$this->answer         = $data['faq_answer'];
			$this->author_email   = $data['author_email'];
			$this->author_website = $data['author_website'];
			$this->author_name    = $data['author_name'];
			
			$this->author_website_follow = isset ($data['author_website_follow']) ? true : false;

			if ($data['save'])
			{
				$this->status = 'approved';
				$this->approve ('approved', $data);
			}
			else
				$this->status = 'pending';
				
			$this->positive = intval ($data['faq_positive']);
			$this->negative = intval ($data['faq_negative']);
			
			$original_group = $this->group_id;
			if (isset ($data['group_id']))
				$this->group_id = intval ($data['group_id']);
			
			$question = $wpdb->escape ($data['faq_question']);
			$answer   = $wpdb->escape ($data['faq_answer']);
			$email    = $wpdb->escape ($data['author_email']);
			$name     = $wpdb->escape ($data['author_name']);
			$website  = $wpdb->escape ($data['author_website']);
			
			$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET question='$question', answer='$answer', group_id='{$this->group_id}', author_email='$email', author_website='$website', author_name='$name', positive='{$this->positive}', negative='{$this->negative}', author_website_follow='{$this->author_website_follow}' WHERE id='{$this->id}'");

			// Update pages
			if ($original_group != $this->group_id)
			{
				$group = QuestionGroup::get ($this->group_id);
				$group->reset_hierarchy ($group->page_id);
			}

			// Sort out related questions
			$wpdb->query ("DELETE FROM {$wpdb->prefix}faqtastic_related WHERE question_id='{$this->id}'");

			if (isset ($data['related']) && !empty ($data['related']))
			{
				foreach ($data['related'] AS $id)
				{
					$follow = 0;
					if (in_array ($id, is_array ($data['related_follow']) ? $data['related_follow'] : array ($data['related_follow'])))
						$follow = 1;
					
					$wpdb->query ("INSERT INTO {$wpdb->prefix}faqtastic_related (question_id,related_id,follow) VALUES ({$this->id},".intval ($id).",$follow)");
				}
			}
			
			// Update the post
			if ($update_post)
			{
				$post = get_post ($this->page_id);

				$post->post_title = $wpdb->escape ($data['page_title']);
				$post->post_name  = $wpdb->escape ($data['page_url']);
				
				wp_insert_post ($post);
				
				if (class_exists ('MetaData'))
				{
					MetaData::add_tags        ($this->page_id, $data['page_tags']);
					MetaData::add_description ($this->page_id, $data['page_description']);
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	function delete ()
	{
		global $wpdb;
		$wpdb->query ("DELETE FROM {$wpdb->prefix}faqtastic_questions WHERE id='{$this->id}'");
		
		if ($this->page_id > 0)
			wp_delete_post ($this->page_id);
	}
	
	function reorder ($group_id, $offset = '', $order = '')
	{
		global $wpdb;
		if ($offset === '')
		{
			$ids = $wpdb->get_results ("SELECT id FROM {$wpdb->prefix}faqtastic_questions WHERE group_id=$group ORDER BY position");
			if (count ($ids) > 0)
			{
				foreach ($ids AS $pos => $id)
					$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET position=$pos WHERE id='{$id->id}'");
			}
		}
		else
		{
			foreach ($order AS $pos => $id)
				$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET position='".($offset + $pos)."' WHERE id='$id'");
		}
		
		// Reorder if page group
		if(FAQ_Features::is_pro ()){
		$group = QuestionGroup::get($group_id);
			if ($group)
			{
				$posts = array ();
				foreach ($order AS $id)
				{
					$row = $wpdb->get_row ("SELECT page_id FROM {$wpdb->prefix}faqtastic_questions WHERE id='$id'");
					$posts[] = $row->page_id;
				}
	
				$group->reorder ($offset, $posts);
			}
		}
	}
	
	function approval ()
	{
		return sprintf ("%+d", $this->positive - $this->negative);
	}
	
	function rate_positive ()
	{
		global $wpdb;
		$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET positive=positive+1 WHERE id='{$this->id}'");
	}
	
	function rate_negative ()
	{
		global $wpdb;
		$wpdb->query ("UPDATE {$wpdb->prefix}faqtastic_questions SET negative=negative+1 WHERE id='{$this->id}'");
	}
	
	function author_link ()
	{
		if ($this->author_website)
		{
			$name = $this->author_name;
			if ( empty( $name ) )
				$name = $this->author_website;
				
			if (substr ($this->author_website, 0, 4) != 'http')
				$website = 'http://'.$this->author_website;
			else
				$website = $this->author_website;
				
			$rel = $this->author_website_follow ? 'follow' : 'nofollow';
			return '<a href="'.$website.'" rel="'.$rel.'">'.htmlspecialchars ($name).'</a>';
		}
		
		return htmlspecialchars ($this->author_name);
	}
	
	function has_rated ()
	{
		if (isset ($_COOKIE['faqtastic_rating']))
		{
			$ratings = unserialize ($_COOKIE['faqtastic_rating']);
			if (isset ($ratings[$this->group_id]) && in_array ($this->id, $ratings[$this->group_id]))
				return true;
		}
		return false;
	}
	
	function get_related ()
	{
		global $wpdb;
		
		$rows = $wpdb->get_results ("SELECT * FROM {$wpdb->prefix}faqtastic_questions,{$wpdb->prefix}faqtastic_related WHERE {$wpdb->prefix}faqtastic_related.question_id={$this->id} AND {$wpdb->prefix}faqtastic_related.related_id={$wpdb->prefix}faqtastic_questions.id ORDER BY {$wpdb->prefix}faqtastic_questions.question ASC", ARRAY_A);
		$data = array ();
		if ($rows)
		{
			foreach ($rows AS $row)
				$data[] = new Question ($row);
		}
		return $data;
	}
	
	function link ()
	{
		$linkLocation = ( $this->page_id > 0 ) ? get_permalink ($this->page_id) : $linkLocation = '#faq_'.$this->id;

		if ($this->follow)
			return '<a href="'.$linkLocation.'">'.$this->question.'</a>';
		else
			return '<a rel="nofollow" href="'.$linkLocation.'">'.$this->question.'</a>';
	}
}

?>