<?php
require_once 'thinbus/thinbus-srp.php';

require_once 'vendor/pear/math_biginteger/Math/BigInteger.php';

require_once 'thinbus/thinbus-srp-client.php';

use PHPUnit\Framework\TestCase;

/**
 * This subclass lets use override the random 'b' value and constant 'k' value with those seen in a debugger running the js+java thinbus tests.
 */
class NotRandomSrp extends ThinbusSrp
{

    protected $notRandomNumber;

    function setNotRandom($nr)
    {
        $this->notRandomNumber = new Math_BigInteger($nr, 16);
    }

    function createRandomBigIntegerInRange($n)
    {
        return $this->notRandomNumber;
    }
}

/**
 * This subclass lets use override the random 'b' value and constant 'k' value with those seen in a debugger running the js+java thinbus tests.
 */
class NotRandomSrpClient extends ThinbusSrpClient
{

    protected $notRandomNumber;

    function setNotRandom($nr)
    {
        $this->notRandomNumber = new Math_BigInteger($nr, 16);
    }

    function createRandomBigIntegerInRange($n)
    {
        return $this->notRandomNumber;
    }
    
}

class ThibusTest extends TestCase
{

    private $Srp;

    private $SrpClient;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // this is a tiny *unsafe* 512 bit prime used because the ci service has no native BigMath libraries and runs things very slowly
        $N_base10str = "12011227848607788786368694963433884984412082499535709330868967500649221002744550181892301655579232030755877988083054752398217087019239027392446";
        $g_base10str = "2";
        $k_base16str = "a3f8623ed27323beeb61e2b3c3c74370aac903e2f626fd0fdae3cd9df514a54f";
        
        $this->Srp = new NotRandomSrp($N_base10str, $g_base10str, $k_base16str, "sha256");
        
        $this->SrpClient = new NotRandomSrpClient($N_base10str, $g_base10str, $k_base16str, "sha256");
        
        $this->SrpClient->setNotRandom("823466d37e1945a2d4491690bdca79dadd2ee3196e4611342437b7a2452895b9");
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Srp = null;
        
        parent::tearDown();
    }
    
    /**
     * Tests the PHP client session against the PHP server session. 
     */
    public function testMutualAuthentiation() {
        $this->Srp->setNotRandom("823466d37e1945a2d4491690bdca79dadd2ee3196e4611342437b7a2452895b9");
        // salt is created at user first registration
        $salt = $this->SrpClient->generateRandomSalt(); 
        $username = "tom@arcot.com";
        $password = "password1234";
        // verifier to be generated at the browser during user registration and password (or email address) reset only
        $v = $this->SrpClient->generateVerifier($salt, $username, $password);
        // normal login flow step1a client: browser starts with username and password given by user at the browser
        $this->SrpClient->step1($username, $password);
        // server challenge
        $B = $this->Srp->step1($username, $salt, $v);
        // client response is array of credentials
        $credentials = $this->SrpClient->step2($salt, $B);
        $A = $credentials[0];
        $M1 = $credentials[1];
        $M2 = $this->Srp->step2($A, $M1);
        $this->SrpClient->verifyConfirmation($M2);
        // noop assert else phpunit complains about this test. thinbus-php will have thrown exception if authenitication didn't work.
        $this->assertEquals(0, 0);

    }

}
?>
