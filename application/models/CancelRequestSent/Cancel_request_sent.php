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

class Cancel_request_sent extends CI_Model {

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
    $this->url = $this->base_url.'/friends/center/requests/outgoing/#friends_center_main';
  }

  public function index()
  {
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($hrefs, '/a/friendrequest/cancel/?subject_id=') !== false)
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
      $this->cancel();
    }
  }

  private function cancel()
  {
    foreach ($this->data as $user)
    {
      preg_match('/\/a\/friendrequest\/cancel\/\?subject_id=(.*?)&/', $user, $user_id);
      $this->configs->request_get($user, $this->cookies);
      $this->climate->out("  * Sucessfully canceled ({$user_id[1]})");
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
  }
}