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
//////// ----コールバックファンクション ////////
function callback() {}
callback.prototype = {
    Filter1Tbl_add_selectbox : function( result ){
        var filterAreaWrap = 'Filter1_Nakami';
        var strFilterPrintId = 'Filter1Tbl';
        var containerClassName = 'fakeContainer_Filter1Setting';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultSetTargetSeq = ary_result[2];
        var resultContentTag = ary_result[3];

        var objHtmlSetArea = $('#'+filterAreaWrap+' .'+resultSetTargetSeq).get()[0];

        if( objHtmlSetArea === null ){
            htmlSetExcute = false;
        }else{
            if( ary_result[0] != "000" ){
                htmlSetExcute = false;
                errMsgBody = ary_result[2];
            }
        }

        if( htmlSetExcute == true ){
            //----生成されたセレクトタグ、を埋め込み
            $(objHtmlSetArea).html(resultContentTag);
            //生成されたセレクトタグ、を埋め込み----

            addPullDownBox(filterAreaWrap, strFilterPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Filter1Tbl_reload : function( result ){
        var filterAreaWrap = 'Filter1_Nakami';
        var strFilterPrintId = 'Filter1Tbl';

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objTableArea=$('#'+filterAreaWrap+' .table_area').get()[0];

        if( objTableArea === null){
            htmlSetExcute = false;
        }else{
            if( ary_result[0] != "000" ){
                htmlSetExcute = false;
                errMsgBody = ary_result[2];
            }
        }

        if( htmlSetExcute == true ){
            objTableArea.innerHTML = resultContentTag;

            show('Filter1_Midashi','Filter1_Nakami')
            adjustTableAuto (strFilterPrintId,
                   "sDefault",
                   "fakeContainer_Filter1Setting",
                   webStdTableHeight,
                   webStdTableWidth );
            linkDateInputHelper(filterAreaWrap);
            show('Filter1_Midashi','Filter1_Nakami')

            if(ary_result[3]==1){
                Filter1Tbl_reset_filter(true);
            }
            if( varInitedFlag1===false ){
                varInitedFlag1 = true;
                if(initialFilter == 1){
                    Filter1Tbl_search_async();
                }
            }
            
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Filter1Tbl_recCount : function(result){
        var strMixOuterFrameName = 'Mix1_Nakami';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];
        objAlertArea.style.display = "none";

        if( ary_result[0] == "000" ){
            if( ckRangeOfAlert(ary_result[2], webPrintRowLimit) ){
                window.alert(getSomeMessage("ITAWDCC90103",{0:webPrintRowLimit,1:ary_result[2]}));
                // Web表を表示しない
                Filter1Tbl_print_async(0);
            }else{
                if( ckRangeOfConfirm(ary_result[2] , webPrintRowConfirm, webPrintRowLimit) ){
                    if( window.confirm( getSomeMessage("ITAWDCC20201",{0:ary_result[2]})) ){
                        // Web表を表示する
                        Filter1Tbl_print_async(1);
                    }else{
                        // Web表を表示しない
                        Filter1Tbl_print_async(0);
                    }
                }else{
                    // Web表を表示する
                    Filter1Tbl_print_async(1);
                }
            }
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = ary_result[2];
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Filter1Tbl_printTable : function(result){
        var strMixOuterFrameName = 'Mix1_Nakami';
        var strMixInnerFramePrefix = 'Mix1_';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];
        objAlertArea.style.display = "none";

        var objPrintArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

        if( ary_result[0] == "000" ){

            objPrintArea.innerHTML = resultContentTag;

            adjustTableAuto (strMixInnerFramePrefix+'1',
                            "sDefault",
                            "fakeContainer_Filter1Print",
                            webStdTableHeight,
                            webStdTableWidth );
            adjustTableAuto (strMixInnerFramePrefix+'2',
                            "sDefault",
                            "fakeContainer_ND_Filter1Sub",
                            webStdTableHeight,
                            webStdTableWidth );
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = ary_result[2];
            objAlertArea.style.display = "block";
            objPrintArea.innerHTML = "";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    
    Filter2Tbl_add_selectbox : function( result ){
        var filterAreaWrap = 'Filter2_Nakami';
        var strFilterPrintId = 'Filter2Tbl';
        var containerClassName = 'fakeContainer_Filter2Setting';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);
        
        var resultSetTargetSeq = ary_result[2];
        var resultContentTag = ary_result[3];

        var objHtmlSetArea = $('#'+filterAreaWrap+' .'+resultSetTargetSeq).get()[0];

        if( objHtmlSetArea === null ){
            htmlSetExcute = false;
        }else{
            if( ary_result[0] != "000" ){
                htmlSetExcute = false;
                errMsgBody = ary_result[2];
            }
        }

        if( htmlSetExcute == true ){
            //----生成されたセレクトタグ、を埋め込み
            $(objHtmlSetArea).html(resultContentTag);
            //生成されたセレクトタグ、を埋め込み----

            //----生成されたセレクトタグ、を埋め込み
            $(objHtmlSetArea).html(resultContentTag);
            //生成されたセレクトタグ、を埋め込み----

            addPullDownBox(filterAreaWrap, strFilterPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Filter2Tbl_reload : function( result ){
        var filterAreaWrap = 'Filter2_Nakami';
        var strFilterPrintId = 'Filter2Tbl';

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objTableArea=$('#'+filterAreaWrap+' .table_area').get()[0];

        if( objTableArea === null){
            htmlSetExcute = false;
        }else{
            if( ary_result[0] != "000" ){
                htmlSetExcute = false;
                errMsgBody = ary_result[2];
            }
        }

        if( htmlSetExcute == true ){
            objTableArea.innerHTML = resultContentTag;

            show('Filter2_Midashi','Filter2_Nakami')
            adjustTableAuto (strFilterPrintId,
                   "sDefault",
                   "fakeContainer_Filter2Setting",
                   webStdTableHeight,
                   webStdTableWidth );

            show('Filter2_Midashi','Filter2_Nakami')
            linkDateInputHelper(filterAreaWrap);

            if(ary_result[3]==1){
                Filter2Tbl_reset_filter(true);
            }
            
            if( varInitedFlag2===false ){
                varInitedFlag2 = true;
                if(initialFilter == 1){
                    Filter2Tbl_search_async();
                }
            }
            
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Filter2Tbl_recCount : function(result){
        var strMixOuterFrameName = 'Mix2_Nakami';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];
        objAlertArea.style.display = "none";

        if( ary_result[0] == "000" ){
            if( ckRangeOfAlert(ary_result[2], webPrintRowLimit) ){
                window.alert(getSomeMessage("ITAWDCC90103",{0:webPrintRowLimit,1:ary_result[2]}));
                // Web表を表示しない
                Filter2Tbl_print_async(0);
            }else{
                if( ckRangeOfConfirm(ary_result[2] , webPrintRowConfirm, webPrintRowLimit) ){
                    if( window.confirm( getSomeMessage("ITAWDCC20201",{0:ary_result[2]})) ){
                        // Web表を表示する
                        Filter2Tbl_print_async(1);
                    }else{
                        // Web表を表示しない
                        Filter2Tbl_print_async(0);
                    }
                }else{
                    // Web表を表示する
                    Filter2Tbl_print_async(1);
                }
            }
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = ary_result[2];
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Filter2Tbl_printTable : function(result){
        var strMixOuterFrameName = 'Mix2_Nakami';
        var strMixInnerFramePrefix = 'Mix2_';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];
        objAlertArea.style.display = "none";

        var objPrintArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

        if( ary_result[0] == "000" ){

            objPrintArea.innerHTML = resultContentTag;

            adjustTableAuto (strMixInnerFramePrefix+'1',
                            "sDefault",
                            "fakeContainer_Filter2Print",
                            webStdTableHeight,
                            webStdTableWidth );
            adjustTableAuto (strMixInnerFramePrefix+'2',
                            "sDefault",
                            "fakeContainer_ND_Filter2Sub",
                            webStdTableHeight,
                            webStdTableWidth );
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = ary_result[2];
            objAlertArea.style.display = "block";
            objPrintArea.innerHTML = "";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //----Symphony系メソッド
    printSymphonyClass : function( result ){

        var strAlertAreaName = 'symphony_message';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            printSymphonyClass(false,ary_result[2],ary_result[3],ary_result[4],ary_result[5]);

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[6];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[6];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    printOperationInfo : function( result ){

        var strAlertAreaName = 'symphony_message';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            var aryOpeInfo = getArrayBySafeSeparator(ary_result[3]);

            printOperationInfo(false,ary_result[2],aryOpeInfo[0],aryOpeInfo[1]);

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[4];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[4];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    symphonyExecute : function( result ){

        var strAlertAreaName = 'symphony_message';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            symphonyExecute(false, ary_result[2]);

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    }
    //Symphony系メソッド----
    ,
    //----Conductor系メソッド
    //----Conductor再描画用-----//
    printconductorClass : function( result ){

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        conductorUseList.conductorData = result;
        if ( conductorGetMode === 'starting') {
          initEditor('view');
        } else {
          $( window ).trigger('conductorReset');
        }
    },
    //----Movementリスト用-----//
    printMatchedPatternListJson : function( result ){
        conductorUseList.movementList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
          proxy.printConductorList();
        }
    },
    //----個別オペレーションリスト用-----//
    printOperationList : function( result ){
        conductorUseList.operationList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
          proxy.printMatchedPatternListJson();
        }
    },
    //----Callリスト用-----//
    printConductorList : function( result ){
        conductorUseList.conductorCallList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
            proxy.printSymphonyList();
        }
    },
    //---- Symphony Callリスト用-----//
    printSymphonyList : function( result ){
        conductorUseList.symphonyCallList = JSON.parse( result );
        // Editor起動時
        if ( conductorGetMode === 'starting') {
            conductorUseList.conductorData = null;
            initEditor('execute');
        }
    },
    //----Conductor登録----//
    conductorExecute : function( result ){
            
      var ary_result = getArrayBySafeSeparator(result);
      checkTypicalFlagInHADACResult(ary_result);

      var logType = '',
          message = '',
          trigger = '';
      switch( result[0] ) {
        case '000': // Done
          // 実行に成功したら作業確認に移動する
          var conductor_instance_id = result[2];
          if ( conductor_instance_id !== undefined && conductor_instance_id !== '') {
            var url = '/default/menu/01_browse.php?no=2100180005&conductor_instance_id=' + conductor_instance_id;
            location.href=url;
            return;
          } else {
            logType = 'error';
            message = 'conductor_instance_id error.';
            trigger = 'conductorError';
          }
          break;
        case '002': // Error          
        case '003': // ???
          logType = 'error';
          message += result[3];
          trigger = 'conductorError';
          break;
        default: //System Error
          logType = 'error';
          message += getSomeMessage("ITAWDCC90101");
          trigger = 'conductorSystemError';
          break;      
      }
      // ログエリアにメッセージ表示
      editor.log.set( logType, message );
      // ボタン活性化
      conductorFooterButtonDisabled( false );
      // イベントトリガー
      $( window ).trigger( trigger );
    },
    
    // ---- Notice ----- //
    printNoticeList : function( result ) {
      conductorUseList.noticeList = JSON.parse( result );
      if ( conductorGetMode === 'starting') {
        proxy.printNoticeStatusList();
      }
    },
    printNoticeStatusList : function( result ) {
      conductorUseList.noticeStatusList = JSON.parse( result );
      if ( conductorGetMode === 'starting') {
        proxy.printOperationList();
      }
    }
    // Notice ----

}

//////// ----汎用系ファンクション ////////
function setInputButtonDisable(rangeId,targetClass,toValue){
    if(toValue === true){
        $('#'+rangeId+' .'+targetClass).attr("disabled",true);
    }else{
        $('#'+rangeId+' .'+targetClass).removeAttr("disabled");
    }
}
//////// 汎用系ファンクション---- ////////

//////// テーブルレイアウト設定 ////////
var msgTmpl = {};
//////// 画面生成時に初回実行する処理 ////////

var webPrintRowLimit;
var webPrintRowConfirm;

var webStdTableWidth;
var webStdTableHeight;

var varInitedFlag1;
var varInitedFlag2;
var initialFilterEl;
var initialFilter;

var proxy = new Db_Access(new callback());

window.onload = function(){
    initialFilterEl = document.getElementById('sysInitialFilter');
    if(initialFilterEl == null){
        initialFilter = 2;
    }
    else{
        initialFilter = initialFilterEl.innerHTML;
    }
    initProcess('instanceConstruct');
    show('SetsumeiMidashi','SetsumeiNakami');
}


//////// ----セレクトタグ追加ファンクション ////////
function Filter1Tbl_add_selectbox( show_seq ){
    proxy.Filter1Tbl_add_selectbox(show_seq);
}
//////// セレクトタグ追加ファンクション---- ////////

//////// ----表示フィルタリセット用ファンクション ////////
function Filter1Tbl_reset_filter(boolBack){
    // 検索条件をクリア(リセット)
    var filterAreaWrap = 'Filter1_Nakami';
    var strMixOuterFrameName = 'Mix1_Nakami';
    if( boolBack===true ){
        var objHyoujiFlag = $('#'+strMixOuterFrameName+' .hyouji_flag').get()[0];
        if( objHyoujiFlag != null ){
            // すでに一覧が表示されている場合（オートフィルタがonの場合、一覧を最新化する）
            var objFCSL = $('#'+filterAreaWrap+' .filter_ctl_start_limit').get()[0];
            if( objFCSL == null){
            }else{
                if( objFCSL.value == 'on' && objFCSL.checked == true ){
                    // タグが存在し、オートフィルタにチェックが入っている
                    //----再表示しますか？
                    if( window.confirm( getSomeMessage("ITAWDCC20204")) ){
                        Filter1Tbl_search_async(1);
                    }
                }
            }
        }
    }
    else{
        proxy.Filter1Tbl_reload(1);
    }
}
//////// 表示フィルタリセット用ファンクション---- ////////

//////// ----search_asyncを呼ぶかどうか判断するファンクション ////////
function Filter1Tbl_pre_search_async(inputedCode){

    // ----Enterキーが押された場合
    if( inputedCode == 13 ){
        Filter1Tbl_search_async('keyInput13');
    }
    // Enterキーが押された場合----
}
//////// search_asyncを呼ぶかどうか判断するファンクション---- ////////

//////// ----フィルタ結果表示呼出ファンクション[1] ////////
function Filter1Tbl_search_async( value1 ){

    var filterAreaWrap = 'Filter1_Nakami';
    var printAreaWrap = 'Mix1_Nakami';
    var printAreaHead = 'Mix1_Midashi';

    var exec_flag = true;

    // 引数を準備
    var filter_data = $("#"+filterAreaWrap+" :input").serializeArray();

    exec_flag = Filter1Tbl_search_control(exec_flag, value1);
    var objUpdTag = $('#'+printAreaWrap+' .editing_flag').get()[0];
    if ( objUpdTag != null ){
        // 更新系(更新/廃止/復活)モード中の場合はSELECTモードに戻っていいか尋ねる
        if( exec_flag == true ){
            //----メンテナンス中ですが中断してよろしいですか？
            if( !window.confirm( getSomeMessage("ITAWDCC20203") ) ){
                exec_flag = false;
            }
        }
    }

    if( exec_flag ){
        // 更新時アラート出力エリアをブランクにしたうえ非表示にする
        var objAlertArea=$('#'+printAreaWrap+' .alert_area').get()[0];
        objAlertArea.innerHTML = "";
        objAlertArea.style.display = "none";

        // テーブル表示用領域を一旦クリアする
        var objTableArea=$('#'+printAreaWrap+' .table_area').get()[0];
        //----※ここに一覧が表示されます。
        objTableArea.innerHTML = "";

        // テーブル表示用領域を開く
        if( checkOpenNow(printAreaWrap)===false ){
            show(printAreaHead, printAreaWrap);
        }

        // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
        restruct_for_IE();

        // proxy.Filter1Tbl_recCount実行
        proxy.Filter1Tbl_recCount(filter_data);
    }
}
//////// フィルタ結果表示呼出ファンクション[1]---- ////////

//////// ----フィルタ結果表示呼出ファンクション[2] ////////
function Filter1Tbl_search_control( exec_flag_var, value1 ){
    var filterAreaWrap = 'Filter1_Nakami';

    var exec_flag_ret = true;

    if( typeof(value1) == 'undefined' ){
        exec_flag_ret = exec_flag_var;
    }else{
        if( exec_flag_var == false ){
            exec_flag_ret = false;
        }else{
            var objFCSL = $('#'+filterAreaWrap+' .filter_ctl_start_limit').get()[0];

            if(objFCSL == null){
                // 自動開始制御タグがない場合は、システムエラー扱い、とする。
                // システムエラーが発生しました。
                alert( getSomeMessage("ITAWDCC20205") );
                exec_flag_ret = false;
            }else{
                if( objFCSL.value == 'on' ){
                    // 自動開始制御タグが存在し、オートフィルタ開始の抑制が働いている可能性がある
                    exec_flag_ret = false;
                    if( value1 == 'orderFromFilterCmdBtn' ){
                        // フィルタボタンが押された場合、条件「なし」で開始----
                        exec_flag_ret = true;
                    }else if( value1 == 'idcolumn_filter_default' || value1 == 'keyInput13' ){
                        if( objFCSL.checked == true ){
                            // 自動開始制御タグが存在し、オートフィルタにチェックが入っている
                            exec_flag_ret = true;
                        }
                    }else{
                        exec_flag_ret = true;
                    }
                }
            }
        }
    }
    return exec_flag_ret;
}
//////// フィルタ結果表示呼出ファンクション[2]---- ////////

//////// ----検索条件指定用ファンクション ////////
function Filter1Tbl_print_async( intPrintMode ){

    var filterAreaWrap = 'Filter1_Nakami';
    var printAreaWrap = 'Mix1_Nakami';
    var printAreaHead = 'Mix1_Midashi';

    var filter_data=$('#'+filterAreaWrap+' :input').serializeArray();

    // テーブル表示用領域を開く
    if( checkOpenNow(printAreaWrap)===false ){
        show(printAreaHead, printAreaWrap);
    }

    // しばらくお待ち下さいを出す
    var objTableArea = $('#'+printAreaWrap+' .table_area').get()[0];
    objTableArea.innerHTML = "<div class=\"wait_msg\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
    restruct_for_IE();

    // proxy.Filter1Tbl_printTable実行
    proxy.Filter1Tbl_printTable(intPrintMode, filter_data);
}
//////// 検索条件指定用ファンクション---- ////////



//////// ----セレクトタグ追加ファンクション ////////
function Filter2Tbl_add_selectbox( show_seq ){
    proxy.Filter2Tbl_add_selectbox(show_seq);
}
//////// セレクトタグ追加ファンクション---- ////////

//////// ----表示フィルタリセット用ファンクション ////////
function Filter2Tbl_reset_filter(boolBack){
    // 検索条件をクリア(リセット)
    var filterAreaWrap = 'Filter2_Nakami';
    var strMixOuterFrameName = 'Mix2_Nakami';
    if( boolBack===true ){
        var objHyoujiFlag = $('#'+strMixOuterFrameName+' .hyouji_flag').get()[0];
        if( objHyoujiFlag != null ){
            // すでに一覧が表示されている場合（オートフィルタがonの場合、一覧を最新化する）
            var objFCSL = $('#'+filterAreaWrap+' .filter_ctl_start_limit').get()[0];
            if( objFCSL == null){
            }else{
                if( objFCSL.value == 'on' && objFCSL.checked == true ){
                    // タグが存在し、オートフィルタにチェックが入っている
                    //----再表示しますか？
                    if( window.confirm( getSomeMessage("ITAWDCC20204")) ){
                        Filter2Tbl_search_async(1);
                    }
                }
            }
        }
    }
    else{
        proxy.Filter2Tbl_reload(1);
    }
}
//////// 表示フィルタリセット用ファンクション---- ////////

//////// ----search_asyncを呼ぶかどうか判断するファンクション ////////
function Filter2Tbl_pre_search_async(inputedCode){

    // ----Enterキーが押された場合
    if( inputedCode == 13 ){
        Filter2Tbl_search_async('keyInput13');
    }
    // Enterキーが押された場合----
}
//////// search_asyncを呼ぶかどうか判断するファンクション---- ////////

//////// ----フィルタ結果表示呼出ファンクション[1] ////////
function Filter2Tbl_search_async( value1 ){

    var filterAreaWrap = 'Filter2_Nakami';
    var printAreaWrap = 'Mix2_Nakami';
    var printAreaHead = 'Mix2_Midashi';

    var exec_flag = true;

    // 引数を準備
    var filter_data = $("#"+filterAreaWrap+" :input").serializeArray();

    exec_flag = Filter2Tbl_search_control(exec_flag, value1);
    var objUpdTag = $('#'+printAreaWrap+' .editing_flag').get()[0];
    if ( objUpdTag != null ){
        // 更新系(更新/廃止/復活)モード中の場合はSELECTモードに戻っていいか尋ねる
        if( exec_flag == true ){
            //----メンテナンス中ですが中断してよろしいですか？
            if( !window.confirm( getSomeMessage("ITAWDCC20203") ) ){
                exec_flag = false;
            }
        }
    }

    if( exec_flag ){
        // 更新時アラート出力エリアをブランクにしたうえ非表示にする
        var objAlertArea=$('#'+printAreaWrap+' .alert_area').get()[0];
        objAlertArea.innerHTML = "";
        objAlertArea.style.display = "none";

        // テーブル表示用領域を一旦クリアする
        var objTableArea=$('#'+printAreaWrap+' .table_area').get()[0];
        //----※ここに一覧が表示されます。
        objTableArea.innerHTML = "";

        // テーブル表示用領域を開く
        if( checkOpenNow(printAreaWrap)===false ){
            show(printAreaHead, printAreaWrap);
        }

        // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
        restruct_for_IE();

        // proxy.Filter2Tbl_recCount実行
        proxy.Filter2Tbl_recCount(filter_data);
    }
}
//////// フィルタ結果表示呼出ファンクション[1]---- ////////

//////// ----フィルタ結果表示呼出ファンクション[2] ////////
function Filter2Tbl_search_control( exec_flag_var, value1 ){
    var filterAreaWrap = 'Filter2_Nakami';

    var exec_flag_ret = true;

    if( typeof(value1) == 'undefined' ){
        // value1がundefined型の場合
        exec_flag_ret = exec_flag_var;
    }else{
        if( exec_flag_var == false ){
            exec_flag_ret = false;
        }else{
            var objFCSL = $('#'+filterAreaWrap+' .filter_ctl_start_limit').get()[0];

            if(objFCSL == null){
                //----RedMineチケット1011
                // 自動開始制御タグがない場合は、システムエラー扱い、とする。
                // システムエラーが発生しました。
                alert( getSomeMessage("ITAWDCC20205") );
                exec_flag_ret = false;
            }else{
                if( objFCSL.value == 'on' ){
                    // 自動開始制御タグが存在し、オートフィルタ開始の抑制が働いている可能性がある
                    exec_flag_ret = false;
                    if( value1 == 'orderFromFilterCmdBtn' ){
                        // フィルタボタンが押された場合、条件「なし」で開始----
                        exec_flag_ret = true;
                    }else if( value1 == 'idcolumn_filter_default' || value1 == 'keyInput13' ){
                        if( objFCSL.checked == true ){
                            // 自動開始制御タグが存在し、オートフィルタにチェックが入っている
                            exec_flag_ret = true;
                        }
                    }else{
                        exec_flag_ret = true;
                    }
                }
            }
        }
    }
    return exec_flag_ret;
}
//////// フィルタ結果表示呼出ファンクション[2]---- ////////

//////// ----検索条件指定用ファンクション ////////
function Filter2Tbl_print_async( intPrintMode ){

    var filterAreaWrap = 'Filter2_Nakami';
    var printAreaWrap = 'Mix2_Nakami';
    var printAreaHead = 'Mix2_Midashi';

    var filter_data=$('#'+filterAreaWrap+' :input').serializeArray();

    // テーブル表示用領域を開く
    if( checkOpenNow(printAreaWrap)===false ){
        show(printAreaHead, printAreaWrap);
    }

    // しばらくお待ち下さいを出す
    var objTableArea = $('#'+printAreaWrap+' .table_area').get()[0];
    objTableArea.innerHTML = "<div class=\"wait_msg\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
    restruct_for_IE();

    // proxy.Filter2Tbl_printTable実行
    proxy.Filter2Tbl_printTable(intPrintMode, filter_data);
}
//////// 検索条件指定用ファンクション---- ////////


//---- ここからカスタマイズした場合の一般メソッド配置域
function symphonyLoadForExecute(conductor_class_id){
    proxy.printNoticeList( conductor_class_id );
    proxy.printconductorClass( conductor_class_id );
}

function operationLoadForExecute(operation_no){
    var operationID = $('#Mix2_Nakami #cell_print_table_' + operation_no + '_2').text(),
        operationName = $('#Mix2_Nakami #cell_print_table_' + operation_no + '_3').text();
    //カンマを削除
    operationID = operationID.replace(/,/g, '');
    $('#select-operation-id').text( operationID );
    $('#select-operation-name').text( operationName );
    executeButtonCheck();
}

function executeButtonCheck() {
    if( $('#conductor-class-id').text() !== '' &&
        $('#select-operation-id').text() !== '' ) {
      conductorFooterButtonDisabled( false );
    } else {
      conductorFooterButtonDisabled( true );
    }
}

function symphonyExecute(boolCallProxy, symphony_instance_id){
    if( boolCallProxy===true ){
        var strSymphonyClassNoArea = 'print_symphony_id';
        var objSymphonyClassNoArea = document.getElementById(strSymphonyClassNoArea);
        var symphony_class_no = objSymphonyClassNoArea.innerHTML;

        var strOperationNoArea = 'print_operation_no_uapk';;
        var objOperationNoArea = document.getElementById(strOperationNoArea);
        var operation_no = objOperationNoArea.innerHTML;

        var bookdatetime = document.getElementById('bookdatetime').value;

        var exec_flag = false;

        if( symphony_class_no != '' && operation_no !='' ){
            if( window.confirm( getSomeMessage("ITABASEC010701") ) ){
                var strOptionOrderStream = collectElementInfoForExecute();
                exec_flag = true;
            }
        }

        if( exec_flag === true ){
            proxy.symphonyExecute(symphony_class_no,operation_no,bookdatetime,strOptionOrderStream);
        }
    }
    else{
        if( typeof symphony_instance_id!=="undefined" ){
            var url = '/default/menu/01_browse.php?no=2100180005&conductor_instance_id=' + symphony_instance_id;

            // 作業状態確認メニューに遷移
            //location.href=url;
            open( url, "_blank");
        }
    }
}

//----要素の情報を、タグから集める。
function collectElementInfoForExecute(){
    strSortAreaClassName = 'sortable_area';
    strElementClassName = 'movement2';

    var tmpArray = new Array();
    tmpArray.seqNum = "statictext";
    tmpArray.ORCHE = "hiddenstatic";
    tmpArray.PATTERN = "hiddenstatic";
    tmpArray.skip_box = "checkbox";
    tmpArray.ovrd_ope_box = "inputtext";
    var strMoveList = dataOperationGetValuesFromElementList(strSortAreaClassName, strElementClassName, tmpArray);
    return strMoveList;
}
//要素の情報を、タグから集める。----
//////// 日時ピッカー表示用ファンクション ////////
$(function(){
    setDatetimepicker('bookdatetime');
});


// ここまでカスタマイズした場合の一般メソッド配置域----

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   エディタ共通初期設定（editor_common.js）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const editor = new itaEditorFunctions();

// DOM読み込み完了
$( function(){
    // リスト取得開始
    proxy.printNoticeList();
    // タブ切り替え
    editor.tabMenu();
    // 画面縦リサイズ
    editor.rowResize();  
});
