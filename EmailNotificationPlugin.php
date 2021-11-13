<?php

/**
 * @version 1.4
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Daniele Binaghi, 2018-2021
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
		if (!$args['post'] || $args['insert'] == 1) {
			// saving new item, so no action is required
			return;
		}

		$record = $args['record'];
		$item = get_record_by_id('Item', $record->id);

		if ($item->public == 1) {
			// item is already public, so no action is required
			return;
		}

		if ($args['post']['public'] == 0) {
			// item hasn't been made public, so no action is required
			return;
		}

		if ($item->owner_id == current_user()->id) {
			// item has been edited by creator, so no action is required
			return;
		}

		// alert creator
		$this->notifyCreator('item', $item);
	}

	/**
	 * Before save collection hook.
	 *
	 * @param array $args
	 */
	public function hookBeforeSaveCollection($args)
	{
		if (!$args['post'] || $args['insert'] == 1) {
			// saving new collection, so no action is required
			return;
		}

		$record = $args['record'];
		$collection = get_record_by_id('Collection', $record->id);

		if ($collection->public == 1) {
			// collection is already public, so no action is required
			return;
		}

		if ($args['post']['public'] == 0) {
			// collection hasn't been made public, so no action is required
			return;
		}

		if ($collection->owner_id == current_user()->id) {
			// collection has been edited by creator, so no action is required
			return;
		}

		// alert creator
		$this->notifyCreator('collection', $collection);
	}

	/**
	 * Before save exhibit hook.
	 *
	 * @param array $args
	 */
	public function hookBeforeSaveExhibit($args)
	{
		if (!$args['post'] || $args['insert'] == 1) {
			// saving new exhibit, so no action is required
			return;
		}

		$record = $args['record'];
		$exhibit = get_record_by_id('Exhibit', $record->id);

		if ($exhibit->public == 1) {
			// exhibit is already public, so no action is required
			return;
		}

		if ($args['post']['public'] == 0) {
			// exhibit hasn't been made public, so no action is required
			return;
		}

		if ($exhibit->owner_id == current_user()->id) {
			// exhibit has been edited by creator, so no action is required
			return;
		}

		// alert creator
		$this->notifyCreator('exhibit', $exhibit);
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
	 * Creates notification for users.
	 *
	 * @param string $type
	 * @param array $record
	 */
	private function notifyUsers($type, $record) {
		$recipientAddress = get_option('email_notification_recipient_address');
		$notifyEditors = get_option('email_notification_notify_editors');
		$bodyHtml = '';
		$subject = '';
		$bMessageSent = false;

		if ($recipientAddress != '' || $notifyEditors) {
			// creates e-mail elements
			$title = metadata($record, array('Dublin Core', 'Title'));
			$creator = current_user()->name;
			$date = metadata($record, 'added');
			$public = ($record->public == 1 ? __('public') : __('private'));
			$featured = ($record->featured == 1 ? __('is featured') : __('is not featured'));

			if ($type == 'item' && get_option('email_notification_new_item')) {
				$subject =  get_option('email_notification_new_item_email_subject');
				$bodyHtml = get_option('email_notification_new_item_email_message');
				$url_admin = $_SERVER['HTTP_HOST'] . '/admin/items/show/id/' . $record['id'];
				$url_public = $_SERVER['HTTP_HOST'] . '/items/show/id' . $record['id'];
				$collection_title = metadata($record, 'Collection Name');
				if ($title == '') {
					$title = __('not provided');
					$message = __('No title has been provided for the new Item');
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'error');
				}
				$fields = array('{item_title}', '{item_creator}', '{item_creation_date}', '{item_collection_title}', '{item_public_status}', '{item_featured_status}', '{item_admin_url}', '{item_public_url}');
				$values = array($title, $creator, $date, $collection_title, $public, $featured, $url_admin, $url_public);
				$bodyHtml = str_replace($fields, $values, $bodyHtml);
			} elseif ($type == 'collection' && get_option('email_notification_new_collection')) {
				$subject =  get_option('email_notification_new_collection_email_subject');
				$bodyHtml = get_option('email_notification_new_collection_email_message');
				$url_admin = $_SERVER['HTTP_HOST'] . '/admin/collections/show/id/' . $record['id'];
				$url_public = $_SERVER['HTTP_HOST'] . '/collections/show/id' . $record['id'];
				if ($title == '') {
					$title = __('not provided');
					$message = __('No title has been provided for the new Collection.');
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'error');
				}
				$fields = array('{collection_title}', '{collection_creator}', '{collection_creation_date}', '{collection_public_status}', '{collection_featured_status}', '{collection_admin_url}', '{collection_public_url}');
				$values = array($title, $creator, $date, $public, $featured, $url_admin, $url_public);
				$bodyHtml = str_replace($fields, $values, $bodyHtml);
			} elseif ($type == 'exhibit' && get_option('email_notification_new_exhibit') && plugin_is_active('ExhibitBuilder')) {
				$subject =  get_option('email_notification_new_exhibit_email_subject');
				$bodyHtml = get_option('email_notification_new_exhibit_email_message');
				$url_admin = $_SERVER['HTTP_HOST'] . '/admin/exhibits/show/id/' . $record['id'];
				$url_public = $_SERVER['HTTP_HOST'] . '/exhibits/show/id' . $record['id'];
				if ($title == '') {
					$title = __('not provided');
					$message = __('No title has been provided for the new Exhibit.');
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'error');
				}
				$fields = array('{exhibit_title}', '{exhibit_creator}', '{exhibit_creation_date}', '{exhibit_public_status}', '{exhibit_featured_status}', '{exhibit_admin_url}', '{exhibit_public_url}');
				$values = array($title, $creator, $date, $public, $featured, $url_admin, $url_public);
				$bodyHtml = str_replace($fields, $values, $bodyHtml);
			}

			if ($bodyHtml != '' && $subject != '') {
				$bodyHtml .= '<hr>' . __('This is an automatically generated message - please do not reply directly to this e-mail');
				if ($recipientAddress != '') {
					// sends e-mail to recipient address(es)
					$bMessageSent = $this->sendEmail(explode(',', $recipientAddress), $subject, $bodyHtml);
				}

				if ($notifyEditors) {
					// sends e-mail to all users with general 'edit' privilege
					$acl = get_acl();
					$users = get_db()->getTable('User')->findAll();
					foreach ($users as $key => $user) {
						if ($acl->isAllowed($user, new Item, 'edit')) {
							if ($this->sendEmail($user->email, $subject, $bodyHtml)) $bMessageSent = true;
						}
					}
				}

				if (get_option('email_notification_message_sent') && $bMessageSent) {
					// shows alert for new item/collection/exhibit creator
					$message = __('Admin/editors have been notified about the new addition.');
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'success');
				}
			}
		}
	}

	/**
	 * Creates notification for creator.
	 *
	 * @param string $type
	 * @param array $record
	 */
	private function notifyCreator($type, $record) {
		$bodyHtml = '';
		$subject = '';
		$recipientAddress = $record->getOwner()->email;

		if ($recipientAddress != '') {
			// creates e-mail elements
			$title = metadata($record, array('Dublin Core', 'Title'));
			$editor = current_user()->name;
			$type_localized = __($type);
			if (is_array($type_localized)) $type_localized = $type_localized[0];
			$subject = __('%s made public', ucfirst($type_localized));
			$url_public = $_SERVER['HTTP_HOST'] . '/' . $type . 's/show/id' . $record['id'];
			$bodyHtml  = '<p>' . __('Hello!') . '</p>';
			$bodyHtml .= '<p>' . __('Your %s "<b>%s</b>" has just been made public by <b>%s</b>.', $type_localized, $title, $editor) . '</p>';
			$bodyHtml .= '<p>' . __('The %s is now available at the page %s.', $type_localized, $url_public) . '</p>';

			if ($bodyHtml != '' && $subject != '') {
				$bodyHtml .= '<hr>' . __('This is an automatically generated message - please do not reply directly to this e-mail');
				if ($recipientAddress != '') {
					// sends e-mail to recipient address
					$bMessageSent = $this->sendEmail($recipientAddress, $subject, $bodyHtml);
				}

				if (get_option('email_notification_notification_alert') && $bMessageSent) {
					// shows alert for publisher
					$message = __('The owner of the %s has been notified of the publishing.', $type_localized);
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'success');
				}
			}
		}
	}

	/**
	 * Sends e-mail.
	 *
	 * @param string $recipient
	 * @param string $subject
	 * @param string $body
	 *
	 * returns boolean
	 */
	private function sendEmail($recipient, $subject, $body) {
		if ($recipient == '' || $subject == '' || $body == '') return false;

		$email = new Zend_Mail('utf-8');
		$email->setFrom(get_option('administrator_email'))
			->addTo($recipient)
			->setSubject($subject)
			->setBodyHtml($body)
			->addHeader('X-Mailer', 'PHP/' . phpversion())
			->send();
		return true;
	}
}
