<?php // MagicianPersoIndex.php

/* FUTUR AMELIORATION petite modif
QUE SE PASSE T IL SI NIVEAU 5 EXPERIENCE 100
BAISSER L'EXPERIENCE A 99 
LA POSSIBILITE DE SE DESENSORCELER SI ON EST UN MAGICIEN
UN SEUL ENSORCELEMENT - PAR UN CERTAIN LAPS DE TEMPS
LE GUERRIER NE PEUT PAS SE DEFENDRE SI IL EST ENSORCELER
TUER UN PERSONNAGE FAIT REVENIR A ZERO DEGATS
REMPLACEMENT PAR VOUS AVEZ 0 COUPS A DONNER PAR VOUS N'AVEZ PLUS DE COUPS
*/ 
/* FUTUR AMELIORATION grosse modif
SYSTEME DE CONNEXION : CREATION DU NAMETEAM + PASSWORD
/ CREATION D'UNE AUTRE TABLE : ID/TEAM NAMETEAM PASSWORD 
PUIS JONCTION DES TABLES EN CREANT UNE COLONNE TEAM DANS NOTRE TABLE
CREATION DE 4 PERSONNAGES PAR TEAM MAXIMUM / SI ON A CREER NOS 4 PERSONNAGE LE BOUTON CREATE DISPARAIT
ON NE PEUT UTILISER QUE NOS PERSONNAGES OU SE CONNECTER ALEATOIREMENT A EUX
EN + : SOIT ON CHOISIT DE SE CONNECTER ALEATOIREMENT ET ON DOIT CHOISIR QUI FRAPPER ( bouton disparait),
	   SOIT ON CHOISIT A QUI SE CONNECTER ET ON DOIT FRAPPER ALEATOIREMENT ( liste disparait)
*/
	   /* FUTUR AMELIORATION Alix
3 nouveaux type de perso : Espion ( type de personnage transparent : il ne peut que voir les dégats lorsque les autres types de perso ne peuvent pas les voir / ) Medecin ( il peut soigner , mais ne peut pas se soigner lui-même ), Pirates ( il peut donner plus de coups ou voler un attributs)
*/

