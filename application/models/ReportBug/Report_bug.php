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
    $ch = curl_init('https://dz-tools.my.id/api/bug-report-cli');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
      $this->climate->br()->shout('  Connection error, please check your connection and try again.');
      exit(0);
    }
    curl_close($ch);
    $decode = json_decode($response);
    if (isset($decode->error) and $decode->error == false)
    {
      $this->climate->br()->info('  Thank you for your report, your message was sent successfully');
    }
    else
    {
       $this->climate->br()->shout('  Failed try again later');
    }
  }
}