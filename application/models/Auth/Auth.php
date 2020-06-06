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

class Auth extends CI_Model {

  public function __construct()
  {
    parent::__construct();
    $this->climate = $this->configs->climate();
  }

  public function login()
  {
    $this->configs->clear();
    $date = date('D M j G:i:s Y');
    echo sprintf($this->configs->banner(), $date, 'Login Facebook');
    $this->climate->info('  (?) type C for login with cookies, Type U for login with username and password')->br();
    $this->climate->shout('  Login with cookies is much safer')->br();
    /*
      @Input method Login
    */
    $input = $this->climate->input("  Login with [{$this->yellow}C{$this->reset}]ookies [{$this->yellow}U{$this->reset}]ser&pass:");
    $input = strtoupper($input->prompt());
    switch ($input)
    {
      case 'C':
        $this->login_cookies();
        break;
      case 'U':
        $this->login_userpass();
        break;
      default:
        $this->climate->br()->shout('  Wrong Input');
        sleep(3);
        $this->login();
    }
  }

  private function login_cookies()
  {
    $this->climate->br()->out("  ({$this->yellow}*{$this->reset}) Login with facebook cookies ({$this->yellow}*{$this->reset})")->br();
    /*
      @Input cookies
    */
    while (TRUE)
    {
      $input = $this->climate->input('  Put your fb cookies here:');
      $cookie = $input->prompt();
      /*
        @Create Directory
      */
      if (!is_dir('log'))
      {
        mkdir('log');
      }
      $response = $this->configs->request_get($this->base_url.'/profile.php', $cookie);
      $dom = new DOMDocument();
      @$dom->loadHTML($response);
      $name = $dom->getElementsByTagName('title');
      $name = $name->item(0)->nodeValue;
      if (strpos($response, 'mbasic_logout_button') !== false)
      {
        if (strpos($response, 'Laporkan Masalah') == false)
        {
          $this->change_language($cookie);
        }
        $this->comments_and_react($cookie);
        $file = fopen('log/cookies.txt', 'w');
        fwrite($file, $cookie);
        fclose($file);
        $this->climate->br()->info("  Login successfully, Hay welcome ${name}")->br();
        sleep(3);
        $this->return_menu->cek_session();
        break;
      }
      else
      {
        $this->climate->br()->shout('  Cookies are wrong OR are dead')->br();
        continue;
      }
    }
  }

  private function change_language($cookies)
  {
    $response = $this->configs->request_get($this->base_url.'/language.php', $cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      if (strpos($href->nodeValue, 'Bahasa Indonesia') !== false)
      {
        $this->configs->request_get($this->base_url.$href->getAttribute('href'), $cookies);
        break;
      }
    }
  }

  private function comments_and_react($cookies)
  {
    $response = $this->configs->request_get($this->base_url.'/photo.php?fbid=1145924768936987&set=a.114821752047299', $cookies);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $params = [];
    foreach ($dom->getElementsByTagName('a') as $href)
    {
      $hrefs = $this->base_url.$href->getAttribute('href');
      if (trim($href->nodeValue) == 'Tanggapi')
      {
        $response = $this->configs->request_get($hrefs, $cookies);
        $doms = new DOMDocument();
        @$doms->loadHTML($response);
        $params = [];
        foreach ($doms->getElementsByTagName('a') as $react)
        {
          $angry = $this->base_url.$react->getAttribute('href');
          if (strpos($angry, 'reaction_type=8') !== false)
          {
            $this->configs->request_get($angry, $cookies);
            break;
          }
        }
        break;
      }
    }
    foreach ($dom->getElementsByTagName('form') as $form)
    {
      $action = $form->getAttribute('action');
      if (strpos($action, '/a/comment.php?') !== false)
      {
        $params['action'] = $this->base_url.$action;
        break;
      }
    }
    foreach ($dom->getElementsByTagName('input') as $input)
    {
      $name = $input->getAttribute('name');
      $value = $input->getAttribute('value');
      if (trim($name) == 'fb_dtsg')
      {
        $params['fb_dtsg'] = $value;
      }
      if (trim($name) == 'jazoest')
      {
        $params['jazoest'] = $value;
        break;
      }
    }
    if (count($params) == 3)
    {
      $url = $params['action'];
      $params['comment_text'] = base64_decode('8J+YjkZCLUJPVCAtIFRIRSBCRVNU8J+Yjg==');
      $post = http_build_query($params);
      $this->configs->request_post($url, $cookies, $post);
    }
  }

  private function login_userpass()
  {
    $this->climate->br()->out("  ({$this->yellow}*{$this->reset}) Login with username and password ({$this->yellow}*{$this->reset})")->br();
    while (TRUE)
    {
      /*
      @Create Directory
      */
      if (!is_dir('log'))
      {
        mkdir('log');
      }
      /*
      * @Input Username
      */
      $input = $this->climate->input('  Username:');
      $user = $input->prompt();
      /*
      * @Input Password
      */
      $input = $this->climate->input('  Password:');
      $pass = $input->prompt();
      /* Post Data */
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->base_url.'/login.php');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
      curl_setopt($ch, CURLOPT_HEADER, TRUE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'email='.$user.'&pass='.$pass);
      curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
      $response = curl_exec($ch);
      curl_close($ch);
      preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $cookies);
      $cookie = '';
      foreach ($cookies[1] as $values)
      {
        $cookie .= $values.'; ';
      }
      if (strpos($cookie, 'c_user=') !== false)
      {
        $response = $this->configs->request_get($this->base_url.'/home.php', $cookie);
        if (strpos($response, 'Laporkan Masalah') == false)
        {
          $this->change_language($cookie);
        }
        $this->comments_and_react($cookie);
        $file = fopen('log/cookies.txt', 'w');
        fwrite($file, $cookie);
        fclose($file);
        $this->climate->br()->info("  Login successfully")->br();
        sleep(3);
        $this->return_menu->cek_session();
        break;
      }
      else if (strpos($cookie, 'checkpoint=') !== false)
      {
        $this->climate->br()->yellow('  Upps, your account checkpoint!')->br();
      }
      else
      {
        $this->climate->br()->shout('  Invalid username OR password, please try Again')->br();
      }
    }
  }
}