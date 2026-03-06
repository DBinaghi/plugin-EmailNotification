<?php

/**
 * @version 1.5
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Daniele Binaghi, 2018-2026
 * @package EmailNotification
 */

class EmailNotificationPlugin extends Omeka_Plugin_AbstractPlugin
{
	/**
	 * @var array Hooks for the plugin.
	 */
	protected $_hooks = array(
		'install',
		'uninstall',
		'initialize',
		'config_form',
		'config',
		'before_save_item',
		'before_save_collection',
		'before_save_exhibit',
		'after_save_item',
		'after_save_collection',
		'after_save_exhibit'
	);

	/**
	 * Install the plugin.
	 */
	public function hookInstall()
	{
		set_option('email_notification_new_item', '0');
		set_option('email_notification_new_item_email_subject', __('New Item added'));
		set_option('email_notification_new_item_email_message', __('A new Item has been added to the Omeka repository.'));
		set_option('email_notification_new_collection', '0');
		set_option('email_notification_new_collection_email_subject', __('New Collection added'));
		set_option('email_notification_new_collection_email_message', __('A new Collection has been added to the Omeka repository.'));
		set_option('email_notification_new_exhibit', '0');
		set_option('email_notification_new_exhibit_email_subject', __('New Exhibit added'));
		set_option('email_notification_new_exhibit_email_message', __('A new Exhibit has been added to the Omeka repository.'));
		set_option('email_notification_recipient_address', get_option('administrator_email'));
		set_option('email_notification_notify_editors', '0');
		set_option('email_notification_notify_owner', '0');
		set_option('email_notification_notification_alert', '0');
	}

	/**
	 * Uninstall the plugin.
	 */
	public function hookUninstall()
	{
		delete_option('email_notification_new_item');
		delete_option('email_notification_new_item_email_subject');
		delete_option('email_notification_new_item_email_message');
		delete_option('email_notification_new_collection');
		delete_option('email_notification_new_collection_email_subject');
		delete_option('email_notification_new_collection_email_message');
		delete_option('email_notification_new_exhibit');
		delete_option('email_notification_new_exhibit_email_subject');
		delete_option('email_notification_new_exhibit_email_message');
		delete_option('email_notification_recipient_address');
		delete_option('email_notification_notify_editors');
		delete_option('email_notification_notify_owner');
		delete_option('email_notification_notification_alert');
	}

	/**
	 * Initializes the plugin.
	 */
	public function hookInitialize()
	{
		add_translation_source(dirname(__FILE__) . '/languages');
	}

	/**
	 * Shows plugin configuration page.
	 *
	 * @return void
	 */
	public function hookConfigForm()
	{
		include 'config_form.php';
	}

	/**
	 * Processes the configuration form.
	 *
	 * @return void
	 */
	public function hookConfig($args)
	{
		$post = $args['post'];
		set_option('email_notification_new_item', $post['email_notification_new_item']);
		set_option('email_notification_new_item_email_subject', $post['email_notification_new_item_email_subject']);
		set_option('email_notification_new_item_email_message', $post['email_notification_new_item_email_message']);
		set_option('email_notification_new_collection', $post['email_notification_new_collection']);
		set_option('email_notification_new_collection_email_subject', $post['email_notification_new_collection_email_subject']);
		set_option('email_notification_new_collection_email_message', $post['email_notification_new_collection_email_message']);
		set_option('email_notification_new_exhibit', $post['email_notification_new_exhibit']);
		set_option('email_notification_new_exhibit_email_subject', $post['email_notification_new_exhibit_email_subject']);
		set_option('email_notification_new_exhibit_email_message', $post['email_notification_new_exhibit_email_message']);
		set_option('email_notification_recipient_address', $post['email_notification_recipient_address']);
		set_option('email_notification_notify_editors', $post['email_notification_notify_editors']);
		set_option('email_notification_notify_owner', $post['email_notification_notify_owner']);
		set_option('email_notification_notification_alert', $post['email_notification_notification_alert']);
	}

	/**
	 * Before save item hook.
	 *
	 * @param array $args
	 */
	public function hookBeforeSaveItem($args)
	{
		$this->handleBeforeSave($args, 'item', 'Item');
	}

