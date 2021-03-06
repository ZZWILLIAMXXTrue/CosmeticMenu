<?php 

namespace NinjaKnights\CosmeticMenu\cosmetics\Particles;

use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

use pocketmine\level\particle\GenericParticle;
use pocketmine\level\particle\Particle;

use NinjaKnights\CosmeticMenu\Main;

class EmeraldTwirl extends Task {
	
	public function __construct(Main $main) {
        $this->main = $main;
        $this->r = 0;
    }
    
    public function onRun($tick) {
        foreach($this->main->getServer()->getOnlinePlayers() as $player) {
            $name = $player->getName();
            $level = $player->getLevel();
        
            $x = $player->getX();
            $y = $player->getY();
            $z = $player->getZ();
            if(in_array($name, $this->main->particle8)) {
				if($this->r < 0){
					$this->r++;
					return true;
				}
	     		$size = 1;
		   	    $a = cos(deg2rad($this->r/0.09))* $size;
				$b = sin(deg2rad($this->r/0.09))* $size;
				$c = sin(deg2rad($this->r/0.2))* $size;
				$level->addParticle(new GenericParticle(new Vector3($x - $a, $y + $c + 1.4, $z - $b), Particle::TYPE_VILLAGER_HAPPY));
				$this->r++;
            } 	
        }
    }

}