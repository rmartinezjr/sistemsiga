<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Users Model
 *
 * @property |\Cake\ORM\Association\BelongsTo $Perfils
 * @property |\Cake\ORM\Association\BelongsTo $Contactos
 * @property |\Cake\ORM\Association\BelongsTo $Cestados
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        //$this->getTable($this->getConnection()->config()['database'] . "." . $this->getTable());
        $esquema = ConnectionManager::get('dbpriv')->config()["database"];
        $this->setTable($esquema.'.users');

        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Perfils', [
            'foreignKey' => 'perfil_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Contactos', [
            'foreignKey' => 'contacto_id',
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
    /*   public function validationDefault(Validator $validator)
       {
           $validator
               ->integer('id')
               ->allowEmpty('id', 'create');

           $validator
               ->scalar('username')
               ->requirePresence('username', 'create')
               ->notEmpty('username')
               ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

           $validator
               ->scalar('password')
               ->requirePresence('password', 'create')
               ->notEmpty('password');

           $validator
               ->scalar('numrandom')
               ->allowEmpty('numrandom');

           $validator
               ->email('email')
               ->requirePresence('email', 'create')
               ->notEmpty('email')
               ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

           $validator
               ->scalar('usuario')
               ->requirePresence('usuario', 'create')
               ->notEmpty('usuario');

           $validator
               ->scalar('usuariomodif')
               ->allowEmpty('usuariomodif');

           $validator
               ->dateTime('lastreset')
               ->allowEmpty('lastreset');

           $validator
               ->dateTime('lastlogin')
               ->allowEmpty('lastlogin');

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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['perfil_id'], 'Perfils'));
        $rules->add($rules->existsIn(['contacto_id'], 'Contactos'));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'dbpriv';
    }
}
