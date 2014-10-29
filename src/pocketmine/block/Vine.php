<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class Vine extends Transparent{

	public $hasEntityCollision = true;

	public function __construct($meta = 0){
		parent::__construct(self::VINE, $meta, "Vines");
		$this->isSolid = false;
		$this->isFullBlock = false;
		$this->hardness = 1;
	}

	public function onEntityCollide(Entity $entity){
		$entity->fallDistance = 0;
	}

	protected function recalculateBoundingBox(){

		$f1 = 1;
		$f2 = 1;
		$f3 = 1;
		$f4 = 0;
		$f5 = 0;
		$f6 = 0;

		$flag = $this->meta > 0;

		if(($this->meta & 0x02) > 0){
			$f4 = max($f4, 0.0625);
			$f1 = 0;
			$f2 = 0;
			$f5 = 1;
			$f3 = 0;
			$f6 = 1;
			$flag = true;
		}

		if(($this->meta & 0x08) > 0){
			$f1 = min($f1, 0.9375);
			$f4 = 1;
			$f2 = 0;
			$f5 = 1;
			$f3 = 0;
			$f6 = 1;
			$flag = true;
		}

		if(($this->meta & 0x01) > 0){
			$f3 = min($f3, 0.9375);
			$f6 = 1;
			$f1 = 0;
			$f4 = 1;
			$f2 = 0;
			$f5 = 1;
			$flag = true;
		}

		if(!$flag and $this->getSide(1)->isSolid){
			$f2 = min($f2, 0.9375);
			$f5 = 1;
			$f1 = 0;
			$f4 = 1;
			$f3 = 0;
			$f6 = 1;
		}

		return AxisAlignedBB::getBoundingBoxFromPool(
			$this->x + $f1,
			$this->y + $f2,
			$this->z + $f3,
			$this->x + $f4,
			$this->y + $f5,
			$this->z + $f6
		);
	}


	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($target->isSolid){
			$faces = [
				0 => 0,
				1 => 0,
				2 => 1,
				3 => 4,
				4 => 8,
				5 => 2,
			];
			if(isset($faces[$face])){
				$this->meta = $faces[$face];
				$this->getLevel()->setBlock($block, $this, true, true);

				return true;
			}
		}

		return false;
	}

	public function getBreakTime(Item $item){
		if($item->isShears()){
			return 0.02;
		}elseif($item->isSword()){
			return 0.2;
		}elseif($item->isAxe()){
			switch($item->isAxe()){
				case Tool::TIER_WOODEN:
					return 0.15;
				case Tool::TIER_STONE:
					return 0.075;
				case Tool::TIER_IRON:
					return 0.05;
				case Tool::TIER_DIAMOND:
					return 0.0375;
				case Tool::TIER_GOLD:
					return 0.025;
			}
		}

		return 0.3;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			/*if($this->getSide(0)->getID() === self::AIR){ //Replace with common break method
				Server::getInstance()->api->entity->drop($this, Item::get(LADDER, 0, 1));
				$this->getLevel()->setBlock($this, new Air(), true, true, true);
				return Level::BLOCK_UPDATE_NORMAL;
			}*/
		}

		return false;
	}

	public function getDrops(Item $item){
		if($item->isShears()){
			return [
				[$this->id, 0, 1],
			];
		}else{
			return [];
		}
	}
}