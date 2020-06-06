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

class mass_posting_groups extends CI_Model {

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
    $this->climate->br()->shout("  Select the group you want to posting (EX: 1,2,3) , Type 'all' for posting in all groups");
    $this->climate->br()->shout("  Type 'BC' for back to menu")->br();
    $input = $this->climate->input("  Select:");
    $input = $input->prompt();
    if (strtolower($input) == 'bc')
    {
      $this->return_menu->index();
    }
    $this->climate->br()->info("  Use '<n>' for new lines (ex: hello<n>word)")->br();
    $caption = $this->climate->input('  Captions:');
    $this->caption = str_replace("<n>", "\n", $caption->prompt());
    if (!trim($this->caption))
    {
      $this->climate->br()->shout('  Please enter a valid caption');
      sleep(3);
      $this->tools->mass_posting_groups('Mass Posting Groups');
    }
    $this->climate->br();
    $indexs = explode(',', trim($input));
    if (strtolower($input) == 'all')
    {
      foreach ($this->data as $group)
      {
        $this->id = $group['id'];
        $this->name = $group['name'];
        $this->posting_group();
      }
    }
    else
    {
      foreach ($indexs as $i)
      {
        if (isset($this->data[$i-1]))
        {
          $this->id = $this->data[$i-1]['id'];
          $this->name = $this->data[$i-1]['name'];
          $this->posting_group();
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

  private function posting_group()
  {
    $this->climate->out('  Opening groups: '.$this->name);
    $response = $this->configs->request_get($this->base_url.'/groups/'.$this->id, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $this->params = [];
    foreach ($dom->getElementsByTagName('form') as $form) {
      $action = $form->getAttribute('action');
      if (strpos($action, '/composer/mbasic/?') !== false) {
        $this->params['action'] = $this->base_url.$action;
      }
    }
    foreach ($dom->getElementsByTagName('input') as $form) {
      $name = $form->getAttribute('name');
      $value = $form->getAttribute('value');
      if (trim($name) == 'fb_dtsg') {
        $this->params['fb_dtsg'] = $value;
      }
      if (trim($name) == 'jazoest') {
        $this->params['jazoest'] = $value;
      }
      if (trim($name) == 'target') {
        $this->params['target'] = $value;
        break;
      }
    }
    if (count($this->params) == 4)
    {
      $this->posting_exec();
    }
    else
    {
      $this->climate->out('    * Failed, error when GET value');
    }
  }

  private function posting_exec()
  {
    $this->params['c_src'] = 'group';
    $this->params['cwevent'] = 'composer_entry';
    $this->params['referrer'] = 'group';
    $this->params['cver'] = 'amber';
    $this->params['xc_message'] = $this->caption;
    $this->params['view_post'] = 'Posting';
    $post = http_build_query($this->params);
    $this->configs->request_post($this->params['action'], $this->cookies, $post);
    $this->climate->out('    * Successfully posted');
  }
}