<?php
/**
 * @version 1.5 stable $Id: image.php 1750 2013-09-03 20:50:59Z ggppdk $
 * @package Joomla
 * @subpackage FLEXIcontent
 * @copyright (C) 2009 Emmanuel Danan - www.vistamedia.fr
 * @license GNU/GPL v2
 * 
 * FLEXIcontent is a derivative work of the excellent QuickFAQ component
 * @copyright (C) 2008 Christoph Lukes
 * see www.schlu.net for more information
 *
 * FLEXIcontent is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$uri = JURI::getInstance();
$current_uri = $uri->toString();
$ctrl_task  = FLEXI_J16GE ? 'task=filemanager.'  :  'controller=filemanager&amp;task=';
$del_task   = FLEXI_J16GE ? 'filemanager.remove'  :  'remove';
$session = JFactory::getSession();

$jquerylib = JURI::root().'components/com_flexicontent/librairies/jquery/';
$pluploadlib = JURI::root().'components/com_flexicontent/librairies/plupload/';

$doc = JFactory::getDocument();
$doc->addStyleSheet($pluploadlib.'js/jquery.plupload.queue/css/jquery.plupload.queue.css');
//<link rel="stylesheet" href="../../js/jquery.plupload.queue/css/jquery.plupload.queue.css" type="text/css" media="screen" />

$doc->addScript($jquerylib.'js/jquery-1.9.0.min.js');
//<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

//<!-- production -->
//<script type="text/javascript" src="../../js/plupload.full.min.js"></script>
//<script type="text/javascript" src="../../js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
$doc->addScript($pluploadlib.'js/plupload.full.min.js');
$doc->addScript($pluploadlib.'js/jquery.plupload.queue/jquery.plupload.queue.js');

/*<!-- debug 
<script type="text/javascript" src="../../js/moxie.js"></script>
<script type="text/javascript" src="../../js/plupload.dev.js"></script>
<script type="text/javascript" src="../../js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
-->*/
$doc->addScript($pluploadlib.'js/moxie.js');
$doc->addScript($pluploadlib.'js/plupload.dev.js');
$doc->addScript($pluploadlib.'js/jquery.plupload.queue/jquery.plupload.queue.js');

$doc->addScriptDeclaration('
jQuery(function() {
	// Setup flash version
	jQuery("#flash_uploader").pluploadQueue({
		// General settings
		runtimes : "flash",
		url : "'.JURI::base().'index.php?option=com_flexicontent&'.$ctrl_task.'uploads&'.$session->getName().'='.$session->getId().'&fieldid='.$this->fieldid.'&u_item_id='.$this->u_item_id.'&folder_mode='.$this->folder_mode.'&secure=0&'.JUtility::getToken().'=1",
		chunk_size : "1mb",
		unique_names : true,

		filters : {
			max_file_size : "10mb",
			mime_types: [
				{title : "Image files", extensions : "jpg,gif,png"},
				{title : "Zip files", extensions : "zip"}
			]
		},

		// Resize images on clientside if we can
		resize : {width : 320, height : 240, quality : 90},

		// Flash settings
		flash_swf_url : "'.$pluploadlib.'/js/Moxie.swf"
	});
	
	var uploader = jQuery("#flash_uploader").pluploadQueue();
	
	uploader.bind(\'UploadComplete\',function(){
        console.log("All Files Uploaded");
		window.location.reload();
    });
});
');
?>

<div class="flexicontent">
<table width="100%" border="0" style="padding: 5px; margin-bottom: 10px;" id="filemanager-zone">
	<tr>
		<td>
			<?php
			echo FLEXI_J16GE ? JHtml::_('tabs.start') : $this->pane->startPane( 'stat-pane' );
			if ($this->CanUpload) :
				echo FLEXI_J16GE ?
					JHtml::_('tabs.panel', JText::_( 'FLEXI_UPLOAD_LOCAL_FILE' ), 'local' ) :
					$this->pane->startPanel( JText::_( 'FLEXI_UPLOAD_LOCAL_FILE' ), 'local' ) ;
			?>

			<!-- File Upload Form -->
			<form action="<?php echo JURI::base(); ?>index.php?option=com_flexicontent&amp;<?php echo $ctrl_task; ?>upload&amp;<?php echo $session->getName().'='.$session->getId(); ?>" id="uploadForm" method="post" enctype="multipart/form-data">
				<fieldset class="filemanager-tab" >
					<legend><?php echo JText::_( 'FLEXI_CHOOSE_FILE' ); ?> [ <?php echo JText::_( 'FLEXI_MAX' ); ?>&nbsp;<?php echo ($this->params->get('upload_maxsize') / 1000000); ?>M ]</legend>
					<fieldset class="actions" id="filemanager-1">
						<table class="admintable" cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td class="key">
									<label for="file-upload">
									<?php echo JText::_( 'FLEXI_CHOOSE_FILE' ); ?>
									</label>
								</td>
								<td>
									<input type="file" id="file-upload" name="Filedata" />
								</td>
							</tr>
