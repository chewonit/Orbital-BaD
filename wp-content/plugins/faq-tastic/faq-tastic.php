<?php
/*
Plugin Name: FAQ-Tastic
Plugin URI: http://faq-tastic.com/
Description: <a href="tools.php?page=faq-tastic.php&amp;sub=questions">Create and manage FAQs</a>.  A short overview is given on the <a href="tools.php?page=faq-tastic.php&amp;sub=help">help</a> page.
Author: John Godley
Version: 1.0.16
Author URI: http://faq-tastic.com/
============================================================================================================
0.9.5 - Fix nesting bugs
1.0   - Add Pro features
1.0.2 - Admin email
1.0.4 - Kitten fix, database update
1.0.5 - Database upgrade routine
1.0.6 - Import/export routine
1.0.7 - Fix #149
1.0.8 - Fixes for WP 2.5
1.0.9 - Fixes for WP 2.7
1.0.10 - Fix #395, #399, #412, #421
1.0.11 - Localisations, ordering, HTTPS
1.0.12 - Admin email fix
1.0.13 - Allow Search Unleashed to index FT pages, related question fix, removed paged parent
1.0.14 - FT Pro amendments. Changes to text stings. New features added. Updated *.po file. Fixed some minor issues.
1.0.15 - WP 2.8 fixes, related items deletion
1.0.16 - More localisations
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

include (dirname (__FILE__).'/plugin.php');
include (dirname (__FILE__).'/models/question.php');
include (dirname (__FILE__).'/models/group.php');
include (dirname (__FILE__).'/models/pager.php');
include (dirname (__FILE__).'/models/defaults.php');
include (dirname (__FILE__).'/models/formatter.php');

if (file_exists (dirname (__FILE__).'/pro/pro.php'))
	include (dirname (__FILE__).'/pro/pro.php');
else
	include (dirname (__FILE__).'/models/lite.php');

/**
 * The FAQ-Tastic plugin class
 *
 * @package FAQ-Tastic
 **/

class FAQTastic extends FAQTastic_Plugin
{
	/**
	 * Keep track of how many replacements have been made
	 *
	 * @var int
	 **/
	
	var $count = 0;
	var $wp_head = false;
	
	/**
	 * Constructor sets up all filters and actions
	 *
	 * @return void
	 **/
	
	function FAQTastic ()
	{
		$this->register_plugin ('faqtastic', __FILE__);
		$this->features = new FAQ_Features ($this);
		
		if (is_admin ())
		{
			if (strpos ($_SERVER['REQUEST_URI'], 'faq-tastic.php') !== false)
				$this->add_action ('admin_head');
				
			$this->add_action ('admin_menu');
			$this->add_action ('delete_post');
			$this->add_action ('save_post');

			$this->register_activation (__FILE__);
		}

		$this->add_filter ('wp_head');
		$this->add_filter ('the_content');
		$this->add_filter ('the_excerpt', 'the_content');
		
		$this->add_action ('init');
	}

	
	function is_25 ()
	{
		global $wp_version;
		if (version_compare ('2.5', $wp_version) <= 0)
			return true;
		return false;
	}
	
	/**
	 * Output appropriate HEAD data into the wp_head filter (i.e. setup CSS and JS)
	 *
	 * @return void
	 **/
	
	function wp_head ()
	{
		global $posts;
		
		$this->wp_head = true;
		if (count ($posts) > 0)
		{
			foreach ($posts AS $pos => $post)
				$posts[$pos]->post_content = $this->pre_replace ($posts[$pos]->post_content);
		}

		if ($this->count > 0)
		{
			$css = $this->url ();
			if (file_exists (TEMPLATEPATH.'/view/faqtastic/style.css'))
				$css = TEMPLATEPATH;
			$this->render ('head', array ('css' => $css));
		}
	}

	function pre_replace ($content)
	{
		return preg_replace_callback ('@(?:<p>)*\s*\[faq\s+(\w+)\s*(.*?)\]\s*(?:</p>)*@', array (&$this, 'replace'), $content);
	}

	/**
	 * Called when the plugin is activated - create all database tables and redirect the user to the help page.
   *
	 * NOTE: This function does not return and the program exits (otherwise the redirect would not work)
	 *
	 * @return void
	 **/
	
