<?php
$app = $_GET['app'];
$score = $_GET['score'];
if (isset($app) && isset($score)) {
    $valid = ['pdf','slides'];
    if (in_array($app, $valid) && is_numeric($score)) {
        $len = max(array_map('strlen',$valid));
        $app = substr($app,0,$len);
        $score = min(2, max(intval($score,10),-2));
        $options = array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        );
        $database = new PDO("mysql:host=127.0.0.1;dbname=rating;port=3306;charset=utf8", getenv("DB_USER"), getenv("DB_PASS"), $options);
        $query = $database->prepare('INSERT INTO stats (app,score) VALUES (:a,:b)');
        $params = [':a'=>$app,':b'=>$score];
        $query->execute($params);
    };
}