<?php if (!$this->folder_mode) { ?>
							<tr>
								<td class="key">
									<label for="file-desc">
									<?php echo JText::_( 'FLEXI_DESCRIPTION' ); ?>
									</label>
								</td>
								<td>
									<textarea name="file-desc" cols="24" rows="3" id="file-desc"></textarea>
								</td>
							</tr>
							
							<tr>
								<td class="key">
									<label for="file-title">
									<?php echo JText::_( 'FLEXI_FILE_TITLE' ); ?>
									</label>
								</td>
								<td>
									<input type="text" id="file-title" size="44" class="required" name="file-title" />
								</td>
							</tr>
<?php } ?>
						</table>
						<input type="submit" id="file-upload-submit" class="fc_button fcsimple" value="<?php echo JText::_( 'FLEXI_START_UPLOAD' ); ?>"/>
						<span id="upload-clear"></span>
						
					</fieldset>
					
					<ul class="upload-queue" id="upload-queue">
						<li style="display: none"></li>
					</ul>
				</fieldset>
				<?php echo JHTML::_( 'form.token' ); ?>
				<input type="hidden" name="fieldid" value="<?php echo $this->fieldid; ?>" />
				<input type="hidden" name="u_item_id" value="<?php echo $this->u_item_id; ?>" />
				<input type="hidden" name="folder_mode" value="<?php echo $this->folder_mode; ?>" />
				<input type="hidden" name="secure" value="0" />
				<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_flexicontent&view=fileselement&tmpl=component&field='.$this->fieldid.'&folder_mode='.$this->folder_mode.'&layout=image&filter_secure=M'); ?>" />
			</form>
			<?php echo FLEXI_J16GE ? '' : $this->pane->endPanel(); ?>
			
			<!----start files tab---->
			<?php
			echo FLEXI_J16GE ?
					JHtml::_('tabs.panel', JText::_( 'FLEXI_UPLOAD_LOCAL_FILES' ), 'local-files' ) :
					$this->pane->startPanel( JText::_( 'FLEXI_UPLOAD_LOCAL_FILES' ), 'local-files' ) ;
			?>
			<fieldset class="filemanager-tabx" >
				<legend><?php echo JText::_( 'FLEXI_CHOOSE_FILE' ); ?> [ <?php echo JText::_( 'FLEXI_MAX' ); ?>&nbsp;<?php echo ($this->params->get('upload_maxsize') / 1000000); ?>M ]</legend>
				<fieldset class="actions" id="filemanager-2">
					<div id="flash_uploader" style="width: 100%; height: 330px;">Your browser doesn't have Flash installed.</div>
				</fieldset>
			</fieldset>
			<?php echo FLEXI_J16GE ? '' : $this->pane->endPanel(); ?>
			<!----end files tab---->
			
			
			<?php endif; ?>
			<?php echo FLEXI_J16GE ? JHtml::_('tabs.end') : $this->pane->endPane(); ?>
		</td>
	</tr>
</table>

<form action="<?php echo JURI::base(); ?>index.php?option=com_flexicontent&amp;view=fileselement&amp;field=<?php echo $this->fieldid?>&amp;tmpl=component&amp;layout=image&amp;filter_secure=M" method="post" name="adminForm" id="adminForm">

<?php if (!$this->folder_mode) : ?>
	<table class="adminform" border="0">
		<tr>
			<td align="left">
				<label class="label"><?php echo JText::_( 'FLEXI_SEARCH' ); ?></label>
				<?php echo $this->lists['filter']; ?>
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
				<div id="fc-filter-buttons">
					<button class="fc_button fcsimple" onclick="this.form.submit();"><?php echo JText::_( 'FLEXI_GO' ); ?></button>
					<button class="fc_button fcsimple" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'FLEXI_RESET' ); ?></button>
				</div>
			</td>
			<td nowrap="nowrap">
			 	<?php echo $this->lists['ext']; ?>
			 	<?php if ($this->CanViewAllFiles) echo $this->lists['uploader']; ?>
			 	&nbsp; &nbsp; &nbsp;
				<label class="label">Item ID</label> <?php echo $this->lists['item_id']; ?>
			</td>
		</tr>
	</table>
