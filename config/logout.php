<?php
session_start();

// Desloga o usuário
session_unset();
session_destroy();

header("Location: ../index.html");
exit();
?>