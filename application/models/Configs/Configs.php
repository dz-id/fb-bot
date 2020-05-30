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

class Configs extends CI_Model {

  public function __construct() {
    parent::__construct();
    /*
      @Banner please don't remove the author name
    */
    $this->banner = PHP_EOL."
{$this->yellow}                         \ \ {$this->reset}| {$this->yellow}* %s
{$this->yellow}    .-'''''-.    __       |/ {$this->reset}|----------------------------
{$this->yellow}   /         \.'`  `',.--//  {$this->reset}| {$this->yellow}* Bot for Facebook
{$this->yellow} -(  {$this->red}FACEBOOK{$this->yellow} | {$this->red}BOT{$this->yellow}  |  @@\  {$this->reset}| {$this->yellow}* Made with love by DulLah
{$this->yellow}   \         /'.____.'\___|  {$this->reset}| {$this->yellow}* Github.com/dz-id
{$this->yellow}    '-.....-' __/ | \   (`)  {$this->reset}|----------------------------
{$this->reset}    v0.1 dev{$this->yellow} /   /  /        {$this->reset}| {$this->yellow}* %s
{$this->yellow}                 \  \ \n{$this->reset}".PHP_EOL;
    /*
      @Load modules climate
    */
    include('vendor/autoload.php');
    $this->cli = new League\CLImate\CLImate;
  }

  public function banner() {
    /*
      @Banner please don't remove the author name
    */
    return $this->banner;
  }

  public function clear() {
    if (strtolower(substr(PHP_OS, 0, 3)) === 'lin') {
      system('clear');
    } else
    {
      system('cls');
    }
  }

  public function back_menu() {
    $input = $this->climate->br()->input("  {$this->yellow}Press enter to return menu{$this->reset}");
    $input->prompt();
    $this->return_menu->index();
  }

  public function load_cookies() {
    if (file_exists('log/cookies.txt') and filesize('log/cookies.txt') > 0) {
      /*
      @Read Cookies
      */
      $file = fopen('log/cookies.txt', 'r');
      $cookies = fgets($file);
      fclose($file);
      return $cookies;
    } else
    {
      return false;
    }
  }

  public function list_captions($file) {
    $caption = [];
    $file = fopen('random/'.$file, 'r');
    while (!feof($file)) {
      if (trim(fgets($file))) {
        $caption[] = trim(fgets($file));
      }
    }
    fclose($file);
    return $caption;
  }

  public function climate() {
    return $this->cli;
  }

  public function request_get($url, $cookies = false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
      $this->climate->br()->shout('  Connection error, please check your connection and try again.');
      exit(0);
    }
    curl_close($ch);
    return $response;
  }

  public function request_post($url, $cookies = false, $post) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
      $this->climate->br()->shout('  Connection error, please check your connection and try again.');
      exit(0);
    }
    curl_close($ch);
    return $response;
  }

  public function show_menu() {
    $menu = json_encode(
      array(
        ['no' => '01', 'name' => 'Chat Messages Eraser'],
        ['no' => '02', 'name' => 'Post Eraser'],
        ['no' => '03', 'name' => 'FriendsList Eraser'],
        ['no' => '04', 'name' => 'Photo Album Eraser'],
        ['no' => '05', 'name' => 'Accept Delete Friends Request'],
        ['no' => '06', 'name' => 'Mass Join Group By Search Name'],
        ['no' => '07', 'name' => 'Update Status Random Caption'],
        ['no' => '08', 'name' => 'Mass Chat To Friends-list'],
        ['no' => '09', 'name' => 'Spam Chat Target'],
        ['no' => '10', 'name' => 'Mass Leave Group'],
        ['no' => '11', 'name' => 'Mass React'],
        ['no' => '12', 'name' => 'Mass Comments'],
      )
    );
    return json_decode($menu);
  }

  public function show_menu_update_status() {
    $menu = json_encode(
      array(
        ['no' => '01', 'name' => 'Status Bucin'],
        ['no' => '02', 'name' => 'Status Islam'],
        ['no' => '03', 'name' => 'Status Bijak'],
        ['no' => '00', 'name' => 'Back'],
      )
    );
    return json_decode($menu);
  }

  public function show_menu_mass_react() {
    $menu = json_encode(
      array(
        ['no' => '01', 'name' => 'Bomb React In Home'],
        ['no' => '02', 'name' => 'Bomb React In Friends-timeline'],
        ['no' => '03', 'name' => 'Bomb React In Groups'],
        ['no' => '04', 'name' => 'Bomb React In FansPage'],
        ['no' => '00', 'name' => 'Back'],
      )
    );
    return json_decode($menu);
  }

  public function show_menu_reactions() {
    $menu = json_encode(
      array(
        ['no' => '01', 'name' => 'Like'],
        ['no' => '02', 'name' => 'Love'],
        ['no' => '03', 'name' => 'Care'],
        ['no' => '04', 'name' => 'HaHa'],
        ['no' => '05', 'name' => 'Wow'],
        ['no' => '06', 'name' => 'Sad'],
        ['no' => '07', 'name' => 'Angry'],
        ['no' => '00', 'name' => 'Back'],
      )
    );
    return json_decode($menu);
  }

  public function show_menu_mass_comments() {
    $menu = json_encode(
      array(
        ['no' => '01', 'name' => 'Comments In Home'],
        ['no' => '02', 'name' => 'Comments In Friends-timeline'],
        ['no' => '03', 'name' => 'Comments In Groups'],
        ['no' => '04', 'name' => 'Comments In FansPage'],
        ['no' => '00', 'name' => 'Back'],
      )
    );
    return json_decode($menu);
  }
}