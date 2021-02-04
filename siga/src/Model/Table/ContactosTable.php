<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Contactos Model
 *
 * @property \App\Model\Table\CdocidtiposTable|\Cake\ORM\Association\BelongsTo $Cdocidtipos
 * @property \App\Model\Table\CcontactotiposTable|\Cake\ORM\Association\BelongsTo $Ccontactotipos
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 * @property \App\Model\Table\EntidadcontactosTable|\Cake\ORM\Association\HasMany $Entidadcontactos
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Contacto get($primaryKey, $options = [])
 * @method \App\Model\Entity\Contacto newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Contacto[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Contacto|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Contacto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Contacto[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Contacto findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContactosTable extends Table
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

        $this->setTable($esquema.'.contactos');
        $this->setDisplayField('nombre_completo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cdocidtipos', [
            'foreignKey' => 'cdocidtipo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Ccontactotipos', [
            'foreignKey' => 'ccontactotipo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Entidadcontactos', [
            'foreignKey' => 'contacto_id'
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'contacto_id'
        ]);
        $this->belongsTo('Cpaises', [
            'foreignKey' => 'cpaise_id',
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
        $rules->add($rules->isUnique(['docid']));
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['cdocidtipo_id'], 'Cdocidtipos'));
        $rules->add($rules->existsIn(['ccontactotipo_id'], 'Ccontactotipos'));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'dbpriv';
    }
}
