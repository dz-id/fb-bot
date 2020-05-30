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

class Update_status extends CI_Model {

  public function reset_data()
  {
    /*
     @Array $params
    */
    $this->params = [];
  }

  public function index()
  {
    foreach ($this->configs->show_menu_update_status() as $menu)
    {
      $this->climate->out("    [{$this->yellow}{$menu->no}{$this->reset}]. $menu->name");
    }
    $input = $this->climate->br()->input('  Choice:');
    $input = $input->prompt();
    switch ($input)
    {
      case '01':
        $this->caption = $this->configs->list_captions('bucin.txt');
        $this->update();
        break;
      case '02':
        $this->caption = $this->configs->list_captions('islam.txt');
        $this->update();
        break;
      case '03':
        $this->caption = $this->configs->list_captions('bijak.txt');
        $this->update();
        break;
      case '0':
        $this->return_menu->index();
        exit(0);
      default:
        $this->climate->br()->shout('  Wrong Input');
        sleep(3);
        $this->tools->update_status('Update Status Random Caption');
        break;
    }
  }

  private function update()
  {
    $input = $this->climate->br()->input('  How many (ex: 10):');
    $limit = $input->prompt();
    if (!is_numeric($limit))
    {
      $this->climate->br()->shout('  Invalid input.');
      sleep(3);
      $this->tools->update_status('Update Status Random Caption');
    }
    if ($limit == 0)
    {
      $this->climate->br()->shout("  Can't empty");
      sleep(3);
      $this->tools->update_status('Update Status Random Caption');
    }
    $this->climate->br()->shout('  Starting...')->br();
    for ($i = 0; $i < $limit; $i++)
    {
      $response = $this->configs->request_get($this->base_url.'/profile', $this->cookies);
      $dom = new DOMDocument();
      @$dom->loadHTML($response);
      foreach ($dom->getElementsByTagName('form') as $form)
      {
        $action = $form->getAttribute('action');
        if (strpos($action, '/composer/mbasic/?') !== false)
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
        }
        if (trim($name) == 'privacyx')
        {
          $this->params['privacyx'] = $value;
        }
        if (trim($name) == 'r2a')
        {
          $this->params['r2a'] = $value;
        }
        if (trim($name) == 'xhpc_timeline')
        {
          $this->params['xhpc_timeline'] = $value;
        }
        if (trim($name) == 'target')
        {
          $this->params['target'] = $value;
        }
        if (trim($name) == 'c_src')
        {
          $this->params['c_src'] = $value;
        }
        if (trim($name) == 'cwevent')
        {
          $this->params['cwevent'] = $value;
        }
        if (trim($name) == 'referrer')
        {
          $this->params['referrer'] = $value;
        }
        if (trim($name) == 'cver')
        {
          $this->params['cver'] = $value;
        }
        if (trim($name) == 'view_privacy')
        {
          $this->params['view_privacy'] = $value;
          break;
        }
      }
      if (count($this->params) > 5)
      {
        $this->execute($this->params['action']);
      }
      else
      {
        $this->climate->shout('  - Failed, error when grab value');
      }
    }
    $this->climate->br()->out('  Finished, thank you for using this tool');
    $this->configs->back_menu();
  }

  private function execute($url)
  {
    $msg = $this->caption[array_rand($this->caption)];
    unset($this->params['action']);
    $this->params['xc_message'] = $msg;
    $this->params['view_post'] = 'Posting';
    $post_data = http_build_query($this->params);
    $response = $this->configs->request_post($url, $this->cookies, $post_data);
    $this->climate->out("  + SUCCESS with caption: {$this->green}{$msg}{$this->reset}");
  }
}