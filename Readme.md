Readme
---------------
A proxy tool built in PHP that supports basic web http restful api ideas like **get** and **set**. It does implement a **delete**, but it is an internal feature. Results are in JSON format mainly because this tool is supposed to be used programatically (sometimes to get around the same origin policy issue)

License
---------------
FREE LICENSE. You can use this software as you see fit just remember to give me some credit. The creator of this tool will bear no liability to any damage that comes from the use of this tool.

Prerequisites
---------------
- PHP5 with curl library enabled

How to Install
---------------
- Download the files and extract to the desired folder on your webserver
- Change the permission on the WORKSPACE_DIRECTORY folder (default is "doc") to allow all to read and write
  chmod a+rw /PATH/TO/FOLDER/doc/
- Go to the url you just created and without entering anything, you should get "Invalid action!!!"

Usage
---------------
### get
`http://www.webrw-site.com?get=www.google.com`


### set
SIMPLE ENTRY
By entering the following

`http://www.webrw-site.com?set=lalalalalalalala`

you should get something similar to

`callback("UID:e5lr859ffa6f73234ab14bcc9a2d5c1876d31fd1")`

To retrieve your data you have two approaches

Raw Data

`http://www.webrw-site.com/doc/UID:e5lr859ffa6f73234ab14bcc9a2d5c1876d31fd1`

Returns 

`lalalalalalalala`

As a JSON object

`http://www.webrw-site.com/?get=UID:e5lr859ffa6f73234ab14bcc9a2d5c1876d31fd1`

Returns

`callback(["lalalalalalalala",""])`

APPEND

To append to an existing resource, simply add the "key" to the URL 

`http://www.webrw-site.com?set=lelelelelelelele&key=UID:e5lr859ffa6f73234ab14bcc9a2d5c1876d31fd1`

Returns the same key (as expected)

`callback("UID:e5lr859ffa6f73234ab14bcc9a2d5c1876d31fd1")`

Now if you look at the key

`http://www.webrw-site.com/doc/UID:e5lr859ffa6f73234ab14bcc9a2d5c1876d31fd1`

you should get 

`lalalalalalalala`

`lelelelelelelele`

Useful Notes
---------------
- all include statements are in class.webrw.php, so if you plan to use something other than index.php, all you have to include is class.webrw.php
