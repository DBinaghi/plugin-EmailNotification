<?php

/**
 * @version $Id$
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Daniele Binaghi, 2018-2019
 * @package EmailNotification
 */

 
// Define Constants
define('EMAIL_NOTIFICATION_NEW_ITEM_EMAIL_SUBJECT', __('New Item added'));
define('EMAIL_NOTIFICATION_NEW_ITEM_EMAIL_MESSAGE', __('A new Item has been added to the Omeka repository.'));
define('EMAIL_NOTIFICATION_NEW_COLLECTION_EMAIL_SUBJECT', __('New Collection added'));
define('EMAIL_NOTIFICATION_NEW_COLLECTION_EMAIL_MESSAGE', __('A new Collection has been added to the Omeka repository.'));
define('EMAIL_NOTIFICATION_NEW_EXHIBIT_EMAIL_SUBJECT', __('New Exhibit added'));
define('EMAIL_NOTIFICATION_NEW_EXHIBIT_EMAIL_MESSAGE', __('A new Exhibit has been added to the Omeka repository.'));
define('EMAIL_NOTIFICATION_MESSAGE_SENT', __('Admin/editors have been notified about the new addition.'));

class EmailNotificationPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_hooks = array(
		'install',
		'uninstall',
		'initialize',
		'config',
		'config_form',
		'after_save_item',
		'after_save_collection',
		'after_save_exhibit'
	);

	public function hookInstall()
	{
		set_option('email_notification_new_item', '0');	
		set_option('email_notification_new_item_email_subject', EMAIL_NOTIFICATION_NEW_ITEM_EMAIL_SUBJECT);
		set_option('email_notification_new_item_email_message', EMAIL_NOTIFICATION_NEW_ITEM_EMAIL_MESSAGE);
		set_option('email_notification_new_collection', '0');
		set_option('email_notification_new_collection_email_subject', EMAIL_NOTIFICATION_NEW_COLLECTION_EMAIL_SUBJECT);
		set_option('email_notification_new_collection_email_message', EMAIL_NOTIFICATION_NEW_COLLECTION_EMAIL_MESSAGE);
		set_option('email_notification_new_exhibit', '0');
		set_option('email_notification_new_exhibit_email_subject', EMAIL_NOTIFICATION_NEW_EXHIBIT_EMAIL_SUBJECT);
		set_option('email_notification_new_exhibit_email_message', EMAIL_NOTIFICATION_NEW_EXHIBIT_EMAIL_MESSAGE);
		set_option('email_notification_recipient_address', get_option('administrator_email'));
		set_option('email_notification_notify_editors', '0');
		set_option('email_notification_message_sent', '0');
	}

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
		delete_option('email_notification_message_sent');
	 }

	public function hookInitialize()
	{
		add_translation_source(dirname(__FILE__) . '/languages');
	}
	
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
		set_option('email_notification_message_sent', $post['email_notification_message_sent']);
	}
	
	public function hookConfigForm()
	{
		include 'config_form.php';
	}
	
	public function hookAfterSaveItem($args)
	{
		if (!$args['post'] || $args['insert'] != 1) {
			return;
		}
		$this->sendNotification('item', $args['record']);
	}

	public function hookAfterSaveCollection($args)
	{
		if (!$args['post'] || $args['insert'] != 1) {
			return;
		}
		$this->sendNotification('collection', $args['record']);
	}

	public function hookAfterSaveExhibit($args)
	{
		if (!$args['post'] || $args['insert'] != 1) {
			return;
		}
		$this->sendNotification('exhibit', $args['record']);
	}

	public function sendNotification($type, $record) {
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
					$message = __('No title has been provided for the new Item.');
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
					$recipientAddresses = explode(',', $recipientAddress);
					// sends e-mail to recipient address(es)
					$email = new Zend_Mail('utf-8');
					$email->setBodyHtml($bodyHtml);
					$email->setFrom(get_option('administrator_email'));
					$email->addTo($recipientAddresses);
					$email->setSubject($subject);
					$email->send();
					$bMessageSent = true;
				}
				
				if ($notifyEditors) {
					// sends e-mail to all users with general 'edit' privilege
					$acl = get_acl();
					$users = get_db()->getTable('User')->findAll();
					foreach ($users as $key => $user):
						if ($acl->isAllowed($user, new Item, 'edit')):
							$email = new Zend_Mail('utf-8');
							$email->setBodyHtml($bodyHtml);
							$email->setFrom(get_option('administrator_email'));
							$email->addTo($user->email);
							$email->setSubject($subject);
							$email->send();
							$bMessageSent = true;
						endif;
					endforeach;
				}
				
				if (get_option('email_notification_message_sent') && $bMessageSent) {
					// shows alert for new item/collection/exhibit creator
					$message = __(EMAIL_NOTIFICATION_MESSAGE_SENT);
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'success');
				}
			}
		}
	}
}