<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 12.04.2018
 * Time: 17:32
 */
require_once 'IConfirmable.php';
require_once 'OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';

class TestiqleyecekShexs
{
    const TIP_REDAKT_EDEN     = 'redakt_eden';
    const TIP_VISA_VEREN      = 'visa_veren';
    const TIP_RAZILASHDIRAN   = 'razilashdiran';
    const TIP_CHAP_EDEN       = 'chap_eden';
    const TIP_REY_MUELIFI     = 'rey_muelifi';
    const TIP_KURATOR         = 'kurator';
    const TIP_TANISH_OL       = 'tanish_ol';
    const TIP_MESUL_SHEXS     = 'mesul_shexs';
    const TIP_IMZALAYAN_SHEXS = 'imza';
    const TIP_UMUMI_SHOBE     = 'umumi_shobe';
    const TIP_UMUMI_SHOBE_NETICE  = 'umumi_shobe_netice';
    const KIM_GONDERIR        = 'kim_gonderir';
    const TIP_HESABAT_VER_PARTLAMAMISH_TEK_SURAT  = 'hesabat_ver_pts';

    const STATUS_TESTIQLEMEYIB = 0;
    const STATUS_TESTIQLEYIB   = 1;
    const STATUS_IMTINA_OLUNUB = 2;

    private $testiqleyecekShexs;
    private $sessionUserId;

    public function __construct($id)
    {
        $this->testiqleyecekShexs = DB::fetchById('tb_prodoc_testiqleyecek_shexs', $id);
        $this->sessionUserId = $_SESSION['erpuserid'];
    }

    public function testiqle($information = null)
    {
        if (is_null($information)) {
            $information = [];
        }

        if (!array_key_exists('note', $information)) {
            $information['note'] = NULL;
        }
        $className = $this->testiqleyecekShexs['related_class'];
        $instance = new $className($this->testiqleyecekShexs['related_record_id']);


        if ($instance instanceof IConfirmable) {
            $confirmation = new Service\Confirmation\Confirmation($instance);
            $confirmingUsersForConfirmation = $confirmation->getUsersForConfirmation($this->testiqleyecekShexs);
        } else {
            $confirmingUsersForConfirmation = [];
            $confirmingUsersForConfirmation[] = (int)$this->testiqleyecekShexs['user_id'];
        }
        $powerOfAttorney_id = null;
        if ($instance instanceof \PowerOfAttorney\IPowerOfAttorneyDocument) {
            $powerOfAttorney = new PowerOfAttorney\PowerOfAttorney(
                $instance,
                $this->sessionUserId,
                new User()
            );

            if (!$powerOfAttorney->canExecute($confirmingUsersForConfirmation)) {
                throw new Exception('Access error!');
            }
            $powerOfAttorney_id = $powerOfAttorney->getPowerOfAttorneysByExecutors($confirmingUsersForConfirmation);
        } else {
            if (!in_array($this->sessionUserId, $confirmingUsersForConfirmation, true)) {
                throw new Exception('Access error!');
            }
        }

        DB::update('tb_prodoc_testiqleyecek_shexs', [
            'status' => self::STATUS_TESTIQLEYIB,
            'status_changed_at' => 'GETDATE()',
            'operator_id' => $this->sessionUserId
        ], $this->testiqleyecekShexs['id'], 'id', ['status_changed_at']);

        if ($instance instanceof IConfirmable) {
            $confirmingUser = $this->testiqleyecekShexs;
            $confirmingUser['note'] = $information['note'];
            $confirmingUser['power_of_attorney']=$powerOfAttorney_id;
            $instance->onApprove($confirmingUser, $confirmation);
        }

        $this->hamiTestiqleyendenSonra();
    }

