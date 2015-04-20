# encstorage
Encrypted storage

Пример зашифрованного SQL хранилища, где пользователи могут оставить свои данные (например, телефоны и электронные адреса) 
и впоследствии запросить номер телефона указав свой email.
Даже если сайт будет взломан и хакеры получат полный доступ к базе данных и всем файлам, 
они не должны узнать данные пользователей.
 
Решение должно использовать только стандартный набор PHP библиотек.

Phones encrypted storage.

That page shows a sample of an encrypted storage.
Requires SQLite and PDO (they are enabled by default in PHP since 5.1).
Requires internet connection because jQuery is loaded from Google storage.

The task: to create a sample of an encrypted storage with the provided design (the design of the web page is provided as an image).
The user can store phone by email and retrieve it later via email.
Use only default PHP libs.

Architecture:
Classes:
DB - simple PDO connector to SQLite, the connection is static, meaning, for all instances of the class the connection will be the same.
As IPhonesStorageDBConnect it should have high level methods which PhoneStorageEntry calls:
store
getPhoneByEmail
getSpecifiedError

PhoneStorageEntry - an item. Has an instance of IPhonesStorageDBConnect class and stores/retrieves data from database via its methods.
Public functions:
save
load
validateEmail
validatePhone
getError

PhoneStorageTaskController - main controller that process data, renders views and responds to ajax calls.