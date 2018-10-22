<?php
/**
 * @package         FLEXIcontent
 * @version         3.3
 *
 * @author          Emmanuel Danan, Georgios Papadakis, Yannick Berges, others, see contributor page
 * @link            http://www.flexicontent.com
 * @copyright       Copyright © 2018, FLEXIcontent team, All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\String\StringHelper;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

global $globalcats;
$app      = JFactory::getApplication();
$jinput   = $app->input;
$config   = JFactory::getConfig();
$user     = JFactory::getUser();
$session  = JFactory::getSession();
$document = JFactory::getDocument();
$cparams  = JComponentHelper::getParams('com_flexicontent');
$ctrl     = 'types.';
$hlpname  = 'fctypes';
$isAdmin  = $app->isAdmin();



/**
 * COMMON CSS classes and COMMON repeated texts
 */

$btn_class = 'btn';
$ico_class = 'fc-man-icon-s';
$out_class = FLEXI_J40GE ? 'btn btn-outline-dark' : 'btn';

$edit_entry  = JText::_('FLEXI_EDIT_TYPE', true);
$edit_layout = htmlspecialchars(JText::_('FLEXI_EDIT_LAYOUT_N_GLOBAL_PARAMETERS', true), ENT_QUOTES, 'UTF-8');
$view_fields = JText::_('FLEXI_VIEW', true); //JText::_('FLEXI_VIEW_FIELDS', true);
$view_items  = JText::_('FLEXI_VIEW', true); //JText::_('FLEXI_VIEW_ITEMS', true);



/**
 * JS for Columns chooser box and Filters box
 */

flexicontent_html::jscode_to_showhide_table(
	'mainChooseColBox',
	'adminListTableFCtypes',
	$start_html = '',  //'<span class="badge ' . (FLEXI_J40GE ? 'badge-dark' : 'badge-inverse') . '">' . JText::_('FLEXI_COLUMNS', true) . '<\/span> &nbsp; ',
	$end_html = '<div id="fc-columns-slide-btn" class="icon-arrow-up-2 btn btn-outline-secondary" title="' . JText::_('FLEXI_HIDE') . '" style="cursor: pointer;" onclick="fc_toggle_box_via_btn(\\\'mainChooseColBox\\\', document.getElementById(\\\'fc_mainChooseColBox_btn\\\'), \\\'btn-primary\\\');"><\/div>'
);
$tools_cookies['fc-filters-box-disp'] = 0; //JFactory::getApplication()->input->cookie->get('fc-filters-box-disp', 0, 'int');



/**
 * ICONS and reusable variables
 */


$attribs_editlayout = ' class="fc-edit-layout-btn ntxt ' . $this->btn_sm_class . ' ' . $this->tooltip_class . '" title="'.flexicontent_html::getToolTip( 'FLEXI_EDIT_LAYOUT_N_GLOBAL_PARAMETERS', null, 1, 1).'" ';

$image_editlayout = 0 ?
	JHtml::image('components/com_flexicontent/assets/images/'.'layout_edit.png', htmlspecialchars(JText::_('FLEXI_EDIT_LAYOUT_N_GLOBAL_PARAMETERS'), ENT_QUOTES, 'UTF-8'), ' class="'.$ico_class.'"') :
	'<span class="'.$ico_class.'"><span class="icon-edit"></span></span>' ;

$article_viewing_tip  = '<img src="components/com_flexicontent/assets/images/comments.png" class="fc-man-icon-s ' . $this->tooltip_class . '" data-placement="bottom" alt="'.JText::_('FLEXI_JOOMLA_ARTICLE_VIEW', true).'" title="'.flexicontent_html::getToolTip('FLEXI_JOOMLA_ARTICLE_VIEW', 'FLEXI_ALLOW_ARTICLE_VIEW_DESC', 1, 1).'" /> ';
$default_template_tip = '<img src="components/com_flexicontent/assets/images/comments.png" class="fc-man-icon-s ' . $this->tooltip_class . '" data-placement="bottom" alt="'.JText::_( 'FLEXI_TYPE_DEFAULT_TEMPLATE', true ).'" title="'.flexicontent_html::getToolTip('FLEXI_TYPE_DEFAULT_TEMPLATE', 'FLEXI_TYPE_DEFAULT_TEMPLATE_DESC', 1, 1).'" /> ';


