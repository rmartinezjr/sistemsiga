<?php
namespace App\Model\Table;

use  Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Cestados Model
 *
 * @property \App\Model\Table\CcontactotiposTable|\Cake\ORM\Association\HasMany $Ccontactotipos
 * @property \App\Model\Table\CdocidtiposTable|\Cake\ORM\Association\HasMany $Cdocidtipos
 * @property \App\Model\Table\CentidadrolsTable|\Cake\ORM\Association\HasMany $Centidadrols
 * @property \App\Model\Table\CentidadtiposTable|\Cake\ORM\Association\HasMany $Centidadtipos
 * @property \App\Model\Table\CformtiposTable|\Cake\ORM\Association\HasMany $Cformtipos
 * @property \App\Model\Table\CindicadorambitosTable|\Cake\ORM\Association\HasMany $Cindicadorambitos
 * @property \App\Model\Table\CindicadortiposTable|\Cake\ORM\Association\HasMany $Cindicadortipos
 * @property \App\Model\Table\CobjetoplanifsTable|\Cake\ORM\Association\HasMany $Cobjetoplanifs
 * @property \App\Model\Table\CtipodatosTable|\Cake\ORM\Association\HasMany $Ctipodatos
 * @property \App\Model\Table\CunidadsTable|\Cake\ORM\Association\HasMany $Cunidads
 *
 * @method \App\Model\Entity\Cestado get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cestado newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cestado[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cestado|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cestado patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cestado[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cestado findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CestadosTable extends Table
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

        $this->setTable($esquema.'.cestados');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Ccontactotipos', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Cdocidtipos', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Centidadrols', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Centidadtipos', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Cformtipos', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Cindicadorambitos', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Cindicadortipos', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Cobjetoplanifs', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Ctipodatos', [
            'foreignKey' => 'cestado_id'
        ]);
        $this->hasMany('Cunidads', [
            'foreignKey' => 'cestado_id'
        ]);
    }
    public static function defaultConnectionName() {
        return 'dbtransac';
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */

}
