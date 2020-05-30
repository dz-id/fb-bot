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

class Friendslist_eraser extends CI_Model {

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
    $this->url = $this->base_url.'/friends/center/friends/?fb_ref=fbm&ref_component=mbasic_bookmark&ref_page=XMenuController';
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
            $this->data[] = $id[1];
            $this->fullname = $name;
            $this->delete_friends($id[1]);
          }
        }
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

  public function delete_friends($id)
  {
    $response = $this->configs->request_get($this->base_url.'/removefriend.php?friend_id='.$id.'&unref=profile_gear', $this->cookies);
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
      }
      if (trim($name) == 'unref')
      {
        $this->params['unref'] = $value;
        break;
      }
    }
    if (count($this->params) == 2 OR count($this->params) == 3)
    {
      $this->execute($id);
    }
    else
    {
      $this->climate->shout('  [FAILEDS] '.$id.' - '.$this->fullname);
    }
  }

  public function execute($id)
  {
    $this->params['friend_id'] = $id;
    $this->params['confirm'] = 'Konfirmasi';
    $post = http_build_query($this->params);
    $response = $this->configs->request_post($this->base_url.'/a/removefriend.php', $this->cookies, $post);
    if (strpos($response, 'Anda tidak lagi berteman dengan') !== false)
    {
      $this->climate->out('  [REMOVED] '.$id.' - '.$this->fullname);
    }
    else
    {
      $this->climate->out('  [FAILEDS] '.$id.' - '.$this->fullname);
    }
  }
}