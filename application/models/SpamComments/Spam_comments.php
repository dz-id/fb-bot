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

class Spam_comments extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $input = $this->climate->input('  Enter post url:');
    $post = $input->prompt();
    if (!filter_var($post, FILTER_VALIDATE_URL))
    {
      $this->climate->br()->shout('  Please enter a valid url.');
      sleep(3);
      $this->tools->spam_comments('Spam Comments In One Post');
    }
    $this->post = str_replace(parse_url($post)['host'], 'mbasic.facebook.com', $post);
    $this->climate->br()->shout("  ?: Use '<n>' for new lines")->br();
    $input = $this->climate->input('  Messages:');
    $this->msg = $input->prompt();
    if (!trim($this->msg))
    {
      $this->climate->br()->shout('  Please enter a messages.');
      sleep(3);
      $this->tools->spam_comments('Spam Comments In One Post');
    }
    $input = $this->climate->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == 0)
    {
      $this->climate->br()->shout('  Please enter a valid numbers.');
      sleep(3);
      $this->tools->spam_comments('Spam Comments In One Post');
    }
    $this->climate->br();
    $this->data = $this->get_csrf();
    $this->comments();
  }

  private function get_csrf()
  {
    $response = $this->configs->request_get($this->post, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $data = [];
    foreach ($dom->getElementsByTagName('form') as $form)
    {
      $action = $form->getAttribute('action');
      if (strpos($action, '/a/comment.php?') !== false)
      {
        $data['action'] = $action;
        break;
      }
    }
    foreach ($dom->getElementsByTagName('input') as $input)
    {
      $name = $input->getAttribute('name');
      $value = $input->getAttribute('value');
      if (trim($name) == 'fb_dtsg')
      {
        $data['fb_dtsg'] = $value;
      }
      if (trim($name) == 'jazoest')
      {
        $data['jazoest'] = $value;
        break;
      }
    }
    if (count($data) == 3)
    {
      return $data;
    }
    else
    {
      return [];
    }
  }

  private function comments()
  {
    if (count($this->data) == 3)
    {
      $loop = 0;
      for ($i = 0; $i < $this->limit; $i++)
      {
        $loop++;
        $o = str_pad($loop, 2, '0', STR_PAD_LEFT);
        $url = $this->base_url.$this->data['action'];
        $this->data['comment_text'] = str_replace("<n>", "\n", $this->msg);
        $params = http_build_query($this->data);
        $this->configs->request_post($url, $this->cookies, $params);
        $this->climate->out("  [{$this->yellow}{$o}{$this->reset}] Successfully commented.");
      }
      $this->climate->br()->out('  Finished, thank you for using this tool');
      $this->configs->back_menu();
    }
    else
    {
      $this->climate->shout('  Post not found :(');
      $this->configs->back_menu();
    }
  }
}