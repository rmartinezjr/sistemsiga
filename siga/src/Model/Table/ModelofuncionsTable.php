<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;


/**
 * Modelofuncions Model
 *
 * @property \App\Model\Table\ModelosTable|\Cake\ORM\Association\BelongsTo $Modelos
 * @property \App\Model\Table\FuncionsTable|\Cake\ORM\Association\BelongsTo $Funcions
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 * @property \App\Model\Table\MenuitemsTable|\Cake\ORM\Association\HasMany $Menuitems
 * @property \App\Model\Table\PrivmodelosTable|\Cake\ORM\Association\HasMany $Privmodelos
 *
 * @method \App\Model\Entity\Modelofuncion get($primaryKey, $options = [])
 * @method \App\Model\Entity\Modelofuncion newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Modelofuncion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Modelofuncion|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Modelofuncion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Modelofuncion[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Modelofuncion findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ModelofuncionsTable extends Table
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
        $this->setTable($esquema.'.modelofuncions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Modelos', [
            'foreignKey' => 'modelo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Funcions', [
            'foreignKey' => 'funcion_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Menuitems', [
            'foreignKey' => 'modelofuncion_id'
        ]);
        $this->hasMany('Privmodelos', [
            'foreignKey' => 'modelofuncion_id'
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
            ->boolean('used')
            ->requirePresence('used', 'create')
            ->notEmpty('used');

        $validator
            ->integer('orden')
            ->allowEmpty('orden');

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
        $rules->add($rules->existsIn(['modelo_id'], 'Modelos'));
        $rules->add($rules->existsIn(['funcion_id'], 'Funcions'));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }
    public static function defaultConnectionName() {
        return 'dbpriv';
    }

}