/**
 * Order stuff and table related variables
 */

$list_total_cols = 12;


/**
 * Add inline JS
 */

$js = '';

$js .= "

// Delete a specific list filter
function delFilter(name)
{
	//if(window.console) window.console.log('Clearing filter:'+name);
	var myForm = jQuery('#adminForm');
	var filter = jQuery('#'+name);

	if (!filter.length)
	{
		return;
	}
	else if (filter.attr('type') == 'checkbox')
	{
		filter.checked = '';
	}
	else
	{
		filter.val('');

		// Case that input has Calendar JS attached
		if (filter.attr('data-alt-value'))
		{
			filter.attr('data-alt-value', '');
		}
	}
}

function delAllFilters()
{
	delFilter('search');
	delFilter('filter_state');
	delFilter('filter_access');
	delFilter('filter_order');
	delFilter('filter_order_Dir');
}

";

if ($js)
{
	$document->addScriptDeclaration('
		jQuery(document).ready(function()
		{
			' . $js . '
		});
	');
}
?>


<div id="flexicontent" class="flexicontent">


<form action="index.php?option=<?php echo $this->option; ?>&amp;view=<?php echo $this->view; ?>" method="post" name="adminForm" id="adminForm">


<div class="<?php echo FLEXI_J40GE ? 'row' : 'row-fluid'; ?>">

<?php if (!empty( $this->sidebar)) : ?>

	<div id="j-sidebar-container" class="span2 col-md-2">
		<?php echo str_replace('type="button"', '', $this->sidebar); ?>
	</div>
	<div id="j-main-container" class="span10 col-md-10">

<?php else : ?>

	<div id="j-main-container" class="span12 col-md-12">

<?php endif;?>


	<div id="fc-managers-header">

		<?php if (!empty($this->lists['scope_tip'])) : ?>
		<div class="fc-filter-head-box filter-search nowrap_box" style="margin: 0;">
			<?php echo $this->lists['scope_tip']; ?>
		</div>
		<?php endif; ?>

		<div class="fc-filter-head-box filter-search nowrap_box">
			<div class="btn-group <?php echo $this->ina_grp_class; ?>">
				<?php
					echo !empty($this->lists['scope']) ? $this->lists['scope'] : '';
				?>
				<input type="text" name="search" id="search" placeholder="<?php echo !empty($this->scope_title) ? $this->scope_title : JText::_('FLEXI_SEARCH'); ?>" value="<?php echo htmlspecialchars($this->lists['search'], ENT_QUOTES, 'UTF-8'); ?>" class="inputbox" />
				<button title="" data-original-title="<?php echo JText::_('FLEXI_SEARCH'); ?>" class="<?php echo $btn_class . (FLEXI_J40GE ? ' btn-outline-dark ' : ' ') . $this->tooltip_class; ?>" onclick="document.adminForm.limitstart.value=0; Joomla.submitform();"><?php echo FLEXI_J30GE ? '<i class="icon-search"></i>' : JText::_('FLEXI_GO'); ?></button>

				<div id="fc_filters_box_btn" data-original-title="<?php echo JText::_('FLEXI_FILTERS'); ?>" class="<?php echo $this->tooltip_class . ' ' . ($this->count_filters ? 'btn ' . $this->btn_iv_class : $out_class); ?>" onclick="fc_toggle_box_via_btn('fc-filters-box', this, 'btn-primary', false, undefined, 1);">
					<?php echo FLEXI_J30GE ? '<i class="icon-filter"></i>' : JText::_('FLEXI_FILTERS'); ?>
					<?php echo ($this->count_filters  ? ' <sup>' . $this->count_filters . '</sup>' : ''); ?>
				</div>

				<div id="fc-filters-box" <?php if (!$this->count_filters || !$tools_cookies['fc-filters-box-disp']) echo 'style="display:none;"'; ?> class="fcman-abs" onclick="var event = arguments[0] || window.event; event.stopPropagation();">
					<?php
					echo $this->lists['filter_state'];
					echo $this->lists['filter_access'];
					?>

					<div id="fc-filters-slide-btn" class="icon-arrow-up-2 btn btn-outline-secondary" title="<?php echo JText::_('FLEXI_HIDE'); ?>" style="cursor: pointer;" onclick="fc_toggle_box_via_btn('fc-filters-box', document.getElementById('fc_filters_box_btn'), 'btn-primary');"></div>
					<input type="hidden" id="fc-filters-box-disp" name="fc-filters-box-disp" value="<?php echo $tools_cookies['fc-filters-box-disp']; ?>" />
				</div>

				<button title="" data-original-title="<?php echo JText::_('FLEXI_RESET_FILTERS'); ?>" class="<?php echo $btn_class . (FLEXI_J40GE ? ' btn-outline-dark ' : ' ') . $this->tooltip_class; ?>" onclick="document.adminForm.limitstart.value=0; delAllFilters(); Joomla.submitform();"><?php echo FLEXI_J30GE ? '<i class="icon-remove"></i>' : JText::_('FLEXI_CLEAR'); ?></button>
			</div>

		</div>


		<div class="fc-filter-head-box nowrap_box">

			<div class="btn-group">
				<div id="fc_mainChooseColBox_btn" class="<?php echo $out_class . ' ' . $this->tooltip_class; ?> hidden-phone" onclick="fc_toggle_box_via_btn('mainChooseColBox', this, 'btn-primary');" title="<?php echo flexicontent_html::getToolTip('', 'FLEXI_ABOUT_AUTO_HIDDEN_COLUMNS', 1, 1); ?>">
					<?php echo JText::_( 'FLEXI_COLUMNS' ); ?><sup id="columnchoose_totals"></sup>
				</div>

				<?php if (!empty($this->minihelp) && FlexicontentHelperPerm::getPerm()->CanConfig): ?>
				<div id="fc-mini-help_btn" class="<?php echo $out_class; ?>" onclick="fc_toggle_box_via_btn('fc-mini-help', this, 'btn-primary');" >
					<span class="icon-help"></span>
					<?php echo $this->minihelp; ?>
				</div>
				<?php endif; ?>
			</div>
			<div id="mainChooseColBox" class="group-fcset fcman-abs" style="display:none;"></div>

		</div>

		<div class="fc-filter-head-box nowrap_box">
			<div class="limit nowrap_box">
				<?php
				$pagination_footer = $this->pagination->getListFooter();
				if (strpos($pagination_footer, '"limit"') === false) echo $this->pagination->getLimitBox();
				?>
			</div>

			<span class="fc_item_total_data nowrap_box fc-mssg-inline fc-info fc-nobgimage hidden-phone hidden-tablet">
				<?php echo @$this->resultsCounter ? $this->resultsCounter : $this->pagination->getResultsCounter(); // custom Results Counter ?>
			</span>

			<?php if (($getPagesCounter = $this->pagination->getPagesCounter())): ?>
			<span class="fc_pages_counter nowrap_box fc-mssg-inline fc-info fc-nobgimage">
				<?php echo $getPagesCounter; ?>
			</span>
			<?php endif; ?>
		</div>
	</div>


	<div class="fcclear"></div>

	<table id="adminListTableFCtypes" class="adminlist table fcmanlist" itemscope itemtype="http://schema.org/WebPage">
	<thead>
		<tr>

			<th class="hidden-phone">
				<?php echo JText::_( 'FLEXI_NUM' ); ?>
			</th>

			<th class="col_cb left">
				<div class="group-fcset">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					<label for="checkall-toggle" class="green single"></label>
				</div>
			</th>

			<th class="hideOnDemandClass left">
				<?php echo JHtml::_('grid.sort', 'FLEXI_STATUS', 'a.' . $this->state_propname, $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>

			<th class="hideOnDemandClass title">
				<?php echo JHtml::_('grid.sort', 'FLEXI_TYPE_NAME', 'a.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>

			<th class="hideOnDemandClass col_redirect hidden-phone hidden-tablet">
				<?php echo $article_viewing_tip . JText::_( 'FLEXI_JOOMLA_ARTICLE_VIEW' )."<br/><small>(" . JText::_( 'FLEXI_ALLOWED') .' / '. JText::_( 'FLEXI_REROUTED' ) .' / '. JText::_( 'FLEXI_REDIRECTED' ) . ")</small>"; ?>
			</th>

			<th class="left hideOnDemandClass hidden-phone hidden-tablet" colspan="2">
				<?php echo $default_template_tip.JText::_( 'FLEXI_TEMPLATE' )."<br/><small>(".JText::_( 'FLEXI_PROPERTY_DEFAULT' )." ".JText::_( 'FLEXI_TEMPLATE_ITEM' ).")</small>"; ?>
			</th>

			<th class="hideOnDemandClass hidden-phone hidden-tablet">
				<?php echo JHtml::_('grid.sort', 'FLEXI_ALIAS', 'a.alias', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>

			<th class="hideOnDemandClass center">
				<?php echo JHtml::_('grid.sort', 'FLEXI_FIELDS', 'fassigned', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>

			<th class="hideOnDemandClass center">
				<?php echo JHtml::_('grid.sort', 'FLEXI_ITEMS', 'iassigned', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>

			<th class="hideOnDemandClass hidden-phone">
				<?php echo JHtml::_('grid.sort', 'FLEXI_ACCESS', 'a.access', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>

			<th class="hideOnDemandClass col_id center hidden-phone hidden-tablet">
				<?php echo JHtml::_('grid.sort', 'FLEXI_ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>

		</tr>
	</thead>

	<tbody>
		<?php
		$canCheckinRecords = $user->authorise('core.admin', 'com_checkin');

		// Add 1 collapsed row to the empty table to allow border styling to apply
		if (!count($this->rows))
		{
			echo '<tr class="collapsed_row"><td colspan="'.$list_total_cols.'"></td></tr>';
		}

		foreach ($this->rows as $i => $row)
		{
			$row = & $this->rows[$i];
			$link 		= 'index.php?option=com_flexicontent&amp;task=types.edit&amp;view=type&amp;id='. $row->id;

			$fields_link = 'index.php?option=com_flexicontent&amp;view=fields&amp;filter_type='. $row->id;
			$items_link  = 'index.php?option=com_flexicontent&amp;view=items&amp;filter_type='. $row->id;

			// Permissions
			$row->canCheckin   = empty($row->checked_out) || $row->checked_out == $user->id || $canCheckinRecords;
			$row->canEdit      = 1;
			$row->canEditState = 1;
			$row->canDelete    = 1;

			$canEdit    = 1;

			$stateIsChangeable = $row->canCheckin && $row->canEditState;
			$row_ilayout       = $row->config->get('ilayout');
   		?>
		<tr class="<?php echo 'row' . ($i % 2); ?>">

			<td class="hidden-phone">
				<?php echo $this->pagination->getRowOffset($i); ?>
			</td>

			<td class="col_cb">
				<!--div class="adminlist-table-row"></div-->
				<?php echo JHtml::_($hlpname . '.grid_id', $i, $row->id); ?>
			</td>

			<td class="col_status" style="padding-right: 8px;">
				<div class="btn-group fc-group fc-types">
					<?php
					//echo JHtml::_('jgrid.published', $row->state, $i, $ctrl, $stateIsChangeable);
					//echo JHtml::_($hlpname . '.published', $row->state, $i, $stateIsChangeable);

					echo JHtml::_($hlpname . '.statebutton', $row, $i);
					?>
				</div>
			</td>

			<td class="col_title">
				<?php
				/**
				 * Display an edit pencil or a check-in button if: either (a) current user has Global
				 * Checkin privilege OR (b) record checked out by current user, otherwise display a lock
				 */
				echo JHtml::_($hlpname . '.checkedout', $row, $user, $i);

				/**
				 * Display title with edit link ... (row editable and not checked out)
				 * Display title with no edit link ... if row is not-editable for any reason (no ACL or checked-out by other user)
				 */
				echo JHtml::_($hlpname . '.edit_link', $row, $i, $row->canEdit);
				?>
			</td>

			<td class="col_redirect hidden-phone hidden-tablet">
				<?php
					$jarticle_url_handling_txts = array(0 => 'FLEXI_ROUTE_TO_ITEM_VIEW', 1 => 'FLEXI_ALLOWED', '2' => 'FLEXI_REDIRECT_TO_ITEM_VIEW');
					$allow_jview = $row->config->get("allow_jview");

					$jview_ops = array();
					$jview_ops[] = JHtml::_('select.option', '1', 'FLEXI_ALLOWED');
					$jview_ops[] = JHtml::_('select.option', '0', 'FLEXI_ROUTE_TO_ITEM_VIEW');
					$jview_ops[] = JHtml::_('select.option', '2', 'FLEXI_REDIRECT_TO_ITEM_VIEW');

					echo JHtml::_('select.genericlist', $jview_ops, 'allow_jview['.$row->id.']', 'size="1" class="use_select2_lib" onchange="listItemTask(\'cb'.$i.'\',\'types.toggle_jview\'); Joomla.submitform()"', 'value', 'text', $allow_jview, 'allow_jview'.$row->id, $translate=true);
				?>
			</td>

			<td class="col_edit_layout hidden-phone hidden-tablet">
				<?php echo JHtml::_($hlpname . '.edit_layout', $row, '__modal__', $i, $this->perms->CanTemplates, $row_ilayout); ?>
			</td>

			<td class="col_template hidden-phone hidden-tablet">
				<small><?php echo $row_ilayout; ?></small>
			</td>

			<td class="col_alias hidden-phone hidden-tablet">
				<?php echo StringHelper::strlen($row->alias) > 25
					? StringHelper::substr( htmlspecialchars($row->alias, ENT_QUOTES, 'UTF-8'), 0 , 25) . '...'
					: htmlspecialchars($row->alias, ENT_QUOTES, 'UTF-8');
				?>
			</td>

			<td class="center">
				<a class="badge fc-assignments-btn" href="<?php echo $fields_link; ?>">
					<?php echo $row->fassigned; ?>
				</a>
			</td>

			<td class="center">
				<a class="badge badge-info fc-assignments-btn" href="<?php echo $items_link; ?>">
					<?php echo $row->iassigned; ?>
				</a>
			</td>

			<td class="col_access hidden-phone">
				<?php echo $row->canEdit
					? flexicontent_html::userlevel('access['.$row->id.']', $row->access, 'onchange="return listItemTask(\'cb'.$i.'\',\''.$ctrl.'access\')" class="use_select2_lib"')
					: $this->escape($row->access_level);
				?>
			</td>

			<td class="col_id hidden-phone hidden-tablet">
				<?php echo $row->id; ?>
			</td>

		</tr>
		<?php
		}
		?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="<?php echo $list_total_cols; ?>" style="text-align: left;">
				<?php echo $pagination_footer; ?>
			</td>
		</tr>
	</tfoot>

	</table>


	<!-- Common management form fields -->
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_flexicontent" />
	<input type="hidden" name="controller" value="types" />
	<input type="hidden" name="view" value="types" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" id="filter_order" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" id="filter_order_Dir" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<input type="hidden" name="fcform" value="1" />
	<?php echo JHtml::_('form.token'); ?>

	<!-- fc_perf -->

	</div>  <!-- j-main-container -->
</div>  <!-- row / row-fluid-->

</form>
</div><!-- #flexicontent end -->
