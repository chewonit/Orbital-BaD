<?php
/*
============================================================================================================
This software is provided "as is" and any express or implied warranties, including, but not limited to, the
implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
consequential damages (including, but not limited to, procurement of substitute goods or services; loss of
use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.

This software is provided free-to-use, but is not free software.  The copyright and ownership remains entirely
with the author.  Please distribute and use as necessary, in a personal or commercial environment, but it cannot
be sold or re-used without express consent from the author.
============================================================================================================
*/

include ('../../../wp-config.php');
if (!function_exists ('write_post') && file_exists (ABSPATH.'wp-admin/admin-functions.php'))
	include (ABSPATH.'wp-admin/admin-functions.php');
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

/**
 * Handles all AJAX functionality.  This file is called directly by the AJAX command and whatever is output will
 * be displayed by the calling JavaScript function
 *
 * @package FAQ-Tastic
 **/

class FAQTastic_AJAX extends FAQTastic_Plugin
{
	/**
	 * Constructor validates that the user is allowed to use AJAX (editors) and then calls the appropriate function
	 *
	 * @return void
	 **/
	
	function FAQTastic_AJAX ($id, $command)
	{
		global $faqtastic;
		
		if ($faqtastic->has_access () === false)
			die ('<p style="color: red">You are not allowed access to this resource</p>');
		
		$_POST = stripslashes_deep ($_POST);
		
		$this->features = new FAQ_Features ($this);
		$this->register_plugin ('faqtastic', __FILE__);
		
		if (method_exists ($this, $command))
			$this->$command ($id);
		else
			die ('<p style="color: red">That function is not defined</p>');
	}


	/**
	 * Deletes a question group
	 *
	 * @param int $id Question group ID
	 * @return void
	 **/
	
	function delete_group ($id)
	{
		$group = QuestionGroup::get ($id);
		$group->delete ();
	}
	
	
	/**
	 * Displays edit details for a group
	 *
	 * @param int $id Question group ID
	 * @return void
	 **/
	
	function edit_group ($id)
	{
		$group     = QuestionGroup::get ($id);
		
		$this->features->render_admin ('group_edit', array ('group' => $group));
	}
	
	
	/**
	 * Cancels a question group edit
	 *
	 * @param int $id Question group ID
	 * @return void
	 **/
	
	function show_group ($id)
	{
		$group = QuestionGroup::get ($id);
		$group->questions = Question::get_count ($id);
		$this->render_admin ('group_item', array ('group' => $group));
	}
	
	
	/**
	 * Saves new details for a question group
	 *
	 * @param int $id Question group ID
	 * @return void
	 **/
	
	function save_group ($id)
	{
		$group = QuestionGroup::get ($id);
		$group->update ($_POST);
	}



	/**
	 * Deletes a question
	 *
	 * @param int $id Question ID
	 * @return void
	 **/

	function delete_question ($id)
	{
		$question = Question::get ($id);
		$question->delete ();
		
		Question::reorder ($question->group_id);
	}
	
	function delete_questions ($id)
	{
		$items = array_filter (explode (',', $_POST['items']));
		if (count ($items) > 0)
		{
			foreach ($items AS $id)
			{
				$question = Question::get ($id);
				$question->delete ();
				Question::reorder ($question->group_id);
			}
		}
	}
	
	/**
	 * Marks a question as being rejected
	 *
	 * @param int $id Question ID
	 * @return void
	 **/
	
	function reject_question ($id)
	{
		$question = Question::get ($id);
		$mail     = FAQDefaults::rejected ($question, $_POST['faq_message']);

		$question->delete ();
		
		Question::reorder ($question->group_id);
		
		global $faqtastic;
		$faqtastic->send_email ($question->author_email, __ ('FAQ Answer: ', 'faqtastic').substr ($question->question, 0, 40), $mail);
	}
	
	
	/**
	 * Displays edit details for a question
	 *
	 * @param int $id Question ID
	 * @return void
	 **/
	
