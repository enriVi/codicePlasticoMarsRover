<?php

class Planet{

    private $dimension;
    private $obstacles;

    public function __construct($dimension, $obstacles){
        $this->dimension = $dimension;
        $this->obstacles = $obstacles;
    }

    public function isObstacle($x, $y){
        return in_array([$x, $y], $this->obstacles);
    }

    public function wrapEdge($coord){
        if($coord < 0){
            $coord = $this->dimension - 1;
        }
        else if($coord >= $this->dimension){
            $coord = 0;
        }
        return $coord;
    }
}

?>