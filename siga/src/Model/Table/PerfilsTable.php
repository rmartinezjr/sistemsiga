<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Perfils Model
 *
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 * @property \App\Model\Table\PrivmodelosTable|\Cake\ORM\Association\HasMany $Privmodelos
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Perfil get($primaryKey, $options = [])
 * @method \App\Model\Entity\Perfil newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Perfil[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Perfil|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Perfil patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Perfil[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Perfil findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PerfilsTable extends Table
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
        $this->setTable($esquema.'.perfils');

        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Privmodelos', [
            'foreignKey' => 'perfil_id'
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'perfil_id'
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
        $rules->add($rules->isUnique(['nombre']));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'dbpriv';
    }
}
