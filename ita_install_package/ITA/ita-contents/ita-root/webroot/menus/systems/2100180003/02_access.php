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
    //  【処理概要】
    //    ・Symphonyクラスを定義するページの、各種動的機能を呼び出す
    //
    //////////////////////////////////////////////////////////////////////

    global $g;
    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    $g['requestByHA'] = 'forHADAC'; //[H]tml-[A]AX.[D]b_[A]ccess_[C]ore

    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // access系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_access_01.php");
        
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    ky_include_path_add(getApplicationRootDirPath()."/confs/webconfs/path_HTML_AJAX.txt", 1);
    require_once 'HTML/AJAX/Server.php';

    class Db_Access_Core {
        //-- サイト個別PHP要素、ここから--

        //////////////////////////////////////////////////////////////////
        //  (Conductorクラス編集,作業実行,作業確認)フローの読み込み  //
        //////////////////////////////////////////////////////////////////
        
        // ポリシー1:SQL関数（makeSQLForUtnTableUpdate）は、SELECTのみのプロセスでは使わない
        function printSymphonyClass($intShmphonyClassId, $mode){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
     
            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/72_symphonyClassAdmin.php");
            $arrayResult = printOneOfSymphonyClasses($intShmphonyClassId, $mode);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
            //CONDUCTOR_ins_noごとに作業パターンの流れを収集する----
        }
        //////////////////////////////////////////////
        //  (Conductorクラス編集)素材の読み込み  //
        //////////////////////////////////////////////
        function printMatchedPatternList($filterData){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");
            $arrayResult = printPatternListForEdit($filterData);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
            //オーケストレータ—ごとに作業パターンを収集する----
        }


        ////////////////////////////////
        //  オペレーション一覧の表示  //
        ////////////////////////////////

        function printOperationList(){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            #require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_operation_info.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printOperationListInfoRegConductor();
            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }

        ////////////////////////////////
        //  Conductor一覧の表示  //
        ////////////////////////////////

        function printConductorList(){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            #require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_operation_info.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printConductorListInfoRegConductor();

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }

        //////////////////////////////////////////////
        //  (Conductorクラス編集)JSON形式の取得  //
        //////////////////////////////////////////////

        function printconductorClass($intConductorClassId){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            #require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_operation_info.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printConductorInfoRegConductor($intConductorClassId);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }

        //////////////////////////////////////////////
        //   Movement一覧の表示  //
        //////////////////////////////////////////////
        function printMatchedPatternListJson($filterData=""){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");
            $arrayResult = printPatternListForEditJSON($filterData);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
            //オーケストレータ—ごとに作業パターンを収集する----
        }

        //////////////////////////////////////////
        //  (Conductorクラス編集)クラス登録  //
        //////////////////////////////////////////
        function register_execute($arrayReceptData){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();

            //整形
            $tmpReceptData = json_decode($arrayReceptData,true);
            $arrayReceptData=$tmpReceptData['conductor'];
            $strSortedData=$tmpReceptData;
            unset($strSortedData['conductor']);
            foreach ($strSortedData as $key => $value) {
                if( preg_match('/line-/',$key) ){
                    unset($strSortedData[$key]);
                }
            }
            unset($strSortedData['conductor']);
            unset($strSortedData['config']);

            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");
            $arrayResult = conductorClassRegisterExecute(null, $arrayReceptData, $strSortedData, null,1);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            #return makeAjaxProxyResultStream($arrayResult);
            return $arrayResult;
        }
        //////////////////////////////////////////
        //  (Conductorクラス編集)クラス更新  //
        //////////////////////////////////////////
        function update_execute($intShmphonyClassId, $arrayReceptData, $strLT4UBody){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            //整形
            $tmpReceptData = json_decode($arrayReceptData,true);
            $arrayReceptData=$tmpReceptData['conductor'];
            $strSortedData=$tmpReceptData;
            unset($strSortedData['conductor']);
            foreach ($strSortedData as $key => $value) {
                if( preg_match('/line-/',$key) ){
                    unset($strSortedData[$key]);
                }
            }
            unset($strSortedData['conductor']);
            unset($strSortedData['config']);

            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");

            $arrayResult = conductorClassRegisterExecute($intShmphonyClassId, $arrayReceptData, $strSortedData, $strLT4UBody,1);

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
        ////////////////////////////////
        //  Symphony一覧の表示  //
        ////////////////////////////////

        function printSymphonyList(){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            #require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_operation_info.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printConductorListInfoRegSymphony();

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }

        ////////////////////////////////
        //  通知一覧の表示  //
        ////////////////////////////////

        function printNoticeList( $conductorClassID="", $mode="" ){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();

            if( gettype($conductorClassID) == "NULL" )$conductorClassID = "";


            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printNoticeListInfoRegConductor( $conductorClassID , $mode );

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }

        ////////////////////////////////
        //  ステータスの表示  //
        ////////////////////////////////

        function printNoticeStatusList(){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printNoticeStatusListInfoRegConductor();
            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }

    }


    class Db_Access extends Db_Access_Core {
    
    }
    
    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);
    $server->handleRequest();

?>
