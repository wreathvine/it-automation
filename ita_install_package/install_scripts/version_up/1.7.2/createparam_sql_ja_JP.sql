CREATE OR REPLACE VIEW G_CREATE_REFERENCE_ITEM AS 
SELECT TAB_A.CREATE_ITEM_ID ITEM_ID               ,
       TAB_C.LINK_ID LINK_ID                      ,
       TAB_A.DISP_SEQ DISP_SEQ                    ,
       TAB_C.TABLE_NAME TABLE_NAME                ,
       TAB_C.PRI_NAME PRI_NAME                    ,
       CASE WHEN CHAR_LENGTH(TAB_A.CREATE_ITEM_ID) <= 4 THEN CONCAT('KY_AUTO_COL_', lpad(TAB_A.CREATE_ITEM_ID, 4, '0')) ELSE CONCAT('KY_AUTO_COL_', TAB_A.CREATE_ITEM_ID) END AS COLUMN_NAME,
       TAB_A.ITEM_NAME ITEM_NAME                  ,
       TAB_D.FULL_COL_GROUP_NAME COL_GROUP_NAME   ,
       TAB_A.DESCRIPTION DESCRIPTION              ,
       TAB_A.INPUT_METHOD_ID INPUT_METHOD_ID      ,
       CASE WHEN TAB_A.INPUT_METHOD_ID = 8 THEN 2 ELSE 1 END AS SENSITIVE_FLAG,
       CASE WHEN CHAR_LENGTH(TAB_A.CREATE_ITEM_ID) <= 4 THEN
           CASE WHEN CONCAT('KY_AUTO_COL_', lpad(TAB_A.CREATE_ITEM_ID, 4, '0')) = TAB_C.COLUMN_NAME THEN 1 ELSE '' END
       ELSE
           CASE WHEN CONCAT('KY_AUTO_COL_', TAB_A.CREATE_ITEM_ID) = TAB_C.COLUMN_NAME THEN 1 ELSE '' END
       END AS MASTER_COL_FLAG                     ,
       TAB_A.ACCESS_AUTH                          ,
       TAB_A.NOTE                                 ,
       TAB_A.DISUSE_FLAG                          ,
       TAB_A.LAST_UPDATE_TIMESTAMP                ,
       TAB_A.LAST_UPDATE_USER                     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01        ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02        ,
       TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03 
FROM F_CREATE_ITEM_INFO TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID)
LEFT JOIN G_OTHER_MENU_LINK TAB_C ON (TAB_B.MENU_NAME = TAB_C.MENU_NAME)
LEFT JOIN F_COLUMN_GROUP TAB_D ON (TAB_A.COL_GROUP_ID = TAB_D.COL_GROUP_ID)
WHERE NOT TAB_A.INPUT_METHOD_ID = 7 AND TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0'
;


CREATE OR REPLACE VIEW G_CREATE_REFERENCE_ITEM_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                       ,
       TAB_A.JOURNAL_REG_DATETIME                 ,
       TAB_A.JOURNAL_ACTION_CLASS                 ,
       TAB_A.CREATE_ITEM_ID ITEM_ID               ,
       TAB_C.LINK_ID LINK_ID                      ,
       TAB_A.DISP_SEQ DISP_SEQ                    ,
       TAB_C.TABLE_NAME TABLE_NAME                ,
       TAB_C.PRI_NAME PRI_NAME                    ,
       CASE WHEN CHAR_LENGTH(TAB_A.CREATE_ITEM_ID) <= 4 THEN CONCAT('KY_AUTO_COL_', lpad(TAB_A.CREATE_ITEM_ID, 4, '0')) ELSE CONCAT('KY_AUTO_COL_', TAB_A.CREATE_ITEM_ID) END AS COLUMN_NAME,
       TAB_A.ITEM_NAME ITEM_NAME                  ,
       TAB_D.FULL_COL_GROUP_NAME COL_GROUP_NAME   ,
       TAB_A.DESCRIPTION DESCRIPTION              ,
       TAB_A.INPUT_METHOD_ID INPUT_METHOD_ID      ,
       CASE WHEN TAB_A.INPUT_METHOD_ID = 8 THEN 2 ELSE 1 END AS SENSITIVE_FLAG,
       CASE WHEN CHAR_LENGTH(TAB_A.CREATE_ITEM_ID) <= 4 THEN
           CASE WHEN CONCAT('KY_AUTO_COL_', lpad(TAB_A.CREATE_ITEM_ID, 4, '0')) = TAB_C.COLUMN_NAME THEN 1 ELSE '' END
       ELSE
           CASE WHEN CONCAT('KY_AUTO_COL_', TAB_A.CREATE_ITEM_ID) = TAB_C.COLUMN_NAME THEN 1 ELSE '' END
       END AS MASTER_COL_FLAG                     ,
       TAB_A.ACCESS_AUTH                          ,
       TAB_A.NOTE                                 ,
       TAB_A.DISUSE_FLAG                          ,
       TAB_A.LAST_UPDATE_TIMESTAMP                ,
       TAB_A.LAST_UPDATE_USER                     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01        ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02        ,
       TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03 
FROM F_CREATE_ITEM_INFO_JNL TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID)
LEFT JOIN G_OTHER_MENU_LINK TAB_C ON (TAB_B.MENU_NAME = TAB_C.MENU_NAME)
LEFT JOIN F_COLUMN_GROUP TAB_D ON (TAB_A.COL_GROUP_ID = TAB_D.COL_GROUP_ID)
WHERE NOT TAB_A.INPUT_METHOD_ID = 7 AND TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0'
;

UPDATE A_PROC_LOADED_LIST SET LOADED_FLG='0' WHERE ROW_ID=2100020005;
