<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
* Name        : Facebook Bot (fb-bot)
* Author      : DulLah
* Version     : 1.1
* Update      : 06 June 2020
* Facebook    : https://www.facebook.com/dulahz
* Telegram    : https://t.me/DulLah
* Whatsapp    : https://wa.me/6282320748574
* Donate      : Ovo/Dana (6282320748574)
*
* Changing/removing the author's name will not make you a real programmer
* Please respect me for making this tool from the beginning. :)
*/

class Tools extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  private function show_banner($title)
  {
    $this->configs->clear();
    $date = date('D M j G:i:s Y');
    echo sprintf($this->configs->banner(), $date, $title);
    $this->climate->out('  +------------------------------------------------------+')->br();
  }

  public function report_bug($title)
  {
    $this->show_banner($title);
    $this->load->model('ReportBug/report_bug');
    $this->report_bug->index();
  }

  public function chat_messages_eraser($title)
  {
    $this->show_banner($title);
    $this->load->model('ChatMessagesEraser/chat_messages_eraser');
    $this->chat_messages_eraser->reset_data();
    $this->chat_messages_eraser->index();
  }

  public function post_eraser($title)
  {
    $this->show_banner($title);
    $this->load->model('PostEraser/post_eraser');
    $this->post_eraser->reset_data();
    $this->post_eraser->index();
  }

  public function friendslist_eraser($title)
  {
    $this->show_banner($title);
    $this->load->model('FriendsListEraser/friendslist_eraser');
    $this->friendslist_eraser->reset_data();
    $this->friendslist_eraser->index();
  }

  public function photo_album_eraser($title)
  {
    $this->show_banner($title);
    $this->load->model('PhotoAlbumEraser/photo_album_eraser');
    $this->photo_album_eraser->reset_data();
    $this->photo_album_eraser->index();
  }

  public function friends_request($title)
  {
    $this->show_banner($title);
    $this->load->model('FriendsRequest/friends_request');
    $this->friends_request->reset_data();
    $this->friends_request->ask_input();
    $this->friends_request->index();
  }

  public function mass_join_groups($title)
  {
    $this->show_banner($title);
    $this->load->model('MassJoinGroups/mass_join_groups');
    $this->mass_join_groups->reset_data();
    $this->mass_join_groups->query_input();
    $this->mass_join_groups->index();
  }

  public function update_status($title)
  {
    $this->show_banner($title);
    $this->load->model('UpdateStatusRandom/update_status');
    $this->update_status->reset_data();
    $this->update_status->index();
  }

  public function mass_chat($title)
  {
    $this->show_banner($title);
    $this->load->model('MassChat/mass_chat');
    $this->mass_chat->reset_data();
    $this->mass_chat->messages_input();
    $this->mass_chat->index();
  }

  public function spam_chat($title)
  {
    $this->show_banner($title);
    $this->load->model('SpamChat/spam_chat');
    $this->spam_chat->reset_data();
    $this->spam_chat->uid_input();
    $this->spam_chat->index();
  }

  public function mass_leave_group($title)
  {
    $this->show_banner($title);
    $this->load->model('MassLeaveGroup/mass_leave_group');
    $this->mass_leave_group->reset_data();
    $this->mass_leave_group->get_group();
    $this->mass_leave_group->index();
  }

  public function mass_react($title)
  {
    $this->show_banner($title);
    $this->load->model('MassReact/mass_react');
    $this->mass_react->reset_data();
    $this->mass_react->index();
  }

  public function mass_comments($title)
  {
    $this->show_banner($title);
    $this->load->model('MassComments/mass_comments');
    $this->mass_comments->reset_data();
    $this->mass_comments->index();
  }

  public function spam_comments($title)
  {
    $this->show_banner($title);
    $this->load->model('SpamComments/spam_comments');
    $this->spam_comments->index();
  }

  public function mass_posting_groups($title)
  {
    $this->show_banner($title);
    $this->load->model('MassPostingGroups/mass_posting_groups');
    $this->mass_posting_groups->reset_data();
    $this->mass_posting_groups->get_group();
    $this->mass_posting_groups->index();
  }

  public function cancel_request_sent($title)
  {
    $this->show_banner($title);
    $this->load->model('CancelRequestSent/cancel_request_sent');
    $this->cancel_request_sent->reset_data();
    $this->cancel_request_sent->index();
  }

  public function unblock_user($title)
  {
    $this->show_banner($title);
    $this->load->model('UnblockUser/unblock_user');
    $this->unblock_user->reset_data();
    $this->unblock_user->index();
  }
}