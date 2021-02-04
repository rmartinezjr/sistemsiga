<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cpreference Entity
 *
 * @property int $id
 * @property string $nombre
 * @property string $params
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 */
class Cpreference extends Entity
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
        'nombre' => true,
        'params' => true,
        'created' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true
    ];
}
