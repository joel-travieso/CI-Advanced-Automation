<?php

/**
 * @file
 * Utility functions.
 */

/**
 * Get a data array for a single PR.
 */
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

/**
 * Create a PR.
 */
function createPR($head, $base, $title, $github_token, $github_user, $repo, $body = '') {
  $post_string = json_encode([
    'title' => $title,
    'body' => $body,
    'head' => $head,
    'base' => $base
  ]);
  $ch = curl_init();  
  curl_setopt($ch,CURLOPT_URL,"https://api.github.com/repos/$repo/pulls");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_USERPWD, "$github_user:$github_token");
  curl_setopt($ch, CURLOPT_USERAGENT, "$github_user");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($post_string))                                                                       
  );
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

  $output = json_decode(curl_exec($ch), TRUE);
  curl_close($ch);
  return $output;
}


/**
 * Get a data array for a single PR.
 */
function getLabels($pr_number, $github_token, $github_user, $repo) {
  $ch = curl_init();  
  curl_setopt($ch,CURLOPT_URL,"https://api.github.com/repos/$repo/issues/$pr_number/labels");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_USERPWD, "$github_user:$github_token");
  curl_setopt($ch, CURLOPT_USERAGENT, "$github_user");

  $output=json_decode(curl_exec($ch), TRUE);
  curl_close($ch);
  return $output;
}

/**
 * Set labels for a PR.
 */
function setLabels($pr_number, $github_token, $github_user, $repo, $labels_add, $labels_remove) {
  $ch = curl_init();  

  $post_string = json_encode($labels_add);
  curl_setopt($ch,CURLOPT_URL,"https://api.github.com/repos/$repo/issues/$pr_number/labels");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_USERPWD, "$github_user:$github_token");
  curl_setopt($ch, CURLOPT_USERAGENT, "$github_user");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($post_string))                                                                       
  );
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
  curl_exec($ch);

  foreach ($labels_remove as $label) {
    curl_setopt($ch,CURLOPT_URL,"https://api.github.com/repos/$repo/issues/$pr_number/labels/$label");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_exec($ch);
  }
  curl_close($ch);
}

/**
 * Extracts a Jira issue ID from a text.
 */
function getIssueID($text) {
  preg_match("#IC-[0-9]+#", $text, $matches);
  if (!empty($matches)) {
    return $matches[0];
  }
  return NULL;
}

/**
 * Posts a comment in a Slack DM.
 */
function commentOnSlack($text, $slack_token, $recipient) {
  $post_string = json_encode([
    'text' => $text,
    'channel' => $recipient,
    'username' => 'ICBot',
    'icon_emoji' => ':circleci:',
  ]);

  $ch = curl_init();  
  curl_setopt($ch,CURLOPT_URL,"https://slack.com/api/chat.postMessage");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: Bearer $slack_token"
  ]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

  $output=json_decode(curl_exec($ch), TRUE);
  curl_close($ch);
  return $output;
}


/**
 * Posts a comment in a Jira issue.
 */
function commentOnJira($text, $issue_id, $jira_user, $jira_token, $jira_space) {
  $post_string = json_encode([
    'body' => $text,
  ]);
  $ch = curl_init();  
  curl_setopt($ch,CURLOPT_URL,"https://$jira_space.atlassian.net/rest/api/2/issue/$issue_id/comment");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_USERPWD, "$jira_user:$jira_token");
  curl_setopt($ch, CURLOPT_USERAGENT, "$jira_user");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($post_string))                                                                       
  );
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

  $output=json_decode(curl_exec($ch), TRUE);
  curl_close($ch);
  return $output;
}

/**
 * Get an array of comments for a Jira issue.
 */
function getIssueComments($issue_id, $jira_user, $jira_token, $jira_space) {
  $ch = curl_init();  
  curl_setopt($ch,CURLOPT_URL,"https://$jira_space.atlassian.net/rest/api/2/issue/$issue_id/comment");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_USERPWD, "$jira_user:$jira_token");
  curl_setopt($ch, CURLOPT_USERAGENT, "$jira_user");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

  $output=json_decode(curl_exec($ch), TRUE);
  curl_close($ch);
  return $output;
}

/**
 * Get the status for a Jira issue.
 */
function getIssueStatus($issue_id, $jira_user, $jira_token, $jira_space) {
  $ch = curl_init();  
  curl_setopt($ch,CURLOPT_URL,"https://$jira_space.atlassian.net/rest/api/2/status/$issue_id");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_USERPWD, "$jira_user:$jira_token");
  curl_setopt($ch, CURLOPT_USERAGENT, "$jira_user");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

  $output=json_decode(curl_exec($ch), TRUE);
  curl_close($ch);
  return $output;
}
