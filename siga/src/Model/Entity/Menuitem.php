<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Menuitem Entity
 *
 * @property int $id
 * @property int $menu_id
 * @property string $nombre
 * @property string $alias
 * @property int $modelofuncion_id
 * @property string $url
 * @property string $descripcion
 * @property int $cestado_id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 *
 * @property \App\Model\Entity\Menu $menu
 * @property \App\Model\Entity\Modelofuncion $modelofuncion
 * @property \App\Model\Entity\Cestado $cestado
 */
class Menuitem extends Entity
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
        'menu_id' => true,
        'nombre' => true,
        'alias' => true,
        'modelofuncion_id' => true,
        'url' => true,
        'descripcion' => true,
        'cestado_id' => true,
        'created' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true,
        'trash' => true,
        'menu' => true,
        'modelofuncion' => true,
        'cestado' => true
    ];
}
