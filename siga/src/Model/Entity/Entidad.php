<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Entidad Entity
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string $nombrelargo
 * @property bool $nacional
 * @property bool $docidnull
 * @property string $docid
 * @property int $cdocidtipo_id
 * @property int $centidadtipo_id
 * @property int $centidadrol_id
 * @property string $descripcion
 * @property int $cestado_id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 *
 * @property \App\Model\Entity\Cdocidtipo $cdocidtipo
 * @property \App\Model\Entity\Centidadtipo $centidadtipo
 * @property \App\Model\Entity\Centidadrol $centidadrol
 * @property \App\Model\Entity\Cestado $cestado
 * @property \App\Model\Entity\Entidadcontacto[] $entidadcontactos
 */
class Entidad extends Entity
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
        'codigo' => true,
        'nombre' => true,
        'nombrelargo' => true,
        'nacional' => true,
        'docidnull' => true,
        'docid' => true,
        'cdocidtipo_id' => true,
        'centidadtipo_id' => true,
        'centidadrol_id' => true,
        'descripcion' => true,
        'cestado_id' => true,
        'created' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true,
        'trash' => true,
        'cdocidtipo' => true,
        'centidadtipo' => true,
        'centidadrol' => true,
        'cestado' => true,
        'entidadcontactos' => true
    ];
}