	function activate ()
	{
		$this->upgrade ();
	}
	
	
	/**
	 * Creates a menu entry
	 *
	 * @return void
	 **/
	
	function admin_menu ()
	{
		if ($this->has_access ())
		{
			if ($this->is_pro ())
				add_management_page (__ ("FAQ-Tastic Pro", 'faqtastic'), __ ("FAQ-Tastic Pro", 'faqtastic'), 'edit_posts', basename (__FILE__), array (&$this, "admin_screen"));
			else
				add_management_page (__ ("FAQ-Tastic", 'faqtastic'), __ ("FAQ-Tastic", 'faqtastic'), 'edit_posts', basename (__FILE__), array (&$this, "admin_screen"));
		}
	}
	
	function has_access ()
	{
		$options = $this->get_options ();
		
		global $wp_roles;
		$caps = $wp_roles->get_role ($options['access_level']);

		// Get highest level of the role
		for ($x = 10; $x >= 0; $x--)
		{
			if (isset ($caps->capabilities['level_'.$x]))
				break;
		}

		global $current_user;
		
		// Can this user access that level
		if (isset ($current_user->allcaps['level_'.$x]))
			return true;
		return false;
	}
	
	function init ()
	{
		if (is_admin ())
		{
			session_cache_limiter ('nocache');
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		}
	}
	
	/**
	 * Insert CSS and JS into administration page
	 *
	 * @return void
	 **/
	
	function admin_head ()
	{
		$this->upgrade ();
		$this->render_admin ('head');
	}


	function upgrade ()
	{
		$version = get_option ('faq_version');
		if ($version != 4 || isset ($_GET['special']))
		{
			include (dirname (__FILE__).'/models/upgrade.php');
			
			if (isset ($_GET['special']))
				$version = intval ($_GET['special']);
				
			$upgrade = new FT_Upgrade ();
			$upgrade->run ($version, 4);
		}
	}
	
	function submenu ($inwrap = false)
	{
		// Decide what to do
		$sub = isset ($_GET['sub']) ? $_GET['sub'] : '';
	  $url = explode ('&', $_SERVER['REQUEST_URI']);
	  $url = $url[0];
	
		if (!$this->is_25 () && $inwrap == false)
			$this->render_admin ('submenu', array ('url' => $url, 'sub' => $sub, 'class' => 'id="subsubmenu"'));
		else if ($this->is_25 () && $inwrap == true)
			$this->render_admin ('submenu', array ('url' => $url, 'sub' => $sub, 'class' => 'class="subsubsub"', 'trail' => ' | '));
			
		return $sub;
	}
	
	
	/**
	 * Decide which administration page to display
	 *
	 * @return void
	 **/
	
	function admin_screen ()
	{
		if ($this->has_access ())
		{
			$sub = $this->submenu ();
			
			// Decide what to do, based upon URL
			if ($sub == '')
				$this->admin_pending ();
			else if ($sub == 'questions')
				$this->admin_questions ();
			else if ($sub == 'options')
				$this->admin_options ();
			else if ($sub == 'help')
				$this->admin_help ();
			else if ($sub == 'resources')
				$this->admin_resources ();
			else if ($sub == 'sources')
				$this->admin_sources ();
		}
	}
	
	
	function get_options ()
	{
		$options = get_option ('faq_options');
		if ($options === false)
			$options = array ();
			
		if (!isset ($options['show_author_details']))
			$options['show_author_details'] = false;

		if (!isset ($options['access_level']))
			$options['access_level'] = 'administrator';

		if (!isset ($options['question_order']))
			$options['question_order'] = 'default';

		if (!isset ($options['textile']))
			$options['textile'] = false;
			
		if (!isset ($options['dontcare']))
			$options['dontcare'] = false;
			
		if (!isset ($options['admin_email']))
			$options['admin_email'] = get_option ('admin_email');
			
		if (!isset ($options['admin_email_name']))
			$options['admin_email_name'] = 'FAQ-Tastic';

		return $options;
	}
	
	/**
	 * Display the options page
	 *
	 * @return void
	 **/
	
