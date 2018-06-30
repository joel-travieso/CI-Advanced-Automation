<?php

/**
 * @file
 * Checks the branch Circle is running over, and sets the multidev environment 
 * name to 'develop' if this is a deployment PR.
 */

$branch = $argv[1];
$env = $argv[2];
$deployment_branch_name = $argv[3];
$circle_build = $argv[4];

if ($branch == $deployment_branch_name) {
  print_r("ci-dev-$circle_build");
}
else {
  print_r($env);
}
