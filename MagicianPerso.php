<?php // MagicianPerso.php


abstract class Perso 
{
	// attributs
	protected  $_id,
		    $_name,
  		    $_type,
		    $_damage,
		    $_assets,
		    $_level,
			$_experience,
			$_power,
			$_numberOfHit,
			$_lastHitDate,
			$_lastConnexionDate,
			$_wakeUpTime ;
	public const ITS_ME = 1 ;
	public const CHARACTER_HIT = 2 ;
	public const CHARACTER_DIE = 3 ;
	public const CANNOT_HIT = 4 ;
	public const CHARACTER_BEWITCHED = 5 ;
	// HYDRATATION
	public function hydrate (array $data)
	{
		foreach($data as $key => $value)
    	{
      		$method = 'set'.ucfirst($key);
      		if (method_exists($this, $method))
      		{
        		$this->$method($value);
      		}
    	}
	}
	// __construct
	public function __construct(array $data)
	{
		$this->hydrate($data) ;
		$this->_type = strtolower(static::class) ;
	}

	// GETTERS 


	public function getId ()
	{
		return $this->_id ;
	}
	public function getName ()
	{
		return $this->_name ;
	}
	public function getType ()
	{
		return $this->_type ;
	}
	public function getDamage ()
	{
		return $this->_damage ;
	}
	public function getAssets ()
	{
		return $this->_assets ;
	}
	public function getLevel ()
	{
		return $this->_level ;
	}
	public function getExperience ()
	{
		return $this->_experience ;
	}
	public function getPower ()
	{
		return $this->_power ;
	}
	public function getNumberOfHit()
	{
		return $this->_numberOfHit ;
	}
	public function getLastHitDate()
	{
		return $this->_lastHitDate ;
	}
	public function getLastConnexionDate()
	{
		return $this->_lastConnexionDate ;
	}
	public function getWakeUpTime ()
	{
		return $this->_wakeUpTime;
	}

	// SETTERS

	public function setId ($newid)
	{
		if(ctype_digit($newid))
		{
			$this->_id = $newid ;
		}
	}
	public function setName ($newname)
	{
		if(is_string($newname))
		{
			$this->_name = $newname ;
		}
	}
	// PAS DE SETTER POUR TYPE
	public function setDamage ($newdamage)
	{
		$newdamage = (int)$newdamage;
		if($newdamage>=0 && $newdamage<=100)
		{
			$this->_damage = $newdamage ;
		}
	}
	public function setAssets ($newassets)
	{
		$newassets = (int)$newassets;
		if($newassets>=0 && $newassets<=4)
		{
			$this->_assets = $newassets ;
		}

	}
	public function setLevel ($newlevel)
	{
		$newlevel = (int)$newlevel;
		if($newlevel>=1 && $newlevel<=5)
		{
			$this->_level = $newlevel ;
		}
	}
	public function setExperience ($newexperience)
	{
		$newexperience = (int)$newexperience;
		if($newexperience>=0 && $newexperience<=100)
		{
			$this->_experience = $newexperience;
		}
	}
	public function setPower ($newpower)
	{
		$newpower = (int)$newpower;
		if($newpower>=1 && $newpower<=15)
		{
			$this->_power = $newpower ;
		}
	}
	public function setNumberOfHit($newnumberofhit)
	{
		$newnumberofhit = (int)$newnumberofhit;
		if($newnumberofhit>=0 && $newnumberofhit<=3)
		{
			$this->_numberOfHit = $newnumberofhit ;
		}
				
	}
	public function setLastHitDate($newlasthitdate)
	{
		$newlasthitdate=strtotime($newlasthitdate);
		if ($newlasthitdate = (int)$newlasthitdate)
		{
			$this->_lastHitDate = $newlasthitdate ;
		}
	}
	public function setLastConnexionDate($newlastconnexiondate)
	{
		$newlastconnexiondate=strtotime($newlastconnexiondate);
		if($newlastconnexiondate = (int)$newlastconnexiondate)
		{
			$this->_lastConnexionDate = $newlastconnexiondate ; 
		}
	}
	public function setWakeUpTime($newwakeuptime)
	{
		$newwakeuptime=strtotime($newwakeuptime);
		if($newwakeuptime = (int)$newwakeuptime)
		{
			$this->_wakeUpTime = $newwakeuptime ; 
		}
	}

	// METHODES DE CLASSE


