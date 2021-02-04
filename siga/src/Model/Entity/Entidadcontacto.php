<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Entidadcontacto Entity
 *
 * @property int $id
 * @property int $entidad_id
 * @property int $contacto_id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 *
 * @property \App\Model\Entity\Entidad $entidad
 * @property \App\Model\Entity\Contacto $contacto
 */
class Entidadcontacto extends Entity
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
        'entidad_id' => true,
        'contacto_id' => true,
        'created' => true,
        'usuario' => true,
        'entidad' => true,
        'contacto' => true
    ];
}
