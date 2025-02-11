<?php

header("Content-Type: application/json");

require_once 'Rover.php';
require_once 'Planet.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $data = json_decode(file_get_contents("php://input"), true);

    $validDirections = ['N', 'S', 'E', 'W'];
    $validCommands = ['f', 'b', 'l', 'r'];
    $errors = [];

    if(!isset($data['x'], $data['y'], $data['direction'], $data['dimension'], $data['commands'], $data['obstacles'])) {
        $errors[] = "Missing required parameters: 'x', 'y', 'direction', 'dimension', or 'commands'.";
    }else{
       
        if(!is_int($data['x']) || !is_int($data['y']) || !is_int($data['dimension']) || $data['x'] < 0 || 
            $data['y'] < 0 || $data['dimension'] < 0 || $data['x'] >= $data['dimension'] || $data['y'] >= $data['dimension']
        ){
            $errors[] = "Invalid numerical values for 'x', 'y', or 'dimension'.";
        }
    
        if(!in_array(strtoupper($data['direction']), $validDirections)) {
            $errors[] = "Invalid direction. Must be 'N', 'S', 'E', or 'W'.";
        }
    
        if(!is_array($data['commands'])) {
            $errors[] = "Invalid commands. They must be an array.";
        } else {
            $validCommand = true;
            $data['commands'] = array_map(function ($item) {
                return is_string($item) ? strtolower($item) : $item;
            }, $data['commands']);

            for($i = 0; $i < count($data['commands']) && $validCommand; $i++){
                if (!in_array($data['commands'][$i], $validCommands)) {
                    $validCommand = false;
                    $errors[] = "Invalid command. Allowed: 'f', 'b', 'l', 'r'.";
                }
            }
        }

        if(!is_array($data['obstacles'])) {
            $errors[] = "Invalid obstacles. They must be an array of [x, y] coordinates.";
        } else {
            $validObstacle = true;

            for($i = 0; $i < count($data['obstacles']) && $validObstacle; $i++) {
                if (!is_array($data['obstacles'][$i]) || count($data['obstacles'][$i]) !== 2 || 
                    !is_int($data['obstacles'][$i][0]) || !is_int($data['obstacles'][$i][1]) || 
                    $data['obstacles'][$i][0] < 0 || $data['obstacles'][$i][1] < 0 || 
                    $data['obstacles'][$i][0] >= $data['dimension'] || $data['obstacles'][$i][1] >= $data['dimension']
                    ) {
                    $validObstacle = false;
                    $errors[] = "Invalid obstacle. Must be [x, y] within grid.";
                }
            }
        }
    }

    if (!empty($errors)) {
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(array('outcome' => 'error' , 'response' => $errors));
        exit;
    }

    $x = $data['x'];
    $y = $data['y'];
    $direction = strtoupper($data['direction']);
    $dimension = $data['dimension'];
    $commands = $data['commands'];
    $obstacles = $data['obstacles'];

    $planet = new Planet($dimension, $obstacles);
    $rover = new Rover($x, $y, $direction);

    if($rover ->executeCommands($commands, $planet)){
        $response = 'I\'m arrived at (' . $rover->getCoordX() . ', ' . $rover->getCoordY() . ') facing ' . $rover->getDirection();
    }else{
        $response = 'Sequence aborted. Obstacle found. I am currently at (' . $rover->getCoordX() . ', ' . $rover->getCoordY() . ') facing ' . $rover->getDirection();
    }

    header("HTTP/1.1 200 Success");
    echo json_encode(array('outcome' => 'success', 'response' => $response));
    exit;

}else{
    
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(array('outcome' => 'error', 'response' => "Only POST is allowed."));
    exit;
}

?>