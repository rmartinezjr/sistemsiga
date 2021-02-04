<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Contacto Entity
 *
 * @property int $id
 * @property string $nombres
 * @property string $apellidos
 * @property bool $nacional
 * @property string $docid
 * @property string $email
 * @property int $cdocidtipo_id
 * @property int $ccontactotipo_id
 * @property string $descripcion
 * @property int $cestado_id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 *
 * @property \App\Model\Entity\Cdocidtipo $cdocidtipo
 * @property \App\Model\Entity\Ccontactotipo $ccontactotipo
 * @property \App\Model\Entity\Cestado $cestado
 * @property \App\Model\Entity\Entidadcontacto[] $entidadcontactos
 * @property \App\Model\Entity\User[] $users
 */
class Contacto extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'nombres' => true,
        'apellidos' => true,
        'nacional' => true,
        'docid' => true,
        'email' => true,
        'cdocidtipo_id' => true,
        'ccontactotipo_id' => true,
        'descripcion' => true,
        'cestado_id' => true,
        'created' => true,
        'cpaise_id' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true,
        'trash' => true,
        'cdocidtipo' => true,
        'ccontactotipo' => true,
        'cestado' => true,
        'entidadcontactos' => true,
        'users' => true,
        'nombre_completo' => true
    ];

    protected $_virtual = ['nombre_completo'];

    protected function _getNombreCompleto()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
}
