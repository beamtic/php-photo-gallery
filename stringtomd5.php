<?php
// This file can be used to create a new md5 hash from a password.
// In the future the Gallery will do this automatically

if ((isset($_GET['string'])) && (strlen($_GET['string'])) <= 200) {
    echo md5($_GET['string']);
} else {
  echo 'Max 200 characters...';
}