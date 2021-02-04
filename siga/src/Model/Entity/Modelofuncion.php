<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Modelofuncion Entity
 *
 * @property int $id
 * @property int $modelo_id
 * @property int $funcion_id
 * @property bool $used
 * @property int $cestado_id
 * @property int $orden
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 *
 * @property \App\Model\Entity\Modelo $modelo
 * @property \App\Model\Entity\Funcion $funcion
 * @property \App\Model\Entity\Cestado $cestado
 * @property \App\Model\Entity\Menuitem[] $menuitems
 * @property \App\Model\Entity\Privmodelo[] $privmodelos
 */
class Modelofuncion extends Entity
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
        'modelo_id' => true,
        'funcion_id' => true,
        'used' => true,
        'cestado_id' => true,
        'orden' => true,
        'created' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true,
        'trash' => true,
        'modelo' => true,
        'funcion' => true,
        'cestado' => true,
        'menuitems' => true,
        'privmodelos' => true
    ];
}
