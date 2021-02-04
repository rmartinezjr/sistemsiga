<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ModeloMenu Entity
 *
 * @property int $id
 * @property string $modelo
 * @property string $alias
 * @property string $descripcion
 * @property int $cestado_id
 * @property bool $movil
 * @property bool $trash
 * @property string $estado
 * @property int $modelofuncion_id
 * @property int $id_menuitems
 * @property string $alias_menuitems
 * @property int $id_submenu
 * @property string $alias_submenu
 * @property int $id_menu
 * @property string $alias_menu
 *
 * @property \App\Model\Entity\Modelos $modelos
 * @property \App\Model\Entity\ModelosCestado $modelos_cestado
 * @property \App\Model\Entity\Cestado $cestado
 * @property \App\Model\Entity\Modelofuncion $modelofuncion
 */
class ModeloMenu extends Entity
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
        'id' => true,
        'modelo' => true,
        'alias' => true,
        'descripcion' => true,
        'cestado_id' => true,
        'movil' => true,
        'trash' => true,
        'estado' => true,
        'modelofuncion_id' => true,
        'id_menuitems' => true,
        'alias_menuitems' => true,
        'id_submenu' => true,
        'alias_submenu' => true,
        'id_menu' => true,
        'alias_menu' => true,
        'modelos' => true,
        'modelos_cestado' => true,
        'cestado' => true,
        'modelofuncion' => true
    ];
}
