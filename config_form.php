<?php echo js_tag('vendor/tinymce/tinymce.min'); ?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		Omeka.wysiwyg({
			selector: '.html-editor'
		});
	});
</script>

<?php
$new_item						= get_option('email_notification_new_item');
$new_item_email_subject			= get_option('email_notification_new_item_email_subject');
$new_item_email_message			= get_option('email_notification_new_item_email_message');
$new_collection					= get_option('email_notification_new_collection');	
$new_collection_email_subject	= get_option('email_notification_new_collection_email_subject');
$new_collection_email_message	= get_option('email_notification_new_collection_email_message');
$new_exhibit					= get_option('email_notification_new_exhibit');	
$new_exhibit_email_subject		= get_option('email_notification_new_exhibit_email_subject');
$new_exhibit_email_message		= get_option('email_notification_new_exhibit_email_message');
$recipient_address				= get_option('email_notification_recipient_address');
$notify_editors					= get_option('email_notification_notify_editors');
$message_sent					= get_option('email_notification_message_sent');
$view = get_view();
?>

<h2><?php echo __('New Item addition'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_item', __('Enable notification')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, sends a notification every time a new Item is added.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_new_item', $new_item, null, array('1', '0')); ?>
	</div>
</div>

 <div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_item_email_subject', __('E-mail subject')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The subject line for the notification e-mail sent to recipients.'); ?>
		</p>
		<?php echo $view->formText('email_notification_new_item_email_subject', $new_item_email_subject); ?>
	</div>
</div>

 <div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_item_email_message', __('E-mail message')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The notification message to be sent to recipients. User can insert any of the following fields (within curly braces) in the text, as many times as they want; they will be substituted by the actual values when the message is sent: %s', '<b>{item_title}</b>, <b>{item_creator}</b>, <b>{item_creation_date}</b>, <b>{item_collection_title}</b>, <b>{item_public_status}</b>, <b>{item_featured_status}</b>, <b>{item_admin_url}</b>, <b>{item_public_url}</b>') ?>
		</p>
		<?php echo $view->formTextarea('email_notification_new_item_email_message', $new_item_email_message, array('rows' => '10', 'cols' => '60', 'class' => array('html-editor'))); ?>
	</div>
</div>

<p>&nbsp;</p>

<h2><?php echo __('New Collection addition'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_collection', __('Enable notification')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, sends a notification every time a new Collection is added.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_new_collection', $new_collection, null, array('1', '0')); ?>
	</div>
</div>

 <div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_collection_email_subject', __('E-mail subject')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The subject line for the notification e-mail to be sent to recipients.'); ?>
		</p>
		<?php echo $view->formText('email_notification_new_collection_email_subject', $new_collection_email_subject); ?>
	</div>
</div>

 <div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_collection_email_message', __('E-mail message')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The notification message to be sent to recipients. User can insert any of the following fields (within curly braces) in the text, as many times as they want; they will be substituted by the actual values when the message is sent: %s', '<b>{collection_title}</b>, <b>{collection_creator}</b>, <b>{collection_creation_date}</b>, <b>{collection_public_status}</b>, <b>{collection_featured_status}</b>, <b>{collection_admin_url}</b>, <b>{collection_public_url}</b>'); ?>
		</p>
		<?php echo $view->formTextarea('email_notification_new_collection_email_message', $new_collection_email_message, array('rows' => '10', 'cols' => '60', 'class' => array('html-editor'))); ?>
	</div>
</div>

<p>&nbsp;</p>

<h2><?php echo __('New Exhibit addition'); ?></h2>

<?php if (plugin_is_active('ExhibitBuilder')): ?>
<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_exhibit', __('Enable notification')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, sends a notification every time a new Exhibit is added.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_new_exhibit', $new_exhibit, null, array('1', '0')); ?>
	</div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_exhibit_email_subject', __('E-mail subject')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The subject line for the notification e-mail sent to recipients.'); ?>
		</p>
		<?php echo $view->formText('email_notification_new_exhibit_email_subject', $new_exhibit_email_subject); ?>
	</div>
</div>

 <div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_exhibit_email_message', __('E-mail message')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The notification message to be sent to recipients. User can insert any of the following fields (within curly braces) in the text, as many times as they want; they will be substituted by the actual values when the message is sent: %s', '<b>{exhibit_title}</b>, <b>{exhibit_creator}</b>, <b>{exhibit_creation_date}</b>, <b>{exhibit_public_status}</b>, <b>{exhibit_featured_status}</b>, <b>{exhibit_admin_url}</b>, <b>{exhibit_public_url}</b>') ?>
		</p>
		<?php echo $view->formTextarea('email_notification_new_exhibit_email_message', $new_exhibit_email_message, array('rows' => '10', 'cols' => '60', 'class' => array('html-editor'))); ?>
	</div>
</div>

<?php else: ?>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_exhibit_email_message', __('Plugin not installed/active')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The Exhibit Builder plugin is not installed or active. Install and activate the plugin in order to be able to configure notifications for new Exhibits.'); ?>
		</p>
	</div>
</div>	
<?php endif; ?>

<h2><?php echo __('Recipients'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_recipient_address', __('Recipient e-mail address(es)')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The address(es) that will receive the notification. Multiple addresses must be separated by a comma (,).'); ?>
		</p>
		<?php echo $view->formInput('email_notification_recipient_address', $recipient_address, array('type'=>'email','multiple'=>'multiple')); ?>
	</div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_notify_editors', __('Notify editors')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, system sends a notification of new submissions also to users with editing role.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_notify_editors', $notify_editors, null, array('1', '0')); ?>
	</div>
</div>

<h2><?php echo __('Alerts'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_message_sent', __('Alert contributor')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, system shows an alert informing contributor about the notification.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_message_sent', $message_sent, null, array('1', '0')); ?>
	</div>
</div>