<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cestado Entity
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property string $colorbkg
 * @property string $colortext
 * @property \Cake\I18n\FrozenTime $created
 * @property string $usuario
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $usuariomodif
 * @property bool $trash
 *
 * @property \App\Model\Entity\Ccontactotipo[] $ccontactotipos
 * @property \App\Model\Entity\Cdocidtipo[] $cdocidtipos
 * @property \App\Model\Entity\Centidadrol[] $centidadrols
 * @property \App\Model\Entity\Centidadtipo[] $centidadtipos
 * @property \App\Model\Entity\Cformtipo[] $cformtipos
 * @property \App\Model\Entity\Cindicadorambito[] $cindicadorambitos
 * @property \App\Model\Entity\Cindicadortipo[] $cindicadortipos
 * @property \App\Model\Entity\Cobjetoplanif[] $cobjetoplanifs
 * @property \App\Model\Entity\Ctipodato[] $ctipodatos
 * @property \App\Model\Entity\Cunidad[] $cunidads
 */
class Cestado extends Entity
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
        'id'    => true,
        'nombre' => true,
        'descripcion' => true,
        'colorbkg' => true,
        'colortext' => true,
        'created' => true,
        'usuario' => true,
        'modified' => true,
        'usuariomodif' => true,
        'trash' => true,
        'ccontactotipos' => true,
        'cdocidtipos' => true,
        'centidadrols' => true,
        'centidadtipos' => true,
        'cformtipos' => true,
        'cindicadorambitos' => true,
        'cindicadortipos' => true,
        'cobjetoplanifs' => true,
        'ctipodatos' => true,
        'cunidads' => true
    ];
}
