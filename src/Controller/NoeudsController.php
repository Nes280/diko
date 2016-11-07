<?php
namespace App\Controller;
use Cake\Cache\Cache;

class NoeudsController extends AppController
{
    public $paginate = [
        'limit' => 30
        
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    } 

	public function index()
    {
        $noeuds = $this->Noeuds->find('all', ['limit' => 50, 'order' => ['poids' => 'desc']])->where(function ($exp, $q) {
					return $exp->notLike('mot', '%\_%');
				});
        $this->set('noeuds', $noeuds);
    }
    
    public function view($id = null)
    {

        $noeud = $this->Noeuds->get($id);
        $this->set(compact('noeud'));
		//echo $this->request->session()->read('User.r_associated');
        $this->set('r_associated',$this->request->session()->read('User.r_associated'));
		
		$val = '0'; 
		//Polarité du mot négative
		$optionsNeg = array(
            'fields' => array(
                'Aretes.poids'
            ),
            'conditions' => array(
                'Aretes.mot1' => $id, 
				'Aretes.mot2' => '254878'
            )
        );
		$negatif = $this->Noeuds->Aretes->find('all', $optionsNeg);
		$this->set('negatif', $val);
		foreach ($negatif as $neg) {
			$this->set('negatif', $neg->poids);
		}
		
		//Polarité du mot neutre
		$optionsNeu = array(
            'fields' => array(
                'Aretes.poids'
            ),
            'conditions' => array(
                'Aretes.mot1' => $id, 
				'Aretes.mot2' => '254877'
            )
        );
		$neutre = $this->Noeuds->Aretes->find('all', $optionsNeu);
		$this->set('neutre', $val);
		foreach ($neutre as $neu) {
			$this->set('neutre', $neu->poids);
		}
		
		//Polarité du mot positive
		$optionsPos = array(
            'fields' => array(
                'Aretes.poids'
            ),
            'conditions' => array(
                'Aretes.mot1' => $id, 
				'Aretes.mot2' => '254876'
            )
        );
		$positif = $this->Noeuds->Aretes->find('all', $optionsPos);
		$this->set('positif', $val);
		foreach ($positif as $pos) {
			$this->set('positif', $pos->poids);
		}
		
		
        $query = $this->Noeuds->findById($id)->contain(['Definitions']);
        foreach ($query as $def) {
            $this->set('def', $def);
        }
        
        $options = array(
            'fields' => array(
                'Aretes.mot2'
            ),
            'conditions' => array(
                'Aretes.mot1' => $id
            ),
            'order' =>array(
                 'Aretes.poids' => 'desc'   
            )
        );

        $identifiant = $this->Noeuds->Aretes->find('all', $options);
        $compteur = 0;
        foreach ($identifiant as $i) {
            //$this->set('identifiant', $i);
            $tab[$compteur] = $i->mot2;
            $compteur++;
        }
         
        $options2 = array(
            'fields' => array(
                'Noeuds.mot',
                'Noeuds.id'
            ),
            'conditions' => array(
                'Noeuds.id IN' => $tab
            ),
        );
        //$data = $this->Paginator->paginate($options2, $paginate);
        $data = $this->Noeuds->find('all', $options2);
        $compteur = 0;
        //$table="[";
        foreach ($data as $d) {
            if ($compteur>0 AND substr($d->mot, 0, 1) != "_" AND substr($d->mot, 0, 1) != ":") {
                //$table.= ",";
            }
            if (substr($d->mot, 0, 1) != "_" AND substr($d->mot, 0, 1) != ":") {
                $tab2[$compteur]= $d;
                str_replace("\'", "'", $d->mot);
                //$table.= "<a href=\"/diko/noeuds/view/$d->id\">".$d->mot."</a>";
                $compteur++;
            } 
        }
        if(($n = Cache::read('cache_'.$id)) !== false)
        {
            $this->set('data',$n);
        }
        else
        {
            $donnee = $this->paginate($data->cache('cache_'.$id));
            $this->set('data',$donnee);
        }

    }

}

?>