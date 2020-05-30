<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
* Author      : DulLah
* Name        : Facebook Bot (fb-bot)
* Version     : 1.0
* Update      : 30 mei 2020
* Facebook    : https://www.facebook.com/dulahz
* Telegram    : https://t.me/unikers
*
* Changing/removing the author's name will not make you a real programmer
* Please respect me for making this tool from the beginning. :)
*/

class Chat_messages_eraser extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  public function reset_data()
  {
    $this->url = $this->base_url.'/messages';
    /*
      @Array data
    */
    $this->data = [];
    /*
      @Array params
    */
    $this->params = [];

    $this->username = '';
  }

  public function index()
  {
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($hrefs, '/messages/read') !== false)
      {
        $this->data[] = $this->base_url.$hrefs;
        $this->climate->inline("\r  Collecting (".count($this->data).") chat messages...");
      }
    }
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($href->nodeValue, 'Lihat Pesan Sebelumnya') !== false)
      {
        $this->url = $this->base_url.$hrefs;
        $this->index();
        break;
      }
    }
    if (count($this->data) !== 0)
    {
      $this->climate->br()->br()->shout('  Starting delete '.count($this->data).' messages...')->br();
      $this->delete_messages();
    }
    else
    {
      $this->climate->shout('  No messages found!');
      $this->configs->back_menu();
    }
  }

  public function delete_messages()
  {
    foreach ($this->data as $chat)
    {
      $response = $this->configs->request_get($chat, $this->cookies);
      $dom = new DOMDocument();
      @$dom->loadHTML($response);
      $this->params = [];
      $name = $dom->getElementsByTagName('title');
      $this->username = $name->item(0)->nodeValue;
      foreach ($dom->getElementsByTagName('form') as $form)
      {
        $action = $form->getAttribute('action');
        if (strpos($action, '/messages/action_redirect') !== false)
        {
          $this->params['action'] = $this->base_url.$action;
          break;
        }
      }
      foreach ($dom->getElementsByTagName('input') as $input)
      {
        $name = $input->getAttribute('name');
        $value = $input->getAttribute('value');
        if (trim($name) == 'fb_dtsg')
        {
          $this->params['fb_dtsg'] = $value;
        }
        if (trim($name) == 'jazoest')
        {
          $this->params['jazoest'] = $value;
          break;
        }
      }
      if (count($this->params) == 3)
      {
        $this->execute();
      }
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
  }

  public function execute()
  {
    $this->params['delete'] = 'Hapus';
    $post = http_build_query($this->params);
    $response = $this->configs->request_post($this->params['action'], $this->cookies, $post);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($hrefs, '/messages/action/?mm_action=delete') !== false)
      {
        $this->configs->request_get($this->base_url.$hrefs, $this->cookies);
        $this->climate->out('  [DELETED] '.$this->username);
      }
    }
  }
}