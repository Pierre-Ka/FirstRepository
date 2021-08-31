<?php // MagicianPersoManager.php


class PersoManager
{
	//attributs : bdd // setter construct
	private $_db ;
	public function db (PDO $db)
	{
		$this->_db= $db ;
	}
	public function __construct (PDO $db)
	{
		$this->db($db) ;
	}

	public function add(Perso $perso)
	{
		$q = $this->_db->prepare('INSERT INTO onepiece(name, type) VALUES(:name, :type)');
		$q->bindValue(':name', $perso->getName());
		$q->bindValue(':type', $perso->getType());
		$q->execute();
		$perso->hydrate([
			'id'=>$this->_db->lastInsertId(),
			'damage'=>0,
			'assets'=>4,
			'level'=>1,
			'experience'=>0,
			'power'=>1,
			'numberOfHit'=>3,
			'lastHitDate'=>NULL,
			'lastConnexionDate'=>NULL,
			'wakeUpTime'=>NULL
					]);
	}

	public function delete(Perso $perso)
	{
		$this->_db->exec('DELETE FROM onepiece WHERE id='.$perso->getId());
	}


	public function count ()
	{
		$q=$this->_db->query('SELECT COUNT(*) FROM onepiece')->fetchColumn();
		return $q;
	}
	public function selectId ()
	{
		$ids=[];
		$q=$this->_db->query('SELECT id FROM onepiece');
		while($data=$q->fetch(PDO::FETCH_ASSOC))
		{
			$ids[]=$data;
		}
		return $ids; 
	}


	public function exist($info)
	{
		if (is_int($info))
		{
			return (bool) $this->_db->query('SELECT COUNT(*) FROM onepiece WHERE id='.$info)->fetchColumn();
		}
		elseif (is_string($info))
		{
			$q = $this->_db->prepare('SELECT COUNT(*) FROM onepiece WHERE name=:name');
			$q->bindValue(':name', $info);
			$q->execute();
			return (bool) $q->fetchColumn() ;
		}
	}

	public function get($info)
	{
		if (ctype_digit($info))
		{
			$q = $this->_db->query('SELECT * FROM onepiece WHERE id='.$info);
			$data = $q->fetch(PDO::FETCH_ASSOC);	
		}
		else
		{
			$q = $this->_db->prepare('SELECT * FROM onepiece WHERE name=:name');
			$q->bindValue(':name',$info);
			$q->execute();
			$data = $q->fetch(PDO::FETCH_ASSOC);
		}
		switch ($data['type'])
		{
			case $data['type']=='Magician' :
			return new Magician($data);
			break ;
			case $data['type']=='Guerrier' :
			return new Guerrier($data);
			break ;
			case $data['type']=='Brute' :
			return new Brute($data);
			break ;
			default : return NULL ;
		}	
	}

	public function getList($nom)
	{
		$persos=[]; 
		$q = $this->_db->prepare('SELECT * FROM onepiece WHERE name <> :name ORDER BY name');
		$q->bindValue(':name', $nom);
		$q->execute(); // $q->execute(['name'=>$nom];)
		while($data=$q->fetch(PDO::FETCH_ASSOC))
		{
			switch ($data['type'])
			{
				case $data['type']=='Magician' :
				$persos[] = new Magician($data);
				break ;
				case $data['type']=='Guerrier' :
				$persos[] = new Guerrier($data);
				break ;
				case $data['type']=='Brute' :
				$persos[] = new Brute($data);
				break ;
				default : return NULL ;
			}	
		}
		return $persos ;
	}

	public function update(Perso $perso)
	{
		$q = $this->_db->prepare('UPDATE onepiece SET damage=:damage, assets=:assets, level=:level, experience=:experience, power=:power, numberOfHit=:numberOfHit, wakeUpTime=:wakeUpTime WHERE id=:id');
		$q->bindValue(':id', $perso->getId());
		$q->bindValue(':damage', $perso->getDamage());
		$q->bindValue(':assets', $perso->getAssets());
		$q->bindValue(':level', $perso->getLevel());
		$q->bindValue(':experience', $perso->getExperience());
		$q->bindValue(':power', $perso->getPower());
		$q->bindValue(':numberOfHit',$perso->getNumberOfHit());
		$q->bindValue(':wakeUpTime',date('Y/m/d H:i:s', $perso->getWakeUpTime()));
		$q->execute();
	}
	public function updateLastHit(Perso $perso)
	{
		 $this->_db->exec('UPDATE onepiece SET lastHitDate=NOW() WHERE id='.$perso->getId());
	}
	public function updateLastConnexion(Perso $perso)
	{
		 $this->_db->exec('UPDATE onepiece SET lastConnexionDate=NOW() WHERE id='.$perso->getId());
	}


}