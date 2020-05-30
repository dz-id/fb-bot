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
      $hrefs = $href->getAttribute('href');
      $name = false;
      foreach ($href->getElementsByTagName('div') as $div ) {
        $name = $div->nodeValue;
        break;
      }
      if (preg_match('/\/groups\/(.*?)\?refid=/', $hrefs) and $name) {
        preg_match('/\/groups\/(.*?)\?refid=/', $hrefs, $ids);
        array_push($this->data, ['id' => $ids[1], 'name' => $name]);
        $this->climate->out("  - $name");
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
      $response = $this->execute($groups['id']);
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

  private function execute($id)
  {
    $response = $this->configs->request_get($this->base_url.'/groups/'.$id, $this->cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $params = [];
    foreach ($dom->getElementsByTagName('form') as $form) {
      $action = $form->getAttribute('action');
      if (strpos($action, '/a/group/join/?group_id=') !== false) {
        $params['action'] = $action;
      }
    }
    foreach ($dom->getElementsByTagName('input') as $form) {
      $name = $form->getAttribute('name');
      $value = $form->getAttribute('value');
      if (trim($name) == 'fb_dtsg') {
        $params['fb_dtsg'] = $value;
      }
      if (trim($name) == 'jazoest') {
        $params['jazoest'] = $value;
        break;
      }
    }
    if (count($params) !== 3)
    {
      return false;
    }
    else
    {
      $post = http_build_query($params);
      $this->configs->request_post($this->base_url.$params['action'], $this->cookies, $post);
      return true;
    }
  }
}