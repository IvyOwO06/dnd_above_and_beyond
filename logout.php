<?php

session_start();
unset($_SESSION['user']);
echo "<script>
alert('Log out successful!');
window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';
</script>";
exit();
?>