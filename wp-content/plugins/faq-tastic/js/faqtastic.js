/* Group functions */

function delete_group (item)
{
	if (confirm ('Are you sure you want to delete this group and all its questions?'))
	{
	  new Ajax.Request (wp_base + '?cmd=delete_group&id=' + item,
	    {
	      asynchronous: true,
	      onLoading: function(request) { Element.show ('loading')},
	      onComplete: function(request) { Element.hide ('loading'); Element.remove ('group_' + item);}
	    });
	}
}

function edit_group (item)
{
  Modalbox.show (wp_base + '?id=' + item + '&cmd=edit_group&id=' + item,
    {
      title: 'Edit group',
      overlayOpacity: 0.4,
      inactiveFade: false
    });
}

function save_group (item,form)
{
  Modalbox.deactivate ();
  Modalbox.show (wp_base + '?id=' + item + '&cmd=save_group&id=' + item,
    {
      title: 'Saving',
      overlayOpacity: 0.4,
      inactiveFade: false,
      method: 'post',
      params: Form.serialize (form),
      afterLoad:function()
      {
        Modalbox.hide ();
        new Ajax.Updater ('group_' + item, wp_base + '?cmd=show_group&id=' + item, { asynchronous: true });
      }
    });

  return false;
}




/* Question functions */
function edit_question (item)
{
  new Ajax.Updater ('question_' + item, wp_base + '?cmd=edit_question&id=' + item,
    {
      asynchronous: true, evalScripts: true,
      onLoading: function(request) { Element.show ('loading')},
      onComplete: function(request)
      {
        Element.hide ('loading');
        var tabs = new Control.Tabs ('question_edit_' + item);
        tabs.setActiveTab ('basic_' + item);
      }
    });
}

function delete_question (item)
{
	if (confirm ('Are you sure you want to delete this question?'))
	{
	  new Ajax.Request (wp_base + '?cmd=delete_question&id=' + item,
	    {
	      asynchronous: true, evalScripts: true,
	      onLoading: function(request) { Element.show ('loading')},
	      onComplete: function(request) { Element.hide ('loading'); Element.remove ('question_' + item);}
	    });
	}
}

function save_question (item,form)
{
  new Ajax.Updater ('question_' + item, wp_base + '?cmd=save_question&id=' + item,
    {
      asynchronous: true, evalScripts: true,
      evalScripts: true,
			parameters: Form.serialize (form),
      onLoading: function(request) { Element.show ('loading')},
      onComplete: function(request) { Element.hide ('loading');  }
    });
}

function reject_question (item,form)
{
  new Ajax.Updater ('question_' + item, wp_base + '?cmd=reject_question&id=' + item,
    {
      asynchronous: true, evalScripts: true,
			parameters: Form.serialize (form),
      onLoading: function(request) { Element.show ('loading')},
      onComplete: function(request) { Element.hide ('loading'); Element.remove ('question_' + item);}
    });
}

function cancel_question (item)
{
  new Ajax.Updater ('question_' + item, wp_base + '?cmd=cancel_question&id=' + item,
    {
      asynchronous: true, evalScripts: true,
      onLoading: function(request) { Element.show ('loading')},
      onComplete: function(request) { Element.hide ('loading'); }
    });
}

function save_order (group,offset)
{
  new Ajax.Request (wp_base + '?cmd=save_order&id=' + group + '&offset=' + offset,
    {
      asynchronous: true, evalScripts: true,
      parameters: Sortable.serialize ('questions'),
      onLoading: function(request) { Element.show ('loading')},
      onComplete: function(request) { Element.hide ('loading'); }
    });
}


function add_related (item)
{
  var value, insert;
  
  value   = $('related_' + item).options[$('related_' + item).selectedIndex];
  if (!document.getElementById ('rel_' + item + '_' + value.value))
  {
    insert  = '<li>' + value.text.escapeHTML ();
    insert += '<input type="hidden" name="related[]" value="' + value.value + '" id="rel_' + item + '_' + value.value + '"/>';
    insert += ' <small>(';
    insert += 'follow <input type="checkbox" name="related_follow[]" value="' + value.value + '" checked="checked"/> | ';
    insert += '<a href="#" onclick="return delete_related(this)">delete</a>)</small>';
    insert += '</li>'
  
    new Insertion.Bottom ('related_list_' + item, insert);
  }
}

function delete_related (item)
{
  Element.remove (item.parentNode.parentNode);
  return false;
}

function delete_selected ()
{
	if (confirm ('Are you sure you want to delete these questions?'))
	{
	  var items;
	  items = $$('input[type=checkbox]');
	  items = items.reject (function (item)
	    {
	      if (item.name == 'select[]' && item.checked)
	        return false;
	      return true;
	    });
	  
	  var str = '';
	  items.each (function (item)
	    {
	      str += ',' + item.value;
	    });
	
	  new Ajax.Request (wp_base + '?cmd=delete_questions&id=0',
	    {
	      asynchronous: true, evalScripts: true,
	      parameters: { items: str},
	      onLoading: function(request) { Element.show ('loading')},
	      onComplete: function(request)
	      {
	        Element.hide ('loading');
	        items.each (function (item)
	        {
	          Element.remove ('question_' + item.value);
	        });
	      }
	    });
	
	  return false;
    }
}

function select_all ()
{
  var items;
  items = $$('input[type=checkbox]');
  items = items.reject (function (item)
    {
      if (item.name == 'select[]')
        return false;
      return true;
    });
  
  items.each (function (item)
    {
      item.checked = selected_all;
    });
    
  selected_all = !selected_all;
  return false;
}

var selected_all = true;
