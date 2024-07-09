<?php

use PHPUnit\Framework\TestCase;

class MockCOM {
    public $Token;
    public $Sign;
    public $Cuit;
    public $Reprocesar;
    public $AppServerStatus;
    public $DbServerStatus;
    public $AuthServerStatus;
    public $Resultado;
    public $CbteNro;
    public $Vencimiento;
    public $EmisionTipo;
    public $Obs;
    public $Motivo;
    public $XmlRequest;
    public $XmlResponse;

    public function CreateTRA() { return 'mockedTRA'; }
    public function SignTRA() { return 'mockedCMS'; }
    public function LoginCMS() { return 'mockedTA'; }
    public function Conectar() { return true; }
    public function Dummy() { 
        $this->AppServerStatus = 'OK';
        $this->DbServerStatus = 'OK';
        $this->AuthServerStatus = 'OK';
    }
    public function CompUltimoAutorizado() { return 1; }
    public function CrearFactura() { return true; }
    public function AgregarCmpAsoc() { return true; }
    public function AgregarTributo() { return true; }
    public function AgregarIva() { return true; }
    public function AgregarOpcional() { return true; }
    public function CAESolicitar() { 
        $this->Resultado = 'A';
        $this->CbteNro = '00000001';
        $this->Vencimiento = '20231231';
        $this->EmisionTipo = 'CAE';
        return 'mockedCAE'; 
    }
}

class WSFEv1Test extends TestCase
{
    protected $WSAA;
    protected $WSFEv1;

    protected function setUp(): void
    {
        $this->WSAA = new MockCOM();
        $this->WSFEv1 = new MockCOM();
    }

    /**
     * @covers MockCOM::CreateTRA
     */
    public function testCreateTRA()
    {
        $tra = $this->WSAA->CreateTRA();
        $this->assertEquals('mockedTRA', $tra);
    }

    /**
     * @covers MockCOM::SignTRA
     */
    public function testSignTRA()
    {
        $cms = $this->WSAA->SignTRA('mockedTRA', 'path/to/cert', 'path/to/key');
        $this->assertEquals('mockedCMS', $cms);
    }

    /**
     * @covers MockCOM::LoginCMS
     */
    public function testLoginCMS()
    {
        $ta = $this->WSAA->LoginCMS('mockedCMS');
        $this->assertEquals('mockedTA', $ta);
    }

    /**
     * @covers MockCOM::Dummy
     */
    public function testDummy()
    {
        $this->WSFEv1->Dummy();
        $this->assertEquals('OK', $this->WSFEv1->AppServerStatus);
        $this->assertEquals('OK', $this->WSFEv1->DbServerStatus);
        $this->assertEquals('OK', $this->WSFEv1->AuthServerStatus);
    }

    /**
     * @covers MockCOM::CompUltimoAutorizado
     */
    public function testCompUltimoAutorizado()
    {
        $ult = $this->WSFEv1->CompUltimoAutorizado(1, 1);
        $this->assertEquals(1, $ult);
    }

    /**
     * @covers MockCOM::CrearFactura
     */
    public function testCrearFactura()
    {
        $ok = $this->WSFEv1->CrearFactura(1, 80, '23111111113', 1, 1, 1, 1, '179.25', '2.00', '150.00', '26.25', '1.00', '0.00', '20230101', '', '', '', 'PES', '1.000');
        $this->assertTrue($ok);
    }

    /**
     * @covers MockCOM::AgregarCmpAsoc
     */
    public function testAgregarCmpAsoc()
    {
        $ok = $this->WSFEv1->AgregarCmpAsoc(19, 2, 1234);
        $this->assertTrue($ok);
    }

    /**
     * @covers MockCOM::AgregarTributo
     */
    public function testAgregarTributo()
    {
        $ok = $this->WSFEv1->AgregarTributo(99, 'Impuesto Municipal', '100.00', '0.10', '0.10');
        $this->assertTrue($ok);
    }

    /**
     * @covers MockCOM::AgregarIva
     */
    public function testAgregarIva()
    {
        $ok = $this->WSFEv1->AgregarIva(5, '100.00', '21.00');
        $this->assertTrue($ok);
    }

    /**
     * @covers MockCOM::AgregarOpcional
     */
    public function testAgregarOpcional()
    {
        $ok = $this->WSFEv1->AgregarOpcional(5, "02");
        $this->assertTrue($ok);
    }

    /**
     * @covers MockCOM::CAESolicitar
     */
    public function testCAESolicitar()
    {
        $this->WSFEv1->Reprocesar = true;
        $cae = $this->WSFEv1->CAESolicitar();
        $this->assertEquals('mockedCAE', $cae);
        $this->assertEquals('A', $this->WSFEv1->Resultado);
        $this->assertEquals('00000001', $this->WSFEv1->CbteNro);
        $this->assertEquals('20231231', $this->WSFEv1->Vencimiento);
        $this->assertEquals('CAE', $this->WSFEv1->EmisionTipo);
    }
    
}