	function admin_options ()
	{
		if (isset ($_POST['save']))
		{
			$_POST = stripslashes_deep ($_POST);
			
			// Save the options
			update_option ('faq_approved_simple', stripslashes ($_POST['faq_approved_simple']));
			update_option ('faq_approved_paged',  stripslashes ($_POST['faq_approved_paged']));
			update_option ('faq_thanks',          stripslashes ($_POST['faq_thanks']));
			update_option ('faq_failed',          stripslashes ($_POST['faq_failed']));
			update_option ('faq_rejected',        stripslashes ($_POST['faq_rejected']));
			update_option ('faq_rating',          stripslashes ($_POST['faq_rating']));
			
			$options = $this->get_options ();
			$options['show_author_details'] = isset ($_POST['show_author_details']) ? true : false;
			$options['textile']             = isset ($_POST['textile']) ? true : false;
			$options['dontcare']            = isset ($_POST['dontcare']) ? true : false;
			$options['access_level']        = $_POST['access_level'];
			$options['adsense']             = $_POST['adsense'];
			$options['adsense_position']    = $_POST['adsense_position'];
			$options['question_order']      = $_POST['question_order'];
			$options['admin_email']         = $_POST['admin_email'];
			$options['admin_email_name']    = $_POST['admin_email_name'];
			
			update_option ('faq_options', $options);
			$this->render_message (__ ('Your options have been saved', 'faqtastic'));
		}
		else if (isset ($_POST['remove']))
		{
			include (dirname (__FILE__).'/models/upgrade.php');
			
			$upgrade = new FT_Upgrade ();
			$upgrade->remove (__FILE__);
			
			$this->render_message (__ ('FAQ-Tastic has been removed', 'faqtastic'));
		}
		
		$this->render_admin ('options', array
			(
				'approved_simple' => FAQDefaults::approved_simple (),
				'approved_paged'  => FAQDefaults::approved_paged (),
				'thankyou'        => FAQDefaults::thanks (),
				'failed'          => FAQDefaults::failed (),
				'rating'          => FAQDefaults::rating (),
				'rejected'        => FAQDefaults::rejected (),
				'options'         => $this->get_options ()
			));
	}
	
	
	/**
	 * Display the help page
	 *
	 * @return void
	 **/
	
	function admin_help ()
	{
		$this->render_admin ('help');
	}
	
	/**
	 * Display the videos page
	 *
	 * @return void
	 **/
	
	function admin_resources ()
	{
		if( $this->is_pro () ){
			include (dirname (__FILE__).'/pro/view/admin/resources.php');
		} else {
			$this->render_admin ('resources');
		}
	}
	
	
	/**
	 * Display the pending page
	 *
	 * @return void
	 **/
	
	function admin_pending ()
	{
		$pager     = new FT_Pager ($_GET, $_SERVER['REQUEST_URI'], 'group_id,position', 'ASC');
		$questions = Question::get_pending (0, $pager);
		$options   = $this->get_options ();
		
		$this->render_admin ('pending', array ('questions' => $questions, 'group' => $group, 'pager' => $pager));
		
		if (!$this->is_pro () && $options['dontcare'] === false)
		{
			$this->render_admin ('kitten');
		}
	}
	
	function is_pro ()
	{
		return file_exists (dirname (__FILE__).'/pro/pro.php');
	}
	
	/**
	 * Display a list of people asking questions
	 *
	 * @return void
	 **/
	
	function admin_sources ()
	{
		$pager = new FT_Pager ($_GET, $_SERVER['REQUEST_URI'], 'author_email', 'ASC');
		
		$this->render_admin ('sources', array ('sources' => Question::get_sources ($pager), 'pager' => $pager));
	}
	
	
	/**
	 * Display questions
	 *
	 * @return void
	 **/
	