require_once('MagicianPerso.php');
require_once('MagicianPersoManager.php');
$db = new PDO('mysql:host=localhost;dbname=zoo;charset=utf8', 'root', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$manager= new PersoManager($db) ;
session_start();

if (isset($_SESSION['perso'])) // Si la session perso existe, on restaure l'objet.
{
  $perso = $_SESSION['perso'];
}

if (isset($_GET['Deconnexion']))
{
	session_destroy();
	unset($perso);
	header('Location: .');
	exit();
}


if (isset($_POST['namecreate']) AND isset($_POST['create']) AND ($_POST['namecreate'])!=NULL AND isset($_POST['type']))
{
		switch ($_POST['type'])
		{
			case $_POST['type']=='Magician' :
			$perso = new Magician ([
			'name'=> $_POST['namecreate'],
				]) ;
			break ;
			case $_POST['type']=='Guerrier' :
			$perso = new Guerrier ([
			'name'=> $_POST['namecreate'],
				]) ;
			break ;
			case $_POST['type']=='Brute' :
			$perso = new Brute ([
			'name'=> $_POST['namecreate'],
				]) ;
			break ;
		}	
		if ($manager->exist($perso->getName()))
			{
				echo 'Le nom est deja utilisé, veuillez en choisir un autre';
				unset($perso);
			}
		else
			{
				$manager->add($perso);
				$manager->updateLastConnexion($perso);
			}
} 
elseif (isset($_POST['name']) AND isset($_POST['use']) AND isset($_POST['name'])!=NULL )
{
		if ($manager->exist($_POST['name']))
			{
				$perso=$manager->get($_POST['name']);
				$lastconnect = $perso->getLastConnexionDate()+(24*60*60); // retourne -10 si il y a 24H
				//$lastconnect = $perso->getLastConnexionDate()+(24*60*60);
				if ($lastconnect<(time()+2*60*60))
				{
					$newdamage=$perso->getDamage()-10;
					$message= 'Votre personnage a recupéré : dégats :-10 ! ';
					$perso->setDamage($newdamage);
				}	
				$perso->defineAssets();
				$manager->updateLastConnexion($perso);
				$lasthit = $perso->getLastHitDate()+(2*60); // 3 coups toutes les 2min
				//$lasthit = $perso->getLastHitDate()+(24*60*60);
				if ($lasthit<(time()+2*60*60))
				{
					$perso->setNumberOfHit(3);
				}
				$manager->update($perso);
			}
		else
			{
				echo ' Le personnage n\'existe pas ! ';
			}

}
elseif (isset($_POST['random'])) 
{
	$ids=$manager->selectId();
	shuffle($ids);
	$mysteryid=$ids[0];
	$perso=$manager->get($mysteryid['id']);
				$lastconnect = $perso->getLastConnexionDate()+(24*60*60); // retourne -10 si il y a 24H
				//$lastconnect = $perso->getLastConnexionDate()+(24*60*60);
				if ($lastconnect<(time()+2*60*60))
				{
					$newdamage=$perso->getDamage()-10;
					$message= 'Votre personnage a recupéré : dégats :-10 ! ';
					$perso->setDamage($newdamage);
				}	
				$perso->defineAssets();
				$manager->updateLastConnexion($perso);
				$lasthit = $perso->getLastHitDate()+(2*60); // 3 coups toutes les 2min
				//$lasthit = $perso->getLastHitDate()+(24*60*60);
				if ($lasthit<(time()+2*60*60))
				{
					$perso->setNumberOfHit(3);
				}
				$manager->update($perso);
}
elseif (isset($_GET['ensorcele'])) 
{
	 if (!isset($perso))
  {
    $message = 'Merci de créer un personnage ou de vous identifier.';
  }
  
  else
  {
		if (!$manager->exist((int) $_GET['ensorcele']))
    	{
      		$message = 'Le personnage que vous voulez ensorcelé n\'existe pas !';
    	}
    	elseif ($perso->isBewitched()) 
    	{
    		$message = 'Vous êtes endormi ; vous ne pouvez pas frapper ou ensorceler !';
    	}
    	else
		{
			$victime=$manager->get($_GET['ensorcele']);
			$retour=$perso->bewitch($victime);
			switch ($retour)
     		{
     			case Perso::ITS_ME :
     			$message = 'Mais... Pourquoi vous ensorcelé vous-même ???';
     			break ;
     			case Magician::NO_MAGIE :
     			$message = 'Votre atout est à 0 ! Vous ne pouvez plus ensorcelé' ;
     			break ;
     			case Perso::CHARACTER_BEWITCHED :
     			$message = 'Le personnage a bien été ensorcelé' ;
     			$manager->update($victime);
     			echo $victime->getWakeUpTime();
     			break ;
     		}
     	}
     }
}
elseif (isset($_GET['frapper'])) 
{
	if (!isset($perso))
  	{
    	$message = 'Merci de créer un personnage ou de vous identifier.';
  	}
    elseif ($perso->isBewitched()) 
    {
    	$message = 'Vous êtes endormi ; vous ne pouvez pas frapper ou ensorceler !';
    }
  
  	else
  	{
		if (!$manager->exist((int) $_GET['frapper']))
    	{
      		$message = 'Le personnage que vous voulez frapper n\'existe pas !';
    	}
		else
		{	

			$victime=$manager->get( $_GET['frapper']);
			$retour=$perso->hit($victime);
			switch ($retour)
     		{
        		case Perso::ITS_ME :
          		$message = 'Mais... pourquoi voulez-vous vous frapper ???';
          		break;

          		case Perso::CANNOT_HIT :
          		$futurehit=$perso->getLastHitDate()+(15*60); // Coup futur dans 15min
          		//$futurehit=$perso->getLastHitDate()+(24*60*60);
        		$message = 'Vous n\'avez plus de coups. Votre dernier coup à été porté le '.date('d/m/Y à H:i:s',$perso->getLastHitDate()).' vous pourrez refrapper le '.date('d/m/Y à H:i:s',$futurehit);
        		
          		break;
        
        		case Perso::CHARACTER_HIT :
          		$message = 'Le personnage '.$victime->getName().' a bien été frappé !';
          		$manager->update($perso);
          		$manager->updateLastHit($perso);
          		$victime->defineAssets();
          		$manager->update($victime);
          		break;
        
        		case Perso::CHARACTER_DIE :
          		$message = 'Vous avez tué ce personnage !';
          		$manager->update($perso);
          		$manager->updateLastHit($perso);
          		$manager->delete($victime);
          		break;
      		}
		}
	}
}    
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title> Yo	</title>
		<style >
			p { 
				font-size:1.1em ; 
				color:red; 
				}
			legend {
				font-family: Arial ;
				font-size: 1.1em ;
			}
		</style>
	</head>
<body>

<h1> TP : MINI JEU DE COMBAT </h1>
<p> Nombre de personnage : <?= $manager->count(); ?> </p>

<?php if(isset($message))
{echo $message;}

if (isset($perso))
{
		?>
		<p><a href="?Deconnexion">Deconnexion</a> </p>

		<fieldset>
			<legend>Qui suis-je?</legend>
		<p> 
			Je suis un <?= ucfirst($perso->getType()); ?> <br/>
			Nom : <strong><?= htmlspecialchars($perso->getName());?></strong> <br/>
		 	Degats : <?= $perso->getDamage();?> <br/>
		 	Atouts : <?= $perso->getAssets();?> <br/>
		 	Niveau : <?= $perso->getLevel();?> <br/>
		 	Experience : <?= $perso->getExperience();?> <br/>
		 	Force : <?= $perso->getPower();?> <br/>	
		 </p>
		</fieldset>
        
        <?php
        if ($perso->isBewitched())
        {
        	$wakeup = $perso->getWakeUpTime();
        	$now=time()+2*60*60;
        	$dateDiff=$perso->dateDiff($wakeup,$now);
        	echo ' <br/> Vous êtes ensorcelé ! Le sortilege vous a endormi.... Vous vous reveillerez le '.date('d/m/Y à H:i:s',$wakeup).' c\est à dire dans : '; 
        	$dateDiffsens = array_reverse($dateDiff) ;
        	foreach ($dateDiffsens as $key => $values){echo $values.' '.ucfirst($key).' -  ' ; } ;
        } 
        else
        {?>
        	<p> Vous avez <?= $perso->getNumberOfHit();?> coups à donner <br/>
		
			<?php   	$idsvictime=$manager->selectId();
						shuffle($idsvictime);
						$victimeid=$idsvictime[0];
				echo '<a href="?frapper='.$victimeid['id'].'">Frapper au hasard </a>' ?>
			</p>
	
			<p> 	
				<?php /*		
					$date1=$perso->getLastConnexionDate();
	        		$date2=$perso->getLastHitDate(); 
	        		$now=time()+2*60*60; 
	        		$date1Diff=[];	$date2Diff=[];
	        		$date1Diff=$perso->dateDiff($date1+24*60*60,$now);
	        		$date2Diff=$perso->dateDiff($date2+24*60*60,$now);	?>
        			Information relatives au jeu : il est <?= date('Y/m/d H:i:s',$now);?>, vous vous etes connecté dernierement à  <?= date('Y/m/d H:i:s', $perso->getLastConnexionDate());?> et vous avez assener votre dernier coup à <?= date('Y/m/d H:i:s', $perso->getLastHitDate());?>. <br/>
        			Si vous avez utiliser vos 3 coups, vous pourrez frapper de nouveau à <?= date('Y/m/d H:i:s', $date2+24*60*60);?> c\'est à dire qu\'il vous reste exactement : <br/>
        			<?php foreach ($date2Diff as $key => $values){echo $values, $key ;}?> à attendre. Quand au bonus de 10 de dégats vous devez rester déconnecter jusqu\'au <?= date('Y/m/d H:i:s', $date1+24*60*60);?><br/> c\'est à dire pendant encore <?php foreach ($date1Diff as $key => $values){echo $values, $key ;}*/?>. A vous de jouer !'
        	</p> 
        
			<fieldset><legend>Qui je frappe?</legend>
			<?php
			$persos=$manager->getList($perso->getName());
			foreach ($persos as $victime) 
			{
				if(($victime->isBewitched()))
				{$q='oui';} 
				else { $q='non';}
			 	echo '<a href="?frapper=', $victime->getId(), '">', htmlspecialchars($victime->getName()), '</a> ==> Type : ', ucfirst($victime->getType()), ' dégâts : ', $victime->getDamage(), ', atouts : ', $victime->getAssets(), ', niveau : ', $victime->getLevel(), ', experience : ', $victime->getExperience(), ', force : ', $victime->getPower(),' , ensorcelé : ',$q, '<span class="id"> ID :', $victime->getId(), '</span>' ;

			 		if ($perso->getType()=='magician')
			 		{
			 			echo ' | <a href="?ensorcele=', $victime->getId(), '"> Lancer un sort </a>';
			 		}
				echo '<br />';
			 	
			}?>
			</fieldset>
<?php
		}
}	
else
{	
	?>
	<fieldset><legend>Créer votre personnage</legend>
	<form method="post" action="MagicianPersoIndex.php">
	<input type="text" name="namecreate"/> <select name="type"> <option value="Magician"> Magician </option><option value="Guerrier"> Guerrier </option><option value="Brute">Brute </option></select> <input type="submit" name="create" value="creer ce personnage"/> </form></fieldset> <br/><br/><br/>
	<fieldset><legend>Utiliser votre personnage</legend>
	<form method="post" action="MagicianPersoIndex.php">
	<input type="text" name="name"/><input type="submit" name="use" value="utiliser ce personnage"/> <p> ou </p> <input type="submit" name="random" value="connectez-vous aléatoirement à un personnage"/>
	</form> </fieldset> <br/><br/><br/>
<?php }?>


</body>
</html>

<?php 
	if (isset($perso))
	{
		$_SESSION['perso']=$perso;
	}
?>