	/**
	 * Before save collection hook.
	 *
	 * @param array $args
	 */
	public function hookBeforeSaveCollection($args)
	{
		$this->handleBeforeSave($args, 'collection', 'Collection');
	}

	/**
	 * Before save exhibit hook.
	 *
	 * @param array $args
	 */
	public function hookBeforeSaveExhibit($args)
	{
		$this->handleBeforeSave($args, 'exhibit', 'Exhibit');
	}

	/**
	 * After save item hook.
	 *
	 * @param array $args
	 */
	public function hookAfterSaveItem($args)
	{
		if (!$args['post'] || $args['insert'] != 1) {
			return;
		}
		$this->notifyUsers('item', $args['record']);
	}

	/**
	 * After save collection hook.
	 *
	 * @param array $args
	 */
	public function hookAfterSaveCollection($args)
	{
		if (!$args['post'] || $args['insert'] != 1) {
			return;
		}
		$this->notifyUsers('collection', $args['record']);
	}

	/**
	 * After save exhibit hook.
	 *
	 * @param array $args
	 */
	public function hookAfterSaveExhibit($args)
	{
		if (!$args['post'] || $args['insert'] != 1) {
			return;
		}
		$this->notifyUsers('exhibit', $args['record']);
	}

	/**
	 * Shared logic for before_save hooks.
	 * Checks conditions and optionally notifies the record creator.
	 *
	 * FIX OPT-5: eliminates triplication across the three before_save hooks.
	 * FIX 1:     now correctly checks email_notification_notify_owner option
	 *            before calling notifyCreator() (was never checked before).
	 *
	 * @param array  $args
	 * @param string $type        lowercase type: 'item', 'collection', 'exhibit'
	 * @param string $recordClass Omeka record class name: 'Item', 'Collection', 'Exhibit'
	 */
	private function handleBeforeSave($args, $type, $recordClass)
	{
		if (!$args['post'] || $args['insert'] == 1) {
			// new record being inserted, no action required
			return;
		}

		$record = get_record_by_id($recordClass, $args['record']->id);

		if ($record->public == 1) {
			// record is already public, no action required
			return;
		}

		if ($args['post']['public'] == 0) {
			// record is not being made public, no action required
			return;
		}

		if ($record->owner_id == current_user()->id) {
			// record is being published by its own creator, no action required
			return;
		}

		// FIX 1: notify_owner option was never checked in the original code;
		// notifyCreator() was always called unconditionally.
		if (get_option('email_notification_notify_owner')) {
			$this->notifyCreator($type, $record);
		}
	}

