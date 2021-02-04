<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Perfil Entity
 *
 * @property int $id
 * @property string $nombre
 * @property bool $su
 * @property int $cestado_id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 *
 * @property \App\Model\Entity\Cestado $cestado
 * @property \App\Model\Entity\Privmodelo[] $privmodelos
 * @property \App\Model\Entity\User[] $users
 */
class Perfil extends Entity
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
        'su' => true,
        'cestado_id' => true,
        'created' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true,
        'trash' => true,
        'cestado' => true,
        'privmodelos' => true,
        'users' => true
    ];
}
