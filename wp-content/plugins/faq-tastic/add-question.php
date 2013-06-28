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

/**
 * Called when a question is asked.  Takes question data, validates it, and then redirects the user
 * to a success or failure page
 *
 * @package FAQ-Tastic
 **/

include ('../../../wp-config.php');

$_POST = stripslashes_deep ($_POST);

$group  = QuestionGroup::get (intval ($_POST['group']));
$source = $_POST['source'];

unset ($_POST['faq_answer']);

$permalink = get_option('permalink_structure');
$joiner = '&';
if ($permalink)
	$joiner = '?';
	
// Check group exists
if (($id = Question::create ($group, $_POST, 'pending', true)))
	wp_redirect ($source.$joiner.'thanks='.$id.'#faq');
else
	wp_redirect ($source.$joiner.'failed#faq');

?>