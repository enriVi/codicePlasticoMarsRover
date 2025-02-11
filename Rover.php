<?php

require_once 'Planet.php';

class Rover {

    private $coordX;
    private $coordY;
    private $direction;

    private const COORDINATES = ['N', 'E', 'S', 'W'];

    public function __construct($coordX, $coordY, $direction){
        $this->coordX = $coordX;
        $this->coordY = $coordY;
        $this->direction = $direction;
    }

    public function getCoordX(){
        return $this->coordX;
    }

    public function getCoordY(){ 
        return $this->coordY;
    }

    public function getDirection(){  
        return $this->direction;
    }

    public function rotate($rotation){

        $index = array_search($this->direction, self::COORDINATES);

        switch($rotation){

            case 'r':
                if($index == count(self::COORDINATES) - 1){
                    $index = 0;
                }else{
                    $index = $index + 1;
                }
    
                $this->direction = self::COORDINATES[$index];
                break;

            case 'l':
                if($index == 0){
                    $index = count(self::COORDINATES) - 1;
                }else{
                    $index = $index - 1;
                }
    
                $this->direction = self::COORDINATES[$index];
                break;
        }
    }

    public function move($movement, Planet $planet){

        $newCoord = 0;

        switch($this->direction){

            case 'N':

                $newCoord = ($movement == 'f' ? $this->coordY - 1 : $this->coordY + 1);
                $newCoord = $planet->wrapEdge($newCoord);

                if($planet->isObstacle($this->coordX, $newCoord)){
                    return false;
                }

                $this->coordY = $newCoord;
                break;

            case 'E':

                $newCoord = ($movement == 'f' ? $this->coordX + 1 : $this->coordX - 1);
                $newCoord = $planet->wrapEdge($newCoord);

                if($planet->isObstacle($newCoord, $this->coordY)){
                    return false;
                }

                $this->coordX = $newCoord;
                break;

            case 'S':

                $newCoord = ($movement == 'f' ? $this->coordY + 1 : $this->coordY - 1);
                $newCoord = $planet->wrapEdge($newCoord);

                if($planet->isObstacle($this->coordX, $newCoord)){
                    return false;
                }

                $this->coordY = $newCoord;
                break;

            case 'W':

                $newCoord = ($movement == 'f' ? $this->coordX - 1 : $this->coordX + 1);
                $newCoord = $planet->wrapEdge($newCoord);

                if($planet->isObstacle($newCoord, $this->coordY)){
                    return false;
                }

                $this->coordX = $newCoord;
                break;

            default: 
                return false;
                break;
        }

        return true;
    }

    public function executeCommands($commands, Planet $planet){
        
        foreach($commands as $command){
            switch($command){
                case 'f':
                case 'b':
                    if(!$this->move($command, $planet))
                        return false;
                    break;

                case 'l':
                case 'r':
                    $this->rotate($command);
                    break;
                
                default:
                    return false;
                    break;
            }
        }

        return true;
    }
}

?>