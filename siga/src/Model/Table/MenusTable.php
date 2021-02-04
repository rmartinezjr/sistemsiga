<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Menus Model
 *
 * @property \App\Model\Table\MenusTable|\Cake\ORM\Association\BelongsTo $Menus
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 * @property \App\Model\Table\MenuitemsTable|\Cake\ORM\Association\HasMany $Menuitems
 * @property \App\Model\Table\MenusTable|\Cake\ORM\Association\HasMany
 *
 * @method \App\Model\Entity\Menu get($primaryKey, $options = [])
 * @method \App\Model\Entity\Menu newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Menu[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Menu|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Menu patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Menu[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Menu findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MenusTable extends Table
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

        $this->setTable($esquema.'.menus');
        $this->setDisplayField('alias');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Menus', [
            'foreignKey' => 'menu_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Menuitems', [
            'foreignKey' => 'menu_id'
        ]);
        $this->hasMany('Menus', [
            'foreignKey' => 'menu_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
 /*   public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('nombre')
            ->requirePresence('nombre', 'create')
            ->notEmpty('nombre')
            ->add('nombre', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('alias')
            ->requirePresence('alias', 'create')
            ->notEmpty('alias');

        $validator
            ->requirePresence('icon', 'create')
            ->notEmpty('icon');

        $validator
            ->allowEmpty('icon2');

        $validator
            ->scalar('filetype')
            ->allowEmpty('filetype');

        $validator
            ->scalar('filename')
            ->allowEmpty('filename');

        $validator
            ->scalar('descripcion')
            ->allowEmpty('descripcion');

        $validator
            ->scalar('usuario')
            ->requirePresence('usuario', 'create')
            ->notEmpty('usuario');

        $validator
            ->scalar('usuariomodif')
            ->allowEmpty('usuariomodif');

        $validator
            ->boolean('trash')
            ->allowEmpty('trash');

        $validator
            ->integer('posicion')
            ->allowEmpty('posicion');

        $validator
            ->integer('orden')
            ->allowEmpty('orden');

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
        $rules->add($rules->isUnique(['nombre']));
        $rules->add($rules->existsIn(['menu_id'], 'Menus'));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }
    public static function defaultConnectionName() {
        return 'dbpriv';
    }
}
