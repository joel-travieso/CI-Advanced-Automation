<?php

/**
 * @file
 * Check if the destination branch is correct.
 */

require 'utility_functions.php';

// Github params.
$pr_number = $argv[1];
$github_token = $argv[2];
$github_user = $argv[3];
$repo = $argv[4];
$build_number = $argv[5];
$slack_token = $argv[6];

$slack_user_ids = [
  'joel-travieso' => 'D3U614FME',
  'ModulesUnraveled' => 'D473W9HNZ'
];

$pr = getPRObject($pr_number, $github_token, $github_user, $repo);
if (isset($slack_user_ids[$pr['user']['login']])) {
  $slack_id = $slack_user_ids[$pr['user']['login']];
  commentOnSlack("Your build failed: https://circleci.com/gh/IthacaCollege/www-ithaca/$build_number", $slack_token, $slack_id);
}
