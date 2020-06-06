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

class Mass_react extends CI_Model {
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
  }

  public function index()
  {
    foreach ($this->configs->show_menu_mass_react() as $menu)
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
    foreach ($this->configs->show_menu_reactions() as $menu)
    {
      $this->climate->out("    [{$this->yellow}{$menu->no}{$this->reset}]. $menu->name");
    }
    $input = $this->climate->br()->input('  Choice:');
    $input = $input->prompt();
    switch ($input)
    {
      case '01':
        $this->reaction_type = '1';
        break;
      case '02':
        $this->reaction_type = '2';
        break;
      case '03':
        $this->reaction_type = '16';
        break;
      case '04':
        $this->reaction_type = '4';
        break;
      case '05':
        $this->reaction_type = '3';
        break;
      case '06':
        $this->reaction_type = '7';
        break;
      case '07':
        $this->reaction_type = '8';
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
    $input = $this->climate->br()->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == '0')
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->mass_react('Mass React');
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
      $this->tools->mass_react('Mass React');
    }
    $this->url = $this->base_url.'/'.$username.'?v=timeline';
    $input = $this->climate->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == '0')
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->mass_react('Mass React');
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
      $this->tools->mass_react('Mass React');
    }
    $this->url = $this->base_url.'/groups/'.$id;
    $input = $this->climate->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == '0')
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->mass_react('Mass React');
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
      $this->tools->mass_react('Mass React');
    }
    $this->url = $this->base_url.'/'.$username;
    $input = $this->climate->input('  Limit (ex: 10):');
    $this->limit = $input->prompt();
    if (!is_numeric($this->limit) OR $this->limit == '0')
    {
      $this->climate->br()->shout('  Invalid input');
      sleep(3);
      $this->tools->mass_react('Mass React');
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
      if (trim($href->nodeValue) == 'Tanggapi')
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
        $this->execute($post);
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

  private function execute($post_url)
  {
    $status = false;
    $response = $this->configs->request_get($post_url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $this->base_url.$href->getAttribute('href');
      if (strpos($hrefs, 'reaction_type='.$this->reaction_type) !== false)
      {
        $status = true;
        preg_match('/:top_level_post_id.(.*?):tl_objid..*?:content_owner_id_new.(.*?):/', urldecode($hrefs), $post_id);
        if (count($post_id) !== 3)
        {
          preg_match('/:top_level_post_id.(.*?):content_owner_id_new.(.*?):/', urldecode($hrefs), $post_id);
        }
        if (count($post_id) == 3)
        {
          $post_ids = $post_id[2].'_'.$post_id[1];
        }
        else
        {
          $post_ids = 'unknown post Id';
        }
        $this->configs->request_get($hrefs, $this->cookies);
        $this->climate->out('  + '.$post_ids.' - Successfully reacted..');
        break;
      }
    }
    if (!$status)
    {
      $this->climate->out('  + Error unknown');
    }
  }
}