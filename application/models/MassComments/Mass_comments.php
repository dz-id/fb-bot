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

class Mass_comments extends CI_Model {
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
  }

  public function index()
  {
    foreach ($this->configs->show_menu_mass_comments() as $menu)
    {
      $this->climate->out("    [{$this->yellow}{$menu->no}{$this->reset}]. $menu->name");
    }
    $input = $this->climate->br()->input('  Choice:');
    $input = $input->prompt();
    switch ($input)
    {
      case '01':
        $this->input_home();
        break;
      case '02':
        $this->input_friends();
        break;
      case '03':
        $this->input_groups();
        break;
      case '04':
        $this->input_fanspage();
        break;
      case '0':
        $this->return_menu->index();
        exit(0);
      default:
        $this->climate->br()->shout('  Wrong input');
        sleep(3);
        $this->tools->mass_react('Mass React');
        break;
    }
    $this->climate->br();
    $this->dump_post();
  }

  private function input_home()
  {
    $this->url = $this->base_url.'/home.php';
    $this->climate->br()->shout("  Type '<n>' for newlines EX: hello<n>world")->br();
    $input = $this->climate->input('  Messages:');
    $this->msg = $input->prompt();
    if (!trim($this->msg))
    {
      $this->climate->br()->shout('  Enter a valid messages');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
    $input = $this->climate->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == '0')
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
  }

  private function input_friends()
  {
    $input = $this->climate->br()->input('  Enter username OR uid:');
    $username = $input->prompt();
    if (!trim($username))
    {
      $this->climate->br()->shout('  Please enter valid username or uid');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
    $this->url = $this->base_url.'/'.$username.'?v=timeline';
    $this->climate->br()->shout("  Type '<n>' for newlines EX: hello<n>world")->br();
    $input = $this->climate->input('  Messages:');
    $this->msg = $input->prompt();
    if (!trim($this->msg))
    {
      $this->climate->br()->shout('  Enter a valid messages');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
    $input = $this->climate->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == '0')
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
  }

  private function input_groups()
  {
    $this->climate->br()->info('  You must join the target group')->br();
    $input = $this->climate->input('  Enter group id:');
    $id = $input->prompt();
    if (!is_numeric($id))
    {
      $this->climate->br()->shout('  Please enter valid id');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
    $this->url = $this->base_url.'/groups/'.$id;
    $this->climate->br()->shout("  Type '<n>' for newlines EX: hello<n>world")->br();
    $input = $this->climate->input('  Messages:');
    $this->msg = $input->prompt();
    if (!trim($this->msg))
    {
      $this->climate->br()->shout('  Enter a valid messages');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
    $input = $this->climate->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == '0')
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
  }

  private function input_fanspage()
  {
    $input = $this->climate->br()->input('  Enter fanspage username OR uid:');
    $username = $input->prompt();
    if (!trim($username))
    {
      $this->climate->br()->shout('  Please enter valid username');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
    $this->url = $this->base_url.'/'.$username;
    $this->climate->br()->shout("  Type '<n>' for newlines EX: hello<n>world")->br();
    $input = $this->climate->input('  Messages:');
    $this->msg = $input->prompt();
    if (!trim($this->msg))
    {
      $this->climate->br()->shout('  Enter a valid messages');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
    $input = $this->climate->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == '0')
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->mass_comments('Mass Comments');
    }
  }

  private function dump_post()
  {
    $stop = false;
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $href->getAttribute('href');
      if (trim($href->nodeValue) == 'Berita Lengkap')
      {
        $this->data[] = $this->base_url.$hrefs;
        $this->climate->inline("\r  GET (".count($this->data).") post...");
        if ($this->limit == count($this->data) OR count($this->data) > $this->limit)
        {
          $stop = true;
          break;
        }
      }
    }
    if (!$stop)
    {
      foreach ($dom->getElementsByTagName('a') as $href)
      {
        $hrefs = $href->getAttribute('href');
        if (strpos($href->nodeValue, 'Lihat Berita Lain') !== false)
        {
          $this->url = $this->base_url.$hrefs;
          $this->dump_post();
          break;
        }
        else if (strpos($href->nodeValue, 'Tampilkan lainnya') !== false)
        {
          $this->url = $this->base_url.$hrefs;
          $this->dump_post();
          break;
        }
        else if (strpos($href->nodeValue, 'Lihat Postingan Lainnya') !== false)
        {
          $this->url = $this->base_url.$hrefs;
          $this->dump_post();
          break;
        }
      }
    }
    if (count($this->data) !== 0)
    {
      $this->climate->br()->br()->shout('  Starting...')->br();
      foreach ($this->data as $post)
      {
        $this->comments($post);
      }
      $this->climate->br()->out('  Finished, thank you for using this tool');
      $this->configs->back_menu();
    }
    else
    {
      $this->climate->shout('  No post found');
      $this->configs->back_menu();
    }
  }

  private function comments($post_url)
  {
    preg_match('/:top_level_post_id.(.*?):tl_objid..*?:content_owner_id_new.(.*?):/', urldecode($post_url), $post_id);
    if (count($post_id) !== 3)
    {
      preg_match('/:top_level_post_id.(.*?):content_owner_id_new.(.*?):/', urldecode($post_url), $post_id);
    }
    if (count($post_id) == 3)
    {
      $this->post_ids = $post_id[2].'_'.$post_id[1];
    }
    else
    {
      $this->post_ids = 'unknown post Id';
    }
    $response = $this->configs->request_get($post_url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $this->params = [];
    foreach ($dom->getElementsByTagName('form') as $form)
    {
      $action = $form->getAttribute('action');
      if (strpos($action, '/a/comment.php?') !== false)
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
      $this->execute($this->params['action']);
    }
    else
    {
      $this->climate->out("  + {$this->post_ids} - Error when grab value");
    }
  }

  private function execute($url)
  {
    unset($this->params['action']);
    $this->params['comment_text'] = str_replace("<n>", "\n", $this->msg);
    $post_data = http_build_query($this->params);
    $this->configs->request_post($url, $this->cookies, $post_data);
    $this->climate->out("  + {$this->post_ids} - Successfully commented..");
  }
}