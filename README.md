# PHP Photo Gallery
**Beamtic PHP Gallery** takes advantage of PHP's build-in capabilities and [file-handling](https://beamtic.com/files-and-directories-php) to create a modern image gallery, allowing us to create categories, upload images, and organize them into categories.

There is a live demo available at the website.

## Installation
Installing the photo gallery is simple. You can either download a **.7z** file from the project website (phpphotogallery.com), or you can clone this git repository.

After extracting the zip and moving the files to your server, you just need to add the usual permissions with **chmod**. _*I.e:* chmod 777 -R /var/www/mysite/gallery/_

It is recommended that you setup groups instead of just allowing everyone (777), so instead you may want to do something like:
> sudo chown -R www-data:www-data /var/www/mysite/gallery/
Then do chmod:
> sudo chmod -R 775 /var/www/mysite/gallery/

**Note.** The above is just an example, the exact steps required on your own setup might be different. Some shared hosts will not need any modifications to permissions. In addition, you might also need to adjust the **upload_max_filesize** and **post_max_size** settings in _php.ini_. Have fun!

[PHP Photo Gallery](https://phpphotogallery.com/)

