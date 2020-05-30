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

class Report_bug extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $this->climate->info('  Report if a Tool not working/bug')->br();
    $input = $this->climate->input('  Your name:');
    $this->name = $input->prompt();
    if (!trim($this->name))
    {
      $this->climate->br()->shout('  Please enter your name');
      sleep(3);
      $this->tools->report_bug('Report Bug');
    }
    $input = $this->climate->input('  Your email:');
    $this->email = $input->prompt();
    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
    {
      $this->climate->br()->shout('  Please enter valid email');
      sleep(3);
      $this->tools->report_bug('Report Bug');
    }
    $input = $this->climate->input('  Your messages:');
    $this->msg = $input->prompt();
    if (!trim($this->msg))
    {
      $this->climate->br()->shout('  Please enter your messages');
      sleep(3);
      $this->tools->report_bug('Report Bug');
    }
    $this->send();
    $this->configs->back_menu();
  }

  private function send()
  {
    $post_data = http_build_query(
      [
        'name' => $this->name,
        'email' => $this->email,
        'messages' => $this->msg
      ]
    );
    $response = $this->configs->request_post('https://dz-tools.my.id/api/bug-report-cli', false, $post_data);
    $decode = json_decode($response);
    if ($decode->error == false)
    {
      $this->climate->br()->info('  Thank you for your report, your message was sent successfully');
    }
    else
    {
       $this->climate->br()->shout('  Failed try again later');
    }
  }
}