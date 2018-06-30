<?php

/**
 * @file
 * Commits a changelog entry correponding to the current PR.
 */

require 'utility_functions.php';

// Github params.
$pr_number = $argv[1];
$github_token = $argv[2];
$github_user = $argv[3];
$repo = $argv[4];
$file_path = $argv[5];
$row_head = "[[PR-$pr_number]]: ";

$content = file_get_contents($file_path);
if (!strpos($content, $row_head)) {
  $pr = getPRObject($pr_number, $github_token, $github_user, $repo);
  file_put_contents($file_path, $content . $row_head . $pr["title"] . "\n");
  print_r("Changelog entry created.");

  $issue_id = getIssueID($pr['title']);
  $branch = $pr["head"]["ref"];
  exec("git add $file_path");
  exec("git commit -m '$issue_id: Changelog entry'");
  print_r("Pushing back to origin...\n");
  exec("git push origin $branch");
}
else {
  print_r("Changelog is already updated.");
}
