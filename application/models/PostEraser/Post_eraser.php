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

class Post_eraser extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  public function reset_data()
  {
    $this->url = $this->base_url.'/profile.php';
    /*
      @Array data
    */
    $this->data = [];
    /*
      @Array params
    */
    $this->params = [];
    /*
      @Post ID
    */
    $this->post_id = '';
  }

  public function index()
  {
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($href->nodeValue, 'Berita Lengkap') !== false)
      {
        $this->data[] = $this->base_url.$hrefs;
        $this->climate->inline("\r  Collecting (".count($this->data).") post...");
      }
    }
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (strpos($href->nodeValue, 'Lihat Berita Lain') !== false)
      {
        $this->url = $this->base_url.$hrefs;
        $this->index();
        break;
      }
    }
    if (count($this->data) !== 0)
    {
      $this->climate->br()->br()->shout('  Starting delete '.count($this->data).' post...')->br();
      $this->delete_post();
    }
    else
    {
      $this->climate->shout('  No post found!');
      $this->configs->back_menu();
    }
  }

  public function delete_post()
  {
    foreach ($this->data as $post)
    {
      $delete_link = false;
      $decode_url = urldecode($post);
      preg_match('/:top_level_post_id.(.*?):tl_objid..*?:content_owner_id_new.(.*?):/', $decode_url, $ids);
      $this->post_id = $ids[2].'_'.$ids[1];
      if (strpos($post, '/story.php?story_fbid=') !== false)
      {
        $response = $this->configs->request_get($post, $this->cookies);
        $dom = new DOMDocument();
        @$dom->loadHTML($response);
        foreach ($dom->getElementsByTagName('a') as $href)
        {
          $hrefs = $href->getAttribute('href');
          if (strpos($hrefs, '/delete.php') !== false OR trim($href->nodeValue) == 'Hapus')
          {
            $delete_link = $this->base_url.$hrefs;
            break;
          }
          else if (strpos($hrefs, '/nfx/basic/direct_actions/?context_str=') !== false)
          {
            $delete_link = $this->base_url.$hrefs;
            break;
          }
        }
      }
      else
      {
        $delete_link = $this->base_url.'/photo.php?fbid='.$ids[1].'&id='.$ids[2].'&delete';
      }
      if ($delete_link)
      {
        $this->execute($delete_link);
      }
      else
      {
        $this->climate->out('  [FAILEDS] '.$this->post_id);
      }
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
  }

  public function execute($url)
  {
    $response = $this->configs->request_get($url, $this->cookies);
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
    foreach ($dom->getElementsByTagName('form') as $form)
    {
      $action = $form->getAttribute('action');
      if (strpos($url, 'delete.php?') !== false)
      {
        if (strpos($action, '/a/delete.php') !== false)
        {
          $this->params['action'] = $this->base_url.$action;
        }
      } else if (strpos($url, '/nfx/basic/direct_actions/?context_str=') !== false)
      {
        if (strpos($action, '/nfx/basic/handle_action/?context_str=') !== false)
        {
          $this->params['action'] = $this->base_url.$action;
          $this->params['action_key'] = 'UNTAG';
          $this->params['submit'] = 'Kirim';
        }
      }
      else
      {
        if (strpos($action, '/a/photo.php') !== false)
        {
          $this->params['confirm_photo_delete'] = '1';
          $this->params['photo_delete'] = 'Hapus';
          $this->params['action'] = $this->base_url.$action;
        }
      }
    }
    if (count($this->params) == 3 OR count($this->params) == 5)
    {
      $postfields = http_build_query($this->params);
      $this->configs->request_post($this->params['action'], $this->cookies, $postfields);
      $this->climate->out('  [DELETED] '.$this->post_id);
    }
    else
    {
      $this->climate->out('  [FAILEDS] '.$this->post_id);
    }
  }
}