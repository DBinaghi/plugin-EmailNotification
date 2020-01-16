<?php

/**
 * @version $Id$
 * @license CeCILL v2.1
 * @copyright Daniele Binaghi, 2018-2019
 * @package EmailNotification
 */

 
// Define Constants
define('EMAIL_NOTIFICATION_NEW_ITEM_EMAIL_SUBJECT', __('New Item added'));
define('EMAIL_NOTIFICATION_NEW_ITEM_EMAIL_MESSAGE', __('A new Item has been added to the Omeka repository.'));
define('EMAIL_NOTIFICATION_NEW_COLLECTION_EMAIL_SUBJECT', __('New Collection added'));
define('EMAIL_NOTIFICATION_NEW_COLLECTION_EMAIL_MESSAGE', __('A new Collection has been added to the Omeka repository.'));
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
		'after_save_collection'
	);

	public function hookInstall()
	{
		set_option('email_notification_new_item', '0');	
		set_option('email_notification_new_item_email_subject', EMAIL_NOTIFICATION_NEW_ITEM_EMAIL_SUBJECT);
		set_option('email_notification_new_item_email_message', EMAIL_NOTIFICATION_NEW_ITEM_EMAIL_MESSAGE);
		set_option('email_notification_new_collection', '0');
		set_option('email_notification_new_collection_email_subject', EMAIL_NOTIFICATION_NEW_COLLECTION_EMAIL_SUBJECT);
		set_option('email_notification_new_collection_email_message', EMAIL_NOTIFICATION_NEW_COLLECTION_EMAIL_MESSAGE);
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
		$recordId = $args['record']['id'];
		
		$this->sendNotification('item', $recordId);
	}

	public function hookAfterSaveCollection($args)
	{
		if (!$args['post'] || $args['insert'] != 1) {
			return;
		}
		$recordId = $args['record']['id'];

		$this->sendNotification('collection', $recordId);
	}

	public function sendNotification($type, $recordId) {
		$recipientAddress = get_option('email_notification_recipient_address');
		$notifyEditors = get_option('email_notification_notify_editors');
		$bodyHtml = '';
		$subject = '';
		$bMessageSent = false;

		
		if ($recipientAddress != '' || $notifyEditors) {
			// creates e-mail elements
			if ($type == 'item' && get_option('email_notification_new_item')) {
				$O_R_AR = get_record_by_id('item', $recordId);
				$bodyHtml = get_option('email_notification_new_item_email_message');
				$title = metadata($O_R_AR, array('Dublin Core', 'Title'));
				$adder = current_user()->name;
				$status = ($O_R_AR->public == 1 ? __('public') : __('private'));
				if ($title != '') {
					$bodyHtml .= '<p>' . __('The title of the new Item, added by <b>%s</b>, is <b>%s</b>' , $adder, $title) . '.</p>';
					$bodyHtml .= '<p>' . __('The status of the new Item is <b>%s</b>', $status) . '.</p>';
					$bodyHtml .= '<p>' . __('Please check that all data are correct, by clicking') . ' <a href="' . $_SERVER['HTTP_HOST'] . '/admin/items/show/id/' . $recordId . '">' . __('this link') . '</a>.</p>';
				} else {
					$bodyHtml .= '<p>' . __('The new Item, added by <b>%s</b>, does not have a title yet', $adder) . '.</p>';
					$bodyHtml .= '<p>' . __('The status of the new Item is <b>%s</b>', $status) . '.</p>';
					$bodyHtml .= '<p>' . __('Please check that all data are correct, by clicking') . ' <a href="' . $_SERVER['HTTP_HOST'] . '/admin/items/show/id/' . $recordId . '">' . __('this link') . '</a>.</p>';
					$message = __('No title has been provided for the new Item.');
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'error');
				}
				$subject =  get_option('email_notification_new_item_email_subject');
			} elseif ($type == 'collection' && get_option('email_notification_new_collection')) {
				$O_R_AR = get_record_by_id('collection', $recordId);
				$bodyHtml = get_option('email_notification_new_collection_email_message');
				$title = metadata($O_R_AR, array('Dublin Core', 'Title'));
				$adder = current_user()->name;
				$status = ($O_R_AR->public == 1 ? __('public') : __('private'));
				if ($title != '') {
					$bodyHtml .= '<p>' . __('The title of the new Collection, added by <b>%s</b>, is <b>%s</b>', $adder, $title) . '.</p>';
					$bodyHtml .= '<p>' . __('The status of the new Collection is <b>%s</b>', $status) . '.</p>';
					$bodyHtml .= '<p>' . __('Please check that all data are correct, by clicking') . ' <a href="' . $_SERVER['HTTP_HOST'] . '/admin/collections/show/id/' . $recordId . '">' . __('this link') . '</a>.</p>';
				} else {
					$bodyHtml .= '<p>' . __('The new Collection, added by <b>%s</b>, does not have a title yet', $adder) . '.</p>';
					$bodyHtml .= '<p>' . __('The status of the new Collection is <b>%s</b>', $status) . '.</p>';
					$bodyHtml .= '<p>' . __('Please check that all data are correct, by clicking') . ' <a href="' . $_SERVER['HTTP_HOST'] . '/admin/collections/show/id/' . $recordId . '">' . __('this link') . '</a>.</p>';
					$message = __('No title has been provided for the new Collection.');
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'error');
				}
				$subject =  get_option('email_notification_new_collection_email_subject');
			}
			
			if ($bodyHtml != '' && $subject != '') {
				if ($recipientAddress != '') {
					// sends e-mail to recipient address
					$email = new Zend_Mail('utf-8');
					$email->setBodyHtml($bodyHtml);
					$email->setFrom(get_option('administrator_email'));
					$email->addTo($recipientAddress);
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
					$message = __(EMAIL_NOTIFICATION_MESSAGE_SENT);
					$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
					$flash->addMessage($message, 'success');
				}
			}
		}
	}
}
