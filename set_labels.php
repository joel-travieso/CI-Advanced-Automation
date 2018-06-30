<?php

/**
 * @file
 * Handles PR labels automation.
 */

require 'utility_functions.php';

// Github params.
$pr_number = $argv[1];
$github_token = $argv[2];
$github_user = $argv[3];
$repo = $argv[4];

if (empty(getLabels($pr_number, $github_token, $github_user, $repo))) {
  setLabels($pr_number, $github_token, $github_user, $repo, ['work in progress'], []);
}
else {
  print_r("The PR already has labels applied.");
}