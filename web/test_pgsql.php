<?php

$link = pg_connect("host=localhost port=5432 dbname=db_test user=postgres password=admin");
print_r($link);