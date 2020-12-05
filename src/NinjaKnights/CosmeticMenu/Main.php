<?php

namespace NinjaKnights\CosmeticMenu;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use NinjaKnights\CosmeticMenu\forms\GadgetForm;
use NinjaKnights\CosmeticMenu\forms\MainForm;
use NinjaKnights\CosmeticMenu\forms\MorphForm;
use NinjaKnights\CosmeticMenu\forms\ParticleForm;
use NinjaKnights\CosmeticMenu\forms\SuitForm;
use NinjaKnights\CosmeticMenu\forms\HatForm;
use NinjaKnights\CosmeticMenu\forms\TrailForm;

use NinjaKnights\CosmeticMenu\EventListener;
use NinjaKnights\CosmeticMenu\util\saveRes;
use NinjaKnights\CosmeticMenu\skin\setSkin;
use NinjaKnights\CosmeticMenu\skin\saveSkin;
use NinjaKnights\CosmeticMenu\skin\resetSkin;

use NinjaKnights\CosmeticMenu\cosmetics\Gadgets\GadgetsEvents;
use NinjaKnights\CosmeticMenu\cosmetics\Gadgets\TNTLauncher;

use NinjaKnights\CosmeticMenu\cosmetics\Particles\BlizzardAura;
use NinjaKnights\CosmeticMenu\cosmetics\Particles\BulletHelix;
use NinjaKnights\CosmeticMenu\cosmetics\Particles\BloodHelix;
use NinjaKnights\CosmeticMenu\cosmetics\Particles\ConduitHalo;
use NinjaKnights\CosmeticMenu\cosmetics\Particles\CupidsLove;
use NinjaKnights\CosmeticMenu\cosmetics\Particles\EmeraldTwirl;
use NinjaKnights\CosmeticMenu\cosmetics\Particles\FlameRings;
use NinjaKnights\CosmeticMenu\cosmetics\Particles\RainCloud;
use NinjaKnights\CosmeticMenu\cosmetics\Particles\WitchCurse;

use NinjaKnights\CosmeticMenu\cosmetics\Trails\Flames;
use NinjaKnights\CosmeticMenu\cosmetics\Trails\Snow;
use NinjaKnights\CosmeticMenu\cosmetics\Trails\Heart;
use NinjaKnights\CosmeticMenu\cosmetics\Trails\Smoke;

use NinjaKnights\CosmeticMenu\cosmetics\Suits\Youtube;
use NinjaKnights\CosmeticMenu\cosmetics\Suits\Frog;

use NinjaKnights\CosmeticMenu\command\CosmeticCommand;

class Main extends PluginBase {

	public $particle1 = array("Rain Cloud");
	public $particle2 = array("Flame Rings");
	public $particle3 = array("Blizzard Aura");
	public $particle4 = array("Cupid's Love");
	public $particle5 = array("Bullet Helix");
	public $particle6 = array("Conduit Aura");
	public $particle7 = array("Witch Curse");
	public $particle8 = array("Emerald Twril");
	public $particle9 = array("Blood Helix");

	public $trail1 = array("Flame Trail");
	public $trail2 = array("Snow Trail");
	public $trail3 = array("Heart Trail");
	public $trail4 = array("Smoke Trail ");

	public $suit1 = array("YouTube Suit");
	public $suit2 = array("Frog Suit");

	public $hat1 = array("TV Hat");
	public $hat2 = array("Melon Hat");

	public function onLoad() {
		$commands = [new CosmeticCommand($this)];
        $this->getServer()->getCommandMap()->registerAll("cosmetic", $commands);
	}

