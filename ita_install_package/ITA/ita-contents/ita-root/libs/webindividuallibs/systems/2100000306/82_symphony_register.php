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


//////////////////////////////////////////////////////////////////
//  シンフォニークラスRestAPI EDIT (登録、更新、廃止、復活) //
//////////////////////////////////////////////////////////////////

function symphonyRegisterFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData,$strApiFlg=false){

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


    $strResultType01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12202");   //登録/Register
    $strResultType02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12203");   //更新/Update
    $strResultType03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12204");   //廃止/Discard
    $strResultType04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12205");   //復活/Restore
    $strResultType99 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12206");   //エラー/Error

    $tmpResult  = array();
    $resultdata = array();
    $resultdata_count = array();
    $resultdata_count['register'] = array("name" => $strResultType01,"ct" => 0);
    $resultdata_count['update']   = array("name" => $strResultType02,"ct" => 0);
    $resultdata_count['delete']   = array("name" => $strResultType03,"ct" => 0);
    $resultdata_count['revive']   = array("name" => $strResultType04,"ct" => 0);
    $resultdata_count['error']    = array("name" => $strResultType99,"ct" => 0);

    // 各種ローカル変数を定義
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    try{
        $objDBCA = new DBConnectAgent();
        $tmpDbConnectResult = $objDBCA->connectOpen();

        //パラメータ整形、入力データチェック
        foreach ($objJSONOfReceptedData as $key => $value) {

            $ret_mov = array();
            $objJSONarrChk = 1;

            if( is_array($value) ){
                //処理種別のチェック
                if ( array_key_exists(0, $value) ){
                    $Process_type = $value[0];
                }else{
                    $Process_type = "";
                }

                if($Process_type == $strResultType02){
                //更新前のデータを取得
                $tmpArrayBind = array('SYMPHONY_CLASS_NO'=>$value[2] );
                $sql = "SELECT * FROM C_SYMPHONY_CLASS_MNG WHERE SYMPHONY_CLASS_NO = :SYMPHONY_CLASS_NO";
                $objQuery = $objDBCA->sqlPrepare($sql);
                $objQuery->sqlBind($tmpArrayBind);
                $r = $objQuery->sqlExecute();
                if($r!=true){
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                while($row = $objQuery->resultFetch()) {
                    if ( !(array_key_exists(3, $value)) ){
                        $value[3] = $row['SYMPHONY_NAME'];
                    }
                    if ( !(array_key_exists(4, $value)) ){
                        $value[4] = $row['DESCRIPTION'];
                    }
                }

                $tmpArrayBind = array('SYMPHONY_CLASS_NO'=>$value[2] );
                $sql = "SELECT * FROM C_MOVEMENT_CLASS_MNG WHERE SYMPHONY_CLASS_NO = :SYMPHONY_CLASS_NO";
                $objQuery = $objDBCA->sqlPrepare($sql);
                $objQuery->sqlBind($tmpArrayBind);
                $r = $objQuery->sqlExecute();
                if($r!=true){
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                while($row = $objQuery->resultFetch()) {
                    if ( !(array_key_exists(10, $value)) ){
                        if($row['NEXT_PENDING_FLAG'] == "1"){
                            $row['NEXT_PENDING_FLAG'] = "checkedValue";
                        }else{
                            $row['NEXT_PENDING_FLAG'] = "";
                        }
                        $value[10][] = array("0" => $row['ORCHESTRATOR_ID'],
                                             "1" => $row['PATTERN_ID'],
                                             "2" => $row['NEXT_PENDING_FLAG'],
                                             "3" => $row['DESCRIPTION'],
                                             "4" => $row['OPERATION_NO_IDBH']);
                    }
                }
                ksort($value);
                }

                //配列構造のチェック
                switch ($Process_type) {
                    //登録、更新
                    case $strResultType01:
                    case $strResultType02:
                    
                            // #1695
                            if ( !(array_key_exists(5, $value)) ){
                                $value[5] = "";
                            }

                            if( count($value) == 7 ){
                                if( array_key_exists( 10, $value )){
                                    if( is_array($value[10])){
                                        $objJSONarrChk=0;
                                    }                                
                                }
                            }
                        break;
                    //廃止、復活
                    case $strResultType03:
                    case $strResultType04:
                        if( count($value) == 3 )$objJSONarrChk=0;
                        break;
                    default:
                        break;
                }
            }

            //データ構造異常時
            if( $objJSONarrChk != 0 ){
                $intErrorType = 2;
                $strErrStepIdInFx="00000100";
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            //登録、更新/廃止、復活時の変換
            switch ($Process_type) {
                //登録、更新
                case $strResultType01:
                case $strResultType02:

                    //symphony要素の配列チェック
                    if( array_key_exists(3, $value) ){
                        if ( !array_key_exists(3, $value) )$value[3]="";
                        if ( !array_key_exists(4, $value) )$value[4]="";
                    }

                    //アクセス権のチェック  #1695
                    if( array_key_exists(5, $value) && !is_array($value[5]) ){
                        $tmpAAuthList = explode( "," ,$value[5] ) ;
                        $arrAccessAuth = array();
                        foreach ( $tmpAAuthList as $tmpAAuth ) {
                            $tmpArrayBind = array( 'ROLE_NAME' => $tmpAAuth );
                            $sql = "SELECT * FROM A_ROLE_LIST WHERE ROLE_NAME = :ROLE_NAME";

                            $objQuery = $objDBCA->sqlPrepare($sql);
                            $objQuery->sqlBind($tmpArrayBind);
                            $r = $objQuery->sqlExecute();
                            if($r!=true){
                                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                            while($row = $objQuery->resultFetch()) {
                                $arrAccessAuth[] = $row['ROLE_ID'];
                            }
                        }               
                        if( $arrAccessAuth != array() ){
                            $value[5] = implode( "," , $arrAccessAuth );
                        }else{
                             $value[5] = "";
                        }
                    }else{
                        $value[5]="";
                    }

                    //symphony要素の成形、型変換
                    $objJSONOfReceptedData[$key][3]= array();
                    unset($objJSONOfReceptedData[$key][4]);
                    $objJSONOfReceptedData[$key][3][]= array(
                                        "name"  => "symphony_name",
                                        "value" => $value[3]
                    );
                    $objJSONOfReceptedData[$key][3][]= array(
                                        "name"  => "symphony_tips",
                                        "value" => $value[4]
                    );
                    // #1695 
                    $objJSONOfReceptedData[$key][3][]= array(
                                        "name"  => "ACCESS_AUTH",
                                        "value" => $value[5]
                    );

                    //最終更新時刻を変換、チェック
                    if ( array_key_exists(7, $value) ){
                        if( $value[7] != "" ) $objJSONOfReceptedData[$key][7] = substr_replace($value[7], "." , 16, 0);
                    }else{
                        $objJSONOfReceptedData[$key][7] =  "";
                    }

                    //movemnt要素の配列チェック
                    if( array_key_exists(10, $value) ){
                        //movemnt要素の配列順＝実行順序へ
                        foreach ($value[10] as $key1 => $value1) {
                            $tmp_ary = array( 0 => $key1+1);
                            $value[10][$key1] = array_merge($tmp_ary, $value[10][$key1]);
                        }
                        //Movemnt要素ss;<XX>型へ変換
                        foreach ($value[10] as $key1 => $value1) {
                            $ret_mov[] = "ss;" . implode( "ss;", $value1 );
                        }
                        $objJSONOfReceptedData[$key][10] =  implode( "", $ret_mov ) ;

                    }else{
                        $objJSONOfReceptedData[$key][10] =  array() ;
                    }

                    break;
                //廃止、復活
                case $strResultType03:
                case $strResultType04:

                    //最終更新日時の配列チェック
                    if( array_key_exists(7, $value) ){
                        $objJSONOfReceptedData[$key][7]=array();
                        $objJSONOfReceptedData[$key][7][0]= array(
                                              "name"  => "UPD_UPDATE_TIMESTAMP",
                                              "value" => $value[7]
                        );
                    }else{
                        $objJSONOfReceptedData[$key][7]=array();
                        $objJSONOfReceptedData[$key][7][0]= array(
                                              "name"  => "UPD_UPDATE_TIMESTAMP",
                                              "value" => ""
                        );
                    }
                    break;
                default:
                    break;

            }
        }


        //X-command毎の処理
        switch ($strCommand) {
            case 'EDIT':
                #登録種別毎の処理
                foreach ($objJSONOfReceptedData as $key => $value) {
                    $tmparrayResult = array();
                    $Process_type = "";
                    $intShmphonyClassId = "";
                    $arrayReceptData = "";
                    $strSortedData = "";
                    $strLT4UBody = "";

                    if( array_key_exists(0, $value) ) $Process_type = $value[0];
                    if( array_key_exists(2, $value) ) $intShmphonyClassId = $value[2];
                    if( array_key_exists(3, $value) ) $arrayReceptData = $value[3];
                    if( array_key_exists(10, $value) ) $strSortedData = $value[10];
                    if( array_key_exists(7, $value) ) $strLT4UBody = $value[7];

                    switch ($Process_type) {
                        //登録
                        case $strResultType01:
                            $tmparrayResult = register_execute($arrayReceptData, $strSortedData);
                            $resultdata_count = result_chk_count($tmparrayResult,$resultdata_count,'register');
                            break;
                        //更新
                        case $strResultType02:
                            $tmparrayResult = update_execute($intShmphonyClassId, $arrayReceptData, $strSortedData, $strLT4UBody);
                            $resultdata_count = result_chk_count($tmparrayResult,$resultdata_count,'update');
                            break;
                        //廃止
                        case $strResultType03:
                            $tmparrayResult = delete_revive_execute(3, $intShmphonyClassId, $strLT4UBody);
                            $resultdata_count = result_chk_count($tmparrayResult,$resultdata_count,'delete');
                            break;
                        //復活
                        case $strResultType04:
                            $tmparrayResult = delete_revive_execute(5, $intShmphonyClassId, $strLT4UBody);
                            $resultdata_count = result_chk_count($tmparrayResult,$resultdata_count,'revive');
                            break;
                        default:
                            $intErrorPlaceMark = 1000;
                            $intResultStatusCode = 400;
                            $aryOverrideForErrorData['Error'] = 'Forbidden';
                            web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            break;
                    }

                    $tmpResult[] = $tmparrayResult;
                }
                break;

            default:
                $intErrorPlaceMark = 1000;
                $intResultStatusCode = 400;
                $aryOverrideForErrorData['Error'] = 'Forbidden';
                web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            break;
        }
        $intResultStatusCode = 200;

        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];

        $resultdata['LIST']['NORMAL'] = $resultdata_count;
        $resultdata['LIST']['RAW'] = Array();
        $resultdata['LIST']['RAW'] = $tmpResult;

        $aryForResultData['resultdata'] = $resultdata;

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
//  (シンフォニークラス編集)クラス登録  //
//////////////////////////////////////////
function register_execute($arrayReceptData, $strSortedData){
    // グローバル変数宣言
    global $g;

    // ローカル変数宣言
    $arrayResult = array();

    require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/72_symphonyClassAdmin.php");
    $arrayResult = symphonyClassRegisterExecute(null, $arrayReceptData, $strSortedData, null);

    // 結果判定
    if($arrayResult[0]=="000"){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
    }else if(intval($arrayResult[0])<500){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
        $arrayResult[3] = str_replace(array(" ", "　"), "",strip_tags($arrayResult[3]));
    }else{
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
        $arrayResult[3] = str_replace(array(" ", "　"), "",strip_tags($arrayResult[3]));
    }
    return $arrayResult;
}

//////////////////////////////////////////
//  (シンフォニークラス編集)クラス更新  //
//////////////////////////////////////////
function update_execute($intShmphonyClassId, $arrayReceptData, $strSortedData, $strLT4UBody){
    // グローバル変数宣言
    global $g;

    // ローカル変数宣言
    $arrayResult = array();

    require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/72_symphonyClassAdmin.php");
    $arrayResult = symphonyClassUpdateExecute($intShmphonyClassId, $arrayReceptData, $strSortedData, $strLT4UBody);

    // 結果判定
    if($arrayResult[0]=="000"){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
    }else if(intval($arrayResult[0])<500){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
        $arrayResult[3] = str_replace(array(" ", "　"), "",strip_tags($arrayResult[3]));
    }else{
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
        $arrayResult[3] =  str_replace(array(" ", "　"), "",strip_tags($arrayResult[3]));
    }
    return $arrayResult;
}

/////////////////////////////////
//  deleteTableファンクション  //
/////////////////////////////////
function delete_revive_execute($mode, $innerSeq, $arrayReceptData){
    // グローバル変数宣言
    global $g;

    // ローカル変数宣言
    $arrayResult = array();
    $aryVariant = array();
    $arySetting = array();

    $arratDeleteData = array();

    $arratDeleteData = convertReceptDataToDataForIUD($arrayReceptData);

    $arySetting = array("Mix1_1","fakeContainer_Delete1","fakeContainer_Delete1");

    // 本体ロジックをコール
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/05_deleteTable.php");
    $arrayResult = deleteTableMain($mode, $innerSeq, $arratDeleteData, null, 3, $aryVariant, $arySetting);
    //$arrayResult[3] = $innerSeq;

    // 結果判定
    if($arrayResult[0]=="000"){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
    }else if(intval($arrayResult[0])<500){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
    }else{
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));

    }
    return $arrayResult;
}

/////////////////////////////////
//  実行状況の(register update delete error)カウント  //
/////////////////////////////////
function result_chk_count($tmparrayResult,$resultdata_count,$Type){
  if($tmparrayResult[0]=="000"){
    $resultdata_count[$Type]['ct']++;
  }else{
    $resultdata_count['error']['ct']++;
  }
  return $resultdata_count;
}


/////////////////////////////////
// FILTER結果へMovement情報の追加  //
/////////////////////////////////
function filter_add($aryForResultData){

    // グローバル変数宣言
    global $g;

    // 本体ロジックをコール
    $objPtnID     = "";
    $objVarID     = "";
    $objChlVarID  = "";
    $objAssSeqID  = "";

    $tmparyForResultData = $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'];

    foreach ($tmparyForResultData as $key => $value) {
        //FILTER結果にのMOVEMENT情報追加
        if ( is_numeric($value[2]) ) {
            $tmpary=array();
            $tmpmovement = select_movment($g['objDBCA'], $g['objMTS'],$value[2], $objPtnID, $objVarID, $objChlVarID, $objAssSeqID);

            foreach ($tmpmovement as $key2 => $value2) {
                $i=0;
                foreach ($value2 as $key3 => $value3) {
                   if ( $key3 == "NEXT_PENDING_FLAG" && $value3 == 1 ) $value3 = "checkedValue";
                   if ( $key3 == "NEXT_PENDING_FLAG" && $value3 == 2 ) $value3 = "";
                   $tmpary[$key2][$i] = $value3;
                   $i++;
                }

            }
            $tmparyForResultData[$key][] = $tmpary;
        }else{
            //FILTER結果にのMOVEMENT情報説明追加
            $tmparyForResultData[$key][] = array(array(
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900100"), //"Orchestrator ID",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900101"), //"Movement ID",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900102"), //"一時停止(OFF:/ON:checkedValue)",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900103"), //"説明",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900104")  //"オペレーションID(個別指定)",
            ));

        }
    }
    $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'] = $tmparyForResultData;

    return $aryForResultData;
}

/////////////////////////////////
// Symphonyクラスに紐づくMovement情報参照  //
/////////////////////////////////
function select_movment($objDBCA, $objMTS, $objPkey, &$objPtnID, &$objVarID, &$objChlVarID, &$objAssSeqID)
{
    // グローバル変数宣言
    global $g;
    $sql =        " SELECT                                      \n " ;
    $sql = $sql . "  ORCHESTRATOR_ID,                           \n " ;
    $sql = $sql . "  PATTERN_ID,                                \n " ;
    $sql = $sql . "  NEXT_PENDING_FLAG,                         \n " ;
    $sql = $sql . "  DESCRIPTION,                               \n " ;
    $sql = $sql . "  OPERATION_NO_IDBH                          \n " ;
    $sql = $sql . " FROM                                        \n " ;
    $sql = $sql . "  C_MOVEMENT_CLASS_MNG                       \n " ;
    $sql = $sql . " WHERE                                       \n " ;
    $sql = $sql . "  SYMPHONY_CLASS_NO = :SYMPHONY_CLASS_NO     \n " ;
    $sql = $sql . " AND                                         \n " ;
    $sql = $sql . "   DISUSE_FLAG = 0                           \n " ;
    $sql = $sql . " ORDER BY                                    \n " ;
    $sql = $sql . "   MOVEMENT_SEQ                              \n " ;

    $objQuery = $objDBCA->sqlPrepare($sql);

    if($objQuery->getStatus()===false){
        web_log($objQuery->getLastError());
        return false;
    }
    $objQuery->sqlBind( array('SYMPHONY_CLASS_NO'=>$objPkey));
    $r = $objQuery->sqlExecute($sql);

    if (!$r){
        web_log($objQuery->getLastError());

        unset($objQuery);
        return false;
    }
    // FETCH行数を取得
    $num_of_rows = $objQuery->effectedRowCount();

    // レコード無しの場合
    if( $num_of_rows < 1 ){
        unset($objQuery);
        return false;
    }
    $row = $objQuery->resultFetchALL();

    return $row;
}


?>
