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

class Friends_request extends CI_Model {

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
    $this->url = $this->base_url.'/friends/center/requests/';
  }

  public function ask_input()
  {
    $this->climate->info("  (?) Type A for accept, Type D for delete")->br();
    $input = $this->climate->input("  [{$this->yellow}A{$this->reset}]accept OR [{$this->yellow}D{$this->reset}]elete:");
    $input = $input->prompt();
    if (strtolower($input) == 'a')
    {
      $this->msg = 'Permintaan diterima';
      $this->type = 'Konfirmasi';
    }
    else if (strtolower($input) == 'd')
    {
      $this->msg = 'Permintaan dihapus';
      $this->type = 'Hapus Permintaan';
    }
    else
    {
      $this->climate->br()->shout('  Wrong Input!');
      exit(0);
    }
    $this->climate->br();
  }

  public function index()
  {
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($hrefs, '/friends/hovercard/mbasic/') !== false)
      {
        $name = $href->nodeValue;
      }
      if (strpos($href->nodeValue, $this->type) !== false)
      {
        if ($this->type == 'Konfirmasi')
        {
          preg_match('/\/a\/notifications.php\?confirm=(.*?)&/', $hrefs, $ids);
        }
        else
        {
          preg_match('/\/a\/notifications.php\?delete=(.*?)&/', $hrefs, $ids);
        }
        $this->data[] = [
          'url' =>$this->base_url.$hrefs,
          'name' => $name,
          'uid' => $ids[1]
        ];
        $this->climate->inline("\r  GET (".count($this->data).") friends requests...");
      }
    }
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($href->nodeValue, 'Lihat selengkapnya') !== false)
      {
        $this->url = $this->base_url.$hrefs;
        $this->index();
        break;
      }
    }
    if (count($this->data) !== 0)
    {
      $this->climate->br()->br()->shout('  Starting...')->br();
      $this->execute();
    }
    else
    {
      $this->climate->shout('  No data!');
      $this->configs->back_menu();
    }
  }

  public function execute()
  {
    foreach ($this->data as $req)
    {
      $response = $this->configs->request_get($req['url'], $this->cookies);
      if ($this->type == 'Konfirmasi')
      {
        if (strpos($response, $this->msg) !== false)
        {
          $this->climate->out('  [ACEPTED] '.$req['uid'].' - '.$req['name']);
        }
        else
        {
          $this->climate->out('  [FAILEDS] '.$req['uid'].' - '.$req['name']);
        }
      }
      else
      {
        if (strpos($response, $this->msg) !== false)
        {
          $this->climate->out('  [DELETED] '.$req['uid'].' - '.$req['name']);
        }
        else
        {
          $this->climate->out('  [FAILEDS] '.$req['uid'].' - '.$req['name']);
        }
      }
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
  }
}