	public function onEnable() {

		$this->loadEvents();
		$this->loadTasks();
		$this->loadFormClass();
		$this->loadSkinClass();

		$saveRes = new saveRes($this);
		$saveRes->saveRes();
		$this->initConfig();

		$configPath = $this->getDataFolder()."config.yml";
		$this->saveDefaultConfig();
		$this->config = new Config($configPath, Config::YAML);
		$version = $this->config->get("Version");
		$pluginVersion = $this->getDescription()->getVersion();
		if($version < $pluginVersion){
			$this->getLogger()->warning("You have updated CosmeticMenu to v$pluginVersion but your config is from v$version! Please delete your old config for new features to be enabled and to prevent errors!");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}

		$this->cosmeticName = (str_replace("&", "§", $this->config->getNested("Name")));
		$this->cosmeticFormContent = (str_replace("&", "§", $this->config->getNested("Form-Content")));
		//Cosmetic Item Support
		if($this->config->getNested("Cosmetic.Enable")){
			$this->cosmeticItemSupport = true;
			$this->cosmeticDes = [str_replace("&", "§", $this->config->getNested("Cosmetic.Des"))];
			$this->cosmeticItemType = $this->config->getNested("Cosmetic.Item");
			$this->cosmeticForceSlot = $this->config->getNested("Cosmetic.Force-Slot");
		} else{
			$this->cosmeticItemSupport = false;
			$this->getLogger()->info("The Cosmetic Item is disabled in the config.");
		}
		//Cosmetic Command Support
		if($this->config->getNested("Command")){
			$this->cosmeticCommandSupport = true;
		} else {
			$this->cosmeticCommandSupport = false;
			$this->getLogger()->info("The Cosmetic Command is disabled in the config.");
		}
		
	}

	private function initConfig() : void {
		$this->particlecfg = new Config($this->getDataFolder()."particles.yml", Config::YAML);
		$this->suitcfg = new Config($this->getDataFolder()."suits.yml", Config::YAML);
		$this->trailcfg = new Config($this->getDataFolder()."trails.yml", Config::YAML);
		$this->hatcfg = new Config($this->getDataFolder()."hats.yml", Config::YAML);
		$this->morphcfg = new Config($this->getDataFolder()."morphs.yml", Config::YAML);
	}

	private function loadFormClass() : void {
		$this->forms = new MainForm($this);
		$this->gadgets = new GadgetForm($this);
		$this->particles = new ParticleForm($this);
		$this->morphs = new MorphForm($this);
		$this->trails = new TrailForm($this);
		$this->suits = new SuitForm($this);
		$this->hats = new HatForm($this);
	}

	private function loadSkinClass() : void {
		$this->setskin = new setSkin($this);
		$this->saveskin = new saveSkin($this);
		$this->resetskin = new resetSkin($this);
	}

	private function loadEvents() : void {
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new GadgetsEvents($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new TNTLauncher($this), $this);
	}

	private function loadTasks() : void {
		$this->getScheduler()->scheduleRepeatingTask(new BlizzardAura($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new BulletHelix($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new ConduitHalo($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new CupidsLove($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new EmeraldTwirl($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new FlameRings($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new RainCloud($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new WitchCurse($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new BloodHelix($this), 3);

		$this->getScheduler()->scheduleRepeatingTask(new Flames($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new Snow($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new Heart($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new Smoke($this), 3);

		$this->getScheduler()->scheduleRepeatingTask(new Youtube($this), 3);
		$this->getScheduler()->scheduleRepeatingTask(new Frog($this), 3);
	}

	function getForms() : MainForm {
		return $this->forms;
	}
	function getGadgetForm() : GadgetForm {
		return $this->gadgets;
	}
	function getParticleForm() : ParticleForm {
		return $this->particles;
	}
	function getMorphForm() : MorphForm {
		return $this->morphs;
	}
	function getTrailForm() : TrailForm {
		return $this->trails;
	}
	function getSuitForm() : SuitForm {
		return $this->suits;
	}
	function getHatForm() : HatForm {
		return $this->hats;
	}

	function setSkin(): setSkin {
        return $this->setskin;
	}
	function saveSkin(): saveSkin {
        return $this->saveskin;
	}
	function resetSkin(): resetSkin {
        return $this->resetskin;
	}

}