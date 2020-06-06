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

class Mass_join_groups extends CI_Model {

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
      @Array fix double groups
    */
    $this->fix = [];
  }

  public function query_input()
  {
    $input = $this->climate->input('  Query name:');
    $query = $input->prompt();
    $this->url = $this->base_url.'/search/groups/?q='.urlencode($query).'&source=filter&isTrending=0';
    $input = $this->climate->input('  Amount (ex: 10):');
    $this->limit = $input->prompt();
    $this->climate->br();
    is_numeric($this->limit) OR exit('  Invalid numbers');
  }

  public function index()
  {
    $stop = false;
    $response = $this->configs->request_get($this->url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      foreach ($href->getElementsByTagName('div') as $div ) {
        $name = $div->nodeValue;
        break;
      }
      $hrefs = $href->getAttribute('href');
      if (strpos($hrefs, '/a/group/join/?button_id=') !== false)
      {
        $link = $this->base_url.$hrefs;
        $this->fix[] = $link;
        if (count($this->fix) == 2)
        {
          $this->climate->out("  - $name");
          preg_match('/group_id=(.*?)&/', $this->fix[0], $id);
          $this->data[] = ['id' => $id[1], 'name' => $name, 'link' => $this->fix[0]];
          $this->fix = [];
        }
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
        if (strpos($href->nodeValue, 'Lihat Hasil Selanjutnya') !== false)
        {
          $this->url = $hrefs;
          $this->index();
          break;
        }
      }
    }
    if (count($this->data) == 0)
    {
      $this->climate->shout('  No groups found!');
      $this->configs->back_menu();
    }
    else
    {
      $this->joins();
      $this->climate->br()->out('  Finished, thank you for using this tool');
      $this->configs->back_menu();
    }
  }

  private function joins()
  {
    $this->climate->br()->shout('  Starting...')->br();
    foreach ($this->data as $groups)
    {
      $response = $this->configs->request_get($groups['link'], $this->cookies);
      if ($response)
      {
        $this->climate->out('  + '.$groups['name'].' -> '.$groups['id'].' -> Requests sent..');
      }
      else
      {
        $this->climate->out('  - '.$groups['name'].' -> '.$groups['id'].' -> Faileds..');
      }
    }
  }
}