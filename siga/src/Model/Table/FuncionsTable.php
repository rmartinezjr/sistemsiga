<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Funcions Model
 *
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 * @property \App\Model\Table\ModelofuncionsTable|\Cake\ORM\Association\HasMany $Modelofuncions
 *
 * @method \App\Model\Entity\Funcion get($primaryKey, $options = [])
 * @method \App\Model\Entity\Funcion newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Funcion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Funcion|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Funcion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Funcion[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Funcion findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FuncionsTable extends Table
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

        $this->setTable('funcions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
            'joinType' => 'INNER'
        ]);
   /*     $this->hasMany('Modelofuncions', [
            'foreignKey' => 'funcion_id'
        ]);*/

        $this->belongsToMany ( 'Modelos' ,  [
            'joinTable'  =>  'funcion_id',
            'through'  =>  'Modelofuncions' ,
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
        $rules->add($rules->isUnique(['funcion']));
        $rules->add($rules->isUnique(['alias']));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }
    public static function defaultConnectionName() {
        return 'dbpriv';
    }
}
