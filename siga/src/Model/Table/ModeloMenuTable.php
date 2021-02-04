<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ModeloMenu Model
 *
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 * @property \App\Model\Table\ModelofuncionsTable|\Cake\ORM\Association\BelongsTo $Modelofuncions
 *
 * @method \App\Model\Entity\ModeloMenu get($primaryKey, $options = [])
 * @method \App\Model\Entity\ModeloMenu newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ModeloMenu[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ModeloMenu|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ModeloMenu patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ModeloMenu[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ModeloMenu findOrCreate($search, callable $callback = null, $options = [])
 */
class ModeloMenuTable extends Table
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

        $this->setTable('modelo_menu');

        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Modelofuncions', [
            'foreignKey' => 'modelofuncion_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->requirePresence('id', 'create')
            ->notEmpty('id');

        $validator
            ->scalar('modelo')
            ->requirePresence('modelo', 'create')
            ->notEmpty('modelo');

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
            ->boolean('trash')
            ->requirePresence('trash', 'create')
            ->notEmpty('trash');

        $validator
            ->scalar('estado')
            ->requirePresence('estado', 'create')
            ->notEmpty('estado');

        $validator
            ->integer('id_menuitems')
            ->requirePresence('id_menuitems', 'create')
            ->notEmpty('id_menuitems');

        $validator
            ->scalar('alias_menuitems')
            ->requirePresence('alias_menuitems', 'create')
            ->notEmpty('alias_menuitems');

        $validator
            ->integer('id_submenu')
            ->requirePresence('id_submenu', 'create')
            ->notEmpty('id_submenu');

        $validator
            ->scalar('alias_submenu')
            ->requirePresence('alias_submenu', 'create')
            ->notEmpty('alias_submenu');

        $validator
            ->integer('id_menu')
            ->requirePresence('id_menu', 'create')
            ->notEmpty('id_menu');

        $validator
            ->scalar('alias_menu')
            ->requirePresence('alias_menu', 'create')
            ->notEmpty('alias_menu');

        return $validator;
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
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));
        $rules->add($rules->existsIn(['modelofuncion_id'], 'Modelofuncions'));

        return $rules;
    }
    public static function defaultConnectionName() {
        return 'dbpriv';
    }
}
