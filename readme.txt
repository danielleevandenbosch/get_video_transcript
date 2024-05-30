In order to use the transcript maker you will need to make some apache configurations. 

php.ini changes 

```
file_uploads = On
upload_max_filesize = 3G
post_max_size = 3.5G
max_file_uploads = 20
max_execution_time = 300
max_input_time = 300
memory_limit = 4G

```

Private tmp removal




```
sudo cp /lib/systemd/system/apache2.service /etc/systemd/system/
sudo vim /etc/systemd/system/apache2.service
Change PrivateTmp=true to PrivateTmp=false.
sudo systemctl daemon-reload
sudo systemctl restart apache2
```