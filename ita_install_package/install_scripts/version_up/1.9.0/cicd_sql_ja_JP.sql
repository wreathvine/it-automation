ALTER TABLE B_CICD_REPOSITORY_LIST ADD COLUMN SSH_PASSWORD TEXT AFTER GIT_PASSWORD;
ALTER TABLE B_CICD_REPOSITORY_LIST ADD COLUMN SSH_PASSPHRASE TEXT AFTER SSH_PASSWORD;
ALTER TABLE B_CICD_REPOSITORY_LIST ADD COLUMN SSH_EXTRA_ARGS TEXT AFTER SSH_PASSPHRASE;
ALTER TABLE B_CICD_REPOSITORY_LIST_JNL ADD COLUMN SSH_PASSWORD TEXT AFTER GIT_PASSWORD;
ALTER TABLE B_CICD_REPOSITORY_LIST_JNL ADD COLUMN SSH_PASSPHRASE TEXT AFTER SSH_PASSWORD;
ALTER TABLE B_CICD_REPOSITORY_LIST_JNL ADD COLUMN SSH_EXTRA_ARGS TEXT AFTER SSH_PASSPHRASE;


CREATE OR REPLACE VIEW D_CICD_REPOLIST_SYNCSTS_LINK AS
SELECT
                                                                               -- B_CICD_REPOSITORY_LIST Columns
    TAB_A.*,
                                                                               -- T_CICD_SYNC_STATUS Columns
    TAB_B.ROW_ID                                                             , -- リポジトリ一覧 項番
    TAB_B.SYNC_LAST_TIMESTAMP                                                  -- 最終同期日時
FROM
    B_CICD_REPOSITORY_LIST TAB_A
    LEFT JOIN T_CICD_SYNC_STATUS TAB_B ON ( TAB_A.REPO_ROW_ID = TAB_B.ROW_ID );

CREATE OR REPLACE VIEW D_CICD_REPOLIST_SYNCSTS_LINK_JNL AS
SELECT
                                                                               -- B_CICD_REPOSITORY_LIST Columns
    TAB_A.*,
                                                                               -- T_CICD_SYNC_STATUS Columns
    TAB_B.ROW_ID                                                             , -- リポジトリ一覧 項番
    TAB_B.SYNC_LAST_TIMESTAMP                                                  -- 最終同期日時
FROM
    B_CICD_REPOSITORY_LIST_JNL TAB_A
    LEFT JOIN T_CICD_SYNC_STATUS TAB_B ON ( TAB_A.REPO_ROW_ID = TAB_B.ROW_ID );


CREATE OR REPLACE VIEW D_CICD_MATL_FILE_LIST AS
SELECT 
  TAB_A.*,
  TAB_A.MATL_ROW_ID                                MATL_FILE_PATH_PULLKEY,
  CONCAT(TAB_B.REPO_NAME,':',TAB_A.MATL_FILE_PATH) MATL_FILE_PATH_PULLDOWN,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM
            B_CICD_MATERIAL_LIST   TAB_A
  LEFT JOIN B_CICD_REPOSITORY_LIST TAB_B ON ( TAB_A.REPO_ROW_ID = TAB_B.REPO_ROW_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW D_CICD_MATL_FILE_LIST_JNL AS
SELECT 
  TAB_A.*,
  TAB_A.MATL_ROW_ID                                MATL_FILE_PATH_PULLKEY,
  CONCAT(TAB_B.REPO_NAME,':',TAB_A.MATL_FILE_PATH) MATL_FILE_PATH_PULLDOWN,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM
            B_CICD_MATERIAL_LIST_JNL   TAB_A
  LEFT JOIN B_CICD_REPOSITORY_LIST_JNL TAB_B ON ( TAB_A.REPO_ROW_ID = TAB_B.REPO_ROW_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW D_CICD_MATL_PATH_LIST AS
SELECT
 TAB_1.*
,TAB_2.ACCESS_AUTH     ACCESS_AUTH_01
FROM
          B_CICD_MATERIAL_LIST    TAB_1
LEFT JOIN B_CICD_REPOSITORY_LIST  TAB_2 ON (TAB_1.REPO_ROW_ID = TAB_2.REPO_ROW_ID)
WHERE
     TAB_2.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW D_CICD_MATL_PATH_LIST_JNL AS
SELECT
 TAB_1.*
,TAB_2.ACCESS_AUTH     ACCESS_AUTH_01
FROM
          B_CICD_MATERIAL_LIST_JNL    TAB_1
LEFT JOIN B_CICD_REPOSITORY_LIST_JNL  TAB_2 ON (TAB_1.REPO_ROW_ID = TAB_2.REPO_ROW_ID)
WHERE
     TAB_2.DISUSE_FLAG = '0';


UPDATE B_CICD_GIT_PROTOCOL_TYPE_NAME SET GIT_PROTOCOL_TYPE_NAME='sshパスワード認証', DISUSE_FLAG='0' WHERE GIT_PROTOCOL_TYPE_ROW_ID=2;
UPDATE B_CICD_GIT_PROTOCOL_TYPE_NAME SET DISP_SEQ=5 WHERE GIT_PROTOCOL_TYPE_ROW_ID=3;
UPDATE B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL SET GIT_PROTOCOL_TYPE_NAME='sshパスワード認証', DISUSE_FLAG='0' WHERE GIT_PROTOCOL_TYPE_ROW_ID=2;
UPDATE B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL SET DISP_SEQ=5 WHERE GIT_PROTOCOL_TYPE_ROW_ID=3;
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME (GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'ssh鍵認証(パスフレーズあり)',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'ssh鍵認証(パスフレーズあり)',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME (GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'ssh鍵認証(パスフレーズなし)',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'ssh鍵認証(パスフレーズなし)',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
