<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CronogramadetperiodosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CronogramadetperiodosTable Test Case
 */
class CronogramadetperiodosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CronogramadetperiodosTable
     */
    public $Cronogramadetperiodos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cronogramadetperiodos',
        'app.cronogramas',
        'app.fproyectos',
        'app.datarecursos',
        'app.recursoestructuradets',
        'app.cobjetoplanifs',
        'app.cestados',
        'app.ccontactotipos',
        'app.cdocidtipos',
        'app.centidadrols',
        'app.centidadtipos',
        'app.cformtipos',
        'app.cindicadorambitos',
        'app.cindicadortipos',
        'app.ctipodatos',
        'app.cunidads',
        'app.recursoestructuras',
        'app.recursoestructurafds',
        'app.formdinamics',
        'app.cformdinamictipos',
        'app.formseccions',
        'app.formpreguntas',
        'app.fdpreguntas',
        'app.cfdpregpilas',
        'app.fdrespuestas',
        'app.cdatotipos',
        'app.formrespuestas',
        'app.recursoestructurawfs',
        'app.workflows',
        'app.dataforms',
        'app.wfetapas',
        'app.wfformdinamicprivs',
        'app.perfils',
        'app.privmodelos',
        'app.modelofuncions',
        'app.modelos',
        'app.funcions',
        'app.menuitems',
        'app.menus',
        'app.users',
        'app.contactos',
        'app.entidadcontactos',
        'app.entidads',
        'app.cwfaccions',
        'app.dataformdets',
        'app.wftransicions',
        'app.regaccions',
        'app.cacciontipos',
        'app.regaccionwfs',
        'app.regaccionfiles',
        'app.factividades',
        'app.fproductos',
        'app.fproyectometas',
        'app.indicadores',
        'app.datarecursoindicadormetas',
        'app.presupuestos',
        'app.presupuestodets',
        'app.pformgfuentes',
        'app.pforms',
        'app.pformampliaciones',
        'app.pformffinanciamientos',
        'app.ccuentacontabs',
        'app.ccuentacontabmaestros',
        'app.ccuentacontabtipocostos',
        'app.ccuentacontabtipos',
        'app.ccuentacontabinsumos',
        'app.insumos',
        'app.ctipoinsumos',
        'app.ccuentacontabrestrictdets',
        'app.ccuentacontabrestricts',
        'app.cpuestotrabajos',
        'app.cestructuras',
        'app.vfactividades',
        'app.productos',
        'app.subcomponentes',
        'app.componentes',
        'app.convocatorias',
        'app.actividads',
        'app.tecnicas',
        'app.vfproductos',
        'app.cronogramadets'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Cronogramadetperiodos') ? [] : ['className' => CronogramadetperiodosTable::class];
        $this->Cronogramadetperiodos = TableRegistry::get('Cronogramadetperiodos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Cronogramadetperiodos);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
