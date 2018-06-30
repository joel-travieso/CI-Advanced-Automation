<?php

/**
 * @file
 * Removes all multidev environments whose corresponding PR is not open.
 */

$github_token = $argv[1];
$github_user = $argv[2];
$repo = $argv[3];

exec("terminus build-env:list ithaca-www '^pr-'", $envs);

for ($i = 3; $i < count($envs) - 1; $i++) {
  $pieces = explode(' ', $envs[$i]);
  $branch_name = $pieces[2];
  print_r("Checking $branch_name environment.");
  $number = getPRNumberFromEnv($branch_name);
  if ($number) {
    $pr_data = getPRObject($number, $github_token, $github_user, $repo);
    // print_r($pr_data['state']);    
    if (!isset($pr_data['state']) || $pr_data['state'] != 'open') {
      //Remove the environment
      print_r("There is no pull request numbered $number or it is not open. Proceeding to remove.");    
      exec("terminus multidev:delete ithaca-www.$branch_name --yes --delete-branch", $output, $result);
      if (!$result) {
        print_r("Removed $branch_name environment from Pantheon as there is not an open PR for it.");    
      }
      else {
        print_r("There was a problem trying to remove $branch_name environment. Exit code $result.");    
      }
    }
    else {
      print_r("$branch_name will be kept.");    
    }
  }
  else {
    print_r("Cannot identify a pull request number from $branch_name.");    
  }
}

function getPRNumberFromEnv($name) {
  $pieces = explode('-', $name);
  if(isset($pieces[1]) && is_numeric($pieces[1])) {
    return $pieces[1];
  }
  return 0;
}

function getPRObject($pr_number, $github_token, $github_user, $repo) {
  $ch = curl_init();  
  curl_setopt($ch,CURLOPT_URL,"https://api.github.com/repos/$repo/pulls/$pr_number");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_USERPWD, "$github_user:$github_token");
  curl_setopt($ch, CURLOPT_USERAGENT, "$github_user");

  $output=json_decode(curl_exec($ch), TRUE);
  curl_close($ch);
  return $output;
}
