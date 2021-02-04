<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Entidads Model
 *
 * @property \App\Model\Table\CdocidtiposTable|\Cake\ORM\Association\BelongsTo $Cdocidtipos
 * @property \App\Model\Table\CentidadtiposTable|\Cake\ORM\Association\BelongsTo $Centidadtipos
 * @property \App\Model\Table\CentidadrolsTable|\Cake\ORM\Association\BelongsTo $Centidadrols
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 * @property \App\Model\Table\EntidadcontactosTable|\Cake\ORM\Association\HasMany $Entidadcontactos
 *
 * @method \App\Model\Entity\Entidad get($primaryKey, $options = [])
 * @method \App\Model\Entity\Entidad newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Entidad[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Entidad|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Entidad patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Entidad[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Entidad findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EntidadsTable extends Table
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

        $this->setTable($esquema.'.entidads');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cdocidtipos', [
            'foreignKey' => 'cdocidtipo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Centidadtipos', [
            'foreignKey' => 'centidadtipo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Centidadrols', [
            'foreignKey' => 'centidadrol_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Entidadcontactos', [
            'foreignKey' => 'entidad_id'
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
        $rules->add($rules->isUnique(['codigo']));
        $rules->add($rules->isUnique(['docid']));
        $rules->add($rules->isUnique(['nombre']));
        $rules->add($rules->existsIn(['cdocidtipo_id'], 'Cdocidtipos'));
        $rules->add($rules->existsIn(['centidadtipo_id'], 'Centidadtipos'));
        $rules->add($rules->existsIn(['centidadrol_id'], 'Centidadrols'));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'dbpriv';
    }
}
