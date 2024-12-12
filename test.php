<?php
$inputPassword = "510401"; // 用户输入的密码
$storedHash = "$2b$12$4GgpSPA11MNc0h46Ala4W.qUoIQOp3SprlRupFoy8R7OYJ5wJck.e";

if (password_verify($inputPassword, $storedHash)) {
    echo "Password verified successfully.";
} else {
    echo "Password verification failed.";
}
