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

$pr = getPRObject($pr_number, $github_token, $github_user, $repo);
if ($pr['base']['ref'] == 'master' && $pr['head']['ref'] != 'develop' && strpos($pr['title'], 'hotfix') === FALSE) {
  print_r("Aborting non-develop PR pointing to the master branch. If this is a hotfix, include 'hotfix' in the PR title.");
  exit(1);
}