	function edit_question ($id)
	{
		$question = Question::get ($id);
		$group    = QuestionGroup::get ($question->group_id);
		$related  = $question->get_related ();
		$groups   = QuestionGroup::get_all ();

		$newgroups = array ();
		foreach ($groups AS $pos => $tmp)
			$newgroups[$tmp->id] = $tmp->name;

		$groups = $newgroups;
		
		$allquestions = Question::get_all ();
		if (count ($allquestions) > 0)
		{
			$newquestions = array ();
			foreach ($allquestions AS $pos => $quest)
			{
				if ( ($quest->id != $id) && ($quest->page_id > 0)) // 2009-06-01 restricted to Paged Questions only. ZB
					$newquestions[$quest->id] = substr ($quest->question, 0, 40);
			}
			
			$allquestions = $newquestions;
		}
		
		if ($question->page_id > 0)
		{
			$page = get_post ($question->page_id);
			
			if (class_exists ('MetaData'))
			{
				$page->page_description = MetaData::get_description ($question->page_id);
				$page->page_tags        = MetaData::get_tags ($question->page_id);
			}
			
			$this->features->render_admin ('question_edit', array (
					'question'     => $question,
					'group'        => $group,
					'page'         => $page,
					'related'      => $related,
					'groups'       => $groups,
					'allquestions' => $allquestions
				));
		}
		else
			$this->features->render_admin ('question_edit', array ('question' => $question, 'group' => $group, 'groups' => $groups, 'related' => $related, 'allquestions' => $allquestions));
	}
	
	
	/**
	 * Cancels editing a question
	 *
	 * @param int $id Question ID
	 * @return void
	 **/
	
	function cancel_question ($id)
	{
		$question = Question::get ($id);
		
		$this->render_admin ('question_item', array ('question' => $question));
	}
	
	
	/**
	 * Save updated details for a question
	 *
	 * @param int $id Question ID
	 * @return void
	 **/
	
	function save_question ($id)
	{
		$question = Question::get ($id);
		
		$delete = false;
		if (isset ($_POST['group_id']) && $question->group_id != $_POST['group_id'])
			$delete = true;
			
		$question->update ($_POST);
		
		if ($delete)
			$this->render_admin ('question_remove', array ('question' => $question));
		else
		{
			$this->render_admin ('question_item', array ('question' => $question));
			$this->render_admin ('question_item_colour', array ('id' => $question->id, 'status' => $question->status));
		}
	}
	
	
	/**
	 * Updates question order.  The order is contained in $_POST['questions] and is a list of question ID numbers.  $_GET['offset] is used
	 * as the base offset into the question IDs
	 *
	 * @param int $id Question ID
	 * @return void
	 **/
	
	function save_order ($id)
	{
		Question::reorder ($id, intval ($_GET['offset']), $_POST['questions']);
	}
	
	function base ()
	{
		$parts = explode( '?', basename( $_SERVER['REQUEST_URI'] ) );
		return str_replace('ajax.php', 'tools.php', $parts[0]);
	}
}


function my_parent_dropdown( $default = 0, $parent = 0, $level = 0 ) {
	global $wpdb, $post_ID;
	$items = $wpdb->get_results( "SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = $parent AND post_type = 'page' ORDER BY menu_order" );

	if ( $items ) {
		foreach ( $items as $item ) {
			// A page cannot be its own parent.
			if (!empty ( $post_ID ) ) {
				if ( $item->ID == $post_ID ) {
					continue;
				}
			}
			$pad = str_repeat( '&nbsp;', $level * 3 );
			if ( $item->ID == $default)
				$current = ' selected="selected"';
			else
				$current = '';

			echo "\n\t<option value='$item->ID'$current>$pad $item->post_title</option>";
			my_parent_dropdown( $default, $item->ID, $level +1 );
		}
	} else {
		return false;
	}
}

$id  = $_GET['id'];
$cmd = $_GET['cmd'];

$obj = new FAQTastic_AJAX ($id, $cmd);

?>