<?php endif; ?>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'FLEXI_NUM' ); ?></th>
<?php if ($this->folder_mode) { ?>
			<th width="5">&nbsp;</th>
<?php } ?>
			<th width="5"><?php echo JText::_( 'FLEXI_THUMB' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'FLEXI_FILENAME', 'f.filename', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="20%"><?php echo JHTML::_('grid.sort', 'FLEXI_FILE_TITLE', 'f.altname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JText::_( 'FLEXI_SIZE' ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', 'FLEXI_UPLOADER', 'uploader', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', 'FLEXI_UPLOAD_TIME', 'f.uploaded', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
<?php if (!$this->folder_mode) { ?>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'FLEXI_ID', 'f.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
<?php } ?>
		</tr>
		
	</thead>

	<tfoot>
		<tr>
			<td colspan="<?php echo $this->folder_mode ? 10 : 10; ?>">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		
	</tfoot>

	<tbody>
		<?php
		$imageexts = array('jpg','gif','png','bmp','jpeg');
		$index = JRequest::getInt('index', 0);
		$k = 0;
		$i = 0;
		$n = count($this->rows);
		foreach ($this->rows as $row) {
			unset($thumb_or_icon);
			$filename    = str_replace( array("'", "\""), array("\\'", ""), $row->filename );
			if ( !in_array(strtolower($row->ext), $imageexts)) continue;  // verify image is in allowed extensions
			
			$path      = COM_FLEXICONTENT_MEDIAPATH;  // JPATH_ROOT . DS . <media_path>
			$file_path = $row->filename;
			
			if ($this->folder_mode) {
				$file_path = $this->img_folder . DS . $row->filename;
			} else if (substr($row->filename, 0, 7)!='http://') {
				$file_path = $path . DS . $row->filename;
			} else {
				$thumb_or_icon = 'URL';
			}
			
			$file_path    = str_replace('\\', '/', $file_path);
			if ( empty($thumb_or_icon) ) {
				$thumb_or_icon = JURI::root() . 'components/com_flexicontent/librairies/phpthumb/phpThumb.php?src=' . $file_path . '&w=60&h=60';
				$thumb_or_icon = "<img src=\"$thumb_or_icon\" alt=\"$filename\" />";
			}
			$file_preview = JURI::root() . 'components/com_flexicontent/librairies/phpthumb/phpThumb.php?src=' . $file_path . '&w='.$this->thumb_w.'&h='.$this->thumb_h;
			if ($this->folder_mode) {
				$img_assign_link = "window.parent.qmAssignFile".$this->fieldid."('".$this->targetid."', '".$filename."', '".$file_preview."');";
			} else {
				$img_assign_link = "qffileselementadd(document.getElementById('file".$row->id."'), '".$row->id."', '".$filename."');";
			}
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pagination->getRowOffset( $i ); ?></td>
<?php if ($this->folder_mode) { ?>
			<td>
				<a href="javascript:;" onclick="if (confirm('<?php echo JText::_('FLEXI_SURE_TO_DELETE_FILE'); ?>')) { document.adminForm.filename.value='<?php echo $row->filename;?>'; document.adminForm.controller.value='filemanager'; <?php echo FLEXI_J16GE ? "Joomla." : ""; ?>submitbutton('<?php echo $del_task; ?>'); }" href="#">
				<?php echo JHTML::image('components/com_flexicontent/assets/images/trash.png', JText::_('FLEXI_REMOVE') ); ?>
				</a>
			</td>
<?php } ?>
			<td align="center">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'FLEXI_SELECT' ); ?>::<?php echo $row->filename; ?>">
				<a style="cursor:pointer" onclick="<?php echo $img_assign_link; ?>">
				<?php echo $thumb_or_icon; ?>
				</a>
				</span>
			</td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'FLEXI_SELECT' );?>::<?php echo $row->filename; ?>">
					<a style="cursor:pointer" id="file<?php echo $row->id;?>" rel="<?php echo $filename; ?>" onclick="<?php echo $img_assign_link; ?>">
					<?php echo htmlspecialchars($row->filename, ENT_QUOTES, 'UTF-8'); ?>
					</a>
				</span>
			</td>
			<td>
				<?php
				if (JString::strlen($row->altname) > 25) {
					echo JString::substr( htmlspecialchars($row->altname, ENT_QUOTES, 'UTF-8'), 0 , 25).'...';
				} else {
					echo htmlspecialchars($row->altname, ENT_QUOTES, 'UTF-8');
				}
				?>
			</td>
			<td align="center"><?php echo $row->size; ?></td>
			<td align="center"><?php echo $row->uploader; ?></td>
			<td align="center"><?php echo JHTML::Date( $row->uploaded, JText::_( 'DATE_FORMAT_LC4' )." H:i:s" ); ?></td>
<?php if (!$this->folder_mode) { ?>
			<td align="center"><?php echo $row->id; ?></td>
<?php } ?>
		</tr>
		<?php 
			$k = 1 - $k;
			$i++;
		} 
		?>
	</tbody>

	</table>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="file" value="" />
	<input type="hidden" name="files" value="<?php echo $this->files; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<input type="hidden" name="fieldid" value="<?php echo $this->fieldid; ?>" />
	<input type="hidden" name="u_item_id" value="<?php echo $this->u_item_id; ?>" />
	<input type="hidden" name="folder_mode" value="<?php echo $this->folder_mode; ?>" />
	<input type="hidden" name="secure" value="0" />
	<input type="hidden" name="filename" value="" />
</form>
</div>