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

class Unblock_user extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  public function reset_data()
  {
    /*
      @Array data
    */
    $this->data = [];
    $this->url = $this->base_url.'/privacy/touch/block/';
  }

  public function index()
  {
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($hrefs, '/privacy/touch/unblock/confirm/?unblock_id=') !== false)
      {
        $this->data[] = $this->base_url.$hrefs;
        $this->climate->inline("\r  GET (".count($this->data).") data...");
      }
    }
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      if (strpos($href->nodeValue, 'Lihat selengkapnya') !== false)
      {
        $hrefs = $href->getAttribute('href');
        $this->url = $this->base_url.$hrefs;
        $this->index();
        break;
      }
    }
    if (count($this->data) == 0)
    {
      $this->climate->shout('  No data');
      $this->configs->back_menu();
    }
    else
    {
      $this->climate->br()->br()->shout('  Starting...')->br();
      $this->unblock();
    }
  }

  private function unblock()
  {
    foreach ($this->data as $user)
    {
      preg_match('/\/privacy\/touch\/unblock\/confirm\/\?unblock_id=(.*?)$/', $user, $user_id);
      $response = $this->configs->request_get($user, $this->cookies);
      $dom = new DOMDocument();
      @$dom->loadHTML($response);
      $this->params = [];
      foreach ($dom->getElementsByTagName('form') as $form)
      {
        $action = $form->getAttribute('action');
        if (strpos($action, '/privacy/touch/unblock/write/?') !== false)
        {
          $this->params['action'] = $this->base_url.$action;
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
        $this->params['confirmed'] = 'Buka Blokir';
        $post_data = http_build_query($this->params);
        $response = $this->configs->request_post($this->params['action'], $this->cookies, $post_data);
        if (strpos($response, 'Anda telah menghapus blokir') !== false)
        {
          $this->climate->out("  * Sucessfully unbloked ({$user_id[1]})");
        }
        else
        {
          $this->climate->out("  * Failed ({$user_id[1]})");
        }
      }
      else
      {
        $this->climate->out("  * Failed error when grab values ({$user_id[1]})");
      }
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
  }
}