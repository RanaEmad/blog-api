# Blog API
A challenge task to create a simple blog API using PHP by Shopbox

# Installation  
- Clone this repository
- Clone the vagrant environment located in https://github.com/RanaEmad/puphpet-blog-api
- Update the config.yaml located in pm7kRM/puphpet/config.yaml in the vagrant environment with the source folder for this repository
  ```source: 'C:/xampp/htdocs/blog-api' ```
- Run ```vagrant up``` in pm7kRM

OR

- Clone this repository in your server's folder  
- Import the blog-api.sql file located in resources/blog-api.sql in your database  
- Update the db_config.php file located in config/db_config.php with the correct database configuration settings  
- Use the instructions and parameters listed in the Endpoints section to call each endpoint

# Enpoints
- All endpoints expect Content-Type: application/x-www-form-urlencoded in the header and respond with a JSON object.
- Each JSON object has a "result" key indicating whether the call was a success or failed
- The authentication method used is Basic authentication sent in the headers.
You can use the following credentials for testing:
username: admin
password: YWRtaW4xMjM0NTZhZG1pbg==

| Endpoint | Auth | Method | Parameters | Success Response | Error Response  
| ------ | ------ | ------ | ------ | ------ | ------ |  
| /get_all | No | GET | None | {"result":"success","data":[{"id": "1","title":"Sample Title","text":"Sample Text"},{"id": "2","title":"Sample Title 2","text":"Sample Text 2"}]} | {"result":"fail","errors":"The error that occured"}  
| /get_one | No | GET | id: the record's id |{"result":"success","data":{"title":"Sample Title","text":"Sample Text"}} | {"result":"fail","errors":"The error that occured"}  
| /create | Yes | POST | title: the article's title with maximum length of 250 characters, text: the article| {"result":"success","data":{"id":"1"}} | {"result":"fail","errors":"The error that occured"}  
| /update | Yes | PUT | id: the id of the record to be updated, title: the article's title with maximum lenght of 200 characters, text: the article | {"result":"success"} | {"result":"fail","errors":"The error that occured"}  
| /delete | Yes | DELETE | id: the id of the record to be deleted | {"result":"success"} | {"result":"fail","errors":"The error that occured"}  
| /get_logs | No | GET | None | The username is the authentication username if exists {"result":"success","data":[{"id":"1","remote_addr":"192.168.56.1","timestamp":"2018-12-19 22:33:27","action":"get_all enpoint was executed successfully","username":"admin"}]} | {"result":"fail","errors":"The error that occured"}  

## Resources
The resources folder located in the root folder has Postman screenshots for each endpoint and the database sql file and .mwb file

## Third Parties Used
- PuPHPet for setting the vagrant environment

## Author

* **Rana Emad**  - (https://github.com/RanaEmad)

## License

This project is licensed under the MIT License
