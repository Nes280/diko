<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class RelationsTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
		$this->hasMany('Aretes', [
            'className' => 'Aretes', 
			['foreignKey' => ['rel', ],
			 'bindingKey' => [ 'id', ]]
        ]);
    }
}
?>