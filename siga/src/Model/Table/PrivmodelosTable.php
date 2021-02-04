<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Privmodelos Model
 *
 * @property \App\Model\Table\ModelofuncionsTable|\Cake\ORM\Association\BelongsTo $Modelofuncions
 * @property \App\Model\Table\PerfilsTable|\Cake\ORM\Association\BelongsTo $Perfils
 *
 * @method \App\Model\Entity\Privmodelo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Privmodelo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Privmodelo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Privmodelo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Privmodelo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Privmodelo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Privmodelo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PrivmodelosTable extends Table
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

        $this->setTable('privmodelos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Modelofuncions', [
            'foreignKey' => 'modelofuncion_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Perfils', [
            'foreignKey' => 'perfil_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */


    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['modelofuncion_id'], 'Modelofuncions'));
        $rules->add($rules->existsIn(['perfil_id'], 'Perfils'));

        return $rules;
    }
    public static function defaultConnectionName()
    {
        return 'dbpriv';
    }
}