    public function imtinaEt($information = null)
    {
        if (is_null($information)) {
            $information = [];
        }

        if (!array_key_exists('note', $information)) {
            $information['note'] = NULL;
        }

        $className = $this->testiqleyecekShexs['related_class'];
        $instance = new $className($this->testiqleyecekShexs['related_record_id']);

        if ($instance instanceof IConfirmable) {
            $confirmation = new Service\Confirmation\Confirmation($instance);
            $confirmingUsersForConfirmation = $confirmation->getUsersForConfirmation($this->testiqleyecekShexs);
        } else {
            $confirmingUsersForConfirmation = [];
            $confirmingUsersForConfirmation[] = (int)$this->testiqleyecekShexs['user_id'];
        }
        $powerOfAttorney_id=null;
        if ($instance instanceof \PowerOfAttorney\IPowerOfAttorneyDocument) {
            $powerOfAttorney = new PowerOfAttorney\PowerOfAttorney(
                $instance,
                $this->sessionUserId,
                new User()
            );

            if (!$powerOfAttorney->canExecute($confirmingUsersForConfirmation)) {
                throw new Exception('Access error!');
            }

            $powerOfAttorney_id = $powerOfAttorney->getPowerOfAttorneysByExecutors($confirmingUsersForConfirmation);

        } else {
            if (!in_array($this->sessionUserId, $confirmingUsersForConfirmation, true)) {
                throw new Exception('Access error!');
            }
        }

        // TODO: check order
        DB::update('tb_prodoc_testiqleyecek_shexs', [
            'status' => self::STATUS_IMTINA_OLUNUB,
            'status_changed_at' => 'GETDATE()',
            'operator_id' => $this->sessionUserId
        ], $this->testiqleyecekShexs['id'], 'id', ['status_changed_at']);

        if($this->testiqleyecekShexs['related_class']=='Appeal'){
            $sql = sprintf("
            DELETE FROM tb_prodoc_testiqleyecek_shexs
            WHERE related_class = '%s' AND  related_record_id = %s AND id <> %s
        ", $this->testiqleyecekShexs['related_class'],$this->testiqleyecekShexs['related_record_id'],$this->testiqleyecekShexs['id']);
            DB::exec($sql);
        }

        if ($instance instanceof IConfirmable) {
            $confirmation = new Service\Confirmation\Confirmation($instance);

            $confirmingUser = $this->testiqleyecekShexs;
            $confirmingUser['note'] = $information['note'];
            $confirmingUser['powerOfAttorney_id']=$powerOfAttorney_id;

            $instance->onStatusChange(IConfirmable::STATUS_IMTINA_OLUNUB, $confirmation);
            $instance->onCancel($confirmingUser, $confirmation);
        }

    }

    public function getInfo(){
        return $this->testiqleyecekShexs;
    }

    public function getSessionUserId(){
        return $this->sessionUserId;
    }

    public static function getBtnNameByType($tip){
        $BtnName = "";

        switch ($tip)
        {
            case self::TIP_TANISH_OL:
                $BtnName = "Tanış ol";
                break;
            case self::TIP_UMUMI_SHOBE_NETICE:
                $BtnName = "Nəticə qeyd et";
                break;
            default:
                $BtnName = "";
        }

        return $BtnName;
    }

    public function hamiTestiqleyendenSonra()
    {


        $sql = sprintf("
            SELECT COUNT(*)
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE
            status = %s AND
            related_record_id = %s AND
            related_class = '%s'
        ",
            self::STATUS_TESTIQLEMEYIB,
            $this->testiqleyecekShexs['related_record_id'],
            $this->testiqleyecekShexs['related_class']
        );

        $testiqlenmeyenlerinSayi = (int)DB::fetchColumn($sql);

        $className = $this->testiqleyecekShexs['related_class'];
        $instance = new $className($this->testiqleyecekShexs['related_record_id']);

        if (0 === $testiqlenmeyenlerinSayi) {

            if ($instance instanceof IConfirmable) {
                $confirmation = new Service\Confirmation\Confirmation($instance);

                $instance->onStatusChange(IConfirmable::STATUS_TESTIQLENIB, $confirmation);
                $instance->onFullApprove();
            }
        }
    }
}