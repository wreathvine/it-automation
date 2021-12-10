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
global $g;

function symphonyDownloadFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){
    global $g;
    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
  
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
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';

    $aryOverrideForErrorData = array();
    try{
        //X-command毎の処理 
        switch($strCommand){
          case "DOWNLOAD":
            break;
          default:
            $intErrorPlaceMark = 1000;
            $intResultStatusCode = 400;
            $aryOverrideForErrorData['Error'] = 'Forbidden';
            web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $selectTable = array("C_ANSIBLE_LNS_EXE_INS_MNG"
                         ,"C_ANSIBLE_LRL_EXE_INS_MNG"
                         ,"C_ANSIBLE_PNS_EXE_INS_MNG"
                         ,"C_TERRAFORM_EXE_INS_MNG");
        
        $inOrOut = "";
        
        $ACRCM_representative_file_name = "/default/menu/01_browse.php?no=2100000310";
        $objMTS = new MessageTemplateStorage();
        
        $filepath = $root_dir_path.'/temp/data_download/';
        $filename = "";
        
        $legacyDir = "";
        $roleDir =  "";
        $pioneerDir = "";
        $terraformDir = "";
        
        require_once($g['root_dir_path']. "/libs/commonlibs/common_php_functions.php");
        require_once($g['root_dir_path']. "/libs/commonlibs/common_php_classes.php");
        
        $tmparrayResult = array();
        $tmparrayFileResult = array();
        $intShmphonyClassId = "";
        $intShmphonyClassId10 = "";
        
        $dataCnt = 0;
        
        //パラメータなしの場合は全件取得
        if(empty($objJSONOfReceptedData)){
          $sql = "SELECT SYMPHONY_INSTANCE_NO
                  FROM C_SYMPHONY_INSTANCE_MNG
                  WHERE DISUSE_FLAG = '0' ";
          
          //Bindしたいものがあれば配列に入れる。
          $tmpAryBind = array();

          //関数呼び出し
          $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
          
          $objJSONOfReceptedData = array('SYMPHONY_INSTANCE_NO' => array());
          //返り値チェック
          if($retArray[0] === true){
              //$listに結果を入れる場合
              $objQuery =& $retArray[1];
              while($row = $objQuery->resultFetch() ){
                  array_push($objJSONOfReceptedData['SYMPHONY_INSTANCE_NO'], $row['SYMPHONY_INSTANCE_NO']);
              }
          }
        }
        
        if(array_key_exists('SYMPHONY_INSTANCE_NO',$objJSONOfReceptedData)){
          foreach ($objJSONOfReceptedData['SYMPHONY_INSTANCE_NO'] as $key => $value) {
            $intShmphonyClassId = $value;
            $intShmphonyClassId10 = str_pad($value,10,0,STR_PAD_LEFT);
            $tmparrayResult[$dataCnt]['SYMPHONY_INSTANCE_NO'] = $intShmphonyClassId;
            
            $filename = "InputData_Symphony_".$intShmphonyClassId10.".zip";
            $inOrOut = "FILE_INPUT";
            $tmparrayResult[$dataCnt]['INPUT_DATA'] = $filename;
            
            $legacyDir = $root_dir_path."/uploadfiles/2100020113/".$inOrOut."/";
            $roleDir =  $root_dir_path."/uploadfiles/2100020314/".$inOrOut."/";
            $pioneerDir = $root_dir_path."/uploadfiles/2100020213/".$inOrOut."/";
            $terraformDir = $root_dir_path."/uploadfiles/2100080011/".$inOrOut."/";
            
            $zip = new ZipArchive();
            
            // Zipファイルオープン
            $zip->open($filepath.$filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
  
            // 処理制限時間を外す
            set_time_limit(0);
  
            foreach($selectTable as $tableName){
              $list = [];
  
              $sql = "SELECT EXECUTION_NO, {$inOrOut} ,PATTERN_ID
                      FROM {$tableName}
                      WHERE SYMPHONY_INSTANCE_NO = {$intShmphonyClassId} ";
              
              //Bindしたいものがあれば配列に入れる。
              $tmpAryBind = array();
  
              //関数呼び出し
              $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
              //返り値チェック
              if($retArray[0] === true){
                  //$listに結果を入れる場合
                  $list = array();
                  $objQuery =& $retArray[1];
                  while($row = $objQuery->resultFetch() ){
                      array_push($list, $row);
                  }
              }
  
              if(!empty($list)){
                  switch($tableName){
                      case "C_ANSIBLE_LNS_EXE_INS_MNG":
                          $callDir = $legacyDir;
                      break;
                      case "C_ANSIBLE_LRL_EXE_INS_MNG":
                          $callDir = $roleDir;
                      break;
                      case "C_ANSIBLE_PNS_EXE_INS_MNG":
                          $callDir = $pioneerDir;
                      break;
                      case "C_TERRAFORM_EXE_INS_MNG":
                          $callDir = $terraformDir;
                      break;
                  }
                  foreach($list as $dllist){
                      $selectZip = $callDir.str_pad($dllist['EXECUTION_NO'],10,0,STR_PAD_LEFT)."/".$dllist[$inOrOut];
                      if(!file_exists($selectZip)) continue;
                      $empDir = str_pad($dllist['PATTERN_ID'],10,0,STR_PAD_LEFT);
                      $zip->addEmptyDir($empDir);
                      $zip->addFile($selectZip,$empDir."/".$dllist[$inOrOut]);
                  }
              }
            }
  
            $zip->close();
  
            $res = $zip->open($filepath.$filename, ZipArchive::CHECKCONS);
            
            if($res !== true || !file_exists($filepath.$filename)){
              $tmparrayResult[$dataCnt]['INPUT_DATA'] = '';
              $tmparrayFileResult[$dataCnt] = array();
            }else{
              $tmparrayFileResult[$dataCnt][$filename] = base64_encode(file_get_contents($filepath.$filename));
              $zip->close();
            }
            
            $filename = "ResultData_Symphony_".$intShmphonyClassId10.".zip";
            $inOrOut = "FILE_RESULT";
            $tmparrayResult[$dataCnt]['RESULT_DATA'] = $filename;
            
            $legacyDir = $root_dir_path."/uploadfiles/2100020113/".$inOrOut."/";
            $roleDir =  $root_dir_path."/uploadfiles/2100020314/".$inOrOut."/";
            $pioneerDir = $root_dir_path."/uploadfiles/2100020213/".$inOrOut."/";
            $terraformDir = $root_dir_path."/uploadfiles/2100080011/".$inOrOut."/";
            
            // Zipファイルオープン
            $zip->open($filepath.$filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
  
            // 処理制限時間を外す
            set_time_limit(0);
  
            foreach($selectTable as $tableName){
              $list = [];
  
              $sql = "SELECT EXECUTION_NO, {$inOrOut} ,PATTERN_ID
                      FROM {$tableName}
                      WHERE SYMPHONY_INSTANCE_NO = {$intShmphonyClassId} ";
              
              //Bindしたいものがあれば配列に入れる。
              $tmpAryBind = array();
  
              //関数呼び出し
              $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
              //返り値チェック
              if($retArray[0] === true){
                  //$listに結果を入れる場合
                  $list = array();
                  $objQuery =& $retArray[1];
                  while($row = $objQuery->resultFetch() ){
                      array_push($list, $row);
                  }
              }
  
              if(!empty($list)){
                  switch($tableName){
                      case "C_ANSIBLE_LNS_EXE_INS_MNG":
                          $callDir = $legacyDir;
                      break;
                      case "C_ANSIBLE_LRL_EXE_INS_MNG":
                          $callDir = $roleDir;
                      break;
                      case "C_ANSIBLE_PNS_EXE_INS_MNG":
                          $callDir = $pioneerDir;
                      break;
                      case "C_TERRAFORM_EXE_INS_MNG":
                          $callDir = $terraformDir;
                      break;
                  }
                  foreach($list as $dllist){
                      $selectZip = $callDir.str_pad($dllist['EXECUTION_NO'],10,0,STR_PAD_LEFT)."/".$dllist[$inOrOut];
                      if(!file_exists($selectZip)) continue;
                      $empDir = str_pad($dllist['PATTERN_ID'],10,0,STR_PAD_LEFT);
                      $zip->addEmptyDir($empDir);
                      $zip->addFile($selectZip,$empDir."/".$dllist[$inOrOut]);
                  }
              }
            }
  
            $zip->close();
  
            $res = $zip->open($filepath.$filename, ZipArchive::CHECKCONS);
            
            if($res !== true || !file_exists($filepath.$filename)){
              $tmparrayResult[$dataCnt]['RESULT_DATA'] = '';
              $tmparrayFileResult[$dataCnt] = array();
            }else{
              $tmparrayFileResult[$dataCnt][$filename] = base64_encode(file_get_contents($filepath.$filename));
              $zip->close();
            }
            $dataCnt++;
          }
        }
        
        

        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
        $aryForResultData['resultdata'] = array('CONTENTS'=>array('RECORD_LENGTH'=>$dataCnt++,
                                                              'BODY'=>$tmparrayResult,
                                                              'DOWNLOAD_FILE'=>$tmparrayFileResult,
                                                             )
                                           );

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
                          'ResultData'=>$aryForResultData);;
    return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
}

?>
