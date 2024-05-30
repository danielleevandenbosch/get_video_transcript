This application uses python and PHP allowing the end user to drag and drop a video and it will give the end user a download of the transcript of the said view. 
Especially useful for teams meetings where you might want to search for words that were said in the meeting and find that part of the video. 

In order to use the transcript maker you will need to make some apache configurations. 
## Python considerations: ##

You'll need these tools. 
```
python3 --version
pip3 --version
sudo apt update
sudo apt install python3
sudo apt install python3-pip
sudo apt install ffmpeg
pip3 install git+https://github.com/openai/whisper.git
pip3 install ffmpeg-python tqdm
```

## php ##

### php.ini changes ###

```
file_uploads = On
upload_max_filesize = 3G
post_max_size = 3.5G
max_file_uploads = 20
max_execution_time = 300
max_input_time = 300
memory_limit = 4G

```

###Private tmp removal###

```
sudo cp /lib/systemd/system/apache2.service /etc/systemd/system/
sudo vim /etc/systemd/system/apache2.service
Change PrivateTmp=true to PrivateTmp=false.
sudo systemctl daemon-reload
sudo systemctl restart apache2
```
