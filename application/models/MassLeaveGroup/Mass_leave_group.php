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

class Mass_leave_group extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  public function reset_data()
  {
    /*
      @Array Data groups
    */
    $this->data = [];
    /*
      @Array Post Data
    */
    $this->params = [];

    $this->id = '';
    $this->name = '';
  }

  public function get_group()
  {
    $url = $this->base_url.'/groups/?seemore&refid=27';
    $response = $this->configs->request_get($url, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('td') as $td)
    {
      foreach ($td->getElementsByTagName('a') as $href)
      {
        $hrefs = $href->getAttribute('href');
        $name = $href->nodeValue;
        preg_match('/\/groups\/(.*?)\?refid=/', $hrefs, $id);
        if (count($id) == 2)
        {
          $this->data[] = array('id' => $id[1], 'name' => $name);
          break;
        }
      }
    }
    if (count($this->data) == 0)
    {
      $this->climate->shout('  You have not joined any groups');
      $this->configs->back_menu();
      exit(0);
    }
  }

  public function index()
  {
    $no = 0;
    $this->climate->out('    [ You have '.count($this->data).' groups ]')->br();
    sleep(3);
    foreach ($this->data as $group)
    {
      $no++;
      $index = str_pad($no, 2, '0', STR_PAD_LEFT);
      $this->climate->out("    [{$this->yellow}{$index}{$this->reset}]. {$group['name']}");
    }
    $this->climate->br()->shout("  Select the group you want to leave (EX: 1,2,3) , Type 'all' for leave all groups");
    $this->climate->br()->shout("  Type 'BC' for back to menu")->br();
    $input = $this->climate->input("  Select:");
    $input = $input->prompt();
    $this->climate->br();
    $indexs = explode(',', trim($input));
    if (strtolower($input) == 'all')
    {
      foreach ($this->data as $group)
      {
        $this->id = $group['id'];
        $this->name = $group['name'];
        $this->leave_group();
      }
    }
    else if (strtolower($input) == 'bc')
    {
      $this->return_menu->index();
    }
    else
    {
      foreach ($indexs as $i)
      {
        if (isset($this->data[$i-1]))
        {
          $this->id = $this->data[$i-1]['id'];
          $this->name = $this->data[$i-1]['name'];
          $this->leave_group();
        }
        else
        {
          $this->climate->shout("  Invalids numbers '{$i}'");
        }
      }
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
    exit(0);
  }

  private function leave_group()
  {
    $this->climate->out('  Opening groups: '.$this->name);
    $response = $this->configs->request_get($this->base_url.'/group/leave/?group_id='.$this->id.'&refid=18', $this->cookies);
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
      if (trim($name) == 'group_id')
      {
        $this->params['group_id'] = $value;
        break;
      }
    }
    if (count($this->params) == 3)
    {
      $this->execute();
    }
    else
    {
      $this->climate->out("   + Error when grab value");
    }
  }

  public function execute()
  {
    $this->params['confirm'] = 'Keluar dari Grup';
    $post_data = http_build_query($this->params);
    $response = $this->configs->request_post($this->base_url.'/a/group/leave/?qp=0', $this->cookies, $post_data);
    $this->climate->out("   - Success leaved");
  }
}