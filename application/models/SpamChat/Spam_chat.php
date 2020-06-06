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

class Spam_chat extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  public function reset_data()
  {
    /*
      @Array params
    */
    $this->params = [];

    $this->ids = '';
    $this->limit = '';
    $this->messages = '';
  }

  public function uid_input()
  {
    $input = $this->climate->input('  Enter uid not username:');
    $this->ids = $input->prompt();
    if (!is_numeric($this->ids))
    {
      $this->climate->br()->shout('  Invalid uid');
      sleep(3);
      $this->tools->spam_chat('Spam Chat Target');
      exit(0);
    }
    $this->climate->br()->shout("  Type '<n>' for new lines ex: hello<n>world")->br();
    $input = $this->climate->input('  Messages:');
    $this->messages = $input->prompt();
    if (!trim($this->messages))
    {
      $this->climate->br()->shout("  Can't empty");
      sleep(3);
      $this->tools->spam_chat('Spam Chat Target');
      exit(0);
    }
    $input = $this->climate->input('  How many (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == 0)
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->spam_chat('Spam Chat Target');
      exit(0);
    }
    $this->messages = str_replace("<n>", "\n", $this->messages);
    $this->climate->br();
  }

  public function index()
  {
    $this->climate->shout('  Spamming message to: '.$this->ids)->br();
    for ($i = 0; $i < $this->limit; $i++)
    {
      $this->chats();
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
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
      $this->climate->out('  + Failed');
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
      $this->climate->out('  + Sended');
    }
    else
    {
      $this->climate->out('  + Failed');
    }
  }
}