	function admin_questions ()
	{
		if (isset ($_GET['questions']) || isset ($_GET['pending']))
		{
			if (isset ($_GET['questions']))
				$id = intval ($_GET['questions']);
			else
				$id = intval ($_GET['pending']);

			$group = QuestionGroup::get ($id);
			
			if (isset ($_POST['import']) && is_uploaded_file ($_FILES['upload']['tmp_name']))
			{
				include (dirname (__FILE__).'/pro/csv_import.php');

				$csv = new Csv_Import ();
				$number = $csv->load ($_FILES['upload']['tmp_name'], $group);
				
				$this->render_message (sprintf (__ ('Number of questions imported: %d', 'faqtastic'), $number));
			}
			else if (isset ($_POST['addquestion']))
			{
				if (Question::create ($group, $_POST))
					$this->render_message (__ ('Your question has been created and added to the end of the list', 'faqtastic'));
				else
					$this->render_error (__ ('You must supply a question &amp; answer', 'faqtastic'));
			}
			
			$pager = new FT_Pager ($_GET, $_SERVER['REQUEST_URI'], 'position', 'ASC');

			if (isset ($_GET['questions']))
			{
				$questions = Question::get_by_group ($id, $pager);
				$this->render_admin ('questions', array ('questions' => $questions, 'group' => $group, 'pager' => $pager, 'features' => $this->features));
			}
			else
			{
				$questions = Question::get_pending ($id, $pager);
				$this->render_admin ('questions_pending', array ('questions' => $questions, 'group' => $group, 'pager' => $pager));
			}
		}
		else
		{
			if (isset ($_POST['addgroup']))
			{
				if (QuestionGroup::create ($_POST))
					$this->render_message (__ ('Your group has been created', 'faqtastic'));
				else
					$this->render_error (__ ('You must supply a unique name and URL', 'faqtastic'));
			}
			else if (isset ($_POST['publish']))
			{
				$name  = trim ($_POST['page_name']);
				$group = QuestionGroup::get ($_POST['group']);
				
				if ($name == '')
					$this->render_error ('You must supply a page name');
				else
				{
					if ($this->is_pro ()) {
						$top_anchor = (isset ($_POST['page_to_top'])) ? '<a name="faqTop"></a>' : '';
						$summary_code = (isset ($_POST['page_summary_code'])) ? '<p>[faq summary '.$group->name.']</p>' : '';
						$ask_code = (isset ($_POST['page_ask_code'])) ? '<p>[faq ask '.$group->name.']</p>' : '';
						$top_text = (isset ($_POST['page_top_text'])) ? $_POST['page_top_text'] : 'Back To Top';
						$top_link = (isset ($_POST['page_to_top'])) ? '<a href="#faqTop" class="faqTopLink"><span>'.$top_text.'</span></a>' : '';
						wp_insert_post (array ('post_content' => $top_anchor.$summary_code.'<p>[faq list '.$group->name.']</p>'.$ask_code.$top_link, 'post_title' => $name, 'post_type' => 'page', 'post_status' => 'publish'));
					} else {
						wp_insert_post (array ('post_content' => '[faq list '.$group->name.']', 'post_title' => $name, 'post_type' => 'page', 'post_status' => 'publish'));
					}
					$this->render_message (__ ('Your page has been created', 'faqtastic'));
				}
			}
			
			$pager  = new FT_Pager ($_GET, $_SERVER['REQUEST_URI'], 'name', 'ASC');
			$groups = QuestionGroup::get_groups ($pager);
			
			$this->render_admin ('groups', array ('groups' => $groups, 'pager' => $pager));
		}
	}
	
	
	/**
	 * Replace FAQ-Tastic tags inside a section of text
	 *
	 * @param array $matches Array of matched text, as returned from preg
	 * @return string Replaced text
	 **/
	
