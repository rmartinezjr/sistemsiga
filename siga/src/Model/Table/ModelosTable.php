<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Modelos Model
 *
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 * @property \App\Model\Table\ModelofuncionsTable|\Cake\ORM\Association\HasMany $Modelofuncions
 *
 * @method \App\Model\Entity\Modelo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Modelo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Modelo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Modelo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Modelo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Modelo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Modelo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ModelosTable extends Table
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
        $this->setTable($esquema.'.modelos');

        $this->setDisplayField('alias');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
            'joinType' => 'INNER'
        ]);
      $this->hasMany('Modelofuncions', [
            'foreignKey' => 'modelo_id'
        ]);



    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
   /* public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('modelo')
            ->requirePresence('modelo', 'create')
            ->notEmpty('modelo')
            ->add('modelo', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('alias')
            ->requirePresence('alias', 'create')
            ->notEmpty('alias');

        $validator
            ->scalar('descripcion')
            ->allowEmpty('descripcion');

        $validator
            ->boolean('movil')
            ->requirePresence('movil', 'create')
            ->notEmpty('movil');

        $validator
            ->scalar('usuario')
            ->requirePresence('usuario', 'create')
            ->notEmpty('usuario');

        $validator
            ->scalar('usuariomodif')
            ->allowEmpty('usuariomodif');

        $validator
            ->boolean('trash')
            ->requirePresence('trash', 'create')
            ->notEmpty('trash');

        return $validator;
    }
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
        $rules->add($rules->isUnique(['modelo']));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }
    public static function defaultConnectionName() {
        return 'dbpriv';
    }
}