	/**
	 * Creates notification for admin/editor users when a new record is added.
	 *
	 * @param string $type   'item', 'collection', or 'exhibit'
	 * @param object $record Omeka record object
	 */
	private function notifyUsers($type, $record)
	{
		$recipientAddress = get_option('email_notification_recipient_address');
		$notifyEditors    = get_option('email_notification_notify_editors');
		$bMessageSent     = false;

		// Early exits: nobody to notify, or notification not enabled for this type
		if (!$recipientAddress && !$notifyEditors) {
			return;
		}
		if (!get_option('email_notification_new_' . $type)) {
			return;
		}
		if ($type == 'exhibit' && !plugin_is_active('ExhibitBuilder')) {
			return;
		}

		// Build common field values
		$title    = ($type == 'exhibit')
			? metadata($record, 'title')
			: metadata($record, array('Dublin Core', 'Title'));
		$date     = metadata($record, 'added');
		$creator  = current_user()->name;
		$public   = ($record->public   == 1 ? __('public')        : __('private'));
		$featured = ($record->featured == 1 ? __('is featured')   : __('is not featured'));

		if ($title == '') {
			$title = __('not provided');
			$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
			$flash->addMessage(__('No title has been provided for the new %s.', __(ucfirst($type))), 'error');
		}

		$subject  = get_option('email_notification_new_' . $type . '_email_subject');
		$bodyHtml = get_option('email_notification_new_' . $type . '_email_message');

		$url_admin  = absolute_url($type . 's/show/' . $record['id'], 'admin');
		$url_public = absolute_url($type . 's/show/' . $record['id']);

		// Build placeholder arrays — common to all types
		$fields = array(
			'{' . $type . '_title}',
			'{' . $type . '_creator}',
			'{' . $type . '_creation_date}',
			'{' . $type . '_public_status}',
			'{' . $type . '_featured_status}',
			'{' . $type . '_admin_url}',
			'{' . $type . '_public_url}',
		);
		$values = array($title, $creator, $date, $public, $featured, $url_admin, $url_public);

		// Extra placeholder only for items: collection title
		if ($type == 'item') {
			$collection_title = metadata($record, 'Collection Name');
			array_splice($fields, 3, 0, array('{item_collection_title}'));
			array_splice($values, 3, 0, array($collection_title));
		}

		$bodyHtml = str_replace($fields, $values, $bodyHtml);
		$bodyHtml .= '<hr>' . __('This is an automatically generated message - please do not reply directly to this e-mail');

		// Send to explicit recipient address(es)
		if ($recipientAddress) {
			$bMessageSent = $this->sendEmail(explode(',', $recipientAddress), $subject, $bodyHtml);
		}

		// Send to all users with edit privilege
		if ($notifyEditors) {
			$acl   = get_acl();
			$users = get_db()->getTable('User')->findAll();
			foreach ($users as $user) {
				if ($acl->isAllowed($user, new Item, 'edit')) {
					if ($this->sendEmail($user->email, $subject, $bodyHtml)) {
						$bMessageSent = true;
					}
				}
			}
		}

		if (get_option('email_notification_notification_alert') && $bMessageSent) {
			$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
			$flash->addMessage(__('Admin/editors have been notified about the new addition.'), 'success');
		}
	}

	/**
	 * Creates notification for the original record creator when it is made public.
	 *
	 * @param string $type   'item', 'collection', or 'exhibit'
	 * @param object $record Omeka record object
	 */
	private function notifyCreator($type, $record)
	{
		$recipientAddress = $record->getOwner()->email;

		if ($recipientAddress == '') {
			return;
		}

		$title          = metadata($record, array('Dublin Core', 'Title'));
		$editor         = current_user()->name;
		$type_localized = __($type);
		if (is_array($type_localized)) {
			$type_localized = $type_localized[0];
		}

		$subject = __('%s made public', ucfirst($type_localized));

		$url_public = absolute_url($type . 's/show/' . $record['id']);

		$bodyHtml  = '<p>' . __('Hello!') . '</p>';
		$bodyHtml .= '<p>' . __('Your %s "<b>%s</b>" has just been made public by <b>%s</b>.', $type_localized, $title, $editor) . '</p>';
		$bodyHtml .= '<p>' . __('The %s is now available at the page %s.', $type_localized, $url_public) . '</p>';
		$bodyHtml .= '<hr>' . __('This is an automatically generated message - please do not reply directly to this e-mail');

		$bMessageSent = $this->sendEmail($recipientAddress, $subject, $bodyHtml);

		if (get_option('email_notification_notification_alert') && $bMessageSent) {
			$type_localized = __($type);
			if (is_array($type_localized)) {
				$type_localized = $type_localized[0];
			}
			$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
			$flash->addMessage(__('The owner of the %s has been notified of the publishing.', $type_localized), 'success');
		}
	}

	/**
	 * Sends an HTML e-mail via Zend_Mail.
	 *
	 * @param string|array $recipient  Single address or array of addresses
	 * @param string       $subject
	 * @param string       $body       HTML body
	 *
	 * @return bool  true on success, false on empty input or send failure
	 */
	private function sendEmail($recipient, $subject, $body)
	{
		if ($recipient == '' || $subject == '' || $body == '') {
			return false;
		}

		try {
			$email = new Zend_Mail('utf-8');
			$email->setFrom(get_option('administrator_email'))
				->addTo($recipient)
				->setSubject($subject)
				->setBodyHtml($body)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->send();
			return true;
		} catch (Exception $e) {
			_log(
				'EmailNotification: failed to send email to '
				. (is_array($recipient) ? implode(', ', $recipient) : $recipient)
				. ' — ' . $e->getMessage(),
				Zend_Log::ERR
			);
			return false;
		}
	}
}