	function replace_tags ($matches)
	{
		$options = $this->get_options ();
		$tag     = trim ($matches[1]);
		
		$this->count++;

		// Decide what the function is
		if ($tag == 'ask')
		{
			if (isset ($_GET['thanks']))
				$extra = FAQDefaults::thanks (Question::get (intval ($_GET['thanks'])));
			else if (isset ($_GET['failed']))
				$extra = FAQDefaults::failed ();
			
			$group = QuestionGroup::get_by_name ($matches[2]);
			if ($group !== false)
				return $extra.$this->features->capture ('ask', array ('group' => $group, 'options' => $this->get_options ()));
		}
		else if ($tag == 'answer')
		{
			// Replace with the answer text
			global $post;
			$question = Question::get_by_page ($post->ID);
			if ($question)
			{
				$group    = QuestionGroup::get ($question->group_id);
				$before = $after = '';
				if (FAQ_Features::is_pro () && $options['adsense'])
				{
					if ($options['adsense_position'] == 'above')
						$before = $options['adsense'];
					else
						$after = $options['adsense'];
				}
			
				return $before.$this->capture ('answer', array ('question' => $question, 'group' => $group, 'allow_rating' => $group->ratings)).$after;
			}
			
			return '';
		}
		else if ($tag == 'list')
		{
			$group = QuestionGroup::get_by_name ($matches[2]);
			if ($group !== false && $group->page_id == 0)
			{
				$questions = Question::get_approved ($group->id, $options['question_order']);
				return $this->capture ('list', array ('questions' => $questions, 'group' => $group, 'allow_rating' => $group->ratings));
			}
			else if ($group !== false && $group->page_id > 0)
			{
				$questions = Question::get_approved ($group->id, $options['question_order']);
				return $this->capture ('links', array ('questions' => $questions, 'group' => $group, 'allow_rating' => $group->ratings));
			}
		}
		else if ($tag == 'summary')
		{
			$group = QuestionGroup::get_by_name ($matches[2]);
			if ($group !== false && $group->page_id == 0)
			{
				$questions = Question::get_approved ($group->id, $options['question_order']);
				return $this->capture ('summary', array ('questions' => $questions, 'group' => $group, 'allow_rating' => $group->ratings));
			}
			else if ($group !== false && $group->page_id > 0)
			{
				$questions = Question::get_approved ($group->id, $options['question_order']);
				return $this->capture ('links', array ('questions' => $questions, 'group' => $group, 'allow_rating' => $group->ratings));
			}
		}
		else if ($tag == 'open')
		{
			$questions = Question::get_open ();
			return $this->capture ('open', array ('questions' => $questions));
		}
		
		// Do nothing
		return '';
	}
	
	function replace ($matches)
	{
		$this->faq_text[$this->count] = $this->replace_tags ($matches);
		return '[XXX'.$this->count.']';
	}
	
	/**
	 * Deletes a question given a WP page ID
	 *
	 * @param int $id Page ID
	 * @return void
	 **/
	
	function delete_post ($id)
	{
		$question = Question::get_by_page ($id);
		if ($question)
			$question->delete ();
	}
	
	
	/**
	 * Hook into the 'save_post' filter and delete any cached data
	 *
	 * @return void
	 **/
	
	function save_post ($id)
	{
		delete_post_meta ($id, 'glossary_cache');
	}


	/**
	 * Hook into the_content filter, calling replace_tags
	 *
	 * @param string Text to display
	 * @return string Text to display
	 **/
	
	function put_content_back ($matches)
	{
		return $this->faq_text[$matches[1] - 1];
	}
	
	function the_content ($text)
	{
		if ($this->wp_head == false)
			$text = $this->pre_replace ($text);
		return preg_replace_callback ('@(?:<p>\s*)?\[XXX(\d*)\](?:\s*</p>)?@', array (&$this, 'put_content_back'), $text);
	}
	
	function wp_mail_from ($mail)
	{
		$options = $this->get_options ();
		return $options['admin_email'];
	}

	function wp_mail_from_name ($mail)
	{
		$options = $this->get_options ();
		return $options['admin_email_name'];
	}
	
	function wp_mail_content_type ()
	{
		return 'text/html';
	}
	
	function send_email ($to, $subject, $message, $html = false)
	{
		// Set the from name and email
		$this->add_filter ('wp_mail_from');
		$this->add_filter ('wp_mail_from_name');
		
		if ($html)
			$this->add_filter ('wp_mail_content_type');
		
		wp_mail ($to, $subject, $message);
		
		remove_filter ('wp_mail_from', array (&$this, 'wp_mail_from'));
		remove_filter ('wp_mail_from_name', array (&$this, 'wp_mail_from_name'));
		remove_filter ('wp_mail_from_name', array (&$this, 'wp_mail_content_type'));
	}
}


/**
 * Instantiate the FAQ-Tastic plugin
 *
 * @global
 */

$faqtastic = new FAQTastic;
?>