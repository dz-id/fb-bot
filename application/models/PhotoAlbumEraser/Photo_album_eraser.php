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

class Photo_album_eraser extends CI_Model {

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

    $this->album_url = '';
    $this->album_name = '';
    $this->url = $this->base_url.'/menu/bookmarks/';
  }

  public function index()
  {
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $foto = false;
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (trim($href->nodeValue) == 'Foto')
      {
        $foto = $hrefs;
      }
    }
    if ($foto)
    {
      $response = $this->configs->request_get($this->base_url.$foto, $this->cookies);
      $dom = new DOMDocument();
      @$dom->loadHTML($response);
      $album = [];
      $count = 0;
      foreach ($dom->getElementsByTagName('td') as $td)
      {
        $items = false;
        foreach ($td->getElementsByTagName('span') as $span)
        {
          foreach ($span->getElementsByTagName('a') as $href)
          {
            $hrefs = $href->getAttribute('href');
            if (strpos($hrefs, 'albums') !== false)
            {
              $album[] = array(
                'url' => $this->base_url.$hrefs,
                'name' => $href->nodeValue
              );
              $count++;
              $items = true;
              $no = str_pad($count,2,'0',STR_PAD_LEFT);
              $this->climate->inline("    [{$this->yellow}{$no}{$this->reset}]. $href->nodeValue  ");
            }
          }
        }
        if ($items)
        {
          foreach ($td->getElementsByTagName('div') as $div)
          {
            $this->climate->inline($div->nodeValue)->br();
          }
        }
      }
      if (count($album) !== 0)
      {
        $this->climate->out("    [{$this->yellow}00{$this->reset}]. Back");
        $input = $this->climate->br()->input('  Select album (1-'.count($album).'):');
        $input = $input->prompt();
        if ($input == '0')
        {
          $this->return_menu->index();
        }
        else if (empty($album[$input-1]))
        {
          $this->climate->br()->shout('  Wrong input!');
          sleep(3);
          $this->tools->photo_album_eraser('Photo Album Eraser');
          return;
        }
        else
        {
          $this->climate->br();
          $this->album_url = $album[$input-1]['url'];
          $this->album_name = $album[$input-1]['name'];
          $this->get_photo();
        }
      }
    }
    if ($count == 0)
    {
      $this->climate->shout('  Oops, no albums found!');
      $this->configs->back_menu();
    }
  }

  public function get_photo()
  {
    $response = $this->configs->request_get($this->album_url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($hrefs, '/photo.php?fbid=') !== false)
      {
        preg_match('/\/photo.php\?fbid=(.*?)&id=(.*?)&/', $hrefs, $ids);
        if ($ids[1] and $ids[2])
        {
          $this->data[] = array(
            'post' => $ids[1], 'uid' => $ids[2],
          );
          $this->climate->inline("\r  GET (".count($this->data).") photo from album ".$this->album_name);
        }
      }
    }
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($href->nodeValue, 'Lihat Foto Lainnya') !== false)
      {
        $this->album_url = $this->base_url.$hrefs;
        $this->get_photo();
        break;
      }
    }
    if (count($this->data) !== 0)
    {
      $this->climate->br()->br()->shout('  Starting delete '.count($this->data).' photo...')->br();
      $this->delete_photo();
    }
    else
    {
      $this->climate->shout('  No photo found');
      $this->configs->back_menu();
    }
  }

  public function delete_photo()
  {
    foreach ($this->data as $photo)
    {
      $response = $this->configs->request_get($this->base_url.'/photo.php?fbid='.$photo['post'].'&id='.$photo['uid'].'&delete', $this->cookies);
      $dom = new DOMDocument();
      @$dom->loadHTML($response);
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
      foreach ($dom->getElementsByTagName('form') as $form)
      {
        $action = $form->getAttribute('action');
        if (strpos($action, '/a/photo.php') !== false)
        {
          $this->params['confirm_photo_delete'] = '1';
          $this->params['photo_delete'] = 'Hapus';
          $this->params['action'] = $this->base_url.$action;
        }
      }
      if (count($this->params) == 5)
      {
        $postfields = http_build_query($this->params);
        $this->configs->request_post($this->params['action'], $this->cookies, $postfields);
        $this->climate->out('  [DELETED] '.$photo['uid'].'_'.$photo['post']);
      }
      else
      {
        $this->climate->out('  [FAILEDS] '.$photo['uid'].'_'.$photo['post']);
      }
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
  }
}