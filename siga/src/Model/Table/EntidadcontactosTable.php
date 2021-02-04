<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Entidadcontactos Model
 *
 * @property \App\Model\Table\EntidadsTable|\Cake\ORM\Association\BelongsTo $Entidads
 * @property \App\Model\Table\ContactosTable|\Cake\ORM\Association\BelongsTo $Contactos
 *
 * @method \App\Model\Entity\Entidadcontacto get($primaryKey, $options = [])
 * @method \App\Model\Entity\Entidadcontacto newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Entidadcontacto[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Entidadcontacto|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Entidadcontacto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Entidadcontacto[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Entidadcontacto findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EntidadcontactosTable extends Table
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

        $this->setTable($esquema.'.entidadcontactos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Entidads', [
            'foreignKey' => 'entidad_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Contactos', [
            'foreignKey' => 'contacto_id',
            'joinType' => 'INNER'
        ]);
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
        $rules->add($rules->existsIn(['entidad_id'], 'Entidads'));
        $rules->add($rules->existsIn(['contacto_id'], 'Contactos'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'dbpriv';
    }
}
