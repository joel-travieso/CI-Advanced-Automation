<?php

/**
 * @file
 * Runs rollback & import of legacy migration on the chosen environment.
 */

define('MAXIMUM_ITEMS_LEFT', 5);

$env = $argv[1];
$time = microtime(true);

print_r("---> Rolling back last migration items...");
exec("terminus drush ithaca-www.$env -- mrs legacy");
$items_imported = getMigrationStatus($env)[4];

while ($items_imported > 0) {
  print_r("NOTICE: $items_imported items left to rollback...");
  exec("terminus drush ithaca-www.$env -- mr legacy || true");
  exec("terminus drush ithaca-www.$env -- mrs legacy");
  $items_imported = getMigrationStatus($env)[4];
}

print_r("---> Beginning to re-import content...");
$items_left = getMigrationStatus($env)[5];
while ($items_left > MAXIMUM_ITEMS_LEFT) {
  print_r("NOTICE: $items_left items left to import...");
  exec("terminus drush ithaca-www.$env -- mi legacy || true");
  exec("terminus drush ithaca-www.$env -- mrs legacy");
  $items_left = getMigrationStatus($env)[5];
}

$time = (microtime(true) - $time)/60;
print_r("---> Finished migration in $time minutes.");

function getMigrationStatus($env) {
  exec("terminus drush ithaca-www.$env -- ms legacy", $envs);
  foreach ($envs as $key => $value) {
    $parts = preg_split('/\s+/', $value);
    if ($parts[1] == 'legacy') {
      return $parts;
    }
  }
  echo "Unexpected status response:\n";
  echo implode("\n", $envs), "\n";
  exit;
}
