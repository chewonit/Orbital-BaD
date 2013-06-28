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
 * Called by rating AJAX command
 *
 * @package FAQ-Tastic
 **/

session_start ();

$ratings = array ();
if (isset ($_COOKIE['faqtastic_rating']))
	$ratings = unserialize ($_COOKIE['faqtastic_rating']);

include ('../../../wp-config.php');

$item   = intval ($_GET['item']);
$group  = intval ($_GET['grp']);
$rating = intval ($_GET['type']);

global $faqtastic;

$options = $faqtastic->get_options ();
if (isset ($ratings[$group]) && in_array ($item, $ratings[$group]))
	echo __ ('You already rated this question', 'faqtastic');
else
{
	$ratings[$group][] = $item;

	setcookie ('faqtastic_rating', serialize ($ratings), time () + 60*60*24*30, '/');

	$question = Question::get ($item);
	if ($question && $question->group_id == $group && ($rating == 0 || $rating == 1))
	{
		if ($rating == 0)
			$question->rate_positive ();
		else
			$question->rate_negative ();
		
		echo FAQDefaults::rating ($question);
	}
	else
		echo __ ('Something went wrong while registering your vote', 'faqtastic');
}
?>