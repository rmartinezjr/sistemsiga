<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Menu Entity
 *
 * @property int $id
 * @property int $menu_id
 * @property string $nombre
 * @property string $alias
 * @property string|resource $icon
 * @property string|resource $icon2
 * @property string $filetype
 * @property string $filename
 * @property string $descripcion
 * @property int $cestado_id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 * @property int $posicion
 * @property int $orden
 *
 * @property \App\Model\Entity\Menu[] $menus
 * @property \App\Model\Entity\Cestado $cestado
 * @property \App\Model\Entity\Menuitem[] $menuitems
 */
class Menu extends Entity
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
        'icon' => true,
        'icon2' => true,
        'filetype' => true,
        'filename' => true,
        'descripcion' => true,
        'cestado_id' => true,
        'created' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true,
        'trash' => true,
        'posicion' => true,
        'orden' => true,
        'menus' => true,
        'cestado' => true,
        'menuitems' => true
    ];
}