	public function upExperience ()
	{
		if ($this->_experience<100)
		{
			$this->_experience+=10;
		}
		else
		{
			$this->_experience=0;
			if ($this->_level<5)
			{$this->_level++;}
			$this->upPower();
		}
	}
	public function DefineAssets ()
	{
		if ($this->_damage>=0 AND $this->_damage<=25)
		{
			$this->_assets = 4 ;
		}
		elseif ($this->_damage>25 AND $this->_damage<=50)
		{
			$this->_assets = 3 ;
		}
		elseif ($this->_damage>=50 AND $this->_damage<=70)
		{
			$this->_assets = 2 ;
		}
		elseif ($this->_damage>70 AND $this->_damage<=90)
		{
			$this->_assets = 1 ;
		}
		else
		{	$this->_assets = 0 ; }
	}
	public function upPower ()
	{
		$this->_power=rand(1,3)*$this->_level ;
	}
	public function hit (Perso $persofrapper)
	{
		if ($this->getId()==$persofrapper->getid())
		{
			return self::ITS_ME ;

		}
		elseif ($this->getNumberOfHit()==0)
		{
			return self::CANNOT_HIT ;
		}
		else
		{
			$this->_numberOfHit--;
			$this->upExperience();
			return $persofrapper->giveDamage($this->_power);
		}
	}
	public function giveDamage ($power)
	{
		$this->_damage += 3*$power;
		if ($this->getDamage() >= 100)
		{
			return self::CHARACTER_DIE;
		}
		else
		{
			return self::CHARACTER_HIT;
		}

	}
	/*public function upWakeUpTime ($secondes)
	{
		$maintenant = (time()+(2*60*60))+$secondes;
		$this->_wakeUpTime=$maintenant ;
		return self::CHARACTER_BEWITCHED ;
	}*/
	public function isBewitched ()
	{
		return (bool) ($this->getWakeUpTime()>time()+(2*60*60)) ;
	}
    public function dateDiff($date1, $date2)
    {
    	$diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
    	$retour = array();
 
    	$tmp = $diff;
    	$retour['secondes'] = $tmp % 60;
 
    	$tmp = floor( ($tmp - $retour['secondes']) /60 );
    	$retour['minutes'] = $tmp % 60;
 
    	$tmp = floor( ($tmp - $retour['minutes'])/60 );
    	$retour['heures'] = $tmp % 24;
 
    	$tmp = floor( ($tmp - $retour['heures'])  /24 );
    	$retour['jour'] = $tmp;
 
    	return $retour;
	}
}

class Magician extends Perso
{
	public const NO_MAGIE = 4 ;

	public function bewitch(Perso $persoensorcele)
	{
		if ($this->getId()==$persoensorcele->getId())
		{
			return parent::ITS_ME ;
		}
		else
		{
			if ($this->getAssets()==0)
			{
				return self::NO_MAGIE ;
			}
			else
			{
				$secondes = $this->getAssets()*(5*60) ; // Endort pour 5minxatout
				// $secondes = $this->getAssets()*(6*60*60) ;
				// return $persoensorcele->upWakeUpTime($secondes);
				$reveil = (time()+(2*60*60))+$secondes;
				$persoensorcele->_wakeUpTime=$reveil ;				
				return parent::CHARACTER_BEWITCHED;
			}

		}
	}

}
class Guerrier extends Perso
{
	public function giveDamage ($power)
	{
		$atouts=$this->getAssets();
		if ($power>$atouts)
		{
			$this->_damage += round(3*($power/(rand($atouts,$power)))) ;
			if ($this->getDamage() >= 100)
			{
				return parent::CHARACTER_DIE;
			}
			else
			{
				return parent::CHARACTER_HIT;
			}
		}
		else
		{
			$this->_damage +=((3*$power)-1);	
			if ($this->getDamage() >= 100)
			{
				return parent::CHARACTER_DIE;
			}
			else
			{
				return parent::CHARACTER_HIT;
			}
		}
	}
}
class Brute extends Perso
{
	public function hit (Perso $persofrapper)
	{
		if ($this->getId()==$persofrapper->getid())
		{
			return self::ITS_ME ;

		}
		elseif ($this->getNumberOfHit()==0)
		{
			return self::CANNOT_HIT ;
		}
		else
		{
			$this->_numberOfHit--;
			$this->upExperience();
			$brutalite=[1,1,1,4];
			shuffle($brutalite);
			$brute=$this->_power*$brutalite[0];
			return $persofrapper->giveDamage($brute);
		}
	}

}
