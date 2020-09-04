<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 11.07.2018
 * Time: 13:20
 */

namespace View\Helper;

require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';

use PowerOfAttorney\IPowerOfAttorneyDocument;
use PowerOfAttorney\PowerOfAttorney;
use User;
use DB;

class Proxy
{
    private $powerOfAttorneyDocument;
    private $principalPowerOfAttorneys;
    private $listOfPrincipals;
    private $proxyNames;

    public function __construct(IPowerOfAttorneyDocument $powerOfAttorneyDocument)
    {
        $this->powerOfAttorneyDocument = $powerOfAttorneyDocument;
    }

    public function setListOfPrincipals($listOfPrincipals)
    {
        $this->listOfPrincipals = array_filter($listOfPrincipals, function($principal) {
            return !is_null($principal) && $principal <> 0;
        });
    }

    public function getProxyNameByPrincipal($principalId, $principalName)
    {
        if (is_null($this->principalPowerOfAttorneys)) {
            $powerOfAttorney = new PowerOfAttorney(
                $this->powerOfAttorneyDocument,
                $_SESSION['erpuserid'],
                new User()
            );

            $this->principalPowerOfAttorneys = $powerOfAttorney->getPowerOfAttorneysAsDirectPrincipal(
                $this->listOfPrincipals
            );

            $this->proxyNames = self::getProxyNames($this->principalPowerOfAttorneys);
        }

        if (is_null($principalId) || (int)$principalId == "") {
            return '<i> Yoxdur</i>';
        }

        if (!isset($this->principalPowerOfAttorneys[$principalId])) {
            if (isDebugModeEnabled()) {
                $principalLink = "console/C0mm4nds.php?command=$principalId&type=command&url=test000.php&reload";

                return sprintf(' <a href="%s"><i class="fa fa-sign-in"></i></a> <i class="fa fa-user"></i> %s - %s',
                    $principalLink,
                    htmlspecialchars($principalName),
                    $principalId
                );
            } else {
                return sprintf(' <i class="fa fa-user"></i> %s',
                    htmlspecialchars($principalName)
                );
            }
        }

        $proxyId    = $this->principalPowerOfAttorneys[$principalId]['to_user_id'];
        $docNumber  = $this->principalPowerOfAttorneys[$principalId]['document_number'];
        $documentId = $this->principalPowerOfAttorneys[$principalId]['document_id'];

        if (isDebugModeEnabled()) {
            $principalLink = "console/C0mm4nds.php?command=$principalId&type=command&url=test000.php&reload";
            $proxyLink = "console/C0mm4nds.php?command=$proxyId&type=command&url=test000.php&reload";

            return sprintf(
                ' <a href="%s"><i class="fa fa-sign-in"></i></a> <i class="fa fa-user"></i> %s  ( V.M.İ.E: <a href="%s"><i class="fa fa-sign-in"></i></a> <i class="fa fa-user"></i> %s )
                    <br><span style="font-size: 12px">Etibarnamə: <a>%s</a></span>',
                $principalLink,
                htmlspecialchars($principalName),
                $proxyLink,
                htmlspecialchars($this->proxyNames[$proxyId]),
                DocumentLinkGenerator::generateDashboardLink($docNumber, $documentId)
            );
        } else {

            if(getProjectName()===TS){
                return sprintf(
                    ' <i class="fa fa-user"></i> %s ( V.M.İ.E: <i class="fa fa-user"></i> %s  )
                    ',
                    htmlspecialchars($principalName),
                    htmlspecialchars($this->proxyNames[$proxyId])
                );
            }else{
                return sprintf(
                    ' <i class="fa fa-user"></i> %s ( V.M.İ.E: <i class="fa fa-user"></i> %s  )
                    <br><span style="font-size: 12px">Etibarnamə: <a>%s</a></span>',
                    htmlspecialchars($principalName),
                    htmlspecialchars($this->proxyNames[$proxyId]),
                    DocumentLinkGenerator::generateDashboardLink($docNumber, $documentId)
                );
            }

        }
    }

    private function getProxyNames(array $powerOfAttorneys)
    {
        $proxyUsers = [];
        foreach ($powerOfAttorneys as $powerOfAttorney) {
            $proxyUsers[] = $powerOfAttorney['to_user_id'];
        }

        if (empty($proxyUsers)) {
            return [];
        }

        $sql = sprintf("
            SELECT user_ad, USERID
            FROM v_user_adlar
            WHERE USERID IN (%s)
	    ", implode(',', $proxyUsers));

        $proxyUserNames = [];
        foreach (DB::fetchAll($sql) as $proxyUser) {
            $proxyUserNames[$proxyUser['USERID']] = $proxyUser['user_ad'];
        }

        return $proxyUserNames;
    }
}