<?php
//   Copyright 2021 NEC Corporation
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

//////////////////////////////////////////////////////////////////
//  メニューエクスポートRestAPI (INFO　EXECUTE)
//////////////////////////////////////////////////////////////////
function menuExportFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){

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
    
    $aryOverrideForErrorData = array();

    $intResultInfoCode="000";//結果コード(正常終了)

    // メニューIDの桁数
    define('MENU_ID_LENGTH', 11);
    // インポートファイル一つに保存するレコード数
    define('MAX_RECORD_CNT', 1000);
    //ログインID取得
    define('ACCOUNT_NAME', $g['login_id']);

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    try{
        //X-command毎の処理 
        switch($strCommand){
            case "INFO":
                    $aryRetBody['MENU_LIST'] = menuExportInfoFromRest();
                break;

            case "EXECUTE":
                //エクスポート対象の確認
                $chkflg = validateMenuNo($objJSONOfReceptedData);
                $chkflg2 = validateModeid($objJSONOfReceptedData);

                if ( $chkflg == "" && $chkflg2 == "" ) {
                    $aryRetBody = menuExportExecutionFromRest($objJSONOfReceptedData);
                }else{
                    $aryRetBody['TASK_ID'] = "";
                    $aryRetBody['RESULTCODE'] = "002";
                    $aryRetBody['RESULTINFO'] = $g['objMTS']->getSomeMessage("ITABASEH-ERR-900077");
                    
                }
                break;

            default:
                $intErrorPlaceMark = 1000;
                $intResultStatusCode = 400;
                $aryOverrideForErrorData['Error'] = 'Forbidden';
                web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
        $aryForResultData['resultdata']=$aryRetBody;

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
    }
    $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                          'ResultData'=>$aryForResultData);
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
}


//////////////////////////////////////////
//  メニューNoのエクスポート可否判定  //
//////////////////////////////////////////
function validateMenuNo($objJSONOfReceptedData){
    global $g;
    $allowmenulist=array();
    $chkflag="";

    //チェック用リスト作成
    $aryRetBody = menuExportInfoFromRest();
    foreach ($aryRetBody as $key => $value) {
        foreach ($value['menu'] as $key2 => $value2) {
            $allowmenulist[$value2['menu_id']]="";;
        }
    }

    //チェック用リストと比較
    $tmpJSONOfReceptedData = $objJSONOfReceptedData;
    unset($tmpJSONOfReceptedData['zip']);
    unset($tmpJSONOfReceptedData['menu_on']);
    #270 対応
    unset($tmpJSONOfReceptedData['abolished_type']);

    foreach ($tmpJSONOfReceptedData as $key => $value) {
        foreach ($value as $key2 => $value2) {
            if (array_key_exists($value2, $allowmenulist) && strlen($value2) < MENU_ID_LENGTH && ctype_digit($value2) === true ){
                $chkflag="";
            }else{
                $chkflag="1";
            }   
        }
    }
    return $chkflag;
}

//////////////////////////////////////////
//  メニューのエクスポート登録処理  //
//////////////////////////////////////////
function menuExportExecutionFromRest($objJSONOfReceptedData){

    global $g;

    $arrayResult = array();
    $resultMsg = "";
    $resultFlg = "";
    $intResultCode="";

    $tmparray = array();

    $tmparray["abolished_type"]=$objJSONOfReceptedData['abolished_type'];


    //不要な要素の削除
    $tmpJSONOfReceptedData = $objJSONOfReceptedData;
    unset($tmpJSONOfReceptedData['zip']);
    unset($tmpJSONOfReceptedData['menu_on']);
    #270 対応
    unset($tmpJSONOfReceptedData['abolished_type']);

    $menuList = array();

    //メニューidをint型からstring型へ変換
    foreach ($tmpJSONOfReceptedData as $key => $value) {
        foreach ($value as $value2) {
            $tmparray[$key][]= (string)$value2;
        }
        foreach ($value as $value2) {
            $menuList[]= (string)$value2;
        }
        // foreach ($tmpJSONOfReceptedData as $menuGroup => $menuList) {
        //     foreach ($menuList as $menuId) {
        //         $tmp["menuList"][] = (string)$menuId;
        //     }
        // }
    }

    //($_POST 利用関数対応)
    $_POST = $tmparray;
    $_POST["menu_list"] = $menuList;

    try {
            $dirName = date('YmdHis') . mt_rand();
            $exportMenuIdAry = makeExportDataList($dirName);

            // データ登録
            $taskNo = insertBulkExcelTask();
            $res = makeBulkExcelExportMenuList($taskNo);
            $resultMsg = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900024', array($taskNo));
            $_SESSION['data_export_task_no'] = $taskNo;

            renameExportDir($dirName, $taskNo);
            $resultFlg = true;
            $intResultCode= "000";

            $arrayResult["TASK_ID"] = $taskNo;
        } catch (Exception $e) {
            $resultMsg = $e->getMessage();
            $resultFlg = true;
            $intResultCode= "002";

        }

        $arrayResult["RESULTCODE"] = $intResultCode;
        $arrayResult['RESULTINFO'] = strip_tags(trim($resultMsg));
    
        return $arrayResult; 

}

//////////////////////////////////////////
//  /エクスポート可能なメニューの取得  //
//////////////////////////////////////////
function menuExportInfoFromRest(){
    // メニューグループとメニューを取得
    $menuGroupAry = makeExportCheckbox();
    // loadTableを使っていないメニューを除去する
    $retExportAry = getExportMenuList($menuGroupAry);

    return $retExportAry;
}

//////////////////////////////////////////
//  ABOLISHED_TYPE(廃止情報) チェック  対応 //
//////////////////////////////////////////
function validateModeid($objJSONOfReceptedData){
    global $g;

    $chkflag="";

    //チェック用リストと比較
    $tmpJSONOfReceptedData = $objJSONOfReceptedData;

    $arrChkModeList=array();
    //チェック対象
    $arrChkModeList["B_BULK_EXCEL_ABOLISHED_TYPE"] = array( "ROW_ID" => $tmpJSONOfReceptedData['abolished_type'] );

    foreach ($arrChkModeList as $tablename => $colmun) {
        foreach ($colmun as $key => $value) {
            
            $sql  = ' SELECT * FROM ';
            $sql .= " $tablename ";
            $sql .= ' WHERE ';
            $sql .= " $key = $value ";
            $sql .= " AND DISUSE_FLAG = '0'";

            $objQuery = $g['objDBCA']->sqlPrepare($sql);

            if ($objQuery->getStatus() === false) {
                web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                                     array(__FILE__, __LINE__)));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
            }
            $res = $objQuery->sqlExecute();
            if ($res === false) {
                web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                                     array(__FILE__, __LINE__)));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
            }
            $rows = array();
            while ($row = $objQuery->resultFetch()){
                $rows[] = $row;
            }
            
            if( count($rows) != 1 ) return 1;
        }
    }
    return $chkflag;
}

//////////////////////////////////////////
//  date型のチェック
//////////////////////////////////////////
function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
?>
