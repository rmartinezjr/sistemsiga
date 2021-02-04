<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * CorreoPlantillas Model
 *
 * @method \App\Model\Entity\CorreoPlantilla get($primaryKey, $options = [])
 * @method \App\Model\Entity\CorreoPlantilla newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CorreoPlantilla[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CorreoPlantilla|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CorreoPlantilla patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CorreoPlantilla[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CorreoPlantilla findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CorreoPlantillasTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $esquema = ConnectionManager::get('dbpriv')->config()["database"];

        $this->setTable($esquema.'.correo_plantillas');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['nombre']));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'dbpriv';
    }
}
