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

class Mass_chat extends CI_Model {

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
    /*
      @Array params
    */
    $this->params = [];

    $this->fullname = '';
    $this->ids = '';
    $this->limit = '';
    $this->messages = '';
    $this->url = $this->base_url.'/friends/center/friends/?fb_ref=fbm&ref_component=mbasic_bookmark&ref_page=XMenuController';
  }

  public function messages_input()
  {
    $this->climate->shout("  Type '<n>' for new lines ex: hello<n>world")->br();
    $input = $this->climate->input('  Messages:');
    $this->messages = $input->prompt();
    if (!trim($this->messages))
    {
      $this->climate->br()->shout("  Can't empty");
      sleep(3);
      $this->tools->mass_chat('Mass Chat To Friends-list');
      exit(0);
    }
    $input = $this->climate->input('  How many (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == 0)
    {
      $this->climate->br()->shout("  Invalid numbers");
      sleep(3);
      $this->tools->mass_chat('Mass Chat To Friends-list');
      exit(0);
    }
    $this->messages = str_replace("<n>", "\n", $this->messages);
    $this->climate->br();
  }

  public function index()
  {
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('td') as $td)
    {
      $style = $td->getAttribute('style');
      if (strpos($style, 'vertical-align: middle') !== false)
      {
        $find = $td->getElementsByTagName('a');
        foreach ($find as $href)
        {
          $stop = false;
          $name = false;
          $hrefs = $href->getAttribute('href');
          $name = $href->nodeValue;
          if (strpos($hrefs, '/friends/hovercard/mbasic/?uid=') !== false)
          {
            preg_match('/\/friends\/hovercard\/mbasic\/\?uid=(.*?)&/', $hrefs, $id);
          }
          else
          {
            preg_match('/\/(.*?)\?/', $hrefs, $id);
          }
          if ($id[1] AND $name)
          {
            $this->fullname = $name;
            $this->ids = $id[1];
            if ($this->limit == count($this->data) OR count($this->data) > $this->limit)
            {
              $stop = true;
              break;
            }
            else
            {
              $this->data[] = $id[1];
              $this->climate->out('  - Sending message to: '.$name);
              $this->chats();
            }
          }
        }
      }
      if ($stop)
      {
        break;
      }
    }
    if (!$stop)
    {
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
    }
    if (count($this->data) == 0)
    {
      $this->climate->shout('  No friends found!');
      $this->configs->back_menu();
    }
    else
    {
      $this->climate->br()->out('  Finished, thank you for using this tool');
      $this->configs->back_menu();
    }
  }

  private function chats()
  {
    $response = $this->configs->request_get($this->base_url.'/messages/read/?tid=cid.c.'.$this->ids, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $this->params = [];
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
    if (count($this->params) == 2)
    {
      $this->execute();
    }
    else
    {
      $this->climate->out('    * ERRORS when send message to: '.$this->fullname.' ('.$this->ids.')');
    }
  }

  public function execute()
  {
    $this->params['ids['.$this->ids.']'] = $this->ids;
    $this->params['body'] = $this->messages;
    $this->params['send'] = 'Kirim';
    $response = $this->configs->request_post($this->base_url.'/messages/send/?icm=1&amp;refid=12', $this->cookies, $this->params);
    if (strpos($response, 'send_success') !== false)
    {
      $this->climate->out('    * SUCCESS sended to: '.$this->fullname.' ('.$this->ids.')');
    }
    else
    {
      $this->climate->out('    * ERRORS when send message to: '.$this->fullname.' ('.$this->ids.')');
    }
  }
}