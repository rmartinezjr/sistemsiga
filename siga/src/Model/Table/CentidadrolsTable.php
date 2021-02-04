<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Centidadrols Model
 *
 * @property \App\Model\Table\CestadosTable|\Cake\ORM\Association\BelongsTo $Cestados
 *
 * @method \App\Model\Entity\Centidadrol get($primaryKey, $options = [])
 * @method \App\Model\Entity\Centidadrol newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Centidadrol[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Centidadrol|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Centidadrol patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Centidadrol[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Centidadrol findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CentidadrolsTable extends Table
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
        $esquema = ConnectionManager::get('dbtransac')->config()["database"];

        $this->setTable($esquema . '.centidadrols');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cestados', [
            'foreignKey' => 'cestado_id',
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
        $rules->add($rules->isUnique(['nombre']));
        $rules->add($rules->existsIn(['cestado_id'], 'Cestados'));

        return $rules;
    }

    public static function defaultConnectionName() {
        return 'dbtransac';
    }
}
