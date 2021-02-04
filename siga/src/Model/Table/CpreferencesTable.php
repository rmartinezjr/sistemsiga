<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Cpreferences Model
 *
 * @method \App\Model\Entity\Cpreference get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cpreference newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cpreference[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cpreference|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cpreference patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cpreference[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cpreference findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CpreferencesTable extends Table
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

        $this->setTable('cpreferences');
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

    public static function defaultConnectionName() {
        return 'dbtransac';
    }
}
