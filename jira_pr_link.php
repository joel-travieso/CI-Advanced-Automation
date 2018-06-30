<?php

/**
 * @file
 * Posts a link to the PR as a comment at the issue whose ID is specified in the title of the PR.
 */

require 'utility_functions.php';

// Github params.
$pr_number = $argv[1];
$github_token = $argv[2];
$github_user = $argv[3];
$repo = $argv[4];

// Jira params.
$jira_user = $argv[5];
$jira_token = $argv[6];
$jira_space = $argv[7];

// Get PR data.
$pr_data = getPRObject($pr_number, $github_token, $github_user, $repo);
// Find the Issue ID.
$issue_id = getIssueID($pr_data['title']);
if (!empty($issue_id )) {
  $comment_text = "[PR]: " . $pr_data['url'];
  print_r("Issue $issue_id found. ");
  // Get comments from the issue.
  $comments = getIssueComments($issue_id, $jira_user, $jira_token, $jira_space);
  // If the PR has already been linked, abort.
  foreach ($comments['comments'] as $comment) {
    if ($comment['body'] == $comment_text) {
      print_r("Already linked to the PR. Ignoring.");
      return;
    }
  }
  // Post a comment in the issue.
  $comment_result = commentOnJira($comment_text, $issue_id, $jira_user, $jira_token, $jira_space);
  // Handle error while trying to comment.
  if (!empty($comment_result['errorMessages'])) {
    print_r("ERROR: " . $comment_result['errorMessages'][0]);
  }
  else {
    print_r("Comment successfully posted.");
  }
}
else {
	print_r("Issue ID not found");
}
