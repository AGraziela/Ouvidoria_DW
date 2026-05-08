<?php
// ============================================================
//  OUVIDORIA - EEEP DOM WALFRIDO
//  Arquivo: logout.php
//  Destrói a sessão e redireciona para index com ?logout=1
// ============================================================
 
session_start();
session_unset();
session_destroy();
 
header("Location: index.html?logout=1");
exit();
 