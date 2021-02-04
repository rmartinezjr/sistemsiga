<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Privmodelo Entity
 *
 * @property int $id
 * @property int $modelofuncion_id
 * @property int $perfil_id
 * @property bool $allow
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 *
 * @property \App\Model\Entity\Modelofuncion $modelofuncion
 * @property \App\Model\Entity\Perfil $perfil
 */
class Privmodelo extends Entity
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
        'modelofuncion_id' => true,
        'perfil_id' => true,
        'allow' => true,
        'created' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true,
        'trash' => true,
        'modelofuncion' => true,
        'perfil' => true
    ];
}
