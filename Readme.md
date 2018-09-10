# Web Gozmo 2 - Developer notes
## Semi-static, CMS, content adaptor

## Supported filesystems

- Local FS
- AWS S3
- DigitalOcean Spaces

etc anything that [Flysystem](http://flysystem.thephpleague.com/) can use.

## Setup

Need to have Composer, the PHP package manager, installed

    brew install composer

then

    composer install

## Run tests

    ./vendor/bin/peridot test