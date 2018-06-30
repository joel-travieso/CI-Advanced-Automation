<?php

/**
 * @file
 * Creates the release branch if it doesn't exist and there are changes in the base branch.
 */

require 'utility_functions.php';

// Github params.
$github_token = $argv[1];
$github_user = $argv[2];
$repo = $argv[3];

// Specific params
$head = $argv[4];
$base = $argv[5];
$title = $argv[6];
$body = $argv[7];

print_r("Attempting to create $title PR...");
$result = createPR($head, $base, $title, $github_token, $github_user, $repo, $body);
if (!empty($result['errors'])) {
  print_r("ERROR: " . $result['errors'][0]['message']);
}
else {
  print_r("PR $title created at " .  $result['url']);
}
