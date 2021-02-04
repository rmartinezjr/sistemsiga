<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Menuitems Model
 *
 * @property \App\Model\Table\MenusTable|\Cake\ORM\Association\BelongsTo $Menus
 * @property \App\Model\Table\ModelofuncionsTable|\Cake\ORM\Association\BelongsTo $Modelofuncions
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 *
 * @method \App\Model\Entity\Menuitem get($primaryKey, $options = [])
 * @method \App\Model\Entity\Menuitem newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Menuitem[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Menuitem|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Menuitem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Menuitem[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Menuitem findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MenuitemsTable extends Table
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

        $this->setTable($esquema.'.menuitems');
        $this->setDisplayField('alias');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Menus', [
            'foreignKey' => 'menu_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Modelofuncions', [
            'foreignKey' => 'modelofuncion_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
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
        $rules->add($rules->existsIn(['menu_id'], 'Menus'));
        $rules->add($rules->existsIn(['modelofuncion_id'], 'Modelofuncions'));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }
    public static function defaultConnectionName() {
        return 'dbpriv';
    }
}
