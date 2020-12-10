<?php 

namespace NinjaKnights\CosmeticMenu\forms;
    
use NinjaKnights\CosmeticMenu\Main;
use NinjaKnights\CosmeticMenu\EventListener;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
    
class HatForm {
    
    private $main;

    public function __construct(Main $main){
        $this->main = $main;
    }

    public function openHats($player) {
        $form = new SimpleForm(function (Player $player, $data) {
        $result = $data;
            if($result === null) {
                return true;
            }
            switch($result) {
                //TV Hat
                case 0:
                    if($player->hasPermission("cosmeticmenu.hats.tv")){
                        $name = $player->getName();
                        
                        if(!in_array($name, $this->main->hat1)) {

                            $this->unsetSuits($player);

                            $this->main->setSkin()->setSkin($player, "tv", "hats");
                            $this->main->hat1[] = $name;
                            
                            if(in_array($name, $this->main->hat2)) {
                                unset($this->main->hat2[array_search($name, $this->main->hat2)]);
                            }

                        } else {
                            $this->unsetHats($player);
                        }
                        
                    }
                break;
                //Melon Hat
                case 1:
                    if($player->hasPermission("cosmeticmenu.hats.melon")){
                        $name = $player->getName();
                        
                        if(!in_array($name, $this->main->hat2)) {

                            $this->unsetSuits($player);

                            $this->main->setSkin()->setSkin($player, "melon", "hats");
                            $this->main->hat2[] = $name;
                            
                            if(in_array($name, $this->main->hat1)) {
                                unset($this->main->hat1[array_search($name, $this->main->hat1)]);
                            }

                        } else {
                            $this->unsetHats($player);
                        }
                        
                    }
                break;
				
                case 2:
                    $this->unsetHats($player);
                    $this->unsetSuits($player);
				break;
				
				case 3:
                    $this->main->getForms()->menuForm($player);   
                break;
            }
        });
        $hatcfg = $this->main->hatcfg;
        $form->setTitle($hatcfg->getNested("Name"));
        $form->setContent($hatcfg->getNested("Form-Content"));
        $perm = "cosmeticmenu.hats.";
        if($hatcfg->getNested("TV-Hat.Enable")){
            $this->hatSupport = true;
            if($player->hasPermission($perm ."tv")){
                $form->addButton("TV Hat",0,"",0);
            }
        }

        if($hatcfg->getNested("Melon-Hat.Enable")){
            $this->hatSupport = true;
            if($player->hasPermission($perm ."melon")){
                $form->addButton("Melon Hat",0,"",1);
            }
        }
        $form->addButton("Clear",0,"",2);
        $form->addButton("§l§8<< Back",0,"",3);
        $form->sendToPlayer($player);
        return $form;
    }

    public function resetSkin(Player $player)
    {
        $player->sendPopup("§aReset to original skin successfull");
        $reset = $this->main->resetSkin();
        $reset->setSkin($player);
    }

    public function unsetHats(Player $player){
        $name = $player->getName();
        $this->resetSkin($player);
       
        if(in_array($name, $this->main->hat1)) {
            unset($this->main->hat1[array_search($name, $this->main->hat1)]);
        }elseif(in_array($name, $this->main->hat2)) {
            unset($this->main->hat2[array_search($name, $this->main->hat2)]);
        }
        $player->removeAllEffects();
    }

    public function unsetSuits(Player $player){
        $name = $player->getName();
       
        if(in_array($name, $this->main->suit1)) {
            unset($this->main->suit1[array_search($name, $this->main->suit1)]);
        }elseif(in_array($name, $this->main->suit2)) {
            unset($this->main->suit2[array_search($name, $this->main->suit2)]);
        }
        $player->removeAllEffects();
    }

}