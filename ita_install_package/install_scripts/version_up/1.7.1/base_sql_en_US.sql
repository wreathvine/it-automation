UPDATE A_ACCOUNT_LIST SET MAIL_ADDRESS = '' WHERE MAIL_ADDRESS = 'sample@xxx.bbb.ccc';
UPDATE A_ACCOUNT_LIST_JNL SET MAIL_ADDRESS = '' WHERE MAIL_ADDRESS = 'sample@xxx.bbb.ccc';


UPDATE B_LOGIN_AUTH_TYPE SET LOGIN_AUTH_TYPE_NAME='Key authentication (no passphrase)' WHERE LOGIN_AUTH_TYPE_ID=1;
UPDATE B_LOGIN_AUTH_TYPE_JNL SET LOGIN_AUTH_TYPE_NAME='Key authentication (no passphrase)' WHERE LOGIN_AUTH_TYPE_ID=1;
