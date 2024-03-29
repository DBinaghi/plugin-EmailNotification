<?php echo js_tag('vendor/tinymce/tinymce.min'); ?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		Omeka.wysiwyg({
			selector: '.html-editor'
		});
	});
</script>

<?php
	$view = get_view();
?>

<h2><?php echo __('New Item Addition'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_item', __('Enable Notification')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, sends a notification every time a new Item is added.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_new_item', get_option('email_notification_new_item'), null, array('1', '0')); ?>
	</div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_item_email_subject', __('E-mail Subject')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The subject line for the notification e-mail sent to recipients.'); ?>
		</p>
		<?php echo $view->formText('email_notification_new_item_email_subject', get_option('email_notification_new_item_email_subject')); ?>
	</div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_item_email_message', __('E-mail Message')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The notification message to be sent to recipients. User can insert any of the following fields (within curly braces) in the text, as many times as they want; they will be substituted by the actual values when the message is sent: %s', '<b>{item_title}</b>, <b>{item_creator}</b>, <b>{item_creation_date}</b>, <b>{item_collection_title}</b>, <b>{item_public_status}</b>, <b>{item_featured_status}</b>, <b>{item_admin_url}</b>, <b>{item_public_url}</b>') ?>
		</p>
		<?php echo $view->formTextarea('email_notification_new_item_email_message', get_option('email_notification_new_item_email_message'), array('rows' => '10', 'cols' => '60', 'class' => array('html-editor'))); ?>
	</div>
</div>

<h2><?php echo __('New Collection Addition'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_collection', __('Enable Notification')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, sends a notification every time a new Collection is added.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_new_collection', get_option('email_notification_new_collection'), null, array('1', '0')); ?>
	</div>
</div>

 <div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_collection_email_subject', __('E-mail Subject')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The subject line for the notification e-mail sent to recipients.'); ?>
		</p>
		<?php echo $view->formText('email_notification_new_collection_email_subject', get_option('email_notification_new_collection_email_subject')); ?>
	</div>
</div>

 <div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_collection_email_message', __('E-mail Message')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The notification message to be sent to recipients. User can insert any of the following fields (within curly braces) in the text, as many times as they want; they will be substituted by the actual values when the message is sent: %s', '<b>{collection_title}</b>, <b>{collection_creator}</b>, <b>{collection_creation_date}</b>, <b>{collection_public_status}</b>, <b>{collection_featured_status}</b>, <b>{collection_admin_url}</b>, <b>{collection_public_url}</b>'); ?>
		</p>
		<?php echo $view->formTextarea('email_notification_new_collection_email_message', get_option('email_notification_new_collection_email_message'), array('rows' => '10', 'cols' => '60', 'class' => array('html-editor'))); ?>
	</div>
</div>

<h2><?php echo __('New Exhibit Addition'); ?></h2>

<?php if (plugin_is_active('ExhibitBuilder')): ?>
<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_exhibit', __('Enable Notification')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, sends a notification every time a new Exhibit is added.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_new_exhibit', get_option('email_notification_new_exhibit'), null, array('1', '0')); ?>
	</div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_exhibit_email_subject', __('E-mail Subject')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The subject line for the notification e-mail sent to recipients.'); ?>
		</p>
		<?php echo $view->formText('email_notification_new_exhibit_email_subject', get_option('email_notification_new_exhibit_email_subject')); ?>
	</div>
</div>

 <div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_exhibit_email_message', __('E-mail Message')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The notification message to be sent to recipients. User can insert any of the following fields (within curly braces) in the text, as many times as they want; they will be substituted by the actual values when the message is sent: %s', '<b>{exhibit_title}</b>, <b>{exhibit_creator}</b>, <b>{exhibit_creation_date}</b>, <b>{exhibit_public_status}</b>, <b>{exhibit_featured_status}</b>, <b>{exhibit_admin_url}</b>, <b>{exhibit_public_url}</b>') ?>
		</p>
		<?php echo $view->formTextarea('email_notification_new_exhibit_email_message', get_option('email_notification_new_exhibit_email_message'), array('rows' => '10', 'cols' => '60', 'class' => array('html-editor'))); ?>
	</div>
</div>

<?php else: ?>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_new_exhibit_email_message', __('Plugin Unavailable')); ?>
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
		<?php echo $view->formLabel('email_notification_recipient_address', __('Recipient Address')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The address(es) that will receive the notification. Multiple addresses must be separated by a comma (,).'); ?>
		</p>
		<?php echo $view->formInput('email_notification_recipient_address', get_option('email_notification_recipient_address'), array('type'=>'email','multiple'=>'multiple')); ?>
	</div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_notify_editors', __('Notify Editors')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, system sends a notification of new submissions also to users with editing role.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_notify_editors', get_option('email_notification_notify_editors'), null, array('1', '0')); ?>
	</div>
</div>

<h2><?php echo __('Publishing'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_notify_owner', __('Notify Owner')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, system sends a notification to original owner when an Item / Collection / Exhibit they contributed is made public.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_notify_owner', get_option('email_notification_notify_owner'), null, array('1', '0')); ?>
	</div>
</div>

<h2><?php echo __('Alerts'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('email_notification_notification_alert', __('Notification Alert')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, system shows an alert informing contributor that notifications have been sent.'); ?>
		</p>
		<?php echo $view->formCheckbox('email_notification_notification_alert', get_option('email_notification_notification_alert'), null, array('1', '0')); ?>
	</div>
</div>
