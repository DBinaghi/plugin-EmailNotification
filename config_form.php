<?php echo js_tag('vendor/tinymce/tinymce.min'); ?>
<script type="text/javascript">
jQuery(document).ready(function () {
	Omeka.wysiwyg({
		selector: '.html-editor'
	});
});
</script>

<?php
$new_item			= get_option('email_notification_new_item');
$new_item_email_subject		= get_option('email_notification_new_item_email_subject');
$new_item_email_message 	= get_option('email_notification_new_item_email_message');
$new_collection			= get_option('email_notification_new_collection');	
$new_collection_email_subject	= get_option('email_notification_new_collection_email_subject');
$new_collection_email_message 	= get_option('email_notification_new_collection_email_message');
$recipient_address		= get_option('email_notification_recipient_address');
$notify_editors			= get_option('email_notification_notify_editors');
$message_sent			= get_option('email_notification_message_sent');
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
			<?php echo __('The subject line for the notification e-mail sent to the recipients.'); ?>
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
			<?php echo __('The beginning of the notification message sent to the recipients.') ?>
		</p>
		<?php echo $view->formTextarea('email_notification_new_item_email_message', $new_item_email_message, array('rows' => '10', 'cols' => '60', 'class' => array('html-editor'))); ?>
	</div>
</div>

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
			<?php echo __('The subject line for the notification e-mail sent to the recipients.'); ?>
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
			<?php echo __('The beginning of the notification message sent to the recipients.'); ?>
		</p>
		<?php echo $view->formTextarea('email_notification_new_collection_email_message', $new_collection_email_message, array('rows' => '10', 'cols' => '60', 'class' => array('html-editor'))); ?>
	</div>
</div>

<h2><?php echo __('Recipients'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_recipient_address', __('Recipient e-mail address')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The address that will receive the notification.'); ?>
		</p>
		<?php echo $view->formText('email_notification_recipient_address', $recipient_address); ?>
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
		<?php echo $view->formLabel('email_notification_message_sent', __('Alert contributor about notification')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, system shows an alert informing contributor about the notification.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_message_sent', $message_sent, null, array('1', '0')); ?>
	</div>
</div>
