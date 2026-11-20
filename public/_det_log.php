<?php file_put_contents(__DIR__.'/_det_log.txt', file_get_contents('php://input')."\n", FILE_APPEND);
