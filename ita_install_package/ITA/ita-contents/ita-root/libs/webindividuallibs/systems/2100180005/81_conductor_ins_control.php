<?php
//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
    //////////////////////////////////////////////////////////////////////
    //
    //  【特記事項】
    //      オーケストレータ別の設定記述あり
    //
    //////////////////////////////////////////////////////////////////////


function printOneOfConductorInstances($fxVarsIntConductorInstanceId){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyClassId = "";
    $strStreamOfMovements = "";
    $strStreamOfSymphony = "";
    $strExpectedErrMsgBodyForUI = "";
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $aryOutputItemFromSymphonySource = array(
        'SYMPHONY_INSTANCE_ID'=>""
        ,'I_SYMPHONY_NAME'=>"htmlspecialchars"
        ,'I_DESCRIPTION'=>"htmlspecialchars"
        ,'STATUS_ID'=>""
        ,'EXECUTION_USER'=>""
        ,'ABORT_EXECUTE_FLAG'=>""
        ,'OPERATION_NO_UAPK'=>""
        ,'OPERATION_NO_IDBH'=>"htmlspecialchars"
        ,'OPERATION_NAME'=>"htmlspecialchars"
        ,'TIME_BOOK'=>""
    );
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/71_basic_common_lib.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
        
        $aryRetBody = symphonyInstancePrint($fxVarsIntSymphonyInstanceId);
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $arySymphonySourceOrg = $aryRetBody[0]['SYMPHONY_INSTANCE_INFO'];
        $arySymphonySourceResult = array();
        foreach($aryOutputItemFromSymphonySource as $strKey=>$strFxName){
            if( 0 < strlen($strFxName) ){
                $tmpStrValue = $strFxName($arySymphonySourceOrg[$strKey]);
            }
            else{
                $tmpStrValue = $arySymphonySourceOrg[$strKey];
            }
            $arySymphonySourceResult[$strKey] = $tmpStrValue;
        }
        unset($tmpStrValue);
        $strStreamOfSymphony = makeAjaxProxyResultStream($arySymphonySourceResult);
        
        $aryListSourceOrg = $aryRetBody[0]['MOVEMENTS'];
        $aryListSourceResult = array();
        foreach($aryListSourceOrg as $aryMovementIns){
            foreach($aryMovementIns['CLASS_ITEM'] as $strKey=>$strVal){
                $aryListSourceResult[] = htmlspecialchars($strVal);
            }
            $aryListSourceResult[] = makeAjaxProxyResultStream($aryMovementIns['INS_ITEM']);
        }
        $strStreamOfMovements = makeAjaxProxyResultStream($aryListSourceResult);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intSymphonyClassId,
                         $strStreamOfMovements,
                         $strStreamOfSymphony,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//予約キャンセル[Conductor作業確認画面-操作]
function bookCancelOneOfConductorInstances($fxVarsIntConductorInstanceId){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
        
        $aryRetBody = conductorInstanceBookCancel($fxVarsIntConductorInstanceId);
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $fxVarsIntConductorInstanceId,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//強制停止[Conductor作業確認画面-操作]
function scramOneOfConductorInstances($fxVarsIntConductorInstanceId){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
        
        $aryRetBody = conductorInstanceScram($fxVarsIntConductorInstanceId);
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $fxVarsIntConductorInstanceId,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    return $arrayResult;
}
//保留解除[Conductor作業確認画面-操作]
function holdReleaseOneOfNodeInstances($intNodeInstanceId){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyInstanceId = "";
    $intSeqNo = "";
    $strExpectedErrMsgBodyForUI = "";
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
        
        $aryRetBody = nodeInstanceHoldRelease($intNodeInstanceId);

        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intNodeInstanceId,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//Conductorステータス取得
function printConductorInstanceStatus($fxVarsIntConductorInstanceId){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $strExpectedErrMsgBodyForUI = "";
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");

        $aryRetBody = conductorInstancePrint($fxVarsIntConductorInstanceId);

        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $strStreamOfStatus = json_encode($aryRetBody[0],JSON_UNESCAPED_UNICODE);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfStatus,
                         $strSysErrMsgBody
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}

//REST各種操作実行[REST-操作]
function conductorInstanceControlFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayRetBody = array();
    
    $intResultStatusCode = null;
    $aryForResultData = array();
    $aryPreErrorData = null;
    
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $strSymphonyInstanceId = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $strSysErrMsgBody = '';
    $intErrorPlaceMark = "";
    $strErrorPlaceFmt = "%08d";
    
    $intUIErrorMsgSaveIndex = -1;
    $aryOverrideForErrorData = array();
    
    $intResultInfoCode="000";//結果コード(正常終了)

   // 各種ローカル変数を定義
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/71_basic_common_lib.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");

        if( is_array($objJSONOfReceptedData) !== true ){
            $tmpAryOrderData = array();
        }
        else{
            $tmpAryOrderData = $objJSONOfReceptedData;
        }
        list($intSymphonyInstanceId       , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('CONDUCTOR_INSTANCE_ID') ,null);
        list($intNodeInstanceId       , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('NODE_INSTANCE_ID') ,null);

        //予約取消、緊急停止の場合
        if( $strCommand == "CANCEL" || $strCommand == "SCRAM" ){
            //ステータス確認、予約取消、緊急停止の結果コード追加
            $aryRowOfSymInstanceTable = getSingleConductorInfoFromConductorInstances($intSymphonyInstanceId, 1);

            if ( array_key_exists('STATUS_ID', $aryRowOfSymInstanceTable[4])) {
                if ( in_array($aryRowOfSymInstanceTable[4]['STATUS_ID'], array(5,6,7,8,9) )   ){
                    //----予約取消
                    if( $strCommand == "CANCEL"  ){
                        $intResultInfoCode = "002";//結果コード(予約取消不可)
                        $strExpectedErrMsgBodyForUI = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-102040",$aryRowOfSymInstanceTable[4]['STATUS_ID']);
                    }
                    //----緊急停止
                    if( $strCommand == "SCRAM"  &&  $aryRowOfSymInstanceTable[4]['ABORT_EXECUTE_FLAG'] == '2' ){
                        $intResultInfoCode = "003";//結果コード(緊急停止不可)
                        $strExpectedErrMsgBodyForUI = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-101030",$aryRowOfSymInstanceTable[4]['STATUS_ID']);
                    }
                }
            }else{
                 if( $strCommand == "CANCEL" )$intResultInfoCode = "002";//結果コード(予約取消不可)
                 if( $strCommand == "SCRAM"  )$intResultInfoCode = "003";//結果コード(緊急停止不可)
            }  
        }

        //一時停止解除の場合
        if( $strCommand == "RELEASE" ){
            //保留解除のフラグチェック、結果コード追加
            $aryRetBody = getInfoFromOneOfConductorInstances($intSymphonyInstanceId,1);

            $arrNodeInfo=array();
            foreach ($aryRetBody[5] as $key => $value) {
                $arrNodeInfo[ $value['NODE_INSTANCE_NO']]=$value;
            }
            if( $arrNodeInfo[$intNodeInstanceId]['STATUS_ID'] == 8 && $arrNodeInfo[$intNodeInstanceId]['RELEASED_FLAG'] == 1 ){
                $execReleaseflg=true;

            }else{
                $execReleaseflg=false;
                $intResultInfoCode = "004";//結果コード(一時停止解除不可)  
                if( $arrNodeInfo[$intNodeInstanceId]['I_NODE_TYPE_ID'] == 8 ){
                    $strExpectedErrMsgBodyForUI =  $g['objMTS']->getSomeMessage("ITABASEH-ERR-170101");
                }else{
                    $strExpectedErrMsgBodyForUI =  $g['objMTS']->getSomeMessage("ITABASEH-ERR-170102");;

                }

            }
     
        }

        switch($strCommand){
            case "INFO":
                $aryRetBody = conductorInstancePrint($intSymphonyInstanceId);
                #1825
                if( isset($aryRetBody[0]['CONDUCTOR_INSTANCE_INFO']['CONDUCTOR_CLASS_NO']) ){
                    unset($aryRetBody[0]['CONDUCTOR_INSTANCE_INFO']['CONDUCTOR_CLASS_NO']);
                }

                $intUIErrorMsgSaveIndex = 4;
                break;
            case "CANCEL":
                $aryRetBody = conductorInstanceBookCancel($intSymphonyInstanceId);
                $intUIErrorMsgSaveIndex = 4;
                break;
            case "SCRAM":
                $aryRetBody = conductorInstanceScram($intSymphonyInstanceId);
                $intUIErrorMsgSaveIndex = 4;
                break;
            case "RELEASE":
                list($intSeqNo       , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('MOVEMENT_SEQ_NO') ,null);
                if( $execReleaseflg === true ){
                    $aryRetBody = nodeInstanceHoldRelease($intNodeInstanceId);
                    $intUIErrorMsgSaveIndex = 4;

                }else{
                    $intUIErrorMsgSaveIndex = 4;

                    $aryRetBody=array(
                        array("CONDUCTOR_INSTANCE_ID" => $intSymphonyInstanceId,"NODE_INSTANCE_NO" => $intNodeInstanceId),
                        "","","",$strExpectedErrMsgBodyForUI
                    );
                }
                break;
            default:
                $intErrorPlaceMark = 1000;
                $intResultStatusCode = 400;
                $aryOverrideForErrorData['Error'] = 'Forbidden';
                web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3810101"));
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                break;
        }

        if( $aryRetBody[1] !== null ){
                if( $aryRetBody[$intUIErrorMsgSaveIndex] != "" )$strExpectedErrMsgBodyForUI = $aryRetBody[$intUIErrorMsgSaveIndex];
        }
        
        if( headers_sent() === true ){
            $intErrorType = 900;
            $intErrorPlaceMark = 3000;
            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $intResultStatusCode = 200;
        
        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
        $aryForResultData['resultdata'] = $aryRetBody[0];
        $aryForResultData['resultdata']['RESULTCODE'] = $intResultInfoCode;
        $aryForResultData['resultdata']['RESULTINFO'] = $strExpectedErrMsgBodyForUI;

    }
    catch (Exception $e){
        // 失敗時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['errorInfo'];
        foreach($aryOverrideForErrorData as $strKey=>$varVal){
            $aryForResultData[$strKey] = $varVal;
        }
        if( 0 < strlen($strExpectedErrMsgBodyForUI) ){
            $aryPreErrorData[] = $strExpectedErrMsgBodyForUI;
        }
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        if( $intResultStatusCode === null ) $intResultStatusCode = 500;
        if( $aryPreErrorData !== null ) $aryForResultData['Error'] = $aryPreErrorData;
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        $intResultInfoCode   = "";//結果コード(異常終了)
    }

    if($intResultInfoCode != "")$aryForResultData['resultdata']['RESULTCODE'] = $intResultInfoCode;

    $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                          'ResultData'=>$aryForResultData);
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
}

?>